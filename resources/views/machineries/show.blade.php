<x-layouts.app>
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <a href="{{ route('machineries.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white mb-2 transition">
                &larr; Back to Machinery Fleet
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-xs font-black bg-slate-800 dark:bg-slate-700 text-amber-400">{{ $machinery->asset_code }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold
                            @if($machinery->operational_status === 'active_on_site') bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800
                            @elseif($machinery->operational_status === 'under_maintenance') bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800
                            @else bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $machinery->operational_status)) }}
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $machinery->name }}</h1>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $machinery->category }} &bull; Plate: <span class="font-bold text-slate-800 dark:text-slate-200">{{ $machinery->registration_number }}</span></p>
                </div>

                @if(in_array(auth()->user()->role, ['admin', 'asset_manager']))
                <a href="{{ route('machineries.edit', $machinery) }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold text-xs rounded-xl min-h-[40px] flex items-center transition">
                    Edit Equipment Details
                </a>
                @endif
            </div>
        </div>

        <!-- Specifications & Deployment Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs transition-colors">
                <div class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase">Make & Model</div>
                <div class="text-base font-bold text-slate-900 dark:text-white mt-1">{{ $machinery->make_model }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-2">Registration: <span class="font-bold text-slate-800 dark:text-slate-200">{{ $machinery->registration_number }}</span></div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs transition-colors">
                <div class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase">Current Site Deployment</div>
                <div class="text-base font-bold text-slate-900 dark:text-white mt-1">
                    {{ $machinery->currentProject ? "{$machinery->currentProject->code}: {$machinery->currentProject->name}" : 'Stored in Central Yard' }}
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-2">Location: {{ $machinery->currentProject?->location ?? 'Headquarters Equipment Yard' }}</div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs transition-colors">
                <div class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase">Assigned Operator / Driver</div>
                <div class="text-base font-bold text-slate-900 dark:text-white mt-1">{{ $machinery->assignedDriver?->name ?? 'Unassigned' }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-2">{{ $machinery->assignedDriver?->phone ?? 'Contact fleet manager' }}</div>
            </div>
        </div>

        <!-- Compliance Documents Table -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs transition-colors">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Mandatory RTO Compliance & Insurance Documents</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pollution Under Control (PUC), Motor Insurance & Fitness Certificate (FC)</p>
                </div>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($machinery->documents as $doc)
                <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3" x-data="{ flagModal: false }">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $doc->document_type }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">#{{ $doc->document_number }}</span>
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Issued: {{ $doc->issue_date?->format('d M Y') }} &bull; Expiry: <strong class="text-slate-800 dark:text-slate-200">{{ $doc->expiry_date?->format('d M Y') }}</strong>
                            &bull; Renewal Est: {{ $doc->formatted_renewal_cost }}
                        </div>
                        @if($doc->flagged_for_renewal)
                        <div class="text-xs text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 rounded-lg p-2 mt-2 border border-amber-200 dark:border-amber-800">
                            <strong>Flagged for Renewal</strong> by {{ $doc->flagger?->name ?? 'Operator' }}: &ldquo;{{ $doc->flag_notes }}&rdquo;
                        </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        @if($doc->status_bucket === 'expired')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300">
                            Expired ({{ abs($doc->days_remaining) }}d ago)
                        </span>
                        @elseif($doc->status_bucket === 'critical')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 dark:bg-orange-950/60 text-orange-800 dark:text-orange-300">
                            Critical (&le; 7 Days)
                        </span>
                        @elseif($doc->status_bucket === 'expiring_soon')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">
                            Expiring Soon ({{ $doc->days_remaining }}d)
                        </span>
                        @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300">
                            Valid ({{ $doc->days_remaining }}d)
                        </span>
                        @endif

                        @if(! $doc->flagged_for_renewal)
                        <button type="button" @click="flagModal = true" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-lg cursor-pointer transition">
                            Flag Renewal
                        </button>
                        @endif
                    </div>

                    <!-- Modal for Flagging -->
                    <div x-show="flagModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs" @click="flagModal = false"></div>
                        <div class="relative bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white">
                            <h4 class="text-base font-bold text-slate-900 dark:text-white">Flag Document for Renewal</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Flag {{ $doc->document_type }} for {{ $machinery->name }} to alert the Fleet & Asset Manager.
                            </p>
                            <form action="{{ route('compliance.flag', $doc) }}" method="POST" class="mt-4 space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Renewal Notes / RTO Issue *</label>
                                    <textarea name="flag_notes" rows="3" required placeholder="e.g. Emission check due next week; vehicle stationed in Pune for test." class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs p-2.5 text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:border-amber-500 focus:outline-none"></textarea>
                                </div>
                                <div class="flex items-center justify-end gap-2 pt-2">
                                    <button type="button" @click="flagModal = false" class="px-3 py-1.5 text-xs text-slate-600 dark:text-slate-400">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl transition">
                                        Submit Renewal Flag
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-6 text-center text-slate-400 text-xs">
                    No compliance documents attached to this machine.
                </div>
                @endforelse
            </div>
        </div>

        @if($machinery->notes)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs transition-colors">
            <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Fleet Maintenance & Service Notes</h3>
            <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">{{ $machinery->notes }}</p>
        </div>
        @endif
    </div>
</x-layouts.app>
