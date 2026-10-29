<x-layouts.app>
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <a href="{{ route('daily-reports.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-slate-800 mb-2">
                &larr; Back to Site Reports
            </a>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500 text-slate-950 uppercase tracking-wider">
                    Mobile Fast Logger
                </span>
                <span class="text-xs text-slate-500">&lt; 15 Seconds Submission</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">Daily Site Progress Log</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Record concrete pours, earthwork quantities, labor headcount, and progress</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs">
            <form action="{{ route('daily-reports.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Row 1: Project & Date (Pre-selected) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="project_id" class="block text-xs font-bold text-slate-700">Project Site *</label>
                        <select id="project_id" name="project_id" required class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-3 px-3 font-semibold text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none min-h-12">
                            @foreach($projects as $proj)
                            <option value="{{ $proj->id }}" {{ (old('project_id', request('project_id')) == $proj->id) || (!request('project_id') && $defaultProject?->id == $proj->id) ? 'selected' : '' }}>
                                {{ $proj->code }}: {{ $proj->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="report_date" class="block text-xs font-bold text-slate-700">Report Date *</label>
                        <input type="date" id="report_date" name="report_date" value="{{ old('report_date', $todayDate) }}" required class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-3 px-3 font-semibold text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none min-h-12">
                    </div>
                </div>

                <!-- Row 2: Work Category -->
                <div>
                    <label for="work_category" class="block text-xs font-bold text-slate-700">Civil Works Category *</label>
                    <select id="work_category" name="work_category" required class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-3 px-3 font-semibold text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none min-h-12">
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('work_category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Row 3: Quantity, Unit, Cumulative % -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label for="quantity_completed" class="block text-xs font-bold text-slate-700">Quantity Done</label>
                        <input type="number" step="0.01" min="0" id="quantity_completed" name="quantity_completed" value="{{ old('quantity_completed') }}" placeholder="e.g. 48.50" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-3 px-3 font-semibold text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none min-h-12">
                    </div>

                    <div>
                        <label for="quantity_unit" class="block text-xs font-bold text-slate-700">Unit</label>
                        <select id="quantity_unit" name="quantity_unit" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-3 px-3 font-semibold text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none min-h-12">
                            @foreach($units as $u)
                            <option value="{{ $u }}" {{ old('quantity_unit') == $u ? 'selected' : '' }}>{{ $u }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="reported_progress_percent" class="block text-xs font-bold text-slate-700">Cumulative Progress % *</label>
                        <input type="number" min="0" max="100" id="reported_progress_percent" name="reported_progress_percent" value="{{ old('reported_progress_percent') }}" required placeholder="0 - 100" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-3 px-3 font-black text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none min-h-12">
                    </div>
                </div>

                <!-- Row 4: Labor Count & Weather -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="labor_count" class="block text-xs font-bold text-slate-700">Site Labor Count *</label>
                        <input type="number" min="0" id="labor_count" name="labor_count" value="{{ old('labor_count', 20) }}" required class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-3 px-3 font-semibold text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none min-h-12">
                    </div>

                    <div>
                        <label for="weather" class="block text-xs font-bold text-slate-700">Weather Condition *</label>
                        <select id="weather" name="weather" required class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-3 px-3 font-semibold text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none min-h-12">
                            @foreach($weatherOptions as $w)
                            <option value="{{ $w }}" {{ old('weather', 'Clear / Sunny') == $w ? 'selected' : '' }}>{{ $w }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Row 5: Activity Summary -->
                <div>
                    <label for="activity_summary" class="block text-xs font-bold text-slate-700">Activity Summary & Structural Element *</label>
                    <textarea id="activity_summary" name="activity_summary" rows="3" required placeholder="e.g. Pier Cap P-14 M45 concrete pour completed using boom pump with two transit mixers in continuous cycle." class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-3 px-3 text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none">{{ old('activity_summary') }}</textarea>
                </div>

                <!-- Row 6: Delays / Issues (Optional) -->
                <div>
                    <label for="issues_or_delays" class="block text-xs font-bold text-slate-700">Delays, Weather Stoppages or Safety Issues (Optional)</label>
                    <input type="text" id="issues_or_delays" name="issues_or_delays" value="{{ old('issues_or_delays') }}" placeholder="e.g. Heavy monsoon rain halted pour for 45 minutes; slump test re-checked." class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-3 px-3 text-slate-900 focus:bg-white focus:border-amber-500 focus:outline-none min-h-12">
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('daily-reports.index') }}" class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800">
                        Cancel
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-sm rounded-xl shadow-sm min-h-12 cursor-pointer flex items-center justify-center gap-2">
                        <span>Submit Site Report &rarr;</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
