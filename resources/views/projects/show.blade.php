<x-layouts.app>
    <div class="space-y-6" x-data="{ tab: 'overview' }">
        <!-- Back Navigation & Header -->
        <div>
            <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white mb-2 transition">
                &larr; Back to Project Portfolio
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-xs font-black bg-slate-800 dark:bg-slate-700 text-amber-400">
                            {{ $project->code }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold
                            @if($project->status === 'active') bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800
                            @elseif($project->status === 'planned') bg-sky-50 dark:bg-sky-950/60 text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-800
                            @elseif($project->status === 'on_hold') bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800
                            @else bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $project->name }}</h1>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Client: {{ $project->client_name }} &bull; Location: {{ $project->location }}</p>
                </div>

                <div class="flex items-center gap-2">
                    @if(auth()->user()->isAdmin() || (auth()->user()->isProjectManager() && $project->manager_id === auth()->id()))
                    <a href="{{ route('projects.edit', $project) }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold text-xs rounded-xl min-h-[40px] flex items-center transition">
                        Edit Project
                    </a>
                    @endif
                    @if(! auth()->user()->isDriver())
                    <a href="{{ route('daily-reports.create') }}?project_id={{ $project->id }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-xs min-h-[40px] flex items-center transition">
                        + New Site Report
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4-Tab Command Navigation -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs p-1 flex overflow-x-auto transition-colors">
            <button type="button" @click="tab = 'overview'" :class="tab === 'overview' ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" class="flex-1 py-2.5 px-4 text-xs rounded-lg transition text-center whitespace-nowrap min-h-[40px] cursor-pointer">
                1. Overview & Progress
            </button>
            <button type="button" @click="tab = 'reports'" :class="tab === 'reports' ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" class="flex-1 py-2.5 px-4 text-xs rounded-lg transition text-center whitespace-nowrap min-h-[40px] cursor-pointer">
                2. Daily Site Reports ({{ $project->dailyReports->count() }})
            </button>
            <button type="button" @click="tab = 'workforce'" :class="tab === 'workforce' ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" class="flex-1 py-2.5 px-4 text-xs rounded-lg transition text-center whitespace-nowrap min-h-[40px] cursor-pointer">
                3. Assigned Workforce ({{ $project->employees->count() }})
            </button>
            <button type="button" @click="tab = 'machinery'" :class="tab === 'machinery' ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" class="flex-1 py-2.5 px-4 text-xs rounded-lg transition text-center whitespace-nowrap min-h-[40px] cursor-pointer">
                4. Deployed Machinery ({{ $project->machineries->count() }})
            </button>
        </div>

        <!-- Tab 1: Overview & Progress -->
        <div x-show="tab === 'overview'" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs transition-colors">
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase">Overall Completion</div>
                    <div class="text-3xl font-black text-slate-900 dark:text-white mt-2">{{ $project->overall_progress }}%</div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full mt-3 overflow-hidden">
                        <div class="bg-amber-500 h-2.5 rounded-full" style="width: {{ $project->overall_progress }}%"></div>
                    </div>
                    <div class="text-[11px] text-slate-400 mt-2">Driven by reviewed daily site progress reports.</div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs transition-colors">
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase">Financial Budget</div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white mt-2">{{ $project->formatted_budget }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-2">Standardized INR contract value.</div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs transition-colors">
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase">Project Timeline</div>
                    <div class="text-sm font-bold text-slate-900 dark:text-white mt-2">
                        {{ $project->start_date?->format('d M Y') }} &rarr; {{ $project->target_end_date?->format('d M Y') }}
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-2">Manager: <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $project->manager?->name ?? 'Unassigned' }}</span></div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs transition-colors">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-2">Scope of Civil Works</h3>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">{{ $project->description ?: 'No detailed scope description provided.' }}</p>
            </div>
        </div>

        <!-- Tab 2: Daily Site Reports -->
        <div x-show="tab === 'reports'" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs transition-colors">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Site Log Records ({{ $project->dailyReports->count() }})</h3>
                @if(! auth()->user()->isDriver())
                <a href="{{ route('daily-reports.create') }}?project_id={{ $project->id }}" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">
                    + Log New Activity
                </a>
                @endif
            </div>

            <div class="overflow-x-auto w-full">
                <table class="min-w-full text-xs text-left divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/70 text-slate-600 dark:text-slate-300 font-semibold uppercase">
                        <tr>
                            <th class="py-2.5 px-3">Date</th>
                            <th class="py-2.5 px-3">Work Category</th>
                            <th class="py-2.5 px-3">Quantity</th>
                            <th class="py-2.5 px-3">Cumulative %</th>
                            <th class="py-2.5 px-3">Weather</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        @forelse($project->dailyReports as $rep)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50">
                            <td class="py-2.5 px-3 whitespace-nowrap font-medium text-slate-900 dark:text-white">{{ $rep->report_date?->format('d M Y') }}</td>
                            <td class="py-2.5 px-3 whitespace-nowrap">{{ $rep->work_category }}</td>
                            <td class="py-2.5 px-3 whitespace-nowrap font-semibold">
                                {{ $rep->quantity_completed ? number_format((float) $rep->quantity_completed, 2) . ' ' . $rep->quantity_unit : '-' }}
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap font-bold text-slate-900 dark:text-white">{{ $rep->reported_progress_percent }}%</td>
                            <td class="py-2.5 px-3 whitespace-nowrap">{{ $rep->weather }}</td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                @if($rep->status === 'reviewed')
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300">Reviewed / Approved</span>
                                @elseif($rep->status === 'flagged')
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300">Flagged</span>
                                @else
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">Submitted</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap text-right">
                                <a href="{{ route('daily-reports.show', $rep) }}" class="text-amber-600 dark:text-amber-400 font-bold hover:underline">View &rarr;</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400">No site reports logged for this project yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 3: Assigned Workforce -->
        <div x-show="tab === 'workforce'" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs transition-colors">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">Assigned Site Crew ({{ $project->employees->count() }})</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @forelse($project->employees as $emp)
                <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">{{ $emp->employee_code }}</span>
                        <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded {{ $emp->wage_type === 'monthly' ? 'bg-indigo-100 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-300' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300' }}">
                            {{ $emp->wage_type }}
                        </span>
                    </div>
                    <div class="font-bold text-slate-900 dark:text-white text-sm mt-1">{{ $emp->full_name }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $emp->designation }}</div>
                    <div class="text-xs text-slate-700 dark:text-slate-300 mt-2 font-medium">Base: {{ $emp->formatted_base_salary }} {{ $emp->wage_type === 'daily' ? '/day' : '/mo' }}</div>
                </div>
                @empty
                <div class="col-span-full py-6 text-center text-slate-400">No workforce members primarily assigned to this project site.</div>
                @endforelse
            </div>
        </div>

        <!-- Tab 4: Deployed Machinery -->
        <div x-show="tab === 'machinery'" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs transition-colors">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">Deployed Heavy Fleet ({{ $project->machineries->count() }})</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @forelse($project->machineries as $mch)
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-800 dark:text-slate-200">{{ $mch->asset_code }}</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                            @if($mch->operational_status === 'active_on_site') bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300
                            @elseif($mch->operational_status === 'under_maintenance') bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300
                            @else bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $mch->operational_status)) }}
                        </span>
                    </div>
                    <div class="font-bold text-slate-900 dark:text-white text-sm mt-1.5">{{ $mch->name }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $mch->registration_number }} &bull; {{ $mch->category }}</div>
                    <div class="text-xs text-slate-600 dark:text-slate-400 mt-2">Driver: <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $mch->assignedDriver?->name ?? 'Unassigned' }}</span></div>
                    <div class="mt-3 pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                        <a href="{{ route('machineries.show', $mch) }}" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">View Machine Profile &rarr;</a>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-6 text-center text-slate-400">No heavy machinery currently deployed to this site.</div>
                @endforelse
            </div>
        </div>

    </div>
</x-layouts.app>
