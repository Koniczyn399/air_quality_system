<div class="container mx-auto px-4 py-8">
    <form wire:submit.prevent="submit">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <!-- Nazwa -->
            <div class="col-span-1">
                <x-wireui-input
                    name="name"
                    label="Nazwa urządzenia"
                    required
                    wire:model="name"
                />
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Model -->
            <div class="col-span-1">
                <x-wireui-input
                    name="model"
                    label="Model"
                    required
                    wire:model="model"
                />
                @error('model')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Numer seryjny -->
            <div class="col-span-1">
                <x-wireui-input
                    name="serial_number"
                    label="Numer seryjny"
                    required
                    wire:model="serial_number"
                   
                />
                @error('serial_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Data kalibracji -->
            <div class="col-span-1">
                <x-wireui-datetime-picker
                    name="calibration_date"
                    label="Data kalibracji"
                    required
                    wire:model="calibration_date"
                />
                @error('calibration_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Następna kalibracja -->
            <div class="col-span-1">
                <x-wireui-datetime-picker
                    name="next_calibration_date"
                    label="Następna kalibracja"
                    required
                     wire:model="next_calibration_date"
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

                    wire:model="status"
                />
                @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Parametry -->


            <div class="col-span-1">
                <x-wireui-select
                    label="{{ __('data.attributes.parameters') }}"
                    placeholder="{{ __('data.attributes.parameters') }}"
                    multiselect
                    :options="$this->parameters"
                    option-label="name"
                    option-value="id"
                    class="w-full theme-input"
                    wire:model="parameter_ids"
                >

                </x-wireui-select>

            </div>


            <!-- Serwisant -->
            <div class="col-span-1">
                <x-wireui-select
                    name="user_id"
                    id="user_id"
                    label="Serwisant"
                    placeholder="Wybierz serwisanta"
                    :options="$maintainers"
                    option-label="label"
                    option-value="value"
                    wire:model="user_id"
                />
                @error('user_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Opis -->
            <div class="col-span-2">
                <x-wireui-textarea
                    name="description"
                    label="Opis"
                    rows="3"
                    wire:model="description"
                />
                @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

                    <!-- Sekcja lokalizacji -->
            <div class="col-span-2 mb-6">
                <h3 class="text-lg font-semibold theme-text mb-2">Lokalizacja urządzenia</h3>

                    <div wire:ignore>
                    <div id="add-device-map" class="w-full h-96 border-2 border-gray-300 dark:border-gray-700 rounded-lg shadow-md" style=" z-index: 0;" ></div>
                    </div>


                <!-- Ukryte inputy do przechowywania współrzędnych -->
                <x-wireui-input name="latitude" id="latitude" value="{{$latitude}}" wire:model.fill="latitude"/>
                <x-wireui-input name="longitude" id="longitude" value="{{$longitude}}" wire:model.fill="longitude"/>

                <p class="mt-1 text-sm theme-text">
                    Kliknij na mapie, aby wybrać lokalizację urządzenia.
                </p>
            </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('measurement-devices.index') }}" 
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium theme-text hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                Anuluj
            </a>
            
            @if(isset($measurementDevice))
                <x-wireui-button type="submit" primary label="Aktualizuj urządzenie" />
            @elseif (!isset($measurementDevice))
                <x-wireui-button type="submit" primary label="Dodaj urządzenie" />
            @endif
        </div>
    </form>

    {{--  Leaflet --}}



    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script> 
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"  />

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

            L.marker(["{{ $latitude }}", "{{ $longitude }}"]).addTo(addDeviceMap).bindPopup("{{ $name }}");

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
                @this.call('work_again',lat, lng )

            });
        });
    </script>



</div>


