<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmer extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_no',
        'imei_number',
        'farmer_name',
        'state',
        'district',
        'pump_capacity_hp',
        'component',
        'subsidy_percent',
        'installation_date',
        'status',
    ];

    protected $casts = [
        'installation_date' => 'date',
        'pump_capacity_hp' => 'decimal:1',
        'subsidy_percent' => 'decimal:2',
    ];

    public function generations()
    {
        return $this->hasMany(Generation::class);
    }

    /**
     * Approx solar panel capacity (kWp) installed for the pump, based on
     * standard PM-KUSUM Component B sizing norms (dummy approximation).
     */
    public function panelCapacityKw(): float
    {
        $map = [
            2 => 2.0,
            3 => 3.0,
            5 => 5.4,
            7.5 => 8.2,
            10 => 11.0,
        ];

        $hp = (float) $this->pump_capacity_hp;

        return $map[$hp] ?? round($hp * 1.1, 1);
    }

    /**
     * States covered under this dummy project.
     */
    public static function states(): array
    {
        return ['Uttar Pradesh', 'Jammu and Kashmir', 'Karnataka'];
    }

    public static function components(): array
    {
        return ['Component A', 'Component B', 'Component C'];
    }

    public static function statuses(): array
    {
        return [
            'Applied',
            'Approved',
            'Under Verification',
            'Pending Commissioning',
            'Installed',
            'Pending',
            'Rejected',
        ];
    }
}
