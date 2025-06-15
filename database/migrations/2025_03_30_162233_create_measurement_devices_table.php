<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('measurement_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model');
            $table->string('serial_number')->unique();
            $table->date('calibration_date');
            $table->date('next_calibration_date');
            $table->text('description')->nullable();
            $table->string('status')->default('active'); // Zmieniamy boolean na string
            $table->decimal('latitude', 10, 8)->nullable(); // Szerokość geograficzna
            $table->decimal('longitude', 11, 8)->nullable(); // Długość geograficzna
            $table->json('parameter_ids')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('measurement_devices');
    }
};
