<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeView();

        $wageType = $request->query('wage_type');
        $status = $request->query('status');
        $projectId = $request->query('project_id');
        $search = $request->query('search');

        $query = Employee::with(['primaryProject', 'user'])
            ->withCount(['attendanceRecords', 'payrollRecords']);

        if ($wageType) {
            $query->where('wage_type', $wageType);
        }
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        if ($projectId) {
            $query->where('primary_project_id', $projectId);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('full_name')->paginate(12)->withQueryString();
        $projects = Project::where('status', 'active')->orderBy('name')->get();

        return view('employees.index', compact('employees', 'projects', 'wageType', 'status', 'projectId', 'search'));
    }

    public function show(Employee $employee): View
    {
        $this->authorizeView();

        $employee->load([
            'primaryProject',
            'user',
            'attendanceRecords' => fn ($q) => $q->with('project')->orderBy('attendance_date', 'desc')->take(15),
            'payrollRecords' => fn ($q) => $q->orderBy('period_month', 'desc')->take(6),
        ]);

        return view('employees.show', compact('employee'));
    }

    public function create(): View
    {
        $this->authorizeManage();

        $projects = Project::where('status', 'active')->orderBy('name')->get();
        $availableUsers = User::whereDoesntHave('employee')->where('is_active', true)->get();

        return view('employees.create', compact('projects', 'availableUsers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManage();

        $validated = $request->validate([
            'employee_code' => 'required|string|max:50|unique:employees,employee_code',
            'full_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'primary_project_id' => 'nullable|exists:projects,id',
            'user_id' => 'nullable|exists:users,id|unique:employees,user_id',
            'wage_type' => 'required|in:monthly,daily',
            'base_salary_inr' => 'required|numeric|min:0',
            'overtime_rate_per_hour_inr' => 'required|numeric|min:0',
            'joining_date' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        $employee = Employee::create($validated);

        ActivityLog::log(
            'Attendance',
            'Employee Registered',
            "Registered new employee '{$employee->full_name}' ({$employee->employee_code}, Wage: {$employee->wage_type})."
        );

        return redirect()->route('employees.show', $employee)->with('success', 'Employee registered successfully.');
    }

    public function edit(Employee $employee): View
    {
        $this->authorizeManage();

        $projects = Project::where('status', 'active')->orderBy('name')->get();
        $availableUsers = User::where(function ($q) use ($employee) {
            $q->whereDoesntHave('employee')
                ->orWhere('id', $employee->user_id);
        })->where('is_active', true)->get();

        return view('employees.edit', compact('employee', 'projects', 'availableUsers'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $this->authorizeManage();

        $validated = $request->validate([
            'employee_code' => "required|string|max:50|unique:employees,employee_code,{$employee->id}",
            'full_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'primary_project_id' => 'nullable|exists:projects,id',
            'user_id' => "nullable|exists:users,id|unique:employees,user_id,{$employee->id}",
            'wage_type' => 'required|in:monthly,daily',
            'base_salary_inr' => 'required|numeric|min:0',
            'overtime_rate_per_hour_inr' => 'required|numeric|min:0',
            'joining_date' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        $employee->update($validated);

        ActivityLog::log(
            'Attendance',
            'Employee Updated',
            "Updated profile for employee '{$employee->full_name}' ({$employee->employee_code})."
        );

        return redirect()->route('employees.show', $employee)->with('success', 'Employee details updated successfully.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $this->authorizeManage();

        $hasAttendance = $employee->attendanceRecords()->exists();
        $hasPayroll = $employee->payrollRecords()->exists();

        if ($hasAttendance || $hasPayroll) {
            return back()->with('error', 'Cannot delete employee because attendance history or payroll records are attached. Please set employee status to "inactive" instead.');
        }

        $name = $employee->full_name;
        $employee->delete();

        ActivityLog::log('Attendance', 'Employee Removed', "Deleted record for {$name}.");

        return redirect()->route('employees.index')->with('success', 'Employee record removed successfully.');
    }

    private function authorizeView(): void
    {
        $role = auth()->user()->role;
        if (! in_array($role, ['admin', 'hr_payroll', 'project_manager', 'foreman'], true)) {
            abort(403, 'Unauthorized access to employee directory.');
        }
    }

    private function authorizeManage(): void
    {
        $role = auth()->user()->role;
        if (! in_array($role, ['admin', 'hr_payroll'], true)) {
            abort(403, 'Only administrators and HR/Payroll officers can modify employee records.');
        }
    }
}
