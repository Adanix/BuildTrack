<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Project::with(['manager', 'dailyReports'])
            ->withCount(['dailyReports', 'employees', 'machineries']);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%");
            });
        }

        $projects = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('projects.index', compact('projects', 'status', 'search'));
    }

    public function show(Project $project): View
    {
        $project->load([
            'manager',
            'dailyReports' => fn ($q) => $q->with(['reporter', 'reviewer'])->orderBy('report_date', 'desc'),
            'employees' => fn ($q) => $q->orderBy('full_name'),
            'machineries' => fn ($q) => $q->with(['assignedDriver', 'documents']),
        ]);

        return view('projects.show', compact('project'));
    }

    public function create(): View
    {
        $this->authorizeAdmin();

        $projectManagers = User::whereIn('role', ['admin', 'project_manager'])
            ->where('is_active', true)
            ->get();

        return view('projects.create', compact('projectManagers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:projects,code',
            'name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'budget_inr' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'target_end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:planned,active,on_hold,completed',
            'description' => 'nullable|string',
        ]);

        $project = Project::create($validated);

        ActivityLog::log(
            'Projects',
            'Project Created',
            "Created new project '{$project->name}' ({$project->code}) with budget INR " . number_format((float) $project->budget_inr, 2)
        );

        return redirect()->route('projects.show', $project)->with('success', 'Project created successfully.');
    }

    public function edit(Project $project): View
    {
        $this->authorizeProjectManagement($project);

        $projectManagers = User::whereIn('role', ['admin', 'project_manager'])
            ->where('is_active', true)
            ->get();

        return view('projects.edit', compact('project', 'projectManagers'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeProjectManagement($project);

        $rules = [
            'name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'budget_inr' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'target_end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:planned,active,on_hold,completed',
            'description' => 'nullable|string',
        ];

        // Admin can manually adjust overall progress
        if (auth()->user()->isAdmin()) {
            $rules['overall_progress'] = 'required|integer|min:0|max:100';
        }

        $validated = $request->validate($rules);

        $oldProgress = $project->overall_progress;
        $project->update($validated);

        if (auth()->user()->isAdmin() && isset($validated['overall_progress']) && (int) $validated['overall_progress'] !== (int) $oldProgress) {
            ActivityLog::log(
                'Projects',
                'Manual Progress Override',
                "Administrator manually adjusted project '{$project->name}' progress from {$oldProgress}% to {$validated['overall_progress']}%."
            );
        } else {
            ActivityLog::log(
                'Projects',
                'Project Updated',
                "Updated details for project '{$project->name}' ({$project->code})."
            );
        }

        return redirect()->route('projects.show', $project)->with('success', 'Project details updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorizeAdmin();

        // Check related records to prevent destructive deletion
        $hasReports = $project->dailyReports()->exists();
        $hasEmployees = $project->employees()->exists();
        $hasAttendance = $project->attendanceRecords()->exists();
        $hasMachinery = $project->machineries()->exists();

        if ($hasReports || $hasEmployees || $hasAttendance || $hasMachinery) {
            return back()->with('error', 'Cannot delete project because historical site records, employees, attendance, or machinery are attached to it. Please set the status to "completed" or "on_hold" instead.');
        }

        $projectName = $project->name;
        $project->delete();

        ActivityLog::log('Projects', 'Project Deleted', "Deleted project {$projectName}.");

        return redirect()->route('projects.index')->with('success', 'Project removed successfully.');
    }

    private function authorizeAdmin(): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can perform this action.');
        }
    }

    private function authorizeProjectManagement(Project $project): void
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return;
        }

        if ($user->isProjectManager() && $project->manager_id === $user->id) {
            return;
        }

        abort(403, 'You are not authorized to manage this project.');
    }
}
