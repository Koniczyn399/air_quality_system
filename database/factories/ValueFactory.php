<?php

namespace Database\Factories;

use App\Models\MeasurementDevice;
use App\Models\Parameter;
use Illuminate\Database\Eloquent\Factories\Factory;

class ValueFactory extends Factory
{
    public function definition()
{
    $availableTags = ['PM2_5', 'PM10', 'TEMP', 'HUM', 'PRESS'];
    
    $tag = $this->faker->randomElement($availableTags);
    
    $parameter = Parameter::where('tag', $tag)->firstOrFail();

    $value = match($tag) {
        'PM2_5' => $this->faker->randomFloat(2, 0, 100),
        'PM10' => $this->faker->randomFloat(2, 0, 150),
        'TEMP' => $this->faker->randomFloat(2, -20, 40),
        'HUM' => $this->faker->randomFloat(2, 0, 100),
        'PRESS' => $this->faker->randomFloat(2, 950, 1050),
        default => $this->faker->randomFloat(2, 0, 100)
    };

    return [
        'measurement_id' => MeasurementDevice::inRandomOrder()->first()->id,
        'parameter_id' => $parameter->id,
        'value' => $value,
        'created_at' => $this->faker->dateTimeBetween('-1 year', 'now')
    ];
}
}