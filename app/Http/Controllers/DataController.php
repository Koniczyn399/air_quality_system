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
        $min_pm1=0;
        $delta_pm1=0;

        $max_pm2_5=0;
        $min_pm2_5=0;
        $delta_pm2_5=0;

        $max_pm10=0;
        $min_pm10=0;
        $delta_pm10=0;

        $max_humidity=0;
        $min_humidity=0;
        $delta_humidity=0;

        $max_pressure=0;
        $min_pressure=0;
        $delta_pressure=0;

        $max_temperature=0;
        $min_temperature=0;
        $delta_temperature=0;

        foreach($values as $value){

        }

        // <x-wireui-button 
        //     wire:click="values_report" 
        //     primary 
        //     label="{{ __('data.actions.generate_values') }}" 
        // />

        dd($values);

        $pdf = Pdf::loadView('pdfs.values_pdf', [
            'values' => $values,


        ]);

        return $pdf->download();
    }
}
