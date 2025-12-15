<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class MeasurementDevice extends Model
{
    protected $fillable = [
        'name', 'model', 'serial_number',
        'calibration_date', 'next_calibration_date',
        'description', 'status', 'user_id', 'latitude', 'longitude', 'parameter_ids'
    ];

    protected $casts = [
        'calibration_date' => 'date',
        'next_calibration_date' => 'date',
        'parameter_ids' => 'array', // To kluczowe dla działania selecta parametrów
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

    /**
     * Bezpieczne pobieranie parametrów.
     * Naprawia błąd "count(): string given" nawet przy "brudnych" danych.
     */
    public function parameters()
    {
        $ids = $this->parameter_ids;

        // ZABEZPIECZENIE: Jeśli casting Laravel nie zadziałał lub dane są stringiem (np. "1,2")
        if (is_string($ids)) {
            // Próbujemy dekodować JSON
            $decoded = json_decode($ids, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $ids = $decoded;
            } else {
                // Jeśli nie JSON, próbujemy explode po przecinku
                $ids = explode(',', $ids);
            }
        }

        // Ostateczne upewnienie się, że mamy tablicę
        $ids = is_array($ids) ? $ids : [];

        return Parameter::whereIn('id', $ids)->get();
    }

    /**
     * Relacja do Wartości (Values) musi iść PRZEZ Pomiary (Measurements).
     * Urządzenie -> ma wiele Pomiarów -> które mają wiele Wartości.
     */
    public function values(): HasManyThrough
    {
        return $this->hasManyThrough(
            Value::class,           // Model docelowy (Wartość)
            Measurement::class,     // Model pośredni (Pomiar)
            'device_id',            // Klucz obcy w tabeli measurements (wskazuje na device)
            'measurement_id',       // Klucz obcy w tabeli values (wskazuje na measurement)
            'id',                   // Klucz lokalny w tabeli measurement_devices
            'id'                    // Klucz lokalny w tabeli measurements
        );
    }
}
