<x-layouts.app>
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <a href="{{ route('employees.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 mb-2 transition-colors">
                &larr; Back to Employees
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-xs font-black bg-slate-800 text-amber-400">{{ $employee->employee_code }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $employee->wage_type === 'monthly' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/80 dark:text-indigo-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300' }}">
                            {{ $employee->wage_type }} Wage
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $employee->status === 'active' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                            {{ ucfirst($employee->status) }}
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-slate-100 mt-1">{{ $employee->full_name }}</h1>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $employee->designation }} &bull; Phone: {{ $employee->phone }}</p>
                </div>

                @if(auth()->user()->isAdmin() || auth()->user()->isHrPayroll())
                <div class="flex items-center gap-2">
                    <a href="{{ route('employees.edit', $employee) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold text-xs rounded-xl min-h-[40px] flex items-center transition-colors">
                        Edit Profile
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Compensation Details -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs">
            <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-4">Wage Structure (INR)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-800">
                    <div class="text-xs text-slate-500 dark:text-slate-400">Base Salary / Daily Rate</div>
                    <div class="text-xl font-black text-slate-900 dark:text-slate-100 mt-1">
                        {{ $employee->formatted_base_salary }}
                        <span class="text-xs font-normal text-slate-500 dark:text-slate-400">{{ $employee->wage_type === 'daily' ? 'per day' : 'per month (26d norm)' }}</span>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-900/60">
                    <div class="text-xs text-purple-700 dark:text-purple-300 font-medium">Overtime Hourly Rate</div>
                    <div class="text-xl font-black text-purple-900 dark:text-purple-200 mt-1">
                        {{ $employee->formatted_overtime_rate }}
                        <span class="text-xs font-normal text-purple-600 dark:text-purple-400">per approved OT hr</span>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-800">
                    <div class="text-xs text-slate-500 dark:text-slate-400">Primary Project Site</div>
                    <div class="text-sm font-bold text-slate-900 dark:text-slate-100 mt-1 truncate">
                        {{ $employee->primaryProject ? "{$employee->primaryProject->code} ({$employee->primaryProject->name})" : 'Unassigned / General Pool' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance History -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-3">Recent Attendance Logs (Last 15 records)</h3>
            <div class="overflow-x-auto w-full">
                <table class="min-w-full text-xs text-left divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 font-semibold uppercase">
                        <tr>
                            <th class="py-2.5 px-3">Date</th>
                            <th class="py-2.5 px-3">Site</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3">Regular Hrs</th>
                            <th class="py-2.5 px-3">OT Hrs</th>
                            <th class="py-2.5 px-3">OT Approval</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-slate-700 dark:text-slate-300">
                        @forelse($employee->attendanceRecords as $att)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                            <td class="py-2.5 px-3 whitespace-nowrap font-medium text-slate-900 dark:text-slate-100">{{ $att->attendance_date?->format('d M Y') }}</td>
                            <td class="py-2.5 px-3 whitespace-nowrap">{{ $att->project?->code }}</td>
                            <td class="py-2.5 px-3 whitespace-nowrap capitalize font-semibold">{{ str_replace('_', ' ', $att->status) }}</td>
                            <td class="py-2.5 px-3 whitespace-nowrap">{{ $att->regular_hours }} hrs</td>
                            <td class="py-2.5 px-3 whitespace-nowrap font-bold text-slate-900 dark:text-slate-100">{{ $att->overtime_hours }} hrs</td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                @if((float)$att->overtime_hours > 0)
                                    @if($att->overtime_approved)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">Approved</span>
                                    @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300">Pending Review</span>
                                    @endif
                                @else
                                    <span class="text-slate-400 dark:text-slate-500">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400 dark:text-slate-500">No attendance history logged yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(auth()->user()->isAdmin() || auth()->user()->isHrPayroll())
        <!-- Payroll Records History -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-3">Monthly INR Payroll Records</h3>
            <div class="overflow-x-auto w-full">
                <table class="min-w-full text-xs text-left divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 font-semibold uppercase">
                        <tr>
                            <th class="py-2.5 px-3">Period</th>
                            <th class="py-2.5 px-3">Paid Days</th>
                            <th class="py-2.5 px-3">Approved OT</th>
                            <th class="py-2.5 px-3">Base Earned</th>
                            <th class="py-2.5 px-3">OT Pay</th>
                            <th class="py-2.5 px-3">Net Salary</th>
                            <th class="py-2.5 px-3">Lock Status</th>
                            <th class="py-2.5 px-3 text-right">Payslip</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-slate-700 dark:text-slate-300">
                        @forelse($employee->payrollRecords as $pay)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                            <td class="py-2.5 px-3 whitespace-nowrap font-bold text-slate-900 dark:text-slate-100">{{ $pay->period_month }}</td>
                            <td class="py-2.5 px-3 whitespace-nowrap">{{ $pay->days_present }}</td>
                            <td class="py-2.5 px-3 whitespace-nowrap">{{ $pay->total_overtime_hours }} hrs</td>
                            <td class="py-2.5 px-3 whitespace-nowrap">{{ $pay->formatted_base_earned }}</td>
                            <td class="py-2.5 px-3 whitespace-nowrap">{{ $pay->formatted_overtime_pay }}</td>
                            <td class="py-2.5 px-3 whitespace-nowrap font-extrabold text-emerald-600 dark:text-emerald-400">{{ $pay->formatted_net_salary }}</td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded font-bold text-[10px]
                                    @if($pay->status === 'paid') bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300
                                    @elseif($pay->status === 'approved') bg-sky-100 text-sky-800 dark:bg-sky-950/80 dark:text-sky-300
                                    @else bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300
                                    @endif">
                                    {{ ucfirst($pay->status) }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap text-right">
                                <a href="{{ route('payroll.payslip', $pay) }}" target="_blank" class="text-amber-600 dark:text-amber-400 font-bold hover:underline">Print Payslip</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-slate-400 dark:text-slate-500">No payroll batches calculated for this employee.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-layouts.app>
