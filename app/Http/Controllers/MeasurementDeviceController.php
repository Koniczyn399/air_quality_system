<?php

namespace App\Http\Controllers;

use App\Models\MeasurementDevice;
use App\Models\Parameter;
use App\Models\User;
use App\Services\GeolocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeasurementDeviceController extends Controller
{
        public function index(Request $request)
        {
            $query = MeasurementDevice::query();

            if ($request->get('filter') === 'mine' && Auth::check()) {
             $query->where('user_id', Auth::user()->id);
            }

            $devices = $query->get();

            return view('measurement-devices.index', compact('devices'));
        }



    public function create()
    {
        // Pobierz tylko użytkowników z rolą serwisanta
        $maintainers = User::whereHas('roles', function($query) {
                $query->where('name', 'MAINTEINER'); 
            })
            ->select('id', 'name')
            ->get()
            ->map(fn($u) => [
                'label' => $u->name,
                'value' => $u->id,
            ]);


        return view('measurement-devices.create', compact('maintainers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255',
            'calibration_date' => 'required|date',
            'next_calibration_date' => 'required|date|after:calibration_date',
            'status' => 'required|in:active,inactive,in_repair',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'user_id' => ['nullable', 'exists:users,id'],
            'parameter_ids' => 'nullable|json',
        ]);

        MeasurementDevice::create($validated);

        return redirect()->route('measurement-devices.index')->with('success', 'Urządzenie dodane pomyślnie.');
    }

    public function show(MeasurementDevice $measurementDevice)
    {
        $measurementDevice->load('statusHistory.changedBy');

        // Pobieranie lokalizacji
        $city = GeolocationService::getCityFromCoordinates(
            $measurementDevice->latitude,
            $measurementDevice->longitude
        );

        // dd(json_decode($measurementDevice->parameter_ids)   );
        $parameters = Parameter::query()
        ->whereIn('parameters.id', json_decode($measurementDevice->parameter_ids))
        ->get();
        



        return view('measurement-devices.show', compact('measurementDevice', 'city','parameters'));
    }

    public function edit(MeasurementDevice $measurementDevice)
    {
        // Pobierz tylko użytkowników z rolą serwisanta
        $maintainers = User::whereHas('roles', function($query) {
                $query->where('name', 'MAINTEINER');
            })
            ->select('id', 'name')
            ->get()
            ->map(fn($u) => [
                'label' => $u->name,
                'value' => $u->id,
            ]);

            $parameters = Parameter::query()->get();

        return view('measurement-devices.edit', compact('measurementDevice', 'maintainers','parameters'));
    }

    public function update(Request $request, MeasurementDevice $measurementDevice)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:measurement_devices,serial_number,'.$measurementDevice->id,
            'calibration_date' => 'required|date',
            'next_calibration_date' => 'required|date|after:calibration_date',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'status' => 'required|in:active,inactive,in_repair',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'user_id' => ['nullable', 'exists:users,id'],
            'parameter_ids' => 'nullable|json',

        ]);
        // $ids =$request->input("parameter_ids");

        // $ids=json_decode($ids);
        // //dd($ids);
        // // $ids = str_replace("[","",$ids);
        // // $ids = str_replace("]","",$ids);
        // // $ids=json_encode(explode(',',$ids));


        // $request->merge([
        // 'parameter_ids' => $ids]);

        // dd($request->request);

        if ($measurementDevice->status != $request->status) {
            $measurementDevice->addStatusHistory($request->status, 'Zmiana statusu przez formularz edycji');
        }

        $measurementDevice->update($validated);

        return redirect()->route('measurement-devices.index')
            ->with('success', 'Urządzenie zaktualizowane pomyślnie');
    }

    public function destroy(MeasurementDevice $measurementDevice)
    {
        $measurementDevice->delete();

        return redirect()->route('measurement-devices.index')->with('success', 'Urządzenie usunięte pomyślnie.');
    }

    public function get_devices(): array
    {
        $start = ['All'];
        $devices = MeasurementDevice::query()
            ->select(
                'measurement_devices.id',
                'measurement_devices.name',
            )
            ->get()->toArray();
        $result = array_merge($start, $devices);

        return $result;
    }

        public function get_parameters(): array
    {


        // Pobierz parametry
        $parameters = Parameter::query()->get()->toArray();
        //dd($parameters);


        return $parameters;
    }

                // <!-- Parametry -->
                // <div class="col-span-1">
                //     <x-wireui-select
                //         label="{{ __('data.attributes.parameters') }}"
                //         placeholder="{{ __('data.attributes.parameters') }}"
                //         multiselect
                //         class="w-full theme-input"  
               
                //     >
                //     <x-wireui-select.option disabled selected label="{{ 9 }}" value="    sdfsd "  />
                //     @foreach ( $parameters as $parameter)
                //         <x-wireui-select.option label="{{ $parameter['name'] }}" value="{{ $parameter['id'] }}"  />
                //     @endforeach
                
                
                // </x-wireui-select>
                // </div>
}