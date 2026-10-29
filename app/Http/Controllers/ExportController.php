<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Machinery;
use App\Models\MachineryDocument;
use App\Models\PayrollRecord;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        if (in_array($user->role, ['foreman', 'driver'], true)) {
            abort(403, 'Your role is not authorized to export executive data reports.');
        }

        $projects = Project::orderBy('name')->get();
        $locations = Project::select('location')->distinct()->pluck('location');
        $currentMonth = Carbon::now('Asia/Kolkata')->format('Y-m');

        return view('exports.index', compact('projects', 'locations', 'currentMonth'));
    }

    public function exportProjectsCsv(Request $request): StreamedResponse
    {
        $this->authorizeExport('projects');

        $projectId = $request->query('project_id');
        $location = $request->query('location');

        $query = Project::with(['manager']);
        if ($projectId) {
            $query->where('id', $projectId);
        }
        if ($location) {
            $query->where('location', 'like', "%{$location}%");
        }

        $projects = $query->orderBy('name')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="buildtrack_projects_' . date('Ymd_His') . '.csv"',
        ];

        return response()->stream(function () use ($projects) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Project Code',
                'Project Name',
                'Client Name',
                'Location',
                'Project Manager',
                'Budget (INR)',
                'Start Date',
                'Target End Date',
                'Overall Progress (%)',
                'Status',
            ]);

            foreach ($projects as $p) {
                fputcsv($handle, [
                    $this->sanitizeCsvCell($p->code),
                    $this->sanitizeCsvCell($p->name),
                    $this->sanitizeCsvCell($p->client_name),
                    $this->sanitizeCsvCell($p->location),
                    $this->sanitizeCsvCell($p->manager?->name ?? 'Unassigned'),
                    $this->sanitizeCsvCell(number_format((float) $p->budget_inr, 2, '.', '')),
                    $this->sanitizeCsvCell($p->start_date?->format('Y-m-d')),
                    $this->sanitizeCsvCell($p->target_end_date?->format('Y-m-d')),
                    $this->sanitizeCsvCell($p->overall_progress),
                    $this->sanitizeCsvCell(ucfirst($p->status)),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function exportAttendanceCsv(Request $request): StreamedResponse
    {
        $this->authorizeExport('attendance');

        $projectId = $request->query('project_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = AttendanceRecord::with(['employee', 'project', 'approver']);
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        if ($dateFrom) {
            $query->where('attendance_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('attendance_date', '<=', $dateTo);
        }

        $records = $query->orderBy('attendance_date', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="buildtrack_attendance_' . date('Ymd_His') . '.csv"',
        ];

        return response()->stream(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Date',
                'Employee Code',
                'Employee Name',
                'Project Code',
                'Status',
                'Regular Hours',
                'Overtime Hours',
                'Overtime Approved',
                'Approved By',
                'Remarks',
            ]);

            foreach ($records as $r) {
                fputcsv($handle, [
                    $this->sanitizeCsvCell($r->attendance_date?->format('Y-m-d')),
                    $this->sanitizeCsvCell($r->employee?->employee_code),
                    $this->sanitizeCsvCell($r->employee?->full_name),
                    $this->sanitizeCsvCell($r->project?->code),
                    $this->sanitizeCsvCell($r->status),
                    $this->sanitizeCsvCell($r->regular_hours),
                    $this->sanitizeCsvCell($r->overtime_hours),
                    $this->sanitizeCsvCell($r->overtime_approved ? 'Yes' : 'No'),
                    $this->sanitizeCsvCell($r->approver?->name ?? '-'),
                    $this->sanitizeCsvCell($r->remarks ?? ''),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function exportPayrollCsv(Request $request): StreamedResponse
    {
        $this->authorizeExport('payroll');

        $periodMonth = $request->query('period_month', Carbon::now('Asia/Kolkata')->format('Y-m'));

        $records = PayrollRecord::with(['employee.primaryProject', 'approver'])
            ->where('period_month', $periodMonth)
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="buildtrack_payroll_' . $periodMonth . '.csv"',
        ];

        return response()->stream(function () use ($records, $periodMonth) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Period Month',
                'Employee Code',
                'Employee Name',
                'Wage Type',
                'Days Present',
                'Approved OT Hours',
                'Base Earned (INR)',
                'Overtime Pay (INR)',
                'Allowances (INR)',
                'Deductions (INR)',
                'Net Salary (INR)',
                'Status',
            ]);

            foreach ($records as $r) {
                fputcsv($handle, [
                    $this->sanitizeCsvCell($periodMonth),
                    $this->sanitizeCsvCell($r->employee?->employee_code),
                    $this->sanitizeCsvCell($r->employee?->full_name),
                    $this->sanitizeCsvCell($r->employee?->wage_type),
                    $this->sanitizeCsvCell($r->days_present),
                    $this->sanitizeCsvCell($r->total_overtime_hours),
                    $this->sanitizeCsvCell(number_format((float) $r->base_earned_inr, 2, '.', '')),
                    $this->sanitizeCsvCell(number_format((float) $r->overtime_pay_inr, 2, '.', '')),
                    $this->sanitizeCsvCell(number_format((float) $r->allowances_inr, 2, '.', '')),
                    $this->sanitizeCsvCell(number_format((float) $r->deductions_inr, 2, '.', '')),
                    $this->sanitizeCsvCell(number_format((float) $r->net_salary_inr, 2, '.', '')),
                    $this->sanitizeCsvCell(ucfirst($r->status)),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function exportMachineryCsv(Request $request): StreamedResponse
    {
        $this->authorizeExport('machinery');

        $machineries = Machinery::with(['currentProject', 'assignedDriver', 'documents'])->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="buildtrack_machinery_compliance_' . date('Ymd_His') . '.csv"',
        ];

        return response()->stream(function () use ($machineries) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Asset Code',
                'Equipment Name',
                'Category',
                'Registration No',
                'Current Project',
                'Assigned Driver',
                'Operational Status',
                'Document Type',
                'Document Number',
                'Expiry Date',
                'Days Remaining',
                'Status Bucket',
                'Renewal Estimate (INR)',
                'Flagged for Renewal',
            ]);

            foreach ($machineries as $m) {
                if ($m->documents->isEmpty()) {
                    fputcsv($handle, [
                        $this->sanitizeCsvCell($m->asset_code),
                        $this->sanitizeCsvCell($m->name),
                        $this->sanitizeCsvCell($m->category),
                        $this->sanitizeCsvCell($m->registration_number),
                        $this->sanitizeCsvCell($m->currentProject?->name ?? 'Yard'),
                        $this->sanitizeCsvCell($m->assignedDriver?->name ?? 'Unassigned'),
                        $this->sanitizeCsvCell($m->operational_status),
                        '-', '-', '-', '-', '-', '-', '-',
                    ]);
                } else {
                    foreach ($m->documents as $doc) {
                        fputcsv($handle, [
                            $this->sanitizeCsvCell($m->asset_code),
                            $this->sanitizeCsvCell($m->name),
                            $this->sanitizeCsvCell($m->category),
                            $this->sanitizeCsvCell($m->registration_number),
                            $this->sanitizeCsvCell($m->currentProject?->name ?? 'Yard'),
                            $this->sanitizeCsvCell($m->assignedDriver?->name ?? 'Unassigned'),
                            $this->sanitizeCsvCell($m->operational_status),
                            $this->sanitizeCsvCell($doc->document_type),
                            $this->sanitizeCsvCell($doc->document_number),
                            $this->sanitizeCsvCell($doc->expiry_date?->format('Y-m-d')),
                            $this->sanitizeCsvCell($doc->days_remaining),
                            $this->sanitizeCsvCell($doc->status_bucket_label),
                            $this->sanitizeCsvCell(number_format((float) $doc->renewal_cost_estimate_inr, 2, '.', '')),
                            $this->sanitizeCsvCell($doc->flagged_for_renewal ? 'Yes' : 'No'),
                        ]);
                    }
                }
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function printProjects(Request $request): View
    {
        $this->authorizeExport('projects');

        $projectId = $request->query('project_id');
        $location = $request->query('location');

        $query = Project::with(['manager', 'dailyReports']);
        if ($projectId) {
            $query->where('id', $projectId);
        }
        if ($location) {
            $query->where('location', 'like', "%{$location}%");
        }

        $projects = $query->orderBy('name')->get();

        return view('exports.print_projects', compact('projects'));
    }

    public function printAttendance(Request $request): View
    {
        $this->authorizeExport('attendance');

        $projectId = $request->query('project_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = AttendanceRecord::with(['employee', 'project', 'approver']);
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        if ($dateFrom) {
            $query->where('attendance_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('attendance_date', '<=', $dateTo);
        }

        $records = $query->orderBy('attendance_date', 'desc')->take(100)->get();

        return view('exports.print_attendance', compact('records', 'dateFrom', 'dateTo'));
    }

    public function printPayroll(Request $request): View
    {
        $this->authorizeExport('payroll');

        $periodMonth = $request->query('period_month', Carbon::now('Asia/Kolkata')->format('Y-m'));

        $records = PayrollRecord::with(['employee.primaryProject', 'approver'])
            ->where('period_month', $periodMonth)
            ->get();

        return view('exports.print_payroll', compact('records', 'periodMonth'));
    }

    public function printMachinery(Request $request): View
    {
        $this->authorizeExport('machinery');

        $machineries = Machinery::with(['currentProject', 'assignedDriver', 'documents'])->get();

        return view('exports.print_machinery', compact('machineries'));
    }

    private function sanitizeCsvCell(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        $str = (string) $value;

        // Prevent CSV Formula Injection: Prefix =, +, -, @ with a single quote
        if (preg_match('/^[\=\+\-\@]/', $str)) {
            return "'" . $str;
        }

        return $str;
    }

    private function authorizeExport(string $module): void
    {
        $role = auth()->user()->role;

        if ($role === 'admin') {
            return;
        }

        if ($role === 'project_manager' && in_array($module, ['projects', 'attendance'], true)) {
            return;
        }

        if ($role === 'hr_payroll' && in_array($module, ['attendance', 'payroll'], true)) {
            return;
        }

        if ($role === 'asset_manager' && in_array($module, ['machinery'], true)) {
            return;
        }

        abort(403, 'Your role is not authorized to export ' . ucfirst($module) . ' records.');
    }
}

