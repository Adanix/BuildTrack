<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects Portfolio Summary - BuildTrack India</title>
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
            <h1 class="text-xl font-black text-slate-900 uppercase">BuildTrack India &bull; Infrastructure Projects Portfolio</h1>
            <p class="text-xs text-slate-500 mt-1">Generated on {{ now('Asia/Kolkata')->format('d M Y, H:i') }} IST &bull; Total Contracts: {{ $projects->count() }}</p>
        </div>

        <table class="min-w-full text-xs text-left divide-y divide-slate-200">
            <thead class="bg-slate-50 font-bold uppercase text-slate-600">
                <tr>
                    <th class="py-2.5 px-3">Code</th>
                    <th class="py-2.5 px-3">Project Title</th>
                    <th class="py-2.5 px-3">Client</th>
                    <th class="py-2.5 px-3">Location</th>
                    <th class="py-2.5 px-3">Manager</th>
                    <th class="py-2.5 px-3">Budget (INR)</th>
                    <th class="py-2.5 px-3">Progress</th>
                    <th class="py-2.5 px-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @foreach($projects as $p)
                <tr>
                    <td class="py-2.5 px-3 font-bold text-slate-900">{{ $p->code }}</td>
                    <td class="py-2.5 px-3 font-semibold">{{ $p->name }}</td>
                    <td class="py-2.5 px-3">{{ $p->client_name }}</td>
                    <td class="py-2.5 px-3">{{ $p->location }}</td>
                    <td class="py-2.5 px-3">{{ $p->manager?->name ?? 'Unassigned' }}</td>
                    <td class="py-2.5 px-3 font-bold">{{ $p->formatted_budget }}</td>
                    <td class="py-2.5 px-3 font-black">{{ $p->overall_progress }}%</td>
                    <td class="py-2.5 px-3 capitalize font-semibold">{{ $p->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
