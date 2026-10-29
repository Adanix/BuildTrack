<x-layouts.app>
    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <a href="{{ route('daily-reports.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white mb-2 transition">
                &larr; Back to Site Reports
            </a>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded text-xs font-black bg-slate-800 dark:bg-slate-700 text-amber-400">
                    Report #{{ $dailyReport->id }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold
                    @if($dailyReport->status === 'reviewed') bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800
                    @elseif($dailyReport->status === 'flagged') bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800
                    @else bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800
                    @endif">
                    {{ ucfirst($dailyReport->status) }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">
                {{ $dailyReport->work_category }} &bull; {{ $dailyReport->report_date?->format('d M Y') }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Site: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $dailyReport->project?->code }}: {{ $dailyReport->project?->name }}</span>
            </p>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs space-y-6 transition-colors">
            <!-- Metrics Row -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">Quantity Completed</div>
                    <div class="text-xl font-black text-slate-900 dark:text-white mt-1">
                        {{ $dailyReport->quantity_completed ? number_format((float) $dailyReport->quantity_completed, 2) . ' ' . $dailyReport->quantity_unit : 'N/A' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">Reported Cumulative %</div>
                    <div class="text-xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $dailyReport->reported_progress_percent }}%</div>
                </div>

                <div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">Labor Headcount</div>
                    <div class="text-xl font-bold text-slate-900 dark:text-white mt-1">{{ $dailyReport->labor_count }} workers</div>
                </div>

                <div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">Weather Condition</div>
                    <div class="text-sm font-bold text-slate-800 dark:text-slate-200 mt-1">{{ $dailyReport->weather }}</div>
                </div>
            </div>

            <!-- Activity Summary -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Activity Description & Structural Element</h3>
                <p class="text-sm text-slate-800 dark:text-slate-200 leading-relaxed bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-200 dark:border-slate-700/80">
                    {{ $dailyReport->activity_summary }}
                </p>
            </div>

            @if($dailyReport->issues_or_delays)
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400 mb-2">Delays / Issues Encountered</h3>
                <p class="text-sm text-rose-900 dark:text-rose-200 bg-rose-50 dark:bg-rose-950/40 p-4 rounded-xl border border-rose-200 dark:border-rose-800/60">
                    {{ $dailyReport->issues_or_delays }}
                </p>
            </div>
            @endif

            <!-- Reporter and Review Metadata -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs">
                <div>
                    <div class="text-slate-500 dark:text-slate-400">Reported By</div>
                    <div class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $dailyReport->reporter?->name ?? 'Foreman' }}</div>
                    <div class="text-slate-400 dark:text-slate-500 text-[11px]">{{ $dailyReport->created_at?->format('d M Y, H:i') }} (IST)</div>
                </div>

                <div>
                    <div class="text-slate-500 dark:text-slate-400">Supervisor Review</div>
                    @if($dailyReport->reviewed_by)
                    <div class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $dailyReport->reviewer?->name }} (Reviewed on {{ $dailyReport->reviewed_at?->format('d M Y, H:i') }})</div>
                    @if($dailyReport->review_notes)
                    <div class="text-slate-600 dark:text-slate-400 mt-1 italic">&ldquo;{{ $dailyReport->review_notes }}&rdquo;</div>
                    @endif
                    @else
                    <div class="text-amber-600 dark:text-amber-400 font-semibold mt-0.5">Pending Supervisor Approval</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Supervisor Review & Approval Action Form -->
        @if(auth()->user()->isAdmin() || (auth()->user()->isProjectManager() && $dailyReport->project?->manager_id === auth()->id()))
        <div class="bg-slate-900 dark:bg-slate-900 rounded-2xl text-white p-6 border border-slate-800 shadow-md">
            <h3 class="text-base font-bold text-white mb-1">Supervisor Review & Verification</h3>
            <p class="text-xs text-slate-400 mb-4">
                Approving this report updates project progress to <strong class="text-amber-400">&ge; {{ $dailyReport->reported_progress_percent }}%</strong> deterministically. Flagging requests foreman correction without modifying overall project progress.
            </p>

            <form action="{{ route('daily-reports.review', $dailyReport) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="review_notes" class="block text-xs font-semibold text-slate-300">Supervisor Review Notes / Concrete Cube Test Results</label>
                    <textarea id="review_notes" name="review_notes" rows="2" class="mt-1 block w-full rounded-xl bg-slate-800 border-slate-700 text-white text-xs p-3 focus:border-amber-500 focus:outline-none" placeholder="e.g. Slump test recorded 120mm. Core sample batch #4 verified against IS 456 standards.">{{ old('review_notes', $dailyReport->review_notes) }}</textarea>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" name="action" value="reviewed" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs rounded-xl shadow-xs cursor-pointer min-h-[44px] transition">
                        Approve Report & Sync Progress
                    </button>

                    <button type="submit" name="action" value="flagged" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-semibold text-xs rounded-xl shadow-xs cursor-pointer min-h-[44px] transition">
                        Flag Report for Revision
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>
</x-layouts.app>
