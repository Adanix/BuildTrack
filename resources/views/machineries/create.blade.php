<x-layouts.app>
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <a href="{{ route('machineries.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-slate-800 mb-2">
                &larr; Back to Fleet
            </a>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900">Add Equipment to Fleet</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Register backhoe loader, excavator, transit mixer or tipper</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <form action="{{ route('machineries.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="asset_code" class="block text-xs font-semibold text-slate-700">Asset Code *</label>
                        <input type="text" id="asset_code" name="asset_code" value="{{ old('asset_code') }}" required placeholder="e.g. MCH-EX-07" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700">Equipment Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. Sany SY215C Hydraulic Excavator" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="block text-xs font-semibold text-slate-700">Category *</label>
                        <select id="category" name="category" required class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                            @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="registration_number" class="block text-xs font-semibold text-slate-700">Registration Plate *</label>
                        <input type="text" id="registration_number" name="registration_number" value="{{ old('registration_number') }}" required placeholder="e.g. MH-12-AB-1234" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="make_model" class="block text-xs font-semibold text-slate-700">Make / Model & Year *</label>
                        <input type="text" id="make_model" name="make_model" value="{{ old('make_model') }}" required placeholder="e.g. Sany India SY215C (2024)" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="operational_status" class="block text-xs font-semibold text-slate-700">Operational Status *</label>
                        <select id="operational_status" name="operational_status" required class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                            <option value="active_on_site">Active on Site</option>
                            <option value="available_yard" selected>Available in Yard</option>
                            <option value="under_maintenance">Under Maintenance</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="current_project_id" class="block text-xs font-semibold text-slate-700">Deployed Project Site</label>
                        <select id="current_project_id" name="current_project_id" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                            <option value="">-- Central Yard / Unassigned --</option>
                            @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ old('current_project_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->code }}: {{ $p->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="assigned_driver_id" class="block text-xs font-semibold text-slate-700">Assigned Driver / Operator</label>
                        <select id="assigned_driver_id" name="assigned_driver_id" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                            <option value="">-- Unassigned --</option>
                            @foreach($drivers as $d)
                            <option value="{{ $d->id }}" {{ old('assigned_driver_id') == $d->id ? 'selected' : '' }}>
                                {{ $d->name }} ({{ $d->phone }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-semibold text-slate-700">Equipment Specifications & Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none" placeholder="Bucket capacity, hydraulic lines, attachment details...">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('machineries.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-xs min-h-[44px]">
                        Save Equipment
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
