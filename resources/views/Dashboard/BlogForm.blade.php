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

    <div class="bg-white rounded-lg shadow-md p-6" x-data="blogFormHandler()" x-init="init()">
        <form @submit.prevent="submitForm()" enctype="multipart/form-data">
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
                                        <input type="file" x-ref="thumbnailInput" class="sr-only" accept="image/*"
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
                <input type="text" x-model="formData.judul" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                    placeholder="Masukkan judul blog...">
                <p class="text-xs text-gray-500 mt-1">Slug akan di-generate otomatis dari judul</p>
            </div>

            {{-- Type --}}
            <div class="mb-6">
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

            {{-- Content Editor --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Konten Blog</label>
                <textarea x-ref="tinymceEditor" class="w-full"></textarea>
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fa-solid fa-info-circle"></i>
                    Gambar akan diupload ke server dan tersimpan di <code>storage/blog-content/</code>
                </p>
            </div>

            {{-- Publish Status --}}
            <div class="mb-6">
                <div class="flex items-center">
                    <input type="checkbox" x-model="formData.is_published"
                        class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                    <label class="ml-2 text-sm text-gray-700">Publish blog sekarang</label>
                </div>
            </div>

            {{-- Submit Buttons --}}
            <div class="flex justify-end gap-3 pt-6 border-t">
                <a href="{{ route('blog.index') }}"
                    class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit" :disabled="isSubmitting"
                    class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition disabled:opacity-50 flex items-center gap-2">
                    <i class="fa-solid" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                    <span
                        x-text="isSubmitting ? 'Menyimpan...' : '{{ $mode === 'create' ? 'Buat Blog' : 'Update Blog' }}'"></span>
                </button>
            </div>
        </form>
    </div>

    {{-- TinyMCE CDN --}}
    <script src="https://cdn.tiny.cloud/1/{{ config('services.tiny.key') }}/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script>
        function blogFormHandler() {
            return {

                previewImage: @json($mode === 'edit' && $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : null),
                isDragging: false,
                thumbnailFile: null,
                isSubmitting: false,
                editor: null,
                formData: {
                    judul: @json($mode === 'edit' ? $blog->judul : ''),
                    type: @json($mode === 'edit' ? $blog->type : ''),
                    is_published: {{ $mode === 'edit' && $blog->is_published ? 'true' : 'false' }}
                },

                init() {
                    this.initTinyMCE();
                },


                initTinyMCE() {
                    tinymce.init({
                        target: this.$refs.tinymceEditor,
                        height: 500,
                        menubar: false,
                        plugins: [
                            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                            'insertdatetime', 'media', 'table', 'help', 'wordcount'
                        ],
                        toolbar: 'undo redo | blocks | ' +
                            'bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter ' +
                            'alignright alignjustify | bullist numlist outdent indent | ' +
                            'removeformat | image media link | code | help',
                        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                        relative_urls: false,
                        remove_script_host: false,
                        convert_urls: true,
                        images_upload_handler: (blobInfo, progress) => {
                            return new Promise((resolve, reject) => {
                                this.uploadImage(blobInfo.blob())
                                    .then(url => resolve(url))
                                    .catch(error => reject(error));
                            });
                        },
                        automatic_uploads: true,
                        file_picker_types: 'image',

                        image_advtab: true,
                        image_class_list: [{
                                title: 'Responsive',
                                value: 'img-fluid'
                            },
                            {
                                title: 'Rounded',
                                value: 'rounded'
                            }
                        ],

                        setup: (editor) => {
                            this.editor = editor;

                            @if ($mode === 'edit' && $blog->content)
                                editor.on('init', () => {
                                    editor.setContent({!! json_encode($blog->content) !!});
                                });
                            @endif
                        }
                    });
                },

                async uploadImage(blob) {
                    const formData = new FormData();
                    formData.append('image', blob);

                    try {
                        const response = await fetch('{{ route('blog.uploadImage') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            throw new Error(result.message || 'Upload failed');
                        }

                        return result.url;
                    } catch (error) {
                        console.error('Upload error:', error);
                        throw error;
                    }
                },


                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (!file) return;

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
                            text: 'Harap pilih file gambar'
                        });
                        event.target.value = '';
                        return;
                    }

                    this.thumbnailFile = file;
                    this.previewImage = URL.createObjectURL(file);
                },


                handleDrop(event) {
                    this.isDragging = false;
                    const file = event.dataTransfer.files[0];

                    if (!file || !file.type.startsWith('image/')) return;

                    if (file.size > 2 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Terlalu Besar',
                            text: 'Ukuran file maksimal 2MB'
                        });
                        return;
                    }

                    this.thumbnailFile = file;
                    this.previewImage = URL.createObjectURL(file);

                    // Update file input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    this.$refs.thumbnailInput.files = dataTransfer.files;
                },


                removeImage() {
                    this.previewImage = null;
                    this.thumbnailFile = null;
                    this.$refs.thumbnailInput.value = '';
                },


                async submitForm() {
                    // Get content from TinyMCE
                    const content = this.editor.getContent();

                    if (!content || content === '' || content === '<p></p>') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Konten Kosong',
                            text: 'Mohon isi konten blog terlebih dahulu'
                        });
                        return;
                    }

                    this.isSubmitting = true;

                    try {
                        // Build FormData
                        const formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('judul', this.formData.judul);
                        formData.append('type', this.formData.type);
                        formData.append('content', content);

                        if (this.formData.is_published) {
                            formData.append('is_published', '1');
                        }

                        if (this.thumbnailFile) {
                            formData.append('thumbnail', this.thumbnailFile);
                            console.log('Thumbnail attached:', this.thumbnailFile);
                        }

                        console.log('FormData contents:');
                        for (let [key, value] of formData.entries()) {
                            console.log(key + ':', value);
                        }

                        // Determine URL
                        const mode = '{{ $mode }}';
                        let url = '{{ route('blog.store') }}';

                        if (mode === 'edit') {
                            const blogId = document.getElementById('blog_id')?.value;
                            url = '{{ route('blog.update', ':id') }}'.replace(':id', blogId);
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
                            await Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: result.message || (mode === 'create' ? 'Blog berhasil dibuat!' :
                                    'Blog berhasil diupdate!'),
                                showConfirmButton: false,
                                timer: 1500
                            });
                            window.location.href = '{{ route('blog.index') }}';
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
                                text: errorMessage
                            });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan: ' + error.message
                        });
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }
        }
    </script>
@endsection
