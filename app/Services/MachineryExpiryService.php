<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\MachineryDocument;
use Carbon\Carbon;

class MachineryExpiryService
{
    /**
     * Scan all machinery documents where expiry_date <= today + 30 days.
     * Log alerts idempotently (once per document per calendar day in Asia/Kolkata).
     */
    public function checkExpiries(?int $userId = null): array
    {
        $today = Carbon::today('Asia/Kolkata');
        $thirtyDaysThreshold = $today->copy()->addDays(30)->toDateString();

        $documents = MachineryDocument::with('machinery')
            ->where('expiry_date', '<=', $thirtyDaysThreshold)
            ->get();

        $scannedCount = $documents->count();
        $alertsCreated = 0;
        $skippedCount = 0;

        foreach ($documents as $doc) {
            $lastLogged = $doc->last_reminder_logged_at
                ? Carbon::parse($doc->last_reminder_logged_at, 'Asia/Kolkata')->startOfDay()
                : null;

            // Idempotent: If already logged today, skip creating another log
            if ($lastLogged && $lastLogged->equalTo($today)) {
                $skippedCount++;
                continue;
            }

            $days = $doc->days_remaining;
            $urgency = match (true) {
                $days < 0 => "EXPIRED (" . abs($days) . " days ago)",
                $days <= 7 => "CRITICAL (Expires in {$days} days)",
                default => "EXPIRING SOON (Expires in {$days} days)",
            };

            $machineryName = $doc->machinery ? "{$doc->machinery->name} ({$doc->machinery->registration_number})" : "Machinery #{$doc->machinery_id}";

            ActivityLog::log(
                'Compliance',
                'Expiry Reminder Alert',
                "[In-App Alert] Document '{$doc->document_type}' (#{$doc->document_number}) for {$machineryName} is {$urgency}. Expiry Date: {$doc->expiry_date->format('d M Y')}.",
                $userId
            );

            $doc->update([
                'last_reminder_logged_at' => now('Asia/Kolkata'),
            ]);

            $alertsCreated++;
        }

        return [
            'scanned' => $scannedCount,
            'alerts_created' => $alertsCreated,
            'already_alerted_today' => $skippedCount,
            'message' => "Expiry check completed: {$scannedCount} documents scanned, {$alertsCreated} new compliance alerts generated, {$skippedCount} already alerted today.",
        ];
    }
}
