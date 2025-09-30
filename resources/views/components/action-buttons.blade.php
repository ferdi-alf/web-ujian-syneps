{{-- resources/views/components/action-buttons.blade.php --}}
@props([
    'modalTarget' => null,
    'drawerId' => null,
    'deleteRoute' => null,
    'viewAction' => null,
    'deleteMessage' => null,
    'showView' => true,
    'editData' => null,
    'viewData' => null,
])

<div class="flex space-x-2">
    @if ($showView && $viewAction)
        <button onclick="{{ is_string($viewAction) ? "window.location.href='{$viewAction}'" : $viewAction }}"
            class="inline-flex items-center p-3 cursor-pointer text-xs font-medium text-green-600 bg-green-100 rounded-md hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition-colors duration-200"
            title="Lihat Detail">
            <i class="fa-solid fa-eye"></i>
        </button>
    @elseif ($viewData)
        @if ($viewData['type'] === 'slideOver')
            <button
                onclick="openDrawerWithData('{{ $viewData['drawerTarget'] ?? $viewData['drawerId'] }}', {{ json_encode($viewData) }});"
                class="inline-flex cursor-pointer items-center p-3 text-xs font-medium text-green-600 bg-green-100 rounded-md hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition-colors duration-200"
                title="Lihat Detail">
                <i class="fa-solid fa-eye"></i>
            </button>
        @elseif ($viewData['type'] === 'bottomSheet')
            <button onclick="openDrawerWithData('{{ $viewData['drawerTarget'] }}', {{ json_encode($viewData) }})"
                class="inline-flex items-center p-3 text-xs font-medium text-green-600 bg-green-100 rounded-md hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition-colors duration-200"
                title="Lihat Detail">
                <i class="fa-solid fa-eye"></i>
            </button>
        @endif
    @elseif ($drawerId)
        <button onclick="openDrawer('{{ $drawerId }}')"
            class="inline-flex items-center p-3 text-xs font-medium text-green-600 bg-green-100 rounded-md hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition-colors duration-200"
            title="Lihat Detail">
            <i class="fa-solid fa-eye"></i>
        </button>
    @endif

    @if ($modalTarget && $editData)
        <x-fragments.modal-button :target="$modalTarget" variant="edit" size="sm" act="update" :data="$editData">
            <i class="fa-solid fa-pen"></i>
        </x-fragments.modal-button>
    @endif

    @if ($deleteRoute)
        <x-fragments.delete-button :url="$deleteRoute" title="Hapus Data" :message="$deleteMessage ?? 'Yakin ingin menghapus data ini?'" />
    @endif
</div>
