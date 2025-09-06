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

    <div class="max-w-4xl mx-auto p-6 pb-24">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-100">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">
                @if(auth()->user()->role === 'admin')
                    Forum Alumni
                @elseif(auth()->user()->role === 'pengajar')
                    Forum Pengajar
                @else
                    Forum Alumni
                @endif
            </h1>
            <p class="text-gray-600">
                @if(auth()->user()->role === 'admin')
                    Berbagi informasi dan diskusi dengan sesama administrator
                @elseif(auth()->user()->role === 'pengajar')
                    Berbagi informasi dan diskusi dengan sesama pengajar
                @else
                    Berbagi cerita dan pengalaman dengan sesama alumni
                @endif
            </p>
        </div>

    <!-- Success Notification -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif


        <!-- Posts Feed -->
        <div id="posts-container">
            @include('components.post-list', ['posts' => $posts])
        </div>

        <!-- Loading Indicator -->
        <div id="loading-indicator" class="hidden bg-white rounded-lg shadow-sm p-8 text-center mb-6">
            <div class="flex flex-col items-center space-y-3">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                <p class="text-sm text-gray-500">Memuat postingan...</p>
            </div>
        </div>

        <!-- End of Posts Indicator -->
        <div id="end-of-posts" class="hidden bg-white rounded-lg shadow-sm p-8 text-center mb-6">
            <div class="flex flex-col items-center space-y-3">
                <i class="fas fa-check-circle text-3xl text-green-500"></i>
                <p class="text-sm text-gray-500">Anda telah melihat semua postingan</p>
            </div>
        </div>
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
            <button type="button" onclick="handleSharePost(event)" id="shareButton" class="text-gray-400 font-semibold text-sm cursor-not-allowed" disabled>
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
                            placeholder="@if(auth()->user()->role === 'admin')Tulis pengumuman atau informasi...@elseif(auth()->user()->role === 'pengajar')Bagikan tips atau motivasi untuk pengajar...@else Tulis cerita atau pencapaianmu...@endif" required></textarea>

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
                            <input type="file" name="media" accept="image/*,video/*" class="hidden" onchange="previewMedia(this)" required>
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
    <div id="commentModal"
        class="fixed inset-0 z-[1000] hidden bg-black/30 bg-opacity-50 md:items-center md:justify-center">
        <div
            class="bg-white h-full w-full md:rounded-lg md:shadow-2xl md:w-full md:max-w-4xl md:mx-4 md:max-h-[90vh] md:h-auto overflow-hidden flex md:flex-row flex-col">
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
                <div
                    class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-white sticky top-0 z-10">
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
                    <!-- Loading State -->
                    <div id="commentsLoading" class="flex items-center justify-center h-40">
                        <div class="flex flex-col items-center space-y-3">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                            <p class="text-sm text-gray-500">Memuat komentar...</p>
                        </div>
                    </div>
                    <!-- Comments content -->
                    <div id="commentsContent" class="hidden">
                        <!-- Comments will be loaded here -->
                    </div>
                </div>

                <!-- Add Comment Form - Sticky bottom on mobile -->
                <div class="px-4 py-3 border-t border-gray-100 bg-white sticky bottom-0 z-10">
                    <form id="modalCommentForm" class="flex items-center space-x-3">
                        <input type="hidden" id="modalPostId" value="">
                        <!-- User Avatar -->
                        <img src="{{ asset('images/avatar/' . Auth::user()->avatar) }}" alt="Avatar"
                            class="w-8 h-8 rounded-full flex-shrink-0 border border-gray-200">
                        <!-- Comment Input -->
                        <input type="text" id="modalCommentInput" placeholder="Tambahkan komentar..."
                            class="flex-1 py-3 px-4 bg-gray-50 rounded-full border-none focus:outline-none text-sm focus:bg-gray-100 transition-colors">
                        <!-- Submit Button -->
                        <button type="submit" class="text-blue-500 font-semibold text-sm hover:text-blue-600 px-3 py-2 min-w-[60px]">
                            Kirim
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Comment Drawer for Mobile -->
    <div id="commentDrawer-overlay" onclick="handleCommentDrawerOverlay(event)"
        class="fixed inset-0 bg-black/50 z-50 opacity-0 pointer-events-none transition-opacity duration-300">
        <div id="commentDrawer-drawer"
            class="fixed bottom-0 left-0 right-0 h-[90vh] bg-white rounded-t-2xl shadow-2xl transform translate-y-full transition-transform duration-300 ease-out flex flex-col"
            onclick="event.stopPropagation()">

            <!-- Drag Handle -->
            <div class="flex justify-center pt-3 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
            </div>

            <!-- Drawer Header -->
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <button onclick="closeCommentDrawer()" class="text-gray-900 hover:text-gray-700">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </button>
                    <span class="font-semibold text-base text-gray-900">Komentar</span>
                </div>
            </div>

            <!-- Comments List -->
            <div id="drawerCommentsList" class="flex-1 overflow-y-auto px-4 py-2">
                <!-- Loading State -->
                <div id="drawerCommentsLoading" class="flex items-center justify-center h-40">
                    <div class="flex flex-col items-center space-y-3">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                        <p class="text-sm text-gray-500">Memuat komentar...</p>
                    </div>
                </div>
                <!-- Comments content -->
                <div id="drawerCommentsContent" class="hidden">
                    <!-- Comments will be loaded here -->
                </div>
            </div>

            <!-- Comment Input - Fixed at Bottom -->
            <div class="px-4 py-3 border-t border-gray-100 bg-white">
                <form id="drawerCommentForm" class="flex items-center space-x-3">
                    <input type="hidden" id="drawerPostId" value="">
                    <!-- User Avatar -->
                    <img src="{{ asset('images/avatar/' . Auth::user()->avatar) }}" alt="Avatar"
                        class="w-8 h-8 rounded-full flex-shrink-0 border border-gray-200">
                    <!-- Comment Input -->
                    <input type="text" id="drawerCommentInput" placeholder="Tambahkan komentar..."
                        class="flex-1 py-3 px-4 bg-gray-50 rounded-full border-none focus:outline-none text-sm focus:bg-gray-100 transition-colors">
                    <!-- Submit Button -->
                    <button type="submit" class="text-blue-500 font-semibold text-sm hover:text-blue-600 px-3 py-2 min-w-[60px]">
                        Kirim
                    </button>
                </form>
            </div>
        </div>
    </div>

        <!-- Fixed Bottom Create Post Section -->
        <div class="fixed bottom-4 bg-white rounded-lg shadow-lg border border-gray-200 p-6 z-30 md:z-50" style="width: calc(100% - 3rem); max-width: 1000px;">
            <div class="flex items-start space-x-4">
                <img src="{{ asset('images/avatar/' . Auth::user()->avatar) }}" alt="Avatar"
                    class="w-10 h-10 rounded-full border border-gray-200">
                <div class="flex-1">
                    <button onclick="openPostModal()" 
                        class="w-full text-left p-4 bg-gray-50 rounded-full text-gray-500 hover:bg-gray-100 transition duration-200 shadow-sm">
                        @if(auth()->user()->role === 'admin')
                            Buat pengumuman untuk alumni...
                        @elseif(auth()->user()->role === 'pengajar')
                            Bagikan tips atau motivasi untuk pengajar lain...
                        @else
                            Bagikan cerita atau pencapaianmu...
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .post-card {
                transition: transform 0.2s ease;
            }

            .post-card:hover {
                transform: translateY(-1px);
            }

            .like-btn.liked {
                color: #3b82f6 !important;
            }

            .like-btn.liked i {
                color: #3b82f6;
            }
            
            /* Drawer specific styles */
            #commentDrawer-drawer {
                z-index: 60;
            }
            
            #commentDrawer-overlay {
                z-index: 55;
            }
            
            /* Ensure drawer appears above other elements on mobile */
            @media (max-width: 767px) {
                #commentDrawer-drawer {
                    z-index: 9999;
                }
                
                #commentDrawer-overlay {
                    z-index: 9998;
                }
            }
            
            /* User Profile Improvements */
            .comment-container img[alt] {
                transition: transform 0.2s ease;
            }
            
            .comment-container:hover img[alt] {
                transform: scale(1.05);
            }
            
            .reply-button {
                transition: all 0.2s ease;
            }
            
            .reply-button:hover {
                color: #3B82F6 !important;
            }
            
            /* Comment input styling */
            #drawerCommentForm img,
            #modalCommentForm img {
                transition: transform 0.2s ease;
            }
            
            #drawerCommentForm:focus-within img,
            #modalCommentForm:focus-within img {
                transform: scale(1.1);
            }
            
            /* Infinite scroll animations */
            .post-card {
                transition: opacity 0.5s ease, transform 0.5s ease;
            }
            
            #loading-indicator {
                transition: opacity 0.3s ease;
            }
            
            #end-of-posts {
                transition: opacity 0.5s ease;
            }
            
            /* Smooth scroll behavior */
            html {
                scroll-behavior: smooth;
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
