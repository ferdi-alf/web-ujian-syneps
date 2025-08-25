@props([
    'type' => 'button',
    'disabled' => false,
])

<button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} @class([
    'bg-gradient-to-br cursor-pointer w-full text-lg font-semibold p-2 focus:ring-4 focus:ring-emerald-300 from-emerald-200 via-teal-300 text-white rounded-lg shadow-lg to-emerald-400 transition-all duration-200',
    'opacity-50 cursor-not-allowed' => $disabled,
    'hover:shadow-xl hover:scale-[1.02]' => !$disabled,
]) {{ $attributes }}>
    {{ $slot }}
</button>
