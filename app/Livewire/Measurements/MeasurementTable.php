<?php

namespace App\Livewire\Measurements;

use App\Models\Measurement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class MeasurementTable extends PowerGridComponent
{
    public string $tableName = 'measurement-table-ld6keh-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Measurement::query()
        ->join('measurement_devices', function ($measurement_devices) {
            $measurement_devices->on('measurements.device_id', '=', 'measurement_devices.id');
        })
        ->select([
            'measurements.id',
            'measurements.measurements_date',
            'measurements.created_at',
            'measurement_devices.name as device_name',



        ]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('measurements_date')
            ->add('device_name')

            ->add('created_at')
            ->add('created_at_formatted', fn (Measurement $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->searchable()
                ->sortable(),

            Column::make(__('measurements.attributes.measurements_date'), 'measurements_date')
                ->searchable()
                ->sortable(),
            Column::make(__('measurements.attributes.device_name'), 'device_name')
            ->searchable()
            ->sortable(),

            Column::make(__('measurements.attributes.created_at'), 'created_at')
                ->hidden(),

            Column::make(__('measurements.attributes.created_at'), 'created_at_formatted', 'created_at')
                ->searchable(),

            Column::action(__('translation.actions.actions'))
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name'),
            Filter::datepicker('created_at_formatted', 'created_at'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    
    #[\Livewire\Attributes\On('removeMeasurementAction')]
    public function removeMeasurementAction($id): void
    {
        //$this->authorize('delete', Measurement::findOrFail($id));
        Measurement::findOrFail($id)->delete();
    }


    public function actions(Measurement $measurement): array
    {
        return [
            Button::add('show_measurement')
            ->slot(Blade::render('<x-wireui-icon name="eye" class="w-5 h-5" mini />'))
            ->tooltip(__('measurements.actions.show_measurement'))
            ->class('text-green-500')
            ->route('data.show', ['measurement' =>$measurement]),
        

        Button::add('removeMeasurementAction')
        ->slot(Blade::render('<x-wireui-icon name="x-mark" class="w-5 h-5" mini />'))
        ->tooltip(__('measurements.actions.remove_measurement'))
        ->class('text-red-500')
        ->dispatch('removeMeasurementAction', ['id' => $measurement->id]),
        ];
    }

    /*
    public function actionRules(Measurement $row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
