<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Measurement;
use App\Models\Value;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DataController extends Controller
{


    public function index()
    {
       
       
        //$this->authorize('viewAny', User::class);
        
     
            return view(
                'measurements.index',
                [
                    'measurements' => Measurement::paginate(
                        config('pagination.default')
                    )
            
                ]
            );

    }

    public function show(Measurement $measurement)
    {
   
       
        //$this->authorize('viewAny', User::class);

        $values = Value::query()
        ->join('parameters', function ($parameters) {
            $parameters->on('values.parameter_id', '=', 'parameters.id');
        })
        ->select([
            'values.id',
            'values.measurement_id',
            'values.value',
            'parameters.name as parameter_name',
            'parameters.unit as unit_name'
        ])
        ->where('values.measurement_id', '=', $measurement->id)->get();

        
        return view(
            'measurements.show',
            [
                'measurement' => $measurement,
                'values' =>$values,         
            ]
        );

    }

    public function upload()
    {   
        //$this->authorize('viewAny', User::class);
        


        return view(
            'data.form'
    
        );

    }

    
}
