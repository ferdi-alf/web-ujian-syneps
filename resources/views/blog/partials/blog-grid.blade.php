@if (count($blogs) > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($blogs as $blog)
            <div
                class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden flex flex-col">


                <div class="relative h-48 overflow-hidden cursor-pointer bg-gray-100"
                    onclick="openDrawerWithData('drawer-blog-form', {
                        id: '{{ $blog['id'] }}',
                        fetchEndpoint: '/blog/{{ $blog['slug'] }}/show',
                        drawerTarget: 'drawer-blog-form',
                        type: 'slideOver',
                        title: '{{ addslashes($blog['judul']) }}',
                        description: 'Detail artikel blog'
                    })">
                    <div class="w-full h-full flex items-center justify-center">
                        <img src="{{ $blog['thumbnail'] }}" alt="{{ $blog['judul'] }}"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                            loading="lazy">
                    </div>


                    <div class="absolute top-3 right-3">
                        {!! $blog['type_badge'] !!}
                    </div>

                    @if ($blog['is_published'])
                        <div class="absolute top-3 left-3">
                            <span
                                class="bg-green-500 text-white text-xs px-2 py-1 rounded-full shadow-lg backdrop-blur-sm bg-opacity-90">
                                <i class="fa-solid fa-check-circle mr-1"></i>Published
                            </span>
                        </div>
                    @else
                        <div class="absolute top-3 left-3">
                            <span
                                class="bg-gray-500 text-white text-xs px-2 py-1 rounded-full shadow-lg backdrop-blur-sm bg-opacity-90">
                                <i class="fa-solid fa-file-circle-question mr-1"></i>Draft
                            </span>
                        </div>
                    @endif
                </div>
                <div class="p-5 flex-1 flex flex-col cursor-pointer"
                    onclick="openDrawerWithData('drawer-blog-form', {
                        id: '{{ $blog['id'] }}',
                        fetchEndpoint: '/blog/{{ $blog['slug'] }}/show',
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
                    <div class="flex items-center text-xs text-gray-500">
                        <i class="fa-solid fa-user mr-1"></i>
                        <span class="mr-3">{{ $blog['created_by'] }}</span>
                        <i class="fa-solid fa-calendar mr-1"></i>
                        <span>{{ $blog['created_at'] }}</span>
                    </div>
                </div>
                <div class="px-5 pb-4 flex gap-2 border-t border-gray-100 pt-3">
                    <a href="{{ route('blog.show', ['slug' => $blog['slug'], 'act' => 'edit']) }}"
                        onclick="event.stopPropagation();"
                        class="flex-1 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200 text-sm font-medium text-center inline-flex items-center justify-center">
                        <i class="fa-solid fa-pen mr-2"></i>Edit
                    </a>
                    <button
                        onclick="
                            event.stopPropagation();
                            confirmDelete('{{ route('blog.destroy', $blog['id']) }}', '{{ $blog['judul'] }}')"
                        class="flex-1 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors duration-200 text-sm font-medium inline-flex items-center justify-center">
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

<style>
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

    .object-cover {
        object-fit: cover;
        object-position: center;
    }

    .shadow-md {
        transition: box-shadow 0.3s ease;
    }
</style>
