<!DOCTYPE html>
<html lang="en" x-data="{
    theme: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
    toggleTheme() {
        this.theme = this.theme === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', this.theme);
        if (this.theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $payroll->employee->full_name }} ({{ $payroll->period_month }})</title>
    <script>
        (function() {
            try {
                var stored = localStorage.getItem('theme');
                if (stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; color: black !important; }
            .print-border { border-color: #cbd5e1 !important; box-shadow: none !important; }
            .print-bg { background-color: white !important; color: black !important; }
        }
    </style>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 p-4 sm:p-8 font-sans transition-colors duration-200">

    <!-- Top Action Bar -->
    <div class="no-print max-w-3xl mx-auto mb-6 flex items-center justify-between">
        <a href="{{ route('payroll.index', ['period_month' => $payroll->period_month]) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
            &larr; Back to Payroll Sheet
        </a>
        <div class="flex items-center gap-3">
            <button @click="toggleTheme()" type="button" class="p-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-amber-400 hover:border-amber-500 cursor-pointer transition-colors" title="Toggle Theme">
                <template x-if="theme === 'dark'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </template>
                <template x-if="theme !== 'dark'">
                    <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </template>
            </button>
            <button onclick="window.print()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-amber-500 dark:hover:bg-amber-400 text-white dark:text-slate-950 font-bold text-xs rounded-xl shadow-xs flex items-center gap-2 cursor-pointer transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Print / Save as PDF</span>
            </button>
        </div>
    </div>

    <!-- Payslip Paper Document Container -->
    <div class="max-w-3xl mx-auto bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-md p-8 sm:p-12 print-border print-bg">

        <!-- Company Letterhead Header -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between border-b-2 border-slate-900 dark:border-slate-700 pb-6 gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-amber-500 text-slate-950 font-black flex items-center justify-center text-sm shadow-xs">
                        BT
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-slate-900 dark:text-slate-100 uppercase tracking-tight">BuildTrack India Private Limited</h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Civil Works, Highways & Heavy Infrastructure Contractors</p>
                    </div>
                </div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                    Corporate Office: MIDC Industrial Area, Pune, Maharashtra 411019<br>
                    Operations Division &bull; Regional Project Sites: Pune / Navi Mumbai / Nagpur / Nashik
                </div>
            </div>

            <div class="text-left sm:text-right shrink-0">
                <span class="inline-block px-3 py-1 rounded bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-amber-400 text-xs font-black uppercase tracking-wider">
                    Salary Slip
                </span>
                <div class="text-xs text-slate-600 dark:text-slate-300 font-semibold mt-1.5">Period: {{ $payroll->period_month }}</div>
                <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Disbursement: {{ ucfirst($payroll->status) }}</div>
            </div>
        </div>

        <!-- Employee Info Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-6 border-b border-slate-200 dark:border-slate-800 text-xs">
            <div>
                <div class="text-slate-500 dark:text-slate-400">Employee Name</div>
                <div class="font-bold text-slate-900 dark:text-slate-100 text-sm mt-0.5">{{ $payroll->employee->full_name }}</div>
            </div>
            <div>
                <div class="text-slate-500 dark:text-slate-400">Employee Code</div>
                <div class="font-bold text-slate-900 dark:text-slate-100 mt-0.5">{{ $payroll->employee->employee_code }}</div>
            </div>
            <div>
                <div class="text-slate-500 dark:text-slate-400">Designation / Trade</div>
                <div class="font-bold text-slate-900 dark:text-slate-100 mt-0.5">{{ $payroll->employee->designation }}</div>
            </div>
            <div>
                <div class="text-slate-500 dark:text-slate-400">Wage Structure</div>
                <div class="font-bold text-slate-900 dark:text-slate-100 mt-0.5 capitalize">{{ $payroll->employee->wage_type }} (Civil Norm)</div>
            </div>

            <div>
                <div class="text-slate-500 dark:text-slate-400">Days Paid (Present/Half/Leave)</div>
                <div class="font-black text-slate-900 dark:text-slate-100 text-sm mt-0.5">{{ $payroll->days_present }} days</div>
            </div>
            <div>
                <div class="text-slate-500 dark:text-slate-400">Approved Overtime</div>
                <div class="font-black text-purple-700 dark:text-purple-400 text-sm mt-0.5">{{ $payroll->total_overtime_hours }} hrs</div>
            </div>
            <div>
                <div class="text-slate-500 dark:text-slate-400">Assigned Project Site</div>
                <div class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5 truncate">{{ $payroll->employee->primaryProject?->code ?? 'General Site Pool' }}</div>
            </div>
            <div>
                <div class="text-slate-500 dark:text-slate-400">Phone Contact</div>
                <div class="font-medium text-slate-700 dark:text-slate-300 mt-0.5">{{ $payroll->employee->phone }}</div>
            </div>
        </div>

        <!-- Earnings & Deductions Table -->
        <div class="py-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-3">Earnings & Deductions Statement (INR)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                <!-- Earnings Column -->
                <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
                    <div class="bg-slate-50 dark:bg-slate-800/80 px-4 py-2 border-b border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-800 dark:text-slate-200">
                        Earnings Breakdown
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800/60 text-xs p-4 space-y-2">
                        <div class="flex items-center justify-between pb-1">
                            <span class="text-slate-600 dark:text-slate-400">Base Earned (Regular)</span>
                            <span class="font-semibold text-slate-900 dark:text-slate-100">INR {{ number_format((float) $payroll->base_earned_inr, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1">
                            <span class="text-slate-600 dark:text-slate-400">Approved Site Overtime Pay</span>
                            <span class="font-semibold text-slate-900 dark:text-slate-100">INR {{ number_format((float) $payroll->overtime_pay_inr, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1">
                            <span class="text-slate-600 dark:text-slate-400">Special Field Allowances</span>
                            <span class="font-semibold text-slate-900 dark:text-slate-100">INR {{ number_format((float) $payroll->allowances_inr, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t-2 border-slate-200 dark:border-slate-800 font-bold">
                            <span class="text-slate-800 dark:text-slate-200">Total Gross Earnings</span>
                            <span class="text-slate-900 dark:text-slate-100">INR {{ number_format((float) ($payroll->base_earned_inr + $payroll->overtime_pay_inr + $payroll->allowances_inr), 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Deductions Column -->
                <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
                    <div class="bg-slate-50 dark:bg-slate-800/80 px-4 py-2 border-b border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-800 dark:text-slate-200">
                        Authorized Deductions
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800/60 text-xs p-4 space-y-2">
                        <div class="flex items-center justify-between pb-1">
                            <span class="text-slate-600 dark:text-slate-400">Safety Gear / Advance Deductions</span>
                            <span class="font-semibold text-rose-700 dark:text-rose-400">INR {{ number_format((float) $payroll->deductions_inr, 2) }}</span>
                        </div>
                        @if($payroll->deduction_notes)
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 italic py-1">
                            Note: {{ $payroll->deduction_notes }}
                        </div>
                        @endif
                        <div class="flex items-center justify-between pt-2 border-t-2 border-slate-200 dark:border-slate-800 font-bold">
                            <span class="text-slate-800 dark:text-slate-200">Total Deductions</span>
                            <span class="text-rose-700 dark:text-rose-400">INR {{ number_format((float) $payroll->deductions_inr, 2) }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Net Payable Salary Banner -->
        <div class="bg-slate-900 dark:bg-slate-800 text-white rounded-xl p-5 flex items-center justify-between mt-2">
            <div>
                <div class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Net Salary Payable (INR)</div>
                <div class="text-xs text-slate-300 mt-0.5">Transferred / Disbursed via Bank Transfer</div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-amber-400">
                INR {{ number_format((float) $payroll->net_salary_inr, 2) }}
            </div>
        </div>

        <!-- Signatures & Disclaimer Footer -->
        <div class="pt-12 mt-8 border-t border-slate-200 dark:border-slate-800 grid grid-cols-2 gap-8 text-xs text-center text-slate-600 dark:text-slate-400">
            <div>
                <div class="border-t border-slate-400 dark:border-slate-600 w-40 mx-auto pt-1">Employee Signature</div>
                <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Acknowledged & Received</div>
            </div>
            <div>
                <div class="border-t border-slate-400 dark:border-slate-600 w-40 mx-auto pt-1">Authorized Signatory</div>
                <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">For BuildTrack India Pvt Ltd</div>
            </div>
        </div>

        <div class="mt-8 text-center text-[10px] text-slate-400 dark:text-slate-500 border-t border-slate-100 dark:border-slate-800 pt-3">
            This payslip is a system-generated operational wage statement from BuildTrack India v3.0 &bull; Generated on {{ now('Asia/Kolkata')->format('d M Y, H:i') }} IST
        </div>
    </div>

</body>
</html>
