<?php

namespace App\Livewire\MeasurementDevices;

use Livewire\Component;
use App\Models\MeasurementDevice;
use App\Models\User;
use App\Models\Parameter;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Str;

class MeasurementDeviceForm extends Component
{

use WireUiActions;

    public MeasurementDevice $measurementDevice;

    public $id;
    public $name;
    public $model;

    public $serial_number;
    public $calibration_date;

    public $next_calibration_date;

    public $status;

    public $parameter_ids;

    public $user_id;

    public $description;

    public $maintainers;
    public $parameters;
    public $other_parameters;

    public $p;

    public $latitude;
    public $longitude;
    

    public function mount(MeasurementDevice $measurementDevice){

        //dd($category);
                // Pobierz tylko użytkowników z rolą serwisanta
        $maintainers = User::whereHas('roles', function($query) {
            $query->where('name', 'MAINTEINER');
        })
        ->select('id', 'name')
        ->get()
        ->map(fn($u) => [
            'label' => $u->name,
            'value' => $u->id,
        ]);

        $this->parameters = Parameter::query()
        ->get()
        ->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
        ]);


        $this->maintainers = $maintainers;


            if (isset($measurementDevice->id)) {
                $this->measurementDevice =$measurementDevice;
                $this->id = $measurementDevice->id;
                $this->name = $measurementDevice->name;
                $this->model = $measurementDevice->model;
                $this->serial_number = $measurementDevice->serial_number;
                $this->calibration_date = $measurementDevice->calibration_date;
                $this->next_calibration_date = $measurementDevice->next_calibration_date;
                $this->status = $measurementDevice->status;

                $params = Parameter::query()
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                ])
                ->toArray();
                

                $this->parameters= $params;

                $this->parameter_ids = json_decode($measurementDevice->parameter_ids);


                $this->user_id = $measurementDevice->user_id;
                $this->description = $measurementDevice->description;

                $this->latitude = $measurementDevice->latitude;
                $this->longitude = $measurementDevice->longitude;

            }
        
    }


    

    public function submit()
    {
        // if (isset($this->measurementDevice->id)) {
        //     $this->authorize('update', $this->measurementDevice);
        // } else {
        //     $this->authorize('create', MeasurementDevice::class);
        // }


        $this->measurementDevice->addStatusHistory($this->status, 'Zmiana statusu przez formularz edycji');




        $this->parameter_ids = json_encode($this->parameter_ids);


        MeasurementDevice::updateOrCreate(
            ['id' => $this->id],
            $this->validate()
        );

        


        return redirect()->route('measurement-devices.index')
            ->with('success', 'Urządzenie zaktualizowane pomyślnie');
    }

    public function open_parameters()
    {
        //  $this->dispatch('showToast', type: 'success', message: 'Urządzenie zostało usunięte');
    }

    public function work_again($lat, $lng)
    {

        $this->latitude=$lat;
        $this->longitude=$lng; 

    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'unique:measurement_devices,name'.
                    (isset($this->measurementDevice->id) ? (','.$this->measurementDevice->id) : ''),
            ],
            'model' => [
            'required',
            'string',
            'min:2',

            ],

            'serial_number' => [
                'required',
                'string',
                'min:2',

            ],

            'calibration_date' => [
                'date',
            ],
            'next_calibration_date' => [
                'date',
            ],

            'parameter_ids' => [
                'json',
            ],
            'user_id' => [
                'required',
                'integer',
            ],
            'status' => [
                'required',
                'string',
            ],
            'latitude' => [
                'numeric',
            ],
            'longitude' => [
                'numeric',
            ],
            


        ];
    }

    public function validationAttributes()
    {
        return [

        ];
    }



    public function render()
    {
        return view('livewire.measurement-devices.measurement-device-form');
    }
}
