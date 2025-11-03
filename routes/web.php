<?php

use App\Http\Controllers\DataController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MeasurementDeviceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValueController;
use App\Http\Controllers\MeasurementController;
use App\Livewire\MeasurementDeviceTable;
use App\Models\MeasurementDevice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {

        $start_date = Carbon::now()->addDay()->format('Y-m-d');
        $end_date = Carbon::now()->addMonths(1)->format('Y-m-d');

        $data = MeasurementDevice::query()
            ->whereBetween('next_calibration_date', [$start_date, $end_date])->get();

        return view('dashboard', ['data' => $data]);
    })->name('dashboard');

    Route::get('data.values_report/{start_date}/{end_date}', [DataController::class, 'values_report'])->name('data.values_report');
    Route::get('data.system_report/{start_date}/{end_date}', [DataController::class, 'system_report'])->name('data.system_report');
    Route::get('data.device_report/{start_date}/{end_date}/{device_ids}', [DataController::class, 'device_report'])->name('data.device_report');
    Route::get('data.file/{start_date}/{end_date}/{device_ids}', [DataController::class, 'file'])->name('data.file');

    // Route::name('data.export_file')->get('/start_date/{start_date}/end_date/{end_date}', [DataController::class, 'export_file']);
    Route::name('data.export')->get('/export', [DataController::class, 'export']);

    Route::name('data.show')->get('/show/{measurement}', [DataController::class, 'show']);
    Route::name('data.index')->get('/index', [DataController::class, 'index']);
    Route::name('data.upload')->get('/upload', [DataController::class, 'upload']);
    Route::name('data.form')->get('/form', [DataController::class, 'form']);

    Route::name('users.edit')->get('/user_edit/{user}', [UserController::class, 'edit']);
    Route::name('users.create')->get('/user_create', [UserController::class, 'create']);

    Route::name('measurement-devices.get_devices')->get('/get_devices', [MeasurementDeviceController::class, 'get_devices']);
    
    
    
    //Route::name('measurement-devices.get_parameters')->get('/get_parameters', [MeasurementDeviceController::class, 'get_parameters']);
    Route::get('get_parameters/{parameters}', [MeasurementDeviceController::class, 'get_parameters'])->name('measurement-devices.get_parameters');

    Route::get('/measurement-devices/table', MeasurementDeviceTable::class)->name('measurement-devices.table');
    Route::resource('measurement-devices', MeasurementDeviceController::class);
    Route::get('measurement-devices/{measurement_device}', [MeasurementDeviceController::class, 'show'])->name('measurement-devices.show');
    Route::get('/measurements/create', [MeasurementController::class, 'create'])
        ->name('measurements.create');
    Route::post('/measurements', [MeasurementController::class, 'store'])
    ->name('measurements.store');
    Route::get('/measurements/{measurement}/edit', [MeasurementController::class, 'edit'])
    ->name('measurements.edit');
    Route::resource('measurements', MeasurementController::class);
    Route::resource('values', ValueController::class);
    Route::delete('/measurements/{measurement}', [MeasurementController::class, 'destroy'])->name('measurements.destroy');    
    Route::resource('users', UserController::class)->only([
        'index',
        // 'create',
        // 'edit',
        // 'show',
    ]);
    Route::get('/values', [ValueController::class, 'index'])->name('values.index');

    Route::get('/map', [MapController::class, 'index'])->name('map'); // do mapy

});
