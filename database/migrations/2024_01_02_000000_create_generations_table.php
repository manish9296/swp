<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained()->cascadeOnDelete();

            // RMS (Remote Monitoring System) daily reading fields —
            // mirrors the fields sent by the pump's IoT/RMS device.
            $table->date('process_date');
            $table->dateTime('time_stamp');
            $table->enum('rms_data_status', ['VALID', 'INVALID'])->default('VALID');
            $table->decimal('dc_input_voltage', 6, 2)->nullable(); // Volts
            $table->decimal('solar_generation_kwh', 6, 2)->default(0); // Today Solar Generation (KWh)
            $table->decimal('pump_run_hours', 5, 2)->default(0); // Today Pump Day Run Hours (Hrs)
            $table->decimal('water_discharge_liter', 10, 2)->default(0); // Today Water Discharge (Liter)

            $table->timestamps();

            $table->unique(['farmer_id', 'process_date']);
            $table->index('process_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generations');
    }
};
