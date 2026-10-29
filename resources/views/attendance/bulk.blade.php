<x-layouts.app>
    <div class="space-y-6">
        <div>
            <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white mb-2 transition">
                &larr; Back to Attendance
            </a>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500 text-slate-950 uppercase tracking-wider">
                    Site Crew Sheet
                </span>
                <span class="text-xs text-slate-500 dark:text-slate-400">1-Screen Daily Roll Call</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">Bulk Daily Crew Attendance Sheet</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Mark muster-roll statuses and site overtime hours for all workers on this site</p>
        </div>

        <!-- Project & Date Selector Bar -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs transition-colors">
            <form method="GET" action="{{ route('attendance.bulk') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                <div>
                    <label for="bulk_project" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Select Project Site *</label>
                    <select id="bulk_project" name="project_id" class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 font-semibold text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:border-amber-500 focus:outline-none min-h-12">
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ $selectedProjectId == $p->id ? 'selected' : '' }}>
                            {{ $p->code }}: {{ $p->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="bulk_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Attendance Date *</label>
                    <input type="date" id="bulk_date" name="date" value="{{ $selectedDate }}" class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 font-semibold text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:border-amber-500 focus:outline-none min-h-12">
                </div>

                <div>
                    <button type="submit" class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl min-h-12 cursor-pointer transition">
                        Load Crew Roster
                    </button>
                </div>
            </form>
        </div>

        <!-- Bulk Attendance Entry Form -->
        <form action="{{ route('attendance.bulk.store') }}" method="POST">
            @csrf
            <input type="hidden" name="project_id" value="{{ $selectedProjectId }}">
            <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden transition-colors">
                <div class="p-4 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="text-xs font-bold text-slate-700 dark:text-slate-300">
                        Crew Roster: <span class="text-slate-900 dark:text-white font-black">{{ $employees->count() }} Employees Available</span>
                    </div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400">
                        Standard Hours: Present = 8.0h &bull; Half Day = 4.0h &bull; Leave/Absent = 0.0h
                    </div>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="min-w-full text-xs text-left divide-y divide-slate-200 dark:divide-slate-800">
                        <thead class="bg-slate-100/70 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 font-bold uppercase">
                            <tr>
                                <th class="py-3 px-3.5">Employee Name & Trade</th>
                                <th class="py-3 px-3.5">Wage Type</th>
                                <th class="py-3 px-3.5">Status</th>
                                <th class="py-3 px-3.5">Regular Hrs</th>
                                <th class="py-3 px-3.5">Overtime Hrs</th>
                                <th class="py-3 px-3.5">Field Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-800 dark:text-slate-200">
                            @forelse($employees as $idx => $emp)
                            @php
                                $existing = $existingRecords->get($emp->id);
                                $currentStatus = old("records.{$idx}.status", $existing?->status ?? 'present');
                                $currentReg = old("records.{$idx}.regular_hours", $existing?->regular_hours ?? 8.00);
                                $currentOt = old("records.{$idx}.overtime_hours", $existing?->overtime_hours ?? 0.00);
                                $currentRemarks = old("records.{$idx}.remarks", $existing?->remarks ?? '');
                            @endphp
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/50" x-data="{
                                status: '{{ $currentStatus }}',
                                regHours: {{ $currentReg }},
                                updateHours() {
                                    if (this.status === 'present') this.regHours = 8.00;
                                    else if (this.status === 'half_day') this.regHours = 4.00;
                                    else this.regHours = 0.00;
                                }
                            }">
                                <input type="hidden" name="records[{{ $idx }}][employee_id]" value="{{ $emp->id }}">

                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $emp->full_name }}</div>
                                    <div class="text-slate-500 dark:text-slate-400 text-[11px]">{{ $emp->employee_code }} &bull; {{ $emp->designation }}</div>
                                </td>

                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $emp->wage_type === 'monthly' ? 'bg-indigo-100 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-300' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300' }}">
                                        {{ $emp->wage_type }}
                                    </span>
                                </td>

                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <select name="records[{{ $idx }}][status]" x-model="status" @change="updateHours()" class="text-xs font-semibold rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 py-1.5 px-2.5 focus:border-amber-500 focus:outline-none">
                                        <option value="present">Present (1.0d)</option>
                                        <option value="half_day">Half Day (0.5d)</option>
                                        <option value="paid_leave">Paid Leave (1.0d)</option>
                                        <option value="absent">Absent (0.0d)</option>
                                    </select>
                                </td>

                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <input type="number" step="0.5" min="0" max="12" name="records[{{ $idx }}][regular_hours]" x-model="regHours" class="w-20 text-xs font-bold rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 py-1.5 px-2 focus:border-amber-500 focus:outline-none">
                                </td>

                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <input type="number" step="0.5" min="0" max="12" name="records[{{ $idx }}][overtime_hours]" value="{{ $currentOt }}" placeholder="0.0" class="w-20 text-xs font-black rounded-lg border-purple-300 dark:border-purple-700 bg-purple-50 dark:bg-purple-950/60 text-purple-900 dark:text-purple-300 py-1.5 px-2 focus:border-purple-500 focus:outline-none">
                                </td>

                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <input type="text" name="records[{{ $idx }}][remarks]" value="{{ $currentRemarks }}" placeholder="Optional note..." class="w-full min-w-[140px] text-xs rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 py-1.5 px-2 focus:bg-white dark:focus:bg-slate-800 focus:border-amber-500 focus:outline-none">
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    No active employees found to mark for this site.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="text-xs text-slate-500 dark:text-slate-400">
                        Recorded overtime requires Supervisor/PM approval before disbursement.
                    </div>
                    <button type="submit" class="px-6 py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-sm rounded-xl shadow-xs min-h-12 cursor-pointer flex items-center justify-center gap-1.5 transition">
                        <span>Save Crew Attendance Sheet &rarr;</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>
