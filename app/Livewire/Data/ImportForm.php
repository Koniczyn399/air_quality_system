<?php

namespace App\Livewire\Data;

use App\Models\Value;
use Livewire\Component;
use App\Models\Measurement;
use Livewire\WithFileUploads;
use WireUi\Traits\WireUiActions;
use App\Models\MeasurementDevice;
use Illuminate\Support\Facades\Storage;


class ImportForm extends Component
{
    use WireUiActions;
    use WithFileUploads;

    public $devices;

    public $device_ids;

    public $meaurements = '';

    public $extension;

    public $file = null;

    public $devices_data = null;
    public $data_headers = null;
    public $data_headers_2 = null;

    public $filename = null;

    public function mount() {}

    // Triggeruje się gdy plik zostanie wysłany do formularza / Zostanie cokolwiek wysłane do formularza lub zaznaczone w formularzu
    public function updated()
    {
        $extension = $this->file->getClientOriginalExtension();
        $this->extension = $extension;

        // Przetwarzanie pliku na tablicę. Plik zapisywany jest w storage/app/private/files
        $filename = 'files/'.$this->file->getClientOriginalName();
        $this->file->storeAs(path: 'files', name: $this->file->getClientOriginalName());
        $this->filename = $filename;

    

        if ($extension == 'json') {
            $devices_data = Storage::json($filename);
            $this->devices_data = $devices_data;

            // Wrzucanie wszystkich danych z pliku w jednego Stringa
            $preview = '';
            foreach ($devices_data as $d) {

                $preview = $preview.'id: '.$d['id'].'<br>';
                $preview = $preview.'device_id: '.$d['device_id'].'<br>';
                $preview = $preview.'date_time: '.$d['date_time'].'<br>';

                $parameters = $d['parameters'];

                $preview = $preview.'&nbsp;&nbsp;&nbsp;'.'pm1: '.$parameters['pm1'].'<br>';
                $preview = $preview.'&nbsp;&nbsp;&nbsp;'.'pm2_5: '.$parameters['pm2_5'].'<br>';
                $preview = $preview.'&nbsp;&nbsp;&nbsp;'.'pm10: '.$parameters['pm10'].'<br>';
                $preview = $preview.'&nbsp;&nbsp;&nbsp;'.'humidity: '.$parameters['humidity'].'<br>';
                $preview = $preview.'&nbsp;&nbsp;&nbsp;'.'pressure: '.$parameters['pressure'].'<br>';
                $preview = $preview.'&nbsp;&nbsp;&nbsp;'.'temperature: '.$parameters['temperature'].'<br>';

                $preview = $preview.'<br><br>';

            }

            $this->meaurements = $preview;
        } elseif ($extension == 'csv') 
        {

            //$csv = array_map('str_getcsv', file(Storage::path($filename)));
            ini_set('max_execution_time', '30000');

           
            
            // 1. Split by new line. Use the PHP_EOL constant for cross-platform compatibility.
            $csv=file(Storage::path($filename));
            //dd($csv);

        
            // 2. Extract the header and convert it into a Laravel collection.
            $header = collect(explode(';',str_replace("\r","",str_replace("\n","",array_shift($csv))) ) );

            //dd($header);
            
            // 3. Convert the rows into a Laravel collection.
            $rows = collect($csv);
            //dd($rows);
        

            // 4. Map through the rows and combine them with the header to produce the final collection.
            $data = $rows->map(
                fn($row) => $header->combine((explode(';',str_replace("\r","",str_replace("\n","",$row))) ) )
            
            );

            //dd($data[0]);

            $preview = "<bold>Wczytane pomiary</bold><br>";
           
            for ($head=0; $head < count($header); $head++) { 
                $preview = $preview . $header[$head]." ";
            }
            $preview = $preview ."<br>";
            foreach ($data as $d) {
                for ($i=0; $i < count($header); $i++) { 
                     $preview = $preview . $d[$header[$i]]. " | ";
                   
                }
                $preview = $preview ."<br>";
                 
               
            }
            

            // $this->dispatch('add_device', [
            //         'id' => $d,
            //         'confirm' => [
            //             'title' => 'Zapytanie o urządzenie',
            //             'description' => 'Nie znaleziono urządzenia pod podane parametry. Chcesz utworzyć nowe ?',
            //             'accept' => [
            //                 'label' => 'Tak, dodaj',
            //                 'method' => 'delete',
            //                 'params' => ['measurement_device' => $d],
            //             ],
            //             'reject' => [
            //                 'label' => 'Anuluj',
            //             ],
            //         ],
            //     ]);



            //Stara, wolna logika

            // $hh = implode(';', $csv[0]);
            // $headers = explode(';', $hh);
            // $imported_device_ids=array();

            // for ($i = 1; $i < count($csv); $i++) {
            //     $cc = implode(';', $csv[$i]);
            //     $c = explode(';', $cc);

            //     $preview = $preview.'id: '.$i.'<br>';
            //     $preview = $preview.'device_id: '.$c[0].'<br>';
            //     $imported_device_ids[]=$c[0];
                
            //     $preview = $preview.'date_time: '.$c[1].'<br>';

            //     for ($j = 2; $j < count($headers); $j++) {
            //         $preview = $preview.'&nbsp;&nbsp;&nbsp;'.$headers[$j].': '.$c[$j].'<br>';
            //     }

            //     $preview = $preview.'<br><br>';

            // }
            // ini_set('max_execution_time', '30');


           

         

            $compatibile_devices = ImportForm::check_devices($header);

            $device_info="";
            if(!empty($compatibile_devices) ){
                $device_info="<bold>Kompatybilne urządzenia<bold><br>";
                foreach($compatibile_devices as $device)
                {
                    $device_info= $device_info."ID: " .$device->id ."  ". $device->name."<br>";

                }
            $device_info= $device_info. "<br>";
            }


            $this->meaurements = $device_info . $preview;
            $this->devices_data = $data;

                
        }

    }


    private function check_devices($header){


        $header=$header->toArray();
        array_splice($header, 0, 2);
       


        //Sprawdzenie czy są kompatybilne urządzenia do importowanych danych

        $measurements_devices = MeasurementDevice::query()
        ->select(
            'measurement_devices.id',
            'measurement_devices.name',
            'measurement_devices.parameter_ids',
        )
        ->get();
        

        $parameter_names=array("PM1","PM2_5","PM10", "Humidity","Pressure","Temperature" );


        
        $dh=array();
        $dh_2=array();
        for ($h=0; $h < count($parameter_names); $h++) { 
            for ($h2=0; $h2 < count($header); $h2++) { 

                if($header[$h2]==$parameter_names[$h]){
                    $dh[]=$h+1;
                    $dh_2[]=$parameter_names[$h];
                }
            }
            
        }
        $this->data_headers=$dh;
        $this->data_headers_2=$dh_2;


        $compatibile_devices=array();
       

        foreach ($measurements_devices as $key) {
            $query_parameter_ids = json_decode($key->parameter_ids);
            

            $readed_parameters=array();
           
            for ($ii=0; $ii < count($query_parameter_ids); $ii++)
            { 
                
                $readed_parameters[]= $parameter_names[$query_parameter_ids[$ii]-1];
            }
            
            
        
            //Jeśli match będzie true po tej pętli to znaczy że urządzenie jest kompatybilne
           
            $match_counter=0;
            for ($i=0; $i < count($readed_parameters); $i++) { 
                for ($j=0; $j < count($header); $j++) {
                    if($readed_parameters[$i]==$header[$j]){
                      
                        $match_counter++;
                        break;
                        
                    }

                }
            }
            if($match_counter==count($header)){
                $compatibile_devices[] = $key;
            }
            
        }
       
       return $compatibile_devices;
    }



    // private function return_database_headers(array $array)
    // {
    //     $headers = [];

    //     for ($i = 0; $i < count($array); $i++) {
    //         switch ($array[$i]) {
    //             case 'devid':
    //                 array_push($headers, 'devid');
    //                 break;
    //             case 'created_at':
    //                 array_push($headers, 'created_at');
    //                 break;
    //             case 'PM1':
    //                 array_push($headers, 1);
    //                 break;
    //             case 'PM2_5':
    //                 array_push($headers, 2);
    //                 break;
    //             case 'PM10':
    //                 array_push($headers, 3);
    //                 break;
    //             case 'Humidity':
    //                 array_push($headers, 4);
    //                 break;
    //             case 'Pressure':
    //                 array_push($headers, 5);
    //                 break;
    //             case 'Temperature':
    //                 array_push($headers, 6);
    //                 break;

    //             default:
    //                 dd('Coś jest nie tak w nagłówkami w wczytanym pliku');
    //                 break;
    //         }
    //     }

    //     return $headers;

    // }

    public function submit()
    {
        if ($this->extension == 'json' or $this->extension == 'csv' and $this->file != null) {

            if ($this->extension == 'json') {

                foreach ($this->devices_data as $d) {

                    $parameters = $d['parameters'];

                    $m = Measurement::firstOrCreate(
                        [
                            'measurements_date' => $d['date_time'],
                            'device_id' => $d['device_id'],
                        ]
                    );

                    $measurement_id = $m->id;

                    Value::firstOrCreate(
                        [
                            'measurement_id' => $measurement_id,
                            'parameter_id' => 1,
                            'value' => $parameters['pm1'],

                        ]
                    );
                    Value::firstOrCreate(
                        [
                            'measurement_id' => $measurement_id,
                            'parameter_id' => 2,
                            'value' => $parameters['pm2_5'],

                        ]
                    );

                    Value::firstOrCreate(
                        [
                            'measurement_id' => $measurement_id,
                            'parameter_id' => 3,
                            'value' => $parameters['pm10'],

                        ]
                    );

                    Value::firstOrCreate(
                        [
                            'measurement_id' => $measurement_id,
                            'parameter_id' => 4,
                            'value' => $parameters['humidity'],

                        ]
                    );
                    Value::firstOrCreate(
                        [
                            'measurement_id' => $measurement_id,
                            'parameter_id' => 5,
                            'value' => $parameters['pressure'],

                        ]
                    );

                    Value::firstOrCreate(
                        [
                            'measurement_id' => $measurement_id,
                            'parameter_id' => 6,
                            'value' => $parameters['temperature'],

                        ]
                    );

                }
            } else {

            //Stara, wolna logika

            //     $csv = array_map('str_getcsv', file(Storage::path($this->filename)));
            //     ini_set('max_execution_time', '300');

            //     // headers
            //     $hh = implode(';', $csv[0]);
            //     $headers = explode(';', $hh);
            //     $headers = DataForm::return_database_headers($headers);

            //     for ($i = 1; $i < count($csv); $i++) {
            //         $cc = implode(';', $csv[$i]);
            //         $c = explode(';', $cc);

            //         // dd($c, $headers);

            //         $date = date_create($c[1]);
            //         $date = date_format($date, 'Y-m-d H:i:s');

            //         $m = Measurement::firstOrCreate(
            //             [
            //                 'measurements_date' => $date,
            //                 'device_id' => trim($c[0], 'DEV00'),
            //             ]
            //         );

            //         $measurement_id = $m->id;

            //         for ($j = 2; $j < count($headers); $j++) {
            //             Value::firstOrCreate(
            //                 [
            //                     'measurement_id' => $measurement_id,
            //                     'parameter_id' => $headers[$j],
            //                     'value' => $c[$j],
            //                 ]
            //             );
            //         }
            //     }
            // }

            //dd($this->devices_data[0]);
            foreach ($this->devices_data as $value) {

                //dd($value, $this->data_headers_2, $this->data_headers);
                $headers=$this->data_headers_2;
                $p_id=$this->data_headers;

                $date = date_create($value["created_at"]);
                $date = date_format($date, 'Y-m-d H:i:s');

                    
                $m = Measurement::firstOrCreate(
                    [
                        'measurements_date' => $date,
                        'device_id' => trim($value["devid"], 'DEV00'),
                    ]
                );

                $measurement_id = $m->id;

                for ($j = 0; $j < count($headers); $j++) {
                    
                    Value::firstOrCreate(
                        [
                            'measurement_id' => $measurement_id,
                            'parameter_id' => $p_id[$j],
                            'value' => $value[$headers[$j]],
                        ]
                    );
                }

            }
           



            // dd($this);
            // if (isset($this->product->id)) {
            //     $this->authorize('update', $this->product);
            // } else {
            //     $this->authorize('create', MeasurementDevice::class);
            // }

            return $this->redirect(route('measurement-devices.index'));
            }
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
        return view('livewire.data.import-form');
    }
}
