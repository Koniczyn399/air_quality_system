<?php

namespace App\Livewire\Data;

use Livewire\Component;
use Livewire\WithFileUploads;
use WireUi\Traits\WireUiActions;

class DataForm extends Component
{
    use WireUiActions;
    use WithFileUploads;
   
    public function render()
    {
        return view('livewire.data.data-form');
    }
}
