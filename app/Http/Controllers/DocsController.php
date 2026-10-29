<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class DocsController extends Controller
{
    public function index(): View
    {
        $demoUsers = User::whereIn('role', [
            'admin',
            'project_manager',
            'foreman',
            'hr_payroll',
            'asset_manager',
            'driver',
        ])->get()->keyBy('role');

        return view('docs.index', compact('demoUsers'));
    }
}

