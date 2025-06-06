<?php

namespace App\Livewire\Data;

use App\Models\Measurement;
use Livewire\Component;

class ExportForm extends Component
{
    public $devices;

    public $device_ids;

    public $start_date;

    public $end_date;

    public $from;

    public $to;

    public function mount()
    {

        $this->start_date = '';
        $this->end_date = '';

    }

    public function submit()
    {

        ExportForm::check_date();

        $device_ids = $this->device_ids;

        if ($device_ids != null) {
            $d = [];
            foreach ($device_ids as $device) {
                array_push($d, $device);
            }
            $device_ids = $d;

        } else {
            $device_ids = [];
            array_push($device_ids, 0);
        }
        //  dd($device_ids);

        $measurements = Measurement::query()
            ->join('measurement_devices', function ($m) {
                $m->on('measurement_devices.id', '=', 'measurements.device_id');
            })
            ->select(
                'measurements.id',
                'measurements.device_id',
                'measurements.measurements_date'
            )
            ->whereBetween('measurements_date', [$this->from, $this->to])
            ->whereIn('measurements.device_id', $device_ids)
            ->get();

        return $this->redirect(route('data.file', ['start_date' => $this->from, 'end_date' => $this->to, 'device_ids' => json_encode($device_ids)]));
    }

    public function system_report()
    {
        ExportForm::check_date();

        return $this->redirect(route('data.system_report', ['start_date' => $this->from, 'end_date' => $this->to]));
    }

    public function values_report()
    {
        ExportForm::check_date();

        return $this->redirect(route('data.values_report', ['start_date' => $this->from, 'end_date' => $this->to]));
    }

    public function devices_report()
    {
        ExportForm::check_date();

        $device_ids = $this->device_ids;

        if ($device_ids != null) {
            $d = [];
            foreach ($device_ids as $device) {
                array_push($d, $device);
            }
            $device_ids = $d;

        } else {
            $device_ids = [];
            array_push($device_ids, 0);
        }

        return $this->redirect(route('data.device_report', ['start_date' => $this->from, 'end_date' => $this->to,
        'device_ids' => json_encode($device_ids)]));
    }

    public function check_date()
    {
        if ($this->start_date == null) {
            $this->from = '1900-01-01';

        } else {
            $this->from = $this->start_date;
        }

        if ($this->end_date == null) {
            $this->to = '3000-01-01';
        } else {
            $this->to = $this->end_date;
        }

    }

    public function render()
    {
        return view('livewire.data.export-form');
    }
}
