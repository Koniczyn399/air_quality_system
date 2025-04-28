<?php

namespace Database\Seeders;

use App\Models\MeasurementDevice;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory as Faker;

class MeasurementDevicesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $devices = [
            [
                'name' => 'Miernik temperatury T-1000',
                'model' => 'TempMaster Pro',
                'serial_number' => 'SN-TMP-2023-001',
                'calibration_date' => Carbon::today()->subMonths(2),
                'next_calibration_date' => Carbon::today()->addMonths(10),
                'description' => 'Precyzyjny miernik temperatury do zastosowań laboratoryjnych',
                'status' => 'active',
                'latitude' => $faker->latitude(50, 55),
                'longitude' => $faker->longitude(15, 25),
            ],
            [
                'name' => 'Analizator wilgotności HA-300',
                'model' => 'HumiControl X',
                'serial_number' => 'SN-HUM-2023-002',
                'calibration_date' => Carbon::today()->subMonth(),
                'next_calibration_date' => Carbon::today()->addMonths(11),
                'description' => 'Przenośny analizator wilgotności z wyświetlaczem dotykowym',
                'status' => 'inactive',
                'latitude' => $faker->latitude(50, 55),
                'longitude' => $faker->longitude(15, 25),
            ],
            [
                'name' => 'Miernik pH SoilProbe',
                'model' => 'pH-200',
                'serial_number' => 'SN-PH-2023-003',
                'calibration_date' => Carbon::today()->subMonths(3),
                'next_calibration_date' => Carbon::today()->addMonths(9),
                'description' => 'Miernik pH gleby z zestawem elektrod',
                'status' => 'in_repair',
                'latitude' => $faker->latitude(50, 55),
                'longitude' => $faker->longitude(15, 25),
            ],
            [
                'name' => 'ALL in One v1',
                'model' => 'Kronos 300',
                'serial_number' => 'SN-JK-2025-233',
                'calibration_date' => Carbon::today()->subMonths(3),
                'next_calibration_date' => Carbon::today()->addMonths(9),
                'description' => 'Miernik all in one',
                'status' => 'active',
                'latitude' => $faker->latitude(50, 55),
                'longitude' => $faker->longitude(15, 25),
            ],
            [
                'name' => 'ALL in One v3',
                'model' => 'Kronos 600',
                'serial_number' => 'KK-SS-2025-342',
                'calibration_date' => Carbon::today()->subMonths(3),
                'next_calibration_date' => Carbon::today()->addMonths(9),
                'description' => 'Miernik all in one',
                'status' => 'active',
                'latitude' => $faker->latitude(50, 55),
                'longitude' => $faker->longitude(15, 25),
            ],
            [
                'name' => 'ALL in One v2',
                'model' => 'Kronos 535',
                'serial_number' => 'BB-88-2025-234',
                'calibration_date' => Carbon::today()->subMonths(3),
                'next_calibration_date' => Carbon::today()->addMonths(9),
                'description' => 'Miernik all in one',
                'status' => 'active',
                'latitude' => $faker->latitude(50, 55),
                'longitude' => $faker->longitude(15, 25),
            ],
        ];

        foreach ($devices as $device) {
            MeasurementDevice::create($device);
        }
    }
}