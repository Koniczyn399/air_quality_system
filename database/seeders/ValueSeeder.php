<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MeasurementDevice;
use App\Models\Value;

class ValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MeasurementDevice::each(function ($device) {
            Value::factory()
                ->count(rand(20, 50)) 
                ->create([
                    'measurement_id' => $device->id
                ]);
        });
    }
}
