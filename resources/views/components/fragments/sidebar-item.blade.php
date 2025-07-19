@props(['route', 'icon', 'colors' => 'blue', 'href' => null])

@php
    $href = $href ?? route(str_replace('.*', '.index', $route));
    $isActive = Request::routeIs($route);

    $colorMap = [
        'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'text-blue-500'],
        'cyan' => ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-700', 'icon' => 'text-cyan-500'],
        'amber' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'text-amber-500'],
        'emerald' => ['bg' => 'bg-teal-100', 'text' => 'text-emerald-400', 'icon' => 'text-emerald-500'],
        'rose' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'icon' => 'text-rose-500'],
        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'icon' => 'text-purple-500'],
    ];

    $activeClasses = $colorMap[$colors]['bg'] . ' ' . $colorMap[$colors]['text'];
@endphp

<li>
    <a href="{{ $href }}"
        class="flex items-center p-2 rounded-lg group {{ $isActive ? $activeClasses : 'text-gray-500 hover:bg-gray-100' }}">
        <i class="fa-solid fa-{{ $icon }} text-xl w-6 text-center"></i>
        <span class="flex-1 ms-3 whitespace-nowrap">{{ $slot }}</span>
    </a>

</li>
