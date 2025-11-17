<div class="space-y-4 mb-6 mt-5">
    <div class="flex flex-col gap-4">
        <div class="flex flex-wrap items-end gap-4">
            <label class="flex flex-col text-xs font-semibold theme-text">
                <span class="mb-1">Data od</span>
                <input
                    type="date"
                    wire:model.live.debounce.500ms="dateFrom"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-8 text-xs {{ filled($dateFrom) ? 'text-gray-900' : 'text-gray-400' }}"
                    max="{{ $dateTo ?? now()->toDateString() }}"
                />
            </label>

            <label class="flex flex-col text-xs font-semibold theme-text">
                <span class="mb-1">Data do</span>
                <input
                    type="date"
                    wire:model.live.debounce.500ms="dateTo"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-8 text-xs {{ filled($dateTo) ? 'text-gray-900' : 'text-gray-400' }}"
                    min="{{ $dateFrom ?? null }}"
                    max="{{ now()->toDateString() }}"
                />
            </label>

            <div class="flex flex-wrap gap-2">
                @foreach($this->quickRangeOptions as $rangeKey => $rangeLabel)
                    <button
                        type="button"
                        wire:click="applyQuickRange('{{ $rangeKey }}')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-full border transition-colors
                            {{ $activeQuickRange === $rangeKey ? 'bg-indigo-600 text-white border-indigo-600' : 'theme-bg border-gray-200 text-gray-600 hover:border-indigo-200 hover:text-indigo-600' }}"
                    >
                        {{ $rangeLabel }}
                    </button>
                @endforeach
            </div>

            @if(count($this->parameterFilterFields))
                <button
                    type="button"
                    wire:click="toggleAdvancedFilters"
                    class="ml-auto inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 border border-indigo-100 rounded-full px-3 py-1.5 hover:bg-indigo-50"
                >
                    Filtry zaawansowane
                    <span class="text-sm">{{ $showAdvancedFilters ? '▴' : '▾' }}</span>
                </button>
            @endif
        </div>
    </div>

    @if(count($this->parameterFilterFields))
        <div class="transition-all duration-200 overflow-hidden {{ $showAdvancedFilters ? 'max-h-[2000px] opacity-100' : 'max-h-0 opacity-0' }}">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 pt-4">
                @foreach($this->parameterFilterFields as $field)
                    <div class="flex flex-col text-xs font-semibold theme-text" wire:key="parameter-filter-{{ $field['alias'] }}">
                        <span class="mb-2 flex items-center gap-2">
                            {{ $field['label'] }}
                            <span class="text-xs font-medium text-gray-400">{{ $field['unit'] }}</span>
                        </span>
                        <div class="flex gap-3">
                            <input
                                type="number"
                                step="{{ $field['step'] }}"
                                placeholder="Min"
                                wire:model.live.debounce.500ms="parameterRanges.{{ $field['alias'] }}.min"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-8 text-xs"
                            />
                            <input
                                type="number"
                                step="{{ $field['step'] }}"
                                placeholder="Max"
                                wire:model.live.debounce.500ms="parameterRanges.{{ $field['alias'] }}.max"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-8 text-xs"
                            />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
