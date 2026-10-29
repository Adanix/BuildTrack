<x-layouts.app>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Workforce Attendance & Overtime Queue</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Muster roll logs, status weights & supervisor overtime approval</p>
            </div>
            @if(in_array(auth()->user()->role, ['admin', 'project_manager', 'foreman', 'hr_payroll']))
            <a href="{{ route('attendance.bulk') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-xs min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <span>Bulk Crew Attendance Sheet</span>
            </a>
            @endif
        </div>

        <!-- Tab Selector: Daily Roster vs Overtime Approval Queue -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-1 flex overflow-x-auto max-w-md">
            <a href="{{ route('attendance.index', ['tab' => 'daily', 'date' => $date, 'project_id' => $projectId]) }}" class="flex-1 py-2 px-3 text-xs font-bold rounded-lg text-center whitespace-nowrap {{ $tab !== 'overtime_queue' ? 'bg-amber-500 text-slate-950 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Daily Site Attendance
            </a>
            <a href="{{ route('attendance.index', ['tab' => 'overtime_queue']) }}" class="flex-1 py-2 px-3 text-xs font-bold rounded-lg text-center whitespace-nowrap flex items-center justify-center gap-1.5 {{ $tab === 'overtime_queue' ? 'bg-amber-500 text-slate-950 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                <span>Overtime Approval Queue</span>
                @if($pendingOtCount > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $tab === 'overtime_queue' ? 'bg-slate-950 text-amber-400' : 'bg-purple-100 text-purple-800' }}">
                    {{ $pendingOtCount }}
                </span>
                @endif
            </a>
        </div>

        @if($tab !== 'overtime_queue')
        <!-- Daily Filter Bar -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
            <form method="GET" action="{{ route('attendance.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                <input type="hidden" name="tab" value="daily">
                <div>
                    <label for="att_date" class="block text-xs font-semibold text-slate-600 mb-1">Attendance Date</label>
                    <input type="date" id="att_date" name="date" value="{{ $date }}" class="w-full text-xs rounded-lg border-slate-300 bg-slate-50 py-2 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                </div>

                <div>
                    <label for="att_project" class="block text-xs font-semibold text-slate-600 mb-1">Project Site</label>
                    <select id="att_project" name="project_id" class="w-full text-xs rounded-lg border-slate-300 bg-slate-50 py-2 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                        <option value="">All Project Sites</option>
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ $projectId == $p->id ? 'selected' : '' }}>{{ $p->code }}: {{ Str::limit($p->name, 24) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-lg min-h-[36px]">
                        Apply Filter
                    </button>
                    @if($date || $projectId)
                    <a href="{{ route('attendance.index', ['tab' => 'daily']) }}" class="py-2 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs rounded-lg min-h-[36px] flex items-center">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>
        @else
        <!-- Overtime Queue Banner -->
        <div class="bg-purple-900 text-purple-100 rounded-2xl p-5 border border-purple-800 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-white">Pending Overtime Authorization Queue</h3>
                <p class="text-xs text-purple-200 mt-0.5">
                    Foremen record site overtime hours on daily crew muster rolls. Only authorized Overtime enters the INR Payroll Calculation Engine.
                </p>
            </div>
            @if(in_array(auth()->user()->role, ['admin', 'project_manager', 'hr_payroll']) && $pendingOtCount > 0)
            <form action="{{ route('attendance.approve-all-overtime') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-purple-400 hover:bg-purple-300 text-purple-950 font-bold text-xs rounded-xl shadow-xs cursor-pointer min-h-[40px]">
                    Approve All Pending OT ({{ $pendingOtCount }})
                </button>
            </form>
            @endif
        </div>
        @endif

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto w-full">
                <table class="min-w-full text-xs text-left divide-y divide-slate-200">
                    <thead class="bg-slate-50 text-slate-600 uppercase font-semibold">
                        <tr>
                            <th class="py-3 px-3.5">Date</th>
                            <th class="py-3 px-3.5">Employee</th>
                            <th class="py-3 px-3.5">Site Code</th>
                            <th class="py-3 px-3.5">Status</th>
                            <th class="py-3 px-3.5">Regular Hrs</th>
                            <th class="py-3 px-3.5">Overtime Hrs</th>
                            <th class="py-3 px-3.5">OT Approval Status</th>
                            <th class="py-3 px-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($records as $rec)
                        <tr class="hover:bg-slate-50/80">
                            <td class="py-3 px-3.5 whitespace-nowrap font-medium text-slate-900">
                                {{ $rec->attendance_date?->format('d M Y') }}
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <span class="font-bold text-slate-900">{{ $rec->employee?->full_name }}</span>
                                <span class="text-[11px] text-slate-500">({{ $rec->employee?->employee_code }})</span>
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap font-semibold">{{ $rec->project?->code }}</td>
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                @if($rec->status === 'present')
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-emerald-100 text-emerald-800">Present (1.0d)</span>
                                @elseif($rec->status === 'half_day')
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-amber-100 text-amber-800">Half Day (0.5d)</span>
                                @elseif($rec->status === 'paid_leave')
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-sky-100 text-sky-800">Paid Leave (1.0d)</span>
                                @else
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-rose-100 text-rose-800">Absent (0.0d)</span>
                                @endif
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap">{{ $rec->regular_hours }} hrs</td>
                            <td class="py-3 px-3.5 whitespace-nowrap font-black text-slate-900">
                                {{ $rec->overtime_hours > 0 ? $rec->overtime_hours . ' hrs' : '-' }}
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                @if((float)$rec->overtime_hours > 0)
                                    @if($rec->overtime_approved)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Approved ({{ $rec->approver?->name ?? 'PM' }})
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">
                                        Pending Approval
                                    </span>
                                    @endif
                                @else
                                    <span class="text-slate-400">None</span>
                                @endif
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap text-right">
                                @if((float)$rec->overtime_hours > 0 && ! $rec->overtime_approved && in_array(auth()->user()->role, ['admin', 'project_manager', 'hr_payroll']))
                                <form action="{{ route('attendance.approve-overtime', $rec) }}" method="POST" class="inline m-0">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 bg-purple-600 hover:bg-purple-700 text-white font-bold text-[10px] rounded cursor-pointer min-h-[30px]">
                                        Approve OT
                                    </button>
                                </form>
                                @else
                                <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                No attendance records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>

