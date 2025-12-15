<x-app-layout>
    {{-- LOGIKA PHP: PRZYGOTOWANIE DANYCH --}}
    @php
        $user = auth()->user();
        $isTechnical = $user->hasAnyRole(['admin', 'mainteiner']);

        // 1. ZABEZPIECZENIE: Upewniamy się, że $data to kolekcja
        $allDevices = ($data instanceof \Illuminate\Support\Collection) ? $data : collect($data);

        // 2. FILTROWANIE WIDOCZNOŚCI URZĄDZEŃ
        // Admin widzi wszystkie. Użytkownik widzi TYLKO te o statusie 'active'.
        if ($isTechnical) {
            $devicesToDisplay = $allDevices;
        } else {
            $devicesToDisplay = $allDevices->filter(function ($device) {
                return $device->status === 'active';
            });
        }

        // 3. STATYSTYKI DLA ADMINA (Liczymy zawsze ze wszystkich urządzeń w bazie)
        $stats = [
            'total'    => $allDevices->count(),
            'active'   => $allDevices->where('status', 'active')->count(),
            'repair'   => $allDevices->where('status', 'in_repair')->count(),
            'inactive' => $allDevices->where('status', 'inactive')->count(),
            'upcoming' => $allDevices->filter(fn($d) => $d->next_calibration_date && $d->next_calibration_date > now() && $d->next_calibration_date <= now()->addDays(30))->count(),
            'expired'  => $allDevices->filter(fn($d) => $d->next_calibration_date && $d->next_calibration_date < now())->count(),
        ];

        // 4. MAPOWANIE URZĄDZEŃ DO WYŚWIETLENIA
        // !!! TUTAJ BYŁ BŁĄD - DODANO "use ($isTechnical)" !!!
        $mappedDevices = $devicesToDisplay->map(function ($device) use ($isTechnical) {
            
            // --- POBIERANIE DANYCH POMIAROWYCH ---
            $lastMeasurement = \App\Models\Measurement::where('device_id', $device->id)
                ->with(['values.parameter']) 
                ->orderBy('measurements_date', 'desc') 
                ->first();

            $pm1 = null; $pm25 = null; $pm10 = null;
            $temp = null; $hum = null; $pres = null;
            $measurementDate = null;

            if ($lastMeasurement) {
                $measurementDate = $lastMeasurement->measurements_date;

                foreach ($lastMeasurement->values as $val) {
                    $rawName = $val->parameter->name ?? $val->parameter_name ?? '';
                    $paramName = mb_strtoupper(trim($rawName));
                    $v = floatval($val->value);

                    if (in_array($paramName, ['PM1', 'PM1.0', 'PM 1.0', 'PYŁ PM1'])) $pm1 = $v;
                    elseif (in_array($paramName, ['PM2.5', 'PM2,5', 'PM 2.5', 'PYŁ ZAWIESZONY PM2.5'])) $pm25 = $v;
                    elseif (in_array($paramName, ['PM10', 'PM 10', 'PYŁ ZAWIESZONY PM10'])) $pm10 = $v;
                    elseif (in_array($paramName, ['TEMP', 'TEMPERATURA', 'TEMPERATURE', 'T'])) $temp = $v;
                    elseif (in_array($paramName, ['HUM', 'WILGOTNOŚĆ', 'WILGOTNOSC', 'HUMIDITY', 'H'])) $hum = $v;
                    elseif (in_array($paramName, ['PRES', 'CIŚNIENIE', 'CISNIENIE', 'PRESSURE', 'P', 'BAROMETER'])) $pres = $v;
                }
            } else {
                // Fallback do values bezpośrednio
                $latestVal = $device->values->sortByDesc('created_at')->first();
                if ($latestVal) {
                    $measurementDate = $latestVal->created_at;
                }
            }

            // OBLICZANIE STATUSU (CAQI)
            $status = 'Brak danych';
            $color = '#9ca3af'; // szary
            
            if ($pm25 !== null) {
                if ($pm25 <= 25) { $status = 'Dobra'; $color = '#22c55e'; }
                elseif ($pm25 <= 50) { $status = 'Średnia'; $color = '#eab308'; }
                else { $status = 'Zła'; $color = '#ef4444'; }
            } elseif ($pm10 !== null) {
                if ($pm10 <= 50) { $status = 'Dobra'; $color = '#22c55e'; }
                elseif ($pm10 <= 100) { $status = 'Średnia'; $color = '#eab308'; }
                else { $status = 'Zła'; $color = '#ef4444'; }
            } elseif ($temp !== null || $pres !== null) {
                $status = 'Aktywny';
                $color = '#3b82f6'; 
            }

            $formattedDate = 'Brak pomiarów';
            if ($measurementDate) {
                 $dateObj = is_string($measurementDate) ? \Carbon\Carbon::parse($measurementDate) : $measurementDate;
                 $formattedDate = $dateObj->format('d.m.Y H:i');
            }

            // Dodanie statusu do nazwy tylko dla admina (korzystamy z przekazanego $isTechnical)
            $displayName = $device->name;
            if ($isTechnical && $device->status !== 'active') {
                $displayName .= ' (' . ($device->status === 'in_repair' ? 'Serwis' : 'Nieakt.') . ')';
            }

            return [
                'id' => $device->id,
                'name' => $displayName,
                'latitude' => $device->latitude,
                'longitude' => $device->longitude,
                'next_calibration_date' => $device->next_calibration_date ? $device->next_calibration_date->format('Y-m-d') : '-',
                'pm1' => $pm1, 'pm25' => $pm25, 'pm10' => $pm10, 'temp' => $temp, 'hum' => $hum, 'pres' => $pres,
                'last_reading' => $formattedDate,
                'status' => $status,
                'color' => $color,
                'device_status' => $device->status
            ];
        });
        
        $mappedDevices = $mappedDevices->values();
    @endphp

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl theme-text leading-tight flex items-center gap-2">
                @if($isTechnical)
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Panel Techniczny ({{ $user->roles->pluck('name')->first() ?? 'Staff' }})</span>
                @else
                    
                    <span>Twoja Okolica</span>
                @endif
            </h2>
            <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 font-mono bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                {{ now()->format('d.m.Y, H:i') }}
            </div>
        </div>
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <div class="py-8 theme-container">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if($isTechnical)
                {{-- ========================================================= --}}
                {{-- WIDOK 1: ADMIN & MAINTEINER (TECHNICZNY)                  --}}
                {{-- ========================================================= --}}
                
                {{-- GRID 6 KAFELKÓW ZE STATYSTYKAMI --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- 1. Wszystkie --}}
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5 border-l-4 border-blue-500 flex items-center justify-between">
                        <div><p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Wszystkie</p><p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['total'] }}</p></div>
                        <div class="p-3 bg-blue-50 dark:bg-blue-900 rounded-full text-blue-600"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg></div>
                    </div>
                    {{-- 2. Aktywne --}}
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5 border-l-4 border-green-500 flex items-center justify-between">
                        <div><p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Aktywne</p><p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['active'] }}</p></div>
                        <div class="p-3 bg-green-50 dark:bg-green-900 rounded-full text-green-600"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    </div>
                    {{-- 3. W serwisie --}}
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5 border-l-4 border-orange-400 flex items-center justify-between">
                        <div><p class="text-xs font-bold text-gray-400 uppercase tracking-wider">W serwisie</p><p class="text-3xl font-bold text-orange-500 dark:text-orange-300 mt-1">{{ $stats['repair'] }}</p></div>
                        <div class="p-3 bg-orange-50 dark:bg-orange-900 rounded-full text-orange-500"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                    </div>
                    {{-- 4. Nieaktywne --}}
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5 border-l-4 border-gray-400 flex items-center justify-between">
                        <div><p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nieaktywne</p><p class="text-3xl font-bold text-gray-600 dark:text-gray-400 mt-1">{{ $stats['inactive'] }}</p></div>
                        <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full text-gray-500"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg></div>
                    </div>
                    {{-- 5. Kalibracja < 30 dni --}}
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5 border-l-4 border-yellow-400 flex items-center justify-between">
                        <div><p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kalibracja (< 30 dni)</p><p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $stats['upcoming'] }}</p></div>
                        <div class="p-3 bg-yellow-50 dark:bg-yellow-900 rounded-full text-yellow-600"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    </div>
                    {{-- 6. Po terminie --}}
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5 border-l-4 border-red-500 flex items-center justify-between">
                        <div><p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Po terminie!</p><p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $stats['expired'] }}</p></div>
                        <div class="p-3 bg-red-50 dark:bg-red-900 rounded-full text-red-600"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-xl rounded-lg p-1">
                        <div id="map-tech" class="w-full h-[550px] rounded z-0 border border-gray-200 dark:border-gray-700"></div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg flex flex-col h-[600px] p-4">
                        <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4">Lista Serwisowa</h3>
                        <div class="overflow-y-auto custom-scrollbar flex-1 pr-2 space-y-2">
                            @foreach ($mappedDevices as $device)
                                <div class="p-3 border border-gray-100 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <div class="flex justify-between items-center">
                                        <div class="font-semibold text-sm text-gray-700 dark:text-gray-300">{{ $device['name'] }}</div>
                                        @if($device['device_status'] !== 'active')
                                            <span class="text-[10px] px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-500 uppercase">{{ $device['device_status'] }}</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Termin: 
                                        @php
                                            $calDate = \Carbon\Carbon::parse($device['next_calibration_date']);
                                            $isExpired = $calDate->isPast() && $device['next_calibration_date'] !== '-';
                                            $isUpcoming = $calDate->lte(now()->addDays(30)) && !$isExpired && $device['next_calibration_date'] !== '-';
                                        @endphp
                                        <span class="{{ $isExpired ? 'text-red-500 font-bold' : ($isUpcoming ? 'text-yellow-600 font-bold' : '') }}">
                                            {{ $device['next_calibration_date'] }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-400">Ost. odczyt: {{ $device['last_reading'] }}</div>
                                    <div class="mt-2 text-right">
                                        <a href="{{ route('measurement-devices.show', $device['id']) }}" class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded">Edytuj</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            @else
                {{-- WIDOK USERA (Tylko aktywne urządzenia) --}}
                <div id="location-status-bar" class="hidden w-full p-4 rounded-lg mb-4 text-sm font-medium shadow-sm flex items-center gap-3"></div>

                <div id="nearest-sensor-widget" class="hidden bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-6 mb-6 border-l-8 relative overflow-hidden transition-all duration-500">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <svg class="w-32 h-32 text-current" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
                    </div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
                            <div>
                                <span class="inline-block py-1 px-2 rounded bg-green-100 text-green-700 text-[10px] font-bold tracking-wider uppercase mb-1">Najbliższa stacja</span>
                                <h2 id="widget-name" class="text-2xl font-bold text-gray-800 dark:text-white leading-tight">--</h2>
                                <p id="widget-distance" class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">... km stąd</p>
                            </div>
                            <div class="text-right">
                                <div id="widget-status-badge" class="inline-block px-3 py-1 rounded-full text-white text-sm font-bold shadow-sm mb-1">--</div>
                                <p id="widget-time" class="text-xs text-gray-400 font-mono">Brak danych</p>
                            </div>
                        </div>
                        <div id="widget-parameters-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden p-1 relative">
                        <div id="map-user" class="w-full h-[500px] rounded-xl z-0"></div>
                        <div id="map-loader" class="absolute inset-0 z-[500] bg-white/80 dark:bg-gray-800/80 flex items-center justify-center backdrop-blur-sm">
                            <div class="text-center">
                                <svg class="animate-spin h-10 w-10 text-blue-500 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <p class="text-gray-600 dark:text-gray-300 font-medium">Lokalizowanie...</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-6 flex flex-col h-[500px]">
                        <div class="mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-bold text-gray-800 dark:text-gray-200 text-lg">Inne w okolicy</h3>
                            <p class="text-sm text-gray-500">Pozostałe stacje w promieniu 50 km</p>
                        </div>
                        <div id="nearby-devices-list" class="overflow-y-auto custom-scrollbar flex-1 pr-1 space-y-3"></div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () { 
            const isTechnical = @json($isTechnical);
            const devices = @json($mappedDevices); 
            const htmlElement = document.documentElement;

            // Funkcja pomocnicza do pobierania URL w zależności od aktualnej klasy 'dark'
            const getTileUrl = (mode) => {
                // Sprawdzamy klasę 'dark' na żywo w momencie wywołania funkcji
                if (htmlElement.classList.contains('dark')) {
                    return 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
                }
                return mode === 'tech' 
                    ? 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png' 
                    : 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager_labels_under/{z}/{x}/{y}{r}.png';
            };

            // 1. LOGIKA ADMINA
            if (isTechnical) {
                const map = L.map('map-tech').setView([52.237049, 21.017532], 6);
                
                // ZMIANA 1: Przypisujemy warstwę do zmiennej, aby móc ją potem edytować
                const tileLayer = L.tileLayer(getTileUrl('tech'), { attribution: '&copy; OpenStreetMap' }).addTo(map);
                
                // ZMIANA 2: Nasłuchiwanie zmiany motywu (Observer)
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                            // Gdy zmieni się klasa w <html>, aktualizujemy URL mapy
                            tileLayer.setUrl(getTileUrl('tech'));
                        }
                    });
                });
                observer.observe(htmlElement, { attributes: true });

                const bounds = L.latLngBounds();
                devices.forEach(device => {
                    if (device.latitude && device.longitude) {
                        let iconColor = '#3B82F6';
                        if (device.device_status === 'inactive') iconColor = '#9CA3AF';
                        if (device.device_status === 'in_repair') iconColor = '#F97316';

                        L.marker([device.latitude, device.longitude], {
                            icon: L.divIcon({ className: 'admin-marker', html: `<div style="background-color: ${iconColor}; width: 14px; height: 14px; border-radius: 50%; border: 2px solid white;"></div>`, iconSize: [14, 14] })
                        }).addTo(map).bindPopup(`<b>${device.name}</b><br>ID: ${device.id}<br>Status: ${device.device_status}`);
                        bounds.extend([device.latitude, device.longitude]);
                    }
                });
                if(devices.length) map.fitBounds(bounds, { padding: [50, 50] });
                return;
            }

            // 2. LOGIKA USERA (TYLKO AKTYWNE)
            const mapUser = L.map('map-user').setView([52.237049, 21.017532], 6);
            
            // ZMIANA 3: To samo dla Usera - przypisanie do zmiennej
            const tileLayerUser = L.tileLayer(getTileUrl('user'), { attribution: '&copy; OpenStreetMap' }).addTo(mapUser);

            // ZMIANA 4: Nasłuchiwanie zmiany motywu dla mapy usera
            const observerUser = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        tileLayerUser.setUrl(getTileUrl('user'));
                    }
                });
            });
            observerUser.observe(htmlElement, { attributes: true });

            
            const loader = document.getElementById('map-loader');
            const statusDiv = document.getElementById('location-status-bar');
            const listContainer = document.getElementById('nearby-devices-list');
            const widget = document.getElementById('nearest-sensor-widget');
            const widgetGrid = document.getElementById('widget-parameters-grid');

            // ... reszta Twojego kodu (funkcje getDistanceFromLatLonInKm, createParamCard, updateNearestWidget, handleUserLocation) ...
            // (Skopiuj tutaj resztę funkcji bez zmian, bo one działają poprawnie)

            function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
                const R = 6371; 
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon/2) * Math.sin(dLon/2);
                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            }

            function createParamCard(label, value, unit, icon, colorClass = 'text-gray-700 dark:text-gray-300') {
                return `
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 flex flex-col items-center justify-center text-center hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <div class="text-xl mb-1">${icon}</div>
                        <span class="text-[10px] text-gray-500 uppercase font-bold tracking-wide">${label}</span>
                        <div class="font-extrabold text-lg ${colorClass} leading-none mt-1">${value}</div>
                        <span class="text-[10px] text-gray-400">${unit}</span>
                    </div>
                `;
            }

            function updateNearestWidget(device) {
                if (!device) return;

                widget.classList.remove('hidden');
                
                let borderClass = 'border-gray-300';
                if(device.color === '#22c55e') borderClass = 'border-green-500';
                else if(device.color === '#eab308') borderClass = 'border-yellow-400';
                else if(device.color === '#ef4444') borderClass = 'border-red-500';

                widget.className = `bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-6 mb-6 border-l-8 ${borderClass} relative overflow-hidden transition-all duration-500`;

                document.getElementById('widget-name').innerText = device.name;
                document.getElementById('widget-distance').innerHTML = `
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    ${device.distance.toFixed(2)} km od Ciebie
                `;
                document.getElementById('widget-time').innerText = `Ostatni pomiar: ${device.last_reading}`;
                
                const statusBadge = document.getElementById('widget-status-badge');
                statusBadge.innerText = device.status;
                statusBadge.style.backgroundColor = device.color;

                let gridHtml = '';

                if (device.pm1 !== null) gridHtml += createParamCard('PM 1.0', device.pm1, 'µg/m³', '🔬', 'text-gray-800 dark:text-gray-100');
                if (device.pm25 !== null) gridHtml += createParamCard('PM 2.5', device.pm25, 'µg/m³', '☁️', 'text-gray-800 dark:text-gray-100');
                if (device.pm10 !== null) gridHtml += createParamCard('PM 10', device.pm10, 'µg/m³', '🌫️', 'text-gray-800 dark:text-gray-100');
                if (device.temp !== null) gridHtml += createParamCard('Temp', device.temp, '°C', '🌡️', 'text-blue-600 dark:text-blue-300');
                if (device.hum !== null) gridHtml += createParamCard('Wilgotność', device.hum, '%', '💧', 'text-indigo-600 dark:text-indigo-300');
                if (device.pres !== null) gridHtml += createParamCard('Ciśnienie', device.pres, 'hPa', '⏲️', 'text-purple-600 dark:text-purple-300');

                widgetGrid.innerHTML = gridHtml;
            }

            function handleUserLocation(position) {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;
                const searchRadiusKm = 50;

                loader.classList.add('hidden');

                L.marker([userLat, userLng], { 
                    icon: L.divIcon({
                        className: 'my-location',
                        html: `<div class="relative flex h-5 w-5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span><span class="relative inline-flex rounded-full h-5 w-5 bg-blue-600 border-2 border-white shadow-lg"></span></div>`,
                        iconSize: [20, 20]
                    }), zIndexOffset: 1000 
                }).addTo(mapUser).bindPopup("Twoja lokalizacja").openPopup();

                L.circle([userLat, userLng], { color: '#3B82F6', fillOpacity: 0.05, radius: searchRadiusKm * 1000 }).addTo(mapUser);

                let nearbyDevices = [];
                
                devices.forEach(device => {
                    if (device.latitude && device.longitude) {
                        const distance = getDistanceFromLatLonInKm(userLat, userLng, device.latitude, device.longitude);
                        
                        let popupContent = `<b>${device.name}</b><br>Jakość: ${device.status}`;
                        if(device.pm25 !== null) popupContent += `<br>PM2.5: ${device.pm25}`;
                        if(device.temp !== null) popupContent += `<br>Temp: ${device.temp}°C`;

                        L.marker([device.latitude, device.longitude], {
                            icon: L.divIcon({ html: `<div style="background-color: ${device.color}; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></div>`, iconSize: [16, 16] })
                        }).addTo(mapUser).bindPopup(popupContent);

                        if (distance <= searchRadiusKm) {
                            device.distance = distance;
                            nearbyDevices.push(device);
                        }
                    }
                });

                nearbyDevices.sort((a, b) => a.distance - b.distance);
                mapUser.setView([userLat, userLng], 10);
                listContainer.innerHTML = '';

                if (nearbyDevices.length > 0) {
                    updateNearestWidget(nearbyDevices[0]);
                    nearbyDevices.forEach((device, index) => {
                        const isClosest = index === 0;
                        const item = document.createElement('div');
                        item.className = `flex items-center justify-between p-3 rounded-xl transition cursor-pointer border ${isClosest ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800' : 'hover:bg-gray-50 dark:hover:bg-gray-700 border-transparent hover:border-gray-200'}`;
                        item.onclick = function() { mapUser.flyTo([device.latitude, device.longitude], 14); };
                        
                        let listDetails = '';
                        if (device.pm25 !== null) listDetails = `PM2.5: ${device.pm25}`;
                        else if (device.pm10 !== null) listDetails = `PM10: ${device.pm10}`;
                        else if (device.temp !== null) listDetails = `Temp: ${device.temp}°C`;
                        else listDetails = device.status;

                        item.innerHTML = `
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full shadow-sm ring-2 ring-white dark:ring-gray-800" style="background-color: ${device.color}"></div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                        ${device.name} ${isClosest ? '<span class="text-[10px] text-green-600 font-bold ml-1">(Najbliższy)</span>' : ''}
                                    </div>
                                    <div class="text-xs text-gray-500 font-bold">${device.distance.toFixed(1)} km</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs font-bold text-gray-600 dark:text-gray-300">${listDetails}</div>
                                <div class="text-[10px] text-gray-400">${device.last_reading.split(' ')[0]}</div>
                            </div>
                        `;
                        listContainer.appendChild(item);
                    });
                } else {
                    statusDiv.classList.remove('hidden');
                    statusDiv.className = "bg-red-100 text-red-800 p-4 rounded mb-4 text-sm flex gap-2 items-center";
                    statusDiv.innerHTML = `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Brak czujników w promieniu 50 km.`;
                    mapUser.setView([userLat, userLng], 8);
                }
            }

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(handleUserLocation, () => {
                    loader.classList.add('hidden');
                    statusDiv.classList.remove('hidden');
                    statusDiv.innerText = "Nie udało się pobrać lokalizacji.";
                });
            } else {
                loader.classList.add('hidden');
            }
        });
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #4b5563; }
        .leaflet-popup-content-wrapper { border-radius: 12px; }
    </style>
</x-app-layout>