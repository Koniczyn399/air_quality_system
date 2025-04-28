<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Value extends Model
{
    use HasFactory;

    protected $table = 'values';

    protected $fillable = [
        'measurement_id',
        'parameter_id',
        'value'
    ];


    public function measurementDevice(): BelongsTo
    {
        return $this->belongsTo(MeasurementDevice::class, 'measurement_id');
    }

 
    public function parameter(): BelongsTo
    {
        return $this->belongsTo(Parameter::class);
    }


    public function getFormattedValueAttribute(): string
    {
        return number_format($this->value, 2) . ' ' . $this->parameter->unit;
    }


    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }
}