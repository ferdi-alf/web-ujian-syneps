@props([
    'label' => '',
    'name' => '',
    'options' => [],
    'value' => [],
    'placeholder' => 'Pilih ' . strtolower($label),
    'required' => false,
    'id' => null,
])

@php
    $componentId = $id ?? 'multiple-select-' . str_replace(['[', ']', '.'], '', $name) . '-' . uniqid();
    $selectedValues = is_array($value) ? $value : (is_string($value) ? explode(',', $value) : []);
    $selectedValues = array_map('strval', $selectedValues);
@endphp

<div class="mb-4">
    @if ($label)
        <label for="{{ $componentId }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative" x-data="{
        isOpen: false,
        searchQuery: '',
        selectedValues: @js($selectedValues),
        options: @js($options),
        highlightedIndex: -1,
    
        get selectedItems() {
            return this.selectedValues.map(value =>
                this.options.find(option => String(option.value) === String(value))
            ).filter(Boolean);
        },
    
        get filteredOptions() {
            if (!this.searchQuery) return this.options;
            return this.options.filter(option =>
                option.label.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },
    
        selectOption(option) {
            if (!this.selectedValues.includes(String(option.value))) {
                this.selectedValues.push(String(option.value));
            }
            this.searchQuery = '';
            this.isOpen = false;
            this.highlightedIndex = -1;
        },
    
        removeItem(value) {
            this.selectedValues = this.selectedValues.filter(v => String(v) !== String(value));
        },
    
        selectFirstFiltered() {
            if (this.filteredOptions.length > 0 && this.highlightedIndex >= 0) {
                this.selectOption(this.filteredOptions[this.highlightedIndex]);
            } else if (this.filteredOptions.length > 0) {
                this.selectOption(this.filteredOptions[0]);
            }
        },
    
        navigateDown() {
            this.isOpen = true;
            this.highlightedIndex = Math.min(this.highlightedIndex + 1, this.filteredOptions.length - 1);
        },
    
        navigateUp() {
            this.highlightedIndex = Math.max(this.highlightedIndex - 1, -1);
        }
    }" @click.away="isOpen = false">

        <div class="min-h-[42px] border border-gray-300 rounded-md px-3 py-2 bg-white focus-within:ring-1 focus-within:ring-blue-500 focus-within:border-blue-500 cursor-text"
            @click="$refs.searchInput.focus()">

            <div class="flex flex-wrap gap-1 mb-1" x-show="selectedItems.length > 0">
                <template x-for="item in selectedItems" :key="item.value">
                    <div
                        class="inline-flex items-center bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-1 rounded-full">
                        <span x-text="item.label"></span>
                        <button type="button" class="ml-1 text-blue-600 hover:text-blue-800 focus:outline-none"
                            @click.stop="removeItem(item.value)">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            <input type="text" x-ref="searchInput" :id="'{{ $componentId }}'"
                class="w-full border-0 p-0 focus:ring-0 focus:outline-none placeholder-gray-400 text-sm"
                :placeholder="selectedItems.length === 0 ? '{{ $placeholder }}' : ''" x-model="searchQuery"
                @focus="isOpen = true" @keydown.enter.prevent="selectFirstFiltered()"
                @keydown.escape.prevent="isOpen = false" @keydown.arrow-down.prevent="navigateDown()"
                @keydown.arrow-up.prevent="navigateUp()" autocomplete="off">
        </div>

        <div x-show="isOpen" x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">

            <div x-show="filteredOptions.length === 0" class="px-3 py-2 text-gray-500 text-sm">
                <span x-show="options.length === 0">Tidak ada pilihan tersedia</span>
                <span x-show="options.length > 0 && searchQuery">Tidak ditemukan hasil untuk "<span
                        x-text="searchQuery"></span>"</span>
            </div>

            <template x-for="(option, index) in filteredOptions" :key="option.value">
                <div class="px-3 py-2 cursor-pointer hover:bg-gray-100 text-sm flex items-center justify-between"
                    :class="{
                        'bg-blue-50': index === highlightedIndex,
                        'text-gray-400 bg-gray-50': selectedValues.includes(String(option.value))
                    }"
                    @click="!selectedValues.includes(String(option.value)) && selectOption(option)">
                    <span x-text="option.label"></span>
                    <span x-show="selectedValues.includes(String(option.value))" class="text-blue-600">✓</span>
                </div>
            </template>
        </div>

        <template x-for="value in selectedValues" :key="value">
            <input type="hidden" :name="'{{ $name }}[]'" :value="value">
        </template>
    </div>
</div>
