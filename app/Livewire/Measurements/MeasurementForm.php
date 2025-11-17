<?php

namespace App\Livewire\Measurements;

use Livewire\Component;
use App\Models\Measurement;
use App\Models\MeasurementDevice;
use App\Models\Parameter;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MeasurementForm extends Component
{
    public $measurement_id;
    public $measurements_date;
    public $device_id;
    public $values = []; // [parameter_id => value]
    public $isEditing = false;

    public function mount(\App\Models\Measurement $measurement, $device_id = null)
    {
        // Jeśli edytujemy istniejący pomiar
        if ($measurement->exists) {
            $this->isEditing = true;
            $this->measurement_id = $measurement->id;
            $this->device_id = $measurement->device_id;
            $this->measurements_date = Carbon::parse($measurement->measurements_date)->format('Y-m-d\TH:i');

            // Mapowanie istniejących wartości do tablicy
            foreach ($measurement->values as $value) {
                $this->values[$value->parameter_id] = $value->value;
            }
        } 
        // Jeśli tworzymy nowy pomiar
        else {
            $this->device_id = $device_id;
            $this->measurements_date = Carbon::now()->format('Y-m-d\TH:i');
            
            // Inicjalizuj puste wartości dla parametrów
            $parameters = $this->getParameters();
            foreach ($parameters as $param) {
                if (!isset($this->values[$param->id])) {
                    $this->values[$param->id] = null;
                }
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
            'measurements_date' => 'required|date',
            'values.*' => 'nullable|numeric',
        ]);

        // Konwersja formatu daty z powrotem do formatu bazy danych
        $formattedDate = Carbon::parse($this->measurements_date)->format('Y-m-d H:i:s');

        if ($this->isEditing) {
            $measurement = Measurement::findOrFail($this->measurement_id);
            $measurement->update([
                'measurements_date' => $formattedDate,
            ]);

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
                if ($val !== null) {
                    $measurement->values()->create([
                        'parameter_id' => $paramId,
                        'value' => $val,
                    ]);
                }
            }

            session()->flash('success', 'Pomyślnie dodano nowy pomiar.');
        }

        return redirect()->route('values.index', ['device_id' => $this->device_id]);
    }

    public function render()
    {
        $parameters = $this->getParameters();
        
        return view('livewire.measurements.measurement-form', [
            'parameters' => $parameters
        ])->layout('layouts.app');
    }
}