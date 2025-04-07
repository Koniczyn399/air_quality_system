<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ValueController extends Controller
{
    public function index(Request $request)
    {
        $device_id = $request->query('device_id');
        
        return view('values.index', [
            'device_id' => $device_id
        ]);
    }
}
