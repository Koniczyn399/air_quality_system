<!doctype html>
<html lang="pl">
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">


    <title>{{ __('pdf.labels.title') }}</title>
<style>

body { font-family: DejaVu Sans, sans-serif; }
h4 {
    margin: 0;
}
.w-full {
    width: 100%;
}
.w-half {
    width: 50%;
}
.margin-top {
    margin-top: 1.25rem;
}
.footer {
    font-size: 0.875rem;
    padding: 1rem;
    background-color: rgb(241 245 249);
}
table {
    width: 100%;
    border-spacing: 0;
}
table.products {
    font-size: 0.875rem;
}
table.products tr {
    background-color: rgb(96 165 250);
}
table.products th {
    color: #ffffff;
    padding: 0.5rem;
}
table tr.items {
    background-color: rgb(241 245 249);
}
table tr.items td {
    padding: 0.5rem;
}
.total {
    text-align: right;
    margin-top: 1rem;
    font-size: 0.875rem;
}

</style>


</head>
<body>
    @php $logo = public_path('logo.png'); @endphp
    @inlinedImage($logo)
    <table class="w-full">
        <tr>
            <td class="w-half">
                <img src="{{ asset('public/logo.png') }}" alt="Chmurexpol" width="200" />
            </td>
            <td class="w-half">                                     

            </td>
        </tr>
    </table>

    <div class="mt-10 theme-bg shadow-xl rounded-xl overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="flex items-center mb-6">
                    <x-wireui-icon name="clipboard-document-list" class="w-8 h-8 text-indigo-500 mr-3" />
                    <h2 class="text-xl font-semibold theme-text">Historia zmian statusu</h2>
                </div>
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium theme-text-subtle uppercase tracking-wider">Urządzenie</th>
                                <th class="px-6 py-3 text-left text-xs font-medium theme-text-subtle uppercase tracking-wider">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-medium theme-text-subtle uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium theme-text-subtle uppercase tracking-wider">Zmieniony przez</th>
                                <th class="px-6 py-3 text-left text-xs font-medium theme-text-subtle uppercase tracking-wider">Notatki</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($devices_history as $history)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm theme-text">{{ $history->device_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm theme-text">{{ $history->created_at->format('d-m-Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @switch($history->status)
                                            @case('active')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                    <x-wireui-icon name="check-circle" class="w-4 h-4 mr-1.5" /> Aktywny
                                                </span>
                                                @break
                                            @case('inactive')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                                    <x-wireui-icon name="x-circle" class="w-4 h-4 mr-1.5" /> Nieaktywny
                                                </span>
                                                @break
                                            @case('in_repair')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                                    <x-wireui-icon name="wrench-screwdriver" class="w-4 h-4 mr-1.5" /> W naprawie
                                                </span>
                                                @break
                                            @default
                                                <span class="theme-text-subtle">{{ $history->status }}</span>
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm theme-text">
                                        @if($history->changedBy)
                                            <span class="inline-flex items-center">
                                                <x-wireui-icon name="user-circle" class="w-4 h-4 mr-1.5 text-gray-400" />
                                                {{ $history->changedBy->name }}
                                            </span>
                                        @else
                                            <span class="theme-text-subtle italic">System</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm theme-text prose prose-sm max-w-xs">
                                        {!! $history->notes ? nl2br(e($history->notes)) : '<span class="theme-text-subtle italic">Brak</span>' !!}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-sm theme-text-subtle">
                                        <div class="flex flex-col items-center">
                                            <x-wireui-icon name="document-magnifying-glass" class="w-12 h-12 text-gray-300 mb-2" />
                                            Brak historii zmian dla tego urządzenia.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


</body>
</html>