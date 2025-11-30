<?php

namespace App\Http\Controllers;

use App\Models\Measurement;
use App\Models\MeasurementDevice;
use App\Models\Parameter;
use App\Models\Value;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    public function create(Request $request)
    {
        $device = MeasurementDevice::find($request->get('device'));

        if (!$device) {
            return redirect()->route('values.index')->with('error', 'Nie znaleziono urządzenia.');
        }

        // Wymuszamy tablicę. Nawet jeśli w DB jest "" albo "1,2,3"
        $paramIds = $device->parameter_ids;

        if (!is_array($paramIds)) {
            $decoded = json_decode($paramIds, true);
            $paramIds = is_array($decoded) ? $decoded : [];
        }

        $parameters = Parameter::whereIn('id', $paramIds)->get();

        return view('measurements.create', compact('device', 'parameters'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|exists:measurement_devices,id',
            'measurements_date' => 'required|date',
            'values' => 'required|array',
        ]);

        $measurement = Measurement::create([
            'device_id' => $validated['device_id'],
            'measurements_date' => $validated['measurements_date'],
        ]);

        foreach ($validated['values'] as $parameterId => $value) {
            if ($value !== null && $value !== '') {
                Value::create([
                    'measurement_id' => $measurement->id,
                    'parameter_id' => $parameterId,
                    'value' => $value,
                ]);
            }
        }

        return redirect()->route('values.index', ['device_id' => $measurement->device_id])
            ->with('success', 'Pomiar został dodany.');
    }

    public function index()
    {
        // Pobieramy wszystkie pomiary + urządzenie + wartości + parametry
        $measurements = Measurement::with([
            'device',
            'values.parameter'
        ])->orderBy('created_at', 'desc')->get();

        return view('measurements.index', compact('measurements'));
    }

    public function edit(Measurement $measurement)
    {
        

        return view(
            'measurements.form', 
            ['measurement' => $measurement]
        );
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
            $val = $measurement->values()->where('parameter_id', $parameterId)->first();

            if ($val) {
                $val->update(['value' => $value]);
            } else {
                Value::create([
                    'measurement_id' => $measurement->id,
                    'parameter_id' => $parameterId,
                    'value' => $value,
                ]);
            }
        }

        return redirect()->route('values.index', ['device_id' => $measurement->device_id])
            ->with('success', 'Pomiar został zaktualizowany.');
    }

    public function destroy(Measurement $measurement)
    {
        $measurement->delete();

        return redirect()->back()->with('success', 'Pomiar został usunięty.');
    }
}
