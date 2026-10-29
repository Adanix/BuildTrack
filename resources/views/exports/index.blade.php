<x-layouts.app>
    <div class="space-y-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Reports & Executive Export Hub</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Excel-safe CSV streaming and print-styled browser executive reports</p>
        </div>

        <!-- Security & CSV Sanitization Notice -->
        <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-4 text-xs text-indigo-900 flex items-start gap-3">
            <svg class="w-5 h-5 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <strong class="font-bold">Enterprise Export Safety Standards:</strong>
                All CSV downloads include UTF-8 BOM (<code class="bg-indigo-100 px-1 py-0.5 rounded">\xEF\xBB\xBF</code>) for full Microsoft Excel compatibility. Any formula injection prefixes (<code class="bg-indigo-100 px-1 py-0.5 rounded">=, +, -, @</code>) are automatically neutralized with single quotes. Print reports trigger browser print / Save-as-PDF directly.
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- 1. Projects & Progress Report -->
            @if(in_array(auth()->user()->role, ['admin', 'project_manager']))
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Projects Portfolio & Progress</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Executive portfolio status, contract budgets in INR, locations, and cumulative progress percentages.
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-3">
                    <a href="{{ route('exports.projects.csv') }}" class="flex-1 py-2.5 px-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl text-center min-h-[40px] flex items-center justify-center">
                        Download CSV
                    </a>
                    <a href="{{ route('exports.projects.print') }}" target="_blank" class="flex-1 py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl text-center min-h-[40px] flex items-center justify-center">
                        Print / PDF
                    </a>
                </div>
            </div>
            @endif

            <!-- 2. Attendance & Overtime Report -->
            @if(in_array(auth()->user()->role, ['admin', 'project_manager', 'hr_payroll']))
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Workforce Attendance & Overtime</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Muster roll records, regular hours, overtime hours, supervisor approval flags and site locations.
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-3">
                    <a href="{{ route('exports.attendance.csv') }}" class="flex-1 py-2.5 px-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl text-center min-h-[40px] flex items-center justify-center">
                        Download CSV
                    </a>
                    <a href="{{ route('exports.attendance.print') }}" target="_blank" class="flex-1 py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl text-center min-h-[40px] flex items-center justify-center">
                        Print / PDF
                    </a>
                </div>
            </div>
            @endif

            <!-- 3. INR Payroll Statement Report -->
            @if(in_array(auth()->user()->role, ['admin', 'hr_payroll']))
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">INR Monthly Payroll & Wages</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Monthly staff salaries (26-day norm), daily wage earnings, approved OT pay, net wages, and lock statuses.
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-3">
                    <a href="{{ route('exports.payroll.csv', ['period_month' => $currentMonth]) }}" class="flex-1 py-2.5 px-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl text-center min-h-[40px] flex items-center justify-center">
                        Download CSV ({{ $currentMonth }})
                    </a>
                    <a href="{{ route('exports.payroll.print', ['period_month' => $currentMonth]) }}" target="_blank" class="flex-1 py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl text-center min-h-[40px] flex items-center justify-center">
                        Print / PDF
                    </a>
                </div>
            </div>
            @endif

            <!-- 4. Machinery & Compliance Report -->
            @if(in_array(auth()->user()->role, ['admin', 'asset_manager']))
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Fleet Register & Compliance Audit</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Heavy fleet directory, operational status, PUC/Insurance/Fitness expiry dates, and driver renewal flags.
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-3">
                    <a href="{{ route('exports.machinery.csv') }}" class="flex-1 py-2.5 px-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl text-center min-h-[40px] flex items-center justify-center">
                        Download CSV
                    </a>
                    <a href="{{ route('exports.machinery.print') }}" target="_blank" class="flex-1 py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl text-center min-h-[40px] flex items-center justify-center">
                        Print / PDF
                    </a>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-layouts.app>

