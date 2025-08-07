@props([
    'modalId' => null,
    'drawerId' => null,
    'deleteRoute' => null,
    'viewAction' => null,
    'deleteMessage' => null,
    'showView' => true,
])

<div class="flex space-x-2">
    @if ($showView && $viewAction)
        <button onclick="{{ is_string($viewAction) ? "window.location.href='{$viewAction}'" : $viewAction }}"
            class="inline-flex items-center p-3 text-xs font-medium text-green-600 bg-green-100 rounded-md hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition-colors duration-200"
            title="Lihat Detail">
            <i class="fa-solid fa-eye"></i>
        </button>
    @elseif ($drawerId)
        <button onclick="openDrawer('{{ $drawerId }}')"
            class="inline-flex items-center p-3 text-xs font-medium text-green-600 bg-green-100 rounded-md hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition-colors duration-200"
            title="Lihat Detail">
            <i class="fa-solid fa-eye"></i>
        </button>
    @endif

    @if ($modalId)
        <x-fragments.modal-button :target="$modalId" variant="edit" size="sm">
            <i class="fa-solid fa-pen"></i>
        </x-fragments.modal-button>
    @endif

    @if ($deleteRoute)
        <x-fragments.delete-button :url="$deleteRoute" title="Hapus Data" :message="$deleteMessage ?? 'Yakin ingin menghapus data ini?'" />
    @endif

</div>
