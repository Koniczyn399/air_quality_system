<nav x-data="{ open: false }" class="theme-bg theme-border border-b relative z-[1000]">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="shrink-0">
                    <x-application-mark class="block h-9 w-auto" />
                </a>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:ml-10 space-x-8">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="theme-nav-link">
                        {{ __('Strona główna') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.index')" class="theme-nav-link">
                        Użytkownicy
                    </x-nav-link>
                    <x-nav-link href="{{ route('measurements.index') }}" :active="request()->routeIs('measurements.index')" class="theme-nav-link">
                        Pomiary
                    </x-nav-link>
                    <x-nav-link href="{{ route('measurement-devices.index') }}" :active="request()->routeIs('measurement-devices.index')" class="theme-nav-link">
                        Urządzenia
                    </x-nav-link>
                    <x-nav-link href="{{ route('map') }}" :active="request()->routeIs('map')" class="theme-nav-link">
                        Mapa
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Side Controls -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="ml-3 relative">
                    <!-- Przełącznik trybu jasnego i ciemnego -->
                    <button class="theme-toggle" id="theme-toggle" title="Przełącza tryb jasny i ciemny" aria-label="auto" aria-live="polite">
                        <svg class="sun-and-moon" aria-hidden="true" width="24" height="24" viewBox="0 0 24 24">
                            <mask class="moon" id="moon-mask">
                                <rect x="0" y="0" width="100%" height="100%" fill="white" />
                                <circle cx="24" cy="10" r="6" fill="black" />
                            </mask>
                            <circle class="sun" cx="12" cy="12" r="6" mask="url(#moon-mask)" fill="currentColor" />
                            <g class="sun-beams" stroke="currentColor">
                                <line x1="12" y1="1" x2="12" y2="3" />
                                <line x1="12" y1="21" x2="12" y2="23" />
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
                                <line x1="1" y1="12" x2="3" y2="12" />
                                <line x1="21" y1="12" x2="23" y2="12" />
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
                            </g>
                        </svg>
                    </button>
                </div>

                <!-- User Dropdown -->
                <div x-data="{ open: false }" class="relative ml-3">
                    <button @click="open = !open" class="flex items-center theme-text hover:bg-gray-100 dark:hover:bg-gray-700 px-3 py-2 rounded-md">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <img class="h-8 w-8 rounded-full" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                        @else
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        @endif
                    </button>

                    <div x-show="open" @click.away="open = false" 
                         class="absolute right-0 mt-2 w-48 theme-bg theme-border rounded-md shadow-lg z-[1100]">
                        <div class="py-1">
                            <a href="{{ route('profile.show') }}" class="theme-dropdown-link">
                                {{ __('Profile') }}
                            </a>
                            
                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <a href="{{ route('api-tokens.index') }}" class="theme-dropdown-link">
                                    {{ __('API Tokens') }}
                                </a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="theme-dropdown-link w-full text-left">
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center sm:hidden">
                <button @click="open = !open" class="theme-text hover:bg-gray-100 dark:hover:bg-gray-700 p-2 rounded-md">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="open" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <!-- Add other mobile links here -->
        </div>

        <div class="pt-4 pb-1 border-t theme-border">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 mr-3">
                        <img class="h-10 w-10 rounded-full" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                @endif
                <div>
                    <div class="font-medium theme-text">{{ Auth::user()->name }}</div>
                    <div class="text-sm theme-text">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                
                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    // Obsługa przełączania motywu
    const themeToggle = document.getElementById('theme-toggle');
    const htmlEl = document.documentElement;
    
    // Sprawdź zapisany motyw
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') htmlEl.classList.add('dark');

    themeToggle.addEventListener('click', () => {
        htmlEl.classList.toggle('dark');
        localStorage.setItem('theme', htmlEl.classList.contains('dark') ? 'dark' : 'light');
    });
</script>