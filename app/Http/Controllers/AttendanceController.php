<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        if (in_array($user->role, ['driver'], true)) {
            abort(403, 'Drivers are not authorized to view the general workforce attendance roster.');
        }

        $date = $request->query('date', Carbon::today('Asia/Kolkata')->toDateString());
        $projectId = $request->query('project_id');
        $tab = $request->query('tab', 'daily'); // 'daily' or 'overtime_queue'

        $projects = Project::where('status', 'active')
            ->when($user->isProjectManager(), fn ($q) => $q->where('manager_id', $user->id))
            ->orderBy('name')
            ->get();

        $query = AttendanceRecord::with(['employee.primaryProject', 'project', 'approver', 'recorder']);

        if ($tab === 'overtime_queue') {
            $query->where('overtime_hours', '>', 0)
                ->where('overtime_approved', false)
                ->orderBy('attendance_date', 'desc');
        } else {
            if ($date) {
                $query->where('attendance_date', $date);
            }
            if ($projectId) {
                $query->where('project_id', $projectId);
            }
            $query->orderBy('attendance_date', 'desc');
        }

        if ($user->isProjectManager()) {
            $query->whereHas('project', fn ($q) => $q->where('manager_id', $user->id));
        }

        $records = $query->paginate(20)->withQueryString();

        $pendingOtCount = AttendanceRecord::where('overtime_hours', '>', 0)
            ->where('overtime_approved', false)
            ->when($user->isProjectManager(), fn ($q) => $q->whereHas('project', fn ($pq) => $pq->where('manager_id', $user->id)))
            ->count();

        return view('attendance.index', compact('records', 'projects', 'date', 'projectId', 'tab', 'pendingOtCount'));
    }

    public function bulkForm(Request $request): View
    {
        $user = auth()->user();
        if (! in_array($user->role, ['admin', 'project_manager', 'foreman', 'hr_payroll'], true)) {
            abort(403, 'Unauthorized access to bulk attendance marking.');
        }

        $projects = Project::where('status', 'active')
            ->when($user->isProjectManager(), fn ($q) => $q->where('manager_id', $user->id))
            ->orderBy('name')
            ->get();

        $selectedProjectId = $request->query('project_id', $projects->first()?->id);
        $selectedDate = $request->query('date', Carbon::today('Asia/Kolkata')->toDateString());

        // Get employees assigned to this project or all active if none assigned
        $employees = Employee::where('status', 'active')
            ->when($selectedProjectId, function ($q) use ($selectedProjectId) {
                $q->where(function ($sub) use ($selectedProjectId) {
                    $sub->where('primary_project_id', $selectedProjectId)
                        ->orWhereNull('primary_project_id');
                });
            })
            ->orderBy('full_name')
            ->get();

        // Existing records for this project & date
        $existingRecords = AttendanceRecord::where('project_id', $selectedProjectId)
            ->where('attendance_date', $selectedDate)
            ->get()
            ->keyBy('employee_id');

        return view('attendance.bulk', compact('projects', 'selectedProjectId', 'selectedDate', 'employees', 'existingRecords'));
    }

    public function storeBulk(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (! in_array($user->role, ['admin', 'project_manager', 'foreman', 'hr_payroll'], true)) {
            abort(403, 'Unauthorized access to bulk attendance marking.');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'attendance_date' => 'required|date',
            'records' => 'required|array',
            'records.*.employee_id' => 'required|exists:employees,id',
            'records.*.status' => 'required|in:present,half_day,absent,paid_leave',
            'records.*.regular_hours' => 'required|numeric|min:0|max:12',
            'records.*.overtime_hours' => 'nullable|numeric|min:0|max:12',
            'records.*.remarks' => 'nullable|string|max:255',
        ]);

        $project = Project::findOrFail($validated['project_id']);
        if ($user->isProjectManager() && $project->manager_id !== $user->id) {
            abort(403, 'Project managers can only record attendance for assigned projects.');
        }

        $savedCount = 0;
        foreach ($validated['records'] as $entry) {
            $employeeId = $entry['employee_id'];
            $status = $entry['status'];
            $regHours = (float) $entry['regular_hours'];
            $otHours = isset($entry['overtime_hours']) ? (float) $entry['overtime_hours'] : 0.00;

            // Enforce status hour defaults
            if ($status === 'absent' || $status === 'paid_leave') {
                $regHours = 0.00;
                $otHours = 0.00;
            } elseif ($status === 'half_day' && $regHours > 4.00) {
                $regHours = 4.00;
            }

            $existing = AttendanceRecord::where('employee_id', $employeeId)
                ->where('attendance_date', $validated['attendance_date'])
                ->first();

            $otApproved = $existing ? $existing->overtime_approved : false;
            $approvedBy = $existing ? $existing->overtime_approved_by : null;
            $approvedAt = $existing ? $existing->overtime_approved_at : null;

            // If overtime was edited and increased, reset approval
            if ($existing && (float) $existing->overtime_hours !== $otHours && $otHours > 0) {
                $otApproved = false;
                $approvedBy = null;
                $approvedAt = null;
            }

            AttendanceRecord::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'attendance_date' => $validated['attendance_date'],
                ],
                [
                    'project_id' => $validated['project_id'],
                    'status' => $status,
                    'regular_hours' => $regHours,
                    'overtime_hours' => $otHours,
                    'overtime_approved' => $otApproved,
                    'overtime_approved_by' => $approvedBy,
                    'overtime_approved_at' => $approvedAt,
                    'remarks' => $entry['remarks'] ?? null,
                    'recorded_by' => $user->id,
                ]
            );

            $savedCount++;
        }

        ActivityLog::log(
            'Attendance',
            'Bulk Attendance Recorded',
            "Recorded daily crew attendance for {$savedCount} workers at {$project->name} on {$validated['attendance_date']}."
        );

        return redirect()->route('attendance.index', [
            'date' => $validated['attendance_date'],
            'project_id' => $validated['project_id'],
        ])->with('success', "Crew attendance sheet saved successfully for {$savedCount} employees.");
    }

    public function approveOvertime(AttendanceRecord $attendance): RedirectResponse
    {
        $user = auth()->user();
        $this->authorizeOvertimeApproval($attendance->project);

        $attendance->update([
            'overtime_approved' => true,
            'overtime_approved_by' => $user->id,
            'overtime_approved_at' => now('Asia/Kolkata'),
        ]);

        ActivityLog::log(
            'Attendance',
            'Overtime Approved',
            "Approved {$attendance->overtime_hours} hours of overtime for {$attendance->employee->full_name} on {$attendance->attendance_date->format('d M Y')}."
        );

        return back()->with('success', "Overtime approved for {$attendance->employee->full_name}.");
    }

    public function approveAllOvertime(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $query = AttendanceRecord::where('overtime_hours', '>', 0)
            ->where('overtime_approved', false);

        if ($user->isProjectManager()) {
            $query->whereHas('project', fn ($q) => $q->where('manager_id', $user->id));
        } elseif (! in_array($user->role, ['admin', 'hr_payroll'], true)) {
            abort(403, 'Unauthorized to approve overtime.');
        }

        $pending = $query->get();
        $count = $pending->count();

        foreach ($pending as $record) {
            $record->update([
                'overtime_approved' => true,
                'overtime_approved_by' => $user->id,
                'overtime_approved_at' => now('Asia/Kolkata'),
            ]);
        }

        ActivityLog::log(
            'Attendance',
            'Batch Overtime Approved',
            "Batch-approved {$count} pending overtime records."
        );

        return back()->with('success', "Successfully approved {$count} pending overtime entries.");
    }

    private function authorizeOvertimeApproval(?Project $project): void
    {
        $user = auth()->user();
        if ($user->isAdmin() || $user->isHrPayroll()) {
            return;
        }

        if ($user->isProjectManager() && $project && $project->manager_id === $user->id) {
            return;
        }

        abort(403, 'You do not have permission to approve overtime for this site.');
    }
}

