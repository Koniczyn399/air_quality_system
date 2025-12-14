<?php

namespace App\Http\Controllers;

use App\Models\Measurement;
use App\Models\MeasurementDevice;
use App\Models\Parameter;
use App\Models\Value;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    /**
     * Pomocnicza funkcja do bezpiecznego formatowania parameter_ids na tablicę.
     * Zapobiega błędom typu "count(): string given".
     */
    private function ensureArray($input): array
    {
        if (is_array($input)) {
            return $input;
        }

        if (is_string($input) && !empty($input)) {
            // Próba dekodowania JSON
            $decoded = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            // Jeśli to nie JSON, to może lista po przecinku (np. "1,2,3")
            return explode(',', $input);
        }

        return [];
    }

    public function create(Request $request)
    {
        // Pobieramy ID urządzenia z linku (np. ?device=1), jeśli istnieje
        $deviceId = $request->query('device');

        return view('measurements.form', [
            'measurement' => new Measurement(),
            'deviceId' => $deviceId, // Przekazujemy zmienną do widoku
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|exists:measurement_devices,id',
            'measurements_date' => 'required|date',
            'values' => 'required|array',
        ]);

        $device = MeasurementDevice::find($validated['device_id']);
        
        // ZABEZPIECZENIE: Pobieramy dozwolone parametry jako pewną tablicę
        $allowedParams = $this->ensureArray($device->parameter_ids);

        $measurement = Measurement::create([
            'device_id' => $validated['device_id'],
            'measurements_date' => $validated['measurements_date'],
        ]);

        foreach ($validated['values'] as $parameterId => $value) {
            // Sprawdzamy czy parametr jest dozwolony i czy wartość nie jest pusta
            // Używamy intval($parameterId) dla pewności przy porównywaniu
            if ($value !== null && $value !== '' && in_array($parameterId, $allowedParams)) {
                Value::create([
                    'measurement_id' => $measurement->id,
                    'parameter_id' => $parameterId,
                    'value' => $value,
                ]);
            }
        }

        // Poprawione przekierowanie
        return redirect()->route('measurement-devices.show', $measurement->device_id)
            ->with('success', 'Pomiar został dodany.');
    }

    public function index()
    {
        $measurements = Measurement::with([
            'device',
            'values.parameter'
        ])->orderBy('created_at', 'desc')->get();

        return view('measurements.index', compact('measurements'));
    }

    public function edit(Measurement $measurement)
    {
        return view('measurements.form', ['measurement' => $measurement]);
    }

    public function update(Request $request, Measurement $measurement)
    {
        $validated = $request->validate([
            'measurements_date' => 'required|date',
            'values' => 'required|array',
        ]);

        $measurement->update([
            'measurements_date' => $validated['measurements_date'],
        ]);

        foreach ($validated['values'] as $parameterId => $value) {
            if ($value !== null && $value !== '') {
                Value::updateOrCreate(
                    [
                        'measurement_id' => $measurement->id,
                        'parameter_id' => $parameterId
                    ],
                    ['value' => $value]
                );
            }
        }

        return redirect()->route('measurement-devices.show', $measurement->device_id)
            ->with('success', 'Pomiar został zaktualizowany.');
    }

    public function destroy(Measurement $measurement)
    {
        $measurement->delete();
        return redirect()->back()->with('success', 'Pomiar został usunięty.');
    }
}