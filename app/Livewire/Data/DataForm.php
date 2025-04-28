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

    public $extension;

    public $file=null;

    public $devices_data=null;
    public $filename=null;


    public function mount( ){

    }


    //Triggeruje się gdy plik zostanie wysłany do formularza / Zostanie cokolwiek wysłane do formularza lub zaznaczone w formularzu
    public function updated()
    {
        $extension=$this->file->getClientOriginalExtension();
        $this->extension=$extension;
    


        //Przetwarzanie pliku na tablicę. Plik zapisywany jest w storage/app/private/files 
        $filename = "files/" . $this->file->getClientOriginalName();
        $this->file->storeAs(path: 'files', name: $this->file->getClientOriginalName());
        $this->filename=$filename;


        if($extension=='json'){
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
        }elseif ($extension=='csv') {

            $preview="";
            $csv = array_map('str_getcsv', file(Storage::path($filename)) );
            ini_set('max_execution_time', '300');

            //headers
            $hh = implode(';', $csv[0]);
            $headers = explode(';', $hh);

            for ($i = 1; $i < count($csv); $i++) {
                $cc = implode(';', $csv[$i]);
                $c = explode(';', $cc);



                $preview = $preview . "id: " . $i ."<br>";
                $preview = $preview . "device_id: " . $c[0] . "<br>";
                $preview = $preview . "date_time: " . $c[1] . "<br>";

                for ($j = 2; $j < count($headers); $j++) {
                    $preview = $preview."&nbsp;&nbsp;&nbsp;". $headers[$j] .": " .$c[$j] . "<br>";
                }

                $preview = $preview. "<br><br>";
               
            }
            ini_set('max_execution_time', '30');
            $this->meaurements=$preview;
            
        }

    }

    private function return_database_headers(array $array){
        $headers = array();

        for ($i = 0; $i < count($array); $i++) {
            switch ($array[$i]) {
                case 'devid':
                    array_push($headers, 'devid');
                    break;
                case 'created_at':
                    array_push($headers, 'created_at');
                    break;
                case 'PM1':
                    array_push($headers, 1);
                    break;
                case 'PM2_5':
                    array_push($headers, 2);
                    break;
                case 'PM10':
                    array_push($headers, 3);
                    break;
                case 'Humidity':
                    array_push($headers, 4);
                    break;
                case 'Pressure':
                    array_push($headers, 5);
                    break;
                case 'Temperature':
                    array_push($headers, 6);
                    break;            
                
                default:
                    dd('Coś jest nie tak w nagłówkami w wczytanym pliku');
                    break;
            }
        }
        return $headers;

    } 

    public function submit()
    {
        if($this->extension == 'json' or $this->extension == 'csv'  and $this->file!=null) {
    
            if($this->extension == 'json'){

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

                                    ]
                                );
                    Value::firstOrCreate(
                        [
                                    'measurement_id' => $measurement_id,
                                    'parameter_id' => 2, 
                                    'value' => $parameters["pm2_5"],

                                    ]
                                );

                    Value::firstOrCreate(
                        [
                                    'measurement_id' => $measurement_id,
                                    'parameter_id' => 3, 
                                    'value' => $parameters["pm10"],

                                    ]
                                );
                                
                    Value::firstOrCreate(
                        [
                                    'measurement_id' => $measurement_id,
                                    'parameter_id' => 4, 
                                    'value' => $parameters["humidity"],

                                    ]
                                );
                    Value::firstOrCreate(
                        [
                                    'measurement_id' => $measurement_id,
                                    'parameter_id' => 5, 
                                    'value' => $parameters["pressure"],

                                    ]
                                );
                                            
                    Value::firstOrCreate(
                        [
                                    'measurement_id' => $measurement_id,
                                    'parameter_id' => 6, 
                                    'value' => $parameters["temperature"],

                                    ]
                                );     
                                
                }
            }else{
                $csv = array_map('str_getcsv', file(Storage::path($this->filename)) );
                ini_set('max_execution_time', '300');
    
                //headers
                $hh = implode(';', $csv[0]);
                $headers = explode(';', $hh);
                $headers = DataForm::return_database_headers($headers);

               
    
                for ($i = 1; $i < count($csv); $i++) {
                    $cc = implode(';', $csv[$i]);
                    $c = explode(';', $cc);

                    //dd($c, $headers);

                    $date = date_create($c[1]);
                    $date = date_format($date,"Y-m-d H:i:s");

                    $m =Measurement::firstOrCreate(
                        [
                                        'measurements_date' =>$date,
                                        'device_id' =>trim($c[0],"DEV00"), 
                                    ]
                                );
                                
                    $measurement_id= $m->id;
    
                    for ($j = 2; $j < count($headers); $j++) {
                        Value::firstOrCreate(
                            [
                                        'measurement_id' => $measurement_id,
                                        'parameter_id' => $headers[$j], 
                                        'value' => $c[$j],
                                        ]
                                    );     
                    }
                }


                

                    
            }


       //dd($this);
        // if (isset($this->product->id)) {
        //     $this->authorize('update', $this->product);
        // } else {
        //     $this->authorize('create', MeasurementDevice::class);
        // }

        return $this->redirect(route('measurement-devices.index'));
        }
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
