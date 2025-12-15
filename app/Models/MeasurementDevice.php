<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeasurementDevice extends Model
{
    protected $fillable = [
        'name', 'model', 'serial_number',
        'calibration_date', 'next_calibration_date',
        'description', 'status', 'user_id', 'latitude', 'longitude'
    ];

    protected $casts = [
        'calibration_date' => 'date',
        'next_calibration_date' => 'date',
        'parameter_ids'=>'json',
    ];

    public function statusHistory(): HasMany
    {
        return $this->hasMany(MeasurementHistory::class);
    }

    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Aktywny',
            'inactive' => 'Nieaktywny',
            'in_repair' => 'W naprawie',
            default => 'Nieznany'
        };
    }

    public function addStatusHistory(string $status, ?string $notes = null): void
    {
        $this->statusHistory()->create([
            'status' => $status,
            'changed_by' => Auth::id(),
            'notes' => $notes,
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parameters()
    {
        $ids = json_decode($this->parameter_ids, true) ?? [];
        return Parameter::whereIn('id', $ids)->get();
    }

    public function values(): HasMany
    {
        return $this->hasMany(\App\Models\Value::class, 'measurement_id');
    }

    public function deviceParameters(): HasMany
    {
        return $this->hasMany(DeviceParameter::class, 'device_id', 'id');
    }

    // Отримати останні значення по параметрах цього девайсу
    public function latestParameterValues()
    {
        return Value::query()
            ->join('parameters as p', 'values.parameter_id', '=', 'p.id')
            ->join('device_parameters as dp', function ($join) {
                $join->on('dp.parameter_id', '=', 'values.parameter_id')
                     ->on('dp.device_id', '=', \DB::raw($this->id)); // прив’язка до поточного пристрою
            })
            ->select('values.*', 'p.tag', 'p.name', 'p.unit')
            ->orderByDesc('values.created_at');
    }
}
