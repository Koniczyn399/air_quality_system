<x-app-layout>
    <div class="container mx-auto px-4 py-8 theme-container">
        <h1 class="text-2xl font-bold mb-6 theme-text">Dodaj nowe urządzenie</h1>

        <form action="{{ route('measurement-devices.store') }}" method="POST" class="max-w-3xl theme-bg p-6 rounded-md shadow-sm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Nazwa -->
                <div class="col-span-1">
                    <x-wireui-input
                        name="name"
                        label="Nazwa urządzenia"
                        required
                        value="{{ old('name') }}"
                    />
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Model -->
                <div class="col-span-1">
                    <x-wireui-input
                        name="model"
                        label="Model"
                        required
                        value="{{ old('model') }}"
                    />
                    @error('model')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Numer seryjny -->
                <div class="col-span-1">
                    <x-wireui-input
                        name="serial_number"
                        label="Numer seryjny"
                        required
                        value="{{ old('serial_number') }}"
                    />
                    @error('serial_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Data kalibracji -->
                <div class="col-span-1">
                    <x-wireui-datetime-picker
                        name="calibration_date"
                        label="Data kalibracji"
                        required
                        value="{{ old('calibration_date') }}"
                        without-time
                    />
                    @error('calibration_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Następna kalibracja -->
                <div class="col-span-1">
                    <x-wireui-datetime-picker
                        name="next_calibration_date"
                        label="Następna kalibracja"
                        required
                        value="{{ old('next_calibration_date') }}"
                        without-time
                    />
                    @error('next_calibration_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Status -->
                <div class="col-span-1">
                    <x-wireui-select
                        name="status"
                        label="Status"
                        placeholder="Wybierz status"
                        option-label="label"
                        option-value="value"
                        :options="[
                            ['label' => 'Aktywny', 'value' => 'active'],
                            ['label' => 'Nieaktywny', 'value' => 'inactive'],
                            ['label' => 'W naprawie', 'value' => 'in_repair'],
                        ]"
                        :selected="old('status')"
                    />
                    @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Serwisant -->
                <div class="col-span-1">
                    <x-wireui-select
                        name="user_id"
                        label="Serwisant"
                        placeholder="Wybierz serwisanta"
                        :options="$mainteiners"
                        option-label="label"
                        option-value="value"
                        :selected="old('user_id', $measurementDevice->user_id ?? null)"
                    />
                    @error('user_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Opis -->
                <div class="col-span-2">
                    <x-wireui-textarea
                        name="description"
                        label="Opis"
                        rows="3"
                        :value="old('description')"
                    />
                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('measurement-devices.index') }}" 
                   class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium theme-text hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    Anuluj
                </a>
                <x-wireui-button type="submit" primary label="Dodaj urządzenie" />
            </div>
        </form>
    </div>
</x-app-layout>
