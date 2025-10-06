@extends('layouts.dashboard-layouts')

@section('content')
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Blog</h1>
                <p class="text-gray-600 text-sm mt-1">Kelola artikel, tutorial, dan pengumuman</p>
            </div>
            <button type="button"
                onclick="openDrawerWithData('drawer-blog-form', { 
                id: 'create',
                fetchEndpoint: null,
                type: 'slideOver',
                title: 'Buat Blog Baru',
                description: 'Isi form untuk membuat artikel blog',
                drawerTarget: 'drawer-blog-form'
            })"
                class="text-white bg-gradient-to-r from-teal-400 via-teal-500 to-teal-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                <i class="fa-solid fa-plus mr-2"></i>Buat Blog
            </button>
        </div>
    </div>

    @if (count($blogs) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($blogs as $blog)
                <div
                    class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden flex flex-col">
                    {{-- Thumbnail --}}
                    <div class="relative h-48 overflow-hidden cursor-pointer"
                        onclick="openDrawerWithData('drawer-blog-form', {
                            id: '{{ $blog['id'] }}',
                            fetchEndpoint: '/blog/{{ $blog['slug'] }}',
                            drawerTarget: 'drawer-blog-form',
                            type: 'slideOver',
                            title: '{{ addslashes($blog['judul']) }}',
                            description: 'Detail artikel blog'
                        })">
                        <img src="{{ $blog['thumbnail'] }}" alt="{{ $blog['judul'] }}"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        <div class="absolute top-3 right-3">
                            {!! $blog['type_badge'] !!}
                        </div>
                        @if ($blog['is_published'])
                            <div class="absolute top-3 left-3">
                                <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full shadow-lg">
                                    <i class="fa-solid fa-check-circle mr-1"></i>Published
                                </span>
                            </div>
                        @else
                            <div class="absolute top-3 left-3">
                                <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded-full shadow-lg">
                                    <i class="fa-solid fa-file-circle-question mr-1"></i>Draft
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="p-5 flex-1 flex flex-col cursor-pointer"
                        onclick="openDrawerWithData('drawer-blog-form', {
                            id: '{{ $blog['id'] }}',
                            fetchEndpoint: '/blog/{{ $blog['slug'] }}',
                            drawerTarget: 'drawer-blog-form',
                            type: 'slideOver',
                            title: '{{ addslashes($blog['judul']) }}',
                            description: 'Detail artikel blog'
                        })">
                        <h3
                            class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2 hover:text-teal-600 transition-colors">
                            {{ $blog['judul'] }}
                        </h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3 flex-1">
                            {{ $blog['excerpt'] }}
                        </p>
                        <div class="flex items-center text-xs text-gray-500 mb-4">
                            <i class="fa-solid fa-user mr-1"></i>
                            <span class="mr-3">{{ $blog['created_by'] }}</span>
                            <i class="fa-solid fa-calendar mr-1"></i>
                            <span>{{ $blog['created_at'] }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="px-5 pb-4 flex gap-2 border-t border-gray-100 pt-4">
                        <button
                            onclick="event.stopPropagation(); openDrawerWithData('drawer-blog-form', {
                                id: '{{ $blog['id'] }}',
                                fetchEndpoint: '/blog/{{ $blog['slug'] }}/edit',
                                drawerTarget: 'drawer-blog-form',
                                type: 'slideOver',
                                title: 'Edit Blog',
                                description: 'Update informasi blog'
                            })"
                            class="flex-1 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200 text-sm font-medium">
                            <i class="fa-solid fa-pen mr-2"></i>Edit
                        </button>
                        <button
                            onclick="event.stopPropagation(); confirmDelete('{{ route('blog.destroy', $blog['id']) }}', 'Yakin ingin menghapus blog \"{{ addslashes($blog['judul']) }}\"?')"
                            class="flex-1 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors duration-200 text-sm font-medium">
                            <i class="fa-solid fa-trash mr-2"></i>Hapus
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow-sm">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-blog text-6xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Blog</h3>
            <p class="text-gray-500">Mulai buat artikel pertama Anda sekarang!</p>
        </div>
    @endif


    <x-drawer-layout type="slideOver" id="drawer-blog-form" title="Blog" description="Manajemen blog">
        <div x-data="blogDrawer()" x-init="init()" x-on:drawerdataloaded.window="handleDataLoaded($event)"
            class="space-y-6">

            {{-- VIEW MODE --}}
            <div x-show="mode === 'view'" class="prose max-w-none">
                <img :src="blogData.thumbnail" :alt="blogData.judul" class="w-full h-64 object-cover rounded-lg mb-6"
                    x-show="blogData.thumbnail">

                <div class="flex items-center justify-between mb-4">
                    <div x-html="blogData.type_badge"></div>
                    <span class="text-sm text-gray-500" x-text="blogData.created_at"></span>
                </div>

                <h1 class="text-3xl font-bold mb-2" x-text="blogData.judul"></h1>
                <p class="text-gray-600 mb-6">Oleh <span x-text="blogData.created_by"></span></p>

                <div class="mt-6 blog-content" x-html="blogData.content"></div>
            </div>

            {{-- FORM MODE (Create & Update) --}}
            <template x-if="mode === 'create' || mode === 'update'">
                <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-6">

                    {{-- Thumbnail Upload --}}
                    <div>
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
                                                <input type="file" class="sr-only" accept="image/*"
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Blog</label>
                        <input type="text" x-model="formData.judul" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            placeholder="Masukkan judul blog...">
                        <p class="text-xs text-gray-500 mt-1">Slug: <span x-text="generateSlug(formData.judul)"></span></p>
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select x-model="formData.type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                            <option value="">Pilih kategori</option>
                            <option value="acara">Acara</option>
                            <option value="tutorial">Tutorial</option>
                            <option value="pengumuman">Pengumuman</option>
                            <option value="berita">Berita</option>
                            <option value="tips">Tips</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Konten Blog</label>
                        <div id="quill-editor" class="bg-white" style="height: 300px;"></div>
                        <input type="hidden" x-model="formData.content" required>
                    </div>

                    {{-- Publish Status --}}
                    <div class="flex items-center">
                        <input type="checkbox" x-model="formData.is_published" id="is_published"
                            class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                        <label for="is_published" class="ml-2 text-sm text-gray-700">Publish blog sekarang</label>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" @click="window.closeDrawer('drawer-blog-form')"
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Batal
                        </button>
                        <button type="submit" :disabled="loading"
                            class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 disabled:opacity-50 flex items-center gap-2">
                            <i class="fa-solid" :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                            <span x-text="mode === 'create' ? 'Buat Blog' : 'Update Blog'"></span>
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </x-drawer-layout>

    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>



    <script>
        function blogDrawer() {
            return {
                mode: 'create',
                blogData: {},
                formData: {
                    judul: '',
                    type: '',
                    content: '',
                    is_published: false,
                },
                thumbnailFile: null,
                previewImage: null,
                isDragging: false,
                loading: false,
                editor: null,
                editorInitialized: false,
                blogId: null,

                init() {
                    console.log('BlogDrawer initialized');
                },

                initQuill(initialContent = '') {
                    const self = this;
                    const editorId = 'quill-editor';

                    // 1. Hancurkan editor lama jika ada
                    if (self.editor) {
                        self.editor = null;
                        self.editorInitialized = false;
                        // Opsional: Hapus elemen toolbar lama jika Quill dibuat ulang
                        const container = document.getElementById(editorId);
                        if (container) {
                            container.innerHTML = '';
                        }
                    }

                    // 2. Tunggu DOM selesai di-render dengan $nextTick
                    this.$nextTick(() => {
                        const toolbarOptions = [
                            [{
                                'header': [1, 2, 3, 4, 5, 6, false]
                            }],
                            [{
                                'font': []
                            }],
                            [{
                                'size': ['small', false, 'large', 'huge']
                            }], // Ukuran Font

                            ['bold', 'italic', 'underline', 'strike'], // Format teks dasar
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
                            }], // Indentasi

                            [{
                                'direction': 'rtl'
                            }],

                            [{
                                'color': []
                            }, {
                                'background': []
                            }], // Warna
                            [{
                                'align': []
                            }], // Posisi/Alignment

                            ['link', 'image', 'video'], // Insert Gambar/Link/Video

                            ['clean'] // Hapus format
                        ];

                        // Hanya inisialisasi jika elemen target ada
                        const targetElement = document.getElementById(editorId);
                        if (!targetElement) {
                            console.error('Quill target element not found.');
                            return;
                        }

                        self.editor = new Quill(`#${editorId}`, {
                            theme: 'snow',
                            modules: {
                                toolbar: toolbarOptions
                                // NOTE: Jika ingin upload gambar ke server, Anda perlu 
                                // menambahkan logic Image Handler di sini.
                            }
                        });

                        // 3. Set konten dan sinkronisasi data
                        if (initialContent) {
                            self.editor.root.innerHTML = initialContent;
                        }

                        self.editor.on('text-change', () => {
                            // Update formData.content setiap kali ada perubahan
                            self.formData.content = self.editor.root.innerHTML;
                        });

                        self.editorInitialized = true;
                        console.log('Quill initialized successfully');
                    });
                },

                handleDataLoaded(event) {
                    if (event.detail.drawerId !== 'drawer-blog-form') return;

                    const data = event.detail.data;
                    this.resetForm();

                    let initialContent = '';

                    if (data.id === 'create') {
                        this.mode = 'create';
                    } else if (data.dataUpdate) {
                        this.mode = 'update';
                        this.blogId = data.dataUpdate.id;
                        this.formData = {
                            judul: data.dataUpdate.judul,
                            type: data.dataUpdate.type,
                            content: data.dataUpdate.content,
                            is_published: data.dataUpdate.is_published,
                        };
                        this.previewImage = data.dataUpdate.thumbnail;
                        initialContent = data.dataUpdate.content || '';
                    } else if (data.viewData) {
                        this.mode = 'view';
                        this.blogData = data.viewData;
                        return; // Mode view tidak perlu editor
                    }

                    // Panggil fungsi inisialisasi Quill
                    if (this.mode === 'create' || this.mode === 'update') {
                        this.initQuill(initialContent);
                    }
                },

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.thumbnailFile = file;
                        this.previewImage = URL.createObjectURL(file);
                    }
                },

                handleDrop(event) {
                    this.isDragging = false;
                    const file = event.dataTransfer.files[0];
                    if (file && file.type.startsWith('image/')) {
                        this.thumbnailFile = file;
                        this.previewImage = URL.createObjectURL(file);
                    }
                },

                removeImage() {
                    this.thumbnailFile = null;
                    this.previewImage = null;
                },

                generateSlug(text) {
                    if (!text) return '';
                    return text.toLowerCase()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/--+/g, '-')
                        .trim();
                },

                async submitForm() {
                    if (!this.editorInitialized) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian',
                            text: 'Editor belum siap, mohon tunggu sebentar...'
                        });
                        return;
                    }

                    this.loading = true;


                    const formData = new FormData();
                    if (this.thumbnailFile) {
                        formData.append('thumbnail', this.thumbnailFile);
                    }
                    formData.append('judul', this.formData.judul);
                    formData.append('type', this.formData.type);
                    formData.append('content', editor.getContent());
                    formData.append('is_published', this.formData.is_published ? '1' : '0');

                    const url = this.mode === 'create' ? '/blog' : `/blog/${this.blogId}`;
                    if (this.mode === 'update') {
                        formData.append('_method', 'PUT');
                    }

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: formData
                        });

                        const result = await response.json();

                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: result.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: result.message || 'Terjadi kesalahan'
                            });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menyimpan data'
                        });
                    } finally {
                        this.loading = false;
                    }
                },

                resetForm() {
                    this.formData = {
                        judul: '',
                        type: '',
                        content: '',
                        is_published: false,
                    };
                    this.thumbnailFile = null;
                    this.previewImage = null;
                    this.blogId = null;
                    this.editorInitialized = false;


                    if (this.editor) {
                        this.editor.root.innerHTML = ''; // Clear content
                        // Jika ingin menghancurkan instance
                        this.editor = null;
                    }
                }
            }
        }

        // Delete confirmation function
        function confirmDelete(url, message) {
            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    window.location.reload();
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Gagal menghapus data'
                            });
                        });
                }
            });
        }
    </script>

    <style>
        .blog-content img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            margin: 1rem 0;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection
