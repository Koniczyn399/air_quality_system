<?php

namespace App\Livewire;

use App\Models\MeasurementDevice;
use Livewire\Component;

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
