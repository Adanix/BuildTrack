<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly INR Payroll Statement - BuildTrack India</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-6 font-sans">
    <div class="no-print max-w-5xl mx-auto mb-4 flex items-center justify-between">
        <a href="{{ route('exports.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Back to Exports Hub</a>
        <button onclick="window.print()" class="px-4 py-2 bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs cursor-pointer">
            Print / Save as PDF
        </button>
    </div>

    <div class="max-w-5xl mx-auto bg-white rounded-2xl border border-slate-200 shadow-md p-8">
        <div class="border-b-2 border-slate-900 pb-4 mb-6">
            <h1 class="text-xl font-black text-slate-900 uppercase">BuildTrack India &bull; Monthly INR Payroll Statement ({{ $periodMonth }})</h1>
            <p class="text-xs text-slate-500 mt-1">Generated on {{ now('Asia/Kolkata')->format('d M Y, H:i') }} IST &bull; Total Workforce: {{ $records->count() }}</p>
        </div>

        <table class="min-w-full text-xs text-left divide-y divide-slate-200">
            <thead class="bg-slate-50 font-bold uppercase text-slate-600">
                <tr>
                    <th class="py-2.5 px-3">Code</th>
                    <th class="py-2.5 px-3">Employee Name</th>
                    <th class="py-2.5 px-3">Wage</th>
                    <th class="py-2.5 px-3">Days Paid</th>
                    <th class="py-2.5 px-3">OT Hrs</th>
                    <th class="py-2.5 px-3">Base (INR)</th>
                    <th class="py-2.5 px-3">OT Pay (INR)</th>
                    <th class="py-2.5 px-3">Net Salary (INR)</th>
                    <th class="py-2.5 px-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @foreach($records as $r)
                <tr>
                    <td class="py-2.5 px-3">{{ $r->employee?->employee_code }}</td>
                    <td class="py-2.5 px-3 font-bold text-slate-900">{{ $r->employee?->full_name }}</td>
                    <td class="py-2.5 px-3 capitalize">{{ $r->employee?->wage_type }}</td>
                    <td class="py-2.5 px-3 font-semibold">{{ $r->days_present }}</td>
                    <td class="py-2.5 px-3 font-semibold">{{ $r->total_overtime_hours }}</td>
                    <td class="py-2.5 px-3">{{ number_format((float) $r->base_earned_inr, 2) }}</td>
                    <td class="py-2.5 px-3 text-purple-700 font-semibold">{{ number_format((float) $r->overtime_pay_inr, 2) }}</td>
                    <td class="py-2.5 px-3 font-black text-emerald-800">INR {{ number_format((float) $r->net_salary_inr, 2) }}</td>
                    <td class="py-2.5 px-3 capitalize font-bold">{{ $r->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
