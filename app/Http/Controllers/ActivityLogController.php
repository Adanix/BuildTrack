<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        if (in_array($user->role, ['foreman', 'driver'], true)) {
            abort(403, 'Your role is not authorized to inspect system activity logs.');
        }

        $module = $request->query('module');
        $search = $request->query('search');

        $query = ActivityLog::with('user');

        if ($module && $module !== 'all') {
            $query->where('module', $module);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        $modules = ['Projects', 'Daily Reports', 'Attendance', 'Payroll', 'Machinery', 'Compliance', 'Auth'];

        return view('activity_logs.index', compact('logs', 'modules', 'module', 'search'));
    }
}

