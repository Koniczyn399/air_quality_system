<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl theme-text leading-tight">
            {{ __('Witaj') }}
        </h2>
    </x-slot>

   
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <div class="py-12 theme-container">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="theme-bg overflow-hidden shadow-xl sm:rounded-lg p-4">

                {{-- Kontener na mapę --}}
                <div class="mb-4"> 
                    <div id="map" class="w-full border-2 border-gray-300 dark:border-gray-700 rounded-lg shadow-md" style="height: 500px;"></div> 
                </div>

                <dd class="mt-1 sm:mt-0 sm:col-span-2 text-sm theme-text">
                    &nbsp;<b>Podsumowanie</b> <br>
                    &nbsp;Urządzenia do kalibracji: <br>
                </dd>
                @foreach ($data as $device)
                    <hr class="my-2">
                    <dd class="mt-1 sm:mt-0 sm:col-span-2 text-sm theme-text">
                        &nbsp;&nbsp;&nbsp; {{ $device->name }}: {{  $device->calibration_date }}
                        <div class="" style="right: 30%;">
                        &nbsp;&nbsp;<x-wireui-button  href="{{ route('measurement-devices.show', ['measurement_device' => $device->id])}}" secondary class="mr-2"
                        label="{{ __('translation.placeholder.show') }}"
                        />
                        </div>
                        <br>
                    </dd>
                @endforeach

            </div>
        </div>
    </div>

    {{-- Skrypt Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () { 
            const htmlElement = document.documentElement;
            const map = L.map('map').setView([52.237049, 21.017532], 6); 
            let currentTileLayer;

            const lightTileUrl = 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png';
            const darkTileUrl = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';

            function loadTileLayer(tileUrl) {
                if (currentTileLayer) {
                    map.removeLayer(currentTileLayer);
                }
                currentTileLayer = L.tileLayer(tileUrl, {
                    maxZoom: 18,
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, © <a href="https://carto.com/attributions">CARTO</a>', // Dodano CARTO do atrybucji dla basemapów CartoDB
                }).addTo(map);
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


            const devices = @json($data ?? []); 

            // Dodanie markerów na mapę
            if (devices && devices.length > 0) {
                devices.forEach(device => {
                    if (device.latitude && device.longitude) {
                        const marker = L.marker([device.latitude, device.longitude]).addTo(map);
                        marker.bindPopup(`
                            <div class="mt-1 sm:mt-0 sm:col-span-2 text-sm theme-text">
                                <b>${device.name}</b><br>
                                {{-- Data kalibracji: ${device.calibration_date} --}}
                            </div>
                        `);
                    }
                });
            } else {
                console.log('Brak danych urządzeń do wyświetlenia na mapie lub dane nie zawierają współrzędnych.');
            }
        });
    </script>
</x-app-layout>