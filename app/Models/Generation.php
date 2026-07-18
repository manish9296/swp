<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generation extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'process_date',
        'time_stamp',
        'rms_data_status',
        'dc_input_voltage',
        'solar_generation_kwh',
        'pump_run_hours',
        'water_discharge_liter',
    ];

    protected $casts = [
        'process_date' => 'date',
        'time_stamp' => 'datetime',
        'dc_input_voltage' => 'decimal:2',
        'solar_generation_kwh' => 'decimal:2',
        'pump_run_hours' => 'decimal:2',
        'water_discharge_liter' => 'decimal:2',
    ];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }
}
