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
        'description', 'status'
    ];
    
    protected $casts = [
        'calibration_date' => 'date',
        'next_calibration_date' => 'date',
    ];

    public function statusHistory(): HasMany
    {
        return $this->hasMany(MeasurementHistory::class);
    }

    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            'active' => 'Aktywny',
            'inactive' => 'Nieaktywny',
            'in_repair' => 'W naprawie',
            default => 'Nieznany'
        };
    }

    public function addStatusHistory(string $status, string $notes = null): void
    {
        $this->statusHistory()->create([
            'status' => $status,
            'changed_by' => Auth::id(),
            'notes' => $notes
        ]);
    }
    
}