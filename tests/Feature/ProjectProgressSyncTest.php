<?php

namespace Tests\Feature;

use App\Models\DailyReport;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectProgressSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_submitting_report_leaves_project_progress_unchanged(): void
    {
        $foreman = User::where('role', 'foreman')->first();
        $project = Project::where('code', 'CW-PUN-01')->first();
        $initialProgress = $project->overall_progress; // 68%

        $this->actingAs($foreman)->post(route('daily-reports.store'), [
            'project_id' => $project->id,
            'report_date' => Carbon::today('Asia/Kolkata')->toDateString(),
            'work_category' => 'Concrete Pouring (RCC/PCC)',
            'activity_summary' => 'Pier Cap concrete pour completed',
            'quantity_completed' => 30.00,
            'quantity_unit' => 'm3',
            'reported_progress_percent' => 75,
            'labor_count' => 22,
            'weather' => 'Clear / Sunny',
        ]);

        $project->refresh();
        $this->assertEquals($initialProgress, $project->overall_progress, 'Submitting a report must not modify project progress before review.');
    }

    public function test_approving_report_updates_project_progress_deterministically(): void
    {
        $pm = User::where('role', 'project_manager')->first();
        $project = Project::where('code', 'CW-PUN-01')->first();

        // Create a new submitted report with 72% progress
        $report = DailyReport::create([
            'project_id' => $project->id,
            'reported_by' => User::where('role', 'foreman')->first()->id,
            'report_date' => Carbon::today('Asia/Kolkata')->toDateString(),
            'work_category' => 'Concrete Pouring (RCC/PCC)',
            'activity_summary' => 'Pier cap concrete pour verified',
            'quantity_completed' => 40.00,
            'quantity_unit' => 'm3',
            'reported_progress_percent' => 72,
            'labor_count' => 20,
            'weather' => 'Clear / Sunny',
            'status' => 'submitted',
        ]);

        $this->actingAs($pm)->post(route('daily-reports.review', $report), [
            'action' => 'reviewed',
            'review_notes' => 'Approved cube test passed',
        ]);

        $project->refresh();
        $this->assertEquals(72, $project->overall_progress, 'Reviewed report must advance project overall progress.');
    }

    public function test_older_reviewed_report_does_not_decrease_project_progress(): void
    {
        $pm = User::where('role', 'project_manager')->first();
        $project = Project::where('code', 'CW-PUN-01')->first();
        $currentProgress = $project->overall_progress; // 68%

        // Create an older report (subDays(20)) with reported progress 50%
        $oldReport = DailyReport::create([
            'project_id' => $project->id,
            'reported_by' => User::where('role', 'foreman')->first()->id,
            'report_date' => Carbon::today('Asia/Kolkata')->subDays(20)->toDateString(),
            'work_category' => 'Earthwork & Excavation',
            'activity_summary' => 'Late backfilled documentation',
            'quantity_completed' => 50.00,
            'quantity_unit' => 'm3',
            'reported_progress_percent' => 50,
            'labor_count' => 15,
            'weather' => 'Clear / Sunny',
            'status' => 'submitted',
        ]);

        $this->actingAs($pm)->post(route('daily-reports.review', $oldReport), [
            'action' => 'reviewed',
            'review_notes' => 'Late record approved',
        ]);

        $project->refresh();
        $this->assertEquals($currentProgress, $project->overall_progress, 'Older report must not reduce official project progress.');
    }
}
