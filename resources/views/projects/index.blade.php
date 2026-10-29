<x-layouts.app>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Projects & Infrastructure Sites</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Civil engineering contracts, flyovers, drainage, and road works</p>
            </div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-xs min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>New Project Contract</span>
            </a>
            @endif
        </div>

        <!-- Filter bar -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
            <form method="GET" action="{{ route('projects.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="flex-1 w-full">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by project name, code (CW-PUN-01), client, or location..." class="w-full text-xs rounded-lg border-slate-300 bg-slate-50 py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                </div>
                <div class="w-full sm:w-48">
                    <select name="status" class="w-full text-xs rounded-lg border-slate-300 bg-slate-50 py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                        <option value="all">All Statuses</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Sites</option>
                        <option value="planned" {{ $status === 'planned' ? 'selected' : '' }}>Planned Sites</option>
                        <option value="on_hold" {{ $status === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="flex-1 sm:flex-initial py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-lg min-h-[40px]">
                        Filter
                    </button>
                    @if($search || ($status && $status !== 'all'))
                    <a href="{{ route('projects.index') }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs rounded-lg min-h-[40px] flex items-center">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Projects Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($projects as $p)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between hover:border-slate-300 transition">
                <div>
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-black bg-slate-100 text-slate-800 tracking-wider">
                                {{ $p->code }}
                            </span>
                            <h3 class="text-base font-bold text-slate-900 mt-1 leading-snug">
                                <a href="{{ route('projects.show', $p) }}" class="hover:text-amber-600">
                                    {{ $p->name }}
                                </a>
                            </h3>
                            <div class="text-xs text-slate-500 mt-0.5">Client: {{ $p->client_name }}</div>
                        </div>

                        <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                            @if($p->status === 'active') bg-emerald-100 text-emerald-800
                            @elseif($p->status === 'planned') bg-sky-100 text-sky-800
                            @elseif($p->status === 'on_hold') bg-amber-100 text-amber-800
                            @else bg-slate-100 text-slate-700
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-slate-500 font-medium">Overall Site Progress</span>
                            <span class="font-black text-slate-900">{{ $p->overall_progress }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $p->overall_progress }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-slate-100 text-xs">
                        <div>
                            <div class="text-slate-500">Contract Budget</div>
                            <div class="font-bold text-slate-900 mt-0.5">{{ $p->formatted_budget }}</div>
                        </div>
                        <div>
                            <div class="text-slate-500">Location</div>
                            <div class="font-medium text-slate-800 mt-0.5 truncate">{{ $p->location }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <div class="text-[11px] text-slate-500">
                        Manager: <span class="font-semibold text-slate-700">{{ $p->manager?->name ?? 'Unassigned' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        @if(auth()->user()->isAdmin() || (auth()->user()->isProjectManager() && $p->manager_id === auth()->id()))
                        <a href="{{ route('projects.edit', $p) }}" class="text-xs text-slate-600 hover:text-slate-900 px-2.5 py-1.5 rounded hover:bg-slate-100">
                            Edit
                        </a>
                        @endif
                        <a href="{{ route('projects.show', $p) }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 min-h-[36px] flex items-center">
                            Command View &rarr;
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-white rounded-xl p-8 text-center text-slate-400 border border-slate-200">
                No project sites found matching the given criteria.
            </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $projects->links() }}
        </div>
    </div>
</x-layouts.app>
