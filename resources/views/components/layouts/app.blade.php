<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'BuildTrack India' }} - Civil Works & Field Operations</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="/js/alpine.min.js"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
        }
    </style>
</head>
<body class="h-full text-slate-800 antialiased flex flex-col font-sans" x-data="{ mobileMenuOpen: false }">

    @if(config('app.demo_mode'))
    <!-- Persistent Demo Role Banner -->
    <header class="no-print bg-slate-900 text-white border-b border-amber-500/40 text-xs py-2 px-4 shadow-sm z-40 sticky top-0">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded font-semibold bg-amber-500 text-slate-950 uppercase tracking-wider text-[10px]">
                    DEMO MODE
                </span>
                <span class="text-slate-300 hidden sm:inline">Active User:</span>
                <span class="font-medium text-white">{{ auth()->user()->name }}</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] bg-slate-800 text-amber-300 border border-slate-700">
                    {{ auth()->user()->role_display_name }}
                </span>
            </div>

            <div class="flex items-center gap-3">
                <form action="{{ route('demo.switch-role') }}" method="POST" class="flex items-center gap-1.5 m-0">
                    @csrf
                    <label for="demo_role_select" class="text-slate-300 hidden md:inline text-[11px]">Switch Role:</label>
                    <select id="demo_role_select" name="role" onchange="this.form.submit()" class="bg-slate-800 text-slate-100 text-xs border border-slate-700 rounded px-2.5 py-1 focus:ring-1 focus:ring-amber-500 focus:outline-none cursor-pointer">
                        <option value="admin" {{ auth()->user()->role === 'admin' ? 'selected' : '' }}>Owner / Director (Admin)</option>
                        <option value="project_manager" {{ auth()->user()->role === 'project_manager' ? 'selected' : '' }}>Project Manager</option>
                        <option value="foreman" {{ auth()->user()->role === 'foreman' ? 'selected' : '' }}>Site Foreman</option>
                        <option value="hr_payroll" {{ auth()->user()->role === 'hr_payroll' ? 'selected' : '' }}>HR & Payroll Officer</option>
                        <option value="asset_manager" {{ auth()->user()->role === 'asset_manager' ? 'selected' : '' }}>Fleet & Asset Manager</option>
                        <option value="driver" {{ auth()->user()->role === 'driver' ? 'selected' : '' }}>Machine Operator / Driver</option>
                    </select>
                </form>

                <a href="{{ route('docs') }}" class="inline-flex items-center gap-1 text-amber-400 hover:text-amber-300 font-medium underline-offset-4 hover:underline">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Docs & Guide</span>
                </a>
            </div>
        </div>
    </header>
    @endif

    <div class="flex-1 flex overflow-hidden">
        <!-- Desktop Sidebar -->
        <aside class="no-print hidden lg:flex lg:flex-col w-64 bg-slate-900 border-r border-slate-800 text-slate-300 shrink-0">
            <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-500 text-slate-950 flex items-center justify-center font-black text-sm">
                        BT
                    </div>
                    <div>
                        <div class="font-bold text-white text-base tracking-tight leading-none">BuildTrack</div>
                        <div class="text-[10px] text-amber-400 tracking-wider uppercase font-semibold">India Operations</div>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-sm font-medium">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-amber-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('projects.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('projects.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span>Projects & Sites</span>
                </a>

                <a href="{{ route('daily-reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('daily-reports.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <span>Daily Site Reports</span>
                </a>

                @if(!auth()->user()->isDriver())
                <a href="{{ route('employees.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('employees.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>Workforce Master</span>
                </a>

                <a href="{{ route('attendance.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('attendance.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Attendance & OT</span>
                </a>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->isHrPayroll())
                <a href="{{ route('payroll.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('payroll.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Payroll & Payslips (INR)</span>
                </a>
                @endif

                <a href="{{ route('machineries.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('machineries.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>Machinery & Fleet</span>
                </a>

                <a href="{{ route('compliance.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('compliance.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>Compliance & Expiries</span>
                </a>

                @if(!auth()->user()->isForeman() && !auth()->user()->isDriver())
                <a href="{{ route('exports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('exports.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Exports & Reports</span>
                </a>

                <a href="{{ route('activity-logs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('activity-logs.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Activity & Audit Logs</span>
                </a>
                @endif

                <div class="pt-4 mt-4 border-t border-slate-800">
                    <a href="{{ route('docs') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:text-amber-400 hover:bg-slate-800">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span>System Documentation</span>
                    </a>
                </div>
            </nav>

            <!-- Bottom User Profile Card -->
            <div class="p-3 border-t border-slate-800 bg-slate-950/60">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 pr-2">
                        <div class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-400 rounded-lg hover:bg-slate-800 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <!-- Mobile Top Bar -->
            <div class="no-print lg:hidden bg-slate-900 text-white px-4 py-3 flex items-center justify-between border-b border-slate-800">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded bg-amber-500 text-slate-950 font-bold flex items-center justify-center text-xs">
                        BT
                    </div>
                    <span class="font-bold tracking-tight text-white">BuildTrack India</span>
                </a>

                <div class="flex items-center gap-2">
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-slate-300 hover:text-white rounded-lg focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Drawer Menu -->
            <div x-show="mobileMenuOpen" x-cloak class="no-print lg:hidden fixed inset-0 z-50 flex" @keydown.escape.window="mobileMenuOpen = false">
                <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs" @click="mobileMenuOpen = false"></div>
                <div class="relative flex-1 flex flex-col max-w-xs w-full bg-slate-900 text-slate-200 shadow-xl">
                    <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                        <div class="font-bold text-white text-base">Menu Navigation</div>
                        <button type="button" @click="mobileMenuOpen = false" class="p-2 text-slate-400 hover:text-white min-h-[44px] min-w-[44px] flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 space-y-1.5 text-base">
                        <a href="{{ route('dashboard') }}" class="block px-3 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800' }}">Dashboard</a>
                        <a href="{{ route('projects.index') }}" class="block px-3 py-3 rounded-lg {{ request()->routeIs('projects.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800' }}">Projects & Sites</a>
                        <a href="{{ route('daily-reports.index') }}" class="block px-3 py-3 rounded-lg {{ request()->routeIs('daily-reports.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800' }}">Daily Site Reports</a>
                        @if(!auth()->user()->isDriver())
                        <a href="{{ route('employees.index') }}" class="block px-3 py-3 rounded-lg {{ request()->routeIs('employees.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800' }}">Workforce Master</a>
                        <a href="{{ route('attendance.index') }}" class="block px-3 py-3 rounded-lg {{ request()->routeIs('attendance.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800' }}">Attendance & OT</a>
                        @endif
                        @if(auth()->user()->isAdmin() || auth()->user()->isHrPayroll())
                        <a href="{{ route('payroll.index') }}" class="block px-3 py-3 rounded-lg {{ request()->routeIs('payroll.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800' }}">Payroll & Payslips (INR)</a>
                        @endif
                        <a href="{{ route('machineries.index') }}" class="block px-3 py-3 rounded-lg {{ request()->routeIs('machineries.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800' }}">Machinery & Fleet</a>
                        <a href="{{ route('compliance.index') }}" class="block px-3 py-3 rounded-lg {{ request()->routeIs('compliance.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800' }}">Compliance & Expiries</a>
                        @if(!auth()->user()->isForeman() && !auth()->user()->isDriver())
                        <a href="{{ route('exports.index') }}" class="block px-3 py-3 rounded-lg {{ request()->routeIs('exports.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800' }}">Exports Hub</a>
                        <a href="{{ route('activity-logs.index') }}" class="block px-3 py-3 rounded-lg {{ request()->routeIs('activity-logs.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800' }}">Activity Logs</a>
                        @endif
                        <a href="{{ route('docs') }}" class="block px-3 py-3 rounded-lg text-amber-400 hover:bg-slate-800">System Documentation</a>
                    </div>

                    <div class="p-4 border-t border-slate-800 bg-slate-950">
                        <div class="text-sm font-medium text-white mb-2">{{ auth()->user()->name }} ({{ auth()->user()->role_display_name }})</div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-2.5 px-4 rounded bg-rose-600 hover:bg-rose-700 text-white font-medium text-center text-sm">Logout</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Page Header Alerts (Flash Messages) -->
            <div class="no-print max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                @if(session('success'))
                <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-300 p-4 text-sm text-emerald-800 flex items-start justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-4 rounded-lg bg-rose-50 border border-rose-300 p-4 text-sm text-rose-800 flex items-start justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
                @endif

                @if($errors->any())
                <div class="mb-4 rounded-lg bg-amber-50 border border-amber-300 p-4 text-sm text-amber-900 shadow-xs">
                    <div class="font-semibold mb-1">Please correct the following errors:</div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>

            <!-- Page Main Content Slot -->
            <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
