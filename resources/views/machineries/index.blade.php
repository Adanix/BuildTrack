<x-layouts.app>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Machinery & Heavy Fleet Register</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Excavators, transit mixers, tippers, rollers & mobile cranes</p>
            </div>
            @if(in_array(auth()->user()->role, ['admin', 'asset_manager']))
            <a href="{{ route('machineries.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-xs min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Add Equipment</span>
            </a>
            @endif
        </div>

        <!-- Filter bar -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
            <form method="GET" action="{{ route('machineries.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Search Fleet</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, asset code, or plate (MH-12)..." class="w-full text-xs rounded-lg border-slate-300 bg-slate-50 py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Category</label>
                    <select name="category" class="w-full text-xs rounded-lg border-slate-300 bg-slate-50 py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Operational Status</label>
                    <select name="status" class="w-full text-xs rounded-lg border-slate-300 bg-slate-50 py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                        <option value="all">All Operational States</option>
                        <option value="active_on_site" {{ $status === 'active_on_site' ? 'selected' : '' }}>Active on Site</option>
                        <option value="available_yard" {{ $status === 'available_yard' ? 'selected' : '' }}>Available in Yard</option>
                        <option value="under_maintenance" {{ $status === 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2.5 px-3 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-lg min-h-[40px]">
                        Filter
                    </button>
                    @if($search || $category || ($status && $status !== 'all'))
                    <a href="{{ route('machineries.index') }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs rounded-lg min-h-[40px] flex items-center">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Fleet Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($machineries as $m)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between hover:border-slate-300 transition">
                <div>
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-xs font-black text-amber-600 bg-amber-50 px-2 py-0.5 rounded">{{ $m->asset_code }}</span>
                            <h3 class="text-base font-bold text-slate-900 mt-1 leading-snug">
                                <a href="{{ route('machineries.show', $m) }}" class="hover:text-amber-600">
                                    {{ $m->name }}
                                </a>
                            </h3>
                            <div class="text-xs text-slate-500 mt-0.5 font-medium">{{ $m->category }}</div>
                        </div>

                        <span class="shrink-0 px-2.5 py-1 rounded-full text-[10px] font-bold
                            @if($m->operational_status === 'active_on_site') bg-emerald-100 text-emerald-800
                            @elseif($m->operational_status === 'under_maintenance') bg-rose-100 text-rose-800
                            @else bg-slate-100 text-slate-700
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $m->operational_status)) }}
                        </span>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Reg Plate:</span>
                            <span class="font-bold text-slate-900">{{ $m->registration_number }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Make / Model:</span>
                            <span class="text-slate-700 font-medium truncate max-w-[160px]">{{ $m->make_model }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Deployed Site:</span>
                            <span class="font-semibold text-slate-800 truncate max-w-[160px]">
                                {{ $m->currentProject ? $m->currentProject->code . ' (' . $m->currentProject->location . ')' : 'Equipment Yard' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Assigned Driver:</span>
                            <span class="font-semibold text-slate-800">{{ $m->assignedDriver?->name ?? 'Unassigned' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500 font-medium">{{ $m->documents->count() }} Compliance Docs</span>
                    <div class="flex items-center gap-2">
                        @if(in_array(auth()->user()->role, ['admin', 'asset_manager']))
                        <a href="{{ route('machineries.edit', $m) }}" class="text-slate-600 hover:text-slate-900 px-2 py-1 rounded hover:bg-slate-100">
                            Edit
                        </a>
                        @endif
                        <a href="{{ route('machineries.show', $m) }}" class="font-bold text-amber-600 hover:underline">
                            Inspect &rarr;
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-white rounded-xl p-8 text-center text-slate-400 border border-slate-200">
                No machinery units found matching the query.
            </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $machineries->links() }}
        </div>
    </div>
</x-layouts.app>

