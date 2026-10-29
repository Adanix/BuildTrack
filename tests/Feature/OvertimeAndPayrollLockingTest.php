<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\PayrollRecord;
use App\Models\User;
use App\Services\PayrollCalculationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OvertimeAndPayrollLockingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_unapproved_overtime_is_excluded_from_payroll_calculation(): void
    {
        $employee = Employee::where('wage_type', 'daily')->first();
        $currentMonth = Carbon::now('Asia/Kolkata')->format('Y-m');

        // Set all overtime for this employee to unapproved
        AttendanceRecord::where('employee_id', $employee->id)->update([
            'overtime_approved' => false,
            'overtime_approved_by' => null,
            'overtime_approved_at' => null,
        ]);

        $service = app(PayrollCalculationService::class);
        $metrics = $service->calculateForEmployee($employee, $currentMonth);

        $this->assertEquals(0.00, $metrics['total_overtime_hours']);
        $this->assertEquals(0.00, $metrics['overtime_pay_inr'], 'Unapproved overtime must result in 0.00 OT pay.');
    }

    public function test_approving_overtime_includes_it_in_payroll(): void
    {
        $employee = Employee::where('wage_type', 'daily')->first();
        $currentMonth = Carbon::now('Asia/Kolkata')->format('Y-m');

        // Add 5 hours of approved overtime
        $firstRecord = AttendanceRecord::where('employee_id', $employee->id)->first();
        $firstRecord->update([
            'overtime_hours' => 5.00,
            'overtime_approved' => true,
        ]);

        $service = app(PayrollCalculationService::class);
        $metrics = $service->calculateForEmployee($employee, $currentMonth);

        $expectedOtPay = round(5.00 * (float) $employee->overtime_rate_per_hour_inr, 2);
        $this->assertGreaterThanOrEqual(5.00, $metrics['total_overtime_hours']);
        $this->assertGreaterThanOrEqual($expectedOtPay, $metrics['overtime_pay_inr']);
    }

    public function test_approved_and_paid_payroll_records_are_locked_and_skipped(): void
    {
        $hr = User::where('role', 'hr_payroll')->first();
        $currentMonth = Carbon::now('Asia/Kolkata')->format('Y-m');

        // Verify we have approved and paid records from seeder
        $approvedRecord = PayrollRecord::where('status', 'approved')->first();
        $originalNetSalary = $approvedRecord->net_salary_inr;

        // Recalculate payroll
        $response = $this->actingAs($hr)->post(route('payroll.generate'), [
            'period_month' => $currentMonth,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify approved record was untouched
        $approvedRecord->refresh();
        $this->assertEquals('approved', $approvedRecord->status);
        $this->assertEquals($originalNetSalary, $approvedRecord->net_salary_inr, 'Approved payroll record must remain locked.');
    }
}

