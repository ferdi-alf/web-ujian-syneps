{{-- resources/views/components/fragments/currency-field.blade.php --}}
@props([
    'label' => '',
    'name' => '',
    'placeholder' => 'Masukkan harga...',
    'value' => '',
    'required' => false,
    'currency' => 'Rp',
])

@php
    $hasError = $errors->has($name);
    $errorClass = $hasError
        ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
        : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500';
@endphp

<div>
    @if ($label)
        <label for="{{ $name }}" class="block mb-2 text-sm font-medium text-gray-900">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if ($currency)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 text-sm">{{ $currency }}</span>
            </div>
        @endif

        <input type="text" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            class="bg-gray-50 border {{ $errorClass }} text-gray-900 text-sm rounded-lg block w-full {{ $currency ? 'pl-12' : 'pl-3' }} pr-3 py-2.5 transition-colors duration-200"
            {{ $required ? 'required' : '' }} {{ $attributes }} data-currency-input />

        <input type="hidden" name="{{ $name }}_numeric" id="{{ $name }}_numeric"
            value="{{ old($name . '_numeric', preg_replace('/[^\d]/', '', $value)) }}">
    </div>

    @error($name)
        <p class="mt-1 text-sm text-red-600 flex items-center">
            <i class="fa-solid fa-circle-exclamation mr-1"></i>
            {{ $message }}
        </p>
    @enderror
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function formatCurrency(value) {
            const numericValue = value.toString().replace(/[^\d]/g, '');

            return numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        document.querySelectorAll('[data-currency-input]').forEach(function(input) {
            const hiddenInput = document.getElementById(input.id + '_numeric');

            if (input.value) {
                const numericValue = input.value.replace(/[^\d]/g, '');
                input.value = formatCurrency(numericValue);
                if (hiddenInput) {
                    hiddenInput.value = numericValue;
                }
            }
            input.addEventListener('input', function(e) {
                const cursorPosition = e.target.selectionStart;
                const oldValue = e.target.value;
                const numericValue = oldValue.replace(/[^\d]/g, '');
                const formattedValue = formatCurrency(numericValue);

                e.target.value = formattedValue;

                if (hiddenInput) {
                    hiddenInput.value = numericValue;
                }

                const lengthDifference = formattedValue.length - oldValue.length;
                const newCursorPosition = cursorPosition + lengthDifference;
                e.target.setSelectionRange(newCursorPosition, newCursorPosition);
            });

            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                const numericValue = pastedText.replace(/[^\d]/g, '');
                const formattedValue = formatCurrency(numericValue);

                e.target.value = formattedValue;
                if (hiddenInput) {
                    hiddenInput.value = numericValue;
                }
            });
        });
    });
</script>
