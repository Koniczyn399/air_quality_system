<?php

namespace App\Livewire;

use App\Models\Measurement;
use App\Models\Value;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class ValuesTable extends PowerGridComponent
{
    public string $tableName = 'values-table-a0bsl6-table';

    public string $device_id = '';

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

        $measurements = Measurement::query()
            ->select(
                'measurements.id'
            )
            ->where('measurements.device_id', '=', $this->device_id)->get()->toArray();

        // dd($measurements);

        $query = Value::query()

        // Zapytanie by pomiary dotyczyły tylko tego urządzenia
            ->join('measurements', function ($measurements) {
                $measurements->on('measurements.id', '=', 'values.measurement_id');
            })
            ->select([
                'values.id',
                'values.parameter_id',
                'values.measurement_id',
                'values.value',
                'values.created_at',

            ])
            ->whereIn('values.measurement_id', $measurements);

        return $query;
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('measurement_id')
            ->add('parameter_id')
            ->add('value')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Measurement id', 'measurement_id'),
            Column::make('Parameter id', 'parameter_id'),
            Column::make('Value', 'value')
                ->sortable()
                ->searchable(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable(),

            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(Value $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: '.$row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id]),
        ];
    }

    /*
    public function actionRules($row): array
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
