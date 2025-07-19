@props([
    'type' => 'button',
])
<button type="{{ $type }}"
    class="bg-gradient-to-br cursor-pointer w-full text-lg font-semibold p-2 focus:ring-4 focus:ring-emerald-300 from-emerald-200 via-teal-300 text-white rounded-lg shadow-lg to-emerald-400 ">
    {{ $slot }}
</button>
