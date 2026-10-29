<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Documentation & Feature Guide - BuildTrack India</title>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="/js/alpine.min.js"></script>
</head>
<body class="h-full text-slate-800 dark:text-slate-100 bg-slate-100 dark:bg-slate-950 antialiased font-sans flex flex-col transition-colors duration-150">

    <!-- Top Navigation -->
    <header class="bg-slate-900 dark:bg-slate-950 text-white border-b border-slate-800 py-3.5 px-4 sm:px-6 lg:px-8 sticky top-0 z-30 shadow-md">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-500 text-slate-950 font-black text-sm flex items-center justify-center">
                        BT
                    </div>
                    <div>
                        <div class="font-bold text-white text-sm tracking-tight leading-none">BuildTrack India</div>
                        <div class="text-[10px] text-amber-400 font-semibold uppercase tracking-wider">Civil Works v3.0</div>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <!-- Theme Toggle Button -->
                <button type="button" 
                    x-data="{ 
                        dark: document.documentElement.classList.contains('dark'),
                        toggle() {
                            this.dark = !this.dark;
                            if (this.dark) {
                                document.documentElement.classList.add('dark');
                                localStorage.setItem('theme', 'dark');
                            } else {
                                document.documentElement.classList.remove('dark');
                                localStorage.setItem('theme', 'light');
                            }
                        }
                    }" 
                    @click="toggle()" 
                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition cursor-pointer"
                    :title="dark ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
                    <template x-if="dark">
                        <span class="flex items-center gap-1 text-amber-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span class="hidden sm:inline">Light</span>
                        </span>
                    </template>
                    <template x-if="!dark">
                        <span class="flex items-center gap-1 text-slate-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            <span class="hidden sm:inline">Dark</span>
                        </span>
                    </template>
                </button>

                @auth
                <a href="{{ route('dashboard') }}" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-lg min-h-[32px] flex items-center">
                    &larr; Back to Dashboard
                </a>
                @else
                <a href="{{ route('login') }}" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-lg min-h-[32px] flex items-center">
                    Sign In &rarr;
                </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Docs Content Container -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">

        <!-- Header Hero -->
        <div class="border-b border-slate-200 dark:border-slate-800 pb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-900 dark:text-amber-300 border border-amber-200 dark:border-amber-800 mb-3">
                <span>Architecture Specification & Feature Runbook</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">BuildTrack India System Documentation</h1>
            <p class="text-base text-slate-600 dark:text-slate-400 mt-2 max-w-3xl">
                Civil Works & Field Operations Management System built on Laravel 13, PHP 8.5, MySQL 8.0, and Tailwind CSS 4. Standardized for Indian Rupee (INR) and Asia/Kolkata (IST).
            </p>
        </div>

        <!-- Section 1: Executive Overview & Architecture -->
        <section class="space-y-4">
            <h2 class="text-xl font-black text-slate-900 dark:text-white border-l-4 border-amber-500 pl-3">
                1. Executive Overview & System Architecture
            </h2>
            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                BuildTrack India unifies 3 traditionally fragmented civil construction workflows into a single cohesive database without bloat:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
                    <div class="font-bold text-slate-900 dark:text-white text-sm mb-1">Infrastructure Contracts</div>
                    <p class="text-slate-600 dark:text-slate-400">Flyover elevated works, storm water culverts, and industrial concrete road contracts with cumulative progress synchronization.</p>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
                    <div class="font-bold text-slate-900 dark:text-white text-sm mb-1">Workforce & INR Payroll</div>
                    <p class="text-slate-600 dark:text-slate-400">26-working-day monthly staff divisor vs. daily muster-roll wage math, overtime approval workflows, and immutable locking.</p>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
                    <div class="font-bold text-slate-900 dark:text-white text-sm mb-1">Fleet & RTO Compliance</div>
                    <p class="text-slate-600 dark:text-slate-400">Heavy equipment register with automated 30-day lookahead for PUC, comprehensive insurance, and fitness certificate renewals.</p>
                </div>
            </div>
        </section>

        <!-- Section 2: Interactive Role Access Matrix & Live Switcher -->
        <section class="space-y-4">
            <h2 class="text-xl font-black text-slate-900 dark:text-white border-l-4 border-amber-500 pl-3">
                2. Role-Based Access Control (RBAC) & Scoping Matrix
            </h2>
            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                Security is enforced server-side via middleware (<code class="bg-slate-200 dark:bg-slate-800 px-1 py-0.5 rounded text-xs">role:admin,hr_payroll</code>) and controller-level scoping checks:
            </p>

            <div class="overflow-x-auto w-full bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <table class="min-w-full text-xs text-left divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/70 font-bold text-slate-700 dark:text-slate-300 uppercase">
                        <tr>
                            <th class="py-3 px-3.5">Role</th>
                            <th class="py-3 px-3.5">Persona / Demo User</th>
                            <th class="py-3 px-3.5">Backend Scope & Access Limits</th>
                            @if(config('app.demo_mode'))
                            <th class="py-3 px-3.5 text-right">Interactive Test Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        <tr>
                            <td class="py-3 px-3.5 font-bold text-indigo-600 dark:text-indigo-400">admin</td>
                            <td class="py-3 px-3.5 font-semibold text-slate-900 dark:text-white">Rajesh Sharma (Owner / Director)</td>
                            <td class="py-3 px-3.5">Unscoped full access across all 4 sites, staff directory, payroll disburals, fleet, and audit trail.</td>
                            @if(config('app.demo_mode'))
                            <td class="py-3 px-3.5 text-right">
                                <form action="{{ route('demo.login') }}" method="POST" class="m-0 inline">
                                    @csrf
                                    <input type="hidden" name="role" value="admin">
                                    <button type="submit" class="px-2.5 py-1 rounded bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900 text-indigo-700 dark:text-indigo-300 font-bold cursor-pointer">Test as Admin</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        <tr>
                            <td class="py-3 px-3.5 font-bold text-sky-600 dark:text-sky-400">project_manager</td>
                            <td class="py-3 px-3.5 font-semibold text-slate-900 dark:text-white">Vikram Patel (Project Manager)</td>
                            <td class="py-3 px-3.5">Scoped to assigned contracts (<code class="text-[11px]">projects.manager_id = user.id</code>) for report review & OT approval. Read-only on portfolio. Cannot access Payroll (403).</td>
                            @if(config('app.demo_mode'))
                            <td class="py-3 px-3.5 text-right">
                                <form action="{{ route('demo.login') }}" method="POST" class="m-0 inline">
                                    @csrf
                                    <input type="hidden" name="role" value="project_manager">
                                    <button type="submit" class="px-2.5 py-1 rounded bg-sky-50 hover:bg-sky-100 dark:bg-sky-950/60 dark:hover:bg-sky-900 text-sky-700 dark:text-sky-300 font-bold cursor-pointer">Test as PM</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        <tr>
                            <td class="py-3 px-3.5 font-bold text-emerald-600 dark:text-emerald-400">foreman</td>
                            <td class="py-3 px-3.5 font-semibold text-slate-900 dark:text-white">Suresh Nair (Site Foreman)</td>
                            <td class="py-3 px-3.5">Mobile-first fast logger for concrete pours, earthwork and muster rolls. Cannot approve reports/OT or view Payroll (403).</td>
                            @if(config('app.demo_mode'))
                            <td class="py-3 px-3.5 text-right">
                                <form action="{{ route('demo.login') }}" method="POST" class="m-0 inline">
                                    @csrf
                                    <input type="hidden" name="role" value="foreman">
                                    <button type="submit" class="px-2.5 py-1 rounded bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900 text-emerald-700 dark:text-emerald-300 font-bold cursor-pointer">Test as Foreman</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        <tr>
                            <td class="py-3 px-3.5 font-bold text-purple-600 dark:text-purple-400">hr_payroll</td>
                            <td class="py-3 px-3.5 font-semibold text-slate-900 dark:text-white">Priya Menon (HR & Payroll Officer)</td>
                            <td class="py-3 px-3.5">Full access to Workforce master, Overtime authorization, INR Payroll generation/approval, and payslips. Read-only on sites.</td>
                            @if(config('app.demo_mode'))
                            <td class="py-3 px-3.5 text-right">
                                <form action="{{ route('demo.login') }}" method="POST" class="m-0 inline">
                                    @csrf
                                    <input type="hidden" name="role" value="hr_payroll">
                                    <button type="submit" class="px-2.5 py-1 rounded bg-purple-50 hover:bg-purple-100 dark:bg-purple-950/60 dark:hover:bg-purple-900 text-purple-700 dark:text-purple-300 font-bold cursor-pointer">Test as HR</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        <tr>
                            <td class="py-3 px-3.5 font-bold text-amber-600 dark:text-amber-400">asset_manager</td>
                            <td class="py-3 px-3.5 font-semibold text-slate-900 dark:text-white">Arun Deshmukh (Fleet & Asset Manager)</td>
                            <td class="py-3 px-3.5">Full management of Machinery Register, RTO compliance documents, and expiry reminder triggers. Cannot view Payroll (403).</td>
                            @if(config('app.demo_mode'))
                            <td class="py-3 px-3.5 text-right">
                                <form action="{{ route('demo.login') }}" method="POST" class="m-0 inline">
                                    @csrf
                                    <input type="hidden" name="role" value="asset_manager">
                                    <button type="submit" class="px-2.5 py-1 rounded bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/60 dark:hover:bg-amber-900 text-amber-700 dark:text-amber-300 font-bold cursor-pointer">Test as Asset Mgr</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        <tr>
                            <td class="py-3 px-3.5 font-bold text-teal-600 dark:text-teal-400">driver</td>
                            <td class="py-3 px-3.5 font-semibold text-slate-900 dark:text-white">Manoj Yadav (Operator / Driver)</td>
                            <td class="py-3 px-3.5">Mobile-first view scoped to assigned equipment (<code class="text-[11px]">machineries.assigned_driver_id = user.id</code>) to inspect PUC/insurance validity and flag renewals. Cannot view Payroll (403).</td>
                            @if(config('app.demo_mode'))
                            <td class="py-3 px-3.5 text-right">
                                <form action="{{ route('demo.login') }}" method="POST" class="m-0 inline">
                                    @csrf
                                    <input type="hidden" name="role" value="driver">
                                    <button type="submit" class="px-2.5 py-1 rounded bg-teal-50 hover:bg-teal-100 dark:bg-teal-950/60 dark:hover:bg-teal-900 text-teal-700 dark:text-teal-300 font-bold cursor-pointer">Test as Operator</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Section 3: Module 1 Deep-Dive -->
        <section class="space-y-4">
            <h2 class="text-xl font-black text-slate-900 dark:text-white border-l-4 border-amber-500 pl-3">
                3. Module 1 Deep-Dive: Projects & Deterministic Progress Sync
            </h2>
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300 space-y-3 leading-relaxed shadow-xs">
                <p>
                    <strong>Non-Decreasing Progress Rule (Rule B):</strong> Daily reports submitted by site foremen are logged as <code class="bg-slate-100 dark:bg-slate-800 px-1 py-0.5 rounded text-amber-800 dark:text-amber-300">submitted</code>. Reports in submitted or flagged status <strong>do not modify</strong> official project completion percentages.
                </p>
                <p>
                    Only when a report is verified and approved as <code class="bg-slate-100 dark:bg-slate-800 px-1 py-0.5 rounded text-emerald-800 dark:text-emerald-300">reviewed</code> by the Director or assigned Project Manager does the system evaluate:
                </p>
                <div class="p-3 bg-slate-50 dark:bg-slate-800/80 rounded-xl font-mono text-[11px] border border-slate-200 dark:border-slate-700">
                    new_progress = max(project.overall_progress, report.reported_progress_percent)<br>
                    // Only updates if report_date >= latest previously reviewed report date
                </div>
                <p>
                    This guarantees that older back-dated reports or corrections cannot accidentally reduce official progress.
                </p>
            </div>
        </section>

        <!-- Section 4: Module 2 Deep-Dive (Workforce, Overtime & INR Payroll) -->
        <section class="space-y-4">
            <h2 class="text-xl font-black text-slate-900 dark:text-white border-l-4 border-amber-500 pl-3">
                4. Module 2 Deep-Dive: Attendance Weights, Overtime & INR Payroll
            </h2>
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300 space-y-4 leading-relaxed shadow-xs">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm mb-2">1. Attendance Status Day Weights</h3>
                    <ul class="list-disc pl-5 space-y-1">
                        <li><strong>Present:</strong> Weight = 1.0 day &bull; Default Regular Hours = 8.00 &bull; Allowed Overtime = 0 to 12 hrs.</li>
                        <li><strong>Half Day:</strong> Weight = 0.5 day &bull; Default Regular Hours = 4.00 &bull; Allowed Overtime = 0 to 8 hrs.</li>
                        <li><strong>Paid Leave:</strong> Weight = 1.0 day &bull; Default Regular Hours = 0.00 &bull; Overtime = 0.00 hrs.</li>
                        <li><strong>Absent:</strong> Weight = 0.0 day &bull; Default Regular Hours = 0.00 &bull; Overtime = 0.00 hrs.</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm mb-2">2. Exact Payroll Calculation Formulas</h3>
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/80 rounded-xl font-mono text-[11px] border border-slate-200 dark:border-slate-700 space-y-2">
                        <div>
                            <strong>Daily Wage Workers:</strong><br>
                            base_earned_inr = round(days_present * base_salary_inr, 2)
                        </div>
                        <div>
                            <strong>Monthly Salaried Staff (26-Day Civil Norm, capped at 26):</strong><br>
                            effective_days = min(days_present, 26)<br>
                            base_earned_inr = round((base_salary_inr / 26) * effective_days, 2)
                        </div>
                        <div>
                            <strong>Approved Overtime Pay:</strong><br>
                            overtime_pay_inr = round(sum(approved_overtime_hours) * overtime_rate_per_hour_inr, 2)<br>
                            <em>(Note: Overtime where overtime_approved == false is strictly excluded from OT Pay)</em>
                        </div>
                        <div>
                            <strong>Net Salary Payable:</strong><br>
                            net_salary_inr = base_earned_inr + overtime_pay_inr + allowances_inr - deductions_inr<br>
                            <em>(Validated: deductions cannot exceed gross earnings; net salary cannot be negative)</em>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm mb-1">3. Immutable State Machine & Payroll Locking</h3>
                    <p>
                        Records transition strictly through: <code class="text-slate-800 dark:text-slate-200 font-semibold">draft &rarr; approved &rarr; paid</code>.
                        Approved and Paid records are locked financial snapshots. When the Payroll Generator is triggered, it <strong>strictly skips</strong> all approved and paid records and only recalculates draft records, displaying an explicit summary flash message.
                    </p>
                </div>
            </div>
        </section>

        <!-- Section 5: Module 3 Deep-Dive -->
        <section class="space-y-4">
            <h2 class="text-xl font-black text-slate-900 dark:text-white border-l-4 border-amber-500 pl-3">
                5. Module 3 Deep-Dive: Heavy Machinery & Compliance Expiry Engine
            </h2>
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300 space-y-3 leading-relaxed shadow-xs">
                <p>
                    All heavy machinery compliance certificates (Pollution Under Control, Motor Insurance, Fitness Certificates) are dynamically bucketed against <code class="bg-slate-100 dark:bg-slate-800 px-1 py-0.5 rounded text-slate-800 dark:text-slate-200">Carbon::today('Asia/Kolkata')</code>:
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center">
                    <div class="p-2.5 rounded-lg bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 font-bold">
                        Expired<br><span class="text-[10px] font-normal">days_remaining &lt; 0</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-orange-50 dark:bg-orange-950/50 border border-orange-200 dark:border-orange-800 text-orange-800 dark:text-orange-300 font-bold">
                        Critical<br><span class="text-[10px] font-normal">0 &le; days &le; 7</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 font-bold">
                        Expiring Soon<br><span class="text-[10px] font-normal">8 &le; days &le; 30</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 font-bold">
                        Valid<br><span class="text-[10px] font-normal">days &gt; 30</span>
                    </div>
                </div>
                <p>
                    <strong>Idempotency Rule (Rule D):</strong> Running <code class="bg-slate-100 dark:bg-slate-800 px-1 py-0.5 rounded text-slate-800 dark:text-slate-200">php artisan machinery:check-expiries</code> multiple times on the same calendar day logs exactly 1 compliance reminder per document per day in <code class="bg-slate-100 dark:bg-slate-800 px-1 py-0.5 rounded text-slate-800 dark:text-slate-200">activity_logs</code>, preventing notification spam.
                </p>
            </div>
        </section>

        <!-- Section 6: Adaptability & Future Extensibility Framework -->
        <section class="space-y-4">
            <h2 class="text-xl font-black text-slate-900 dark:text-white border-l-4 border-amber-500 pl-3">
                6. Adaptability & Future Extensibility Framework
            </h2>
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300 space-y-4 shadow-xs leading-relaxed">
                <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60">
                    <p class="font-bold text-amber-900 dark:text-amber-300 text-sm">
                        This web application is highly adaptable and designed to be continuously developed and expanded to fit the evolving needs, field situations, and project conditions of civil operations.
                    </p>
                    <p class="text-amber-800/90 dark:text-amber-300/80 text-[11px] mt-1">
                        Built upon a modular Laravel 13 MVC architecture, decoupled Service layer, and clean MySQL relational schemas, the platform can readily absorb customized workflows as construction contracts grow in scope and complexity.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 pt-1">
                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 space-y-1">
                        <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5 text-xs">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Field Telematics & Equipment IoT
                        </div>
                        <p class="text-slate-600 dark:text-slate-400 text-[11px]">
                            Connect directly to heavy machinery GPS trackers, fuel telemetry probes, and digital engine hour-meters via scheduled background webhooks to automate maintenance intervals and runtime logging.
                        </p>
                    </div>

                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 space-y-1">
                        <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5 text-xs">
                            <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                            Subcontractor & Vendor Portals
                        </div>
                        <p class="text-slate-600 dark:text-slate-400 text-[11px]">
                            Introduce external guest role scopes allowing specialized shuttering, rebar, or earthwork subcontractors to submit daily progress logs and crew attendance directly for internal review.
                        </p>
                    </div>

                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 space-y-1">
                        <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5 text-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Statutory Compliance & Local Taxes
                        </div>
                        <p class="text-slate-600 dark:text-slate-400 text-[11px]">
                            Extend the 26-day civil payroll calculator to automatically compute Indian corporate deductions including Employees' Provident Fund (EPF), ESIC health contributions, Professional Tax, and TDS brackets.
                        </p>
                    </div>

                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 space-y-1">
                        <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5 text-xs">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            Multi-Tier Engineering Sign-offs
                        </div>
                        <p class="text-slate-600 dark:text-slate-400 text-[11px]">
                            Tailor verification chains to enforce dual Quality Assurance (QA) and Client Consultant approvals before daily concrete pours or earthwork volumes sync into official contract billings.
                        </p>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <footer class="bg-slate-900 dark:bg-slate-950 border-t border-slate-800 text-slate-400 py-6 text-center text-xs">
        BuildTrack India &bull; Civil Works & Field Operations Management System (v3.0) &bull; Standardized for INR & IST
    </footer>
</body>
</html>
