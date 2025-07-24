@props(['title', 'icon', 'color', 'bgColor', 'description', 'count'])

<div
    class="bg-white cursor-pointer rounded-lg px-2 py-4 flex gap-x-3 shadow-md hover:shadow-lg transition-shadow duration-300">

    <div class="flex h-full items-center justify-center">
        <div class="bg-{{ $bgColor }} w-16 h-16 flex justify-center items-center rounded-full p-2">
            <i class="fa-solid fa-{{ $icon }} text-{{ $color }}-500 text-2xl p-2"></i>
        </div>
    </div>
    <div class="">
        <h2 class="font-semibold text-sm">
            {{ $title }}
        </h2>
        <h1 class="text-xl font-bold ">
            {{ $count }}
        </h1>
        <p class="text-sm font-light text-gray-500">
            {{ $description }}
        </p>
    </div>
</div>
