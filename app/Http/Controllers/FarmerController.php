<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use Illuminate\Http\Request;

class FarmerController extends Controller
{
    /**
     * Display a listing of the resource, with optional filters.
     */
    public function index(Request $request)
    {
        $query = Farmer::query();

        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('component')) {
            $query->where('component', $request->component);
        }

        $farmers = $query->orderBy('state')->orderBy('district')->paginate(10)->withQueryString();

        // Summary counts for dashboard cards
        $summary = [
            'total' => Farmer::count(),
            'installed' => Farmer::where('status', 'Installed')->count(),
            'pending' => Farmer::whereIn('status', ['Pending', 'Pending Commissioning', 'Under Verification', 'Applied', 'Approved'])->count(),
            'by_state' => Farmer::selectRaw('state, count(*) as total')->groupBy('state')->pluck('total', 'state'),
        ];

        return view('farmers.index', [
            'farmers' => $farmers,
            'summary' => $summary,
            'states' => Farmer::states(),
            'components' => Farmer::components(),
            'statuses' => Farmer::statuses(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('farmers.create', [
            'states' => Farmer::states(),
            'components' => Farmer::components(),
            'statuses' => Farmer::statuses(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        Farmer::create($validated);

        return redirect()->route('farmers.index')->with('success', 'Farmer record added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Farmer $farmer)
    {
        return view('farmers.show', compact('farmer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Farmer $farmer)
    {
        return view('farmers.edit', [
            'farmer' => $farmer,
            'states' => Farmer::states(),
            'components' => Farmer::components(),
            'statuses' => Farmer::statuses(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Farmer $farmer)
    {
        $validated = $this->validateData($request, $farmer->id);

        $farmer->update($validated);

        return redirect()->route('farmers.index')->with('success', 'Farmer record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Farmer $farmer)
    {
        $farmer->delete();

        return redirect()->route('farmers.index')->with('success', 'Farmer record deleted.');
    }

    /**
     * Shared validation rules for store/update.
     */
    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'application_no' => 'required|string|max:255|unique:farmers,application_no' . ($ignoreId ? ",{$ignoreId}" : ''),
            'imei_number' => 'required|string|max:255|unique:farmers,imei_number' . ($ignoreId ? ",{$ignoreId}" : ''),
            'farmer_name' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'pump_capacity_hp' => 'required|numeric|min:1|max:10',
            'component' => 'required|in:Component A,Component B,Component C',
            'subsidy_percent' => 'required|numeric|min:0|max:100',
            'installation_date' => 'nullable|date',
            'status' => 'required|string|max:255',
        ]);
    }
}
