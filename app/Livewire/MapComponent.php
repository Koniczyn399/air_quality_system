<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\MeasurementDevice;

class MapComponent extends Component
{
    public $devices;

    public function mount()
    {
        $this->devices = MeasurementDevice::select('id', 'name', 'latitude', 'longitude', 'status')->get();
    }

    public function render()
    {
        return view('livewire.map-component');
    }
}