@if ($errors->any())
    <div {{ $attributes }}>
        <div class="font-medium text-red-600 theme-text-danger">{{ __('Whoops! Something went wrong.') }}</div>

        <ul class="mt-3 list-disc list-inside text-sm theme-text-danger">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif