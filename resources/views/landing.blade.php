<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildTrack India - Civil Works & Field Operations Management System</title>
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
<body class="h-full flex flex-col font-sans antialiased text-slate-800 dark:text-slate-200 bg-slate-50 dark:bg-slate-950 selection:bg-amber-500 selection:text-slate-950 transition-colors duration-150" x-data="{ mobileNav: false }">

    <!-- Top Announcement Bar -->
    <div class="bg-gradient-to-r from-amber-600 via-amber-500 to-amber-600 text-slate-950 text-xs py-2 px-4 font-bold text-center tracking-wide shadow-xs">
        <div class="max-w-7xl mx-auto flex items-center justify-center gap-2">
            <span class="inline-block px-1.5 py-0.5 rounded bg-slate-950 text-amber-400 text-[10px] uppercase font-black">v3.0 Final</span>
            <span>Standardized for Indian Civil Works &bull; INR Decimal(14,2) &bull; Asia/Kolkata (IST)</span>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 transition-colors duration-150">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-500 text-slate-950 font-black text-base flex items-center justify-center shadow-md">
                    BT
                </div>
                <div>
                    <span class="font-extrabold text-slate-900 dark:text-white text-base tracking-tight leading-none block">BuildTrack India</span>
                    <span class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold uppercase tracking-wider block">Civil Works & Fleet Ops</span>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-6 text-xs font-semibold text-slate-600 dark:text-slate-300">
                <a href="#features" class="hover:text-amber-600 dark:hover:text-amber-400 transition">Core Modules</a>
                <a href="#projects" class="hover:text-amber-600 dark:hover:text-amber-400 transition">Live Portfolio</a>
                <a href="#roles" class="hover:text-amber-600 dark:hover:text-amber-400 transition">Role Personas</a>
                <a href="#compliance" class="hover:text-amber-600 dark:hover:text-amber-400 transition">RTO Compliance</a>
                <a href="{{ route('docs') }}" class="hover:text-amber-600 dark:hover:text-amber-400 transition flex items-center gap-1">
                    <span>System Docs</span>
                </a>
            </nav>

            <!-- Header Action Buttons -->
            <div class="hidden sm:flex items-center gap-3">
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
                    class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 transition cursor-pointer min-h-[40px] min-w-[40px] flex items-center justify-center"
                    :title="dark ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
                    <template x-if="dark">
                        <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </template>
                    <template x-if="!dark">
                        <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </template>
                </button>

                @auth
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-xs transition min-h-[40px] flex items-center">
                    Open Dashboard &rarr;
                </a>
                @else
                <a href="{{ route('login') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-amber-600 dark:hover:text-white transition">
                    Sign In
                </a>
                <a href="{{ route('login') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-xs transition min-h-[40px] flex items-center">
                    Interactive Demo &rarr;
                </a>
                @endauth
            </div>

            <!-- Mobile Hamburger Button -->
            <div class="flex items-center gap-1.5 md:hidden">
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
                    class="p-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg min-h-[44px] min-w-[44px] flex items-center justify-center">
                    <template x-if="dark">
                        <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </template>
                    <template x-if="!dark">
                        <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </template>
                </button>

                <button type="button" @click="mobileNav = !mobileNav" class="p-2 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-lg min-h-[44px] min-w-[44px] flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileNav" x-cloak class="md:hidden bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 py-4 space-y-2 text-sm font-semibold">
            <a href="#features" @click="mobileNav = false" class="block py-2 text-slate-700 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400">Core Modules</a>
            <a href="#projects" @click="mobileNav = false" class="block py-2 text-slate-700 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400">Live Portfolio</a>
            <a href="#roles" @click="mobileNav = false" class="block py-2 text-slate-700 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400">Role Personas</a>
            <a href="#compliance" @click="mobileNav = false" class="block py-2 text-slate-700 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400">RTO Compliance</a>
            <a href="{{ route('docs') }}" class="block py-2 text-amber-600 dark:text-amber-400">System Documentation</a>
            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex flex-col gap-2">
                @auth
                <a href="{{ route('dashboard') }}" class="w-full py-2.5 bg-amber-500 text-slate-950 font-bold text-center rounded-lg min-h-[44px] flex items-center justify-center">
                    Go to Dashboard
                </a>
                @else
                <a href="{{ route('login') }}" class="w-full py-2.5 bg-amber-500 text-slate-950 font-bold text-center rounded-lg min-h-[44px] flex items-center justify-center">
                    Launch Interactive Demo Login
                </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1">

        <!-- Hero Section -->
        <section class="relative overflow-hidden pt-12 pb-20 sm:pt-20 sm:pb-28 border-b border-slate-200 dark:border-slate-800/80 bg-gradient-to-b from-slate-100 via-white to-slate-50 dark:from-slate-900 dark:via-slate-950 dark:to-slate-950 transition-colors duration-150">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white dark:bg-slate-800 text-amber-700 dark:text-amber-400 border border-slate-200 dark:border-slate-700 shadow-xs mb-6">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Deterministic Civil Works & Field Operations Platform</span>
                </div>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-900 dark:text-white tracking-tight max-w-4xl mx-auto leading-tight">
                    Civil Infrastructure, Heavy Fleet & <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-600 to-amber-500 dark:from-amber-400 dark:to-amber-500">Field Workforce Control</span>
                </h1>

                <p class="mt-5 text-sm sm:text-base lg:text-lg text-slate-600 dark:text-slate-400 max-w-3xl mx-auto leading-relaxed">
                    Purpose-built for highway contractors, elevated flyover builders, and earthwork civil firms across Maharashtra and India. Unifying cumulative concrete pour tracking, 26-day monthly workforce payroll math (INR), and automated RTO heavy machinery compliance.
                </p>

                <!-- CTA Buttons -->
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3.5">
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-7 py-3.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-sm rounded-xl shadow-lg shadow-amber-500/20 transition min-h-12 flex items-center justify-center gap-2 cursor-pointer">
                        <span>Launch 1-Click Role Demo</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                    <a href="{{ route('docs') }}" class="w-full sm:w-auto px-6 py-3.5 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-800 dark:text-white font-bold text-sm rounded-xl border border-slate-300 dark:border-slate-700 transition min-h-12 flex items-center justify-center gap-2 shadow-xs">
                        <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>System Architecture & Docs</span>
                    </a>
                </div>

                <!-- Trust Points -->
                <div class="mt-12 pt-8 border-t border-slate-200 dark:border-slate-800/80 max-w-4xl mx-auto grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs text-slate-600 dark:text-slate-400">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Deterministic Progress Sync</span>
                    </div>
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>26-Day Civil Wage Math</span>
                    </div>
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>PUC & FC Expiry Scanner</span>
                    </div>
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Excel-Safe CSV with BOM</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Live Portfolio Metrics Ribbon -->
        <section class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 py-8 transition-colors duration-150">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">Active Infrastructure Sites</div>
                        <div class="text-3xl font-black text-slate-900 dark:text-white mt-1">{{ $activeProjectsCount }} Contracts</div>
                        <div class="text-[11px] text-amber-600 dark:text-amber-400 mt-0.5">NH-48, CIDCO, MIHAN, Godavari</div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">Managed Portfolio Budget</div>
                        <div class="text-3xl font-black text-amber-600 dark:text-amber-400 mt-1">INR {{ number_format((float) $totalBudgetInr / 10000000, 2) }} Cr</div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Standardized DECIMAL(14,2)</div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">Active Workforce & Crew</div>
                        <div class="text-3xl font-black text-slate-900 dark:text-white mt-1">{{ $totalWorkforceCount }} Personnel</div>
                        <div class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-0.5">Staff Engineers & Muster-Roll Crew</div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">Heavy Equipment Units</div>
                        <div class="text-3xl font-black text-slate-900 dark:text-white mt-1">{{ $totalMachineryCount }} Units</div>
                        <div class="text-[11px] text-purple-600 dark:text-purple-400 mt-0.5">18 RTO Compliance Trackers</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Core Modules Feature Grid -->
        <section id="features" class="py-16 sm:py-24 border-b border-slate-200 dark:border-slate-800/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center max-w-3xl mx-auto">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Deterministic Operational Scope</span>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight mt-1.5">
                        Built for Tough Field Realities, Not Generic Spreadsheets
                    </h2>
                    <p class="text-slate-600 dark:text-slate-400 text-sm sm:text-base mt-2">
                        Five core engineering operational modules designed from the ground up to prevent data drift, unapproved payroll leaks, and expired vehicle groundings.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Module 1: Projects & Progress Sync -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 flex flex-col justify-between hover:border-amber-500/50 dark:hover:border-slate-700 transition shadow-xs">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-4 border border-amber-500/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">1. Infrastructure Contracts & Site Command</h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-2 leading-relaxed">
                                Single-page command view across Overview, Site Daily Reports, Assigned Crew, and Deployed Machinery. Evaluates cumulative project progress strictly from <code class="text-amber-700 dark:text-amber-300">reviewed</code> supervisor reports without reducing official progress from older records.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs font-semibold text-amber-600 dark:text-amber-400 flex items-center gap-1">
                            Rule B: Deterministic Cumulative Sync
                        </div>
                    </div>

                    <!-- Module 2: Mobile Quick Logger -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 flex flex-col justify-between hover:border-sky-500/50 dark:hover:border-slate-700 transition shadow-xs">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center mb-4 border border-sky-500/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">2. 1-Screen Mobile Pour Logger</h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-2 leading-relaxed">
                                Formatted for field foremen on 320px mobile devices. Pre-selects active project site, today's date, and weather condition so a concrete pour or rock excavation log is submitted in under 15 seconds with cube test samples.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs font-semibold text-sky-600 dark:text-sky-400 flex items-center gap-1">
                            Touch-Optimized (&ge;48px buttons)
                        </div>
                    </div>

                    <!-- Module 3: 26-Day Wage & Overtime Engine -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 flex flex-col justify-between hover:border-emerald-500/50 dark:hover:border-slate-700 transition shadow-xs">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-4 border border-emerald-500/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">3. INR Wage Math & Overtime Approval</h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-2 leading-relaxed">
                                Distinguishes monthly staff (26-working-day divisor) from daily wage workers. Overtime requires explicit supervisor approval before entering wage batches. Locks approved and paid periods as immutable financial snapshots.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                            Rule C: Immutable Locking & Skip Rule
                        </div>
                    </div>

                    <!-- Module 4: Machinery Compliance -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 flex flex-col justify-between hover:border-purple-500/50 dark:hover:border-slate-700 transition shadow-xs">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-4 border border-purple-500/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">4. RTO Compliance & Expiry Alert Scanner</h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-2 leading-relaxed">
                                Tracks PUC, Comprehensive Insurance, and Fitness Certificates. Dynamically categorizes documents into Expired, Critical (&le;7d), Expiring Soon (8-30d), and Valid buckets with idempotent in-app reminders and driver 1-click renewal flagging.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs font-semibold text-purple-600 dark:text-purple-400 flex items-center gap-1">
                            Rule D: Idempotent Daily Reminder Engine
                        </div>
                    </div>

                    <!-- Module 5: Enterprise Exports -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 flex flex-col justify-between hover:border-rose-500/50 dark:hover:border-slate-700 transition shadow-xs">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-4 border border-rose-500/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">5. Excel-Safe CSV & PDF Print Engine</h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-2 leading-relaxed">
                                Enterprise reporting with UTF-8 BOM headers for Microsoft Excel and formula-injection prefixing protection. Printable executive payslips and audit summaries trigger native browser print / Save-as-PDF.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs font-semibold text-rose-600 dark:text-rose-400 flex items-center gap-1">
                            Rule E: CSV Formula Sanitization
                        </div>
                    </div>

                    <!-- Module 6: Full Audit Trail -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 flex flex-col justify-between hover:border-indigo-500/50 dark:hover:border-slate-700 transition shadow-xs">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-4 border border-indigo-500/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">6. Immutable Event & Audit Logging</h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-2 leading-relaxed">
                                Every supervisor progress override, payroll batch calculation, live role switch, and automated compliance reminder is recorded with Asia/Kolkata timestamps and structured audit payloads.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs font-semibold text-indigo-600 dark:text-indigo-400 flex items-center gap-1">
                            Searchable by Module & Action
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Live Projects Portfolio Showcase -->
        <section id="projects" class="py-16 sm:py-24 border-b border-slate-200 dark:border-slate-800/80 bg-slate-100/60 dark:bg-slate-900/50 transition-colors duration-150">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Live Active Sites</span>
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
                            Maharashtra Civil Works Portfolio
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1">Real-time progress verified from site pour logs and concrete batching records</p>
                    </div>

                    <a href="{{ route('login') }}" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 flex items-center gap-1 shrink-0">
                        <span>Access Project Command &rarr;</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($projects as $p)
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-slate-100 dark:bg-slate-800 text-amber-700 dark:text-amber-400 tracking-wider">
                                    {{ $p->code }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white mt-1.5">{{ $p->name }}</h3>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $p->client_name }} &bull; {{ $p->location }}</div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $p->status === 'active' ? 'bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </div>

                        <div class="mt-4">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-slate-500 dark:text-slate-400">Site Completion</span>
                                <span class="font-black text-amber-600 dark:text-amber-400">{{ $p->overall_progress }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $p->overall_progress }}%"></div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs">
                            <div>
                                <span class="text-slate-500">Contract Budget:</span>
                                <span class="font-bold text-slate-900 dark:text-white ml-1">{{ $p->formatted_budget }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">Manager:</span>
                                <span class="text-slate-700 dark:text-slate-300 ml-1 font-medium">{{ $p->manager?->name ?? 'Unassigned' }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Interactive Role Sandbox (6 Roles) -->
        <section id="roles" class="py-16 sm:py-24 border-b border-slate-200 dark:border-slate-800/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
                <div class="text-center max-w-3xl mx-auto">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Interactive Role Sandbox</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
                        Experience BuildTrack as 6 Real-World Personas
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-2">
                        Under <code class="text-amber-700 dark:text-amber-300 font-semibold">DEMO_MODE=true</code>, select any role card below to test backend action authorization and site scoping instantly without credentials.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    <!-- Admin -->
                    <form action="{{ route('demo.login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" value="admin">
                        <button type="submit" class="w-full text-left p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-amber-500/60 transition group cursor-pointer min-h-[120px] flex flex-col justify-between shadow-xs">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 dark:text-white text-base group-hover:text-amber-600 dark:group-hover:text-amber-400">Rajesh Sharma</span>
                                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30">Owner / Admin</span>
                                </div>
                                <div class="text-xs text-slate-600 dark:text-slate-400 mt-1.5 leading-relaxed">
                                    Unscoped executive control: full projects portfolio, manual progress overrides, and company-wide wage disbursements.
                                </div>
                            </div>
                            <div class="text-xs text-amber-600 dark:text-amber-400 font-bold mt-3 group-hover:underline flex items-center gap-1">
                                Launch as Director &rarr;
                            </div>
                        </button>
                    </form>

                    <!-- Project Manager -->
                    <form action="{{ route('demo.login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" value="project_manager">
                        <button type="submit" class="w-full text-left p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-amber-500/60 transition group cursor-pointer min-h-[120px] flex flex-col justify-between shadow-xs">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 dark:text-white text-base group-hover:text-amber-600 dark:group-hover:text-amber-400">Vikram Patel</span>
                                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-sky-50 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-500/30">Project Manager</span>
                                </div>
                                <div class="text-xs text-slate-600 dark:text-slate-400 mt-1.5 leading-relaxed">
                                    Scoped site review queue: verifies daily pour quantities, inspects cube strength, and authorizes site crew overtime.
                                </div>
                            </div>
                            <div class="text-xs text-amber-600 dark:text-amber-400 font-bold mt-3 group-hover:underline flex items-center gap-1">
                                Launch as Project Manager &rarr;
                            </div>
                        </button>
                    </form>

                    <!-- Site Foreman -->
                    <form action="{{ route('demo.login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" value="foreman">
                        <button type="submit" class="w-full text-left p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-amber-500/60 transition group cursor-pointer min-h-[120px] flex flex-col justify-between shadow-xs">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 dark:text-white text-base group-hover:text-amber-600 dark:group-hover:text-amber-400">Suresh Nair</span>
                                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">Site Foreman</span>
                                </div>
                                <div class="text-xs text-slate-600 dark:text-slate-400 mt-1.5 leading-relaxed">
                                    Mobile-first logger: records daily concrete pours, earthwork quantities, and marks daily crew attendance in &lt;15 seconds.
                                </div>
                            </div>
                            <div class="text-xs text-amber-600 dark:text-amber-400 font-bold mt-3 group-hover:underline flex items-center gap-1">
                                Launch as Site Foreman &rarr;
                            </div>
                        </button>
                    </form>

                    <!-- HR & Payroll -->
                    <form action="{{ route('demo.login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" value="hr_payroll">
                        <button type="submit" class="w-full text-left p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-amber-500/60 transition group cursor-pointer min-h-[120px] flex flex-col justify-between shadow-xs">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 dark:text-white text-base group-hover:text-amber-600 dark:group-hover:text-amber-400">Priya Menon</span>
                                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-purple-50 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30">HR & Payroll</span>
                                </div>
                                <div class="text-xs text-slate-600 dark:text-slate-400 mt-1.5 leading-relaxed">
                                    Compensations: calculates monthly 26-day staff wages, daily muster rates, approves overtime, and prints locked INR payslips.
                                </div>
                            </div>
                            <div class="text-xs text-amber-600 dark:text-amber-400 font-bold mt-3 group-hover:underline flex items-center gap-1">
                                Launch as HR Officer &rarr;
                            </div>
                        </button>
                    </form>

                    <!-- Fleet Manager -->
                    <form action="{{ route('demo.login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" value="asset_manager">
                        <button type="submit" class="w-full text-left p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-amber-500/60 transition group cursor-pointer min-h-[120px] flex flex-col justify-between shadow-xs">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 dark:text-white text-base group-hover:text-amber-600 dark:group-hover:text-amber-400">Arun Deshmukh</span>
                                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">Asset Manager</span>
                                </div>
                                <div class="text-xs text-slate-600 dark:text-slate-400 mt-1.5 leading-relaxed">
                                    Equipment registry: monitors heavy machinery deployment, maintenance status, and triggers idempotent RTO expiry scans.
                                </div>
                            </div>
                            <div class="text-xs text-amber-600 dark:text-amber-400 font-bold mt-3 group-hover:underline flex items-center gap-1">
                                Launch as Asset Manager &rarr;
                            </div>
                        </button>
                    </form>

                    <!-- Driver / Operator -->
                    <form action="{{ route('demo.login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" value="driver">
                        <button type="submit" class="w-full text-left p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-amber-500/60 transition group cursor-pointer min-h-[120px] flex flex-col justify-between shadow-xs">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 dark:text-white text-base group-hover:text-amber-600 dark:group-hover:text-amber-400">Manoj Yadav</span>
                                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-teal-50 dark:bg-teal-500/20 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-500/30">Operator / Driver</span>
                                </div>
                                <div class="text-xs text-slate-600 dark:text-slate-400 mt-1.5 leading-relaxed">
                                    Mobile machine cockpit: scoped to assigned JCB 3DX & Excavator to inspect document validity and flag renewals from field.
                                </div>
                            </div>
                            <div class="text-xs text-amber-600 dark:text-amber-400 font-bold mt-3 group-hover:underline flex items-center gap-1">
                                Launch as Machine Operator &rarr;
                            </div>
                        </button>
                    </form>

                </div>
            </div>
        </section>

        <!-- Compliance & Expiry Alert Section -->
        <section id="compliance" class="py-16 sm:py-20 border-b border-slate-200 dark:border-slate-800/80 bg-slate-100/50 dark:bg-slate-900/40 transition-colors duration-150">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gradient-to-r from-slate-900 to-slate-950 rounded-3xl border border-slate-800 p-8 sm:p-12 flex flex-col lg:flex-row items-center justify-between gap-8 text-white">
                    <div class="max-w-2xl space-y-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                            Zero-Grounding Policy
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                            Never Ground Heavy Machinery Due to Expired PUC or Insurance
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                            Under Motor Vehicles Act regulations in Maharashtra, expired Pollution Under Control (PUC) certificates or commercial Fitness Certificates result in heavy fines and impounded tippers. BuildTrack continuously scans vehicle certificates against <code class="text-amber-300">Asia/Kolkata</code> dates and surfaces 30-day early warnings.
                        </p>
                    </div>

                    <div class="shrink-0 flex flex-col sm:flex-row items-center gap-3">
                        <a href="{{ route('docs') }}" class="px-6 py-3.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-xs transition min-h-12 flex items-center justify-center">
                            View Compliance Runbook
                        </a>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800 py-12 text-xs text-slate-500 dark:text-slate-400 transition-colors duration-150">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded bg-amber-500 text-slate-950 font-bold flex items-center justify-center text-xs">
                        BT
                    </div>
                    <span class="font-black text-slate-900 dark:text-white text-sm">BuildTrack India</span>
                    <span class="text-slate-500">&bull; Civil Works & Field Operations Management System (v3.0)</span>
                </div>

                <div class="flex items-center gap-6">
                    <a href="{{ route('home') }}" class="hover:text-amber-600 dark:hover:text-amber-400 transition">Home</a>
                    <a href="{{ route('docs') }}" class="hover:text-amber-600 dark:hover:text-amber-400 transition">Documentation</a>
                    <a href="{{ route('login') }}" class="hover:text-amber-600 dark:hover:text-amber-400 transition">Console Sign In</a>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 dark:border-slate-900 text-center sm:text-left text-slate-500 dark:text-slate-600 text-[11px] flex flex-col sm:flex-row items-center justify-between gap-2">
                <div>
                    Deployed on VPS Adanix &bull; Container: <code class="text-slate-600 dark:text-slate-500">civil-app-1</code> &bull; Live Preview: <code class="text-slate-600 dark:text-slate-500">civil.ramadadawah.my.id</code>
                </div>
                <div>
                    Standardized for Indian Rupee (INR) & Asia/Kolkata (IST). All rights reserved.
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
