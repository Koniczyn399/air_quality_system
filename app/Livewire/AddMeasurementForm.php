<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MeasurementDevice;
use App\Models\Measurement;
use App\Models\Value;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class AddMeasurementForm extends Component
{
    public $deviceId;
    public $device;
    public $measurementDate;
    public $values = [];

    protected $listeners = ['openAddMeasurement' => 'openForm'];

    public function openForm($deviceId)
    {
        $this->deviceId = $deviceId;
        $this->device = MeasurementDevice::find($deviceId);

        // Przygotuj pola dla parametrów przypisanych do urządzenia
        $this->values = [];
        foreach ($this->device->parameter_ids as $paramId) {
            $this->values[$paramId] = null;
        }

        $this->dispatch('open-modal', 'add-measurement-modal');
    }

    public function save()
    {
        $this->validate([
            'measurementDate' => 'required|date',
        ]);

        $measurement = Measurement::create([
            'device_id' => $this->deviceId,
            'measurements_date' => Carbon::parse($this->measurementDate),
        ]);

        foreach ($this->values as $paramId => $value) {
            if ($value !== null) {
                Value::create([
                    'measurement_id' => $measurement->id,
                    'parameter_id' => $paramId,
                    'value' => $value,
                ]);
            }
        }

        Session::flash('message', 'Urządzenie zostało zmodyfikowane'); 
        Session::flash('icon', 'success'); 

        $this->dispatch('close-modal', 'add-measurement-modal');
        $this->dispatch('refresh'); // odświeży tabelę
        $this->reset(['measurementDate', 'values']);
        $this->dispatch('notify', type: 'success', message: 'Pomiar został dodany.');
    }

    public function render()
    {
        return view('livewire.add-measurement-form');
    }
}
