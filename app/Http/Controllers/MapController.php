<?php

namespace App\Http\Controllers;

use App\Models\MeasurementDevice;
use App\Models\Parameter;
use App\Helpers\PollutionColorHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function index(Request $request)
    {
        $selectedParameterId = $request->input('parameter_id');

        $parameters = Parameter::orderBy('id')->get(['id', 'name', 'tag', 'unit']);
        $devices = MeasurementDevice::where('status', 'active')->get(['id','name','latitude','longitude']);

        $out = [];
        foreach ($devices as $d) {
            // параметри, які підтримує пристрій
            $pids = DB::table('device_parameters')
                ->where('device_id', $d->id)
                ->pluck('parameter_id');

            $vals = [];
            foreach ($pids as $pid) {
                // останнє значення для цього пристрою і параметра
                $row = DB::table('values as v')
                    ->join('measurements as m', 'm.id', '=', 'v.measurement_id')
                    ->join('parameters as p', 'p.id', '=', 'v.parameter_id')
                    ->where('m.device_id', $d->id)            // якщо інша колонка — заміни тут
                    ->where('v.parameter_id', $pid)
                    ->orderByDesc('v.created_at')
                    ->select('v.parameter_id','v.value','v.created_at','p.id as pid','p.tag','p.name','p.unit')
                    ->first();

                if (!$row) continue;

                $color = PollutionColorHelper::getPollutionColor($row->value, $row->name, $row->tag);

                $vals[] = [
                    'parameter_id' => $row->parameter_id,
                    'parameter' => [
                        'id' => $row->pid,
                        'tag' => $row->tag,
                        'name' => $row->name,
                        'unit' => $row->unit,
                    ],
                    'value' => $row->value,
                    'color' => $color,
                    'created_at' => (string)$row->created_at,
                ];
            }

            $out[] = [
                'id' => $d->id,
                'name' => $d->name,
                'latitude' => $d->latitude,
                'longitude' => $d->longitude,
                'values' => $vals,
            ];
        }

        // Діагностика: логуємо перший пристрій з values
        if (!empty($out)) {
            \Log::debug('MapController output', [
                'first_device_id' => $out[0]['id'],
                'first_device_values_count' => count($out[0]['values']),
                'first_value' => $out[0]['values'][0] ?? null,
            ]);
        }

        return view('map', [
            'devices' => collect($out),
            'parameters' => $parameters,
            'selectedParameterId' => $selectedParameterId,
        ]);
    }
}

