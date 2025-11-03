<?php

namespace App\Livewire;

use App\Models\Measurement;
use App\Models\Value;
use Illuminate\Support\Facades\DB;
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
            ->select([
                'measurements.id',
                'measurements.measurements_date',
                DB::raw('MAX(CASE WHEN values.parameter_id = 6 THEN values.value ELSE NULL END) as temp_value'),
                DB::raw('MAX(CASE WHEN values.parameter_id = 4 THEN values.value ELSE NULL END) as hum_value'),
                DB::raw('MAX(CASE WHEN values.parameter_id = 5 THEN values.value ELSE NULL END) as press_value'),
                DB::raw('MAX(CASE WHEN values.parameter_id = 1 THEN values.value ELSE NULL END) as pm1_value'),
                DB::raw('MAX(CASE WHEN values.parameter_id = 2 THEN values.value ELSE NULL END) as pm25_value'),
                DB::raw('MAX(CASE WHEN values.parameter_id = 3 THEN values.value ELSE NULL END) as pm10_value'),
            ])
            ->join('values', 'measurements.id', '=', 'values.measurement_id')
            ->where('measurements.device_id', $this->device_id)
            ->groupBy('measurements.id', 'measurements.measurements_date');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('temp_value')
            ->add('hum_value')
            ->add('press_value')
            ->add('pm1_value')
            ->add('pm25_value')
            ->add('pm10_value')
            ->add('measurements_date'); 
    }

    public function columns(): array
    {
        return [


            Column::make('Temperatura', 'temp_value')
                ->sortable()
                ->searchable(),

            Column::make('Wilgotność', 'hum_value')
                ->sortable()
                ->searchable(),

            Column::make('Ciśnienie', 'press_value')
                ->sortable()
                ->searchable(),

            Column::make('PM1', 'pm1_value')
                ->sortable()
                ->searchable(),

            Column::make('PM2.5', 'pm25_value')
                ->sortable()
                ->searchable(),

            Column::make('PM10', 'pm10_value')
                ->sortable()
                ->searchable(),

            Column::make('Data Pomiaru', 'measurements_date')
                ->sortable()
                ->searchable(),

        ];
    }

    public function filters(): array
    {
        return [
        ];
    }
}
