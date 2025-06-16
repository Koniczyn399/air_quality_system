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

                <!-- Parametry -->
                <div class="col-span-1">
                    <x-wireui-select
                        label="{{ __('data.attributes.parameters') }}"
                        placeholder="{{ __('data.attributes.parameters') }}"
                        multiselect
                        :async-data="route('measurement-devices.get_parameters')"
                        option-label="name"
                        option-value="id"
                        class="w-full theme-input"
                        wire:model="parameter_ids"
                    />
                </div>

                <!-- Serwisant -->
                <div class="col-span-1">
                    <x-wireui-select
                        name="user_id"
                        label="Serwisant"
                        placeholder="Wybierz serwisanta"
                        :options="$maintainers"
                        option-label="label"
                        option-value="value"
                        :selected="old('user_id')"
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

            <!-- Sekcja lokalizacji -->
            <div class="col-span-2 mb-6">
                <h3 class="text-lg font-semibold theme-text mb-2">Lokalizacja urządzenia</h3>
                <div id="add-device-map" class="w-full h-96 border-2 border-gray-300 dark:border-gray-700 rounded-lg shadow-md"></div>

                <!-- Ukryte inputy do przechowywania współrzędnych -->
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                <p class="mt-1 text-sm theme-text">
                    Kliknij na mapie, aby wybrać lokalizację urządzenia.
                </p>
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

    {{--  Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"  />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script> 

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const htmlElement = document.documentElement;
            const addDeviceMap = L.map('add-device-map').setView([52.237049, 21.017532], 6);

            let currentTileLayer;

            const lightTileUrl = 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png'; 
            const darkTileUrl = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'; 

            function loadTileLayer(tileUrl) {
                if (currentTileLayer) {
                    addDeviceMap.removeLayer(currentTileLayer);
                }
                currentTileLayer = L.tileLayer(tileUrl, {
                    maxZoom: 18,
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>  contributors',
                }).addTo(addDeviceMap);
            }

            if (htmlElement.classList.contains('dark')) {
                loadTileLayer(darkTileUrl);
            } else {
                loadTileLayer(lightTileUrl);
            }

            const observer = new MutationObserver((mutationsList, observer) => {
                for (const mutation of mutationsList) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        if (htmlElement.classList.contains('dark')) {
                            loadTileLayer(darkTileUrl);
                        } else {
                            loadTileLayer(lightTileUrl);
                        }
                    }
                }
            });

            observer.observe(htmlElement, { attributes: true });

            // Obsługa kliknięcia na mapie
            addDeviceMap.on('click', function (e) {
                const lat = e.latlng.lat.toFixed(6);
                const lng = e.latlng.lng.toFixed(6);

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;

                // Opcjonalnie: dodanie markera
                addDeviceMap.eachLayer(layer => {
                    if (layer instanceof L.Marker) {
                        layer.remove();
                    }
                });

                L.marker([lat, lng]).addTo(addDeviceMap).bindPopup("Wybrana lokalizacja").openPopup();
                
            });
        });
    </script>
</x-app-layout>