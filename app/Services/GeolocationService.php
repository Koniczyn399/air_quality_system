<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

class GeolocationService
{
    public static function getCityFromCoordinates(float $latitude, float $longitude): string
    {
        $url = "https://nominatim.openstreetmap.org/reverse";
        $appName = config('app.name', 'LaravelApp');
        $appUrl = config('app.url', 'http://localhost');
        $userAgent = "{$appName}/1.0 ({$appUrl}; mailto:email@example.com)"; // Dodaj kontaktowy email

        Log::info("GeolocationService: Próba pobrania miasta dla współrzędnych: lat: {$latitude}, lon: {$longitude}");

        $response = Http::withHeaders([
            'User-Agent' => $userAgent
        ])->get($url, [
            'format' => 'jsonv2',        
            'lat' => $latitude,
            'lon' => $longitude,
            'accept-language' => 'pl',   // Preferowany język odpowiedzi (polski)
            'addressdetails' => '1',     // Wymuszenie szczegółów adresu (choć jsonv2 zwykle to implikuje)
            // 'zoom' => 10,             // Możesz eksperymentować z poziomem zoom (0-18), np. 10 dla miasta, 14 dla miasteczka/wsi
                                        // Niższy zoom (np. 10) może być lepszy do znalezienia nazwy miasta, gdy współrzędne są bardzo precyzyjne.
        ]);

        // Sprawdzenie, czy zapytanie HTTP zakończyło się sukcesem (status 2xx)
        if ($response->successful()) {
            $data = $response->json();

            // KRYTYCZNE DLA DEBUGOWANIA: Zaloguj całą odpowiedź z Nominatim.
            // To pokaże Ci dokładnie, jakie dane są zwracane.
            Log::info('GeolocationService: Surowe dane odpowiedzi z API Nominatim:', (array) $data);

            // Sprawdzenie, czy odpowiedź nie jest pusta
            if (empty($data)) {
                Log::warning('GeolocationService: Odpowiedź z Nominatim jest pusta.', [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ]);
                return 'Nieznane (pusta odpowiedź API)';
            }

            // Sprawdzenie, czy klucz 'address' istnieje w odpowiedzi
            // To najważniejszy obiekt zawierający komponenty adresu
            if (!isset($data['address'])) {
                Log::warning("GeolocationService: Odpowiedź Nominatim nie zawiera klucza 'address'.", [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'api_response' => $data // Zaloguj całą odpowiedź, jeśli brakuje 'address'
                ]);
                // Czasami, jeśli nie ma 'address', 'display_name' może zawierać użyteczną informację
                return $data['display_name'] ?? 'Nieznane (brak bloku adresu w odpowiedzi API)';
            }

            $address = $data['address'];
            // Zaloguj zawartość bloku 'address', aby zobaczyć dostępne pola
            Log::info('GeolocationService: Blok adresowy (address) z API Nominatim:', $address);

            // Próba znalezienia nazwy lokalizacji w określonej kolejności.
            // Możesz dostosować tę kolejność lub dodać inne klucze na podstawie tego,
            // co zobaczysz w logach dla zmiennej $address.
            // Przykładowe klucze, które mogą zawierać nazwę miejscowości:
            $locationName = $address['city']
                ?? $address['town']
                ?? $address['village']
                ?? $address['hamlet']          // Mała wioska, osada
                ?? $address['suburb']          // Przedmieście (może być nazwą dzielnicy w dużym mieście)
                ?? $address['municipality']    // Gmina
                ?? $address['county']          // Powiat (czasem może być użyteczne, jeśli inne brakuje)
                // ?? $address['city_district'] // Dzielnica miasta
                // ?? $address['quarter']       // Kwartał, część miejscowości
                ?? null;                       // Domyślnie null, jeśli żadne z powyższych nie zostanie znalezione

            if ($locationName !== null) {
                Log::info("GeolocationService: Znaleziono nazwę lokalizacji: {$locationName} dla lat: {$latitude}, lon: {$longitude}");
                return $locationName;
            }

            // Jeśli żaden ze znanych kluczy nie został znaleziony, ale blok adresu istnieje.
            Log::warning('GeolocationService: Nie znaleziono znanego klucza (city, town, village itp.) w bloku adresu.', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'address_block' => $address, // Zaloguj cały blok adresu, aby zobaczyć co zawiera
                'display_name_from_root' => $data['display_name'] ?? 'brak display_name' // display_name z głównego poziomu odpowiedzi
            ]);

            // Jako ostateczność, można spróbować użyć $data['display_name'], jeśli jest bardziej opisowe,
            // lub zwrócić bardziej szczegółowy komunikat.
            // Na przykład, jeśli w $address jest 'road' i 'country', ale nie ma 'city'.
            return 'Nieznane (brak konkretnej miejscowości w adresie)';

        } else {
            // Logowanie błędu, jeśli samo zapytanie HTTP do API Nominatim nie powiodło się
            Log::error('GeolocationService: Zapytanie do API Nominatim nie powiodło się.', [
                'status_code' => $response->status(),
                'response_body' => $response->body(), // Zaloguj surową treść błędu z Nominatim
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
            return 'Nieznane (błąd połączenia z API)';
        }
    }
}

