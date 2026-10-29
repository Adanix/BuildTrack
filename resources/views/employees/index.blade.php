<x-layouts.app>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight">Workforce & Employee Directory</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Site engineers, supervisors, machine operators & muster roll crew</p>
            </div>
            @if(auth()->user()->isAdmin() || auth()->user()->isHrPayroll())
            <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-xs min-h-[44px] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Add Employee</span>
            </a>
            @endif
        </div>

        <!-- Filter bar -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs">
            <form method="GET" action="{{ route('employees.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Search Directory</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, code (EMP-101), designation or phone..." class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-900 focus:border-amber-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Wage Type</label>
                    <select name="wage_type" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-amber-500 focus:outline-none">
                        <option value="">All Wage Types</option>
                        <option value="monthly" {{ $wageType === 'monthly' ? 'selected' : '' }}>Monthly Salary (Staff)</option>
                        <option value="daily" {{ $wageType === 'daily' ? 'selected' : '' }}>Daily Wage (Workers)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Primary Project</label>
                    <select name="project_id" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-amber-500 focus:outline-none">
                        <option value="">All Projects</option>
                        @foreach($projects as $proj)
                        <option value="{{ $proj->id }}" {{ $projectId == $proj->id ? 'selected' : '' }}>{{ $proj->code }}: {{ Str::limit($proj->name, 18) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2.5 px-3 bg-slate-900 hover:bg-slate-800 dark:bg-amber-500 dark:hover:bg-amber-400 text-white dark:text-slate-950 font-bold text-xs rounded-lg min-h-[40px] transition-colors">
                        Filter
                    </button>
                    @if($search || $wageType || $projectId)
                    <a href="{{ route('employees.index') }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs rounded-lg min-h-[40px] flex items-center transition-colors">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Employees Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($employees as $emp)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-700 transition">
                <div>
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500">{{ $emp->employee_code }}</span>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 mt-0.5 leading-snug">
                                <a href="{{ route('employees.show', $emp) }}" class="hover:text-amber-500 transition-colors">
                                    {{ $emp->full_name }}
                                </a>
                            </h3>
                            <div class="text-xs text-slate-600 dark:text-slate-400 mt-0.5 font-medium">{{ $emp->designation }}</div>
                        </div>

                        <span class="shrink-0 px-2 py-0.5 rounded text-[10px] uppercase font-bold
                            {{ $emp->wage_type === 'monthly' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/80 dark:text-indigo-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300' }}">
                            {{ $emp->wage_type }}
                        </span>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Base Salary / Rate:</span>
                            <span class="font-bold text-slate-900 dark:text-slate-100">{{ $emp->formatted_base_salary }} {{ $emp->wage_type === 'daily' ? '/day' : '/month' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">OT Rate (Approved):</span>
                            <span class="font-bold text-purple-700 dark:text-purple-400">{{ $emp->formatted_overtime_rate }} /hr</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Assigned Site:</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200 truncate max-w-[150px]">{{ $emp->primaryProject?->code ?? 'General Pool' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs">
                    <span class="text-slate-500 dark:text-slate-400">{{ $emp->phone }}</span>
                    <div class="flex items-center gap-2">
                        @if(auth()->user()->isAdmin() || auth()->user()->isHrPayroll())
                        <a href="{{ route('employees.edit', $emp) }}" class="text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 px-2 py-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            Edit
                        </a>
                        @endif
                        <a href="{{ route('employees.show', $emp) }}" class="font-bold text-amber-600 dark:text-amber-400 hover:underline">
                            Profile &rarr;
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-white dark:bg-slate-900 rounded-xl p-8 text-center text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-800">
                No workforce members match your query.
            </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $employees->links() }}
        </div>
    </div>
</x-layouts.app>
