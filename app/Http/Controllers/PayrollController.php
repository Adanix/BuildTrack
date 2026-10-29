<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\PayrollRecord;
use App\Services\PayrollCalculationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizePayrollAccess();

        $currentMonth = Carbon::now('Asia/Kolkata')->format('Y-m');
        $periodMonth = $request->query('period_month', $currentMonth);

        $records = PayrollRecord::with(['employee.primaryProject', 'approver'])
            ->where('period_month', $periodMonth)
            ->join('employees', 'payroll_records.employee_id', '=', 'employees.id')
            ->orderBy('employees.full_name')
            ->select('payroll_records.*')
            ->get();

        $stats = [
            'total_base' => $records->sum('base_earned_inr'),
            'total_ot' => $records->sum('overtime_pay_inr'),
            'total_allowances' => $records->sum('allowances_inr'),
            'total_deductions' => $records->sum('deductions_inr'),
            'total_net' => $records->sum('net_salary_inr'),
            'draft_count' => $records->where('status', 'draft')->count(),
            'approved_count' => $records->where('status', 'approved')->count(),
            'paid_count' => $records->where('status', 'paid')->count(),
        ];

        // Available periods for the last 6 months
        $periods = [];
        for ($i = 0; $i < 6; $i++) {
            $periods[] = Carbon::now('Asia/Kolkata')->subMonths($i)->format('Y-m');
        }

        return view('payroll.index', compact('records', 'periodMonth', 'stats', 'periods'));
    }

    public function generate(Request $request, PayrollCalculationService $service): RedirectResponse
    {
        $this->authorizePayrollAccess();

        $validated = $request->validate([
            'period_month' => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
        ]);

        $result = $service->generateForMonth($validated['period_month']);

        ActivityLog::log(
            'Payroll',
            'Payroll Batch Calculated',
            "Generated payroll for period {$validated['period_month']}: {$result['recalculated']} draft recalculated, {$result['skipped']} locked records skipped."
        );

        return redirect()->route('payroll.index', ['period_month' => $validated['period_month']])
            ->with('success', $result['message']);
    }

    public function update(Request $request, PayrollRecord $payroll): RedirectResponse
    {
        $this->authorizePayrollAccess();

        if ($payroll->status !== 'draft') {
            return back()->with('error', 'Approved and Paid payroll records are locked financial snapshots and cannot be edited.');
        }

        $validated = $request->validate([
            'allowances_inr' => 'required|numeric|min:0',
            'deductions_inr' => 'required|numeric|min:0',
            'allowance_notes' => 'nullable|string|max:255',
            'deduction_notes' => 'nullable|string|max:255',
        ]);

        $gross = (float) $payroll->base_earned_inr + (float) $payroll->overtime_pay_inr + (float) $validated['allowances_inr'];
        $deductions = (float) $validated['deductions_inr'];

        if ($deductions > $gross) {
            return back()->with('error', "Deductions (INR {$deductions}) cannot exceed gross earnings (INR {$gross}).");
        }

        $netSalary = round($gross - $deductions, 2);

        $payroll->update([
            'allowances_inr' => $validated['allowances_inr'],
            'deductions_inr' => $deductions,
            'net_salary_inr' => $netSalary,
            'allowance_notes' => $validated['allowance_notes'],
            'deduction_notes' => $validated['deduction_notes'],
        ]);

        ActivityLog::log(
            'Payroll',
            'Draft Payroll Adjusted',
            "Adjusted allowances/deductions for {$payroll->employee->full_name} for period {$payroll->period_month}. Net salary: INR {$netSalary}."
        );

        return back()->with('success', "Allowances & deductions updated for {$payroll->employee->full_name}.");
    }

    public function approve(PayrollRecord $payroll): RedirectResponse
    {
        $this->authorizePayrollAccess();

        if ($payroll->status !== 'draft') {
            return back()->with('error', 'Only draft payroll records can be approved.');
        }

        $payroll->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now('Asia/Kolkata'),
        ]);

        ActivityLog::log(
            'Payroll',
            'Payroll Record Approved',
            "Approved payroll of INR {$payroll->net_salary_inr} for {$payroll->employee->full_name} ({$payroll->period_month}). Record is now locked."
        );

        return back()->with('success', "Payroll record for {$payroll->employee->full_name} approved and locked.");
    }

    public function markPaid(PayrollRecord $payroll): RedirectResponse
    {
        $this->authorizePayrollAccess();

        if ($payroll->status !== 'approved') {
            return back()->with('error', 'Only approved payroll records can be marked as paid.');
        }

        $payroll->update([
            'status' => 'paid',
            'paid_at' => now('Asia/Kolkata'),
        ]);

        ActivityLog::log(
            'Payroll',
            'Payroll Disbursed',
            "Marked payroll of INR {$payroll->net_salary_inr} as paid for {$payroll->employee->full_name} ({$payroll->period_month})."
        );

        return back()->with('success', "Salary disbursement recorded for {$payroll->employee->full_name}.");
    }

    public function payslip(PayrollRecord $payroll): View
    {
        $this->authorizePayrollAccess();

        $payroll->load(['employee.primaryProject', 'approver']);

        return view('payroll.payslip', compact('payroll'));
    }

    private function authorizePayrollAccess(): void
    {
        $role = auth()->user()->role;
        if (! in_array($role, ['admin', 'hr_payroll'], true)) {
            abort(403, 'Unauthorized access: Payroll management is restricted to Owner/Director and HR/Payroll officers.');
        }
    }
}

