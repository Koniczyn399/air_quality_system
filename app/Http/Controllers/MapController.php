<?php

namespace App\Http\Controllers;

use App\Models\MeasurementDevice;

class MapController extends Controller
{
    public function index()
    {
        $measurements = Measurement::with('parametrs')->latest()->get();
        return view('map', compact('measurements'));
    }
}