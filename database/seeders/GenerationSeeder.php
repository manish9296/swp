<?php

namespace Database\Seeders;

use App\Models\Farmer;
use App\Models\Generation;
use Illuminate\Database\Seeder;

class GenerationSeeder extends Seeder
{
    /**
     * Generate last 90 days of dummy daily RMS readings for every farmer's
     * pump — mirrors the fields reported by the actual PM-KUSUM RMS
     * (Remote Monitoring System) device: process date, time stamp, data
     * validity, DC input voltage, solar generation, pump run hours, and
     * water discharge.
     */
    public function run(): void
    {
        $farmers = Farmer::all();
        $today = now()->startOfDay();

        foreach ($farmers as $farmer) {
            $panelKw = $farmer->panelCapacityKw();
            $rows = [];

            for ($i = 89; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i);

                // RMS device usually pings once a day, early morning
                // (~05:20 - 05:40), occasionally later if it reconnected
                // after being offline.
                $pingHour = rand(1, 15) === 1 ? rand(12, 18) : 5;
                $pingMinute = rand(15, 45);
                $pingSecond = 0;
                $timeStamp = $date->copy()->setTime($pingHour, $pingMinute, $pingSecond);

                // Occasional invalid/no-data day (device offline, no sun
                // reading captured) — mirrors 0.00 rows seen on real RMS
                // dashboards.
                $isZeroDay = rand(1, 4) === 1; // ~25% no-generation days (cloudy / device idle)
                $isInvalid = rand(1, 30) === 1; // rare invalid reading

                if ($isZeroDay) {
                    $solarKwh = 0;
                    $pumpHours = round(rand(0, 5) / 100, 2); // tiny residual, mostly 0.00-0.05
                    $waterLiter = 0;
                } else {
                    $variance = mt_rand(70, 120) / 100;
                    $solarKwh = round($panelKw * 4.3 * 0.72 * $variance, 2);
                    $pumpHours = round(min(($solarKwh / max($panelKw, 1)) * 1.15, 8), 2);
                    // Rough discharge rate scaled by pump HP and run hours
                    $waterLiter = round($farmer->pump_capacity_hp * $pumpHours * 6000 * (mt_rand(85, 110) / 100), 2);
                }

                $dcVoltage = round(mt_rand(1180, 1600) / 10, 2); // ~118V - 160V typical DC bus range

                $rows[] = [
                    'farmer_id' => $farmer->id,
                    'process_date' => $date->toDateString(),
                    'time_stamp' => $timeStamp->toDateTimeString(),
                    'rms_data_status' => $isInvalid ? 'INVALID' : 'VALID',
                    'dc_input_voltage' => $dcVoltage,
                    'solar_generation_kwh' => $solarKwh,
                    'pump_run_hours' => $pumpHours,
                    'water_discharge_liter' => $waterLiter,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            foreach (array_chunk($rows, 30) as $chunk) {
                Generation::insert($chunk);
            }
        }
    }
}
