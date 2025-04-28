<?php

namespace App\Livewire\Data;

use App\Models\Value;
use Livewire\Component;
use App\Models\Measurement;
use App\Models\MeasurementDevice;
use Livewire\Attributes\Validate;
use PowerComponents\LivewirePowerGrid\Components\Exports\Export;

class ExportForm extends Component
{

    public $devices;
    public $device_ids;

    public $start_date;
    public $end_date;
    
    public function mount()
    {

        
        // $devices = MeasurementDevice::query()->get();

        // $this->devices=$devices;


    }

    public function submit()
    {

        
        if($this->start_date==null){
            $from ='1900-01-01';  
           
        }else{
            $from = $this->start_date;
        }

        if($this->end_date==null){
            $to = '3000-01-01';
        }else{
            $to = $this->end_date;
        }


        $device_ids= $this->device_ids;

        if($device_ids !=null){
            $d=array();
            foreach($device_ids as $device){
                array_push($d, $device);
            }
            $device_ids=$d;

        }else{
            $device_ids=array();
            array_push($device_ids, 0);
        }
        //  dd($device_ids);

        

        $measurements = Measurement::query()
        ->join('measurement_devices', function ($m) {
            $m->on('measurement_devices.id', '=', 'measurements.device_id');
        })
        ->select(
            'measurements.id',
            'measurements.device_id', 
            'measurements.measurements_date'         
        )
        ->whereBetween('measurements_date', [$from, $to])
        ->whereIn('measurements.device_id', $device_ids)
        ->get()
        ;



        return $this->redirect(route('data.file', ['start_date'=>$from, 'end_date' =>$to, 'device_ids' => json_encode($device_ids)]));
    }

    

    public function render()
    {
        return view('livewire.data.export-form');
    }



}
