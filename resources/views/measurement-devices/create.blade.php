<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Dodaj nowe urządzenie</h1>
        
        <form action="{{ route('measurement-devices.store') }}" method="POST" class="max-w-3xl">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Nazwa -->
                <div class="col-span-1">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nazwa urządzenia *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Model -->
                <div class="col-span-1">
                    <label for="model" class="block text-sm font-medium text-gray-700 mb-1">Model *</label>
                    <input type="text" id="model" name="model" value="{{ old('model') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @error('model')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Numer seryjny -->
                <div class="col-span-1">
                    <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-1">Numer seryjny *</label>
                    <input type="text" id="serial_number" name="serial_number" value="{{ old('serial_number') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @error('serial_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Data kalibracji -->
                <div class="col-span-1">
                    <label for="calibration_date" class="block text-sm font-medium text-gray-700 mb-1">Data kalibracji *</label>
                    <input type="date" id="calibration_date" name="calibration_date" value="{{ old('calibration_date') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @error('calibration_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Następna kalibracja -->
                <div class="col-span-1">
                    <label for="next_calibration_date" class="block text-sm font-medium text-gray-700 mb-1">Następna kalibracja *</label>
                    <input type="date" id="next_calibration_date" name="next_calibration_date" value="{{ old('next_calibration_date') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @error('next_calibration_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Status -->
                <div class="col-span-1">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Wybierz status</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktywny</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Nieaktywny</option>
                        <option value="in_repair" {{ old('status') == 'in_repair' ? 'selected' : '' }}>W naprawie</option>
                    </select>
                    @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Opis -->
                <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Opis</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('measurement-devices.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Anuluj
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Dodaj urządzenie
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
