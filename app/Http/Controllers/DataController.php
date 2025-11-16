<?php

namespace App\Http\Controllers;

use App\Models\Measurement;
use App\Models\MeasurementDevice;
use App\Models\MeasurementHistory;
use App\Models\Parameter;
use App\Models\User;
use App\Models\Value;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\VarExporter\Internal\Values;

class DataController extends Controller
{
    public function index()
    {

        // $this->authorize('viewAny', User::class);

        return view(
            'measurements.index',
            [
                'measurements' => Measurement::paginate(
                    config('pagination.default')
                ),

            ]
        );

    }

    public function show(Measurement $measurement)
    {

        // $this->authorize('viewAny', User::class);

        $values = Value::query()
            ->join('parameters', function ($parameters) {
                $parameters->on('values.parameter_id', '=', 'parameters.id');
            })
            ->select([
                'values.id',
                'values.measurement_id',
                'values.value',
                'parameters.name as parameter_name',
                'parameters.unit as unit_name',
            ])
            ->where('values.measurement_id', '=', $measurement->id)->get();

        return view(
            'measurements.show',
            [
                'measurement' => $measurement,
                'values' => $values,
            ]
        );

    }

    public function upload()
    {
        // $this->authorize('viewAny', User::class);

        return view(
            'data.form'

        );

    }

    public function export()
    {
        // $this->authorize('viewAny', User::class);

        return view(
            'data.export.form'

        );

    }

    public function import()
    {
        // $this->authorize('viewAny', User::class);

        return view(
            'data.import.form'

        );

    }

    // https://medium.com/@kanishkanuwanperera/building-a-csv-import-export-feature-in-laravel-10-step-by-step-guide-with-no-external-packages-6b73295aa0c1
    public function file($start_date, $end_date, $device_ids)
    {

        $device_ids = json_decode($device_ids);
        $measurements=Measurement::query()->get();

      

        if(count($device_ids) == 1 && $device_ids[0]==0)
        {
            $measurements = Measurement::query()
            ->join('measurement_devices', function ($m) {
                $m->on('measurement_devices.id', '=', 'measurements.device_id');
            })
            ->select(
                'measurements.id',
                'measurements.device_id',
                'measurements.measurements_date'
            )
            ->whereBetween('measurements_date', [$start_date, $end_date])
            ->get();
        }else{
        $measurements = Measurement::query()
            ->join('measurement_devices', function ($m) {
                $m->on('measurement_devices.id', '=', 'measurements.device_id');
            })
            ->select(
                'measurements.id',
                'measurements.device_id',
                'measurements.measurements_date'
            )
            ->whereBetween('measurements_date', [$start_date, $end_date])
            ->whereIn('measurements.device_id', $device_ids)
            ->get();
        }


        $filename = 'data.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];


        dd($start_date, $end_date, $device_ids, $measurements);


        $callback = function () use ($measurements) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'devid',
                'created_at',
                'Temperature',
                'Pressure',
                'Humidity',
                'PM1',
                'PM2_5',
                'PM10',
            ],
                ';'
            );

            $values = Value::query()->get();

            foreach ($measurements as $measurement) {

                $chosen = [];
                foreach ($values as $value) {
                    if ($value->measurement_id == $measurement->id) {
                        $chosen[] = $value;
                    }
                }
                $data = [
                    'DEV00'.$measurement->device_id,
                    $measurement->measurements_date,
                    ' '.number_format(DataController::parameter_value($chosen, 6), 1),
                    ' '.number_format(DataController::parameter_value($chosen, 5), 1),
                    ' '.number_format(DataController::parameter_value($chosen, 4), 1),
                    ' '.number_format(DataController::parameter_value($chosen, 1), 1),
                    ' '.number_format(DataController::parameter_value($chosen, 2), 1),
                    ' '.number_format(DataController::parameter_value($chosen, 3), 1),
                ];

                fputcsv($handle, $data, ';');
            }

        };

        return response()->stream($callback, 200, $headers);
    }

    public function parameter_value($array, int $chosen_id)
    {
        $value = ' ';
        foreach ($array as $one) {
            if ($one->parameter_id == $chosen_id) {
                $value = $one->value;
                break;
            }

        }

        return $value;
    }

    public function system_report($start_date, $end_date)
    {
        // $measurements = Measurement::query()
        // ->join('measurement_devices', function ($m) {
        //     $m->on('measurement_devices.id', '=', 'measurements.device_id');
        // })
        // ->select(
        //     'measurements.id',
        //     'measurement_devices.name as device_name',
        //     'measurements.measurements_date',
        //     'measurements.created_at',
        // )
        // ->get();

        $m = Measurement::count();
        $u = User::count();
        $md = MeasurementDevice::count();
        $p = Parameter::query()->get();
        // dd($p);

        $pdf = Pdf::loadView('pdfs.pdf', [
            'm' => $m,
            'u' => $u,
            'md' => $md,
            'p' => $p,

        ]);

        return $pdf->download();
    }

    public function device_report($start_date, $end_date, $device_ids)
    {

        $device_ids = json_decode($device_ids);
        //dd($device_ids);

        if(count($device_ids) == 1 && $device_ids[0]==0)
        {
            $devices_history = MeasurementHistory::query()
            ->join('measurement_devices', function ($m) {
                $m->on('measurement_devices.id', '=', 'measurement_histories.measurement_device_id');
            })
            ->select(
                'measurement_histories.id',
                'measurement_devices.name as device_name',
                'measurement_histories.status',
                'measurement_histories.changed_by',
                'measurement_histories.notes',
                'measurement_histories.created_at',
            )
            ->get();
        }else{
            $devices_history = MeasurementHistory::query()
            ->join('measurement_devices', function ($m) {
                $m->on('measurement_devices.id', '=', 'measurement_histories.measurement_device_id');
            })
            ->select(
                'measurement_histories.id',
                'measurement_devices.name as device_name',
                'measurement_histories.status',
                'measurement_histories.changed_by',
                'measurement_histories.notes',
                'measurement_histories.created_at',
            )
            ->whereIn('measurement_histories.measurement_device_id', $device_ids)
            ->get();

        }


        //dd($devices_history);




        $pdf = Pdf::loadView('pdfs.device_pdf', [
            'devices_history' => $devices_history,
        ]);

        return $pdf->download();
    }


    public function values_report($start_date, $end_date)
    {
        $values = Value::query()
            ->join('parameters', function ($p) {
                $p->on('parameters.id', '=', 'values.parameter_id');
            })
            ->join('measurements', function ($m) {
            $m->on('measurements.id', '=', 'values.measurement_id');
            })
            ->select('values.id',
                'measurements.id',
                'parameters.name',
                'parameters.unit',
                'parameters.tag',
                'values.value',
                'measurements.measurements_date'
            )
            ->whereBetween('measurements_date', [$start_date, $end_date])
            ->get();

        $max_pm1=0;
        $min_pm1=10000;
        $delta_pm1=0;
        $pm1_count=0;

        $max_pm2_5=0;
        $min_pm2_5=10000;
        $delta_pm2_5=0;
        $pm2_5_count=0;

        $max_pm10=0;
        $min_pm10=10000;
        $delta_pm10=0;
        $pm10_count=0;

        $max_humidity=0;
        $min_humidity=10000;
        $delta_humidity=0;
        $humidity_count=0;

        $max_pressure=0;
        $min_pressure=10000;
        $delta_pressure=0;
        $pressure_count=0;

        $max_temperature=0;
        $min_temperature=10000  ;
        $delta_temperature=0;
        $temperature_count=0;

        //dd($values[1]);

        foreach($values as $value){
            switch($value->tag){
                case 'PM1':
                    if($value->value > $max_pm1){
                        $max_pm1=$value->value;
                    }
                    
                    if($value->value < $min_pm1){
                        $min_pm1=$value->value;
                    }

                    $delta_pm1+=$value->value;
                    $pm1_count++;

                    break;

                case 'PM2_5':
                    if($value->value > $max_pm2_5){
                        $max_pm2_5=$value->value;
                    }
                    
                    if($value->value < $min_pm2_5){
                        $min_pm2_5=$value->value;
                    }

                    $delta_pm2_5+=$value->value;
                    $pm2_5_count++;

                    break;

                case 'PM10':
                    if($value->value > $max_pm10){
                        $max_pm10=$value->value;
                    }
                    
                    if($value->value < $min_pm10){
                        $min_pm10=$value->value;
                    }

                    $delta_pm10+=$value->value;
                    $pm10_count++;
                    break;

                case 'HUM':
                    if($value->value > $max_humidity){
                        $max_humidity=$value->value;
                    }
                    
                    if($value->value < $min_humidity){
                        $min_humidity=$value->value;
                    }

                    $delta_humidity+=$value->value;
                    $humidity_count++;
                    break;                

                case 'PRESS':
                    if($value->value > $max_pressure){
                        $max_pressure=$value->value;
                    }
                    
                    if($value->value < $min_pressure){
                        $min_pressure=$value->value;
                    }

                    $delta_pressure+=$value->value;
                    $pressure_count++;
                    break;

                case 'TEMP':
                    if($value->value > $max_temperature){
                        $max_temperature=$value->value;
                    }
                    
                    if($value->value < $min_temperature){
                        $min_temperature=$value->value;
                    }

                    $delta_temperature+=$value->value;
                    $temperature_count++;
                    break;

                default:
                    break;
            }

        }

        $delta_array= array();
        $min_max_array =array();
        $pointers =array('p1'=>0,'p2'=>0,'p3'=>0,'p4'=>0,'p5'=>0,'p6'=>0);


        if($delta_pm1!=0){
            $delta_pm1 =$delta_pm1 /$pm1_count;
            $min_max_array = array_merge($min_max_array, array('max_pm1'=> $max_pm1, 'min_pm1'=> $min_pm1));
            $delta_array = array_merge($delta_array, array('delta_pm1'=> $delta_pm1, 'pm1_count'=>$pm1_count));
            $pointers['p1']=1;
        }

        if($delta_pm2_5!=0){
            $delta_pm2_5 =$delta_pm2_5 /$pm2_5_count;
            $min_max_array = array_merge($min_max_array, array('max_pm2_5'=> $max_pm2_5, 'min_pm2_5'=> $min_pm2_5));
            $delta_array = array_merge($delta_array, array('delta_pm2_5'=> $delta_pm2_5, 'pm2_5_count'=>$pm2_5_count));
            $pointers['p2']=1;            
        }

        if($delta_pm10!=0){
            $delta_pm10 =$delta_pm10 /$pm10_count;
            $min_max_array = array_merge($min_max_array, array('max_pm10'=> $max_pm10, 'min_pm10'=> $min_pm10));
            $delta_array = array_merge($delta_array, array('delta_pm10'=> $delta_pm10, 'pm10_count'=>$pm10_count));
            $pointers['p3']=1;
        }

        if($delta_humidity!=0){
            $delta_humidity =$delta_humidity /$humidity_count;
            $min_max_array = array_merge($min_max_array, array('max_humidity'=> $max_humidity, 'min_humidity'=> $min_humidity));
            $delta_array = array_merge($delta_array, array('delta_humidity'=> $delta_humidity, 'humidity_count'=>$humidity_count));
            $pointers['p4']=1;
        }

        if($delta_pressure!=0){
            $delta_pressure =$delta_pressure /$pressure_count;
            $min_max_array = array_merge($min_max_array, array('max_pressure'=> $max_pressure, 'min_pressure'=> $min_pressure));
            $delta_array = array_merge($delta_array, array('delta_pressure'=> $delta_pressure, 'pressure_count'=>$pressure_count));
            $pointers['p5']=1;
        }

        if($delta_temperature!=0){
            $delta_temperature =$delta_temperature /$temperature_count;
            $min_max_array = array_merge($min_max_array, array('max_temperature'=> $max_temperature, 'min_temperature'=> $min_temperature));
            $delta_array = array_merge($delta_array, array('delta_temperature'=> $delta_temperature, 'temperature_count'=>$temperature_count));
            $pointers['p6']=1;
        }
     

        //dd($delta_pm1, $delta_pm2_5, $delta_humidity, $delta_pressure, $delta_temperature);
        //dd($delta_array, $min_max_array);

        

        $pdf = Pdf::loadView('pdfs.values_pdf', [
            'pointers' => $pointers,
            'delta_array' => $delta_array,
            'min_max_array' => $min_max_array,
        ]);
 

        return $pdf->download();
    }

    //     @foreach ($p as $pp)
    //         {{ $pp->name }}: {{ $pp->unit }}<br>
            
    //     @endforeach



}
