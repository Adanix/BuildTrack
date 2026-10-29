<x-layouts.app>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Compliance & Document Expiry Center</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Pollution Under Control (PUC), Comprehensive Insurance & Fitness Certificates (FC)</p>
            </div>

            @if(in_array(auth()->user()->role, ['admin', 'asset_manager']))
            <form action="{{ route('compliance.check-expiries') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-xs min-h-[44px] cursor-pointer transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span>Run Expiry Check Now</span>
                </button>
            </form>
            @endif
        </div>

        <!-- Compliance Status Tabs -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs p-1.5 flex overflow-x-auto gap-1 transition-colors">
            <a href="{{ route('compliance.index', ['tab' => 'all']) }}" class="flex-1 py-2 px-3 text-xs font-bold rounded-xl text-center whitespace-nowrap transition {{ $tab === 'all' ? 'bg-slate-900 dark:bg-slate-800 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                All Documents ({{ $counts['all'] }})
            </a>
            <a href="{{ route('compliance.index', ['tab' => 'expired']) }}" class="flex-1 py-2 px-3 text-xs font-bold rounded-xl text-center whitespace-nowrap transition {{ $tab === 'expired' ? 'bg-rose-600 text-white shadow-xs' : 'text-rose-700 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40' }}">
                Expired ({{ $counts['expired'] }})
            </a>
            <a href="{{ route('compliance.index', ['tab' => 'critical']) }}" class="flex-1 py-2 px-3 text-xs font-bold rounded-xl text-center whitespace-nowrap transition {{ $tab === 'critical' ? 'bg-orange-600 text-white shadow-xs' : 'text-orange-700 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-950/40' }}">
                Critical &le; 7 Days ({{ $counts['critical'] }})
            </a>
            <a href="{{ route('compliance.index', ['tab' => 'expiring_soon']) }}" class="flex-1 py-2 px-3 text-xs font-bold rounded-xl text-center whitespace-nowrap transition {{ $tab === 'expiring_soon' ? 'bg-amber-500 text-slate-950 shadow-xs' : 'text-amber-800 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40' }}">
                Due 8-30 Days ({{ $counts['expiring_soon'] }})
            </a>
            <a href="{{ route('compliance.index', ['tab' => 'flagged']) }}" class="flex-1 py-2 px-3 text-xs font-bold rounded-xl text-center whitespace-nowrap transition {{ $tab === 'flagged' ? 'bg-purple-600 text-white shadow-xs' : 'text-purple-700 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-950/40' }}">
                Flagged by Driver ({{ $counts['flagged'] }})
            </a>
            <a href="{{ route('compliance.index', ['tab' => 'valid']) }}" class="flex-1 py-2 px-3 text-xs font-bold rounded-xl text-center whitespace-nowrap transition {{ $tab === 'valid' ? 'bg-emerald-600 text-white shadow-xs' : 'text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40' }}">
                Valid ({{ $counts['valid'] }})
            </a>
        </div>

        <!-- Compliance List Table -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden transition-colors">
            <div class="p-4 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="text-xs font-bold text-slate-700 dark:text-slate-300">
                    Showing {{ $documents->count() }} Documents
                </div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400">
                    Expiry check scans documents where <code class="text-slate-800 dark:text-slate-200">expiry_date &le; today + 30 days</code>. Alerts are recorded idempotently.
                </div>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="min-w-full text-xs text-left divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-100/70 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 font-bold uppercase">
                        <tr>
                            <th class="py-3 px-3.5">Equipment & Plate</th>
                            <th class="py-3 px-3.5">Document Type</th>
                            <th class="py-3 px-3.5">Document No.</th>
                            <th class="py-3 px-3.5">Expiry Date</th>
                            <th class="py-3 px-3.5">Days Remaining</th>
                            <th class="py-3 px-3.5">Status Bucket</th>
                            <th class="py-3 px-3.5">Renewal Est.</th>
                            <th class="py-3 px-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-800 dark:text-slate-200">
                        @forelse($documents as $doc)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/50" x-data="{ flagModal: false }">
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <a href="{{ route('machineries.show', $doc->machinery) }}" class="font-bold text-slate-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-400">
                                    {{ $doc->machinery?->name }}
                                </a>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $doc->machinery?->asset_code }} &bull; {{ $doc->machinery?->registration_number }}</div>
                            </td>

                            <td class="py-3 px-3.5 whitespace-nowrap font-medium text-slate-900 dark:text-white">
                                {{ $doc->document_type }}
                            </td>

                            <td class="py-3 px-3.5 whitespace-nowrap font-mono text-[11px] text-slate-600 dark:text-slate-400">
                                {{ $doc->document_number }}
                            </td>

                            <td class="py-3 px-3.5 whitespace-nowrap font-semibold text-slate-800 dark:text-slate-200">
                                {{ $doc->expiry_date?->format('d M Y') }}
                            </td>

                            <td class="py-3 px-3.5 whitespace-nowrap font-black">
                                @if($doc->days_remaining < 0)
                                    <span class="text-rose-700 dark:text-rose-400">{{ abs($doc->days_remaining) }} days past due</span>
                                @elseif($doc->days_remaining <= 7)
                                    <span class="text-orange-700 dark:text-orange-400">{{ $doc->days_remaining }} days left</span>
                                @else
                                    <span class="text-slate-800 dark:text-slate-200">{{ $doc->days_remaining }} days</span>
                                @endif
                            </td>

                            <td class="py-3 px-3.5 whitespace-nowrap">
                                @if($doc->status_bucket === 'expired')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300">Expired</span>
                                @elseif($doc->status_bucket === 'critical')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 dark:bg-orange-950/60 text-orange-800 dark:text-orange-300">Critical (&le; 7 Days)</span>
                                @elseif($doc->status_bucket === 'expiring_soon')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">Expiring Soon (8-30d)</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300">Valid</span>
                                @endif

                                @if($doc->flagged_for_renewal)
                                    <span class="ml-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300" title="{{ $doc->flag_notes }}">
                                        Flagged
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-3.5 whitespace-nowrap font-semibold">
                                {{ $doc->formatted_renewal_cost }}
                            </td>

                            <td class="py-3 px-3.5 whitespace-nowrap text-right">
                                @if(! $doc->flagged_for_renewal)
                                <button type="button" @click="flagModal = true" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-[10px] rounded cursor-pointer min-h-[30px] transition">
                                    Flag Renewal
                                </button>
                                @else
                                <span class="text-[10px] text-purple-700 dark:text-purple-400 font-semibold" title="{{ $doc->flag_notes }}">Flagged by driver</span>
                                @endif

                                <!-- Modal for Flagging -->
                                <div x-show="flagModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 text-left">
                                    <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs" @click="flagModal = false"></div>
                                    <div class="relative bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100">
                                        <h4 class="text-base font-bold text-slate-900 dark:text-white">Flag for Renewal</h4>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                            Notify fleet management regarding {{ $doc->document_type }} for {{ $doc->machinery?->name }}.
                                        </p>
                                        <form action="{{ route('compliance.flag', $doc) }}" method="POST" class="mt-4 space-y-3">
                                            @csrf
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Renewal Note / Inspection Feedback *</label>
                                                <textarea name="flag_notes" rows="3" required placeholder="e.g. Local RTO testing center appointment booked for next Tuesday." class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs p-2.5 text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:border-amber-500 focus:outline-none"></textarea>
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
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                No machinery documents found under this filter tab.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
