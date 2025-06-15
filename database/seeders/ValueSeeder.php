<?php

namespace Database\Seeders;

use App\Models\MeasurementDevice;
use App\Models\Value;
use Illuminate\Database\Seeder;

class ValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // MeasurementDevice::each(function ($device) {
        //     Value::factory()
        //         ->count(rand(20, 50))
        //         ->create([
        //             'measurement_id' => $device->id
        //         ]);
        // });
    }
}
