<div class="comment-item mb-3" data-comment-id="{{ $comment->id }}">
    <div class="flex space-x-3">
        <img src="{{ asset('images/avatar/' . $comment->user->avatar) }}" alt="Avatar" 
             class="w-8 h-8 rounded-full flex-shrink-0">
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="text-sm">
                        <span class="font-semibold">{{ $comment->user->nama_lengkap ?? $comment->user->name }}</span>
                        <span class="ml-1">{{ $comment->content }}</span>
                    </div>
                    <div class="flex items-center space-x-4 mt-1 text-xs text-gray-500">
                        <span>{{ $comment->created_at->diffForHumans() }}</span>
                        <button onclick="toggleReplyForm({{ $comment->id }})" class="font-semibold hover:text-gray-700">
                            Balas
                        </button>
                        @if($comment->user_id === auth()->id() || auth()->user()->role === 'admin')
                            <button onclick="deleteComment({{ $comment->id }})" class="hover:text-red-500">
                                Hapus
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Reply Form -->
            <div id="reply-form-{{ $comment->id }}" class="reply-form hidden mt-3">
                <form onsubmit="addReply(event, {{ $post_id }}, {{ $comment->id }})" class="flex items-center space-x-3">
                    <img src="{{ asset('images/avatar/' . Auth::user()->avatar) }}" alt="Avatar" 
                         class="w-6 h-6 rounded-full">
                    <input type="text" name="content" placeholder="Tulis balasan..." 
                           class="flex-1 py-1 px-0 border-none focus:outline-none text-sm" required>
                    <button type="submit" class="text-blue-500 font-semibold text-xs hover:text-blue-600">
                        Kirim
                    </button>
                    <button type="button" onclick="toggleReplyForm({{ $comment->id }})" 
                            class="text-gray-500 font-semibold text-xs hover:text-gray-700">
                        Batal
                    </button>
                </form>
            </div>
            
            <!-- Replies -->
            @if($comment->replies->count() > 0)
                <div class="replies mt-3 pl-4 border-l-2 border-gray-100">
                    @foreach($comment->replies as $reply)
                        @include('components.reply', ['reply' => $reply, 'post_id' => $post_id])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>