@extends('layouts.dashboard-layouts')

@section('content')
    <div class="mb-6">
        <div class="flex md:flex-row flex-col gap-4  justify-between items-start md:items-center">
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

    <div class="mb-6">
        <div class="relative">
            <input type="text" id="searchInput" placeholder="Cari blog berdasarkan judul atau konten..."
                class="w-full px-4 py-3 pl-10 pr-4 text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-teal-500 focus:ring-2 focus:ring-teal-200 focus:outline-none transition-all">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="fa-solid fa-search text-gray-400"></i>
            </div>
            <div id="searchLoading" class="absolute inset-y-0 right-0 flex items-center pr-3 hidden">
                <i class="fa-solid fa-spinner fa-spin text-gray-400"></i>
            </div>
        </div>
    </div>

    <div id="blogContainer">
        @include('blog.partials.blog-grid', ['blogs' => $blogs])
    </div>

    <div id="paginationContainer" class="mt-8">
        @if (count($blogs) > 0)
            <div class="flex items-center justify-between bg-white p-4 rounded-lg shadow-sm">
                <button id="prevBtn"
                    class="sm:px-4 px-2 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-chevron-left"></i>
                    <span>Previous</span>
                </button>

                <div class="flex sm:flex-row flex-col items-center gap-3">
                    <div class="flex sm:flex-row items-center flex-col gap-1.5">
                        <span class="text-sm text-gray-600">Halaman</span>
                        <span id="currentPage"
                            class="px-4 py-2 bg-teal-500 text-white rounded-lg font-medium min-w-[40px] text-center">1</span>
                        <span class="text-sm text-gray-600">dari <span id="totalPages"
                                class="text-sm text-gray-700 font-medium">1</span></span>

                    </div>
                    <span class="text-gray-300">|</span>
                    <div class="truncate">
                        <span class="text-sm text-gray-600">Total: <span id="totalBlogs"
                                class="font-medium text-gray-700">0</span> blog</span>
                    </div>
                </div>

                <button id="nextBtn"
                    class="sm:px-4 px-2 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2">
                    <span>Next</span>
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        @endif
    </div>

    <x-drawer-layout type="slideOver" id="drawer-blog-form" title="Blog" description="Detail artikel blog">
        <div x-data="{
            blogData: null,
            loading: false,
            error: null
        }" x-init="init()"
            x-on:drawerDataLoaded.window="
            if ($event.detail.drawerId === 'drawer-blog-form') {
                loading = true;
                error = null;
                blogData = $event.detail.data;
                console.log('Blog data diterima:', blogData);
                loading = false;
            }
        ">
            <div x-show="loading" class="text-center py-12">
                <i class="fa-solid fa-spinner fa-spin text-4xl text-teal-500"></i>
                <p class="mt-4 text-gray-600">Memuat data...</p>
            </div>

            <div x-show="!loading && error" class="text-center py-12">
                <i class="fa-solid fa-exclamation-circle text-4xl text-red-500"></i>
                <p class="mt-4 text-red-600" x-text="error"></p>
            </div>

            <template x-if="blogData">
                <div class="">

                    <div class="w-full flex justify-center items-center">
                        <img :src="blogData.thumbnail" :alt="blogData.judul" class="w-1/3 object-cover rounded-lg mb-6"
                            x-show="blogData.thumbnail">

                    </div>

                    <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                        <div x-html="blogData.type_badge"></div>
                        <span class="text-sm text-gray-500" x-text="blogData.created_at"></span>
                    </div>

                    <h1 class="text-3xl font-bold mb-2 text-gray-900" x-text="blogData.judul"></h1>
                    <p class="text-gray-600 mb-6">
                        <i class="fa-solid fa-user mr-1 "></i>
                        Oleh <span class="font-medium" x-text="blogData.created_by"></span>
                    </p>


                    <div class="mt-6 blog-content p-2 pb-10" x-html="blogData.content"></div>
                </div>
            </template>
        </div>
    </x-drawer-layout>

    <script>
        function blogDrawer() {
            return {
                blogData: [],
                loading: false,
                error: null,

                init() {
                    console.log('BlogDrawer initialized');
                },

                handleDataLoaded(event) {
                    if (event.detail.drawerId !== 'drawer-blog-form') return;

                    this.loading = true;
                    this.error = null;

                    const response = event.detail.data || [];
                    console.log('Drawer data received:', response);


                    if (response) {
                        this.blogData = response.data;
                        this.loading = false;
                    } else {
                        this.error = 'Format data tidak sesuai';
                        this.loading = false;
                        console.error('Invalid data format:', response);
                    }
                }
            }
        }


        let currentPage = 1;
        let totalPages = 1;
        let totalBlogs = 0;
        let isLoading = false;
        let searchTimeout;
        const cache = new Map();
        const CACHE_DURATION = 5 * 60 * 1000;

        function getCacheKey(page, search) {
            return `blog_${page}_${search || 'all'}`;
        }

        async function loadBlogs(page = 1, search = '') {
            if (isLoading) return;

            const cacheKey = getCacheKey(page, search);

            // Check cache first
            const cached = cache.get(cacheKey);
            if (cached && (Date.now() - cached.timestamp < CACHE_DURATION)) {
                console.log('Loading from cache:', cacheKey);
                updateUI(cached.data, page);
                return;
            }

            isLoading = true;
            const container = document.getElementById('blogContainer');

            container.innerHTML = `
                <div class="text-center py-12 bg-white rounded-lg shadow-sm">
                    <i class="fa-solid fa-spinner fa-spin text-4xl text-teal-500"></i>
                    <p class="mt-4 text-gray-600">Memuat data...</p>
                </div>
                `;

            try {
                const url = new URL('{{ route('blog.index') }}', window.location.origin);
                url.searchParams.append('page', page);
                url.searchParams.append('per_page', 9);
                if (search) url.searchParams.append('search', search);

                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Failed to fetch blogs');

                const data = await response.json();

                // Cache the result
                cache.set(cacheKey, {
                    data: data,
                    timestamp: Date.now()
                });

                updateUI(data, page);
            } catch (error) {
                console.error('Error loading blogs:', error);
                container.innerHTML = `
                    <div class="text-center py-12 bg-white rounded-lg shadow-sm">
                        <i class="fa-solid fa-exclamation-circle text-4xl text-red-500"></i>
                        <p class="mt-4 text-red-600">Gagal memuat data. Silakan coba lagi.</p>
                        <button onclick="loadBlogs(${page}, '${search}')" 
                            class="mt-4 px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600">
                            Coba Lagi
                        </button>
                    </div>
                `;
            } finally {
                isLoading = false;
            }
        }

        function updateUI(data, page) {
            const container = document.getElementById('blogContainer');

            if (data.success && data.data && data.data.length > 0) {
                container.innerHTML = generateBlogGrid(data.data);

                currentPage = page;
                totalPages = data.meta ? data.meta.last_page : 1;
                totalBlogs = data.meta ? data.meta.total : data.data.length;
                updatePaginationUI();
            } else {
                container.innerHTML = `
                    <div class="text-center py-12 bg-white rounded-lg shadow-sm">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-search text-6xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Blog Ditemukan</h3>
                        <p class="text-gray-500">Coba kata kunci lain atau buat blog baru.</p>
                    </div>
                `;
                document.getElementById('paginationContainer').innerHTML = '';
            }
        }

        function generateBlogGrid(blogs) {
            return `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    ${blogs.map(blog => `
                                                                                                                                                                                                        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden flex flex-col">
                                                                                                                                                                                                            <!-- Thumbnail -->
                                                                                                                                                                                                            <div class="relative h-48 overflow-hidden cursor-pointer bg-gray-100"
                                                                                                                                                                                                                onclick="openDrawerWithData('drawer-blog-form', {
                                                                                                                                                                                                                    id: '${blog.id}',
                                                                                                                                                                                                                    fetchEndpoint: '/blog/${blog.slug}/show',
                                                                                                                                                                                                                    drawerTarget: 'drawer-blog-form',
                                                                                                                                                                                                                    type: 'slideOver',
                                                                                                                                                                                                                    title: '${escapeHtml(blog.judul)}',
                                                                                                                                                                                                                    description: 'Detail artikel blog'
                                                                                                                                                                                                                })">
                                                                                                                                                                                                                <div class="w-full h-full flex items-center justify-center">
                                                                                                                                                                                                                    <img src="${blog.thumbnail}" 
                                                                                                                                                                                                                         alt="${escapeHtml(blog.judul)}"
                                                                                                                                                                                                                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                                                                                                                                                                                                         loading="lazy">
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                <div class="absolute top-3 right-3">
                                                                                                                                                                                                                    ${blog.type_badge}
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                ${blog.is_published ? `
                                    <div class="absolute top-3 left-3">
                                        <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full shadow-lg backdrop-blur-sm bg-opacity-90">
                                            <i class="fa-solid fa-check-circle mr-1"></i>Published
                                        </span>
                                    </div>
                                ` : `
                                    <div class="absolute top-3 left-3">
                                        <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded-full shadow-lg backdrop-blur-sm bg-opacity-90">
                                            <i class="fa-solid fa-file-circle-question mr-1"></i>Draft
                                        </span>
                                    </div>
                                `}
                                                                                                                                                                                                            </div>

                                                                                                                                                                                                            <!-- Content -->
                                                                                                                                                                                                            <div class="p-5 flex-1 flex flex-col cursor-pointer"
                                                                                                                                                                                                                onclick="openDrawerWithData('drawer-blog-form', {
                                                                                                                                                                                                                    id: '${blog.id}',
                                                                                                                                                                                                                    fetchEndpoint: '/blog/${blog.slug}/show',
                                                                                                                                                                                                                    drawerTarget: 'drawer-blog-form',
                                                                                                                                                                                                                    type: 'slideOver',
                                                                                                                                                                                                                    title: '${escapeHtml(blog.judul)}',
                                                                                                                                                                                                                    description: 'Detail artikel blog'
                                                                                                                                                                                                                })">
                                                                                                                                                                                                                <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2 hover:text-teal-600 transition-colors">
                                                                                                                                                                                                                    ${escapeHtml(blog.judul)}
                                                                                                                                                                                                                </h3>
                                                                                                                                                                                                                <p class="text-gray-600 text-sm mb-4 line-clamp-3 flex-1">
                                                                                                                                                                                                                    ${escapeHtml(blog.excerpt)}
                                                                                                                                                                                                                </p>
                                                                                                                                                                                                                <div class="flex items-center text-xs text-gray-500">
                                                                                                                                                                                                                    <i class="fa-solid fa-user mr-1"></i>
                                                                                                                                                                                                                    <span class="mr-3">${escapeHtml(blog.created_by)}</span>
                                                                                                                                                                                                                    <i class="fa-solid fa-calendar mr-1"></i>
                                                                                                                                                                                                                    <span>${blog.created_at}</span>
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                            </div>

                                                                                                                                                                                                            <!-- Actions -->
                                                                                                                                                                                                            <div class="px-5 pb-4 flex gap-2 border-t border-gray-100 pt-3">
                                                                                                                                                                                                                <a href="{{ route('blog.index') }}/${blog.slug}/edit"
                                                                                                                                                                                                                    onclick="event.stopPropagation();"
                                                                                                                                                                                                                    class="flex-1 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200 text-sm font-medium text-center inline-flex items-center justify-center">
                                                                                                                                                                                                                    <i class="fa-solid fa-pen mr-2"></i>Edit
                                                                                                                                                                                                                </a>
                                                                                                                                                                                                                <button
                                                                                                                                                                                                                    onclick="event.stopPropagation(); confirmDelete('${blog.id}', '${escapeHtml(blog.judul)}');"
                                                                                                                                                                                                                    class="flex-1 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors duration-200 text-sm font-medium inline-flex items-center justify-center">
                                                                                                                                                                                                                    <i class="fa-solid fa-trash mr-2"></i>Hapus
                                                                                                                                                                                                                </button>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    `).join('')}
                </div>
            `;
        }

        function updatePaginationUI() {
            document.getElementById('currentPage').textContent = currentPage;
            document.getElementById('totalPages').textContent = totalPages;
            document.getElementById('totalBlogs').textContent = totalBlogs;

            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');

            if (prevBtn) prevBtn.disabled = currentPage === 1;
            if (nextBtn) nextBtn.disabled = currentPage >= totalPages;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        document.getElementById('searchInput')?.addEventListener('input', function(e) {
            const searchValue = e.target.value.trim();
            const loadingIcon = document.getElementById('searchLoading');

            clearTimeout(searchTimeout);
            loadingIcon.classList.remove('hidden');

            searchTimeout = setTimeout(() => {
                loadingIcon.classList.add('hidden');
                currentPage = 1;
                loadBlogs(1, searchValue);
            }, 500);
        });

        document.getElementById('prevBtn')?.addEventListener('click', function() {
            if (currentPage > 1) {
                const searchValue = document.getElementById('searchInput').value.trim();
                loadBlogs(currentPage - 1, searchValue);
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        });

        document.getElementById('nextBtn')?.addEventListener('click', function() {
            if (currentPage < totalPages) {
                const searchValue = document.getElementById('searchInput').value.trim();
                loadBlogs(currentPage + 1, searchValue);
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        });


        function confirmDelete(blogId, blogTitle) {
            Swal.fire({
                title: 'Konfirmasi',
                text: `Yakin ingin menghapus blog "${blogTitle}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteBlog(blogId);
                }
            });
        }

        async function deleteBlog(blogId) {
            try {
                const response = await fetch(`${blogId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {

                        cache.clear();


                        const searchValue = document.getElementById('searchInput').value.trim();
                        loadBlogs(currentPage, searchValue);
                    });
                } else {
                    throw new Error(data.message || 'Gagal menghapus blog');
                }
            } catch (error) {
                console.error('Delete error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Gagal menghapus data'
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const initialBlogs = {{ count($blogs) }};
            totalPages = Math.ceil(initialBlogs / 9) || 1;
            totalBlogs = initialBlogs;
            updatePaginationUI();
        });
    </script>

    <style>
        .blog-content img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            margin: 1rem 0;
        }

        .blog-content {
            line-height: 1.8;
        }

        .blog-content h1,
        .blog-content h2,
        .blog-content h3 {
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .blog-content p {
            margin-bottom: 1rem;
        }

        .blog-content ul,
        .blog-content ol {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
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

        .prose::-webkit-scrollbar {
            width: 8px;
        }

        .prose::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .prose::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .prose::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endsection
