<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Machinery;
use App\Models\MachineryDocument;
use App\Models\User;
use App\Services\MachineryExpiryService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplianceExpiryAndIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_document_status_buckets_compute_accurately(): void
    {
        $today = Carbon::today('Asia/Kolkata');
        $machine = Machinery::first();

        $expiredDoc = MachineryDocument::create([
            'machinery_id' => $machine->id,
            'document_type' => 'Pollution Under Control (PUC)',
            'document_number' => 'PUC-TEST-01',
            'issue_date' => $today->copy()->subYear()->toDateString(),
            'expiry_date' => $today->copy()->subDays(5)->toDateString(),
        ]);
        $this->assertEquals('expired', $expiredDoc->status_bucket);

        $criticalDoc = MachineryDocument::create([
            'machinery_id' => $machine->id,
            'document_type' => 'Fitness Certificate (FC)',
            'document_number' => 'FC-TEST-02',
            'issue_date' => $today->copy()->subYear()->toDateString(),
            'expiry_date' => $today->copy()->addDays(3)->toDateString(),
        ]);
        $this->assertEquals('critical', $criticalDoc->status_bucket);

        $soonDoc = MachineryDocument::create([
            'machinery_id' => $machine->id,
            'document_type' => 'Comprehensive Motor Insurance',
            'document_number' => 'INS-TEST-03',
            'issue_date' => $today->copy()->subYear()->toDateString(),
            'expiry_date' => $today->copy()->addDays(15)->toDateString(),
        ]);
        $this->assertEquals('expiring_soon', $soonDoc->status_bucket);

        $validDoc = MachineryDocument::create([
            'machinery_id' => $machine->id,
            'document_type' => 'Road Tax / National Permit',
            'document_number' => 'TAX-TEST-04',
            'issue_date' => $today->copy()->subMonths(2)->toDateString(),
            'expiry_date' => $today->copy()->addDays(90)->toDateString(),
        ]);
        $this->assertEquals('valid', $validDoc->status_bucket);
    }

    public function test_machinery_expiry_check_is_idempotent_per_day(): void
    {
        $service = app(MachineryExpiryService::class);

        // Run 1: Should generate alerts for expired/critical/expiring_soon docs (9 docs from seed)
        $result1 = $service->checkExpiries();
        $this->assertGreaterThan(0, $result1['alerts_created']);
        $initialAlertCount = ActivityLog::where('module', 'Compliance')->count();

        // Run 2: Immediately re-running on the same calendar day must create 0 new alerts
        $result2 = $service->checkExpiries();
        $this->assertEquals(0, $result2['alerts_created']);
        $this->assertGreaterThan(0, $result2['already_alerted_today']);

        $secondaryAlertCount = ActivityLog::where('module', 'Compliance')->count();
        $this->assertEquals($initialAlertCount, $secondaryAlertCount, 'Re-running check on the same day must not duplicate activity logs.');
    }

    public function test_driver_renewal_flagging_works(): void
    {
        $driver = User::where('role', 'driver')->first();
        // Manoj Yadav is assigned to MCH-EX-01
        $assignedMachine = Machinery::where('assigned_driver_id', $driver->id)->first();
        $doc = $assignedMachine->documents->first();

        $response = $this->actingAs($driver)->post(route('compliance.flag', $doc), [
            'flag_notes' => 'Smoke emission test equipment was offline; renewal scheduled.',
        ]);

        $response->assertRedirect();
        $doc->refresh();
        $this->assertTrue($doc->flagged_for_renewal);
        $this->assertEquals($driver->id, $doc->flagged_by);
        $this->assertEquals('Smoke emission test equipment was offline; renewal scheduled.', $doc->flag_notes);
    }
}

