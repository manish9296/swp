<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Models\Generation;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DashboardController extends Controller
{
    /**
     * All-states overview dashboard.
     */
    public function index(Request $request)
    {
        $days = (int) $request->query('days', 30);
        $days = max(1, min(90, $days));
        $since = now()->subDays($days - 1)->startOfDay();

        $states = Farmer::query()->distinct()->orderBy('state')->pluck('state');

        $farmerCountByState = Farmer::selectRaw('state, count(*) as total')
            ->groupBy('state')
            ->pluck('total', 'state');

        $generationByState = Generation::query()
            ->join('farmers', 'farmers.id', '=', 'generations.farmer_id')
            ->where('process_date', '>=', $since)
            ->selectRaw('farmers.state as state, SUM(solar_generation_kwh) as total_units, SUM(water_discharge_liter) as total_water')
            ->groupBy('farmers.state')
            ->pluck('total_units', 'state');

        $waterByState = Generation::query()
            ->join('farmers', 'farmers.id', '=', 'generations.farmer_id')
            ->where('process_date', '>=', $since)
            ->selectRaw('farmers.state as state, SUM(water_discharge_liter) as total_water')
            ->groupBy('farmers.state')
            ->pluck('total_water', 'state');

        $stats = [
            'total_farmers' => Farmer::count(),
            'total_states' => $states->count(),
            'installed' => Farmer::where('status', 'Installed')->count(),
            'total_generation' => round(Generation::where('process_date', '>=', $since)->sum('solar_generation_kwh'), 2),
        ];

        $dailyTrend = Generation::query()
            ->where('process_date', '>=', $since)
            ->selectRaw('process_date, SUM(solar_generation_kwh) as total_units')
            ->groupBy('process_date')
            ->orderBy('process_date')
            ->get();

        return view('dashboard.index', [
            'days' => $days,
            'states' => $states,
            'farmerCountByState' => $farmerCountByState,
            'generationByState' => $generationByState,
            'waterByState' => $waterByState,
            'stats' => $stats,
            'trendLabels' => $dailyTrend->pluck('process_date')->map(fn ($d) => \Carbon\Carbon::parse($d)->format('d-M')),
            'trendUnits' => $dailyTrend->pluck('total_units')->map(fn ($v) => round((float) $v, 2)),
        ]);
    }

    /**
     * Single-state detail dashboard: farmers in that state + aggregated
     * generation data, with the same 1-90 day selector.
     */
    public function state(Request $request, string $state)
    {
        $allStates = Farmer::query()->distinct()->pluck('state');

        if (! $allStates->contains($state)) {
            throw new NotFoundHttpException("No data found for state: {$state}");
        }

        $days = (int) $request->query('days', 30);
        $days = max(1, min(90, $days));
        $since = now()->subDays($days - 1)->startOfDay();

        $farmers = Farmer::where('state', $state)->orderBy('district')->get();
        $farmerIds = $farmers->pluck('id');

        $districtBreakdown = Generation::query()
            ->join('farmers', 'farmers.id', '=', 'generations.farmer_id')
            ->where('farmers.state', $state)
            ->where('process_date', '>=', $since)
            ->selectRaw('farmers.district as district, SUM(solar_generation_kwh) as total_units, SUM(pump_run_hours) as total_hours, SUM(water_discharge_liter) as total_water')
            ->groupBy('farmers.district')
            ->orderBy('farmers.district')
            ->get();

        $dailyTrend = Generation::query()
            ->whereIn('farmer_id', $farmerIds)
            ->where('process_date', '>=', $since)
            ->selectRaw('process_date, SUM(solar_generation_kwh) as total_units')
            ->groupBy('process_date')
            ->orderBy('process_date')
            ->get();

        $stats = [
            'total_farmers' => $farmers->count(),
            'installed' => $farmers->where('status', 'Installed')->count(),
            'total_generation' => round($districtBreakdown->sum('total_units'), 2),
            'total_hours' => round($districtBreakdown->sum('total_hours'), 1),
            'total_water' => round($districtBreakdown->sum('total_water'), 2),
        ];

        return view('dashboard.state', [
            'state' => $state,
            'days' => $days,
            'farmers' => $farmers,
            'districtBreakdown' => $districtBreakdown,
            'stats' => $stats,
            'trendLabels' => $dailyTrend->pluck('process_date')->map(fn ($d) => \Carbon\Carbon::parse($d)->format('d-M')),
            'trendUnits' => $dailyTrend->pluck('total_units')->map(fn ($v) => round((float) $v, 2)),
        ]);
    }
}
