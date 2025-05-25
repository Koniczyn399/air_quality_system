<?php

namespace App\Livewire;

use App\Enums\Auth\RoleType;
use App\Models\MeasurementDevice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class MeasurementDeviceTable extends PowerGridComponent
{
    public $proba;

    use WithExport;

    public string $tableName = 'measurement_devices_powergrid_table';

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
        return MeasurementDevice::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('model')
            ->add('serial_number')
            ->add('calibration_date_formatted', fn ($device) => $device->calibration_date->format('d-m-Y'))
            ->add('next_calibration_date_formatted', fn ($device) => $device->next_calibration_date->format('d-m-Y'))
            ->add('status_formatted', fn ($device) => Blade::render(
                '<div class="flex items-center gap-2">'.
                match ($device->status) {
                    'active' => '<x-wireui-icon name="check-circle" class="w-5 h-5 text-green-500" />',
                    'inactive' => '<x-wireui-icon name="x-circle" class="w-5 h-5 text-red-500" />',
                    'in_repair' => '<x-wireui-icon name="key" class="w-5 h-5 text-yellow-500" />',
                    default => '<x-wireui-icon name="question-mark-circle" class="w-5 h-5 text-gray-500" />'
                }.
                '<span>'.$this->getStatusText($device->status).'</span>'.
                '</div>'
            )
            )
            ->add('user_name', fn ($device) => $device->user ? $device->user->name : 'Brak');

    }

    private function getStatusText(string $status): string
    {
        return match ($status) {
            'active' => 'Aktywny',
            'inactive' => 'Nieaktywny',
            'in_repair' => 'W naprawie',
            default => 'Nieznany'
        };
    }

    public function columns(): array
    {
        return [

            Column::make('Nazwa', 'name')
                ->sortable()
                ->searchable(),

            // Column::make('Model', 'model')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Numer seryjny', 'serial_number')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Data kalibracji', 'calibration_date_formatted', 'calibration_date')
                ->sortable(),

            Column::make('Następna kalibracja', 'next_calibration_date_formatted', 'next_calibration_date')
                ->sortable(),

            Column::make('Status', 'status_formatted')
                // ->sortable()
                ->searchable(),

            Column::make('Serwisant', 'user_name')
                ->sortable()
                ->searchable(),

            Column::action('Akcje'),
        ];
    }

    public function filters(): array
    {
        return [];
    }

    public function actions(MeasurementDevice $device): array
    {
        $actions = [];
        $user = Auth::user(); // Pobieramy zalogowanego użytkownika

        // Przycisk "Pokaż pomiary" - widoczny dla wszystkich
        $actions[] = Button::add('show_measurements')
            ->slot(Blade::render('<x-wireui-icon name="chart-bar" class="w-5 h-5 text-green-500" />'))
            ->tooltip('Pokaż pomiary')
            ->class('hover:bg-green-50 p-1 rounded')
            ->route('values.index', ['device_id' => $device->id]);

        // Przycisk "Szczegóły urządzenia" - widoczny dla wszystkich
        $actions[] = Button::add('info')
            ->slot(Blade::render('<x-wireui-icon name="information-circle" class="w-5 h-5 text-blue-500" />'))
            ->tooltip('Szczegóły urządzenia')
            ->class('hover:bg-blue-50 p-1 rounded')
            ->route('measurement-devices.show', ['measurement_device' => $device->id]);

        // Sprawdzamy, czy użytkownik ma rolę 'ADMIN' lub 'MAINTEINER' (Serwisant)
        // Używamy RoleType::ADMIN->value i RoleType::MAINTEINER->value
        /** @var \App\Models\User $user */
        if ($user && ($user->hasRole(RoleType::ADMIN->value) || $user->hasRole(RoleType::MAINTEINER->value))) {
            $actions[] = Button::add('edit_device')
                ->slot(Blade::render('<x-wireui-icon name="wrench" class="w-5 h-5" mini />'))
                ->tooltip('Edytuj urządzenie')
                ->class('text-yellow-500 hover:text-yellow-700')
                ->route('measurement-devices.edit', ['measurement_device' => $device->id]);

            $actions[] = Button::add('delete')
                ->slot(Blade::render('<x-wireui-icon name="trash" class="w-5 h-5" />'))
                ->tooltip('Usuń')
                ->class('text-red-500 hover:text-red-700')
                ->dispatch('delete_device', [
                    'id' => $device->id,
                    'confirm' => [
                        'title' => 'Potwierdzenie usunięcia',
                        'description' => 'Czy na pewno chcesz usunąć to urządzenie?',
                        'accept' => [
                            'label' => 'Tak, usuń',
                            'method' => 'delete',
                            'params' => ['measurement_device' => $device->id],
                        ],
                        'reject' => [
                            'label' => 'Anuluj',
                        ],
                    ],
                ]);
        }

        return $actions;
    }

    #[\Livewire\Attributes\On('delete_confirmed')]
    public function deleteConfirmed($id): void
    {
        $device = MeasurementDevice::find($id);
        if ($device) {
            $device->delete();
            $this->dispatch('showToast', type: 'success', message: 'Urządzenie zostało usunięte');
        } else {
            $this->dispatch('showToast', type: 'error', message: 'Nie znaleziono urządzenia do usunięcia');
        }
    }
}
