@props(['target', 'variant' => 'primary', 'size' => 'md'])

@php
    $variants = [
        'primary' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-blue-300 ',
        'secondary' => 'text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-gray-200  ',
        'danger' => 'text-white bg-red-700 hover:bg-red-800 focus:ring-red-300 ',
        'success' => 'text-white bg-green-700 hover:bg-green-800 focus:ring-green-300 ',
        'edit' => 'text-blue-500 bg-blue-100 hover:bg-blue-200 focus:ring-blue-300 ',
        'emerald' =>
            'text-white bg-gradient-to-br text-transparent  from-emerald-400 via-teal-500 to-emerald-600 focus:ring-emerald-300 shadow-lg',
    ];

    $sizes = [
        'sm' => 'px-3 py-2  ',
        'md' => 'px-5 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];

    $variantClass = $variants[$variant] ?? $variants['primary'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<button data-modal-target="{{ $target }}" data-modal-toggle="{{ $target }}"
    class="block {{ $variantClass }} cursor-pointer focus:ring-4 focus:outline-none font-medium rounded-lg {{ $sizeClass }} text-center"
    type="button">
    {{ $slot }}
</button>
