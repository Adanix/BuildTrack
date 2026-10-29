<?php

namespace Tests\Feature;

use App\Models\DailyReport;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAndScopingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_foreman_and_driver_cannot_access_payroll(): void
    {
        $foreman = User::where('role', 'foreman')->first();
        $response = $this->actingAs($foreman)->get(route('payroll.index'));
        $response->assertStatus(403);

        $driver = User::where('role', 'driver')->first();
        $response = $this->actingAs($driver)->get(route('payroll.index'));
        $response->assertStatus(403);
    }

    public function test_project_manager_cannot_review_reports_for_another_managers_project(): void
    {
        $pm = User::where('role', 'project_manager')->first();
        $admin = User::where('role', 'admin')->first();

        // CW-NGP-03 is managed by admin (Rajesh Sharma)
        $ngpProject = Project::where('code', 'CW-NGP-03')->first();
        $report = DailyReport::where('project_id', $ngpProject->id)->first();

        // Vikram Patel (PM) attempts to review report on admin's project
        $response = $this->actingAs($pm)->post(route('daily-reports.review', $report), [
            'action' => 'reviewed',
            'review_notes' => 'Unauthorized review attempt',
        ]);

        $response->assertStatus(403);
    }

    public function test_foreman_cannot_review_daily_reports(): void
    {
        $foreman = User::where('role', 'foreman')->first();
        $report = DailyReport::first();

        $response = $this->actingAs($foreman)->post(route('daily-reports.review', $report), [
            'action' => 'reviewed',
        ]);

        $response->assertStatus(403);
    }
}
