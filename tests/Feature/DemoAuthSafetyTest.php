<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class DemoAuthSafetyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_demo_login_works_when_demo_mode_is_true(): void
    {
        Config::set('app.demo_mode', true);

        $response = $this->post(route('demo.login'), [
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('admin', auth()->user()->role);
    }

    public function test_demo_switch_role_works_and_switches_active_user(): void
    {
        Config::set('app.demo_mode', true);

        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $response = $this->post(route('demo.switch-role'), [
            'role' => 'project_manager',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals('project_manager', auth()->user()->role);
        $this->assertEquals('Vikram Patel', auth()->user()->name);
    }

    public function test_inactive_users_are_rejected_from_demo_login(): void
    {
        Config::set('app.demo_mode', true);

        $foreman = User::where('role', 'foreman')->first();
        $foreman->update(['is_active' => false]);

        $response = $this->post(route('demo.login'), [
            'role' => 'foreman',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_demo_endpoints_abort_403_when_demo_mode_is_false(): void
    {
        Config::set('app.demo_mode', false);

        $response = $this->post(route('demo.login'), [
            'role' => 'admin',
        ]);

        $response->assertStatus(403);
    }
}
