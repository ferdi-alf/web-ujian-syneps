@extends('layouts.landing-layout')

@section('title', e($blog->judul))
@section('description', e(Str::limit(strip_tags($blog->content), 160)))


<style>
    .blog-content {
        line-height: 1.8;
    }

    .blog-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1.5rem 0;
    }

    .blog-content h1,
    .blog-content h2,
    .blog-content h3 {
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: 700;
    }

    .blog-content h1 {
        font-size: 2rem;
    }

    .blog-content h2 {
        font-size: 1.75rem;
    }

    .blog-content h3 {
        font-size: 1.5rem;
    }

    .blog-content p {
        margin-bottom: 1.25rem;
    }

    .blog-content ul,
    .blog-content ol {
        margin: 1rem 0 1.5rem 1.5rem;
    }

    .blog-content li {
        margin-bottom: 0.5rem;
    }

    .blog-content code {
        background-color: #f3f4f6;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        font-family: 'Courier New', monospace;
    }

    .blog-content pre {
        background-color: #1f2937;
        color: #f9fafb;
        padding: 1.5rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin: 1.5rem 0;
    }

    .blog-content pre code {
        background-color: transparent;
        padding: 0;
        color: inherit;
    }

    .blog-content blockquote {
        border-left: 4px solid #3b82f6;
        padding-left: 1.5rem;
        margin: 1.5rem 0;
        font-style: italic;
        color: #4b5563;
    }

    .blog-content a {
        color: #3b82f6;
        text-decoration: underline;
    }

    .blog-content a:hover {
        color: #2563eb;
    }

    .recommendation-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .hero-img {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
    }
</style>


@section('content')
    <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 pb-16">
        <nav class="mb-6 text-sm">
            <ol class="flex items-center space-x-2 text-gray-500">
                <li><a href="{{ route('blog.index') }}" class="hover:text-blue-600">Blog</a></li>
                <li><span class="mx-2">/</span></li>
                <li class="text-gray-900 font-medium">{{ e($blog->judul) }}</li>
            </ol>
        </nav>
        <header class="mb-8">
            <div class="flex items-center space-x-2 mb-4">
                <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full {{ getTypeBadge($blog->type) }}">
                    {{ ucfirst($blog->type) }}
                </span>
                <span class="text-gray-500 text-sm">{{ $blog->created_at->format('d F Y') }}</span>
            </div>

            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                {{ e($blog->judul) }}
            </h1>

            <div class="flex items-center text-gray-600 text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>{{ e($blog->creator->name ?? 'Admin') }}</span>
            </div>
        </header>


        @if ($blog->thumbnail)
            <div class="mb-8 rounded-lg overflow-hidden shadow-lg">
                <img src="{{ asset('storage/' . $blog->thumbnail) }}" alt="{{ e($blog->judul) }}" class="hero-img"
                    loading="eager">
            </div>
        @endif

        {{-- Content --}}
        {{-- dilengkapi dengan proooteksi xss ni boss KWOKWOOKO HEKER KETAR KETIR --}}
        <div class="prose prose-lg max-w-none blog-content">
            {!! clean($blog->content, [
                'HTML.Allowed' =>
                    'p,br,strong,em,u,a[href],ul,ol,li,h1,h2,h3,h4,h5,h6,img[src|alt|title],blockquote,code,pre,span[style]',
                'CSS.AllowedProperties' => 'color,background-color,font-size,font-weight,text-align',
                'AutoFormat.RemoveEmpty' => true,
                'AutoFormat.AutoParagraph' => true,
            ]) !!}
        </div>


        <div class="mt-12 pt-8 border-t border-gray-200">
            <h3 class="text-lg font-semibold mb-4">Bagikan artikel ini:</h3>
            <div class="flex space-x-3">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('read-blog.read', $blog->slug)) }}"
                    target="_blank" rel="noopener noreferrer"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                    </svg>
                    Facebook
                </a>

                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('read-blog.read', $blog->slug)) }}&text={{ urlencode($blog->judul) }}"
                    target="_blank" rel="noopener noreferrer"
                    class="inline-flex items-center px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                    </svg>
                    Twitter
                </a>

                <a href="https://wa.me/?text={{ urlencode($blog->judul . ' - ' . route('read-blog.read', $blog->slug)) }}"
                    target="_blank" rel="noopener noreferrer"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                    </svg>
                    WhatsApp
                </a>
            </div>
        </div>


        @if ($recommendations->count() > 0)
            <div class="mt-16 pt-8 border-t border-gray-200">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <span class="w-1 h-8 bg-blue-500 mr-3"></span>
                    Baca Juga
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($recommendations as $recommendation)
                        <a href="{{ route('read-blog.read', $recommendation->slug) }}" class="group">
                            <div
                                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                                <div class="relative overflow-hidden">
                                    <img src="{{ $recommendation->thumbnail ? asset('storage/' . $recommendation->thumbnail) : asset('images/default-blog.jpg') }}"
                                        alt="{{ e($recommendation->judul) }}"
                                        class="recommendation-img group-hover:scale-110 transition duration-300"
                                        loading="lazy">
                                </div>
                                <div class="p-4">
                                    <h3 class="font-semibold mb-2 group-hover:text-blue-600 transition line-clamp-2">
                                        {{ e($recommendation->judul) }}
                                    </h3>
                                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                        {{ e(Str::limit(strip_tags($recommendation->content), 100)) }}
                                    </p>
                                    <span
                                        class="text-xs text-gray-500">{{ $recommendation->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif


        <div class="mt-8 text-center">
            <a href="{{ route('read-blog.index') }}"
                class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Blog
            </a>
        </div>
    </article>
@endsection

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
