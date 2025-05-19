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

    public function values(): HasMany
    {
        return $this->hasMany(Value::class);
    }
}
