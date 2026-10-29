<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\DailyReport;
use App\Models\Project;

class ProjectProgressSyncService
{
    /**
     * Synchronize project overall progress upon daily report review.
     */
    public function syncOnReportReview(DailyReport $reviewedReport): void
    {
        if ($reviewedReport->status !== 'reviewed') {
            return;
        }

        $project = $reviewedReport->project;
        if (! $project) {
            return;
        }

        // Get the latest reviewed report by report_date DESC, id DESC
        $latestReviewedReport = DailyReport::where('project_id', $project->id)
            ->where('status', 'reviewed')
            ->orderBy('report_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if (! $latestReviewedReport) {
            return;
        }

        // Check if this reviewed report is on or after the date of the latest reviewed report
        // (i.e. it's the latest report or shares the latest date)
        if ($reviewedReport->report_date >= $latestReviewedReport->report_date) {
            $newProgress = max((int) $project->overall_progress, (int) $reviewedReport->reported_progress_percent);
            if ($newProgress !== (int) $project->overall_progress) {
                $oldProgress = $project->overall_progress;
                $project->update(['overall_progress' => $newProgress]);

                ActivityLog::log(
                    'Projects',
                    'Progress Updated via Site Report',
                    "Project '{$project->name}' progress updated from {$oldProgress}% to {$newProgress}% based on reviewed Daily Report #{$reviewedReport->id} (Date: {$reviewedReport->report_date->format('d M Y')})."
                );
            }
        }
    }
}

