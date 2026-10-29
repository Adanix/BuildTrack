<x-layouts.app>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">INR Payroll & Overtime Engine</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Civil works monthly wage calculations, allowances, deductions & locked payslips</p>
            </div>

            <!-- Generate Batch Form -->
            <form action="{{ route('payroll.generate') }}" method="POST" class="flex items-center gap-2 m-0">
                @csrf
                <input type="hidden" name="period_month" value="{{ $periodMonth }}">
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-xs min-h-[44px] cursor-pointer transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span>Recalculate Month ({{ $periodMonth }})</span>
                </button>
            </form>
        </div>

        <!-- Period Selector & Operational Disclaimer -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors">
            <form method="GET" action="{{ route('payroll.index') }}" class="flex items-center gap-3">
                <label for="period_select" class="text-xs font-bold text-slate-700 dark:text-slate-300 whitespace-nowrap">Payroll Period (Month):</label>
                <select id="period_select" name="period_month" onchange="this.form.submit()" class="text-xs font-bold rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:border-amber-500 focus:outline-none">
                    @foreach($periods as $p)
                    <option value="{{ $p }}" {{ $periodMonth === $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </form>

            <div class="text-[11px] text-slate-500 dark:text-slate-400 max-w-xl">
                <span class="font-bold text-slate-700 dark:text-slate-300">Operational Scope Note:</span> BuildTrack India calculates civil project operational wages, 26-day monthly staff divisor, and approved site overtime in INR. It does not replace statutory Indian PF/ESIC portals.
            </div>
        </div>

        <!-- Summary KPI Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs transition-colors">
                <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Gross Base Earned</div>
                <div class="text-lg font-black text-slate-900 dark:text-white mt-1">INR {{ number_format((float) $stats['total_base'], 2) }}</div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-purple-200 dark:border-purple-800 bg-purple-50/30 dark:bg-purple-950/30 shadow-xs transition-colors">
                <div class="text-[11px] font-medium text-purple-700 dark:text-purple-400">Approved OT Pay</div>
                <div class="text-lg font-black text-purple-900 dark:text-purple-300 mt-1">INR {{ number_format((float) $stats['total_ot'], 2) }}</div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs transition-colors">
                <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Allowances & Deductions</div>
                <div class="text-sm font-bold text-slate-800 dark:text-slate-200 mt-1">
                    <span class="text-emerald-700 dark:text-emerald-400">+{{ number_format((float) $stats['total_allowances'], 2) }}</span> /
                    <span class="text-rose-700 dark:text-rose-400">-{{ number_format((float) $stats['total_deductions'], 2) }}</span>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-emerald-300 dark:border-emerald-800 bg-emerald-50/40 dark:bg-emerald-950/30 shadow-xs col-span-2 sm:col-span-1 transition-colors">
                <div class="text-[11px] font-medium text-emerald-800 dark:text-emerald-400">Total Net Disbursable</div>
                <div class="text-lg font-black text-emerald-900 dark:text-emerald-300 mt-1">INR {{ number_format((float) $stats['total_net'], 2) }}</div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs col-span-2 sm:col-span-1 flex flex-col justify-between transition-colors">
                <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Lock State Distribution</div>
                <div class="text-[11px] font-bold flex items-center gap-1 mt-1">
                    <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">{{ $stats['draft_count'] }} Draft</span>
                    <span class="px-1.5 py-0.5 rounded bg-sky-100 dark:bg-sky-950/60 text-sky-800 dark:text-sky-300">{{ $stats['approved_count'] }} Approved</span>
                    <span class="px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300">{{ $stats['paid_count'] }} Paid</span>
                </div>
            </div>
        </div>

        <!-- Payroll Records Master Table -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden transition-colors">
            <div class="p-4 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="text-xs font-bold text-slate-700 dark:text-slate-300">
                    Showing {{ $records->count() }} Employee Records for Period {{ $periodMonth }}
                </div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400">
                    Draft rows can be recalculated or adjusted. Approved and Paid records are immutable locked snapshots.
                </div>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="min-w-full text-xs text-left divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-100/70 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 font-bold uppercase">
                        <tr>
                            <th class="py-3 px-3">Employee</th>
                            <th class="py-3 px-3">Wage</th>
                            <th class="py-3 px-3">Days Paid</th>
                            <th class="py-3 px-3">Approved OT</th>
                            <th class="py-3 px-3">Base (INR)</th>
                            <th class="py-3 px-3">OT Pay (INR)</th>
                            <th class="py-3 px-3">Allowances</th>
                            <th class="py-3 px-3">Deductions</th>
                            <th class="py-3 px-3">Net Salary (INR)</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-800 dark:text-slate-200">
                        @forelse($records as $rec)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/50" x-data="{ editing: false }">
                            <td class="py-3 px-3 whitespace-nowrap">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $rec->employee?->full_name }}</div>
                                <div class="text-slate-500 dark:text-slate-400 text-[11px]">{{ $rec->employee?->employee_code }} &bull; {{ $rec->employee?->designation }}</div>
                            </td>

                            <td class="py-3 px-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $rec->employee?->wage_type === 'monthly' ? 'bg-indigo-100 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-300' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300' }}">
                                    {{ $rec->employee?->wage_type }}
                                </span>
                            </td>

                            <td class="py-3 px-3 whitespace-nowrap font-bold text-slate-900 dark:text-white">{{ $rec->days_present }}</td>
                            <td class="py-3 px-3 whitespace-nowrap font-bold text-purple-700 dark:text-purple-400">{{ $rec->total_overtime_hours }} hrs</td>
                            <td class="py-3 px-3 whitespace-nowrap">{{ number_format((float) $rec->base_earned_inr, 2) }}</td>
                            <td class="py-3 px-3 whitespace-nowrap text-purple-700 dark:text-purple-400 font-semibold">{{ number_format((float) $rec->overtime_pay_inr, 2) }}</td>

                            <!-- Allowances Column -->
                            <td class="py-3 px-3 whitespace-nowrap">
                                <template x-if="!editing">
                                    <span class="text-emerald-700 dark:text-emerald-400 font-medium">+{{ number_format((float) $rec->allowances_inr, 2) }}</span>
                                </template>
                                <template x-if="editing">
                                    <form id="edit-form-{{ $rec->id }}" action="{{ route('payroll.update', $rec) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" step="0.01" min="0" name="allowances_inr" value="{{ $rec->allowances_inr }}" class="w-20 text-xs py-1 px-1.5 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                                    </form>
                                </template>
                            </td>

                            <!-- Deductions Column -->
                            <td class="py-3 px-3 whitespace-nowrap">
                                <template x-if="!editing">
                                    <span class="text-rose-700 dark:text-rose-400 font-medium">-{{ number_format((float) $rec->deductions_inr, 2) }}</span>
                                </template>
                                <template x-if="editing">
                                    <div>
                                        <input form="edit-form-{{ $rec->id }}" type="number" step="0.01" min="0" name="deductions_inr" value="{{ $rec->deductions_inr }}" class="w-20 text-xs py-1 px-1.5 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                                    </div>
                                </template>
                            </td>

                            <!-- Net Salary -->
                            <td class="py-3 px-3 whitespace-nowrap font-black text-slate-900 dark:text-white text-sm">
                                INR {{ number_format((float) $rec->net_salary_inr, 2) }}
                            </td>

                            <!-- Lock Status Badge -->
                            <td class="py-3 px-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded font-bold text-[10px]
                                    @if($rec->status === 'paid') bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300
                                    @elseif($rec->status === 'approved') bg-sky-100 dark:bg-sky-950/60 text-sky-800 dark:text-sky-300
                                    @else bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300
                                    @endif">
                                    {{ ucfirst($rec->status) }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-3 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($rec->status === 'draft')
                                        <template x-if="!editing">
                                            <button type="button" @click="editing = true" class="px-2 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded text-[10px] font-semibold cursor-pointer transition">
                                                Edit Adjustments
                                            </button>
                                        </template>
                                        <template x-if="editing">
                                            <div class="flex items-center gap-1">
                                                <button form="edit-form-{{ $rec->id }}" type="submit" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded text-[10px] font-bold cursor-pointer transition">
                                                    Save
                                                </button>
                                                <button type="button" @click="editing = false" class="px-2 py-1 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded text-[10px] transition">
                                                    Cancel
                                                </button>
                                            </div>
                                        </template>

                                        <form action="{{ route('payroll.approve', $rec) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-sky-600 hover:bg-sky-500 text-white rounded text-[10px] font-bold cursor-pointer min-h-[28px] transition">
                                                Approve
                                            </button>
                                        </form>
                                    @elseif($rec->status === 'approved')
                                        <form action="{{ route('payroll.mark-paid', $rec) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded text-[10px] font-bold cursor-pointer min-h-[28px] transition">
                                                Mark Paid
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('payroll.payslip', $rec) }}" target="_blank" class="px-2 py-1 text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 font-bold text-[10px] hover:underline">
                                        Payslip &rarr;
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="py-8 text-center text-slate-400">
                                No payroll records generated for {{ $periodMonth }}. Tap "Recalculate Month" above to generate initial drafts from site attendance.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
