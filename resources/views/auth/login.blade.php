<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BuildTrack India</title>
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
<body class="h-full antialiased text-slate-800 dark:text-slate-100 bg-slate-100 dark:bg-slate-950 flex flex-col justify-between py-10 px-4 sm:px-6 lg:px-8 transition-colors duration-150">

    <!-- Top Floating Header Bar with Home Link & Theme Toggle -->
    <div class="max-w-4xl w-full mx-auto flex items-center justify-between pb-4">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Back to Home</span>
        </a>

        <div class="flex items-center gap-2">
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
                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 transition cursor-pointer shadow-2xs"
                :title="dark ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
                <template x-if="dark">
                    <span class="flex items-center gap-1 text-amber-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span>Light</span>
                    </span>
                </template>
                <template x-if="!dark">
                    <span class="flex items-center gap-1 text-slate-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        <span>Dark</span>
                    </span>
                </template>
            </button>

            <a href="{{ route('docs') }}" class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 px-2 py-1">
                Docs
            </a>
        </div>
    </div>

    <div class="max-w-4xl w-full mx-auto space-y-6">
        <!-- Brand Header -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-500 text-slate-950 font-black text-2xl shadow-md mb-3">
                BT
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight sm:text-4xl">BuildTrack India</h1>
            <p class="text-slate-600 dark:text-slate-400 mt-1.5 text-sm sm:text-base">Civil Works & Field Operations Management System (v3.0)</p>
            <div class="inline-flex items-center gap-2 mt-2 px-3 py-1 rounded-full text-xs font-semibold bg-white dark:bg-slate-800 text-amber-700 dark:text-amber-400 border border-slate-200 dark:border-slate-700 shadow-2xs">
                <span>Standardized for INR & IST (Asia/Kolkata)</span>
            </div>
        </div>

        @if(session('error'))
        <div class="rounded-xl bg-rose-50 dark:bg-rose-950/80 border border-rose-200 dark:border-rose-700 p-4 text-sm text-rose-800 dark:text-rose-200 shadow-2xs">
            {{ session('error') }}
        </div>
        @endif

        @if(session('success'))
        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-200 dark:border-emerald-700 p-4 text-sm text-emerald-800 dark:text-emerald-200 shadow-2xs">
            {{ session('success') }}
        </div>
        @endif

        @if(config('app.demo_mode'))
        <!-- 1-Click Role Login Section -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        Interactive Demo: 1-Click Role Selection
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Select any operational role below to instantly log in without entering credentials.</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30 shrink-0">
                    DEMO_MODE=true
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 mt-6">
                <!-- Role 1: Admin -->
                <form action="{{ route('demo.login') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="admin">
                    <button type="submit" class="w-full text-left p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/50 hover:bg-amber-50/40 dark:hover:bg-slate-800 hover:border-amber-500/60 transition group cursor-pointer min-h-[96px] flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 text-sm">Rajesh Sharma</span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30">Owner / Admin</span>
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-400 mt-1">Full executive command across all sites, payroll, and fleet.</div>
                        </div>
                        <div class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold mt-2 group-hover:underline flex items-center gap-1">
                            Enter as Director &rarr;
                        </div>
                    </button>
                </form>

                <!-- Role 2: Project Manager -->
                <form action="{{ route('demo.login') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="project_manager">
                    <button type="submit" class="w-full text-left p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/50 hover:bg-sky-50/40 dark:hover:bg-slate-800 hover:border-sky-500/60 transition group cursor-pointer min-h-[96px] flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 dark:text-white group-hover:text-sky-600 dark:group-hover:text-sky-400 text-sm">Vikram Patel</span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-sky-50 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-500/30">Project Manager</span>
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-400 mt-1">Scoped site review, progress verification & OT approval.</div>
                        </div>
                        <div class="text-[11px] text-sky-600 dark:text-sky-400 font-semibold mt-2 group-hover:underline flex items-center gap-1">
                            Enter as PM &rarr;
                        </div>
                    </button>
                </form>

                <!-- Role 3: Site Foreman -->
                <form action="{{ route('demo.login') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="foreman">
                    <button type="submit" class="w-full text-left p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/50 hover:bg-emerald-50/40 dark:hover:bg-slate-800 hover:border-emerald-500/60 transition group cursor-pointer min-h-[96px] flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 text-sm">Suresh Nair</span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">Site Foreman</span>
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-400 mt-1">Mobile quick logger for concrete pours & daily crew sheet.</div>
                        </div>
                        <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-2 group-hover:underline flex items-center gap-1">
                            Enter as Foreman &rarr;
                        </div>
                    </button>
                </form>

                <!-- Role 4: HR & Payroll -->
                <form action="{{ route('demo.login') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="hr_payroll">
                    <button type="submit" class="w-full text-left p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/50 hover:bg-purple-50/40 dark:hover:bg-slate-800 hover:border-purple-500/60 transition group cursor-pointer min-h-[96px] flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 text-sm">Priya Menon</span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-purple-50 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30">HR & Payroll</span>
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-400 mt-1">Wage calculations, OT batch approval & INR payslips.</div>
                        </div>
                        <div class="text-[11px] text-purple-600 dark:text-purple-400 font-semibold mt-2 group-hover:underline flex items-center gap-1">
                            Enter as HR Officer &rarr;
                        </div>
                    </button>
                </form>

                <!-- Role 5: Fleet & Asset Manager -->
                <form action="{{ route('demo.login') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="asset_manager">
                    <button type="submit" class="w-full text-left p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/50 hover:bg-amber-50/40 dark:hover:bg-slate-800 hover:border-amber-500/60 transition group cursor-pointer min-h-[96px] flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 text-sm">Arun Deshmukh</span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">Asset Manager</span>
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-400 mt-1">Heavy equipment register & PUC/FC compliance scanner.</div>
                        </div>
                        <div class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold mt-2 group-hover:underline flex items-center gap-1">
                            Enter as Asset Manager &rarr;
                        </div>
                    </button>
                </form>

                <!-- Role 6: Machine Operator / Driver -->
                <form action="{{ route('demo.login') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="driver">
                    <button type="submit" class="w-full text-left p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/50 hover:bg-teal-50/40 dark:hover:bg-slate-800 hover:border-teal-500/60 transition group cursor-pointer min-h-[96px] flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400 text-sm">Manoj Yadav</span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-teal-50 dark:bg-teal-500/20 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-500/30">Operator / Driver</span>
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-400 mt-1">Mobile machine card view & 1-click renewal flagging.</div>
                        </div>
                        <div class="text-[11px] text-teal-600 dark:text-teal-400 font-semibold mt-2 group-hover:underline flex items-center gap-1">
                            Enter as Operator &rarr;
                        </div>
                    </button>
                </form>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                <div>
                    <span class="font-semibold text-slate-700 dark:text-slate-300">Notice:</span> Passwordless role switching is enabled strictly for synthetic evaluation in <code class="text-amber-700 dark:text-amber-300">DEMO_MODE=true</code>. Set <code class="text-amber-700 dark:text-amber-300">DEMO_MODE=false</code> in live production.
                </div>
                <a href="{{ route('docs') }}" class="text-amber-600 dark:text-amber-400 hover:underline font-semibold shrink-0">
                    View Full System Docs &rarr;
                </a>
            </div>
        </div>
        @endif

        <!-- Standard Credentials Login Form -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-4">Standard Credentials Login</h3>
            <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-700 dark:text-slate-300">Work Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', 'admin@buildtrack.in') }}" required class="mt-1 block w-full rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white px-3 py-2.5 text-sm focus:border-amber-500 focus:bg-white dark:focus:bg-slate-800 focus:ring-1 focus:ring-amber-500">
                </div>

                <div>
                    <label for="password" class="block text-xs font-medium text-slate-700 dark:text-slate-300">Password</label>
                    <input type="password" id="password" name="password" value="password" required class="mt-1 block w-full rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white px-3 py-2.5 text-sm focus:border-amber-500 focus:bg-white dark:focus:bg-slate-800 focus:ring-1 focus:ring-amber-500">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600 dark:text-slate-400">
                        <input type="checkbox" name="remember" class="rounded bg-slate-100 dark:bg-slate-800 border-slate-300 dark:border-slate-700 text-amber-500 focus:ring-0">
                        <span>Remember session</span>
                    </label>
                    <span class="text-slate-500 dark:text-slate-400">Default demo password: <code class="text-slate-700 dark:text-slate-200">password</code></span>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm cursor-pointer min-h-[44px] transition shadow-xs">
                    Sign In to Console
                </button>
            </form>
        </div>

        <footer class="text-center text-xs text-slate-500 dark:text-slate-500">
            BuildTrack India &bull; Civil Works & Field Operations &bull; Standardized for INR / IST
        </footer>
    </div>

</body>
</html>
