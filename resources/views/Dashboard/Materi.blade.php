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
            <x-fragments.form-modal id="add-materi-modal" size="xl" title="Tambah Kelas" action="{{ route('materi.store') }}">
                <div class="overflow-auto p-2">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Upload File PDF <span class="text-danger">*</span></label>
                        <div id="pdf-dropzone"
                            class="border border-2 border-dashed border-primary rounded p-4 text-center position-relative"
                            style="min-height: 150px; cursor: pointer;">
                            <div id="drop-area" class="d-flex flex-column align-items-center justify-content-center ">
                                <div class="mb-3">
                                    <i class="fas fa-file-pdf text-primary" style="font-size: 3rem;"></i>
                                </div>
                                <h5 class="text-primary mb-2">Drag & Drop file PDF di sini</h5>
                                <p class="text-muted mb-2">atau</p>
                                <button type="button" class="btn btn-outline-primary"
                                    onclick="document.getElementById('pdf-input').click()">
                                    <i class="fas fa-folder-open me-2"></i>Pilih File
                                </button>
                                <small class="text-muted mt-2">Maksimal ukuran file: 10MB</small>
                            </div>
                            <input type="file" id="pdf-input" name="materi" accept=".pdf" class="d-none" required>



                        </div>
                        <div class="invalid-feedback" id="file-error"></div>
                    </div>
                    <div>
                        <x-fragments.text-field label="Nama Kelas" name="name" placeholder="Masukan Judul Materi..." required />
                    </div>
                    <div class="">
                        <x-fragments.select-field label="Pilih Kelas" name="kelas_id" :options="$kelas->pluck('nama', 'id')->toArray()" required />
                        <p id="batch-info"></p>
                        <p id="batch-message" class="text-xs font-medium"></p>
                        <div id="batch-loading" class="mt-3" style="display: none;">
                            <div class="alert alert-info flex gap-2" role="alert">
                                <div role="status">
                                    <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin  fill-blue-600"
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



            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const kelasSelect = document.getElementById('kelas_id');
                    const batchInfo = document.getElementById('batch-info');
                    const batchMessage = document.getElementById('batch-message');
                    const loadingElement = document.getElementById('batch-loading');
                    const dropzone = document.getElementById('pdf-dropzone');
                    const fileInput = document.getElementById('pdf-input');
                    const dropArea = document.getElementById('drop-area');
                    const filePreview = document.getElementById('file-preview');
                    const fileName = document.getElementById('file-name');
                    const fileSize = document.getElementById('file-size');
                    const fileError = document.getElementById('file-error');

                    // Prevent default drag behaviors
                    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                        dropzone.addEventListener(eventName, preventDefaults, false);
                        document.body.addEventListener(eventName, preventDefaults, false);
                    });

                    // Highlight drop area when item is dragged over it
                    ['dragenter', 'dragover'].forEach(eventName => {
                        dropzone.addEventListener(eventName, highlight, false);
                    });

                    ['dragleave', 'drop'].forEach(eventName => {
                        dropzone.addEventListener(eventName, unhighlight, false);
                    });

                    // Handle dropped files
                    dropzone.addEventListener('drop', handleDrop, false);
                    fileInput.addEventListener('change', handleFileSelect, false);

                    function preventDefaults(e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }

                    function highlight() {
                        dropzone.classList.add('border-success', 'bg-light');
                    }

                    function unhighlight() {
                        dropzone.classList.remove('border-success', 'bg-light');
                    }

                    function handleDrop(e) {
                        const dt = e.dataTransfer;
                        const files = dt.files;
                        handleFiles(files);
                    }

                    function handleFileSelect(e) {
                        const files = e.target.files;
                        handleFiles(files);
                    }

                    function handleFiles(files) {
                        if (files.length > 0) {
                            const file = files[0];
                            if (validateFile(file)) {
                                displayFile(file);
                                // Set file ke input
                                const dt = new DataTransfer();
                                dt.items.add(file);
                                fileInput.files = dt.files;
                            }
                        }
                    }

                    function validateFile(file) {
                        // Reset error
                        fileError.textContent = '';
                        dropzone.classList.remove('border-danger');

                        // Check file type
                        if (file.type !== 'application/pdf') {
                            showError('Hanya file PDF yang diperbolehkan');
                            return false;
                        }

                        // Check file size (10MB = 10 * 1024 * 1024 bytes)
                        if (file.size > 10 * 1024 * 1024) {
                            showError('Ukuran file maksimal 10MB');
                            return false;
                        }

                        return true;
                    }

                    function displayFile(file) {
                        fileName.textContent = file.name;
                        fileSize.textContent = formatFileSize(file.size);

                        dropArea.classList.add('d-none');
                        filePreview.classList.remove('d-none');
                    }

                    function removeFile() {
                        fileInput.value = '';
                        dropArea.classList.remove('d-none');
                        filePreview.classList.add('d-none');
                        fileError.textContent = '';
                        dropzone.classList.remove('border-danger');
                    }

                    function showError(message) {
                        fileError.textContent = message;
                        dropzone.classList.add('border-danger');
                    }

                    function formatFileSize(bytes) {
                        if (bytes === 0) return '0 Bytes';
                        const k = 1024;
                        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                        const i = Math.floor(Math.log(bytes) / Math.log(k));
                        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                    }

                    // Make dropzone clickable
                    dropzone.addEventListener('click', function(e) {
                        if (e.target === dropzone || e.target.closest('#drop-area')) {
                            fileInput.click();
                        }
                    });

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
                })
            </script>
    @endswitch
@endsection
