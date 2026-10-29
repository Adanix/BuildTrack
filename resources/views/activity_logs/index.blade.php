<x-layouts.app>
    <div class="space-y-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">System Activity & Audit Trail</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Immutable event trail for progress overrides, approvals, and compliance reminders</p>
        </div>

        <!-- Filter bar -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
            <form method="GET" action="{{ route('activity-logs.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="flex-1 w-full">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search action, description, or user..." class="w-full text-xs rounded-lg border-slate-300 bg-slate-50 py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                </div>
                <div class="w-full sm:w-48">
                    <select name="module" class="w-full text-xs rounded-lg border-slate-300 bg-slate-50 py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                        <option value="all">All Modules</option>
                        @foreach($modules as $m)
                        <option value="{{ $m }}" {{ $module === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="flex-1 sm:flex-initial py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-lg min-h-[40px]">
                        Filter Logs
                    </button>
                    @if($search || ($module && $module !== 'all'))
                    <a href="{{ route('activity-logs.index') }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs rounded-lg min-h-[40px] flex items-center">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Logs Timeline / Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto w-full">
                <table class="min-w-full text-xs text-left divide-y divide-slate-200">
                    <thead class="bg-slate-50 text-slate-600 font-bold uppercase">
                        <tr>
                            <th class="py-3 px-3.5">Timestamp (IST)</th>
                            <th class="py-3 px-3.5">User</th>
                            <th class="py-3 px-3.5">Module</th>
                            <th class="py-3 px-3.5">Action Event</th>
                            <th class="py-3 px-3.5">Description & Payload Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/60">
                            <td class="py-3 px-3.5 whitespace-nowrap font-mono text-[11px] text-slate-500">
                                {{ $log->created_at?->format('d M Y, H:i:s') }}
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <span class="font-bold text-slate-900">{{ $log->user?->name ?? 'System Service' }}</span>
                                <div class="text-[10px] text-slate-400">{{ $log->user?->role_display_name ?? 'Artisan Scheduler' }}</div>
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                    @if($log->module === 'Projects') bg-indigo-100 text-indigo-800
                                    @elseif($log->module === 'Daily Reports') bg-amber-100 text-amber-800
                                    @elseif($log->module === 'Payroll') bg-emerald-100 text-emerald-800
                                    @elseif($log->module === 'Compliance') bg-rose-100 text-rose-800
                                    @elseif($log->module === 'Attendance') bg-purple-100 text-purple-800
                                    @else bg-slate-100 text-slate-700
                                    @endif">
                                    {{ $log->module }}
                                </span>
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap font-semibold text-slate-900">
                                {{ $log->action }}
                            </td>
                            <td class="py-3 px-3.5 text-slate-600 text-xs leading-relaxed max-w-md">
                                {{ $log->description }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                No activity audit log records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
