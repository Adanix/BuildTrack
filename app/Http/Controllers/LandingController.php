<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Employee;
use App\Models\Machinery;
use App\Models\Project;
use App\Models\User;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $projects = Project::with('manager')->orderBy('overall_progress', 'desc')->take(4)->get();
        $totalBudgetInr = Project::sum('budget_inr');
        $activeProjectsCount = Project::where('status', 'active')->count();
        $totalWorkforceCount = Employee::where('status', 'active')->count();
        $totalMachineryCount = Machinery::count();
        $totalReportsCount = DailyReport::count();

        $demoUsers = User::whereIn('role', [
            'admin',
            'project_manager',
            'foreman',
            'hr_payroll',
            'asset_manager',
            'driver',
        ])->get()->keyBy('role');

        return view('landing', compact(
            'projects',
            'totalBudgetInr',
            'activeProjectsCount',
            'totalWorkforceCount',
            'totalMachineryCount',
            'totalReportsCount',
            'demoUsers'
        ));
    }
}
