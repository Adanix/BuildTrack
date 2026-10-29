<x-layouts.app>
    <div class="space-y-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight">System Activity & Audit Trail</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Immutable event trail for progress overrides, approvals, and compliance reminders</p>
        </div>

        <!-- Filter bar -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs">
            <form method="GET" action="{{ route('activity-logs.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="flex-1 w-full">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search action, description, or user..." class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-900 focus:border-amber-500 focus:outline-none">
                </div>
                <div class="w-full sm:w-48">
                    <select name="module" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-amber-500 focus:outline-none">
                        <option value="all">All Modules</option>
                        @foreach($modules as $m)
                        <option value="{{ $m }}" {{ $module === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="flex-1 sm:flex-initial py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-amber-500 dark:hover:bg-amber-400 text-white dark:text-slate-950 font-bold text-xs rounded-lg min-h-[40px] transition-colors">
                        Filter Logs
                    </button>
                    @if($search || ($module && $module !== 'all'))
                    <a href="{{ route('activity-logs.index') }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs rounded-lg min-h-[40px] flex items-center transition-colors">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Logs Timeline / Table -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="overflow-x-auto w-full">
                <table class="min-w-full text-xs text-left divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 font-bold uppercase">
                        <tr>
                            <th class="py-3 px-3.5">Timestamp (IST)</th>
                            <th class="py-3 px-3.5">User</th>
                            <th class="py-3 px-3.5">Module</th>
                            <th class="py-3 px-3.5">Action Event</th>
                            <th class="py-3 px-3.5">Description & Payload Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-slate-800 dark:text-slate-200">
                        @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-3.5 whitespace-nowrap font-mono text-[11px] text-slate-500 dark:text-slate-400">
                                {{ $log->created_at?->format('d M Y, H:i:s') }}
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <span class="font-bold text-slate-900 dark:text-slate-100">{{ $log->user?->name ?? 'System Service' }}</span>
                                <div class="text-[10px] text-slate-400 dark:text-slate-500">{{ $log->user?->role_display_name ?? 'Artisan Scheduler' }}</div>
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                    @if($log->module === 'Projects') bg-indigo-100 text-indigo-800 dark:bg-indigo-950/80 dark:text-indigo-300
                                    @elseif($log->module === 'Daily Reports') bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300
                                    @elseif($log->module === 'Payroll') bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300
                                    @elseif($log->module === 'Compliance') bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300
                                    @elseif($log->module === 'Attendance') bg-purple-100 text-purple-800 dark:bg-purple-950/80 dark:text-purple-300
                                    @else bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300
                                    @endif">
                                    {{ $log->module }}
                                </span>
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap font-semibold text-slate-900 dark:text-slate-100">
                                {{ $log->action }}
                            </td>
                            <td class="py-3 px-3.5 text-slate-600 dark:text-slate-300 text-xs leading-relaxed max-w-md">
                                {{ $log->description }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 dark:text-slate-500">
                                No activity audit log records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
