<x-app-layout>
    <div class="flex items-center justify-center">
        <!-- Ramka wokół mapy -->
        <div id="map" class="w-full border-2 border-gray-300 rounded-lg shadow-md m-2.5" style="height: calc(100vh - 84px);"></div>
    </div>

    <!-- Biblioteka Leaflet.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // Inicjalizacja mapy
        const map = L.map('map').setView([52.237049, 21.017532], 6); // Centrum Polski

        // Dodanie warstwy mapy OpenStreetMap
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 18,
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        // Dane urządzeń przekazane z kontrolera
        const devices = @json($devices);

        // Dodanie markerów na mapę
        devices.forEach(device => {
            if (device.latitude && device.longitude) {
                const marker = L.marker([device.latitude, device.longitude]).addTo(map);
                marker.bindPopup(`
                 <div class="text-sm font-medium text-gray-800">
                 <b>${device.name}</b><br>
                `);
            }
        });
    </script>
</x-app-layout>