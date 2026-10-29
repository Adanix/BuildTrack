<x-layouts.app>
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <a href="{{ route('employees.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-slate-800 mb-2">
                &larr; Back to Employees
            </a>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900">Add Workforce Member</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Register staff or daily muster-roll worker with INR compensation structure</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <form action="{{ route('employees.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="employee_code" class="block text-xs font-semibold text-slate-700">Employee Code *</label>
                        <input type="text" id="employee_code" name="employee_code" value="{{ old('employee_code') }}" required placeholder="e.g. EMP-207" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="full_name" class="block text-xs font-semibold text-slate-700">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required placeholder="e.g. Rahul Shinde" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="designation" class="block text-xs font-semibold text-slate-700">Designation / Trade *</label>
                        <input type="text" id="designation" name="designation" value="{{ old('designation') }}" required placeholder="e.g. Senior Mason / Bar Bender" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-semibold text-slate-700">Mobile Phone *</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="e.g. +91 98200 12345" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="wage_type" class="block text-xs font-semibold text-slate-700">Wage Structure *</label>
                        <select id="wage_type" name="wage_type" required class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                            <option value="daily" selected>Daily Wage Worker (INR / day)</option>
                            <option value="monthly">Monthly Staff (26-day civil norm)</option>
                        </select>
                    </div>

                    <div>
                        <label for="primary_project_id" class="block text-xs font-semibold text-slate-700">Primary Project Site</label>
                        <select id="primary_project_id" name="primary_project_id" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                            <option value="">-- General Pool / Floating --</option>
                            @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ old('primary_project_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->code }}: {{ $p->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="base_salary_inr" class="block text-xs font-semibold text-slate-700">Base Salary / Daily Rate (INR) *</label>
                        <input type="number" step="0.01" id="base_salary_inr" name="base_salary_inr" value="{{ old('base_salary_inr') }}" required placeholder="e.g. 950.00 (daily) or 35000.00 (monthly)" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="overtime_rate_per_hour_inr" class="block text-xs font-semibold text-slate-700">Overtime Rate per Hour (INR) *</label>
                        <input type="number" step="0.01" id="overtime_rate_per_hour_inr" name="overtime_rate_per_hour_inr" value="{{ old('overtime_rate_per_hour_inr') }}" required placeholder="e.g. 150.00" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="joining_date" class="block text-xs font-semibold text-slate-700">Joining Date *</label>
                        <input type="date" id="joining_date" name="joining_date" value="{{ old('joining_date', date('Y-m-d')) }}" required class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-700">Status *</label>
                        <select id="status" name="status" required class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label for="user_id" class="block text-xs font-semibold text-slate-700">Link System User Account</label>
                        <select id="user_id" name="user_id" class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 text-xs py-2.5 px-3 focus:bg-white focus:border-amber-500 focus:outline-none">
                            <option value="">-- None (Pure Field Worker) --</option>
                            @foreach($availableUsers as $u)
                            <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('employees.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-xs min-h-[44px]">
                        Save Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
