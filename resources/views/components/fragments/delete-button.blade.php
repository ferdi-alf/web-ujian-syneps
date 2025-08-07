@props([
    'url' => '#',
    'title' => 'Hapus',
    'message' => 'Yakin ingin menghapus data ini?',
])

<form method="POST" action="{{ $url }}" class="inline">
    @csrf
    @method('DELETE')
    <button type="button" onclick="confirmDelete(this)" data-title="{{ $title }}" data-message="{{ $message }}"
        class="inline-flex items-center p-3 text-xs font-medium text-red-600 bg-red-100 rounded-md hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-colors duration-200">
        <i class="fa-solid fa-trash"></i>
    </button>
</form>
<script>
    function confirmDelete(button) {
        const form = button.closest('form');
        const title = button.getAttribute('data-title') || 'Hapus';
        const message = button.getAttribute('data-message') || 'Yakin ingin menghapus data ini?';

        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
