<x-layouts.app>
    <div class="space-y-6">

        <!-- Global Slice Filter Bar -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-xs border border-slate-200 dark:border-slate-800 p-4 transition-colors">
            <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div>
                    <label for="filter_project" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Project Site</label>
                    <select id="filter_project" name="project_id" class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 py-2 px-2.5 focus:border-amber-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none">
                        <option value="">All Projects (Portfolio)</option>
                        @foreach($allProjects as $proj)
                        <option value="{{ $proj->id }}" {{ $projectId == $proj->id ? 'selected' : '' }}>
                            {{ $proj->code }} - {{ Str::limit($proj->name, 26) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filter_location" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Location</label>
                    <input type="text" id="filter_location" name="location" value="{{ $location }}" placeholder="e.g. Pune, Navi Mumbai" class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 py-2 px-2.5 focus:border-amber-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none">
                </div>

                <div>
                    <label for="filter_date_from" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Date From</label>
                    <input type="date" id="filter_date_from" name="date_from" value="{{ $dateFrom }}" class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 py-2 px-2.5 focus:border-amber-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none">
                </div>

                <div>
                    <label for="filter_date_to" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Date To</label>
                    <input type="date" id="filter_date_to" name="date_to" value="{{ $dateTo }}" class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 py-2 px-2.5 focus:border-amber-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none">
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-lg shadow-xs cursor-pointer min-h-[36px] transition">
                        Apply Slicer
                    </button>
                    @if($projectId || $location || $dateFrom || $dateTo)
                    <a href="{{ route('dashboard') }}" class="py-2 px-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs rounded-lg min-h-[36px] flex items-center justify-center transition" title="Reset Filters">
                        Clear
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Role Adaptive Headline Banner -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 dark:from-slate-900 dark:to-slate-950 rounded-2xl text-white p-5 sm:p-6 shadow-sm border border-slate-700 dark:border-slate-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500 text-slate-950">
                            {{ $user->role_display_name }}
                        </span>
                        <span class="text-xs text-slate-400">Civil Works Command Hub</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-white mt-1.5">Welcome, {{ $user->name }}</h2>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl">
                        @if($user->isAdmin())
                        Executive portfolio control: 4 active infrastructure contracts, workforce wage disburals, and heavy equipment readiness.
                        @elseif($user->isProjectManager())
                        Site operations queue: Review daily concrete pour logs, inspect quality notes, and approve crew overtime hours.
                        @elseif($user->isForeman())
                        Field operations view: Log daily concrete, earthwork and reinforcement quantities in under 15 seconds.
                        @elseif($user->isHrPayroll())
                        Workforce and compensation: Review daily crew muster rolls, approve overtime, and process INR payroll batches.
                        @elseif($user->isAssetManager())
                        Equipment and compliance monitoring: Track heavy machineries, inspection cycles, and RTO certificate renewals.
                        @elseif($user->isDriver())
                        Machine operator cockpit: Inspect assigned machinery health and report document renewal requirements.
                        @endif
                    </p>
                </div>

                <!-- Field Quick Action Buttons -->
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    @if($user->isAdmin() || $user->isProjectManager() || $user->isForeman())
                    <a href="{{ route('daily-reports.create') }}" class="inline-flex items-center gap-1.5 px-4 py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-sm min-h-12 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Log Daily Site Report</span>
                    </a>
                    @endif

                    @if(! $user->isDriver())
                    <a href="{{ route('attendance.bulk') }}" class="inline-flex items-center gap-1.5 px-4 py-3 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-xl shadow-sm min-h-12 border border-slate-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        <span>Mark Crew Attendance</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Role Adaptive KPI Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1 -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between transition-colors">
                <div>
                    <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Active Projects</div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $activeProjectsCount }}</div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $projectsList->count() }} total contracts in portfolio</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>

            <!-- Card 2: Reports Awaiting Review -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between transition-colors">
                <div>
                    <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Reports In Review</div>
                    <div class="text-2xl font-black {{ $pendingReportsCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-900 dark:text-white' }} mt-1">
                        {{ $pendingReportsCount }}
                    </div>
                    <a href="{{ route('daily-reports.index', ['status' => 'review_queue']) }}" class="text-[11px] text-amber-600 dark:text-amber-400 hover:underline mt-0.5 inline-block font-medium">
                        Open review queue &rarr;
                    </a>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
            </div>

            <!-- Card 3: Pending Overtime -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between transition-colors">
                <div>
                    <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pending OT Hours</div>
                    <div class="text-2xl font-black {{ $pendingOtCount > 0 ? 'text-purple-600 dark:text-purple-400' : 'text-slate-900 dark:text-white' }} mt-1">
                        {{ $pendingOtCount }}
                    </div>
                    <a href="{{ route('attendance.index', ['tab' => 'overtime_queue']) }}" class="text-[11px] text-purple-600 dark:text-purple-400 hover:underline mt-0.5 inline-block font-medium">
                        Review overtime requests &rarr;
                    </a>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- Card 4: Urgent Compliance -->
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between transition-colors">
                <div>
                    <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Compliance Urgency</div>
                    <div class="text-2xl font-black {{ ($expiredDocsCount + $criticalDocsCount) > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white' }} mt-1">
                        {{ $expiredDocsCount + $criticalDocsCount }}
                    </div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        <span class="text-rose-600 dark:text-rose-400 font-semibold">{{ $expiredDocsCount }} Expired</span> &bull; {{ $criticalDocsCount }} Critical (&le;7d)
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </div>

        @if($payrollStats && ($user->isAdmin() || $user->isHrPayroll()))
        <!-- Payroll Quick Glance Panel (Admin & HR) -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs transition-colors">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 gap-2">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Current Month Payroll Glance (INR) &mdash; {{ $periodMonth }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Operational Wage & Overtime Calculator across civil workforce</p>
                </div>
                <a href="{{ route('payroll.index', ['period_month' => $periodMonth]) }}" class="text-xs text-amber-600 dark:text-amber-400 font-semibold hover:underline">
                    Manage Full Payroll Sheet &rarr;
                </a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                <div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">Gross Base Earned</div>
                    <div class="text-lg font-bold text-slate-900 dark:text-white mt-0.5">INR {{ number_format((float) $payrollStats['total_base'], 2) }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">Approved OT Pay</div>
                    <div class="text-lg font-bold text-purple-700 dark:text-purple-400 mt-0.5">INR {{ number_format((float) $payrollStats['total_ot'], 2) }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">Net Salary Payable</div>
                    <div class="text-lg font-extrabold text-emerald-700 dark:text-emerald-400 mt-0.5">INR {{ number_format((float) $payrollStats['total_net'], 2) }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">Status Distribution</div>
                    <div class="text-xs font-semibold mt-1">
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">{{ $payrollStats['draft_count'] }} Draft</span>
                        <span class="px-1.5 py-0.5 rounded bg-sky-100 dark:bg-sky-950/60 text-sky-800 dark:text-sky-300">{{ $payrollStats['approved_count'] }} Approved</span>
                        <span class="px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300">{{ $payrollStats['paid_count'] }} Paid</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Two Column Layout: Projects Progress vs Target & Urgent Machinery -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Left: Project Progress vs Target -->
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-xs border border-slate-200 dark:border-slate-800 p-5 flex flex-col justify-between transition-colors">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Project Progress & Timeline</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Synchronized deterministically with approved site reports</p>
                        </div>
                        <a href="{{ route('projects.index') }}" class="text-xs text-amber-600 dark:text-amber-400 font-semibold hover:underline">
                            View All ({{ $projectsList->count() }}) &rarr;
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800 mt-3">
                        @foreach($projectsList->take(4) as $proj)
                        <div class="py-3">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <a href="{{ route('projects.show', $proj) }}" class="font-bold text-slate-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-400 truncate max-w-[240px]">
                                    {{ $proj->code }}: {{ $proj->name }}
                                </a>
                                <span class="font-black text-slate-800 dark:text-slate-200">{{ $proj->overall_progress }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-amber-500 h-2.5 rounded-full transition-all duration-300" style="width: {{ $proj->overall_progress }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                <span>{{ $proj->location }}</span>
                                <span>Budget: {{ $proj->formatted_budget }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400">
                    <span class="font-semibold text-slate-700 dark:text-slate-300">Audit Rule:</span> Project progress cannot automatically decrease from older site reports.
                </div>
            </div>

            <!-- Right: Urgent Machinery Compliance -->
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-xs border border-slate-200 dark:border-slate-800 p-5 flex flex-col justify-between transition-colors">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Urgent Compliance & Expiry Alerts</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Pollution Under Control (PUC), Motor Insurance & Fitness (FC)</p>
                        </div>
                        <a href="{{ route('compliance.index') }}" class="text-xs text-amber-600 dark:text-amber-400 font-semibold hover:underline">
                            Open Center &rarr;
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800 mt-2">
                        @forelse($urgentDocuments as $doc)
                        <div class="py-2.5 flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <div class="font-bold text-xs text-slate-900 dark:text-white truncate">
                                    {{ $doc->machinery?->name ?? 'Equipment' }}
                                    <span class="text-slate-500 dark:text-slate-400 font-normal">({{ $doc->machinery?->registration_number }})</span>
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                    {{ $doc->document_type }} &bull; #{{ $doc->document_number }}
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                @if($doc->status_bucket === 'expired')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300">
                                    Expired ({{ abs($doc->days_remaining) }}d ago)
                                </span>
                                @elseif($doc->status_bucket === 'critical')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 dark:bg-orange-950/60 text-orange-800 dark:text-orange-300">
                                    Critical ({{ $doc->days_remaining }}d left)
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">
                                    Due in {{ $doc->days_remaining }}d
                                </span>
                                @endif
                                <div class="text-[10px] text-slate-400 mt-0.5">Exp: {{ $doc->expiry_date?->format('d M Y') }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="py-6 text-center text-xs text-slate-400">
                            No critical or expired documents currently recorded.
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-slate-500 dark:text-slate-400">Daily reminder engine active.</span>
                    @if($user->isAdmin() || $user->isAssetManager())
                    <form action="{{ route('compliance.check-expiries') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="text-amber-600 dark:text-amber-400 font-semibold hover:underline cursor-pointer">
                            Run Expiry Check Now &rarr;
                        </button>
                    </form>
                    @endif
                </div>
            </div>

        </div>

        <!-- Recent Site Reports Table -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-xs border border-slate-200 dark:border-slate-800 p-5 transition-colors">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Recent Daily Site Reports</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Concrete pours, earthwork and reinforcement quantities</p>
                </div>
                <a href="{{ route('daily-reports.index') }}" class="text-xs text-amber-600 dark:text-amber-400 font-semibold hover:underline">
                    View All Reports &rarr;
                </a>
            </div>

            <div class="overflow-x-auto w-full mt-3">
                <table class="min-w-full text-xs text-left divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/70 text-slate-600 dark:text-slate-300 uppercase font-semibold">
                        <tr>
                            <th class="py-2.5 px-3">Date</th>
                            <th class="py-2.5 px-3">Project Site</th>
                            <th class="py-2.5 px-3">Category</th>
                            <th class="py-2.5 px-3">Qty Done</th>
                            <th class="py-2.5 px-3">Reported Progress</th>
                            <th class="py-2.5 px-3">Weather</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        @forelse($recentReports as $rep)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50">
                            <td class="py-2.5 px-3 whitespace-nowrap font-medium text-slate-900 dark:text-white">
                                {{ $rep->report_date?->format('d M Y') }}
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $rep->project?->code }}</span>
                                <span class="text-slate-500 dark:text-slate-400">({{ Str::limit($rep->project?->name, 22) }})</span>
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap">{{ $rep->work_category }}</td>
                            <td class="py-2.5 px-3 whitespace-nowrap font-semibold">
                                {{ $rep->quantity_completed ? number_format((float) $rep->quantity_completed, 2) . ' ' . $rep->quantity_unit : '-' }}
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                <span class="font-bold text-slate-900 dark:text-white">{{ $rep->reported_progress_percent }}%</span>
                            </td>
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
                                <a href="{{ route('daily-reports.show', $rep) }}" class="text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 font-bold hover:underline">
                                    Details &rarr;
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-slate-400">
                                No site reports found for the selected slice.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layouts.app>
