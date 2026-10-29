<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DemoAuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $demoUsers = User::whereIn('role', [
            'admin',
            'project_manager',
            'foreman',
            'hr_payroll',
            'asset_manager',
            'driver',
        ])->get()->keyBy('role');

        return view('auth.login', compact('demoUsers'));
    }

    public function demoLogin(Request $request): RedirectResponse
    {
        if (! config('app.demo_mode', false)) {
            abort(403, 'Demo login is only available when DEMO_MODE is true.');
        }

        $validated = $request->validate([
            'role' => 'required|string|in:admin,project_manager,foreman,hr_payroll,asset_manager,driver',
        ]);

        $user = User::where('role', $validated['role'])->first();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Demo account for this role was not found.');
        }

        if (! $user->is_active) {
            return redirect()->route('login')->with('error', "The account for {$user->name} is currently inactive and cannot be accessed.");
        }

        Auth::login($user);
        $request->session()->regenerate();

        ActivityLog::log(
            'Auth',
            '1-Click Demo Login',
            "Authenticated as {$user->name} ({$user->role_display_name}) via Demo Role Selector.",
            $user->id
        );

        return redirect()->route('dashboard')->with('success', "Welcome back, {$user->name}! Active Role: {$user->role_display_name}.");
    }

    public function switchRole(Request $request): RedirectResponse
    {
        if (! config('app.demo_mode', false)) {
            abort(403, 'Role switching is only available when DEMO_MODE is true.');
        }

        $validated = $request->validate([
            'role' => 'required|string|in:admin,project_manager,foreman,hr_payroll,asset_manager,driver',
        ]);

        $user = User::where('role', $validated['role'])->first();

        if (! $user) {
            return back()->with('error', 'Selected demo user was not found.');
        }

        if (! $user->is_active) {
            return back()->with('error', "Cannot switch: {$user->name}'s account is currently inactive.");
        }

        $previousUser = Auth::user();
        Auth::login($user);
        $request->session()->regenerate();

        ActivityLog::log(
            'Auth',
            'Live Role Switched',
            "Switched active session from {$previousUser->name} ({$previousUser->role}) to {$user->name} ({$user->role_display_name}).",
            $user->id
        );

        return redirect()->route('dashboard')->with('success', "Switched role to {$user->name} ({$user->role_display_name}).");
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors(['email' => 'This account has been deactivated.']);
            }

            $request->session()->regenerate();

            ActivityLog::log('Auth', 'Standard Login', "User {$user->email} logged in with credentials.", $user->id);

            return redirect()->intended(route('dashboard'))->with('success', "Welcome, {$user->name}!");
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            ActivityLog::log('Auth', 'Logout', "User {$user->email} logged out.", $user->id);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been successfully logged out.');
    }
}

