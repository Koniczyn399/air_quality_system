<?php

namespace App\Livewire\Measurements;

use Livewire\Component;
use App\Models\Measurement;
use App\Models\MeasurementDevice;
use App\Models\Parameter;
use Carbon\Carbon;

class MeasurementForm extends Component
{
    public $measurement_id;
    public $measurements_date;
    public $device_id;
    public $values = []; // [parameter_id => value]
    public $parameters = [];
    public $isEditing = false;

    public function mount($measurement = null, $device_id = null)
    {
        $this->device_id = $device_id;

        // pobierz parametry urządzenia
        $device = MeasurementDevice::find($device_id);
        $this->parameters = $device ? $device->parameters() : collect();


        if ($measurement) {
            $this->isEditing = true;
            $this->measurement_id = $measurement->id;
            $this->measurements_date = Carbon::parse($measurement->measurements_date)->format('Y-m-d\TH:i');

            // mapowanie istniejących wartości do tablicy
            foreach ($measurement->values as $value) {
                $this->values[$value->parameter_id] = $value->value;
            }
        }
    }

    public function submit()
    {
        $this->validate([
            'measurements_date' => 'required|date',
            'values.*' => 'nullable|numeric',
        ]);

        if ($this->isEditing) {
            $measurement = Measurement::findOrFail($this->measurement_id);
            $measurement->update([
                'measurements_date' => $this->measurements_date,
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
                'measurements_date' => $this->measurements_date,
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
        return view('livewire.measurements.measurement-form');
    }
}
