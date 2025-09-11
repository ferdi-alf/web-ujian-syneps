@extends('layouts.dashboard-layouts')

@section('content')
    @switch(Auth::user()->role)
        @case('siswa')
            <p>Ini Halaman Siswa</p>
        @break

        @default
            <div class="flex justify-end">
                <x-fragments.modal-button target="add-materi-modal" variant="emerald">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Tambah Materi
                </x-fragments.modal-button>
            </div>

            <x-fragments.form-modal id="add-materi-modal" size="xl" title="Tambah Materi" action="{{ route('materi.store') }}">
                <div class="overflow-auto p-2">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Upload File PDF <span class="text-danger">*</span></label>
                        <div id="pdf-upload-area"
                            class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer transition-colors hover:border-blue-400 hover:bg-blue-50">
                            <div id="upload-placeholder">
                                <i class="fa-solid fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-600 mb-2">Drag & drop file PDF di sini atau klik untuk memilih</p>
                                <p class="text-sm text-gray-500">Maksimal ukuran file: 10MB</p>
                            </div>
                            <div id="file-preview" class="hidden overflow-hidden">
                                <i class="fa-solid fa-file-pdf text-red-500 text-4xl mb-3"></i>
                                <p id="file-name" class="text-gray-700 font-medium"></p>
                                <p id="file-size" class="text-sm text-gray-500"></p>
                                <button type="button" id="remove-file" class="mt-2 text-red-500 hover:text-red-700 text-sm">
                                    <i class="fa-solid fa-times mr-1"></i>Hapus File
                                </button>
                            </div>
                        </div>
                        <input type="file" id="pdf-file-input" name="file_pdf" accept="application/pdf" class="hidden">
                        <div class="invalid-feedback" id="file-error"></div>
                    </div>
                    <div>
                        <x-fragments.text-field label="Judul Materi" name="judul" placeholder="Masukan Judul Materi..."
                            required />
                    </div>
                    <div class="">
                        <x-fragments.select-field label="Pilih Kelas" name="kelas_id" :options="$kelas->pluck('nama', 'id')->toArray()" required />
                        <p id="batch-info"></p>
                        <p id="batch-message" class="text-xs font-medium"></p>
                        <div id="batch-loading" class="mt-3" style="display: none;">
                            <div class="alert alert-info flex gap-2" role="alert">
                                <div role="status">
                                    <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin fill-blue-600"
                                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                            fill="currentColor" />
                                        <path
                                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                            fill="currentFill" />
                                    </svg>
                                    <span class="sr-only">Loading...</span>
                                </div>
                                Mengecek batch untuk kelas yang dipilih...
                            </div>
                        </div>
                    </div>
                </div>
            </x-fragments.form-modal>

            <!-- Table untuk menampilkan materi -->
            <div class="mt-6">
                <x-reusable-table :headers="['No', 'Judul Materi', 'Kelas', 'Batch', 'Tanggal Dibuat']" position="center" :data="$materi" :columns="[
                    fn($row, $i) => $i + 1,
                    fn($row) => $row->getFormattedTitleAttribute(),
                    fn($row) => $row->kelas->nama ?? 'Tidak ada',
                    fn($row) => $row->batch->nama ?? 'Tidak ada',
                    fn($row) => $row->created_at->format('d M Y H:i'),
                ]" :showActions="true"
                    :actionButtons="fn($row) => view('components.action-buttons', [
                        'modalId' => 'modal-update-materi-' . $row->id,
                        'updateRoute' => route('materi.update', $row->id),
                        'deleteRoute' => route('materi.destroy', $row->id),
                    ])" :searchBar="true" :truncate="true" :rowPerPage="10" position="left" :autoFilter="[
                        2 => 'Kelas',
                    ]"
                    :filterPlaceholder="'Semua'" />
            </div>

            <!-- Modal Edit untuk setiap materi -->
            @foreach ($materi as $row)
                <x-fragments.form-modal id="modal-update-materi-{{ $row->id }}" title="Edit Materi"
                    action="{{ route('materi.update', $row->id) }}" method="PUT" enctype="multipart/form-data">
                    <div class="overflow-auto p-2">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Upload File PDF (Kosongkan jika tidak ingin mengubah)</label>
                            <div id="pdf-upload-area-edit-{{ $row->id }}"
                                class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer transition-colors hover:border-blue-400 hover:bg-blue-50">
                                <div id="upload-placeholder-edit-{{ $row->id }}">
                                    <i class="fa-solid fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                    <p class="text-gray-600 mb-2">Drag & drop file PDF baru atau klik untuk memilih</p>
                                    <p class="text-sm text-gray-500">Maksimal ukuran file: 10MB</p>
                                    @if ($row->file_pdf)
                                        <div class="mt-2 p-2 bg-gray-100 rounded">
                                            <p class="text-sm text-gray-600">File saat ini:</p>
                                            <p class="text-sm font-medium text-gray-800">
                                                <i class="fa-solid fa-file-pdf text-red-500 mr-1"></i>
                                                {{ basename($row->file_pdf) }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                                <div id="file-preview-edit-{{ $row->id }}" class="hidden">
                                    <i class="fa-solid fa-file-pdf text-red-500 text-4xl mb-3"></i>
                                    <p id="file-name-edit-{{ $row->id }}" class="text-gray-700 font-medium"></p>
                                    <p id="file-size-edit-{{ $row->id }}" class="text-sm text-gray-500"></p>
                                    <button type="button" id="remove-file-edit-{{ $row->id }}"
                                        class="mt-2 text-red-500 hover:text-red-700 text-sm">
                                        <i class="fa-solid fa-times mr-1"></i>Hapus File
                                    </button>
                                </div>
                            </div>
                            <input type="file" id="pdf-file-input-edit-{{ $row->id }}" name="file_pdf"
                                accept="application/pdf" class="hidden">
                        </div>

                        <x-fragments.text-field label="Judul Materi" name="judul" :value="$row->judul" required />

                        <x-fragments.select-field label="Pilih Kelas" name="kelas_id" :options="$kelas->pluck('nama', 'id')->toArray()" :value="$row->kelas_id"
                            required />
                    </div>
                </x-fragments.form-modal>
            @endforeach

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Function untuk setup drag & drop
                    function setupDragDrop(uploadAreaId, fileInputId, placeholderId, previewId, fileNameId, fileSizeId,
                        removeButtonId) {
                        const uploadArea = document.getElementById(uploadAreaId);
                        const fileInput = document.getElementById(fileInputId);
                        const placeholder = document.getElementById(placeholderId);
                        const filePreview = document.getElementById(previewId);
                        const fileName = document.getElementById(fileNameId);
                        const fileSize = document.getElementById(fileSizeId);
                        const removeButton = document.getElementById(removeButtonId);

                        if (!uploadArea || !fileInput) return;

                        // Click to upload
                        uploadArea.addEventListener('click', () => fileInput.click());

                        // File input change
                        fileInput.addEventListener('change', (e) => {
                            const file = e.target.files[0];
                            if (file) handleFile(file);
                        });

                        // Drag events
                        uploadArea.addEventListener('dragover', (e) => {
                            e.preventDefault();
                            uploadArea.classList.add('border-blue-400', 'bg-blue-50');
                        });

                        uploadArea.addEventListener('dragleave', (e) => {
                            e.preventDefault();
                            uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
                        });

                        uploadArea.addEventListener('drop', (e) => {
                            e.preventDefault();
                            uploadArea.classList.remove('border-blue-400', 'bg-blue-50');

                            const files = e.dataTransfer.files;
                            if (files.length > 0) {
                                const file = files[0];
                                if (file.type === 'application/pdf') {
                                    fileInput.files = files;
                                    handleFile(file);
                                } else {
                                    alert('Hanya file PDF yang diperbolehkan!');
                                }
                            }
                        });

                        // Remove file
                        if (removeButton) {
                            removeButton.addEventListener('click', (e) => {
                                e.stopPropagation();
                                fileInput.value = '';
                                if (placeholder) placeholder.classList.remove('hidden');
                                if (filePreview) filePreview.classList.add('hidden');
                            });
                        }

                        function handleFile(file) {
                            if (file.type !== 'application/pdf') {
                                alert('Hanya file PDF yang diperbolehkan!');
                                return;
                            }

                            if (file.size > 10 * 1024 * 1024) { // 10MB
                                alert('Ukuran file maksimal 10MB!');
                                return;
                            }

                            if (placeholder) placeholder.classList.add('hidden');
                            if (filePreview) filePreview.classList.remove('hidden');
                            if (fileName) fileName.textContent = file.name;
                            if (fileSize) fileSize.textContent = formatFileSize(file.size);
                        }

                        function formatFileSize(bytes) {
                            if (bytes === 0) return '0 Bytes';
                            const k = 1024;
                            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                            const i = Math.floor(Math.log(bytes) / Math.log(k));
                            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                        }
                    }

                    // Setup drag & drop untuk modal tambah
                    setupDragDrop(
                        'pdf-upload-area',
                        'pdf-file-input',
                        'upload-placeholder',
                        'file-preview',
                        'file-name',
                        'file-size',
                        'remove-file'
                    );

                    // Setup drag & drop untuk modal edit
                    @foreach ($materi as $row)
                        setupDragDrop(
                            'pdf-upload-area-edit-{{ $row->id }}',
                            'pdf-file-input-edit-{{ $row->id }}',
                            'upload-placeholder-edit-{{ $row->id }}',
                            'file-preview-edit-{{ $row->id }}',
                            'file-name-edit-{{ $row->id }}',
                            'file-size-edit-{{ $row->id }}',
                            'remove-file-edit-{{ $row->id }}'
                        );
                    @endforeach

                    // Existing batch checking code
                    const kelasSelect = document.getElementById('kelas_id');
                    const batchInfo = document.getElementById('batch-info');
                    const batchMessage = document.getElementById('batch-message');
                    const loadingElement = document.getElementById('batch-loading');

                    if (kelasSelect) {
                        kelasSelect.addEventListener('change', function() {
                            const kelasId = this.value;
                            batchInfo.style.display = 'none';

                            if (!kelasId) {
                                return;
                            }
                            loadingElement.style.display = 'block';
                            fetch(`/tambah-ujian/check-batch-active/${kelasId}`, {
                                    method: 'GET',
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                            .getAttribute('content')
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    loadingElement.style.display = 'none';

                                    if (data.success) {
                                        batchInfo.style.display = 'block';
                                        batchMessage.textContent = data.message;

                                        if (data.hasActiveBatch) {
                                            batchMessage.className = 'text-green-500 fw-medium';
                                        } else {
                                            batchMessage.className = 'text-yellow-500 fw-medium';
                                        }
                                    } else {
                                        batchInfo.style.display = 'block';
                                        batchMessage.className = 'text-red-500 fw-medium';
                                        batchMessage.textContent = data.message;
                                    }
                                })
                                .catch(error => {
                                    loadingElement.style.display = 'none';

                                    batchInfo.style.display = 'block';
                                    batchMessage.className = 'text-danger fw-medium';
                                    batchMessage.textContent = 'Terjadi kesalahan saat mengecek batch';
                                    console.error('Error:', error);
                                });
                        });
                    }
                });
            </script>
    @endswitch
@endsection
