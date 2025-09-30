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
        <label for="{{ $name }}_display" class="block mb-2 text-sm font-medium text-gray-900">
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

        {{-- Input untuk display (yang dilihat user) --}}
        <input type="text" name="{{ $name }}_display" id="{{ $name }}_display"
            placeholder="{{ $placeholder }}"
            class="bg-gray-50 border {{ $errorClass }} text-gray-900 text-sm rounded-lg block w-full {{ $currency ? 'pl-12' : 'pl-3' }} pr-3 py-2.5 transition-colors duration-200"
            {{ $attributes }} data-currency-input autocomplete="off" />

        {{-- Hidden input untuk submit ke server (value asli) --}}
        <input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}"
            {{ $required ? 'required' : '' }} data-currency-hidden />
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
            if (!numericValue) return '';
            return numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function unformatCurrency(value) {
            return value.toString().replace(/[^\d]/g, '');
        }


        function setCurrencyValue(displayInput, hiddenInput, value) {
            const numericValue = unformatCurrency(value);
            const formattedValue = formatCurrency(numericValue);

            displayInput.value = formattedValue;
            hiddenInput.value = numericValue;
        }

        window.formatCurrency = function(input) {
            const hiddenInput = document.getElementById(input.id.replace('_display', ''));
            if (hiddenInput && input.value) {
                setCurrencyValue(input, hiddenInput, input.value);
            }
        };


        document.querySelectorAll('[data-currency-input]').forEach(function(displayInput) {
            const hiddenInput = document.getElementById(displayInput.id.replace('_display', ''));

            if (!hiddenInput) {
                console.error('Hidden input not found for:', displayInput.id);
                return;
            }


            if (hiddenInput.value) {
                setCurrencyValue(displayInput, hiddenInput, hiddenInput.value);
            }

            displayInput.addEventListener('input', function(e) {
                const cursorPosition = e.target.selectionStart;
                const oldValue = e.target.value;
                const oldLength = oldValue.length;

                const numericValue = unformatCurrency(oldValue);
                const formattedValue = formatCurrency(numericValue);

                e.target.value = formattedValue;
                hiddenInput.value = numericValue;

                const newLength = formattedValue.length;
                const lengthDiff = newLength - oldLength;
                const newCursorPosition = Math.max(0, cursorPosition + lengthDiff);
                e.target.setSelectionRange(newCursorPosition, newCursorPosition);
            });

            displayInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                setCurrencyValue(displayInput, hiddenInput, pastedText);
            });

            displayInput.addEventListener('blur', function(e) {
                if (e.target.value) {
                    setCurrencyValue(displayInput, hiddenInput, e.target.value);
                }
            });
        });
    });
</script>
