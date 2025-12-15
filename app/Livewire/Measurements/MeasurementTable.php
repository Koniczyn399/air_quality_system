<?php

namespace App\Livewire\Measurements;

use App\Models\Measurement;
use App\Models\MeasurementDevice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
// ZMIANA 1: Poprawny import dla WireUI v2
use WireUi\Traits\WireUiActions; 

final class MeasurementTable extends PowerGridComponent
{
    // ZMIANA 2: Użycie poprawnego Traita
    use WireUiActions; 

    public string $tableName = 'measurement-table';

    public string $name = 'sdfsdfsd';

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
                ->builder(function (Builder $builder, string $value) {
                    $builder->where('measurement_devices.name', $value);
                }),

            Filter::datepicker('measurements_date'),
        ];
    }

    public function actions(Measurement $measurement): array
    {
        return [
            // 1. PODGLĄD
            Button::add('show_measurement')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>')
                ->tooltip('Podgląd')
                ->route('data.show', ['measurement' => $measurement]),

            // 2. EDYCJA
            Button::add('edit')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>')
                ->tooltip('Edytuj')
                ->route('measurements.edit', ['measurement' => $measurement]),

            // 3. USUWANIE (Z WireUI Dialog)
            Button::add('removeMeasurementAction')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>')
                ->tooltip('Usuń')
                ->class('cursor-pointer')
                ->dispatch('triggerDelete', ['id' => $measurement->id]),
        ];
    }

    // --- METODY DO OBSŁUGI DIALOGU ---

    /**
     * Otwiera okienko potwierdzenia WireUI
     */
    #[\Livewire\Attributes\On('triggerDelete')]
    public function triggerDelete($id)
    {
        $this->dialog()->confirm([
            'title'       => 'Potwierdzenie usunięcia',
            'description' => 'Czy na pewno chcesz trwale usunąć ten pomiar?',
            'icon'        => 'error',
            'accept'      => [
                'label'  => 'Tak, usuń',
                'method' => 'deleteMeasurement',
                'params' => $id,
            ],
            'reject' => [
                'label'  => 'Anuluj',
            ],
        ]);
    }

    /**
     * Wykonuje faktyczne usuwanie po kliknięciu "Tak"
     */
    public function deleteMeasurement($id)
    {
        $measurement = Measurement::find($id);

        if ($measurement) {
            $measurement->delete();
            
            $this->notification()->success(
                $title = 'Sukces',
                $description = 'Pomiar został usunięty.'
            );
        } else {
            $this->notification()->error(
                $title = 'Błąd',
                $description = 'Nie znaleziono pomiaru.'
            );
        }
    }
}