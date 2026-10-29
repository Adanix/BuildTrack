<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Machinery;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MachineryController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $status = $request->query('status');
        $category = $request->query('category');
        $search = $request->query('search');

        $query = Machinery::with(['currentProject', 'assignedDriver', 'documents']);

        // Driver scoping
        if ($user->isDriver()) {
            $query->where('assigned_driver_id', $user->id);
        }

        if ($status && $status !== 'all') {
            $query->where('operational_status', $status);
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_code', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%")
                    ->orWhere('make_model', 'like', "%{$search}%");
            });
        }

        $machineries = $query->orderBy('asset_code')->paginate(12)->withQueryString();
        $categories = [
            'Excavator / Backhoe',
            'Transit Mixer',
            'Dump Truck / Tipper',
            'Mobile Crane',
            'Road Roller / Compactor',
            'Concrete Pump',
        ];

        return view('machineries.index', compact('machineries', 'categories', 'status', 'category', 'search'));
    }

    public function show(Machinery $machinery): View
    {
        $user = auth()->user();
        if ($user->isDriver() && $machinery->assigned_driver_id !== $user->id) {
            abort(403, 'Drivers are only authorized to view details of their assigned machinery.');
        }

        $machinery->load(['currentProject', 'assignedDriver', 'documents.flagger']);

        return view('machineries.show', compact('machinery'));
    }

    public function create(): View
    {
        $this->authorizeAssetManagement();

        $projects = Project::where('status', 'active')->orderBy('name')->get();
        $drivers = User::where('role', 'driver')->where('is_active', true)->get();
        $categories = [
            'Excavator / Backhoe',
            'Transit Mixer',
            'Dump Truck / Tipper',
            'Mobile Crane',
            'Road Roller / Compactor',
            'Concrete Pump',
        ];

        return view('machineries.create', compact('projects', 'drivers', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAssetManagement();

        $validated = $request->validate([
            'asset_code' => 'required|string|max:50|unique:machineries,asset_code',
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'registration_number' => 'required|string|max:50|unique:machineries,registration_number',
            'make_model' => 'required|string|max:255',
            'current_project_id' => 'nullable|exists:projects,id',
            'assigned_driver_id' => 'nullable|exists:users,id',
            'operational_status' => 'required|in:active_on_site,available_yard,under_maintenance',
            'notes' => 'nullable|string',
        ]);

        $machinery = Machinery::create($validated);

        ActivityLog::log(
            'Machinery',
            'Equipment Added',
            "Registered heavy machinery '{$machinery->name}' ({$machinery->registration_number}, Code: {$machinery->asset_code})."
        );

        return redirect()->route('machineries.show', $machinery)->with('success', 'Machinery registered successfully in fleet register.');
    }

    public function edit(Machinery $machinery): View
    {
        $this->authorizeAssetManagement();

        $projects = Project::where('status', 'active')->orderBy('name')->get();
        $drivers = User::where('role', 'driver')->where('is_active', true)->get();
        $categories = [
            'Excavator / Backhoe',
            'Transit Mixer',
            'Dump Truck / Tipper',
            'Mobile Crane',
            'Road Roller / Compactor',
            'Concrete Pump',
        ];

        return view('machineries.edit', compact('machinery', 'projects', 'drivers', 'categories'));
    }

    public function update(Request $request, Machinery $machinery): RedirectResponse
    {
        $this->authorizeAssetManagement();

        $validated = $request->validate([
            'asset_code' => "required|string|max:50|unique:machineries,asset_code,{$machinery->id}",
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'registration_number' => "required|string|max:50|unique:machineries,registration_number,{$machinery->id}",
            'make_model' => 'required|string|max:255',
            'current_project_id' => 'nullable|exists:projects,id',
            'assigned_driver_id' => 'nullable|exists:users,id',
            'operational_status' => 'required|in:active_on_site,available_yard,under_maintenance',
            'notes' => 'nullable|string',
        ]);

        $machinery->update($validated);

        ActivityLog::log(
            'Machinery',
            'Equipment Updated',
            "Updated machinery record '{$machinery->name}' ({$machinery->registration_number})."
        );

        return redirect()->route('machineries.show', $machinery)->with('success', 'Machinery details updated.');
    }

    private function authorizeAssetManagement(): void
    {
        $role = auth()->user()->role;
        if (! in_array($role, ['admin', 'asset_manager'], true)) {
            abort(403, 'Only administrators and fleet asset managers can modify machinery register records.');
        }
    }
}

