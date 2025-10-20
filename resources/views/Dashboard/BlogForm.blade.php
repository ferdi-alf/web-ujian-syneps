@extends('layouts.dashboard-layouts')

@section('content')
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ $mode === 'create' ? 'Buat Blog Baru' : 'Edit Blog' }}
                </h1>
                <p class="text-gray-600 text-sm mt-1">
                    {{ $mode === 'create' ? 'Isi form untuk membuat artikel blog' : 'Update informasi blog Anda' }}
                </p>
            </div>
            <a href="{{ route('blog.index') }}"
                class="text-gray-700 bg-gray-100 hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
                <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form id="blogForm" enctype="multipart/form-data" x-data="blogFormHandler()" x-init="init()">
            @csrf
            @if ($mode === 'edit')
                @method('PUT')
                <input type="hidden" name="blog_id" id="blog_id" value="{{ $blog->id }}">
            @endif

            {{-- Thumbnail Upload --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-teal-500 transition"
                    @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
                    @drop.prevent="handleDrop($event)" :class="{ 'border-teal-500 bg-teal-50': isDragging }">
                    <div class="space-y-1 text-center w-full">
                        <template x-if="!previewImage">
                            <div>
                                <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-400"></i>
                                <div class="flex text-sm text-gray-600 mt-4 justify-center">
                                    <label
                                        class="relative cursor-pointer bg-white rounded-md font-medium text-teal-600 hover:text-teal-500">
                                        <span>Upload gambar</span>
                                        <input type="file" name="thumbnail" class="sr-only" accept="image/*"
                                            @change="handleFileSelect($event)">
                                    </label>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">PNG, JPG, WEBP up to 2MB</p>
                            </div>
                        </template>
                        <template x-if="previewImage">
                            <div class="relative inline-block">
                                <img :src="previewImage" class="h-48 w-auto rounded-lg">
                                <button type="button" @click="removeImage()"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-600 shadow-lg">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Judul --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Blog</label>
                <input type="text" name="judul" id="judul"
                    value="{{ $mode === 'edit' ? $blog->judul : old('judul') }}" required @input="generateSlug"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                    placeholder="Masukkan judul blog...">
                <p class="text-xs text-gray-500 mt-1">Slug: <span x-text="slugPreview"></span></p>
            </div>

            {{-- Slug --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                <input type="text" name="slug" id="slug"
                    value="{{ $mode === 'edit' ? $blog->slug : old('slug') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                    placeholder="slug-blog">
                <p class="text-xs text-gray-500 mt-1">URL-friendly version dari judul</p>
            </div>

            {{-- Type --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select name="type" id="type" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    <option value="">Pilih kategori</option>
                    <option value="acara" {{ $mode === 'edit' && $blog->type === 'acara' ? 'selected' : '' }}>Acara
                    </option>
                    <option value="tutorial" {{ $mode === 'edit' && $blog->type === 'tutorial' ? 'selected' : '' }}>
                        Tutorial</option>
                    <option value="pengumuman" {{ $mode === 'edit' && $blog->type === 'pengumuman' ? 'selected' : '' }}>
                        Pengumuman</option>
                    <option value="berita" {{ $mode === 'edit' && $blog->type === 'berita' ? 'selected' : '' }}>Berita
                    </option>
                    <option value="tips" {{ $mode === 'edit' && $blog->type === 'tips' ? 'selected' : '' }}>Tips
                    </option>
                </select>
            </div>

            {{-- Content Editor --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Konten Blog</label>
                <div id="quill-editor" class="bg-white" style="height: 400px;"></div>
                <input type="hidden" name="content" id="content">
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fa-solid fa-info-circle"></i>
                    Gambar akan diupload ke server dan tersimpan di <code>storage/blog-images/</code>
                </p>
            </div>

            {{-- Publish Status --}}
            <div class="mb-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_published" id="is_published" value="1"
                        {{ $mode === 'edit' && $blog->is_published ? 'checked' : '' }}
                        class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                    <label for="is_published" class="ml-2 text-sm text-gray-700">Publish blog sekarang</label>
                </div>
            </div>

            {{-- Submit Buttons --}}
            <div class="flex justify-end gap-3 pt-6 border-t">
                <a href="{{ route('blog.index') }}"
                    class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit" id="submitBtn"
                    class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition disabled:opacity-50 flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    <span>{{ $mode === 'create' ? 'Buat Blog' : 'Update Blog' }}</span>
                </button>
            </div>
        </form>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    <script>
        function blogFormHandler() {
            return {
                previewImage: @if ($mode === 'edit' && $blog->thumbnail)
                    '{{ asset('storage/' . $blog->thumbnail) }}'
                @else
                    null
                @endif ,
                isDragging: false,
                slugPreview: '{{ $mode === 'edit' ? $blog->slug : '' }}',
                quill: null,
                thumbnailFile: null,

                init() {
                    setTimeout(() => {
                        this.initQuill();
                        this.updateSlugPreview();
                    }, 100);
                },

                initQuill() {
                    if (this.quill) return;

                    const editorElement = document.getElementById('quill-editor');
                    if (!editorElement) {
                        console.error('Quill editor element not found');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Editor tidak ditemukan. Silakan refresh halaman.'
                        });
                        return;
                    }

                    const self = this;

                    try {
                        this.quill = new Quill('#quill-editor', {
                            theme: 'snow',
                            modules: {
                                toolbar: {
                                    container: [
                                        [{
                                            'header': [1, 2, 3, 4, 5, 6, false]
                                        }],
                                        [{
                                            'font': []
                                        }],
                                        [{
                                            'size': ['small', false, 'large', 'huge']
                                        }],
                                        ['bold', 'italic', 'underline', 'strike'],
                                        ['blockquote', 'code-block'],
                                        [{
                                            'list': 'ordered'
                                        }, {
                                            'list': 'bullet'
                                        }],
                                        [{
                                            'script': 'sub'
                                        }, {
                                            'script': 'super'
                                        }],
                                        [{
                                            'indent': '-1'
                                        }, {
                                            'indent': '+1'
                                        }],
                                        [{
                                            'direction': 'rtl'
                                        }],
                                        [{
                                            'color': []
                                        }, {
                                            'background': []
                                        }],
                                        [{
                                            'align': []
                                        }],
                                        ['link', 'image', 'video'],
                                        ['clean']
                                    ],
                                    handlers: {
                                        image: () => this.imageHandler()
                                    }
                                }
                            },
                            placeholder: 'Tulis konten blog Anda di sini...'
                        });

                        // Set initial content jika edit mode
                        @if ($mode === 'edit' && $blog->content)
                            this.quill.root.innerHTML = {!! json_encode($blog->content) !!};
                        @endif

                        // Update hidden input on content change
                        this.quill.on('text-change', () => {
                            document.getElementById('content').value = this.quill.root.innerHTML;
                        });

                    } catch (error) {
                        console.error('Error initializing Quill:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Editor Error',
                            text: 'Gagal memuat editor. Silakan refresh halaman.'
                        });
                    }
                },

                imageHandler() {
                    if (!this.quill) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Editor belum siap. Silakan coba lagi.'
                        });
                        return;
                    }

                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.click();

                    input.onchange = async () => {
                        const file = input.files[0];
                        if (!file) return;

                        // Validasi file
                        if (file.size > 2 * 1024 * 1024) {
                            Swal.fire({
                                icon: 'error',
                                title: 'File Terlalu Besar',
                                text: 'Ukuran gambar maksimal 2MB'
                            });
                            return;
                        }

                        // Validasi tipe file
                        if (!file.type.startsWith('image/')) {
                            Swal.fire({
                                icon: 'error',
                                title: 'File Tidak Valid',
                                text: 'Harap pilih file gambar yang valid'
                            });
                            return;
                        }

                        Swal.fire({
                            title: 'Uploading...',
                            text: 'Mohon tunggu, gambar sedang diupload',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        try {
                            const formData = new FormData();
                            formData.append('image', file);

                            const response = await fetch('{{ route('blog.uploadImage') }}', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        ?.content || '',
                                    'Accept': 'application/json'
                                }
                            });

                            const result = await response.json();

                            if (!response.ok || !result.success) {
                                throw new Error(result.message || 'Upload failed');
                            }

                            // FIX: Gunakan approach yang lebih aman untuk insert image
                            this.insertImageToEditor(result.url);

                            Swal.close();

                        } catch (error) {
                            console.error('Upload error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Upload Gagal',
                                text: error.message || 'Terjadi kesalahan saat mengupload gambar'
                            });
                        }
                    };
                },

                insertImageToEditor(imageUrl) {
                    if (!this.quill) return;

                    try {

                        this.quill.focus();


                        let range = this.quill.getSelection();

                        if (!range) {

                            range = {
                                index: this.quill.getLength(),
                                length: 0
                            };
                        }


                        this.quill.insertEmbed(range.index, 'image', imageUrl);
                        this.quill.setSelection(range.index + 1, 0);

                    } catch (error) {
                        console.error('Error inserting image:', error);
                        const fallbackPosition = this.quill.getLength();
                        this.quill.insertEmbed(fallbackPosition, 'image', imageUrl);
                        this.quill.setSelection(fallbackPosition + 1, 0);
                    }
                },

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    console.log('File selected:', file);

                    if (file) {
                        if (file.size > 2 * 1024 * 1024) {
                            Swal.fire({
                                icon: 'error',
                                title: 'File Terlalu Besar',
                                text: 'Ukuran file maksimal 2MB'
                            });

                            event.target.value = '';
                            return;
                        }

                        if (!file.type.startsWith('image/')) {
                            Swal.fire({
                                icon: 'error',
                                title: 'File Tidak Valid',
                                text: 'Harap pilih file gambar (JPEG, PNG, GIF, WEBP)'
                            });

                            event.target.value = '';
                            return;
                        }

                        this.thumbnailFile = file;
                        this.previewImage = URL.createObjectURL(file);

                        console.log('Thumbnail file set:', this.thumbnailFile);
                    }
                },

                handleDrop(event) {
                    this.isDragging = false;
                    const file = event.dataTransfer.files[0];
                    console.log('File dropped:', file);

                    if (file && file.type.startsWith('image/')) {
                        if (file.size > 2 * 1024 * 1024) {
                            Swal.fire({
                                icon: 'error',
                                title: 'File Terlalu Besar',
                                text: 'Ukuran file maksimal 2MB'
                            });
                            return;
                        }


                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        const fileInput = document.querySelector('input[name="thumbnail"]');
                        fileInput.files = dataTransfer.files;

                        this.thumbnailFile = file;
                        this.previewImage = URL.createObjectURL(file);

                        console.log('Thumbnail file set from drop:', this.thumbnailFile); // DEBUG
                    }
                },

                removeImage() {
                    this.previewImage = null;
                    this.thumbnailFile = null;
                    const fileInput = document.querySelector('input[name="thumbnail"]');
                    fileInput.value = '';

                    console.log('Thumbnail removed');
                },

                generateSlug(event) {
                    const text = event.target.value;
                    const slug = text
                        .toLowerCase()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/--+/g, '-')
                        .trim();

                    if ('{{ $mode }}' === 'create') {
                        document.getElementById('slug').value = slug;
                    }
                    this.slugPreview = slug;
                },

                updateSlugPreview() {
                    const slugInput = document.getElementById('slug');
                    if (slugInput) {
                        this.slugPreview = slugInput.value;
                        slugInput.addEventListener('input', (e) => {
                            this.slugPreview = e.target.value;
                        });
                    }
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('blogForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const quillContent = document.getElementById('content').value;
                if (!quillContent || quillContent === '<p><br></p>' || quillContent === '<p></p>') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Konten Kosong',
                        text: 'Mohon isi konten blog terlebih dahulu'
                    });
                    return;
                }

                console.log('Form elements:', form.elements);

                const formData = new FormData(form);

                const thumbnailInput = form.querySelector('input[name="thumbnail"]');
                console.log('Thumbnail input:', thumbnailInput);
                console.log('Thumbnail files:', thumbnailInput?.files);

                if (thumbnailInput && thumbnailInput.files.length > 0) {
                    console.log('Thumbnail file found:', thumbnailInput.files[0]);
                } else {
                    console.log('No thumbnail file selected');
                }

                console.log('FormData entries:');
                for (let [key, value] of formData.entries()) {
                    console.log(key + ': ', value);
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    <span>Menyimpan...</span>
                `;

                try {
                    const mode = '{{ $mode }}';
                    let url = '{{ route('blog.store') }}';

                    if (mode === 'edit') {
                        url = '{{ route('blog.update', ':id') }}'.replace(':id', document
                            .getElementById('blog_id').value);
                        formData.append('_method', 'PUT');
                    }

                    const response = await fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: result.message || (mode === 'create' ?
                                'Blog berhasil dibuat!' : 'Blog berhasil diupdate!'),
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = '{{ route('blog.index') }}';
                        });
                    } else {
                        let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                        if (result.errors) {
                            errorMessage = Object.values(result.errors).flat().join('\n');
                        } else if (result.message) {
                            errorMessage = result.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: errorMessage,
                            scrollbarPadding: false
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menyimpan data: ' + error.message
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `
                <i class="fa-solid fa-save"></i>
                <span>{{ $mode === 'create' ? 'Buat Blog' : 'Update Blog' }}</span>
            `;
                }
            });
        });
    </script>
@endsection
