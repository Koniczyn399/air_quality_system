<x-app-layout>
    <div class="flex items-center justify-center">
        <div id="map" class="w-full border-2 border-gray-300 dark:border-gray-700 rounded-lg shadow-md m-2.5" style="height: calc(100vh - 84px);"></div>    
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const htmlElement = document.documentElement;
        const map = L.map('map').setView([52.237049, 21.017532], 6);
        let currentTileLayer;

        const lightTileUrl = 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png';
        const darkTileUrl = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'; // Możesz zmienić na inny ciemny motyw

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

        // Dane urządzeń przekazane z kontrolera
        const devices = @json($devices);

        // Dodanie markerów na mapę
        devices.forEach(device => {
            if (device.latitude && device.longitude) {
                const marker = L.marker([device.latitude, device.longitude]).addTo(map);
                marker.bindPopup(`
                    <div class="text-sm font-medium text-gray-800 dark:text-gray-200">
                        <b>${device.name}</b><br>
                    </div>
                `);
            }
        });
    </script>
</x-app-layout>