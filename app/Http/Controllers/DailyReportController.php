<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DailyReport;
use App\Models\Project;
use App\Services\ProjectProgressSyncService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailyReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $projectId = $request->query('project_id');
        $status = $request->query('status');
        $workCategory = $request->query('work_category');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = DailyReport::with(['project', 'reporter', 'reviewer']);

        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        if ($status && $status !== 'all') {
            if ($status === 'review_queue') {
                $query->whereIn('status', ['submitted', 'flagged']);
            } else {
                $query->where('status', $status);
            }
        }
        if ($workCategory) {
            $query->where('work_category', $workCategory);
        }
        if ($dateFrom) {
            $query->where('report_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('report_date', '<=', $dateTo);
        }

        // Project Manager scoping
        if ($user->isProjectManager()) {
            $query->whereHas('project', fn ($q) => $q->where('manager_id', $user->id));
        }

        $reports = $query->orderBy('report_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $projects = Project::where('status', 'active')->orderBy('name')->get();
        $categories = [
            'Concrete Pouring (RCC/PCC)',
            'Earthwork & Excavation',
            'Reinforcement & Shuttering',
            'Road Base & Bitumen',
            'Masonry & Finishing',
            'Site Clearance',
        ];

        $pendingReviewCount = DailyReport::whereIn('status', ['submitted', 'flagged'])
            ->when($user->isProjectManager(), fn ($q) => $q->whereHas('project', fn ($pq) => $pq->where('manager_id', $user->id)))
            ->count();

        return view('daily_reports.index', compact(
            'reports',
            'projects',
            'categories',
            'projectId',
            'status',
            'workCategory',
            'dateFrom',
            'dateTo',
            'pendingReviewCount'
        ));
    }

    public function create(): View
    {
        $user = auth()->user();
        if (! in_array($user->role, ['admin', 'project_manager', 'foreman'], true)) {
            abort(403, 'You are not authorized to submit daily site reports.');
        }

        $projects = Project::where('status', 'active')
            ->when($user->isProjectManager(), fn ($q) => $q->where('manager_id', $user->id))
            ->orderBy('name')
            ->get();

        $defaultProject = $projects->first();
        $todayDate = Carbon::today('Asia/Kolkata')->toDateString();

        $categories = [
            'Concrete Pouring (RCC/PCC)',
            'Earthwork & Excavation',
            'Reinforcement & Shuttering',
            'Road Base & Bitumen',
            'Masonry & Finishing',
            'Site Clearance',
        ];

        $units = ['m3', 'sq.m', 'MT', 'RMT', 'Nos'];
        $weatherOptions = ['Clear / Sunny', 'Cloudy', 'Light Rain', 'Heavy Monsoon'];

        return view('daily_reports.create', compact('projects', 'defaultProject', 'todayDate', 'categories', 'units', 'weatherOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (! in_array($user->role, ['admin', 'project_manager', 'foreman'], true)) {
            abort(403, 'You are not authorized to submit daily site reports.');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'report_date' => 'required|date',
            'work_category' => 'required|string',
            'activity_summary' => 'required|string',
            'quantity_completed' => 'nullable|numeric|min:0',
            'quantity_unit' => 'nullable|string',
            'reported_progress_percent' => 'required|integer|min:0|max:100',
            'labor_count' => 'required|integer|min:0',
            'weather' => 'required|string',
            'issues_or_delays' => 'nullable|string',
        ]);

        // If PM submits for unassigned project, check permission
        if ($user->isProjectManager()) {
            $project = Project::findOrFail($validated['project_id']);
            if ($project->manager_id !== $user->id) {
                abort(403, 'Project managers can only submit reports for their assigned projects.');
            }
        }

        $validated['reported_by'] = $user->id;
        $validated['status'] = 'submitted';

        $report = DailyReport::create($validated);

        ActivityLog::log(
            'Daily Reports',
            'Site Report Submitted',
            "Report #{$report->id} submitted by {$user->name} for {$report->project->name} ({$report->work_category}, Date: {$report->report_date->format('d M Y')})."
        );

        return redirect()->route('daily-reports.index')->with('success', 'Daily site report submitted successfully into the review queue.');
    }

    public function show(DailyReport $dailyReport): View
    {
        $dailyReport->load(['project', 'reporter', 'reviewer']);

        return view('daily_reports.show', compact('dailyReport'));
    }

    public function review(Request $request, DailyReport $dailyReport, ProjectProgressSyncService $progressSync): RedirectResponse
    {
        $user = auth()->user();

        // Only admin or assigned PM can review/approve
        if (! $user->isAdmin()) {
            if (! ($user->isProjectManager() && $dailyReport->project && $dailyReport->project->manager_id === $user->id)) {
                abort(403, 'Only administrators or assigned project managers can review and approve site reports.');
            }
        }

        $validated = $request->validate([
            'action' => 'required|in:reviewed,flagged',
            'review_notes' => 'nullable|string|max:1000',
        ]);

        $dailyReport->update([
            'status' => $validated['action'],
            'reviewed_by' => $user->id,
            'reviewed_at' => now('Asia/Kolkata'),
            'review_notes' => $validated['review_notes'],
        ]);

        if ($validated['action'] === 'reviewed') {
            $progressSync->syncOnReportReview($dailyReport);
            $actionText = 'approved';
        } else {
            $actionText = 'flagged for revision';
        }

        ActivityLog::log(
            'Daily Reports',
            'Site Report Reviewed',
            "Report #{$dailyReport->id} for {$dailyReport->project->name} was {$actionText} by {$user->name}."
        );

        return back()->with('success', "Daily report #{$dailyReport->id} marked as {$validated['action']}.");
    }
}

