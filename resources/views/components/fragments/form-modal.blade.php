{{-- components/fragments/form-modal.blade.php --}}
@props([
    'id',
    'title',
    'action' => null,
    'method' => 'POST',
    'size' => 'lg',
    'show' => false,
    'fetchEndpoint' => null,
    'createTitle' => 'Tambah Data',
    'editTitle' => 'Edit Data',
    'updateOnly' => false, // Flag untuk modal update only
])

<x-modal-layout :id="$id" :title="$title" :size="$size" :show="$show">
    <div id="modal-loading-{{ $id }}" class="hidden justify-center items-center">
        <div class="flex items-center space-x-2">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p>Loading...</p>
        </div>
    </div>

    <div id="modal-content-{{ $id }}">
        <form action="{{ $action ?? '#' }}" method="POST" class="space-y-4 p-2" enctype="multipart/form-data">
            @csrf
            @if (in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
                @method($method)
            @endif

            {{ $slot }}

            <div class="flex justify-end">
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 transition-colors duration-200">
                    <i class="fa-solid fa-save mr-2"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</x-modal-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (!window.modalHandlers) {
            window.modalHandlers = {};
        }

        window.modalHandlers['{{ $id }}'] = {
            fetchEndpoint: '{{ $fetchEndpoint }}',
            createTitle: '{{ $createTitle }}',
            editTitle: '{{ $editTitle }}',
            originalAction: '{{ $action }}',
            updateOnly: {{ $updateOnly ? 'true' : 'false' }}
        };
    });

    window.handleModalAction = function(modalId, action, data = null) {
        const modal = document.getElementById(modalId);
        if (!modal) {
            console.error('Modal tidak ditemukan:', modalId);
            return;
        }

        const form = modal.querySelector('form');
        const modalTitle = modal.querySelector('h3');
        const loadingDiv = document.getElementById(`modal-loading-${modalId}`);
        const contentDiv = document.getElementById(`modal-content-${modalId}`);
        const handler = window.modalHandlers[modalId];

        if (!handler) {
            console.error('Handler untuk modal tidak ditemukan:', modalId);
            return;
        }

        if (action === 'create' && handler.updateOnly) {
            console.warn('Modal ini hanya untuk update, tidak bisa digunakan untuk create');
            return;
        }

        if (action === 'create') {
            modalTitle.textContent = handler.createTitle;

            if (handler.originalAction && handler.originalAction !== '#') {
                form.action = handler.originalAction;
            } else if (data && data.createEndpoint) {
                form.action = data.createEndpoint;
            }

            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) {
                methodInput.remove();
            }

            resetModalForm(modalId);
            contentDiv.style.display = 'block';
            loadingDiv.style.display = 'none';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';

            const event = new CustomEvent('modalCreate', {
                detail: {
                    modalId,
                    form,
                    data
                }
            });
            document.dispatchEvent(event);

        } else if (action === 'update' && data && data.id) {
            modalTitle.textContent = handler.editTitle;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';

            loadingDiv.style.display = 'flex';
            contentDiv.style.display = 'none';

            const fetchUrl = data.fetchEndpoint || `${handler.fetchEndpoint}/${data.id}` ||
                `/api/${modalId.replace('-modal', '')}/${data.id}`;
            const updateUrl = data.updateEndpoint || `/api/${modalId.replace('-modal', '')}/${data.id}`;

            fetch(fetchUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') || ''
                    }
                })
                .then(response => response.json())
                .then(responseData => {
                    if (responseData.success) {
                        form.action = updateUrl;

                        let methodInput = form.querySelector('input[name="_method"]');
                        if (!methodInput) {
                            methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = 'PUT';
                            form.appendChild(methodInput);
                        } else {
                            methodInput.value = 'PUT';
                        }

                        loadingDiv.style.display = 'none';
                        contentDiv.style.display = 'block';

                        const event = new CustomEvent('modalUpdate', {
                            detail: {
                                modalId,
                                form,
                                data: responseData.data,
                                originalData: data
                            }
                        });
                        document.dispatchEvent(event);

                    } else {
                        alert('Gagal memuat data: ' + (responseData.message || 'Unknown error'));
                        closeModal(modalId);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memuat data');
                    closeModal(modalId);
                });
        }
    };

    window.resetModalForm = function(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const form = modal.querySelector('form');
        if (!form) return;

        form.reset();

        const event = new CustomEvent('modalReset', {
            detail: {
                modalId,
                form
            }
        });
        document.dispatchEvent(event);
    };

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    };
</script>
