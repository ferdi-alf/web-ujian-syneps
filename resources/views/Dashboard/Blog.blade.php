@extends('layouts.dashboard-layouts')

@section('content')
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Blog</h1>
                <p class="text-gray-600 text-sm mt-1">Kelola artikel, tutorial, dan pengumuman</p>
            </div>
            <a href="{{ route('blog.index', ['act' => 'create']) }}"
                class="text-white bg-gradient-to-r from-teal-400 via-teal-500 to-teal-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-block">
                <i class="fa-solid fa-plus mr-2"></i>Buat Blog
            </a>
        </div>
    </div>

    @if (count($blogs) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($blogs as $blog)
                <div
                    class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden flex flex-col">

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


                    <div class="px-5 pb-4 flex gap-2 border-t border-gray-100 pt-4">
                        <a href="{{ route('blog.show', ['slug' => $blog['slug'], 'act' => 'edit']) }}"
                            onclick="event.stopPropagation();"
                            class="flex-1 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200 text-sm font-medium text-center">
                            <i class="fa-solid fa-pen mr-2"></i>Edit
                        </a>
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


    <x-drawer-layout type="slideOver" id="drawer-blog-form" title="Blog" description="Detail artikel blog">
        <div x-data="blogDrawer()" x-init="init()" x-on:drawerdataloaded.window="handleDataLoaded($event)">

            <div class="prose max-w-none">
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

        </div>
    </x-drawer-layout>
    <script>
        function blogDrawer() {
            return {
                blogData: {},

                init() {
                    console.log('BlogDrawer initialized - View Only');
                },

                handleDataLoaded(event) {
                    if (event.detail.drawerId !== 'drawer-blog-form') return;

                    const data = event.detail.data;

                    if (data.viewData) {
                        this.blogData = data.viewData;
                    }
                }
            }
        }

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
