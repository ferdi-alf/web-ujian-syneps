// Instagram-Style Forum JavaScript Functions

// Post Modal Functions
function openPostModal() {
    const modal = document.getElementById('postModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        const postContent = document.getElementById('postContent');
        if (postContent) {
            setTimeout(() => postContent.focus(), 100);
        }
    }
}

function closePostModal() {
    const modal = document.getElementById('postModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
    
    const form = document.getElementById('createPostForm');
    if (form) form.reset();
    
    const charCount = document.getElementById('charCount');
    if (charCount) charCount.textContent = '0';
    
    removeMedia();
}

// Handle Share Button Click - Submit post
function handleSharePost(event) {
    event.preventDefault();
    
    const form = document.getElementById('createPostForm');
    const postContent = document.getElementById('postContent');
    
    // Validate content
    if (!postContent.value.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Konten Kosong',
            text: 'Silakan tulis sesuatu sebelum membagikan post!'
        });
        return;
    }
    
    // Submit form traditionally (not AJAX) since backend expects redirect
    form.submit();
}

// Media Preview Functions
function previewMedia(input) {
    const file = input.files[0];
    if (!file) return;
    
    const mediaPreview = document.getElementById('mediaPreview');
    const imagePreview = document.getElementById('imagePreview');
    const videoPreview = document.getElementById('videoPreview');
    const videoSource = document.getElementById('videoSource');
    
    if (!mediaPreview || !imagePreview || !videoPreview) return;
    
    // Hide both previews first
    imagePreview.classList.add('hidden');
    videoPreview.classList.add('hidden');
    
    const fileType = file.type;
    const reader = new FileReader();
    
    reader.onload = function(e) {
        if (fileType.startsWith('image/')) {
            imagePreview.src = e.target.result;
            imagePreview.classList.remove('hidden');
        } else if (fileType.startsWith('video/')) {
            videoSource.src = e.target.result;
            videoPreview.load();
            videoPreview.classList.remove('hidden');
        }
        mediaPreview.classList.remove('hidden');
    };
    
    reader.readAsDataURL(file);
}

function removeMedia() {
    const mediaPreview = document.getElementById('mediaPreview');
    const imagePreview = document.getElementById('imagePreview');
    const videoPreview = document.getElementById('videoPreview');
    
    if (mediaPreview) mediaPreview.classList.add('hidden');
    if (imagePreview) {
        imagePreview.classList.add('hidden');
        imagePreview.src = '';
    }
    if (videoPreview) {
        videoPreview.classList.add('hidden');
        videoPreview.src = '';
    }
    
    // Clear all file inputs with name="media"
    document.querySelectorAll('input[type="file"][name="media"]').forEach(input => {
        input.value = '';
    });
}

// Like Functionality
async function toggleLike(postId) {
    try {
        const response = await fetch(`/forum-alumni/${postId}/like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            const postCard = document.querySelector(`[data-post-id="${postId}"]`);
            const likeBtn = postCard.querySelector('.like-btn');
            const likesCount = postCard.querySelector('.likes-count');
            
            // Update like button state (Instagram style)
            if (data.liked) {
                likeBtn.classList.add('text-red-500');
                likeBtn.classList.remove('text-gray-600');
                likeBtn.querySelector('i').classList.remove('far');
                likeBtn.querySelector('i').classList.add('fas');
            } else {
                likeBtn.classList.remove('text-red-500');
                likeBtn.classList.add('text-gray-600');
                likeBtn.querySelector('i').classList.remove('fas');
                likeBtn.querySelector('i').classList.add('far');
            }
            
            // Update likes count
            likesCount.textContent = `${data.likes_count} suka`;
        }
    } catch (error) {
        console.error('Error toggling like:', error);
    }
}

// Comments Functionality
function toggleComments(postId) {
    const commentsSection = document.getElementById(`comments-${postId}`);
    commentsSection.classList.toggle('hidden');
}

async function addComment(event, postId) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    try {
        const response = await fetch(`/forum-alumni/${postId}/comment`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (response.ok) {
            const postCard = document.querySelector(`[data-post-id="${postId}"]`);
            const commentsList = postCard.querySelector('.comments-list');
            const commentsCount = postCard.querySelector('.comments-count');
            
            // Create new comment HTML with Instagram style
            const newComment = document.createElement('div');
            newComment.className = 'comment-item mb-3';
            newComment.setAttribute('data-comment-id', data.comment.id);
            
            const currentUserAvatar = document.querySelector('meta[name="user-avatar"]')?.getAttribute('content') || 'default.png';
            const currentUserName = document.querySelector('meta[name="user-name"]')?.getAttribute('content') || 'User';
            
            newComment.innerHTML = `
                <div class="flex space-x-3">
                    <img src="/images/avatar/${currentUserAvatar}" alt="Avatar" 
                         class="w-8 h-8 rounded-full flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="text-sm">
                                    <span class="font-semibold">${currentUserName}</span>
                                    <span class="ml-1">${data.comment.content}</span>
                                </div>
                                <div class="flex items-center space-x-4 mt-1 text-xs text-gray-500">
                                    <span>Baru saja</span>
                                    <button onclick="toggleReplyForm(${data.comment.id})" class="font-semibold hover:text-gray-700">
                                        Balas
                                    </button>
                                    <button onclick="deleteComment(${data.comment.id})" class="hover:text-red-500">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="reply-form-${data.comment.id}" class="reply-form hidden mt-3">
                            <form onsubmit="addReply(event, ${postId}, ${data.comment.id})" class="flex items-center space-x-3">
                                <img src="/images/avatar/${currentUserAvatar}" alt="Avatar" class="w-6 h-6 rounded-full">
                                <input type="text" name="content" placeholder="Tulis balasan..." 
                                       class="flex-1 py-1 px-0 border-none focus:outline-none text-sm" required>
                                <button type="submit" class="text-blue-500 font-semibold text-xs hover:text-blue-600">Kirim</button>
                                <button type="button" onclick="toggleReplyForm(${data.comment.id})" 
                                        class="text-gray-500 font-semibold text-xs hover:text-gray-700">Batal</button>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            
            // Add to comments list
            commentsList.appendChild(newComment);
            
            // Update comments count
            commentsCount.textContent = `Lihat semua ${data.comments_count} komentar`;
            
            // Clear form
            form.reset();
        }
    } catch (error) {
        console.error('Error adding comment:', error);
    }
}

// Instagram-like Reply Functions
function toggleReplyForm(commentId) {
    const replyForm = document.getElementById(`reply-form-${commentId}`);
    if (replyForm) {
        replyForm.classList.toggle('hidden');
        if (!replyForm.classList.contains('hidden')) {
            const input = replyForm.querySelector('input[name="content"]');
            if (input) {
                setTimeout(() => input.focus(), 100);
            }
        }
    }
}

async function addReply(event, postId, parentId) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    formData.append('parent_id', parentId);
    
    try {
        const response = await fetch(`/forum-alumni/${postId}/comment`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (response.ok) {
            const parentComment = document.querySelector(`[data-comment-id="${parentId}"]`);
            let repliesContainer = parentComment.querySelector('.replies');
            
            if (!repliesContainer) {
                repliesContainer = document.createElement('div');
                repliesContainer.className = 'replies mt-3 pl-4 border-l-2 border-gray-100';
                parentComment.querySelector('.flex-1').appendChild(repliesContainer);
            }
            
            const currentUserAvatar = document.querySelector('meta[name="user-avatar"]')?.getAttribute('content') || 'default.png';
            const currentUserName = document.querySelector('meta[name="user-name"]')?.getAttribute('content') || 'User';
            
            const newReply = document.createElement('div');
            newReply.className = 'reply-item flex space-x-2 mb-2';
            newReply.setAttribute('data-comment-id', data.comment.id);
            
            newReply.innerHTML = `
                <img src="/images/avatar/${currentUserAvatar}" alt="Avatar" 
                     class="w-6 h-6 rounded-full flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <div class="text-sm">
                        <span class="font-semibold">${currentUserName}</span>
                        <span class="ml-1">${data.comment.content}</span>
                    </div>
                    <div class="flex items-center space-x-4 mt-1 text-xs text-gray-500">
                        <span>Baru saja</span>
                        <button onclick="toggleReplyForm(${parentId})" class="font-semibold hover:text-gray-700">
                            Balas
                        </button>
                        <button onclick="deleteComment(${data.comment.id})" class="hover:text-red-500">
                            Hapus
                        </button>
                    </div>
                </div>
            `;
            
            repliesContainer.appendChild(newReply);
            
            // Update comments count in post
            const postCard = document.querySelector(`[data-post-id="${postId}"]`);
            const commentsCount = postCard.querySelector('.comments-count');
            commentsCount.textContent = `Lihat semua ${data.comments_count} komentar`;
            
            // Clear and hide form
            form.reset();
            toggleReplyForm(parentId);
        }
    } catch (error) {
        console.error('Error adding reply:', error);
    }
}

// Delete Functions
async function deletePost(postId) {
    // SweetAlert confirmation dialog like admin job posting page
    const result = await Swal.fire({
        title: 'Hapus Postingan',
        text: 'Apakah Anda yakin ingin menghapus postingan ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;
    
    try {
        const response = await fetch(`/forum-alumni/post/${postId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            const postCard = document.querySelector(`[data-post-id="${postId}"]`);
            postCard.remove();
            
            // Success alert
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Postingan berhasil dihapus!',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.error || 'Gagal menghapus postingan'
            });
        }
    } catch (error) {
        console.error('Error deleting post:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menghapus postingan'
        });
    }
}

async function deleteComment(commentId) {
    // SweetAlert confirmation dialog like admin job posting page
    const result = await Swal.fire({
        title: 'Hapus Komentar',
        text: 'Apakah Anda yakin ingin menghapus komentar ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;
    
    try {
        const response = await fetch(`/forum-alumni/comment/${commentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            const commentItem = document.querySelector(`[data-comment-id="${commentId}"]`);
            const postCard = commentItem.closest('.post-card');
            const commentsCount = postCard.querySelector('.comments-count');
            
            commentItem.remove();
            
            // Update comments count (decrease by 1)
            const currentCountText = commentsCount.textContent;
            const currentCount = parseInt(currentCountText.match(/\d+/)[0]);
            commentsCount.textContent = `Lihat semua ${currentCount - 1} komentar`;
            
            // Success alert
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Komentar berhasil dihapus!',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.error || 'Gagal menghapus komentar'
            });
        }
    } catch (error) {
        console.error('Error deleting comment:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menghapus komentar'
        });
    }
}

// Image Modal Functions
function openImageModal(imageSrc) {
    // Create modal if it doesn't exist
    let modal = document.getElementById('imageModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'imageModal';
        modal.className = 'fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="relative bg-white rounded-lg shadow-2xl max-w-3xl max-h-[90vh] overflow-hidden">
                <div class="relative">
                    <img id="modalImage" class="w-full h-auto max-h-[80vh] object-contain" alt="Full size image">
                    <button onclick="closeImageModal()" 
                            class="absolute top-3 right-3 bg-gray-800 bg-opacity-70 text-white hover:bg-opacity-90 rounded-full w-8 h-8 flex items-center justify-center transition-all duration-200">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Close on background click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeImageModal();
            }
        });
        
        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeImageModal();
            }
        });
    }
    
    document.getElementById('modalImage').src = imageSrc;
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = ''; // Restore background scrolling
    }
}

// Post Detail Modal Functions
function openPostDetailModal(postId) {
    console.log('Opening post detail modal for post ID:', postId);
    
    // Detect current forum type from URL
    let apiEndpoint;
    if (window.location.pathname.includes('Alumni-Forum') || 
        window.location.pathname.includes('Forum-Alumni') || 
        window.location.pathname.includes('Pengajar-Forum')) {
        apiEndpoint = `/forum-alumni/post/${postId}`;
    } else {
        apiEndpoint = `/forum-alumni/post/${postId}`; // Default fallback
    }
    
    fetch(apiEndpoint)
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Post data received:', data);
            const modal = document.getElementById('postDetailModal');
            const content = document.getElementById('postDetailContent');
            
            let mediaHtml = '';
            if (data.post.media_path) {
                if (data.post.media_type === 'image') {
                    mediaHtml = `
                        <div class="mb-4">
                            <img src="/storage/${data.post.media_path}" alt="Post Image" 
                                 class="w-full h-auto max-h-80 object-contain rounded-lg bg-gray-50">
                        </div>
                    `;
                } else if (data.post.media_type === 'video') {
                    mediaHtml = `
                        <div class="mb-4">
                            <video controls class="w-full h-auto max-h-80 rounded-lg">
                                <source src="/storage/${data.post.media_path}" type="video/mp4">
                            </video>
                        </div>
                    `;
                }
            }
            
            content.innerHTML = `
                <!-- User Info -->
                <div class="flex items-center space-x-3 mb-4">
                    <img src="/images/avatar/${data.post.user.avatar}" alt="Avatar" 
                         class="w-8 h-8 rounded-full">
                    <div>
                        <span class="font-medium text-sm text-gray-900">
                            ${data.post.user.nama_lengkap || data.post.user.name}
                        </span>
                        <p class="text-xs text-gray-500">${data.post.created_at_human}</p>
                    </div>
                </div>

                <!-- Post Content -->
                <div class="mb-4">
                    <p class="text-gray-800 leading-relaxed">${data.post.content}</p>
                </div>

                ${mediaHtml}
            `;
            
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        })
        .catch(error => {
            console.error('Error loading post details:', error);
            alert('Error loading post details: ' + error.message);
        });
}

function closePostDetailModal() {
    const modal = document.getElementById('postDetailModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Share Post Function
function sharePost(postId) {
    const postUrl = window.location.origin + window.location.pathname + '#post-' + postId;
    
    // Simple copy to clipboard
    const textArea = document.createElement('textarea');
    textArea.value = postUrl;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'URL postingan berhasil disalin ke clipboard!',
            timer: 2000,
            showConfirmButton: false
        });
    } catch (err) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal menyalin URL postingan'
        });
    }
    
    document.body.removeChild(textArea);
}

// Notification Function
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Fade in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Fade out and remove
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Character Counter
document.addEventListener('DOMContentLoaded', function() {
    const postContent = document.getElementById('postContent');
    const charCount = document.getElementById('charCount');
    
    if (postContent && charCount) {
        postContent.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }
});

// Instagram-style Comment Modal Functions
async function openCommentModal(postId) {
    console.log('=== Opening comment modal ===');
    console.log('Post ID:', postId);
    console.log('Current URL:', window.location.pathname);
    
    // First check if modal exists
    const modal = document.getElementById('commentModal');
    if (!modal) {
        console.error('❌ Comment modal element not found!');
        alert('Error: Comment modal tidak ditemukan di halaman ini. Modal mungkin belum diload.');
        return;
    }
    console.log('✅ Comment modal element found');
    
    try {
        // Always use forum-alumni endpoint since all forums use the same controller
        const apiEndpoint = `/forum-alumni/post/${postId}`;
        console.log('📡 Calling API:', apiEndpoint);
        
        // Fetch post data
        const response = await fetch(apiEndpoint, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('API Error:', errorText);
            throw new Error(`HTTP ${response.status}: ${errorText}`);
        }
        
        const data = await response.json();
        console.log('Received data:', data);
        
        // Extract post from response (it might be wrapped)
        const post = data.post || data;
        console.log('Post data:', post);
        
        // Get modal elements
        const modal = document.getElementById('commentModal');
        if (!modal) {
            console.error('Comment modal not found!');
            alert('Comment modal not found in the page');
            return;
        }
        
        const modalPostImage = document.getElementById('modalPostImage');
        const modalPostVideo = document.getElementById('modalPostVideo');
        const modalVideoSource = document.getElementById('modalVideoSource');
        const modalTextPost = document.getElementById('modalTextPost');
        const modalPostText = document.getElementById('modalPostText');
        const modalUserAvatar = document.getElementById('modalUserAvatar');
        const modalUserName = document.getElementById('modalUserName');
        const modalUserAvatarLarge = document.getElementById('modalUserAvatarLarge');
        const modalUserNameLarge = document.getElementById('modalUserNameLarge');
        const modalPostTime = document.getElementById('modalPostTime');
        const modalPostDescription = document.getElementById('modalPostDescription');
        const modalPostId = document.getElementById('modalPostId');
        
        // Check if required elements exist
        if (!modalUserAvatar || !modalUserName || !modalUserAvatarLarge || !modalUserNameLarge || 
            !modalPostTime || !modalPostDescription || !modalPostId) {
            throw new Error('Required modal elements are missing from the page');
        }
        
        // Reset all content displays
        modalPostImage.classList.add('hidden');
        modalPostVideo.classList.add('hidden');
        modalTextPost.classList.add('hidden');
        
        // Show appropriate content
        if (post.media_path && post.media_type === 'image') {
            modalPostImage.src = `/storage/${post.media_path}`;
            modalPostImage.classList.remove('hidden');
        } else if (post.media_path && post.media_type === 'video') {
            modalVideoSource.src = `/storage/${post.media_path}`;
            modalPostVideo.load();
            modalPostVideo.classList.remove('hidden');
        } else {
            // Text-only post
            modalPostText.textContent = post.content;
            modalTextPost.classList.remove('hidden');
        }
        
        // Fill in post details
        modalUserAvatar.src = `/images/avatar/${post.user.avatar}`;
        modalUserName.textContent = post.user.nama_lengkap || post.user.name;
        modalUserAvatarLarge.src = `/images/avatar/${post.user.avatar}`;
        modalUserNameLarge.textContent = post.user.nama_lengkap || post.user.name;
        modalPostTime.textContent = formatTime(post.created_at);
        modalPostDescription.textContent = post.content;
        modalPostId.value = postId;
        
        // Load comments
        await loadModalComments(postId);
        
        // Show modal
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        
    } catch (error) {
        console.error('Error opening comment modal:', error);
        console.error('Error stack:', error.stack);
        
        // Show detailed error to user
        alert(`Gagal membuka komentar:\n${error.message}\n\nSilakan buka Developer Console (F12) untuk detail lebih lanjut.`);
    }
}

function closeCommentModal() {
    const modal = document.getElementById('commentModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
    
    // Clear comment input
    const commentInput = document.getElementById('modalCommentInput');
    if (commentInput) commentInput.value = '';
}

async function loadModalComments(postId) {
    console.log('🔄 Loading comments for post:', postId);
    try {
        const response = await fetch(`/forum-alumni/post/${postId}/comments`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        console.log('📡 Comments API response status:', response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('❌ Comments API Error:', errorText);
            throw new Error(`HTTP ${response.status}: ${errorText}`);
        }
        
        const data = await response.json();
        console.log('📝 Comments data received:', data);
        
        // Handle different response structures
        const comments = data.comments || data;
        console.log('💬 Parsed comments:', comments);
        
        const commentsList = document.getElementById('modalCommentsList');
        if (!commentsList) {
            console.error('❌ Comments list element not found!');
            return;
        }
        
        // Clear existing comments
        commentsList.innerHTML = '';
        
        if (!Array.isArray(comments)) {
            console.error('❌ Comments is not an array:', typeof comments);
            return;
        }
        
        if (comments.length === 0) {
            console.log('📭 No comments found for this post');
            commentsList.innerHTML = '<div class="text-center text-gray-500 py-4">Belum ada komentar</div>';
            return;
        }
        
        // Add comments
        comments.forEach((comment, index) => {
            console.log(`➕ Adding comment ${index + 1}:`, comment);
            const commentElement = createCommentElement(comment);
            commentsList.appendChild(commentElement);
        });
        
    } catch (error) {
        console.error('❌ Error loading comments:', error);
        const commentsList = document.getElementById('modalCommentsList');
        if (commentsList) {
            commentsList.innerHTML = '<div class="text-center text-red-500 py-4">Gagal memuat komentar</div>';
        }
    }
}

function createCommentElement(comment) {
    const div = document.createElement('div');
    div.className = 'mb-4 comment-container';
    div.setAttribute('data-comment-id', comment.id);
    
    // No like functionality - comments only have reply
    
    div.innerHTML = `
        <div class="flex items-start space-x-3">
            <img src="/images/avatar/${comment.user.avatar}" alt="Avatar" 
                 class="w-8 h-8 rounded-full flex-shrink-0">
            <div class="flex-1 min-w-0">
                <div class="text-sm">
                    <span class="font-medium">${comment.user.nama_lengkap || comment.user.name}</span>
                    <span class="ml-1">${comment.content}</span>
                </div>
                <div class="flex items-center space-x-4 mt-1 text-xs text-gray-500">
                    <span>${formatTime(comment.created_at)}</span>
                    <button class="reply-button font-medium hover:text-gray-700" 
                            data-comment-id="${comment.id}" 
                            data-username="${comment.user.nama_lengkap || comment.user.name}"
                            onclick="replyToComment(${comment.id}, '${(comment.user.nama_lengkap || comment.user.name).replace(/'/g, "\\'")}')">
                        Reply
                    </button>
                </div>
                
                
                <!-- Replies Container -->
                <div class="replies-container mt-3">
                    ${comment.replies && comment.replies.length > 0 ? createRepliesHtml(comment.replies) : ''}
                </div>
            </div>
        </div>
    `;
    
    return div;
}

// Create replies HTML
function createRepliesHtml(replies) {
    return replies.map(reply => {
        // No like functionality - replies only have reply button
        
        return `
            <div class="flex items-start space-x-2 ml-8 mt-2" data-comment-id="${reply.id}">
                <img src="/images/avatar/${reply.user.avatar}" alt="Avatar" 
                     class="w-6 h-6 rounded-full flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <div class="text-sm">
                        <span class="font-medium">${reply.user.nama_lengkap || reply.user.name}</span>
                        <span class="ml-1">${formatReplyContent(reply.content)}</span>
                    </div>
                    <div class="flex items-center space-x-3 mt-1 text-xs text-gray-500">
                        <span>${formatTime(reply.created_at)}</span>
                        <button class="reply-button font-medium hover:text-gray-700" 
                                data-comment-id="${reply.parent_id || reply.id}" 
                                data-username="${reply.user.nama_lengkap || reply.user.name}"
                                onclick="replyToComment(${reply.parent_id || reply.id}, '${(reply.user.nama_lengkap || reply.user.name).replace(/'/g, "\\'")}')">
                            Reply
                        </button>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Reply to comment - adds @username to main comment input (make it globally accessible)
window.replyToComment = function(commentId, username) {
    console.log('💬 REPLY FUNCTION CALLED - Comment ID:', commentId, 'Username:', username);
    
    // Try multiple ways to find the comment input
    let mainCommentInput = document.getElementById('modalCommentInput');
    
    if (!mainCommentInput) {
        console.log('🔍 Trying alternative selectors...');
        mainCommentInput = document.querySelector('#modalCommentInput');
    }
    
    if (!mainCommentInput) {
        mainCommentInput = document.querySelector('input[placeholder*="comment"]');
    }
    
    if (!mainCommentInput) {
        mainCommentInput = document.querySelector('#modalCommentForm input[type="text"]');
    }
    
    console.log('🔍 Comment input element found:', mainCommentInput);
    console.log('🔍 All input elements:', document.querySelectorAll('input'));
    console.log('🔍 Modal form:', document.getElementById('modalCommentForm'));
    
    if (mainCommentInput) {
        // Clean username (remove any extra spaces or special chars)
        const cleanUsername = username.trim();
        const mentionText = `@${cleanUsername} `;
        
        console.log('📝 Setting mention text:', mentionText);
        
        // Clear existing content and add mention
        mainCommentInput.value = mentionText;
        mainCommentInput.focus();
        
        // Store the comment ID we're replying to for submission
        mainCommentInput.setAttribute('data-reply-to', commentId);
        console.log('🔗 Set reply-to attribute:', commentId);
        
        // Position cursor at the end
        setTimeout(() => {
            mainCommentInput.setSelectionRange(mentionText.length, mentionText.length);
            console.log('📍 Cursor positioned at end');
        }, 50);
        
        // Scroll to bottom to show the input
        const commentsList = document.getElementById('modalCommentsList');
        if (commentsList) {
            commentsList.scrollTop = commentsList.scrollHeight;
            console.log('📜 Scrolled to bottom');
        }
        
        // Change placeholder to indicate replying
        mainCommentInput.placeholder = `Replying to ${cleanUsername}...`;
        
        console.log('✅ Reply setup completed successfully');
        
        // Highlight the input field to show it worked
        mainCommentInput.style.border = '2px solid #3B82F6';
        setTimeout(() => {
            mainCommentInput.style.border = '';
        }, 2000);
    } else {
        console.error('❌ Main comment input STILL not found!');
        console.log('🔍 Available inputs:', Array.from(document.querySelectorAll('input')).map(inp => ({
            id: inp.id,
            type: inp.type,
            placeholder: inp.placeholder,
            name: inp.name
        })));
        alert('Error: Comment input field not found. Please refresh the page and try again.');
    }
}

// Format reply content to highlight @mentions like Instagram
function formatReplyContent(content) {
    if (!content) return '';
    
    // Replace @mentions with styled spans (Instagram style)
    return content.replace(/@(\w+)/g, '<span class="text-blue-600 font-medium">@$1</span>');
}

// Update current user avatar in reply inputs
function updateCurrentUserAvatar() {
    const userAvatar = document.querySelector('meta[name="user-avatar"]')?.getAttribute('content') || 'default.jpg';
    document.querySelectorAll('.current-user-avatar').forEach(img => {
        img.src = `/images/avatar/${userAvatar}`;
    });
}


// Show success notification
function showSuccessNotification(message) {
    // Remove existing notifications
    const existing = document.querySelector('.notification-toast');
    if (existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.className = 'notification-toast fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-[9999] transform transition-all duration-300 translate-x-0';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Show error notification
function showErrorNotification(message) {
    // Remove existing notifications
    const existing = document.querySelector('.notification-toast');
    if (existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.className = 'notification-toast fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-[9999] transform transition-all duration-300 translate-x-0';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 5000);
}

// Instagram-style like system with persistent state
window.likeComment = function(commentId) {
    console.log('❤️ Instagram Like System - Comment:', commentId);
    
    const commentContainer = document.querySelector(`[data-comment-id="${commentId}"]`);
    if (!commentContainer) {
        console.error('❌ Comment container not found');
        return;
    }
    
    const likeButton = commentContainer.querySelector(`button[data-like-btn="${commentId}"]`);
    const likesCountSpan = commentContainer.querySelector('.likes-count');
    
    console.log('🔍 Like button:', likeButton);
    console.log('🔍 Likes count span:', likesCountSpan);
    
    if (!likeButton) {
        console.error('❌ Like button not found');
        return;
    }
    
    // Get current heart state
    const currentHeart = likeButton.innerHTML.trim();
    const isCurrentlyLiked = currentHeart === '❤️';
    const willBeLiked = !isCurrentlyLiked;
    
    console.log('💖 Current heart:', currentHeart, '→ Will be liked:', willBeLiked);
    
    // Update heart icon immediately - Instagram style instant feedback
    likeButton.innerHTML = willBeLiked ? '❤️' : '🤍';
    
    // Add heart pop animation
    likeButton.style.transform = 'scale(1.3)';
    setTimeout(() => {
        likeButton.style.transform = 'scale(1)';
    }, 150);
    
    // Update likes count optimistically
    let currentLikesText = likesCountSpan ? likesCountSpan.textContent.trim() : '';
    let currentCount = 0;
    
    // Parse current count
    if (currentLikesText && currentLikesText !== '') {
        const match = currentLikesText.match(/(\d+)/);
        currentCount = match ? parseInt(match[1]) : 0;
    }
    
    console.log('📊 Current count parsed from "' + currentLikesText + '":', currentCount);
    
    const newCount = willBeLiked ? currentCount + 1 : Math.max(0, currentCount - 1);
    const newLikesText = newCount === 0 ? '' : (newCount === 1 ? '1 like' : `${newCount} likes`);
    
    if (likesCountSpan) {
        likesCountSpan.textContent = newLikesText;
        console.log('📊 Updated likes text to:', newLikesText);
    }
    
    // Celebration animation for likes
    if (willBeLiked) {
        likeButton.style.animation = 'heartPop 0.4s ease-in-out';
        setTimeout(() => {
            likeButton.style.animation = '';
        }, 400);
        console.log('🎉 Added celebration animation');
    }
    
    console.log('✅ Like action completed - Heart is now:', likeButton.innerHTML);
    
    // Send to server (background - don't let it interfere with UI)
    fetch('/forum-alumni/like-comment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ comment_id: commentId })
    })
    .then(response => response.json())
    .then(data => {
        console.log('✅ Server response (background):', data);
        // Don't change UI based on server - keep client state
    })
    .catch(error => {
        console.log('⚠️ Server error (ignored):', error);
        // Don't revert - keep client state for better UX
    });
}

// Reply to comment function
function replyToComment(commentId) {
    const commentDiv = document.querySelector(`[data-comment-id="${commentId}"]`);
    if (!commentDiv) return;
    
    // Remove any existing reply forms
    const existingReplyForm = document.querySelector('.reply-form');
    if (existingReplyForm) {
        existingReplyForm.remove();
    }
    
    // Create reply form
    const replyForm = document.createElement('div');
    replyForm.className = 'reply-form mt-2 ml-11';
    replyForm.innerHTML = `
        <div class="flex items-center space-x-2">
            <img src="/images/avatar/${window.currentUser.avatar}" alt="Avatar" class="w-6 h-6 rounded-full">
            <input type="text" placeholder="Write a reply..." 
                   class="flex-1 px-3 py-1 text-sm border border-gray-300 rounded-full focus:outline-none focus:border-blue-500"
                   onkeypress="handleReplySubmit(event, ${commentId})">
            <button onclick="cancelReply()" class="text-xs text-gray-500 hover:text-gray-700">Cancel</button>
        </div>
    `;
    
    commentDiv.appendChild(replyForm);
    replyForm.querySelector('input').focus();
}

// Handle reply submission
async function handleReplySubmit(event, parentCommentId) {
    if (event.key === 'Enter') {
        const content = event.target.value.trim();
        if (!content) return;
        
        try {
            const postId = document.getElementById('modalPostId').value;
            const formData = new FormData();
            formData.append('content', content);
            formData.append('parent_id', parentCommentId);
            
            const response = await fetch(`/forum-alumni/${postId}/comment`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Add reply to the comment
                const parentCommentDiv = document.querySelector(`[data-comment-id="${parentCommentId}"]`);
                if (parentCommentDiv) {
                    let repliesContainer = parentCommentDiv.querySelector('.replies-container');
                    if (!repliesContainer) {
                        repliesContainer = document.createElement('div');
                        repliesContainer.className = 'replies-container ml-11 mt-2 space-y-2';
                        parentCommentDiv.appendChild(repliesContainer);
                    }
                    
                    const replyElement = createReplyElement(data.comment);
                    repliesContainer.appendChild(replyElement);
                }
                
                // Remove reply form
                cancelReply();
                
                // Update comments count
                const commentsCountElement = document.getElementById('modalCommentsCount');
                if (commentsCountElement) {
                    commentsCountElement.textContent = data.comments_count;
                }
            }
        } catch (error) {
            console.error('Error adding reply:', error);
        }
    }
}

// Cancel reply
function cancelReply() {
    const replyForm = document.querySelector('.reply-form');
    if (replyForm) {
        replyForm.remove();
    }
}

// Create reply element
function createReplyElement(reply) {
    const div = document.createElement('div');
    div.className = 'flex items-start space-x-2';
    div.setAttribute('data-comment-id', reply.id);
    
    div.innerHTML = `
        <img src="/images/avatar/${reply.user.avatar}" alt="Avatar" 
             class="w-6 h-6 rounded-full flex-shrink-0">
        <div class="flex-1 min-w-0">
            <div class="text-sm">
                <span class="font-medium">${reply.user.nama_lengkap || reply.user.name}</span>
                <span class="ml-1">${reply.content}</span>
            </div>
            <div class="flex items-center space-x-4 mt-1 text-xs text-gray-500">
                <span>${formatTime(reply.created_at)}</span>
                <button onclick="likeComment(${reply.id})" class="font-medium hover:text-gray-700">
                    Like
                </button>
            </div>
        </div>
    `;
    
    return div;
}

// Handle modal comment form submission
document.addEventListener('DOMContentLoaded', function() {
    const modalCommentForm = document.getElementById('modalCommentForm');
    if (modalCommentForm) {
        modalCommentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const postId = document.getElementById('modalPostId').value;
            const commentInput = document.getElementById('modalCommentInput');
            const content = commentInput.value.trim();
            
            if (!content) return;
            
            try {
                console.log('💬 Submitting comment/reply for post:', postId);
                console.log('📝 Content:', content);
                
                const formData = new FormData();
                formData.append('content', content);
                
                // Check if this is a reply (has @mention and data-reply-to attribute)
                const replyToId = commentInput.getAttribute('data-reply-to');
                const isReply = replyToId && content.startsWith('@');
                
                if (isReply) {
                    formData.append('parent_id', replyToId);
                    console.log('🔗 This is a reply to comment:', replyToId);
                } else {
                    console.log('💬 This is a new comment');
                }
                
                const response = await fetch(`/forum-alumni/${postId}/comment`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                
                console.log('📡 Submission response status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('❌ Submission error:', errorText);
                    throw new Error(`HTTP ${response.status}: ${errorText}`);
                }
                
                const data = await response.json();
                console.log('✅ Submission success:', data);
                
                // Refresh comments to show the new comment/reply
                await loadModalComments(postId);
                
                // Update comments count in main post
                const postCard = document.querySelector(`[data-post-id="${postId}"]`);
                if (postCard) {
                    const commentsCount = postCard.querySelector('.comments-count');
                    if (commentsCount) {
                        commentsCount.textContent = `Lihat semua ${data.comments_count} komentar`;
                        console.log('🔄 Updated comments count in main post');
                    }
                }
                
                // Clear input and remove reply attributes
                commentInput.value = '';
                commentInput.removeAttribute('data-reply-to');
                
                // Show success notification
                const message = isReply ? 'Balasan berhasil dikirim!' : 'Komentar berhasil dikirim!';
                showSuccessNotification(message);
                
                // Scroll to bottom to show new content
                const commentsList = document.getElementById('modalCommentsList');
                if (commentsList) {
                    setTimeout(() => {
                        commentsList.scrollTop = commentsList.scrollHeight;
                    }, 100);
                }
                
            } catch (error) {
                console.error('❌ Error submitting:', error);
                showErrorNotification(`Gagal mengirim: ${error.message}`);
            }
        });
    }
});

// Utility functions for date/time formatting
function formatTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000); // seconds
    
    if (diff < 60) return 'Baru saja';
    if (diff < 3600) return `${Math.floor(diff / 60)} menit yang lalu`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} jam yang lalu`;
    if (diff < 604800) return `${Math.floor(diff / 86400)} hari yang lalu`;
    
    return date.toLocaleDateString('id-ID');
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { 
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Post modal event listeners
    const modal = document.getElementById('postModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closePostModal();
            }
        });
    }
    
    // Comment modal event listeners
    const commentModal = document.getElementById('commentModal');
    if (commentModal) {
        commentModal.addEventListener('click', function(e) {
            if (e.target === commentModal) {
                closeCommentModal();
            }
        });
    }
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
            closePostModal();
            closeCommentModal();
        }
    });
    
    // Auto-resize comment inputs
    document.addEventListener('input', function(e) {
        if (e.target.matches('input[name="content"]')) {
            // Simple auto-resize for inputs would need textarea conversion
        }
    });
    
    // Add event listeners for reply buttons (using event delegation)
    document.addEventListener('click', function(e) {        
        // Handle reply buttons
        if (e.target.classList.contains('reply-button')) {
            e.preventDefault();
            e.stopPropagation();
            
            const commentId = e.target.getAttribute('data-comment-id');
            const username = e.target.getAttribute('data-username');
            
            console.log('🔘 Reply button clicked:', { commentId, username });
            
            if (commentId && username) {
                replyToComment(commentId, username);
            } else {
                console.error('❌ Missing reply data:', { commentId, username });
            }
        }
    });
});
