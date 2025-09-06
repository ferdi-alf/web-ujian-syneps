// Forum JavaScript Functions - Instagram Style

// Modal Functions
function openPostModal() {
  console.log('Opening modal...'); // Debug log
  const modal = document.getElementById('postModal');
  if (modal) {
      modal.classList.remove('hidden');
      modal.style.display = 'flex';
      const postContent = document.getElementById('postContent');
      if (postContent) {
          setTimeout(() => postContent.focus(), 100);
      }
  } else {
      console.error('Modal not found!');
  }
}

function closePostModal() {
  console.log('Closing modal...'); // Debug log
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
  if (imagePreview) imagePreview.classList.add('hidden');
  if (videoPreview) videoPreview.classList.add('hidden');
  
  // Clear file inputs
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
          
          // Update like button state
          if (data.liked) {
              likeBtn.classList.add('liked');
              likeBtn.classList.add('text-blue-600');
              likeBtn.classList.remove('text-gray-600');
          } else {
              likeBtn.classList.remove('liked');
              likeBtn.classList.remove('text-blue-600');
              likeBtn.classList.add('text-gray-600');
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
          
          // Create new comment HTML
          const newComment = document.createElement('div');
          newComment.className = 'comment-item flex space-x-3 mb-4';
          newComment.setAttribute('data-comment-id', data.comment.id);
          
          const currentUserAvatar = document.querySelector('meta[name="user-avatar"]')?.getAttribute('content') || 'default.png';
          const currentUserName = document.querySelector('meta[name="user-name"]')?.getAttribute('content') || 'User';
          
          newComment.innerHTML = `
              <img src="/images/avatar/${currentUserAvatar}" alt="Avatar" 
                   class="w-8 h-8 rounded-full border border-gray-200">
              <div class="flex-1 bg-gray-50 rounded-lg p-3">
                  <div class="flex items-start justify-between">
                      <div>
                          <h4 class="font-medium text-gray-900 text-sm">${currentUserName}</h4>
                          <p class="text-gray-800 text-sm mt-1">${data.comment.content}</p>
                          <p class="text-xs text-gray-500 mt-1">Baru saja</p>
                      </div>
                      <button onclick="deleteComment(${data.comment.id})" 
                              class="text-gray-400 hover:text-red-500 text-xs">
                          <i class="fas fa-times"></i>
                      </button>
                  </div>
              </div>
          `;
          
          // Add to comments list
          commentsList.appendChild(newComment);
          
          // Update comments count
          commentsCount.textContent = `${data.comments_count} komentar`;
          
          // Clear form
          form.reset();
      }
  } catch (error) {
      console.error('Error adding comment:', error);
  }
}

// Delete Functions
async function deletePost(postId) {
  if (!confirm('Apakah Anda yakin ingin menghapus postingan ini?')) return;
  
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
          
          // Show success message
          showNotification('Post berhasil dihapus!', 'success');
      } else {
          showNotification(data.error || 'Gagal menghapus post', 'error');
      }
  } catch (error) {
      console.error('Error deleting post:', error);
      showNotification('Terjadi kesalahan', 'error');
  }
}

async function deleteComment(commentId) {
  if (!confirm('Apakah Anda yakin ingin menghapus komentar ini?')) return;
  
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
          const currentCount = parseInt(commentsCount.textContent.match(/\d+/)[0]);
          commentsCount.textContent = `${currentCount - 1} komentar`;
          
          showNotification('Komentar berhasil dihapus!', 'success');
      } else {
          showNotification(data.error || 'Gagal menghapus komentar', 'error');
      }
  } catch (error) {
      console.error('Error deleting comment:', error);
      showNotification('Terjadi kesalahan', 'error');
  }
}

// Notification Function
function showNotification(message, type = 'success') {
  const notification = document.createElement('div');
  notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
      type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
  }`;
  notification.textContent = message;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
      notification.remove();
  }, 3000);
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('postModal');
  if (modal) {
      modal.addEventListener('click', function(e) {
          if (e.target === modal) {
              closePostModal();
          }
      });
  }
});

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

// Media Preview Functions
function previewMedia(input) {
  const file = input.files[0];
  if (!file) return;
  
  const mediaPreview = document.getElementById('mediaPreview');
  const imagePreview = document.getElementById('imagePreview');
  const videoPreview = document.getElementById('videoPreview');
  const videoSource = document.getElementById('videoSource');
  
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
  
  mediaPreview.classList.add('hidden');
  imagePreview.classList.add('hidden');
  videoPreview.classList.add('hidden');
  
  // Clear file inputs
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
          
          // Update like button state
          if (data.liked) {
              likeBtn.classList.add('liked');
              likeBtn.classList.add('text-blue-600');
              likeBtn.classList.remove('text-gray-600');
          } else {
              likeBtn.classList.remove('liked');
              likeBtn.classList.remove('text-blue-600');
              likeBtn.classList.add('text-gray-600');
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
          
          // Create new comment HTML
          const newComment = document.createElement('div');
          newComment.className = 'comment-item flex space-x-3 mb-4';
          newComment.setAttribute('data-comment-id', data.comment.id);
          
          const currentUserAvatar = document.querySelector('meta[name="user-avatar"]')?.getAttribute('content') || 'default.png';
          const currentUserName = document.querySelector('meta[name="user-name"]')?.getAttribute('content') || 'User';
          
          newComment.innerHTML = `
              <img src="/images/avatar/${currentUserAvatar}" alt="Avatar" 
                   class="w-8 h-8 rounded-full border border-gray-200">
              <div class="flex-1 bg-gray-50 rounded-lg p-3">
                  <div class="flex items-start justify-between">
                      <div>
                          <h4 class="font-medium text-gray-900 text-sm">${currentUserName}</h4>
                          <p class="text-gray-800 text-sm mt-1">${data.comment.content}</p>
                          <p class="text-xs text-gray-500 mt-1">Baru saja</p>
                      </div>
                      <button onclick="deleteComment(${data.comment.id})" 
                              class="text-gray-400 hover:text-red-500 text-xs">
                          <i class="fas fa-times"></i>
                      </button>
                  </div>
              </div>
          `;
          
          // Add to comments list
          commentsList.appendChild(newComment);
          
          // Update comments count
          commentsCount.textContent = `${data.comments_count} komentar`;
          
          // Clear form
          form.reset();
      }
  } catch (error) {
      console.error('Error adding comment:', error);
  }
}

// Delete Functions
async function deletePost(postId) {
  if (!confirm('Apakah Anda yakin ingin menghapus postingan ini?')) return;
  
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
          
          // Show success message
          showNotification('Post berhasil dihapus!', 'success');
      } else {
          showNotification(data.error || 'Gagal menghapus post', 'error');
      }
  } catch (error) {
      console.error('Error deleting post:', error);
      showNotification('Terjadi kesalahan', 'error');
  }
}

async function deleteComment(commentId) {
  if (!confirm('Apakah Anda yakin ingin menghapus komentar ini?')) return;
  
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
          const currentCount = parseInt(commentsCount.textContent.match(/\d+/)[0]);
          commentsCount.textContent = `${currentCount - 1} komentar`;
          
          showNotification('Komentar berhasil dihapus!', 'success');
      } else {
          showNotification(data.error || 'Gagal menghapus komentar', 'error');
      }
  } catch (error) {
      console.error('Error deleting comment:', error);
      showNotification('Terjadi kesalahan', 'error');
  }
}

// Notification Function
function showNotification(message, type = 'success') {
  const notification = document.createElement('div');
  notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
      type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
  }`;
  notification.textContent = message;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
      notification.remove();
  }, 3000);
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('postModal');
  if (modal) {
      modal.addEventListener('click', function(e) {
          if (e.target === modal) {
              closePostModal();
          }
      });
  }
});