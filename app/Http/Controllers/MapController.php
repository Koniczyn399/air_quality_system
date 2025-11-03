<?php

namespace App\Http\Controllers;

use App\Models\Measurement;
use App\Models\Parametrs;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $measurements = Measurement::with('parametrs')->latest()->get();
        return view('map', compact('measurements'));
    }
}
