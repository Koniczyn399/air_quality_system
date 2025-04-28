<?php

namespace App\Http\Controllers;

use App\Models\MeasurementDevice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MeasurementDeviceController extends Controller
{
    public function index()
    {
        $measurementDevices = MeasurementDevice::with('user')->get();

        return view('measurement-devices.index', compact('measurementDevices'));
    }

    public function create()
    {
        return view('measurement-devices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:measurement_devices',
            'calibration_date' => 'required|date',
            'next_calibration_date' => 'required|date|after:calibration_date',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        MeasurementDevice::create($validated);

        return redirect()->route('measurement-devices.index')->with('success', 'Urządzenie dodane pomyślnie.');
    }

    public function show(MeasurementDevice $measurementDevice)
    {
        $measurementDevice->load('statusHistory.changedBy');
        return view('measurement-devices.show', compact('measurementDevice'));
    }

    public function edit(MeasurementDevice $measurementDevice)
    {
        $mainteiners=User::role('mainteiner')->get();

        return view('measurement-devices.edit', compact('measurementDevice', 'mainteiners'));
    }

    
    public function update(Request $request, MeasurementDevice $measurementDevice)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:measurement_devices,serial_number,'.$measurementDevice->id,
            'calibration_date' => 'required|date',
            'next_calibration_date' => 'required|date|after:calibration_date',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'status' => 'required|in:active,inactive,in_repair',
            'user_id' => ['nullable', 'exists:users,id'],

        ]);

        if ($measurementDevice->status != $request->status) {
            $measurementDevice->addStatusHistory($request->status, 'Zmiana statusu przez formularz edycji');
        }
    
        $measurementDevice->update($validated);
    
        return redirect()->route('measurement-devices.index')
            ->with('success', 'Urządzenie zaktualizowane pomyślnie');
    }

    public function destroy(MeasurementDevice $measurementDevice)
    {
        $measurementDevice->delete();

        return redirect()->route('measurement-devices.index')->with('success', 'Urządzenie usunięte pomyślnie.');
    }
}