<?php

namespace App\Http\Controllers;

use App\Models\MeasurementDevice;

class MapController extends Controller
{
    public function index()
    {
        $devices = MeasurementDevice::select('id', 'name', 'latitude', 'longitude', 'status')->get();

        return view('map', compact('devices'));
    }
}
