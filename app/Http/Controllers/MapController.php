<?php

namespace App\Http\Controllers;

use App\Models\MeasurementDevice;
use App\Models\Parameter;
use App\Helpers\PollutionColorHelper;

class MapController extends Controller
{
    public function index()
    {
        $devices = MeasurementDevice::with(['values'=> function ($query) {
            $query->with('parameter')
                ->latest()
                ->limit(50);
        }])
        ->where('status', 'active')
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get()
        ->map(function ($device) {
            $latestValues = $device->values
                ->groupBy('parameter_id')
                ->map(function ($values) {
                    return $values->first();
        })
        ->values();

        $latestValues = $latestValues->map(function($value){
            if ($value->parameter){
                $value->color = PollutionColorHelper::getPollutionColor(
                    $value->value,
                    $value->parameter->name
                );
            }
            return $value;
        });        //    'id', 'name', 'latitude', 'longitude', 'status')->get();
        $device->values = $latestValues;
        return $device;
    });
    $parameters = Parameter::all();
        return view('map', compact('devices', 'parameters'));
    }
}
