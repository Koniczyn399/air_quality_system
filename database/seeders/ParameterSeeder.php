<?php

namespace Database\Seeders;

use App\Models\Parameter;
use Illuminate\Database\Seeder;

class ParameterSeeder extends Seeder
{
    public function run()
    {
        $parameters = [
            [
                'name' => 'PM1',
                'unit' => 'µg/m³',
                'tag' => 'PM1',
            ],
            [
                'name' => 'PM2.5',
                'unit' => 'µg/m³',
                'tag' => 'PM2_5',
            ],
            [
                'name' => 'PM10',
                'unit' => 'µg/m³',
                'tag' => 'PM10',
            ],
            [
                'name' => 'Wilgotność',
                'unit' => '%',
                'tag' => 'HUM',
            ],
            [
                'name' => 'Ciśnienie',
                'unit' => 'hPa',
                'tag' => 'PRESS',
            ],
            [
                'name' => 'Temperatura',
                'unit' => '°C',
                'tag' => 'TEMP',
            ],

        ];

        foreach ($parameters as $param) {
            Parameter::firstOrCreate(
                ['tag' => $param['tag']],
                $param
            );
        }
    }
}
