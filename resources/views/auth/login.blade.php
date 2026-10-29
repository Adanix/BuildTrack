<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BuildTrack India</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full antialiased text-slate-100 flex flex-col justify-between py-10 px-4 sm:px-6 lg:px-8">

    <div class="max-w-4xl w-full mx-auto space-y-8">
        <!-- Brand Header -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-500 text-slate-950 font-black text-2xl shadow-lg mb-3">
                BT
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight sm:text-4xl">BuildTrack India</h1>
            <p class="text-slate-400 mt-2 text-sm sm:text-base">Civil Works & Field Operations Management System (v3.0)</p>
            <div class="inline-flex items-center gap-2 mt-2 px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-amber-400 border border-slate-700">
                <span>Standardized for INR & IST (Asia/Kolkata)</span>
            </div>
        </div>

        @if(session('error'))
        <div class="rounded-lg bg-rose-950/80 border border-rose-700 p-4 text-sm text-rose-200">
            {{ session('error') }}
        </div>
        @endif

        @if(session('success'))
        <div class="rounded-lg bg-emerald-950/80 border border-emerald-700 p-4 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
        @endif

        @if(config('app.demo_mode'))
        <!-- 1-Click Role Login Section -->
        <div class="bg-slate-800/90 rounded-2xl border border-slate-700 p-6 sm:p-8 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-700/80">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                        Interactive Demo: 1-Click Role Selection
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Select any operational role below to instantly log in without entering a password.</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30 shrink-0">
                    DEMO_MODE=true
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 mt-6">
                <!-- Role 1: Admin -->
                <form action="{{ route('demo.login') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="admin">
                    <button type="submit" class="w-full text-left p-4 rounded-xl border border-slate-700 bg-slate-800/50 hover:bg-slate-700/80 hover:border-amber-500/60 transition group cursor-pointer min-h-[96px] flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-white group-hover:text-amber-400 text-sm">Rajesh Sharma</span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">Owner / Admin</span>
                            </div>
                            <div class="text-xs text-slate-400 mt-1">Full executive command across all sites, payroll, and fleet.</div>
                        </div>
                        <div class="text-[11px] text-amber-400 font-semibold mt-2 group-hover:underline flex items-center gap-1">
                            Enter as Director &rarr;
                        </div>
                    </button>
                </form>

                <!-- Role 2: Project Manager -->
                <form action="{{ route('demo.login') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="project_manager">
                    <button type="submit" class="w-full text-left p-4 rounded-xl border border-slate-700 bg-slate-800/50 hover:bg-slate-700/80 hover:border-amber-500/60 transition group cursor-pointer min-h-[96px] flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-white group-hover:text-amber-400 text-sm">Vikram Patel</span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-sky-500/20 text-sky-300 border border-sky-500/30">Project Manager</span>
                            </div>
                            <div class="text-xs text-slate-400 mt-1">Scoped site review, progress verification & OT approval.</div>
                        </div>
                        <div class="text-[11px] text-amber-400 font-semibold mt-2 group-hover:underline flex items-center gap-1">
                            Enter as PM &rarr;
                        </div>
                    </button>
                </form>

                <!-- Role 3: Site Foreman -->
                <form action="{{ route('demo.login') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="foreman">
                    <button type="submit" class="w-full text-left p-4 rounded-xl border border-slate-700 bg-slate-800/50 hover:bg-slate-700/80 hover:border-amber-500/60 transition group cursor-pointer min-h-[96px] flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-white group-hover:text-amber-400 text-sm">Suresh Nair</span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Site Foreman</span>
                            </div>
                            <div class="text-xs text-slate-400 mt-1">Mobile quick logger for concrete pours & daily crew sheet.</div>
                        </div>
                        <div class="text-[11px] text-amber-400 font-semibold mt-2 group-hover:underline flex items-center gap-1">
                            Enter as Foreman &rarr;
                        </div>
                    </button>
                </form>

                <!-- Role 4: HR & Payroll -->
                <form action="{{ route('demo.login') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="hr_payroll">
                    <button type="submit" class="w-full text-left p-4 rounded-xl border border-slate-700 bg-slate-800/50 hover:bg-slate-700/80 hover:border-amber-500/60 transition group cursor-pointer min-h-[96px] flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-white group-hover:text-amber-400 text-sm">Priya Menon</span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-purple-500/20 text-purple-300 border border-purple-500/30">HR & Payroll</span>
                            </div>
                            <div class="text-xs text-slate-400 mt-1">Wage calculations, OT batch approval & INR payslips.</div>
                        </div>
                        <div class="text-[11px] text-amber-400 font-semibold mt-2 group-hover:underline flex items-center gap-1">
                            Enter as HR Officer &rarr;
                        </div>
                    </button>
                </form>

                <!-- Role 5: Fleet & Asset Manager -->
                <form action="{{ route('demo.login') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="asset_manager">
                    <button type="submit" class="w-full text-left p-4 rounded-xl border border-slate-700 bg-slate-800/50 hover:bg-slate-700/80 hover:border-amber-500/60 transition group cursor-pointer min-h-[96px] flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-white group-hover:text-amber-400 text-sm">Arun Deshmukh</span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30">Asset Manager</span>
                            </div>
                            <div class="text-xs text-slate-400 mt-1">Heavy equipment register & PUC/FC compliance scanner.</div>
                        </div>
                        <div class="text-[11px] text-amber-400 font-semibold mt-2 group-hover:underline flex items-center gap-1">
                            Enter as Asset Manager &rarr;
                        </div>
                    </button>
                </form>

                <!-- Role 6: Machine Operator / Driver -->
                <form action="{{ route('demo.login') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="driver">
                    <button type="submit" class="w-full text-left p-4 rounded-xl border border-slate-700 bg-slate-800/50 hover:bg-slate-700/80 hover:border-amber-500/60 transition group cursor-pointer min-h-[96px] flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-white group-hover:text-amber-400 text-sm">Manoj Yadav</span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-teal-500/20 text-teal-300 border border-teal-500/30">Operator / Driver</span>
                            </div>
                            <div class="text-xs text-slate-400 mt-1">Mobile machine card view & 1-click renewal flagging.</div>
                        </div>
                        <div class="text-[11px] text-amber-400 font-semibold mt-2 group-hover:underline flex items-center gap-1">
                            Enter as Operator &rarr;
                        </div>
                    </button>
                </form>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-700/60 text-xs text-slate-400 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                <div>
                    <span class="font-semibold text-slate-300">Notice:</span> Passwordless role switching is enabled strictly for synthetic evaluation in <code class="text-amber-300">DEMO_MODE=true</code>. Set <code class="text-amber-300">DEMO_MODE=false</code> in live production.
                </div>
                <a href="{{ route('docs') }}" class="text-amber-400 hover:text-amber-300 underline font-semibold shrink-0">
                    View Full System Docs &rarr;
                </a>
            </div>
        </div>
        @endif

        <!-- Standard Credentials Login Form -->
        <div class="bg-slate-800/50 rounded-2xl border border-slate-700/80 p-6 sm:p-8">
            <h3 class="text-base font-semibold text-white mb-4">Standard Credentials Login</h3>
            <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-300">Work Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', 'admin@buildtrack.in') }}" required class="mt-1 block w-full rounded-lg bg-slate-900 border border-slate-700 text-white px-3 py-2.5 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>

                <div>
                    <label for="password" class="block text-xs font-medium text-slate-300">Password</label>
                    <input type="password" id="password" name="password" value="password" required class="mt-1 block w-full rounded-lg bg-slate-900 border border-slate-700 text-white px-3 py-2.5 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-400">
                        <input type="checkbox" name="remember" class="rounded bg-slate-900 border-slate-700 text-amber-500 focus:ring-0">
                        <span>Remember session</span>
                    </label>
                    <span class="text-slate-400">Default demo password: <code class="text-slate-200">password</code></span>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm cursor-pointer min-h-[44px]">
                    Sign In to Console
                </button>
            </form>
        </div>

        <footer class="text-center text-xs text-slate-500">
            BuildTrack India &bull; Civil Works & Field Operations &bull; Standardized for INR / IST
        </footer>
    </div>

</body>
</html>
