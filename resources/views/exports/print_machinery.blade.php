<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Heavy Fleet & Compliance Audit - BuildTrack India</title>
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
            <h1 class="text-xl font-black text-slate-900 uppercase">BuildTrack India &bull; Heavy Fleet Register & RTO Compliance Audit</h1>
            <p class="text-xs text-slate-500 mt-1">Generated on {{ now('Asia/Kolkata')->format('d M Y, H:i') }} IST &bull; Total Units: {{ $machineries->count() }}</p>
        </div>

        <table class="min-w-full text-xs text-left divide-y divide-slate-200">
            <thead class="bg-slate-50 font-bold uppercase text-slate-600">
                <tr>
                    <th class="py-2.5 px-3">Asset Code</th>
                    <th class="py-2.5 px-3">Name & Plate</th>
                    <th class="py-2.5 px-3">Category</th>
                    <th class="py-2.5 px-3">Deployed Site</th>
                    <th class="py-2.5 px-3">Driver</th>
                    <th class="py-2.5 px-3">Status</th>
                    <th class="py-2.5 px-3">Compliance Documents Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @foreach($machineries as $m)
                <tr>
                    <td class="py-2.5 px-3 font-bold text-slate-900">{{ $m->asset_code }}</td>
                    <td class="py-2.5 px-3 font-semibold">{{ $m->name }} ({{ $m->registration_number }})</td>
                    <td class="py-2.5 px-3">{{ $m->category }}</td>
                    <td class="py-2.5 px-3">{{ $m->currentProject?->code ?? 'Yard' }}</td>
                    <td class="py-2.5 px-3">{{ $m->assignedDriver?->name ?? 'Unassigned' }}</td>
                    <td class="py-2.5 px-3 capitalize font-semibold">{{ str_replace('_', ' ', $m->operational_status) }}</td>
                    <td class="py-2.5 px-3">
                        <ul class="space-y-0.5">
                            @foreach($m->documents as $d)
                            <li class="text-[11px]">
                                &bull; {{ $d->document_type }}: <strong>{{ $d->status_bucket_label }}</strong> (Exp: {{ $d->expiry_date?->format('d M Y') }})
                            </li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
