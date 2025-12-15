<?php

namespace App\Livewire\Data;

use App\Models\Value;
use Livewire\Component;
use App\Models\Parameter;
use App\Models\Measurement;
use App\Models\MeasurementDevice;
use LaravelDaily\LaravelCharts\Classes\LaravelChart;

class ChartForm extends Component
{
    public $devices;

    public $device_ids;

    public $start_date;

    public $end_date;

    public $from;

    public $to;

    public $labels=null;


    public $readonly=null;

    public $parameters=null;

    public $device_id=null;
    public $data1=null;
    public $data2=null;
    public $data3=null;
    public $data4=null;
    public $data5=null;
    public $data6=null;



    public function mount($device_id = null)
    {
        $this->device_id=$device_id;

    

        $this->start_date = '2023-01-01';
        $this->end_date = '2024-01-10';

        ChartForm::update_chart();
   


    }
    public function updated()
    {
    //  $this->dispatch('data_update');
        

    }

    public function update_chart()
    {

     $data1=collect();
     $data2=collect();
     $data3=collect();
     $data4=collect();
     $data5=collect();
     $data6=collect();

        // $param_array =array(&$data1,&$data2,&$data3,&$data4,&$data5,&$data6);
        $param_array =collect();


        $param = MeasurementDevice::query()
        ->select(
        [
            'measurement_devices.parameter_ids',
         ]
         )
        ->where('id','=',$this->device_id)
        ->get()->toArray();

        
        $m_ids=Measurement::query()
        ->select(
            [
                'measurements.id',
            ]
        )->where('device_id','=',$this->device_id)->get()->toArray();
        $param=$param[0];
        $param=json_decode($param["parameter_ids"]);

        

        for ($i=0; $i < 7; $i++) { 
        

            for ($j=0; $j < count($param); $j++) { 
                
                if($param[$j]==$i)
                {
                    
                    $pp =Value::query()
                    ->select(
                    [
                        'values.created_at as date',
                        'values.value as var',
                        'values.parameter_id',
                        'values.measurement_id',
                    ]
                    )
                    ->where('parameter_id','=',$i)
                    ->whereIn('measurement_id',$m_ids)
                    ->WhereBetween("values.created_at",[$this->start_date,$this->end_date])
                    ->orderBy('date')
                    ->pluck('var', 'date');

                    $param_array->push($pp);
                }else{
                    continue;
                }  
                
            }
        }

        

        $chart_data = Value::query()
        ->select(
        [
            'values.created_at as date',
            'values.value as var',
            'values.parameter_id',
            'values.measurement_id',
         ]
         )
        ->where('parameter_id','=',3)
        ->whereIn('measurement_id',$m_ids)
        ->WhereBetween("values.created_at",[$this->start_date,$this->end_date])
        ->orderBy('date')
        ->pluck('var', 'date');

        
       


        $this->labels = $chart_data->keys();

    

        if($param_array[0]!=null){
            $this->data1 = $param_array[0]->values();
        }else{
            $this->data1=null;
        }
        if($param_array[1]!=null){
            $this->data2 = $param_array[1]->values();
        }else{
            $this->data2=null;
        }
        if($param_array[2]!=null){
            $this->data3 = $param_array[2]->values();
        }else{
            $this->data3=null;
        }
        if($param_array[3]!=null){
            $this->data4 = $param_array[3]->values();
        }else{
            $this->data4=null;
        }
        if($param_array[4]!=null){
            $this->data5 = $param_array[4]->values();
        }else{
            $this->data5=null;
        }
        if($param_array[5]!=null){
            $this->data6 = $param_array[5]->values();
        }else{
            $this->data6=null;
        }

        // $this->data1 = $data1->values();
        // $this->data2 = $data2->values();
        // $this->data3 = $data2->values();
        // $this->data4 = $data2->values();
        // $this->data5 = $data2->values();
        // $this->data6 = $data2->values();

    }




    public function check_date()
    {
        if ($this->start_date == null) {
            $this->from = '1900-01-01';

        } else {
            $this->from = $this->start_date;
        }

        if ($this->end_date == null) {
            $this->to = '3000-01-01';
        } else {
            $this->to = $this->end_date;
        }

    }

    public function render()
    {
        return view('livewire.data.chart-form');
    }
}
