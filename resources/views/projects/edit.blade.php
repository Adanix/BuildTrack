<x-layouts.app>
    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 mb-2 transition-colors">
                &larr; Back to {{ $project->code }}
            </a>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-slate-100">Edit Project Contract &mdash; {{ $project->code }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Update contract parameters, manager assignment, and dates</p>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs">
            <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400">Project Code (Immutable)</label>
                        <input type="text" value="{{ $project->code }}" disabled class="mt-1 block w-full rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs py-2.5 px-3">
                    </div>

                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Project Title / Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $project->name) }}" required class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs py-2.5 px-3 text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-amber-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="client_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Client / Authority Name *</label>
                        <input type="text" id="client_name" name="client_name" value="{{ old('client_name', $project->client_name) }}" required class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs py-2.5 px-3 text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="location" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Site Location *</label>
                        <input type="text" id="location" name="location" value="{{ old('location', $project->location) }}" required class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs py-2.5 px-3 text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-amber-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="budget_inr" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Contract Budget (INR) *</label>
                        <input type="number" step="0.01" id="budget_inr" name="budget_inr" value="{{ old('budget_inr', $project->budget_inr) }}" required class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs py-2.5 px-3 text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="manager_id" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Project Manager</label>
                        <select id="manager_id" name="manager_id" class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs py-2.5 px-3 text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-amber-500 focus:outline-none">
                            <option value="">-- Select Manager --</option>
                            @foreach($projectManagers as $pm)
                            <option value="{{ $pm->id }}" {{ old('manager_id', $project->manager_id) == $pm->id ? 'selected' : '' }}>
                                {{ $pm->name }} ({{ $pm->role_display_name }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="start_date" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Start Date *</label>
                        <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs py-2.5 px-3 text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="target_end_date" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Target Completion Date *</label>
                        <input type="date" id="target_end_date" name="target_end_date" value="{{ old('target_end_date', $project->target_end_date?->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs py-2.5 px-3 text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Status *</label>
                        <select id="status" name="status" required class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs py-2.5 px-3 text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-amber-500 focus:outline-none">
                            <option value="planned" {{ old('status', $project->status) === 'planned' ? 'selected' : '' }}>Planned</option>
                            <option value="active" {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="on_hold" {{ old('status', $project->status) === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="completed" {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                </div>

                @if(auth()->user()->isAdmin())
                <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/60">
                    <label for="overall_progress" class="block text-xs font-bold text-amber-900 dark:text-amber-200">Administrator Manual Progress Override (%)</label>
                    <div class="flex items-center gap-3 mt-1">
                        <input type="number" min="0" max="100" id="overall_progress" name="overall_progress" value="{{ old('overall_progress', $project->overall_progress) }}" class="w-24 text-xs font-bold rounded-lg border border-amber-300 dark:border-amber-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 py-2 px-3 focus:outline-none focus:border-amber-500">
                        <span class="text-xs text-amber-800 dark:text-amber-300">
                            Current progress is {{ $project->overall_progress }}%. Manual edits are logged in audit logs.
                        </span>
                    </div>
                </div>
                @endif

                <div>
                    <label for="description" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Scope of Work & Notes</label>
                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs py-2.5 px-3 text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-amber-500 focus:outline-none">{{ old('description', $project->description) }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    @if(auth()->user()->isAdmin())
                    <button type="button" onclick="if(confirm('Are you sure you want to remove this project? It will be blocked if historical site records exist.')) document.getElementById('delete-project-form').submit();" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-300 cursor-pointer">
                        Delete Project
                    </button>
                    @else
                    <div></div>
                    @endif

                    <div class="flex items-center gap-3">
                        <a href="{{ route('projects.show', $project) }}" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-xs min-h-[44px] transition-colors">
                            Update Project Details
                        </button>
                    </div>
                </div>
            </form>

            @if(auth()->user()->isAdmin())
            <form id="delete-project-form" action="{{ route('projects.destroy', $project) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
            @endif
        </div>
    </div>
</x-layouts.app>
