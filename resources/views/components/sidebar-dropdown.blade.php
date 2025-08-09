@props([
    'id' => '',
    'title' => '',
    'icon' => 'cog',
    'routes' => '',
    'color' => 'emerald',
])

<div>
    <button data-dropdown="{{ $id }}" data-routes="{{ $routes }}"
        class="dropdown-toggle flex w-full items-center p-2 rounded-lg group text-gray-500transition-all duration-200">
        <i class="dropdown-icon fas text-xl w-6 text-center fa-{{ $icon }} transition-colors duration-200"></i>
        <div class="flex justify-between items-center w-full">
            <span class=" ms-3 whitespace-nowrap">
                {{ $title }}
            </span>
            <i class="dropdown-chevron fas fa-chevron-down chevron"></i>
        </div>
    </button>

    <div data-submenu="{{ $id }}" class="dropdown-submenu ml-7  mt-0.5">
        <div class="py-1 space-y-0.5">
            {{ $slot }}
        </div>
    </div>
</div>
