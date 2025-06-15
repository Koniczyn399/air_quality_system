<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class MeasurementDevice extends Model
{
    protected $fillable = [
        'name', 'model', 'serial_number',
        'calibration_date', 'next_calibration_date',
<<<<<<<<< Temporary merge branch 1
        'description', 'status', 'user_id'
=========
        'description', 'status',
        'latitude', 'longitude' 
>>>>>>>>> Temporary merge branch 2
    ];

    protected $casts = [
        'calibration_date' => 'date',
        'next_calibration_date' => 'date',
        'parameter_ids'=>'array',
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
}
