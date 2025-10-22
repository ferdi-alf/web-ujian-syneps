@extends('layouts.landing-layout')

@section('title', 'Blog')
@section('description', 'Baca blog seputar coding, acara, berita dan tips dari Syneps Academy')

@push('styles')
    <style>
        .carousel-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .search-results {
            max-height: 400px;
            overflow-y: auto;
        }

        .blog-card-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }


        @media (max-width: 640px) {
            .blog-list-img {
                width: 80px;
                height: 80px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 pb-16">

        {{-- Carousel Section --}}
        @if ($carouselBlogs->count() > 0)
            <div class="mb-12">
                <div id="blog-carousel" class="relative w-full" data-carousel="slide">
                    <div class="relative h-56 overflow-hidden rounded-lg md:h-96">
                        @foreach ($carouselBlogs as $index => $blog)
                            <div class="{{ $index === 0 ? '' : 'hidden' }} duration-700 ease-in-out" data-carousel-item>
                                <a href="{{ route('read-blog.read', $blog->slug) }}" class="block relative w-full h-full">
                                    <img src="{{ $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : asset('images/default-blog.jpg') }}"
                                        class="carousel-img absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2"
                                        alt="{{ e($blog->judul) }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                    <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                                        <span
                                            class="inline-block px-3 py-1 text-xs font-semibold rounded-full mb-2 {{ getTypeBadge($blog->type) }}">
                                            {{ ucfirst($blog->type) }}
                                        </span>
                                        <h3 class="text-xl md:text-2xl font-bold">{{ e($blog->judul) }}</h3>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>


                    <div class="absolute z-30 flex -translate-x-1/2 bottom-5 left-1/2 space-x-3 rtl:space-x-reverse">
                        @foreach ($carouselBlogs as $index => $blog)
                            <button type="button" class="w-3 h-3 rounded-full bg-white/50 hover:bg-white"
                                aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"
                                data-carousel-slide-to="{{ $index }}"></button>
                        @endforeach
                    </div>


                    <button type="button"
                        class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                        data-carousel-prev>
                        <span
                            class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 group-hover:bg-white/50 transition">
                            <svg class="w-4 h-4 text-white rtl:rotate-180" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 1 1 5l4 4" />
                            </svg>
                            <span class="sr-only">Previous</span>
                        </span>
                    </button>
                    <button type="button"
                        class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                        data-carousel-next>
                        <span
                            class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 group-hover:bg-white/50 transition">
                            <svg class="w-4 h-4 text-white rtl:rotate-180" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 9 4-4-4-4" />
                            </svg>
                            <span class="sr-only">Next</span>
                        </span>
                    </button>
                </div>
            </div>
        @endif

        {{-- Search Bar --}}
        <div class="mb-12 relative">
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <input type="text" id="blog-search" placeholder="Cari blog..."
                        class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        autocomplete="off">
                    <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>

                    {{-- Search Results Dropdown --}}
                    <div id="search-results"
                        class="hidden absolute top-full mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg z-50 search-results">
                        <div id="search-loading" class="hidden p-4 text-center text-gray-500">
                            <svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                        <div id="search-content" class="divide-y divide-gray-100"></div>
                        <div id="search-empty" class="hidden p-4 text-center text-gray-500">
                            Tidak ada hasil ditemukan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pengumuman Section --}}
        @if ($pengumumanBlogs->count() > 0)
            <div class="mb-16">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <span class="w-1 h-8 bg-red-500 mr-3"></span>
                    Pengumuman
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($pengumumanBlogs as $blog)
                        <a href="{{ route('read-blog.read', $blog->slug) }}" class="group">
                            <div
                                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                                <div class="relative overflow-hidden">
                                    <img src="{{ $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : asset('images/default-blog.jpg') }}"
                                        alt="{{ e($blog->judul) }}"
                                        class="blog-card-img group-hover:scale-110 transition duration-300" loading="lazy">
                                    <span
                                        class="absolute top-3 right-3 px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Pengumuman
                                    </span>
                                </div>
                                <div class="p-5">
                                    <h3
                                        class="text-lg font-semibold mb-2 group-hover:text-blue-600 transition line-clamp-2">
                                        {{ e($blog->judul) }}
                                    </h3>
                                    <p class="text-gray-600 text-sm mb-3 line-clamp-3">
                                        {{ e(Str::limit(strip_tags($blog->content), 120)) }}
                                    </p>
                                    <span class="text-xs text-gray-500">{{ $blog->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Tutorial Section --}}
        @if ($tutorialBlogs->count() > 0)
            <div class="mb-16">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <span class="w-1 h-8 bg-blue-500 mr-3"></span>
                    Tutorial
                </h2>
                <div class="space-y-4">
                    @foreach ($tutorialBlogs as $blog)
                        <a href="{{ route('read-blog.read', $blog->slug) }}" class="group">
                            <div
                                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300 flex items-center flex-row">
                                <img src="{{ $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : asset('images/default-blog.jpg') }}"
                                    alt="{{ e($blog->judul) }}" class="blog-list-img p-3" loading="lazy">
                                <div class="p-4 flex-1">
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-semibold rounded-full mb-2 bg-blue-100 text-blue-800">
                                        Tutorial
                                    </span>
                                    <h3
                                        class="text-lg font-semibold mb-2 group-hover:text-blue-600 transition line-clamp-2">
                                        {{ e($blog->judul) }}
                                    </h3>
                                    <p class="text-gray-600 text-sm line-clamp-2 hidden sm:block">
                                        {{ e(Str::limit(strip_tags($blog->content), 150)) }}
                                    </p>
                                    <span
                                        class="text-xs text-gray-500 mt-2 block">{{ $blog->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Berita Section --}}
        @if ($beritaBlogs->count() > 0)
            <div class="mb-16">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <span class="w-1 h-8 bg-green-500 mr-3"></span>
                    Berita
                </h2>
                <div class="space-y-4">
                    @foreach ($beritaBlogs as $blog)
                        <a href="{{ route('read-blog.read', $blog->slug) }}" class="group">
                            <div
                                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300 flex items-center flex-row">
                                <img src="{{ $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : asset('images/default-blog.jpg') }}"
                                    alt="{{ e($blog->judul) }}" class="blog-list-img p-2" loading="lazy">
                                <div class="p-4 flex-1">
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-semibold rounded-full mb-2 bg-green-100 text-green-800">
                                        Berita
                                    </span>
                                    <h3
                                        class="text-lg font-semibold mb-2 group-hover:text-blue-600 transition line-clamp-2">
                                        {{ e($blog->judul) }}
                                    </h3>
                                    <p class="text-gray-600 text-sm line-clamp-2 hidden sm:block">
                                        {{ e(Str::limit(strip_tags($blog->content), 150)) }}
                                    </p>
                                    <span
                                        class="text-xs text-gray-500 mt-2 block">{{ $blog->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Tips & Acara Section  --}}
        <div class="grid md:grid-cols-2 gap-8">

            @if ($tipsBlogs->count() > 0)
                <div>
                    <h2 class="text-2xl font-bold mb-6 flex items-center">
                        <span class="w-1 h-8 bg-yellow-500 mr-3"></span>
                        Tips
                    </h2>
                    <div class="space-y-4">
                        @foreach ($tipsBlogs as $blog)
                            <a href="{{ route('read-blog.read', $blog->slug) }}"
                                class="group w-full bg-white flex flex-row">
                                <img src="{{ $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : asset('images/default-blog.jpg') }}"
                                    alt="{{ e($blog->judul) }}" class="blog-list-img p-3" loading="lazy">
                                <div class=" w-full rounded-lg shadow-md p-4 hover:shadow-xl transition duration-300">
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-semibold rounded-full mb-2 bg-yellow-100 text-yellow-800">
                                        Tips
                                    </span>
                                    <h3 class="font-semibold mb-2 group-hover:text-blue-600 transition line-clamp-2">
                                        {{ e($blog->judul) }}
                                    </h3>
                                    <span class="text-xs text-gray-500">{{ $blog->created_at->format('d M Y') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Acara Section --}}
            @if ($acaraBlogs->count() > 0)
                <div>
                    <h2 class="text-2xl font-bold mb-6 flex items-center">
                        <span class="w-1 h-8 bg-purple-500 mr-3"></span>
                        Acara
                    </h2>
                    <div class="space-y-4">
                        @foreach ($acaraBlogs as $blog)
                            <a href="{{ route('read-blog.read', $blog->slug) }}"
                                class="group w-full bg-white flex flex-row">
                                <img src="{{ $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : asset('images/default-blog.jpg') }}"
                                    alt="{{ e($blog->judul) }}" class="blog-list-img p-3" loading="lazy">
                                <div class=" w-full rounded-lg shadow-md p-4 hover:shadow-xl transition duration-300">
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-semibold rounded-full mb-2 bg-purple-100 text-purple-800">
                                        Acara
                                    </span>
                                    <h3 class="font-semibold mb-2 group-hover:text-blue-600 transition line-clamp-2">
                                        {{ e($blog->judul) }}
                                    </h3>
                                    <span class="text-xs text-gray-500">{{ $blog->created_at->format('d M Y') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let searchTimeout;
        const searchInput = document.getElementById('blog-search');
        const searchResults = document.getElementById('search-results');
        const searchContent = document.getElementById('search-content');
        const searchLoading = document.getElementById('search-loading');
        const searchEmpty = document.getElementById('search-empty');

        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.trim();

            clearTimeout(searchTimeout);

            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }

            searchLoading.classList.remove('hidden');
            searchContent.innerHTML = '';
            searchEmpty.classList.add('hidden');
            searchResults.classList.remove('hidden');

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('read-blog.search') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchLoading.classList.add('hidden');

                        if (data.success && data.data.length > 0) {
                            searchContent.innerHTML = data.data.map(blog => `
                            <a href="/read-blog/${blog.slug}" class="flex items-center p-3 hover:bg-gray-50 transition">
                                <img src="${blog.thumbnail}" 
                                     alt="${blog.judul}" 
                                     class="w-16 h-16 object-cover rounded flex-shrink-0"
                                     loading="lazy">
                                <div class="ml-3 flex-1">
                                    <h4 class="font-semibold text-sm line-clamp-1">${blog.judul}</h4>
                                    <span class="text-xs px-2 py-1 rounded-full inline-block mt-1 ${getTypeBadgeClass(blog.type)}">
                                        ${capitalizeFirst(blog.type)}
                                    </span>
                                </div>
                            </a>
                        `).join('');
                        } else {
                            searchEmpty.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        searchLoading.classList.add('hidden');
                        searchContent.innerHTML =
                            '<div class="p-4 text-center text-red-500">Terjadi kesalahan</div>';
                    });
            }, 300);
        });


        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });

        function getTypeBadgeClass(type) {
            const badges = {
                'acara': 'bg-purple-100 text-purple-800',
                'tutorial': 'bg-blue-100 text-blue-800',
                'pengumuman': 'bg-red-100 text-red-800',
                'berita': 'bg-green-100 text-green-800',
                'tips': 'bg-yellow-100 text-yellow-800'
            };
            return badges[type] || 'bg-gray-100 text-gray-800';
        }

        function capitalizeFirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    </script>
@endpush

@php
    function getTypeBadge($type)
    {
        $badges = [
            'acara' => 'bg-purple-100 text-purple-800',
            'tutorial' => 'bg-blue-100 text-blue-800',
            'pengumuman' => 'bg-red-100 text-red-800',
            'berita' => 'bg-green-100 text-green-800',
            'tips' => 'bg-yellow-100 text-yellow-800',
        ];
        return $badges[$type] ?? 'bg-gray-100 text-gray-800';
    }
@endphp
