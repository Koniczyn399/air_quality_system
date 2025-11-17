<?php

namespace App\Livewire;

use App\Models\Measurement;
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
use Illuminate\Support\Carbon;

final class ValuesTable extends PowerGridComponent
{
    public string $tableName = 'values-table-a0bsl6-table';
    public ?string $device_id = null;

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public array $quickRangeOptions = [
        '24h' => 'Ostatnie 24h',
        '7d'  => 'Ostatnie 7 dni',
        '30d' => 'Ostatnie 30 dni',
        'all' => 'Cały okres',
    ];

    public string $activeQuickRange = 'all';

    public array $parameterRanges = [];

    public bool $showAdvancedFilters = false;

    protected array $parameterMeta = [
        6 => [
            'alias' => 'temp_value',
            'label' => 'Temperatura',
            'unit'  => '°C',
            'step'  => '0.1',
        ],
        4 => [
            'alias' => 'hum_value',
            'label' => 'Wilgotność',
            'unit'  => '%',
            'step'  => '0.1',
        ],
        5 => [
            'alias' => 'press_value',
            'label' => 'Ciśnienie',
            'unit'  => 'hPa',
            'step'  => '1',
        ],
        1 => [
            'alias' => 'pm1_value',
            'label' => 'PM1',
            'unit'  => 'µg/m³',
            'step'  => '0.1',
        ],
        2 => [
            'alias' => 'pm25_value',
            'label' => 'PM2.5',
            'unit'  => 'µg/m³',
            'step'  => '0.1',
        ],
        3 => [
            'alias' => 'pm10_value',
            'label' => 'PM10',
            'unit'  => 'µg/m³',
            'step'  => '0.1',
        ],
    ];

    protected ?array $cachedDeviceParameterIds = null;


    protected $listeners = ['deleteMeasurementConfirmed' => 'deleteMeasurement'];

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->showSearchInput()
                ->includeViewOnTop('livewire.values-table.filters'),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function booted(): void
    {
        if (is_null($this->dateFrom) && is_null($this->dateTo)) {
            $this->applyQuickRange($this->activeQuickRange, false);
        }

        $this->ensureParameterRangeKeys();
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
                ->when($this->dateFrom, fn ($query) => $query->whereDate('measurements.measurements_date', '>=', $this->dateFrom))
                ->when($this->dateTo, fn ($query) => $query->whereDate('measurements.measurements_date', '<=', $this->dateTo))
                ->groupBy('measurements.id', 'measurements.measurements_date')
                ->tap(function ($query) {
                    foreach ($this->getDeviceParameterIds() as $parameterId) {
                        $meta = $this->parameterMeta[$parameterId] ?? null;

                        if (!$meta) {
                            continue;
                        }

                        $alias = $meta['alias'];
                        $min   = data_get($this->parameterRanges, $alias.'.min');
                        $max   = data_get($this->parameterRanges, $alias.'.max');

                        if ($min !== null && $min !== '') {
                            $query->havingRaw("{$alias} >= ?", [$min]);
                        }

                        if ($max !== null && $max !== '') {
                            $query->havingRaw("{$alias} <= ?", [$max]);
                        }
                    }
                });
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

    $deviceParams = $this->getDeviceParameterIds();

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
                ->route('measurements.edit', ['measurement' => $measurement, 'device_id' => $this->device_id]);

            $actions[] = Button::add('delete')
                ->slot(Blade::render('<x-wireui-icon name="trash" class="w-5 h-5" />'))
                ->tooltip('Usuń pomiar')
                ->class('text-red-500 hover:text-red-700')
                ->dispatch('deleteMeasurement', [
                    'id' => $measurement->id,
                    'confirm' => [
                        'title' => 'Potwierdzenie usunięcia',
                        'description' => 'Czy na pewno chcesz usunąć ten pomiar?',
                        'accept' => [
                            'label' => 'Tak, usuń',
                            'method' => 'deleteMeasurement',
                            'params' => $measurement->id,
                        ],
                        'reject' => [
                            'label' => 'Anuluj',
                        ],
                    ],
                ]);
        }
        
        return $actions;
    }

    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('deleteMeasurement')]
    public function deleteMeasurement($id): void
    {
        // Tutaj powinna być implementacja usuwania pomiaru
        // Na przykład:
        $measurement = Measurement::find($id);
        if ($measurement) {
            $measurement->values()->delete();
            $measurement->delete();
            $this->dispatch('showToast', type: 'success', message: 'Pomiar został usunięty.');
        }
    }

    public function updatedDateFrom(?string $value): void
    {
        $this->activeQuickRange = 'custom';

        if ($value && $this->dateTo && $value > $this->dateTo) {
            $this->dateTo = $value;
        }

        $this->resetPage();
    }

    public function updatedDateTo(?string $value): void
    {
        $this->activeQuickRange = 'custom';

        if ($value && $this->dateFrom && $value < $this->dateFrom) {
            $this->dateFrom = $value;
        }

        $this->resetPage();
    }

    public function applyQuickRange(string $rangeKey, bool $resetPagination = true): void
    {
        $range = array_key_exists($rangeKey, $this->quickRangeOptions) ? $rangeKey : 'all';

        $this->activeQuickRange = $range;

        if ($range === 'all') {
            $this->dateFrom = null;
            $this->dateTo   = null;
        } else {
            $this->dateTo = Carbon::now()->toDateString();

            $startPoint = match ($range) {
                '24h' => Carbon::now()->subHours(24),
                '7d'  => Carbon::now()->subDays(7),
                default => Carbon::now()->subDays(30),
            };

            $this->dateFrom = $startPoint->toDateString();
        }

        if ($resetPagination) {
            $this->resetPage();
        }
    }

    public function resetDateFilters(): void
    {
        $this->applyQuickRange('all');
    }

    public function toggleAdvancedFilters(): void
    {
        $this->showAdvancedFilters = !$this->showAdvancedFilters;
    }

    protected function ensureParameterRangeKeys(): void
    {
        foreach ($this->getDeviceParameterIds() as $parameterId) {
            $meta = $this->parameterMeta[$parameterId] ?? null;

            if (!$meta) {
                continue;
            }

            $alias = $meta['alias'];

            if (!isset($this->parameterRanges[$alias])) {
                $this->parameterRanges[$alias] = ['min' => null, 'max' => null];
            }
        }
    }

    protected function getDeviceParameterIds(): array
    {
        if (!is_null($this->cachedDeviceParameterIds)) {
            return $this->cachedDeviceParameterIds;
        }

        if (!$this->device_id) {
            return $this->cachedDeviceParameterIds = [];
        }

        $device = MeasurementDevice::find($this->device_id);

        if (!$device) {
            return $this->cachedDeviceParameterIds = [];
        }

        $ids = $device->parameter_ids;

        if (!is_array($ids)) {
            $decoded = json_decode($ids, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $ids = $decoded;
            } else {
                $ids = array_filter(explode(',', (string) $ids));
            }
        }

        $ids = array_map('intval', $ids);

        return $this->cachedDeviceParameterIds = array_filter($ids);
    }

    public function getParameterFilterFieldsProperty(): array
    {
        $this->ensureParameterRangeKeys();

        $fields = [];

        foreach ($this->getDeviceParameterIds() as $parameterId) {
            if (!isset($this->parameterMeta[$parameterId])) {
                continue;
            }

            $fields[] = $this->parameterMeta[$parameterId];
        }

        return $fields;
    }

    #[\Livewire\Attributes\On('delete_confirmed')]
    public function deleteConfirmed($id): void
    {
        // Sprawdzamy czy $id jest tablicą (jak czasem Livewire przekazuje)
        if (is_array($id)) {
            $id = $id['id'] ?? $id[0] ?? null;
        }

        $measurement = Measurement::find($id);

        if ($measurement) {
            // Usuń powiązane wartości
            $measurement->values()->delete();
            // Usuń pomiar
            $measurement->delete();
            
            $this->dispatch('showToast', type: 'success', message: 'Pomiar został usunięty.');
            
            // Odśwież tabelę
            $this->refresh();
        } else {
            $this->dispatch('showToast', type: 'error', message: 'Nie znaleziono pomiaru do usunięcia.');
        }
    }
}