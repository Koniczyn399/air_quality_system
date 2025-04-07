<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Parameter;

class ParameterSeeder extends Seeder
{
    public function run()
    {
        $parameters = [
            [
                'name' => 'PM2.5',
                'unit' => 'µg/m³',
                'tag' => 'PM2_5'
            ],
            [
                'name' => 'PM10',
                'unit' => 'µg/m³',
                'tag' => 'PM10'
            ],
            [
                'name' => 'Temperatura',
                'unit' => '°C',
                'tag' => 'TEMP'
            ],
            [
                'name' => 'Wilgotność',
                'unit' => '%',
                'tag' => 'HUM'
            ],
            [
                'name' => 'Ciśnienie',
                'unit' => 'hPa',
                'tag' => 'PRESS'
            ]
        ];

        foreach ($parameters as $param) {
            Parameter::firstOrCreate(
                ['tag' => $param['tag']],
                $param
            );
        }
    }
}
