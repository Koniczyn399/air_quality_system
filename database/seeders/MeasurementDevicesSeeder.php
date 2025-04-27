<?php

namespace Database\Seeders;

use App\Models\MeasurementDevice;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MeasurementDevicesSeeder extends Seeder
{

    //Urządzenia
    // 1,2,3 - pomiary_pelne - devid;created_at;Temperature;Pressure;Humidity;PM1;PM2_5;PM10
    // 4,5 - pomiary_podstawowe - devid;created_at;Temperature;Pressure
    // 6,7 - pomiary_posrednie - devid;created_at;Temperature;Pressure;PM2_5;PM10



    public function run()
    {
        $devices = [
            [
                'name' => 'ALL in One v1',
                'model' => 'Kronos 300',
                'serial_number' => 'SN-JK-2025-233',
                'calibration_date' => Carbon::today()->subMonths(3),
                'next_calibration_date' => Carbon::today()->addMonths(9),
                'description' => 'Miernik all in one',
                'status' => 'active',
            ]
            ,
            [
                'name' => 'ALL in One v3',
                'model' => 'Kronos 600',
                'serial_number' => 'KK-SS-2025-342',
                'calibration_date' => Carbon::today()->subMonths(3),
                'next_calibration_date' => Carbon::today()->addMonths(9),
                'description' => 'Miernik all in one',
                'status' => 'active',
            ]
            ,
            [
                'name' => 'ALL in One v2',
                'model' => 'Kronos 535',
                'serial_number' => 'BB-88-2025-234',
                'calibration_date' => Carbon::today()->subMonths(3),
                'next_calibration_date' => Carbon::today()->addMonths(9),
                'description' => 'Miernik all in one',
                'status' => 'active',
            ],
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
                'name' => 'Miernik powietrza i pyłu Airomex 2',
                'model' => 'pH-200',
                'serial_number' => 'SN-PH-2023-003',
                'calibration_date' => Carbon::today()->subMonths(3),
                'next_calibration_date' => Carbon::today()->addMonths(9),
                'description' => 'Miernik powietrza zestawem elektrod',
                'status' => 'in_repair',
            ],
            [
                'name' => 'Miernik powietrza i pyłu Airomex 3',
                'model' => 'pH-200',
                'serial_number' => 'SN-KK-2023-042',
                'calibration_date' => Carbon::today()->subMonths(3),
                'next_calibration_date' => Carbon::today()->addMonths(9),
                'description' => 'Miernik powietrza zestawem elektrod',
                'status' => 'in_repair',
            ],
            // [
            //     'name' => 'test_device',
            //     'model' => 'test_device',
            //     'serial_number' => 'TT-TT-2137-56',
            //     'calibration_date' => Carbon::today()->subMonths(3),
            //     'next_calibration_date' => Carbon::today()->addMonths(9),
            //     'description' => 'test_device',
            //     'status' => 'inactive',
            // ],



        ];

        foreach ($devices as $device) {
            MeasurementDevice::create($device);
        }
    }
}