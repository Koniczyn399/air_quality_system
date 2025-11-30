<?php

namespace App\Livewire\Measurements;

use App\Models\Measurement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use App\Models\MeasurementDevice;

final class MeasurementTable extends PowerGridComponent
{
    public string $tableName = 'measurement-table';

    public function setUp(): array
    {
        return [
            PowerGrid::header(),
            PowerGrid::footer()->showPerPage()->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Measurement::query()
            ->with(['device', 'values.parameter'])
            ->leftJoin('measurement_devices', 'measurement_devices.id', '=', 'measurements.device_id')
            ->select('measurements.*', 'measurement_devices.name as device_name');
    }


    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('measurements_date')
            ->add('device_name', fn($m) => $m->device?->name ?? '-')

            ->add('wilgotnosc', fn($m) => $m->valueByName('Wilgotność'))
            ->add('cisnienie', fn($m) => $m->valueByName('Ciśnienie'))
            ->add('temperatura', fn($m) => $m->valueByName('Temperatura'))

            ->add('pm1', fn($m) => $m->valueByName('PM1'))
            ->add('pm25', fn($m) => $m->valueByName('PM2.5'))
            ->add('pm10', fn($m) => $m->valueByName('PM10'))

            ->add('created_at')
            ->add('created_at_formatted', fn($m) => $m->created_at->format('d/m/Y H:i'));
    }


    public function columns(): array
    {
        return [
            Column::make('Data pomiaru', 'measurements_date')->sortable()->searchable(),
            Column::make('Urządzenie', 'device_name')->sortable(),

            Column::make('Wilgotność', 'wilgotnosc'),
            Column::make('Ciśnienie', 'cisnienie'),
            Column::make('Temperatura', 'temperatura'),
            Column::make('PM1', 'pm1'),
            Column::make('PM2.5', 'pm25'),
            Column::make('PM10', 'pm10'),

            Column::action('Akcje'),
        ];
    }


    public function filters(): array
    {
        return [
            Filter::select('device_name')
                ->dataSource(MeasurementDevice::all())
                ->optionLabel('name')
                ->optionValue('name')
                // ADD THIS: Tell the query to look at the real table column
                ->builder(function (Builder $builder, string $value) {
                    $builder->where('measurement_devices.name', $value);
                }),


            Filter::datepicker('measurements_date'),
        ];
    }

    public function actions(Measurement $measurement): array
    {
        return [
            Button::add('show_measurement')
                ->slot(Blade::render('<x-wireui-icon name="information-circle" class="w-5 h-5 text-blue-500" />'))
                ->tooltip('Podgląd')
                ->class('text-green-500')
                ->route('data.show', ['measurement' => $measurement]),

            Button::add('removeMeasurementAction')
                ->slot(Blade::render('<x-wireui-icon name="trash" class="w-5 h-5" />'))
                ->tooltip('Usuń')
                ->class('text-red-500')
                ->dispatch('removeMeasurementAction', ['id' => $measurement->id]),
        ];
    }

    #[\Livewire\Attributes\On('removeMeasurementAction')]
    public function removeMeasurementAction($id): void
    {
        Measurement::findOrFail($id)->delete();
    }
}
