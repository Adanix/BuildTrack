<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportsAndDocsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_public_docs_renders_successfully_for_guests(): void
    {
        $response = $this->get(route('docs'));
        $response->assertStatus(200);
        $response->assertSee('BuildTrack India System Documentation');
    }

    public function test_docs_renders_successfully_for_authenticated_users(): void
    {
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get(route('docs'));
        $response->assertStatus(200);
        $response->assertSee('BuildTrack India System Documentation');
    }

    public function test_csv_export_includes_utf8_bom_and_sanitizes_formula_injection(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Create a project with potential formula injection title
        Project::create([
            'code' => 'CW-SEC-01',
            'name' => '=cmd|/c calc!A0',
            'client_name' => '@MaliciousClient',
            'location' => '+DangerousLocation',
            'budget_inr' => 1000000.00,
            'start_date' => now()->toDateString(),
            'target_end_date' => now()->addMonths(6)->toDateString(),
            'overall_progress' => 10,
            'status' => 'planned',
        ]);

        $response = $this->actingAs($admin)->get(route('exports.projects.csv'));
        $response->assertStatus(200);

        $content = $response->streamedContent();

        // Check for UTF-8 BOM
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content, 'CSV must start with UTF-8 BOM.');

        // Check formula prefix protection
        $this->assertStringContainsString("'=cmd|/c calc!A0", $content, 'Leading = must be prefixed with single quote.');
        $this->assertStringContainsString("'@MaliciousClient", $content, 'Leading @ must be prefixed with single quote.');
        $this->assertStringContainsString("'+DangerousLocation", $content, 'Leading + must be prefixed with single quote.');
    }

    public function test_print_views_render_successfully(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('exports.projects.print'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('exports.attendance.print'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('exports.payroll.print'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('exports.machinery.print'));
        $response->assertStatus(200);
    }
}
