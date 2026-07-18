<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Models\Generation;
use Illuminate\Http\Request;

class GenerationController extends Controller
{
    /**
     * Per-farmer RMS generation report: full data table (like the RMS
     * dashboard export) plus charts, selectable window of 1-90 days.
     */
    public function farmer(Request $request, Farmer $farmer)
    {
        $days = (int) $request->query('days', 30);
        $days = max(1, min(90, $days)); // clamp to 1-90

        $records = $farmer->generations()
            ->where('process_date', '>=', now()->subDays($days - 1)->startOfDay())
            ->orderBy('process_date')
            ->get();

        $chartLabels = $records->pluck('process_date')->map(fn ($d) => $d->format('d-M'));
        $units = $records->pluck('solar_generation_kwh')->map(fn ($v) => (float) $v);
        $hours = $records->pluck('pump_run_hours')->map(fn ($v) => (float) $v);

        $stats = [
            'total_units' => round($units->sum(), 2),
            'avg_units' => $records->count() ? round($units->avg(), 2) : 0,
            'peak_units' => round($units->max() ?? 0, 2),
            'peak_date' => optional($records->sortByDesc('solar_generation_kwh')->first())->process_date?->format('d-M-Y'),
            'total_hours' => round($hours->sum(), 1),
            'total_water' => round($records->pluck('water_discharge_liter')->sum(), 2),
        ];

        return view('generation.farmer', [
            'farmer' => $farmer,
            'days' => $days,
            'chartLabels' => $chartLabels,
            'units' => $units,
            'hours' => $hours,
            'stats' => $stats,
            // Latest-first order for the RMS-style data table
            'records' => $records->sortByDesc('process_date')->values(),
        ]);
    }

    /**
     * State-wise aggregate generation summary chart, selectable window of 1-90 days.
     */
    public function summary(Request $request)
    {
        $days = (int) $request->query('days', 30);
        $days = max(1, min(90, $days));

        $since = now()->subDays($days - 1)->startOfDay();

        $byState = Generation::query()
            ->join('farmers', 'farmers.id', '=', 'generations.farmer_id')
            ->where('process_date', '>=', $since)
            ->selectRaw('farmers.state as state, SUM(solar_generation_kwh) as total_units, SUM(pump_run_hours) as total_hours, SUM(water_discharge_liter) as total_water')
            ->groupBy('farmers.state')
            ->orderBy('farmers.state')
            ->get();

        $dailyTrend = Generation::query()
            ->where('process_date', '>=', $since)
            ->selectRaw('process_date, SUM(solar_generation_kwh) as total_units')
            ->groupBy('process_date')
            ->orderBy('process_date')
            ->get();

        $stats = [
            'total_units' => round($byState->sum('total_units'), 2),
            'total_hours' => round($byState->sum('total_hours'), 1),
            'total_water' => round($byState->sum('total_water'), 2),
            'farmer_count' => Farmer::count(),
        ];

        return view('generation.summary', [
            'days' => $days,
            'byState' => $byState,
            'trendLabels' => $dailyTrend->pluck('process_date')->map(fn ($d) => \Carbon\Carbon::parse($d)->format('d-M')),
            'trendUnits' => $dailyTrend->pluck('total_units')->map(fn ($v) => round((float) $v, 2)),
            'stats' => $stats,
        ]);
    }
}
