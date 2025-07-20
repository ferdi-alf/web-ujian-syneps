@props([
    'id' => 'default-modal',
    'title' => 'Modal Title',
    'size' => 'md',
    'closable' => true,
    'footerActions' => null,
    'show' => false,
])

@php
    $sizeClasses = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
    ];
    $modalSize = $sizeClasses[$size] ?? 'max-w-md';
@endphp

<div id="{{ $id }}"
    class="fixed inset-0 z-[1000]   {{ $show ? 'flex ' : 'hidden' }} items-center justify-center bg-black/30 bg-opacity-50">

    <div class="relative p-4 w-full {{ $modalSize }} max-h-full">
        <div class=" bg-white rounded-lg shadow-sm ">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t  border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 ">
                    {{ $title }}
                </h3>
                @if ($closable)
                    <button type="button"
                        class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center "
                        data-modal-hide="{{ $id }}">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                @endif
            </div>

            <div class="p-4 md:p-5">
                {{ $slot }}
            </div>

            @if ($footerActions)
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b ">
                    {{ $footerActions }}
                </div>
            @endif
        </div>
    </div>
</div>
