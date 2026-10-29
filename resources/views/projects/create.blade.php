<x-layouts.app>
    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-slate-800 mb-2">
                &larr; Back to Projects
            </a>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900">Register New Project Contract</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Define project code, client information, contract budget (INR), and schedule</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <form action="{{ route('projects.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="code" class="block text-xs font-semibold text-slate-700">Project Code *</label>
                        <input type="text" id="code" name="code" value="{{ old('code') }}" required placeholder="e.g. CW-PUN-05" class="mt-1 block w-full rounded-lg border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700">Project Title / Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. Western Bypass Elevated Corridor" class="mt-1 block w-full rounded-lg border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="client_name" class="block text-xs font-semibold text-slate-700">Client / Authority Name *</label>
                        <input type="text" id="client_name" name="client_name" value="{{ old('client_name') }}" required placeholder="e.g. NHAI / CIDCO / MMRDA" class="mt-1 block w-full rounded-lg border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="location" class="block text-xs font-semibold text-slate-700">Site Location *</label>
                        <input type="text" id="location" name="location" value="{{ old('location') }}" required placeholder="e.g. Pune, Maharashtra" class="mt-1 block w-full rounded-lg border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="budget_inr" class="block text-xs font-semibold text-slate-700">Contract Budget (INR) *</label>
                        <input type="number" step="0.01" id="budget_inr" name="budget_inr" value="{{ old('budget_inr') }}" required placeholder="e.g. 35000000.00" class="mt-1 block w-full rounded-lg border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="manager_id" class="block text-xs font-semibold text-slate-700">Project Manager</label>
                        <select id="manager_id" name="manager_id" class="mt-1 block w-full rounded-lg border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                            <option value="">-- Select Manager --</option>
                            @foreach($projectManagers as $pm)
                            <option value="{{ $pm->id }}" {{ old('manager_id') == $pm->id ? 'selected' : '' }}>
                                {{ $pm->name }} ({{ $pm->role_display_name }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="start_date" class="block text-xs font-semibold text-slate-700">Start Date *</label>
                        <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required class="mt-1 block w-full rounded-lg border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="target_end_date" class="block text-xs font-semibold text-slate-700">Target Completion Date *</label>
                        <input type="date" id="target_end_date" name="target_end_date" value="{{ old('target_end_date') }}" required class="mt-1 block w-full rounded-lg border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-700">Initial Status *</label>
                        <select id="status" name="status" required class="mt-1 block w-full rounded-lg border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                            <option value="planned">Planned</option>
                            <option value="active" selected>Active</option>
                            <option value="on_hold">On Hold</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold text-slate-700">Scope of Work & Notes</label>
                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-lg border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none" placeholder="Provide description of structural elements, concrete grade requirements, etc.">{{ old('description') }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('projects.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-xs min-h-[44px]">
                        Save Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
