<x-layouts.app>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Daily Site Reports</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Concrete pours, earthwork excavation, rebar tying & road base logs</p>
            </div>
            @if(! auth()->user()->isDriver())
            <a href="{{ route('daily-reports.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-xs min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Mobile Quick Logger</span>
            </a>
            @endif
        </div>

        <!-- Filter bar -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
            <form method="GET" action="{{ route('daily-reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Project Site</label>
                    <select name="project_id" class="w-full text-xs rounded-lg border-slate-300 bg-slate-50 py-2 px-2.5 focus:bg-white focus:border-amber-500 focus:outline-none">
                        <option value="">All Projects</option>
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ $projectId == $p->id ? 'selected' : '' }}>{{ $p->code }}: {{ Str::limit($p->name, 22) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Work Category</label>
                    <select name="work_category" class="w-full text-xs rounded-lg border-slate-300 bg-slate-50 py-2 px-2.5 focus:bg-white focus:border-amber-500 focus:outline-none">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $workCategory == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Review Status</label>
                    <select name="status" class="w-full text-xs rounded-lg border-slate-300 bg-slate-50 py-2 px-2.5 focus:bg-white focus:border-amber-500 focus:outline-none">
                        <option value="all">All Statuses</option>
                        <option value="review_queue" {{ $status === 'review_queue' ? 'selected' : '' }}>Review Queue (Submitted & Flagged)</option>
                        <option value="submitted" {{ $status === 'submitted' ? 'selected' : '' }}>Submitted (Pending Approval)</option>
                        <option value="reviewed" {{ $status === 'reviewed' ? 'selected' : '' }}>Reviewed / Approved</option>
                        <option value="flagged" {{ $status === 'flagged' ? 'selected' : '' }}>Flagged for Revision</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Date Range</label>
                    <div class="grid grid-cols-2 gap-1">
                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full text-[11px] rounded-lg border-slate-300 bg-slate-50 py-2 px-1.5 focus:bg-white focus:border-amber-500">
                        <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full text-[11px] rounded-lg border-slate-300 bg-slate-50 py-2 px-1.5 focus:bg-white focus:border-amber-500">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-lg min-h-[36px]">
                        Filter Reports
                    </button>
                    @if($projectId || $status || $workCategory || $dateFrom || $dateTo)
                    <a href="{{ route('daily-reports.index') }}" class="py-2 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs rounded-lg min-h-[36px] flex items-center">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        @if($pendingReviewCount > 0 && $status !== 'review_queue')
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex items-center justify-between text-xs text-amber-900">
            <span class="font-medium">
                There are <strong class="font-bold">{{ $pendingReviewCount }}</strong> site reports in the review queue awaiting supervisor approval.
            </span>
            <a href="{{ route('daily-reports.index', ['status' => 'review_queue']) }}" class="font-bold underline text-amber-800 hover:text-amber-950">
                Filter Review Queue &rarr;
            </a>
        </div>
        @endif

        <!-- Reports Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto w-full">
                <table class="min-w-full text-xs text-left divide-y divide-slate-200">
                    <thead class="bg-slate-50 text-slate-600 uppercase font-semibold">
                        <tr>
                            <th class="py-3 px-3.5">Report Date</th>
                            <th class="py-3 px-3.5">Project Site</th>
                            <th class="py-3 px-3.5">Work Category</th>
                            <th class="py-3 px-3.5">Quantity Completed</th>
                            <th class="py-3 px-3.5">Progress %</th>
                            <th class="py-3 px-3.5">Reporter</th>
                            <th class="py-3 px-3.5">Status</th>
                            <th class="py-3 px-3.5 text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($reports as $r)
                        <tr class="hover:bg-slate-50/80">
                            <td class="py-3 px-3.5 whitespace-nowrap font-bold text-slate-900">
                                {{ $r->report_date?->format('d M Y') }}
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <span class="font-bold text-slate-900">{{ $r->project?->code }}</span>
                                <div class="text-[11px] text-slate-500 truncate max-w-[180px]">{{ $r->project?->name }}</div>
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap font-medium text-slate-800">
                                {{ $r->work_category }}
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap font-bold">
                                {{ $r->quantity_completed ? number_format((float) $r->quantity_completed, 2) . ' ' . $r->quantity_unit : '-' }}
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap font-black text-slate-900">
                                {{ $r->reported_progress_percent }}%
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap text-slate-600">
                                {{ $r->reporter?->name ?? 'Foreman' }}
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                @if($r->status === 'reviewed')
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-emerald-100 text-emerald-800">Reviewed / Approved</span>
                                @elseif($r->status === 'flagged')
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-rose-100 text-rose-800">Flagged</span>
                                @else
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-amber-100 text-amber-800">Submitted</span>
                                @endif
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap text-right">
                                <a href="{{ route('daily-reports.show', $r) }}" class="text-amber-600 hover:text-amber-700 font-bold hover:underline">
                                    Inspect &rarr;
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                No daily site reports found matching your criteria.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>

