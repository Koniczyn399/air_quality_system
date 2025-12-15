<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <div class="p-4 bg-white dark:bg-gray-800 border-b border-gray-300 dark:border-gray-700 flex items-center gap-4">
        <div class="text-lg font-semibold">Air quality map</div>
        <div class="flex items-center gap-2">
            <label for="param" class="text-sm">Parameter:</label>
            <select id="param" class="border rounded px-2 py-1 text-sm">
                <option value="">All</option>
                @foreach($parameters as $p)
                    <option value="{{ $p->id }}" @selected((string)$selectedParameterId === (string)$p->id)>{{ $p->name }} ({{ $p->tag }})</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="map" style="height: calc(100vh - 140px);"></div>

    <script>
        const devices = @json($devices);
        const selectedParam = '{{ $selectedParameterId }}';

        console.log('=== MAP DEBUG ===');
        console.log('Total devices:', devices.length);
        console.log('First device:', devices[0]);
        console.log('First device values:', devices[0]?.values);
        console.log('First device first value:', devices[0]?.values[0]);

        const map = L.map('map').setView([52.2, 21.0], 6);
        
        // Tile layers для світлої та темної теми
        const lightLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            maxZoom: 19
        });
        
        const darkLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            maxZoom: 19
        });
        
        // Перевірити поточну тему сторінки
        const isDarkMode = () => document.documentElement.classList.contains('dark');
        
        // Встановити початковий layer
        let currentLayer = isDarkMode() ? darkLayer : lightLayer;
        currentLayer.addTo(map);

        // Функція для оновлення легенди
        function updateLegendStyle() {
            const legendDiv = document.querySelector('.legend > div');
            if (legendDiv) {
                if (isDarkMode()) {
                    legendDiv.style.background = '#1f2937';
                    legendDiv.style.color = '#f3f4f6';
                    legendDiv.style.borderColor = '#374151';
                } else {
                    legendDiv.style.background = 'white';
                    legendDiv.style.color = '#000';
                    legendDiv.style.borderColor = '#ddd';
                }
            }
        }

        // Legenda
        const legend = L.control({ position: 'bottomright' });
        legend.onAdd = function (map) {
            const div = L.DomUtil.create('div', 'legend');
            const bgColor = isDarkMode() ? '#1f2937' : 'white';
            const textColor = isDarkMode() ? '#f3f4f6' : '#000';
            const borderColor = isDarkMode() ? '#374151' : '#ddd';
            
            div.innerHTML = `
                <div style="background: ${bgColor}; color: ${textColor}; padding: 15px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.2); font-size: 13px; font-family: Arial, sans-serif; max-width: 280px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 14px; font-weight: bold;">Legenda kolorów</h4>
                    <div style="margin-bottom: 12px;">
                        <strong style="font-size: 12px;">Zanieczyszczenie powietrza (PM):</strong>
                        <div style="margin-top: 6px;">
                            <div style="display: flex; align-items: center; margin-bottom: 4px;">
                                <div style="width: 16px; height: 16px; background: #2ecc71; border-radius: 3px; margin-right: 8px;"></div>
                                <span>Dobry</span>
                            </div>
                            <div style="display: flex; align-items: center; margin-bottom: 4px;">
                                <div style="width: 16px; height: 16px; background: #f39c12; border-radius: 3px; margin-right: 8px;"></div>
                                <span>Umiarkowany</span>
                            </div>
                            <div style="display: flex; align-items: center; margin-bottom: 4px;">
                                <div style="width: 16px; height: 16px; background: #e67e22; border-radius: 3px; margin-right: 8px;"></div>
                                <span>Zły</span>
                            </div>
                            <div style="display: flex; align-items: center;">
                                <div style="width: 16px; height: 16px; background: #c0392b; border-radius: 3px; margin-right: 8px;"></div>
                                <span>Bardzo zły</span>
                            </div>
                        </div>
                    </div>
                    <div style="border-top: 1px solid #ddd; padding-top: 10px; margin-top: 10px;">
                        <strong style="font-size: 12px;">Temperatura:</strong>
                        <div style="margin-top: 6px; font-size: 12px;">
                            <div style="display: flex; align-items: center; margin-bottom: 3px;">
                                <div style="width: 12px; height: 12px; background: #3498db; border-radius: 50%; margin-right: 8px;"></div>
                                <span>≤0°C - Mróz</span>
                            </div>
                            <div style="display: flex; align-items: center; margin-bottom: 3px;">
                                <div style="width: 12px; height: 12px; background: #16a085; border-radius: 50%; margin-right: 8px;"></div>
                                <span>1-18°C - Zimno</span>
                            </div>
                            <div style="display: flex; align-items: center; margin-bottom: 3px;">
                                <div style="width: 12px; height: 12px; background: #f39c12; border-radius: 50%; margin-right: 8px;"></div>
                                <span>19-24°C - Komfort</span>
                            </div>
                            <div style="display: flex; align-items: center;">
                                <div style="width: 12px; height: 12px; background: #e67e22; border-radius: 50%; margin-right: 8px;"></div>
                                <span>>24°C - Ciepło</span>
                            </div>
                        </div>
                    </div>
                    <div style="border-top: 1px solid ${borderColor}; padding-top: 10px; margin-top: 10px;">
                        <strong style="font-size: 12px;">Wilgotność/Ciśnienie:</strong>
                        <div style="margin-top: 6px; font-size: 12px;">
                            <div style="display: flex; align-items: center; margin-bottom: 3px;">
                                <div style="width: 12px; height: 12px; background: #2ecc71; border-radius: 50%; margin-right: 8px;"></div>
                                <span>Normalna</span>
                            </div>
                            <div style="display: flex; align-items: center;">
                                <div style="width: 12px; height: 12px; background: #f39c12; border-radius: 50%; margin-right: 8px;"></div>
                                <span>Zaburzona</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            return div;
        };
        legend.addTo(map);
        
        // Оновити стиль легенди при ініціалізації
        updateLegendStyle();

        devices.forEach(device => {
            const vals = device.values || [];
            const chosen = selectedParam ? vals.find(v => String(v.parameter_id) === String(selectedParam)) : vals[0];
            const markerColor = chosen?.color || '#808080';

            const marker = L.circleMarker([Number(device.latitude), Number(device.longitude)], {
                color: markerColor,
                fillColor: markerColor,
                radius: 8,
                weight: 2,
                opacity: 1,
                fillOpacity: 0.9
            }).addTo(map);

            let popup = `<div class="text-sm font-medium"><b>${device.name || 'Device'}</b><div class="mt-2">`;
            vals.forEach(v => {
                const vcolor = v.color || '#808080';
                const isSel = selectedParam && String(v.parameter_id) === String(selectedParam);
                popup += `<div style="background:${vcolor};padding:6px;border-radius:6px;${isSel ? 'border:2px solid #000;' : ''};color:#fff;margin-bottom:6px">
                    ${v.parameter?.name || v.parameter_id}: ${v.value}${v.parameter?.unit ? ' ' + v.parameter.unit : ''} (${v.parameter?.tag || ''})
                </div>`;
            });
            popup += `</div></div>`;
            marker.bindPopup(popup);
        });

        document.getElementById('param').addEventListener('change', (e) => {
            const id = e.target.value;
            const url = new URL(window.location.href);
            if (id) url.searchParams.set('parameter_id', id); else url.searchParams.delete('parameter_id');
            window.location.href = url.toString();
        });

        // Спостерігач за змінами теми
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    const dark = isDarkMode();
                    
                    // Змінити tile layer
                    if (dark && map.hasLayer(lightLayer)) {
                        map.removeLayer(lightLayer);
                        map.addLayer(darkLayer);
                    } else if (!dark && map.hasLayer(darkLayer)) {
                        map.removeLayer(darkLayer);
                        map.addLayer(lightLayer);
                    }
                    
                    // Оновити стиль легенди
                    updateLegendStyle();
                }
            });
        });
        
        // Почати спостереження за змінами class на html елементі
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });
    </script>
</x-app-layout>