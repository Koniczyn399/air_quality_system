# System Monitorowania Jakości Powietrza

Projekt zespołowy realizowany w ramach zajęć. Aplikacja internetowa służąca do gromadzenia, zarządzania i wizualizacji danych o jakości powietrza. System umożliwia monitorowanie stanu czujników, przeglądanie pomiarów na interaktywnej mapie oraz generowanie raportów.

## Wykorzystane technologie

W projekcie wykorzystano następujące technologie (wersje):

- **Laravel 12** (PHP 8.2+) - backend aplikacji
- **Livewire 3** - dynamiczne komponenty frontendowe
- **Tailwind CSS 3** - warstwa wizualna (obsługa trybu jasnego i ciemnego)
- **MySQL 8.0** - baza danych
- **Docker & Laravel Sail** - środowisko uruchomieniowe
- **Spatie Permission** - zarządzanie rolami i uprawnieniami
- **WireUI & PowerGrid** - komponenty interfejsu (tabele, formularze)
- **OpenStreetMap** - wizualizacja danych na mapie

## Wymagania wstępne

Aby uruchomić projekt, upewnij się, że posiadasz zainstalowane:

- **Docker** (Docker Desktop lub Docker Engine) - musi być uruchomiony.
- **Git** - do pobrania repozytorium.

## Instrukcja uruchomienia

Projekt jest przygotowany do pracy w środowisku Docker przy użyciu Laravel Sail.

### Krok po kroku

1. Sklonuj repozytorium:
   ```bash
   git clone <adres-repozytorium>
   cd air_quality_system
   ```

2. Utwórz plik konfiguracyjny:
   ```bash
   cp .env.example .env
   ```

3. Zainstaluj zależności PHP (skrypt wykorzystuje tymczasowy kontener):
   ```bash
   bash laravel_install_vendor.sh
   ```

4. Uruchom aplikację:
   ```bash
   ./vendor/bin/sail up -d
   ```

5. Wygeneruj klucz aplikacji:
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

6. Zainstaluj zależności frontendowe i zbuduj pliki zasobów:
   ```bash
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run build
   ```

7. Uruchom migracje bazy danych wraz z danymi startowymi:
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```

Aplikacja będzie dostępna pod adresem: http://localhost

## Dostęp do systemu (Logowanie)

Po uruchomieniu seedera (`php artisan migrate --seed`), w bazie danych zostaną utworzone domyślne konta testowe dla każdej z ról. Hasło dla wszystkich kont to: `12345678`.

| Rola | Email | Hasło |
| :--- | :--- | :--- |
| **Administrator** | `admin@example.com` | `12345678` |
| **Serwisant** | `mainteiner@example.com` | `12345678` |
| **Użytkownik** | `test@example.com` | `12345678` |

## Zatrzymanie projektu

Aby zatrzymać działające kontenery, wykonaj w terminalu polecenie:
```bash
./vendor/bin/sail down
```
Jeśli uruchomiłeś serwer deweloperski (`npm run dev`) w osobnym terminalu, możesz go zatrzymać skrótem klawiszowym `Ctrl + C`.

## Funkcjonalności systemu

### Zarządzanie Użytkownikami
- Rejestracja i logowanie użytkowników.
- System ról i uprawnień (Administrator, Serwisant, Użytkownik).
- Zarządzanie kontami użytkowników przez administratora (dodawanie, edycja, usuwanie).

### Urządzenia Pomiarowe
- Ewidencja urządzeń pomiarowych (nazwa, model, numer seryjny).
- Monitorowanie statusu urządzeń (aktywne, nieaktywne, w naprawie).
- Śledzenie dat kalibracji (powiadomienia o zbliżającym się terminie na dashboardzie).
- Historia zmian statusów urządzeń.
- Przypisywanie lokalizacji geograficznej do urządzeń.

### Pomiary i Dane
- Gromadzenie danych pomiarowych z urządzeń.
- Import danych z plików CSV.
- Eksport danych pomiarowych.
- Generowanie raportów PDF (systemowe, wartości, dla konkretnych urządzeń).
- Przeglądanie szczegółowych wartości parametrów dla każdego pomiaru.

### Wizualizacja i Mapa
- Interaktywna mapa z naniesionymi lokalizacjami czujników.
- Wizualizacja stanu powietrza za pomocą kolorowych znaczników.
- Dashboard prezentujcy kluczowe informacje (np. urządzenia wymagające kalibracji).

## Autorzy
- Szymon Pinczak
- Michał Grabka
- Vladyslava Mamchenko
- Jakub Kurtyka
