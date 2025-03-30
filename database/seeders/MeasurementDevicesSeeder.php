<?php

namespace Database\Seeders;

use App\Models\MeasurementDevice;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MeasurementDevicesSeeder extends Seeder
{
    public function run()
    {
        $devices = [
            [
                'name' => 'Miernik temperatury T-1000',
                'model' => 'TempMaster Pro',
                'serial_number' => 'SN-TMP-2023-001',
                'calibration_date' => Carbon::today()->subMonths(2),
                'next_calibration_date' => Carbon::today()->addMonths(10),
                'description' => 'Precyzyjny miernik temperatury do zastosowań laboratoryjnych',
                'status' => 'active',
            ],
            [
                'name' => 'Analizator wilgotności HA-300',
                'model' => 'HumiControl X',
                'serial_number' => 'SN-HUM-2023-002',
                'calibration_date' => Carbon::today()->subMonth(),
                'next_calibration_date' => Carbon::today()->addMonths(11),
                'description' => 'Przenośny analizator wilgotności z wyświetlaczem dotykowym',
                'status' => 'inactive',
            ],
            [
                'name' => 'Miernik pH SoilProbe',
                'model' => 'pH-200',
                'serial_number' => 'SN-PH-2023-003',
                'calibration_date' => Carbon::today()->subMonths(3),
                'next_calibration_date' => Carbon::today()->addMonths(9),
                'description' => 'Miernik pH gleby z zestawem elektrod',
                'status' => 'in_repair',
            ]
        ];

        foreach ($devices as $device) {
            MeasurementDevice::create($device);
        }
    }
}