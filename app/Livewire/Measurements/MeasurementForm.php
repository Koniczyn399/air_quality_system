<?php

namespace App\Livewire\Measurements;

use Livewire\Component;
use App\Models\Measurement;
use App\Models\MeasurementDevice;
use Carbon\Carbon;

class MeasurementForm extends Component
{
    public $measurement_id;
    public $measurements_date;
    public $device_id;
    public $values = []; 
    public $isEditing = false;
    public $availableDevices = [];

    // Dodajemy $deviceId jako argument (Livewire automatycznie mapuje kebab-case :device-id)
    public function mount(Measurement $measurement, $deviceId = null)
    {
        // 1. EDYCJA ISTNIEJĄCEGO POMIARU
        if ($measurement->exists) {
            $this->isEditing = true;
            $this->measurement_id = $measurement->id;
            $this->device_id = $measurement->device_id;
            $this->measurements_date = Carbon::parse($measurement->measurements_date)->format('Y-m-d\TH:i');

            foreach ($measurement->values as $value) {
                $this->values[$value->parameter_id] = $value->value;
            }
        } 
        // 2. TWORZENIE NOWEGO POMIARU
        else {
            $this->measurements_date = Carbon::now()->format('Y-m-d\TH:i');
            $this->availableDevices = MeasurementDevice::all(); // Zawsze pobieramy listę dla selecta

            // Jeśli weszliśmy ze szczegółów urządzenia, $deviceId będzie ustawione
            if ($deviceId) {
                $this->device_id = $deviceId;
                $this->initializeValues(); // Od razu ładujemy parametry tego urządzenia
            }
        }
    }

    // Ta metoda uruchamia się po zmianie w select
    public function updatedDeviceId()
    {
        $this->values = []; 
        $this->initializeValues();
    }

    private function initializeValues()
    {
        $parameters = $this->getParameters();
        foreach ($parameters as $param) {
            // Zachowaj istniejącą wartość lub ustaw null
            if (!isset($this->values[$param->id])) {
                $this->values[$param->id] = null;
            }
        }
    }

    public function getParameters()
    {
        if (!$this->device_id) {
            return collect();
        }
        $device = MeasurementDevice::find($this->device_id);
        return $device ? $device->parameters() : collect();
    }

    public function submit()
    {
        $this->validate([
            'device_id' => 'required|exists:measurement_devices,id',
            'measurements_date' => 'required|date',
            'values.*' => 'nullable|numeric',
        ]);

        $formattedDate = Carbon::parse($this->measurements_date)->format('Y-m-d H:i:s');

        if ($this->isEditing) {
            $measurement = Measurement::findOrFail($this->measurement_id);
            $measurement->update(['measurements_date' => $formattedDate]);

            foreach ($this->values as $paramId => $val) {
                $measurement->values()->updateOrCreate(
                    ['parameter_id' => $paramId],
                    ['value' => $val]
                );
            }
            session()->flash('success', 'Pomyślnie zaktualizowano pomiar.');
        } else {
            $measurement = Measurement::create([
                'device_id' => $this->device_id,
                'measurements_date' => $formattedDate,
            ]);

            foreach ($this->values as $paramId => $val) {
                if ($val !== null && $val !== '') {
                    $measurement->values()->create([
                        'parameter_id' => $paramId,
                        'value' => $val,
                    ]);
                }
            }
            session()->flash('success', 'Pomyślnie dodano nowy pomiar.');
        }

        // Powrót do widoku szczegółów urządzenia (logiczne, skoro tam dodajemy)
        return redirect()->route('values.index', ['device_id' => $this->device_id]);
    }

    public function render()
    {
        return view('livewire.measurements.measurement-form', [
            'parameters' => $this->getParameters()
        ]);
    }
}