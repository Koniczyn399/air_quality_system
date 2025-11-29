<?php

namespace App\Livewire\Data;

use App\Models\Value;
use Livewire\Component;
use App\Models\Measurement;
use Livewire\WithFileUploads;
use WireUi\Traits\WireUiActions;
use App\Models\MeasurementDevice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class ImportForm extends Component
{
    use WireUiActions;
    use WithFileUploads;

    public $devices;

    public $device_ids=null;

    public $meaurements = '';

    public $extension;

    public $file = null;

    public $devices_data = null;
    public $data_headers = null;
    public $data_headers_2 = null;

    public $filename = null;

    public $new_device_headers = null;
    public $eligible_devices =null;

    public function mount() {}

    // Triggeruje się gdy plik zostanie wysłany do formularza / Zostanie cokolwiek wysłane do formularza lub zaznaczone w formularzu
    public function updated()
    {
        if($this->file!=null){
        
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

            
                $csv=file(Storage::path($filename));
                $header = collect(explode(';',str_replace("\r","",str_replace("\n","",array_shift($csv))) ) );
                $rows = collect($csv);
                $data = $rows->map(
                    fn($row) => $header->combine((explode(';',str_replace("\r","",str_replace("\n","",$row))) ) )
                
                );

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
            

                $compatibile_devices = ImportForm::check_devices($header);


                if (empty($compatibile_devices)){
                    $this->dispatch('add_device');
                }
                
                $this->eligible_devices=collect($compatibile_devices);
               

                

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

    }

    
    #[\Livewire\Attributes\On('add_device_confirmed')]
    public function add_device_confirmed(): void
    {
        $this->redirect(route('measurement-devices.create_new',['headers'=>json_encode($this->new_device_headers)]));
        
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


        $this->new_device_headers= $dh;            
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

                $headers=$this->data_headers_2;
                $p_id=$this->data_headers;
                $measurement_array=[];
                $start_id=0;
                $values_array=[];

                if($this->device_ids!=null)
                {
                    $selected_device=$this->device_ids;
                    foreach ($this->devices_data as $value) {

                    $date = date_create($value["created_at"]);
                    $date = date_format($date, 'Y-m-d H:i:s');

                        
                    $measurement_array[]=[
                        'measurements_date' => $date,
                        'device_id' => $selected_device,
                        'created_at'=>now(),
                        'updated_at'=>now()
                    ];
                }
                    
                }else{

                    foreach ($this->devices_data as $value) {

                    $date = date_create($value["created_at"]);
                    $date = date_format($date, 'Y-m-d H:i:s');

                        
                    $measurement_array[]=[
                        'measurements_date' => $date,
                        'device_id' => trim($value["devid"], 'DEV00'),
                        'created_at'=>now(),
                        'updated_at'=>now()
                    ];
                }
                }

                

                


                $inc=Measurement::query()->select([
                    'measurements.id'
                ])->get()->toArray();
                $inc=count($inc);
                if($inc!=0){
                    $start_id=$inc;
                }
                
                Measurement::insert($measurement_array);
               

                foreach ($this->devices_data as $d) {


                    for ($j = 0; $j < count($headers); $j++) {

                        $created_at = date_create($d["created_at"]);
                        $created_at = date_format($created_at, 'Y-m-d H:i:s');

                        $values_array[]=[
                            'measurement_id'=>$start_id,
                            'parameter_id' => $p_id[$j],
                            'value' => $d[$headers[$j]],
                            'created_at'=>$created_at,
                            'updated_at'=>now(),
                        ];
                        $start_id++;
                    }

                }
                

                
                foreach (array_chunk($values_array, 1000) as $chunk) {
                    Value::insert($chunk);
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
