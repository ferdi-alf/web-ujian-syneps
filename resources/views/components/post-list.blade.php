@forelse ($posts as $post)
    <div class="bg-white rounded-lg shadow-sm mb-6 post-card" data-post-id="{{ $post->id }}">
        <!-- Post Header -->
        <div class="p-6 pb-4">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/avatar/' . $post->user->avatar) }}" alt="Avatar"
                        class="w-12 h-12 rounded-full border border-gray-200">
                    <div>
                        <h3 class="font-semibold text-gray-900">
                            {{ $post->user->nama_lengkap ?? $post->user->name }}
                        </h3>
                        <div class="flex items-center space-x-2">
                            <p class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                            @if(auth()->user()->role === 'admin')
                                <span class="px-2 py-1 text-xs rounded-full 
                                {{ $post->user->role === 'admin' 
                                    ? 'bg-red-100 text-red-800' 
                                    : ($post->user->role === 'pengajar' 
                                        ? 'bg-blue-100 text-blue-800' 
                                        : 'bg-green-100 text-green-800') }}">
                                    {{ ucfirst($post->user->role) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @if($post->user_id === auth()->id() || auth()->user()->role === 'admin')
                    <button onclick="deletePost({{ $post->id }})"
                        class="text-gray-400 hover:text-red-500 transition duration-200">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                @endif
            </div>
        </div>

        <!-- Post Content -->
        <div class="px-6 pb-4">
            <p class="text-gray-800 leading-relaxed">{{ $post->content }}</p>
        </div>

        <!-- Post Media -->
        @if ($post->media_path)
            <div class="px-6 pb-4">
                @if ($post->media_type === 'image')
                    <div class="relative overflow-hidden rounded-lg bg-gray-100 max-w-lg mx-auto cursor-pointer"
                        onclick="openPostDetailModal({{ $post->id }})">
                        <img src="{{ asset('storage/' . $post->media_path) }}" alt="Post Image"
                            class="w-full h-auto max-h-96 object-contain hover:scale-105 transition-transform duration-200">
                    </div>
                @elseif($post->media_type === 'video')
                    <div class="relative overflow-hidden rounded-lg bg-gray-100 max-w-lg mx-auto">
                        <video controls class="w-full h-auto max-h-96 rounded-lg">
                            <source src="{{ asset('storage/' . $post->media_path) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @endif
            </div>
        @endif

        <!-- Post Actions -->
        <div class="px-6 py-4 border-t border-gray-100">
            <div class="flex items-center space-x-6 mb-4">
                <button onclick="toggleLike({{ $post->id }})"
                    class="like-btn flex items-center space-x-2 transition duration-200 {{ $post->isLikedBy(auth()->id()) ? 'text-red-500' : 'text-gray-600' }}">
                    <i class="{{ $post->isLikedBy(auth()->id()) ? 'fas' : 'far' }} fa-heart text-2xl"></i>
                </button>
                <button onclick="openCommentModal({{ $post->id }})"
                    class="flex items-center space-x-2 transition duration-200 text-gray-600 hover:text-gray-800">
                    <i class="far fa-comment text-2xl"></i>
                </button>
            </div>

            <div class="text-sm">
                <div class="likes-count font-semibold mb-1">{{ $post->likes_count }} suka</div>
                @if ($post->content)
                    <div class="mb-2">
                        <span class="font-semibold">{{ $post->user->nama_lengkap ?? $post->user->name }}</span>
                        <span class="ml-1">{{ $post->content }}</span>
                    </div>
                @endif
                <button onclick="openCommentModal({{ $post->id }})"
                    class="comments-count text-gray-500 hover:text-gray-700 mb-2">
                    Lihat semua {{ $post->comments_count }} komentar
                </button>
                <div class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</div>
            </div>
        </div>

        <!-- Comments Section -->
        <div id="comments-{{ $post->id }}" class="comments-section hidden border-t border-gray-100">
            <!-- Add Comment Form -->
            <div class="px-6 py-3 border-b border-gray-100">
                <form onsubmit="addComment(event, {{ $post->id }})" class="flex items-center space-x-3">
                    <img src="{{ asset('images/avatar/' . Auth::user()->avatar) }}" alt="Avatar"
                        class="w-8 h-8 rounded-full">
                    <input type="text" name="content" placeholder="Tambahkan komentar..."
                        class="flex-1 py-2 px-0 border-none focus:outline-none text-sm" required>
                    <button type="submit" class="text-blue-500 font-semibold text-sm hover:text-blue-600">
                        Kirim
                    </button>
                </form>
            </div>

            <!-- Comments List -->
            <div class="comments-list px-6 pb-4 max-h-96 overflow-y-auto">
                @foreach ($post->comments as $comment)
                    @include('components.comment', [
                        'comment' => $comment,
                        'post_id' => $post->id,
                    ])
                @endforeach
            </div>
        </div>
    </div>
@empty
    @if(!isset($is_loading))
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada postingan</h3>
            <p class="text-gray-500">
                @if(auth()->user()->role === 'admin')
                    Mulai percakapan dengan membuat postingan pertama!
                @elseif(auth()->user()->role === 'pengajar')
                    Mulai berbagi tips dan motivasi untuk alumni!
                @else
                    Jadilah yang pertama berbagi cerita di forum ini!
                @endif
            </p>
        </div>
    @endif
@endforelse