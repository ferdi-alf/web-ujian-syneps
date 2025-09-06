<div class="reply-item flex space-x-2 mb-2" data-comment-id="{{ $reply->id }}">
    <img src="{{ asset('images/avatar/' . $reply->user->avatar) }}" alt="Avatar" 
         class="w-6 h-6 rounded-full flex-shrink-0">
    <div class="flex-1 min-w-0">
        <div class="text-sm">
            <span class="font-semibold">{{ $reply->user->nama_lengkap ?? $reply->user->name }}</span>
            <span class="ml-1">{{ $reply->content }}</span>
        </div>
        <div class="flex items-center space-x-4 mt-1 text-xs text-gray-500">
            <span>{{ $reply->created_at->diffForHumans() }}</span>
            <button onclick="toggleReplyForm({{ $reply->parent_id }})" class="font-semibold hover:text-gray-700">
                Balas
            </button>
            @if($reply->user_id === auth()->id() || auth()->user()->role === 'admin')
                <button onclick="deleteComment({{ $reply->id }})" class="hover:text-red-500">
                    Hapus
                </button>
            @endif
        </div>
    </div>
</div>