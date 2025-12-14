<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Chmurexpol</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */@layer theme{:root,:host{--font-sans:'Instrument Sans',ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";--font-serif:ui-serif,Georgia,Cambria,"Times New Roman",Times,serif;--font-mono:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;--color-red-50:oklch(.971 .013 17.38);--color-sky-50:oklch(.977 .013 236.62);--color-sky-100:oklch(.951 .026 236.824);--color-sky-600:oklch(.588 .158 241.966);--color-sky-900:oklch(.391 .09 240.876);--color-white:#fff;--spacing:.25rem;--text-sm:.875rem;--text-base:1rem;--text-lg:1.125rem;--text-xl:1.25rem;--text-2xl:1.5rem;--text-3xl:1.875rem;--text-4xl:2.25rem;--font-weight-medium:500;--font-weight-bold:700;--radius-lg:.5rem;--radius-xl:.75rem;--shadow-xl:0 20px 25px -5px #0000001a,0 8px 10px -6px #0000001a;--default-font-family:var(--font-sans);}}@layer base{*,:after,:before,::backdrop{box-sizing:border-box;border:0 solid;margin:0;padding:0}html,:host{-webkit-text-size-adjust:100%;font-family:var(--default-font-family)}body{line-height:inherit}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}img,svg{vertical-align:middle;display:block}img{max-width:100%;height:auto}button{font:inherit;color:inherit;background-color:#0000}}@layer utilities{.absolute{position:absolute}.relative{position:relative}.inset-0{inset:0}.flex{display:flex}.hidden{display:none}.h-full{height:100%}.min-h-screen{min-height:100vh}.w-full{width:100%}.flex-col{flex-direction:column}.items-center{align-items:center}.justify-center{justify-content:center}.justify-between{justify-content:space-between}.gap-4{gap:1rem}.gap-6{gap:1.5rem}.rounded-lg{border-radius:var(--radius-lg)}.bg-white{background-color:var(--color-white)}.bg-sky-50{background-color:var(--color-sky-50)}.p-6{padding:1.5rem}.px-5{padding-inline:1.25rem}.py-2{padding-block:0.5rem}.text-center{text-align:center}.text-sky-600{color:var(--color-sky-600)}.text-gray-900{color:#1b1b18}.text-gray-600{color:#706f6c}.shadow-xl{box-shadow:var(--shadow-xl)}.transition{transition-property:all;transition-timing-function:cubic-bezier(.4,0,.2,1);transition-duration:.15s}.hover\:scale-105:hover{transform:scale(1.05)}}@media (width>=64rem){.lg\:flex-row{flex-direction:row}.lg\:text-left{text-align:left}.lg\:w-1\/2{width:50%}.lg\:p-12{padding:3rem}}
                /* Zastąpiłem skróconą wersję CSS powyżej, aby kod był czytelny w odpowiedzi. 
                   W Twoim projekcie Laravel po prostu zostaw ten wielki blok CSS, który wkleiłeś w pytaniu. 
                   Kluczowe jest, abyś miał dostęp do klas takich jak 'text-sky-600', 'bg-sky-50' itp. 
                   które są w domyślnej paczce. */
            </style>
             @if (!file_exists(public_path('build/manifest.json')))
             <style>
                /* Tu wklej ten BARDZO DŁUGI kod CSS z Twojego oryginalnego pytania, 
                   jeśli aplikacja nie ładuje app.css.
                   Zostawiam to puste w podglądzie, żebyś widział strukturę HTML poniżej. */
             </style>
            @endif
        @endif
    </head>
    
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex flex-col min-h-screen font-sans">
        
        <header class="w-full px-6 py-4 lg:px-8 flex items-center justify-between">
            <div class="font-bold text-xl tracking-tight flex items-center gap-2">
                <span class="text-sky-600 dark:text-sky-400">Chumrex</span>pol
            </div>

            @if (Route::has('login'))
                <nav class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-sky-600 dark:hover:text-sky-400 transition">
                            Panel Główny
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-sky-600 dark:hover:text-sky-400 transition">
                            Logowanie
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="hidden sm:inline-block px-4 py-2 text-sm font-medium bg-[#1b1b18] dark:bg-white text-white dark:text-black rounded-full hover:opacity-90 transition">
                                Rejestracja
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <main class="flex-grow flex items-center justify-center p-6 lg:p-8">
            <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                
                <div class="flex flex-col items-start space-y-6 order-2 lg:order-1">
                    <h1 class="text-4xl lg:text-6xl font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC] leading-tight">
                    Monitoruj. <br/>
                    Analizuj. <br/>
                    <span class="text-sky-600 dark:text-sky-400">Oddychaj.</span>
                    </h1>
                    
                    <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] leading-relaxed max-w-lg">
                        Monitoruj stan powietrza w Twojej okolicy w czasie rzeczywistym. 
                        Analizuj dane z czujników, sprawdzaj historię zanieczyszczeń 
                        i dbaj o zdrowie swoje oraz bliskich.
                    </p>

                    <div class="flex flex-wrap gap-4 pt-2">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-8 py-3 rounded-full bg-sky-600 hover:bg-sky-700 text-white font-semibold transition shadow-lg shadow-sky-600/20">
                                Przejdź do Panelu
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="px-8 py-3 rounded-full bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] font-semibold hover:bg-gray-800 dark:hover:bg-gray-200 transition">
                                Zarejestruj się
                            </a>
                            <a href="{{ route('login') }}" class="px-8 py-3 rounded-full border border-[#19140035] dark:border-[#3E3E3A] hover:bg-gray-50 dark:hover:bg-[#161615] dark:text-[#EDEDEC] font-medium transition">
                                Zaloguj się
                            </a>
                        @endauth
                    </div>

                    <div class="pt-8 flex items-center gap-8 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            <span>Dane Live</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                            <span>Precyzyjne czujniki</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                            <span>Alerty</span>
                        </div>
                    </div>
                </div>

                <div class="relative order-1 lg:order-2 w-full aspect-square lg:aspect-auto lg:h-[500px] bg-sky-50 dark:bg-[#111827] rounded-3xl overflow-hidden flex items-center justify-center border border-sky-100 dark:border-gray-800 relative group shadow-xl">

                    <div class="absolute inset-0 pointer-events-none">
                        <img src="{{ asset('map-background.png') }}" alt="Map background" class="w-full h-full object-cover opacity-90 dark:opacity-70 grayscale-[20%]">
                        <div class="absolute inset-0 bg-sky-900/10 dark:bg-black/40"></div>
                    </div>

                    <div class="absolute top-1/3 left-1/4 z-10">
                        <span class="absolute inline-flex h-4 w-4 rounded-full bg-green-400 opacity-75 animate-ping"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-green-500 border-2 border-white dark:border-gray-900 shadow-md"></span>
                    </div>
                    <div class="absolute bottom-1/4 right-1/3 z-10">
                        <span class="absolute inline-flex h-4 w-4 rounded-full bg-yellow-400 opacity-75 animate-ping delay-300"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-yellow-500 border-2 border-white dark:border-gray-900 shadow-md"></span>
                    </div>
                    <div class="absolute top-1/4 right-1/4 z-10">
                        <span class="absolute inline-flex h-4 w-4 rounded-full bg-orange-400 opacity-75 animate-ping delay-700"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-orange-500 border-2 border-white dark:border-gray-900 shadow-md"></span>
                    </div>

                    <div class="relative z-20 p-8 rounded-2xl bg-white/70 dark:bg-black/50 backdrop-blur-md shadow-2xl border border-white/30 transition-transform duration-500 group-hover:scale-105">
                        <img
                            src="{{ asset('logo.png') }}"
                            alt="Air Quality System Logo"
                            class="w-40 lg:w-56 h-auto drop-shadow-md"
                        >
                    </div>
                </div>

            </div>
        </main>

        <footer class="py-6 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">
            &copy; {{ date('Y') }} Chmurexpol Air Monitoring System. Wszelkie prawa zastrzeżone.
        </footer>

    </body>
</html>