<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Livewire\MeasurementDeviceTable;
use App\Http\Controllers\MeasurementDeviceController;
use App\Http\Controllers\ValueController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    Route::name('users.edit')->get('/user_edit/{user}', [UserController::class,'edit']);
    Route::name('users.create')->get('/user_create', [UserController::class,'create']);

    Route::get('/measurement-devices/table', MeasurementDeviceTable::class)->name('measurement-devices.table');
    Route::resource('measurement-devices', MeasurementDeviceController::class);
    Route::get('measurement-devices/{measurement_device}', [MeasurementDeviceController::class, 'show']) ->name('measurement-devices.show');
    Route::resource('users', UserController::class)->only([
        'index',
        //'create',
        //'edit',
        //'show',
    ]);
    Route::get('/values', [ValueController::class, 'index'])->name('values.index');




});
