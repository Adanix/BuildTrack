<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AttendanceRecord;
use App\Models\DailyReport;
use App\Models\Employee;
use App\Models\Machinery;
use App\Models\MachineryDocument;
use App\Models\PayrollRecord;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $today = Carbon::today('Asia/Kolkata');
        $currentMonth = Carbon::now('Asia/Kolkata')->format('Y-m');

        // Global Filters
        $projectId = $request->query('project_id');
        $location = $request->query('location');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $periodMonth = $request->query('period_month', $currentMonth);

        // Base project query
        $projectsQuery = Project::with(['manager']);
        if ($projectId) {
            $projectsQuery->where('id', $projectId);
        }
        if ($location) {
            $projectsQuery->where('location', 'like', "%{$location}%");
        }

        $allProjects = Project::orderBy('name')->get();
        $locations = Project::select('location')->distinct()->pluck('location');

        // Role-based scoping for PM and Driver
        if ($user->isProjectManager()) {
            $managedProjectsQuery = (clone $projectsQuery)->where('manager_id', $user->id);
            $activeProjectsCount = (clone $managedProjectsQuery)->where('status', 'active')->count();
            $projectsList = $managedProjectsQuery->orderBy('overall_progress', 'desc')->get();
        } elseif ($user->isDriver()) {
            $assignedMachineries = Machinery::with(['currentProject', 'documents'])
                ->where('assigned_driver_id', $user->id)
                ->get();
            $activeProjectsCount = $projectsQuery->where('status', 'active')->count();
            $projectsList = $projectsQuery->orderBy('overall_progress', 'desc')->get();
        } else {
            $activeProjectsCount = (clone $projectsQuery)->where('status', 'active')->count();
            $projectsList = (clone $projectsQuery)->orderBy('overall_progress', 'desc')->get();
        }

        // Daily Reports Query
        $reportsQuery = DailyReport::with(['project', 'reporter', 'reviewer'])
            ->orderBy('report_date', 'desc')
            ->orderBy('id', 'desc');

        if ($projectId) {
            $reportsQuery->where('project_id', $projectId);
        }
        if ($location) {
            $reportsQuery->whereHas('project', fn ($q) => $q->where('location', 'like', "%{$location}%"));
        }
        if ($dateFrom) {
            $reportsQuery->where('report_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $reportsQuery->where('report_date', '<=', $dateTo);
        }
        if ($user->isProjectManager()) {
            $reportsQuery->whereHas('project', fn ($q) => $q->where('manager_id', $user->id));
        }

        $recentReports = (clone $reportsQuery)->take(6)->get();
        $pendingReportsCount = DailyReport::whereIn('status', ['submitted', 'flagged'])
            ->when($user->isProjectManager(), fn ($q) => $q->whereHas('project', fn ($pq) => $pq->where('manager_id', $user->id)))
            ->count();

        // Attendance & OT metrics
        $pendingOtQuery = AttendanceRecord::where('overtime_hours', '>', 0)
            ->where('overtime_approved', false);
        if ($user->isProjectManager()) {
            $pendingOtQuery->whereHas('project', fn ($q) => $q->where('manager_id', $user->id));
        }
        $pendingOtCount = $pendingOtQuery->count();

        // Compliance & Machinery metrics
        $allDocuments = MachineryDocument::with('machinery')->get();
        $expiredDocsCount = $allDocuments->where('status_bucket', 'expired')->count();
        $criticalDocsCount = $allDocuments->where('status_bucket', 'critical')->count();
        $expiringSoonDocsCount = $allDocuments->where('status_bucket', 'expiring_soon')->count();
        $flaggedDocsCount = $allDocuments->where('flagged_for_renewal', true)->count();

        $urgentDocuments = MachineryDocument::with('machinery')
            ->where('expiry_date', '<=', $today->copy()->addDays(30)->toDateString())
            ->orderBy('expiry_date', 'asc')
            ->take(5)
            ->get();

        // Payroll stats (for admin and hr_payroll)
        $payrollStats = null;
        if ($user->isAdmin() || $user->isHrPayroll()) {
            $payrollRecords = PayrollRecord::where('period_month', $periodMonth)->get();
            $payrollStats = [
                'total_base' => $payrollRecords->sum('base_earned_inr'),
                'total_ot' => $payrollRecords->sum('overtime_pay_inr'),
                'total_net' => $payrollRecords->sum('net_salary_inr'),
                'draft_count' => $payrollRecords->where('status', 'draft')->count(),
                'approved_count' => $payrollRecords->where('status', 'approved')->count(),
                'paid_count' => $payrollRecords->where('status', 'paid')->count(),
            ];
        }

        // Recent Activity
        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('dashboard.index', compact(
            'user',
            'projectsList',
            'allProjects',
            'locations',
            'activeProjectsCount',
            'recentReports',
            'pendingReportsCount',
            'pendingOtCount',
            'expiredDocsCount',
            'criticalDocsCount',
            'expiringSoonDocsCount',
            'flaggedDocsCount',
            'urgentDocuments',
            'payrollStats',
            'recentActivities',
            'projectId',
            'location',
            'dateFrom',
            'dateTo',
            'periodMonth'
        ));
    }
}
