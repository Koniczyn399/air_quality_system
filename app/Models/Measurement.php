<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Measurement extends Model
{
    protected $fillable = [
        'measurements_date',
        'device_id',
    ];

    public function device()
    {
        return $this->belongsTo(MeasurementDevice::class, 'device_id');
    }

    public function valueByName(string $name)
    {
        $value = $this->values->first(function ($v) use ($name) {
            return $v->parameter?->name === $name;
        });

        return $value->formatted_value ?? '-';
    }



    public function values(): HasMany
    {
        return $this->hasMany(Value::class);
    }
}
