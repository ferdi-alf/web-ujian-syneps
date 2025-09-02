@extends('layouts.dashboard-layouts')

@section('content')
<!-- Add meta tags for user info -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="user-avatar" content="{{ auth()->user()->avatar }}">
<meta name="user-name" content="{{ auth()->user()->nama_lengkap ?? auth()->user()->name }}">

<!-- Instagram-style heart animations -->
<style>
.like-heart {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    transition: transform 0.2s ease;
}

.like-heart:hover {
    transform: scale(1.1);
}

.like-heart.liked {
    animation: heartBeat 0.5s ease-in-out;
}

@keyframes heartBeat {
    0% { transform: scale(1); }
    25% { transform: scale(1.2); }
    50% { transform: scale(1.1); }
    75% { transform: scale(1.15); }
    100% { transform: scale(1); }
}

@keyframes heartPop {
    0% { transform: scale(1); }
    50% { transform: scale(1.3); }
    100% { transform: scale(1); }
}

/* Instagram-style like button */
.like-heart {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.like-heart:active {
    transform: scale(0.9);
}
</style>

<div class="max-w-4xl mx-auto p-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-100">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Forum Alumni</h1>
        <p class="text-gray-600">Berbagi cerita dan pengalaman dengan sesama alumni</p>
    </div>
    
    <!-- Success Notification -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Create Post Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-start space-x-4">
            <img src="{{ asset('images/avatar/' . Auth::user()->avatar) }}" alt="Avatar" 
                 class="w-10 h-10 rounded-full border border-gray-200">
            <div class="flex-1">
                <button onclick="openPostModal()" 
                        class="w-full text-left p-4 bg-gray-50 rounded-full text-gray-500 hover:bg-gray-100 transition duration-200">
                    Bagikan cerita atau pencapaianmu...
                </button>
            </div>
        </div>
    </div>

    <!-- Posts Feed -->
    <div id="posts-container">
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
                                <p class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
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
                @if($post->media_path)
                    <div class="px-6 pb-4">
                        @if($post->media_type === 'image')
                            <div class="relative overflow-hidden rounded-lg bg-gray-100 max-w-lg mx-auto cursor-pointer" onclick="openPostDetailModal({{ $post->id }})">
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
                        <button onclick="sharePost({{ $post->id }})" 
                                class="flex items-center space-x-2 transition duration-200 text-gray-600 hover:text-gray-800">
                            <i class="far fa-paper-plane text-2xl"></i>
                        </button>
                    </div>
                    
                    <div class="text-sm">
                        <div class="likes-count font-semibold mb-1">{{ $post->likes_count }} suka</div>
                        @if($post->content)
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
                        @foreach($post->comments as $comment)
                            @include('components.comment', ['comment' => $comment, 'post_id' => $post->id])
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada postingan</h3>
                <p class="text-gray-500">Jadilah yang pertama berbagi cerita di forum ini!</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($posts->hasPages())
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @endif
</div>

<!-- Post Creation Modal -->
<div id="postModal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/30 bg-opacity-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-auto border-0 overflow-hidden max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between flex-shrink-0">
            <button onclick="closePostModal()" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-lg"></i>
            </button>
            <h2 class="text-base font-semibold text-gray-900">Create new post</h2>
            <button type="button" onclick="handleSharePost(event)" class="text-blue-500 hover:text-blue-600 font-semibold text-sm">
                Share
            </button>
        </div>

        <!-- Modal Content -->
        <div class="flex-1 overflow-y-auto">
            <form id="createPostForm" action="{{ route('forum-alumni.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-4">
                <!-- User Info -->
                <div class="flex items-center space-x-3 mb-4">
                    <img src="{{ asset('images/avatar/' . Auth::user()->avatar) }}" alt="Avatar" 
                         class="w-8 h-8 rounded-full">
                    <span class="font-medium text-sm text-gray-900">
                        {{ Auth::user()->nama_lengkap ?? Auth::user()->name }}
                    </span>
                </div>

                    <!-- Post Content -->
                    <textarea name="content" id="postContent" rows="6" 
                              class="w-full p-3 border-0 resize-none focus:outline-none text-sm placeholder-gray-500" 
                              placeholder="Tulis cerita atau pencapaianmu..." required></textarea>

                    <!-- Media Preview -->
                    <div id="mediaPreview" class="hidden mt-4">
                        <div class="relative">
                            <img id="imagePreview" class="w-full max-h-60 object-contain rounded-lg hidden bg-gray-50">
                            <video id="videoPreview" controls class="w-full max-h-60 rounded-lg hidden">
                                <source id="videoSource" type="video/mp4">
                            </video>
                            <button type="button" onclick="removeMedia()" 
                                    class="absolute top-2 right-2 bg-black bg-opacity-70 text-white rounded-md w-6 h-6 flex items-center justify-center hover:bg-opacity-90 transition-all duration-200">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Media Upload Button -->
                    <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                        <label class="cursor-pointer flex items-center space-x-2 text-gray-600 hover:text-gray-800">
                            <i class="fas fa-image text-lg"></i>
                            <span class="text-sm">Add photos/videos</span>
                            <input type="file" name="media" accept="image/*,video/*" class="hidden" onchange="previewMedia(this)">
                        </label>
                        <div class="text-xs text-gray-400">
                            <span id="charCount">0</span>/2,200
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Post Detail Modal -->
<div id="postDetailModal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/30 bg-opacity-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-auto border-0 overflow-hidden max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between flex-shrink-0">
            <button onclick="closePostDetailModal()" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-lg"></i>
            </button>
            <h2 class="text-base font-semibold text-gray-900">Post Details</h2>
            <div class="w-6"></div> <!-- Spacer -->
        </div>

        <!-- Modal Content -->
        <div class="flex-1 overflow-y-auto">
            <div id="postDetailContent" class="p-4">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Instagram-style Comment Modal -->
<div id="commentModal" class="fixed inset-0 z-[1000] hidden bg-black/30 bg-opacity-50 md:items-center md:justify-center">
    <div class="bg-white h-full w-full md:rounded-lg md:shadow-2xl md:w-full md:max-w-4xl md:mx-4 md:max-h-[90vh] md:h-auto overflow-hidden flex md:flex-row flex-col">
        <!-- Post Content Section - Hidden on mobile -->
        <div class="hidden md:flex md:w-3/5 bg-black items-center justify-center">
            <div id="modalPostContent" class="w-full h-full flex items-center justify-center">
                <!-- Post image/video will be loaded here -->
                <img id="modalPostImage" class="max-w-full max-h-full object-contain hidden">
                <video id="modalPostVideo" controls class="max-w-full max-h-full hidden">
                    <source id="modalVideoSource" type="video/mp4">
                </video>
                <!-- Fallback for text-only posts -->
                <div id="modalTextPost" class="hidden p-8 text-white text-center">
                    <div class="bg-gray-800 rounded-lg p-6">
                        <p id="modalPostText" class="text-lg"></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Comments Section -->
        <div class="w-full md:w-2/5 flex flex-col h-full">
            <!-- Modal Header -->
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-white sticky top-0 z-10">
                <!-- Mobile: Instagram-style header -->
                <div class="md:hidden flex items-center space-x-3">
                    <button onclick="closeCommentModal()" class="text-gray-900 hover:text-gray-700">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </button>
                    <span class="font-semibold text-base text-gray-900">Komentar</span>
                </div>
                
                <!-- Desktop: Original header -->
                <div class="hidden md:flex items-center space-x-3">
                    <img id="modalUserAvatar" class="w-8 h-8 rounded-full">
                    <span id="modalUserName" class="font-medium text-sm text-gray-900"></span>
                </div>
                <button onclick="closeCommentModal()" class="hidden md:block text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <!-- Post Details - Hidden on mobile -->
            <div class="hidden md:block px-4 py-3 border-b border-gray-100">
                <div class="flex items-start space-x-3">
                    <img id="modalUserAvatarLarge" class="w-8 h-8 rounded-full">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <span id="modalUserNameLarge" class="font-medium text-sm"></span>
                            <span id="modalPostTime" class="text-xs text-gray-500"></span>
                        </div>
                        <p id="modalPostDescription" class="text-sm text-gray-800 mt-1"></p>
                    </div>
                </div>
            </div>
            
            <!-- Comments List -->
            <div id="modalCommentsList" class="flex-1 overflow-y-auto px-4 py-2 bg-white">
                <!-- Comments will be loaded here -->
            </div>
            
            <!-- Add Comment Form - Sticky bottom on mobile -->
            <div class="px-4 py-3 border-t border-gray-100 bg-white sticky bottom-0 z-10">
                <form id="modalCommentForm" class="flex items-center space-x-3">
                    <input type="hidden" id="modalPostId" value="">
                    <input type="text" id="modalCommentInput" placeholder="Tambahkan komentar..." 
                           class="flex-1 py-3 px-4 bg-gray-50 rounded-full border-none focus:outline-none text-sm focus:bg-gray-100 transition-colors">
                    <button type="submit" class="text-blue-500 font-semibold text-sm hover:text-blue-600 px-2">
                        Kirim
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Instagram-style Forum Styling */
.post-card {
    transition: all 0.2s ease;
    border: 1px solid #e2e8f0;
}
.post-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Like button styling (Instagram red heart) */
.like-btn {
    transition: all 0.2s ease;
}
.like-btn:hover {
    transform: scale(1.1);
}
.like-btn.text-red-500 i {
    animation: likeAnimation 0.3s ease;
}

@keyframes likeAnimation {
    0% { transform: scale(1); }
    50% { transform: scale(1.3); }
    100% { transform: scale(1); }
}

/* Comment section styling */
.comments-section {
    background-color: #fafafa;
}
.comment-item, .reply-item {
    transition: background-color 0.2s ease;
}
.comment-item:hover, .reply-item:hover {
    background-color: rgba(0, 0, 0, 0.02);
    border-radius: 8px;
    padding: 8px;
    margin: -8px;
}

/* Input styling */
input[type="text"], textarea {
    border: none !important;
    outline: none !important;
    background: transparent !important;
}
input[type="text"]:focus, textarea:focus {
    border: none !important;
    box-shadow: none !important;
}

/* Avatar styling */
img[alt="Avatar"] {
    object-fit: cover;
    border: 1px solid #e2e8f0;
}

/* Media hover effects */
.post-card img[alt="Post Image"] {
    transition: transform 0.3s ease;
}
.post-card img[alt="Post Image"]:hover {
    transform: scale(1.02);
}

/* Button hover effects */
button {
    transition: all 0.2s ease;
}
button:hover {
    transform: translateY(-1px);
}

/* Notification styling */
.notification {
    backdrop-filter: blur(10px);
}

/* Reply form styling */
.reply-form {
    background-color: rgba(255, 255, 255, 0.8);
    border-radius: 8px;
    padding: 8px;
}

/* Scrollbar styling */
.comments-list::-webkit-scrollbar {
    width: 4px;
}
.comments-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}
.comments-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}
.comments-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Create post section */
.create-post-btn {
    background: linear-gradient(45deg, #f093fb 0%, #f5576c 50%, #4facfe 100%);
    transition: all 0.3s ease;
}
.create-post-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}
</style>
@endpush

@push('scripts')
<script>
    // Current user data for JavaScript
    window.currentUser = {
        id: {{ auth()->id() }},
        name: "{{ auth()->user()->nama_lengkap ?? auth()->user()->name }}",
        avatar: "{{ auth()->user()->avatar ?? 'default.jpg' }}"
    };

    // Check for success message and show SweetAlert
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            // Hide default alert
            const defaultAlert = document.querySelector('.alert-success');
            if (defaultAlert) {
                defaultAlert.style.display = 'none';
            }
            
            // Show SweetAlert instead
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        });
    @endif
</script>
<script src="{{ asset('js/forum-instagram.js') }}"></script>
@endpush
@endsection
