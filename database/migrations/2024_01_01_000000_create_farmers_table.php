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
        Schema::create('farmers', function (Blueprint $table) {
            $table->id();
            $table->string('application_no')->unique(); // RMS application/device registration no.
            $table->string('imei_number')->unique(); // IMEI of the RMS monitoring unit on the pump controller
            $table->string('farmer_name');
            $table->string('state');
            $table->string('district');
            $table->decimal('pump_capacity_hp', 4, 1); // e.g. 2.0, 3.0, 5.0, 7.5, 10.0
            $table->enum('component', ['Component A', 'Component B', 'Component C']);
            $table->decimal('subsidy_percent', 5, 2);
            $table->date('installation_date')->nullable();
            $table->enum('status', [
                'Applied',
                'Approved',
                'Under Verification',
                'Pending Commissioning',
                'Installed',
                'Pending',
                'Rejected',
            ])->default('Applied');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmers');
    }
};
