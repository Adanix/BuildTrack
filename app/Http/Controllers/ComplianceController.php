<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\MachineryDocument;
use App\Services\MachineryExpiryService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplianceController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $tab = $request->query('tab', 'all');

        $query = MachineryDocument::with(['machinery.assignedDriver', 'machinery.currentProject', 'flagger']);

        // Driver scoping
        if ($user->isDriver()) {
            $query->whereHas('machinery', fn ($q) => $q->where('assigned_driver_id', $user->id));
        }

        $allDocs = (clone $query)->get();

        // Count tabs
        $counts = [
            'all' => $allDocs->count(),
            'expired' => $allDocs->where('status_bucket', 'expired')->count(),
            'critical' => $allDocs->where('status_bucket', 'critical')->count(),
            'expiring_soon' => $allDocs->where('status_bucket', 'expiring_soon')->count(),
            'flagged' => $allDocs->where('flagged_for_renewal', true)->count(),
            'valid' => $allDocs->where('status_bucket', 'valid')->count(),
        ];

        // Filter by tab
        $documents = match ($tab) {
            'expired' => $allDocs->where('status_bucket', 'expired'),
            'critical' => $allDocs->where('status_bucket', 'critical'),
            'expiring_soon' => $allDocs->where('status_bucket', 'expiring_soon'),
            'flagged' => $allDocs->where('flagged_for_renewal', true),
            'valid' => $allDocs->where('status_bucket', 'valid'),
            default => $allDocs,
        };

        // Sort by expiry_date asc
        $documents = $documents->sortBy('expiry_date');

        return view('compliance.index', compact('documents', 'counts', 'tab'));
    }

    public function flagDocument(Request $request, MachineryDocument $document): RedirectResponse
    {
        $user = auth()->user();

        // Driver scoping
        if ($user->isDriver()) {
            if ($document->machinery && $document->machinery->assigned_driver_id !== $user->id) {
                abort(403, 'Drivers can only flag compliance documents for their assigned equipment.');
            }
        }

        $validated = $request->validate([
            'flag_notes' => 'required|string|max:500',
        ]);

        $document->update([
            'flagged_for_renewal' => true,
            'flagged_by' => $user->id,
            'flagged_at' => now('Asia/Kolkata'),
            'flag_notes' => $validated['flag_notes'],
        ]);

        ActivityLog::log(
            'Compliance',
            'Renewal Flagged',
            "{$user->name} flagged {$document->document_type} (#{$document->document_number}) for {$document->machinery->name} for renewal: '{$validated['flag_notes']}'."
        );

        return back()->with('success', "Document flagged for renewal successfully.");
    }

    public function checkExpiries(MachineryExpiryService $service): RedirectResponse
    {
        $user = auth()->user();
        if (! in_array($user->role, ['admin', 'asset_manager'], true)) {
            abort(403, 'Only administrators and fleet asset managers can trigger manual compliance expiry scans.');
        }

        $result = $service->checkExpiries($user->id);

        return back()->with('success', $result['message']);
    }
}

