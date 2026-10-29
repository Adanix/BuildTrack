<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\PayrollRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollCalculationService
{
    /**
     * Calculate payroll metrics for a single employee in a given month (YYYY-MM).
     */
    public function calculateForEmployee(Employee $employee, string $periodMonth, float $allowances = 0.00, float $deductions = 0.00): array
    {
        $startDate = Carbon::createFromFormat('Y-m', $periodMonth, 'Asia/Kolkata')->startOfMonth()->toDateString();
        $endDate = Carbon::createFromFormat('Y-m', $periodMonth, 'Asia/Kolkata')->endOfMonth()->toDateString();

        $attendanceRecords = $employee->attendanceRecords()
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();

        $daysPresent = 0.0;
        $totalOvertimeHours = 0.0;

        foreach ($attendanceRecords as $record) {
            if ($record->status === 'present') {
                $daysPresent += 1.0;
            } elseif ($record->status === 'paid_leave') {
                $daysPresent += 1.0;
            } elseif ($record->status === 'half_day') {
                $daysPresent += 0.5;
            }

            // Only include approved overtime
            if ($record->overtime_approved && (float) $record->overtime_hours > 0) {
                $totalOvertimeHours += (float) $record->overtime_hours;
            }
        }

        // Base Earned
        $baseSalary = (float) $employee->base_salary_inr;
        if ($employee->wage_type === 'daily') {
            $baseEarned = round($daysPresent * $baseSalary, 2);
        } else {
            // monthly (26-working-day civil construction norm, capped at 26 days)
            $effectiveDays = min($daysPresent, 26.0);
            $baseEarned = round(($baseSalary / 26.0) * $effectiveDays, 2);
        }

        // Overtime Pay
        $otRate = (float) $employee->overtime_rate_per_hour_inr;
        $overtimePay = round($totalOvertimeHours * $otRate, 2);

        // Deductions safety check: deductions cannot exceed gross earnings
        $gross = $baseEarned + $overtimePay + $allowances;
        if ($deductions > $gross) {
            $deductions = $gross;
        }

        $netSalary = round($gross - $deductions, 2);
        if ($netSalary < 0) {
            $netSalary = 0.00;
        }

        return [
            'days_present' => $daysPresent,
            'total_overtime_hours' => $totalOvertimeHours,
            'base_earned_inr' => $baseEarned,
            'overtime_pay_inr' => $overtimePay,
            'allowances_inr' => round($allowances, 2),
            'deductions_inr' => round($deductions, 2),
            'net_salary_inr' => $netSalary,
        ];
    }

    /**
     * Run payroll generation for a given month across all active employees.
     * Skips approved and paid records.
     */
    public function generateForMonth(string $periodMonth): array
    {
        $employees = Employee::where('status', 'active')->get();

        $recalculatedCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($employees, $periodMonth, &$recalculatedCount, &$skippedCount) {
            foreach ($employees as $employee) {
                $existing = PayrollRecord::where('employee_id', $employee->id)
                    ->where('period_month', $periodMonth)
                    ->first();

                // If locked (approved or paid), strictly skip
                if ($existing && in_array($existing->status, ['approved', 'paid'], true)) {
                    $skippedCount++;
                    continue;
                }

                $allowances = $existing ? (float) $existing->allowances_inr : 0.00;
                $deductions = $existing ? (float) $existing->deductions_inr : 0.00;
                $allowanceNotes = $existing ? $existing->allowance_notes : null;
                $deductionNotes = $existing ? $existing->deduction_notes : null;

                $metrics = $this->calculateForEmployee($employee, $periodMonth, $allowances, $deductions);

                PayrollRecord::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'period_month' => $periodMonth,
                    ],
                    [
                        'days_present' => $metrics['days_present'],
                        'total_overtime_hours' => $metrics['total_overtime_hours'],
                        'base_earned_inr' => $metrics['base_earned_inr'],
                        'overtime_pay_inr' => $metrics['overtime_pay_inr'],
                        'allowances_inr' => $metrics['allowances_inr'],
                        'deductions_inr' => $metrics['deductions_inr'],
                        'net_salary_inr' => $metrics['net_salary_inr'],
                        'allowance_notes' => $allowanceNotes,
                        'deduction_notes' => $deductionNotes,
                        'status' => 'draft',
                    ]
                );

                $recalculatedCount++;
            }
        });

        return [
            'recalculated' => $recalculatedCount,
            'skipped' => $skippedCount,
            'message' => "Recalculated {$recalculatedCount} draft records; skipped {$skippedCount} locked (approved/paid) records",
        ];
    }
}
