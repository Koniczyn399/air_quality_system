<?php

namespace App\Livewire\Data;

use App\Models\Measurement;
use App\Models\Value;
use Livewire\Component;
use Livewire\WithFileUploads;
use WireUi\Traits\WireUiActions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DataForm extends Component
{

    use WithFileUploads;
    use WireUiActions;



    public $meaurements = "";


    public $file=null;

    public $devices_data=null;


    public function mount( ){

    }


    //Triggeruje się gdy plik zostanie wysłany do formularza / Zostanie cokolwiek wysłane do formularza lub zaznaczone w formularzu
    public function updated()
    {

        //Przetwarzanie pliku na tablicę. Plik zapisywany jest w storage/app/private/files 
        $filename = "files/" . $this->file->getClientOriginalName();
        $this->file->storeAs(path: 'files', name: $this->file->getClientOriginalName());
        $devices_data = Storage::json( $filename);
        $this->devices_data=$devices_data;



        //Wrzucanie wszystkich danych z pliku w jednego Stringa
        $preview="";
        foreach($devices_data as $d){

            $preview = $preview . "id: " . $d["id"] ."<br>";
            $preview = $preview . "device_id: " . $d["device_id"] . "<br>";
            $preview = $preview . "date_time: " . $d["date_time"] . "<br>";


            $parameters = $d["parameters"];

            $preview = $preview."&nbsp;&nbsp;&nbsp;"."pm1: " .$parameters["pm1"] . "<br>";
            $preview = $preview."&nbsp;&nbsp;&nbsp;"."pm2_5: " .$parameters["pm2_5"]. "<br>";    
            $preview = $preview."&nbsp;&nbsp;&nbsp;"."pm10: " .$parameters["pm10"] . "<br>";
            $preview = $preview."&nbsp;&nbsp;&nbsp;"."humidity: " .$parameters["humidity"]. "<br>";
            $preview = $preview."&nbsp;&nbsp;&nbsp;"."pressure: " .$parameters["pressure"]. "<br>";
            $preview = $preview."&nbsp;&nbsp;&nbsp;"."temperature: " .$parameters["temperature"]. "<br>";

            $preview = $preview. "<br><br>";

        }

        $this->meaurements=$preview;

    }

    public function submit()
    {
    

    foreach($this->devices_data as $d){

        $parameters = $d["parameters"];

        
        $m =Measurement::firstOrCreate(
            [
                            'measurements_date' =>$d["date_time"],
                            'device_id' =>$d["device_id"],
                        ]
                    );
                    
        $measurement_id= $m->id;

                    
        Value::firstOrCreate(
            [
                        'measurement_id' => $measurement_id,
                        'parameter_id' => 1, 
                        'value' => $parameters["pm1"],
                        'created_at' => $d["date_time"],
                        ]
                    );
        Value::firstOrCreate(
            [
                        'measurement_id' => $measurement_id,
                        'parameter_id' => 2, 
                        'value' => $parameters["pm2_5"],
                        'created_at' => $d["date_time"],
                        ]
                    );

        Value::firstOrCreate(
            [
                        'measurement_id' => $measurement_id,
                        'parameter_id' => 3, 
                        'value' => $parameters["pm10"],
                        'created_at' => $d["date_time"],
                        ]
                    );
                    
        Value::firstOrCreate(
            [
                        'measurement_id' => $measurement_id,
                        'parameter_id' => 4, 
                        'value' => $parameters["humidity"],
                        'created_at' => $d["date_time"],
                        ]
                    );
        Value::firstOrCreate(
            [
                        'measurement_id' => $measurement_id,
                        'parameter_id' => 5, 
                        'value' => $parameters["pressure"],
                        'created_at' => $d["date_time"],
                        ]
                    );
                                
        Value::firstOrCreate(
            [
                        'measurement_id' => $measurement_id,
                        'parameter_id' => 6, 
                        'value' => $parameters["temperature"],
                        'created_at' => $d["date_time"],
                        ]
                    );     
                    


        }

        




       //dd($this);
        // if (isset($this->product->id)) {
        //     $this->authorize('update', $this->product);
        // } else {
        //     $this->authorize('create', MeasurementDevice::class);
        // }

        return $this->redirect(route('measurement-devices.index'));
    }

    // public function rules()
    // {
    //     return [

    //         'product_name' => [
    //             'required',
    //             'string',
    //             'min:2',
    //         ],
            
    //         'price' => [
    //             'required',
    //             'string',
    //             'min:1',
    //         ],

    //         'unit' => [
    //             'required',
    //             'string',
    //             'min:1',
    //         ],
    //         'amount' => [
    //             'required',
    //             'string',
    //             'min:1',
    //         ],
    //         'description' => [
    //             'required',
    //             'string',
    //             'min:2',
    //         ],
    //     ];
    // }

    // public function validationAttributes()
    // {
    //     return [
    //         'product_name' => Str::lower(__('services.attributes.product_name')),

    //     ];
    // }



    public function render()
    {
        return view('livewire.data.data-form');
    }
}
