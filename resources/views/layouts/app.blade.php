<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="theme-container">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net ">
    <link href="https://fonts.bunny.net/css?family=figtree :400,500,600&display=swap" rel="stylesheet" />
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    @wireUiScripts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased">
    <x-banner />
    <x-wireui-notifications />
    <x-wireui-dialog />

    <div class="min-h-screen theme-bg">
        @livewire('navigation-menu')

        @if (isset($header))
            <header class="theme-bg shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 theme-text">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main>
            {{ $slot }}
        </main>
    </div>

    @stack('modals')
    @livewireScripts

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- https://sweetalert2.github.io/ -->
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            
            // Obsługa potwierdzenia usuwania
            Livewire.on('delete_device', (event) => {
                Swal.fire({
                    title: event.confirm?.title || 'Potwierdź usunięcie',
                    text: event.confirm?.description || 'Czy na pewno chcesz usunąć to urządzenie?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: event.confirm?.accept?.label || 'Tak, usuń',
                    cancelButtonText: event.confirm?.reject?.label || 'Anuluj'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('delete_confirmed', {
                            id: event.id
                        });
                    }
                });
            });


            // Obsługa potwierdzenia dodawania urządzenia
            Livewire.on('add_device', (event) => {
                Swal.fire({
                    title: event.confirm?.title || 'Potwierdź decyzję',
                    text: event.confirm?.description || 'Czy chcesz dodać nowe urządzenie zanim wczytasz pomiary ?',
                    icon: 'question',
                    draggable: true,
                    showCancelButton: true,
                    confirmButtonColor: '#80d630ff',
                    cancelButtonColor: '#d33',
                    confirmButtonText: event.confirm?.accept?.label || 'Tak, dodaj',
                    cancelButtonText: event.confirm?.reject?.label || 'Anuluj'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch("add_device_confirmed");
                    }
                });
            });


            // Obsługa powiadomień
            Livewire.on('showToast', (event) => {
                Swal.fire({
                    position: 'top-end',
                    icon: event.type,
                    title: event.message,
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        });
    </script>
    
</body>
</html>