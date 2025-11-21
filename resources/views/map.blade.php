<x-app-layout>
    <div class="p-4 bg-white dark:bg-gray-800 border-b border-gray-300 dark:border-gray-700">
        <div class="flex items-center gap-4">
            <label for="parameter-select" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Wybierz parametr:
            </label>
            <select id="parameter-select" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                @foreach($parameters as $parameter)
                    <option value="{{ $parameter->name }}" {{ $parameter->name === 'PM2.5' ? 'selected' : '' }}>
                        {{ $parameter->name }}{{ $parameter->unit ? ' (' . $parameter->unit . ')' : '' }}
                    </option>
                @endforeach
            </select>
            <div class="ml-auto flex items-center gap-3 text-xs">
                <span class="font-medium text-gray-700 dark:text-gray-300">Legenda:</span>
                <div class="flex items-center gap-1">
                    <div class="w-4 h-4 rounded-full" style="background-color: #00e400;"></div>
                    <span class="text-gray-700 dark:text-gray-300">Dobre</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-4 h-4 rounded-full" style="background-color: #ffff00;"></div>
                    <span class="text-gray-700 dark:text-gray-300">Umiarkowane</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-4 h-4 rounded-full" style="background-color: #ff7e00;"></div>
                    <span class="text-gray-700 dark:text-gray-300">Szkodliwe</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-4 h-4 rounded-full" style="background-color: #ff0000;"></div>
                    <span class="text-gray-700 dark:text-gray-300">Niebezpieczne</span>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-center">
        <div id="map" class="w-full border-2 border-gray-300 dark:border-gray-700 rounded-lg shadow-md m-2.5" style="height: calc(100vh - 84px);"></div>    
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const htmlElement = document.documentElement;
        const map = L.map('map').setView([52.237049, 21.017532], 6);
        let currentTileLayer;
        let markers = [];

        const lightTileUrl = 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png';
        const darkTileUrl = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'; 
        function loadTileLayer(tileUrl) {
            if (currentTileLayer) {
                map.removeLayer(currentTileLayer);
            }
            currentTileLayer = L.tileLayer(tileUrl, {
                maxZoom: 18,
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
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

        const devices = @json($devices ?? []);

        console.log('Devices:', devices);

    function createMarkers(selectedParameter) {
       markers.forEach(marker => map.removeLayer(marker));
            markers = [];

            devices.forEach(device => {
                if (device.latitude && device.longitude && device.values && device.values.length > 0) {
                    const parameterValue = device.values.find(v => 
                        v.parameter && v.parameter.name === selectedParameter
                    );
                    
                    if (parameterValue && parameterValue.color) {
                        const color = parameterValue.color;
                        
                        const marker = L.circleMarker([device.latitude, device.longitude], {
                            radius: 12,
                            fillColor: color,
                            color: '#fff',
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 0.8
                        }).addTo(map);

                        let popupContent = `
                            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                <b>${device.name}</b><br>
                                <div class="mt-2 space-y-1">
                        `;
                        
                        device.values.forEach(value => {
                            if (value.parameter && value.color) {
                                const bgColor = value.color;
                                const isSelected = value.parameter.name === selectedParameter;
                                popupContent += `
                                    <div style="background-color: ${bgColor}; padding: 4px 8px; border-radius: 4px; ${isSelected ? 'border: 2px solid #000;' : ''}" class="text-white text-sm">
                                        ${value.parameter.name}: ${value.value}${value.parameter.unit ? ' ' + value.parameter.unit : ''}
                                    </div>
                                `;
                            }
                        });
                        
                        popupContent += `
                                </div>
                            </div>
                        `;
                        
                        marker.bindPopup(popupContent);
                        markers.push(marker);
                    }
                }
            });

            if (markers.length > 0) {
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }

        createMarkers('PM2.5');

        document.getElementById('parameter-select').addEventListener('change', function(e) {
            createMarkers(e.target.value);
        });
    </script>
</x-app-layout>