<?php

namespace App\Livewire;

use App\Models\Measurement;
use App\Models\Value;
use App\Models\MeasurementDevice;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use App\Enums\Auth\RoleType;



final class ValuesTable extends PowerGridComponent
{
    public string $tableName = 'values-table-a0bsl6-table';

    public ?string $device_id = null;


    protected $listeners = ['deleteMeasurementConfirmed' => 'deleteMeasurement'];

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
            ->add('measurements_date')
            ->add('temp_value')
            ->add('hum_value')
            ->add('press_value')
            ->add('pm1_value')
            ->add('pm25_value')
            ->add('pm10_value'); 
    }

    public function columns(): array
    {
        $columns = [
            Column::make('Data Pomiaru', 'measurements_date')
                ->sortable()
                ->searchable(),
        ];

        // ✅ pobieramy urządzenie, żeby sprawdzić jego parametry
        $device = MeasurementDevice::find($this->device_id);
        $deviceParams = [];

        if ($device) {
            $ids = is_array($device->parameter_ids)
                ? $device->parameter_ids
                : json_decode($device->parameter_ids, true);

            if (!is_array($ids)) {
                $ids = explode(',', (string) $device->parameter_ids);
            }

            $deviceParams = array_map('intval', $ids);
        }

        // ✅ dodajemy tylko kolumny dla parametrów, które urządzenie posiada
        if (in_array(6, $deviceParams)) {
            $columns[] = Column::make('Temperatura', 'temp_value')->sortable()->searchable();
        }
        if (in_array(4, $deviceParams)) {
            $columns[] = Column::make('Wilgotność', 'hum_value')->sortable()->searchable();
        }
        if (in_array(5, $deviceParams)) {
            $columns[] = Column::make('Ciśnienie', 'press_value')->sortable()->searchable();
        }
        if (in_array(1, $deviceParams)) {
            $columns[] = Column::make('PM1', 'pm1_value')->sortable()->searchable();
        }
        if (in_array(2, $deviceParams)) {
            $columns[] = Column::make('PM2.5', 'pm25_value')->sortable()->searchable();
        }
        if (in_array(3, $deviceParams)) {
            $columns[] = Column::make('PM10', 'pm10_value')->sortable()->searchable();
        }

        // ✅ kolumna z akcjami
        $columns[] = Column::action('Akcje');

        return $columns;
    }

    public function actions(Measurement $measurement): array
    {
        $actions = [];
        $user = Auth::user();
        /** @var \App\Models\User $user */
        if ($user && ($user->hasRole(RoleType::ADMIN->value) || $user->hasRole(RoleType::MAINTEINER->value))) {
        $actions[] = Button::add('edit_value')
                ->slot(Blade::render('<x-wireui-icon name="wrench" class="w-5 h-5" mini />'))
                ->tooltip('Edytuj pomiar')
                ->class('text-yellow-500 hover:text-yellow-700')
                ->route('measurements.edit', ['measurement' => $measurement]);

        $actions[] = Button::add('delete')
                ->slot(Blade::render('<x-wireui-icon name="trash" class="w-5 h-5" />'))
                ->tooltip('Usuń')
                ->class('text-red-500 hover:text-red-700')
                ->dispatch('delete_measurement', [
                    'id' => $measurement->id,
                    'confirm' => [
                        'title' => 'Potwierdzenie usunięcia',
                        'description' => 'Czy na pewno chcesz usunąć ten pomiar?',
                        'accept' => [
                            'label' => 'Tak, usuń',
                            'method' => 'delete',
                            'params' => ['id' => $measurement->id],
                        ],
                        'reject' => ['label' => 'Anuluj'],
                    ],
                ]);
        }
        return $actions;
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('delete_confirmed')]
    public function deleteConfirmed($id): void
    {
        $measurement = Measurement::find($id);

        if ($measurement) {
            $measurement->delete();
            $this->dispatch('showToast', type: 'success', message: 'Pomiar został usunięty.');
        } else {
            $this->dispatch('showToast', type: 'error', message: 'Nie znaleziono pomiaru do usunięcia.');
        }
    }

}
