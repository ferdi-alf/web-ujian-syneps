@props([
    'url', // URL target delete
    'title' => 'Hapus Data',
    'message' => 'Apakah Anda yakin ingin menghapus data ini?',
])

<form action="{{ $url }}" method="POST" class="inline-block delete-form">
    @csrf
    @method('DELETE')

    <button type="button" class="text-red-600 bg-red-100 rounded-lg px-3 py-2 hover:text-red-800 delete-button"
        data-title="{{ $title }}" data-message="{{ $message }}">
        <i class="fa-solid fa-trash"></i>
    </button>
</form>
