@extends('layouts.dashboard-layouts')

@php
    use Carbon\Carbon;
    $grouped = $materi->groupBy(function ($item) {
        return Carbon::parse($item->created_at)->translatedFormat('F Y');
    });
@endphp

@section('content')
    <x-drawer-layout id="drawer-control-materi" title="Detail Materi" description="Preview dan informasi materi"
        type="slideOver">
        <x-pdf-viewer drawerId="drawer-control-materi" />
    </x-drawer-layout>
    @switch(Auth::user()->role)
        @case('siswa')
            <ol class="relative border-s-2  border-gray-300">
                @forelse ($grouped as $month => $items)
                    <li class="md:mb-10 mb-0 mt-5 ms-6">
                        <span
                            class="absolute flex items-center justify-center w-6 h-6 bg-teal-100 text-teal-500 font-bold rounded-full -start-3 ring-8 ring-white">{{ $loop->iteration }}</span>
                        <h3 class="flex items-center mb-1 text-lg font-semibold text-gray-900">
                            {{ $month }}
                        </h3>

                        @foreach ($items as $item)
                            <div class="mb-4 bg-white flex items-center justify-between shadow-md p-3 rounded-xl md:w-1/2">
                                <div class="flex gap-2 items-center">
                                    <div class="rounded-full p-3 h-10 flex justify-center items-center w-10 bg-red-100">
                                        <i class="fa-solid fa-file-pdf text-xl text-red-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-base font-medium text-gray-700">{{ $item->judul }}
                                        </p>
                                        <small>{{ $item->kelas->nama }} <span
                                                class="bg-purple-100 text-purple-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-purple-400">{{ $item->batch->nama }}</span></small>
                                        <time class="block mb-2 text-sm text-gray-400">
                                            {{ Carbon::parse($item->created_at)->translatedFormat('d M Y, H:i') }}
                                        </time>
                                    </div>
                                </div>
                                <div class="w-10 h-10">
                                    <x-action-buttons :viewData="[
                                        'id' => $item->id,
                                        'fetchEndpoint' => '/materi/pdf/' . $item->id,
                                        'drawerId' => 'drawer-control-materi',
                                        'type' => 'slideOver',
                                        'title' => 'Detail Materi: ' . $item->judul,
                                        'description' => 'Lihat detail dan preview PDF',
                                    ]" />
                                </div>
                            </div>
                        @endforeach
                    </li>
                @empty
                    <div class="bg-gray-100 rounded-xl p-2 flex flex-col justify-center items-center gap-2">
                        <h1 class="text-gray-800 text-lg">
                            Belum Ada Materi Yang Tersedia
                        </h1>
                    </div>
                @endforelse
            </ol>
        @break

        @default
            <div class="flex justify-end">
                <x-fragments.modal-button target="universal-materi-modal" variant="emerald" act="create">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Tambah Materi
                </x-fragments.modal-button>
            </div>


            <x-fragments.form-modal id="universal-materi-modal" title="Form Materi" action="{{ route('materi.store') }}"
                createTitle="Tambah Materi" editTitle="Edit Materi" size="xl">

                <div class="mb-3">
                    <label class="form-label fw-medium">Upload File PDF
                        <span id="pdf-required-indicator" class="text-danger">*</span>
                    </label>
                    <div id="pdf-upload-area-universal"
                        class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer transition-colors hover:border-blue-400 hover:bg-blue-50">
                        <div id="upload-placeholder-universal">
                            <i class="fa-solid fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                            <p class="text-gray-600 mb-2">Drag & drop file PDF di sini atau klik untuk memilih</p>
                            <p class="text-sm text-gray-500">Maksimal ukuran file: 10MB</p>
                            <div id="current-file-info-universal" class="mt-2 p-2 bg-gray-100 rounded hidden">
                                <p class="text-sm text-gray-600">File saat ini:</p>
                                <p class="text-sm font-medium text-gray-800">
                                    <i class="fa-solid fa-file-pdf text-red-500 mr-1"></i>
                                    <span id="current-file-name-universal"></span>
                                </p>
                            </div>
                        </div>
                        <div id="file-preview-universal" class="hidden overflow-hidden">
                            <i class="fa-solid fa-file-pdf text-red-500 text-4xl mb-3"></i>
                            <p id="file-name-universal" class="text-gray-700 font-medium"></p>
                            <p id="file-size-universal" class="text-sm text-gray-500"></p>
                            <button type="button" id="remove-file-universal" class="mt-2 text-red-500 hover:text-red-700 text-sm">
                                <i class="fa-solid fa-times mr-1"></i>Hapus File
                            </button>
                        </div>
                    </div>
                    <input type="file" id="pdf-file-input-universal" name="file_pdf" accept="application/pdf" class="hidden">
                </div>

                <x-fragments.text-field label="Judul Materi" name="judul" placeholder="Masukan Judul Materi..." required />
                <x-fragments.select-field label="Pilih Kelas" name="kelas_id" :options="$kelas->pluck('nama', 'id')->toArray()" required />

                <div id="batch-info-section">
                    <p id="batch-info-universal"></p>
                    <p id="batch-message-universal" class="text-xs font-medium"></p>
                    <div id="batch-loading-universal" class="mt-3" style="display: none;">
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
            </x-fragments.form-modal>

            <x-reusable-table :headers="['No', 'Judul Materi', 'Kelas', 'Batch', 'Tanggal Dibuat']" position="center" :data="$materi" :columns="[
                fn($row, $i) => $i + 1,
                fn($row) => $row->getFormattedTitleAttribute(),
                fn($row) => $row->kelas->nama ?? 'Tidak ada',
                fn($row) => $row->batch->nama ?? 'Tidak ada',
                fn($row) => $row->created_at->format('d M Y H:i'),
            ]" :showActions="true"
                :actionButtons="fn($row) => view('components.action-buttons', [
                    'modalTarget' => 'universal-materi-modal',
                    'deleteRoute' => route('materi.destroy', $row->id),
                    'editData' => [
                        'id' => $row->id,
                        'fetchEndpoint' => '/materi/' . $row->id,
                        'updateEndpoint' => '/materi/' . $row->id,
                        'act' => 'update',
                    ],
                    'viewData' => [
                        'id' => $row->id,
                        'fetchEndpoint' => '/materi/pdf/' . $row->id,
                        'drawerId' => 'drawer-control-materi',
                        'type' => 'slideOver',
                        'title' => 'Detail Materi: ' . $row->judul,
                        'description' => 'Lihat detail dan preview PDF',
                    ],
                ])" :searchBar="true" :truncate="true" :rowPerPage="10" position="left" :autoFilter="[2 => 'Kelas']"
                :filterPlaceholder="'Semua'" />

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    setupDragDrop(
                        'pdf-upload-area-universal',
                        'pdf-file-input-universal',
                        'upload-placeholder-universal',
                        'file-preview-universal',
                        'file-name-universal',
                        'file-size-universal',
                        'remove-file-universal'
                    );

                    document.addEventListener('modalUpdate', function(e) {
                        if (e.detail.modalId === 'universal-materi-modal') {
                            const materiData = e.detail.data;
                            document.getElementById('judul').value = materiData.judul || '';
                            document.getElementById('kelas_id').value = materiData.kelas_id || '';

                            const currentFileInfo = document.getElementById('current-file-info-universal');
                            const currentFileName = document.getElementById('current-file-name-universal');

                            if (materiData.file_pdf) {
                                currentFileName.textContent = materiData.file_pdf_name || 'File tersedia';
                                currentFileInfo.classList.remove('hidden');
                            } else {
                                currentFileInfo.classList.add('hidden');
                            }

                            document.getElementById('pdf-required-indicator').classList.add('hidden');
                            resetFileInput();
                        }
                    });

                    document.addEventListener('modalReset', function(e) {
                        if (e.detail.modalId === 'universal-materi-modal') {
                            document.getElementById('current-file-info-universal').classList.add('hidden');
                            resetFileInput();
                        }
                    });



                    const kelasSelect = document.getElementById('kelas_id');
                    if (kelasSelect) {
                        kelasSelect.addEventListener('change', function() {
                            const kelasId = this.value;
                            const batchInfo = document.getElementById('batch-info-universal');
                            const batchMessage = document.getElementById('batch-message-universal');
                            const loadingElement = document.getElementById('batch-loading-universal');

                            batchInfo.style.display = 'none';
                            if (!kelasId) return;

                            loadingElement.style.display = 'block';
                            fetch(`/tambah-ujian/check-batch-active/${kelasId}`, {
                                    method: 'GET',
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                            ?.getAttribute('content')
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    loadingElement.style.display = 'none';
                                    if (data.success) {
                                        batchInfo.style.display = 'block';
                                        batchMessage.textContent = data.message;
                                        batchMessage.className = data.hasActiveBatch ?
                                            'text-green-500 fw-medium' :
                                            'text-yellow-500 fw-medium';
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

                        uploadArea.addEventListener('click', () => fileInput.click());
                        fileInput.addEventListener('change', (e) => {
                            const file = e.target.files[0];
                            if (file) handleFile(file);
                        });

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
                            if (file.size > 10 * 1024 * 1024) {
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

                    function resetFileInput() {
                        const fileInput = document.getElementById('pdf-file-input-universal');
                        const filePreview = document.getElementById('file-preview-universal');
                        const uploadPlaceholder = document.getElementById('upload-placeholder-universal');

                        if (fileInput) fileInput.value = '';
                        if (filePreview) filePreview.classList.add('hidden');
                        if (uploadPlaceholder) uploadPlaceholder.classList.remove('hidden');
                    }
                });
            </script>
        @break
    @endswitch

    <script>
        document.addEventListener('drawerDataLoaded', function(e) {
            if (e.detail.drawerId === 'drawer-control-materi') {
                const materiData = e.detail.data;
                console.log('Materi data loaded:', materiData);
            }
        });
    </script>
@endsection
