<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\ForumLike;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ForumAlumniController extends Controller
{
    public function index(Request $request)
    {
        $posts = ForumPost::with(['user', 'comments.user', 'likes'])
            ->orderBy('created_at', 'desc')
            ->paginate(5); // Show 5 posts per page for better performance

        // Handle AJAX requests for infinite scroll
        if ($request->ajax()) {
            $postsHtml = view('components.post-list', compact('posts'))->render();

            return response()->json([
                'posts_html' => $postsHtml,
                'has_more' => $posts->hasMorePages(),
                'next_page' => $posts->currentPage() + 1
            ]);
        }

        if (Auth::user()->role == 'admin') {
            return view('Dashboard.Forum-Alumni', compact('posts'));
        } elseif (Auth::user()->role == 'pengajar') {
            return view('Dashboard.Forum-Alumni', compact('posts'));
        } else {
            return view('Dashboard.Forum-Alumni', compact('posts'));
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'media' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:50000', // Media is now required
        ], [
            'media.required' => 'Foto atau video harus ditambahkan untuk membuat postingan.',
            'media.file' => 'File yang diupload harus berupa foto atau video.',
            'media.mimes' => 'File harus berformat: jpg, jpeg, png, gif, mp4, mov, atau avi.',
            'media.max' => 'Ukuran file maksimal 50MB.'
        ]);

        $mediaPath = null;
        $mediaType = null;

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $extension = $file->getClientOriginalExtension();

            // Determine media type
            if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {
                $mediaType = 'image';
            } elseif (in_array(strtolower($extension), ['mp4', 'mov', 'avi'])) {
                $mediaType = 'video';
            }

            $mediaPath = $file->store('forum-media', 'public');
        }

        ForumPost::create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'media_path' => $mediaPath,
            'media_type' => $mediaType,
        ]);

        return redirect()->route('forum-alumni.index')->with('success', 'Post berhasil dibuat!');
    }

    public function toggleLike(Request $request, $postId)
    {
        $post = ForumPost::findOrFail($postId);
        $isLiked = $post->toggleLike(Auth::id());

        return response()->json([
            'liked' => $isLiked,
            'likes_count' => $post->likes_count
        ]);
    }

    public function addComment(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:post_comments,id'
        ]);

        $post = ForumPost::findOrFail($postId);

        $comment = ForumComment::create([
            'user_id' => Auth::id(),
            'post_id' => $postId,
            'parent_id' => $request->input('parent_id'),
            'content' => $request->input('content')
        ]);

        $post->increment('comments_count');

        $comment->load('user');

        return response()->json([
            'comment' => $comment,
            'comments_count' => $post->comments_count,
            'parent_id' => $request->parent_id
        ]);
    }

    public function destroy($postId)
    {
        $post = ForumPost::findOrFail($postId);

        // Only allow deletion by post owner or admin
        if ($post->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete media file if exists
        if ($post->media_path) {
            Storage::disk('public')->delete($post->media_path);
        }

        $post->delete();

        return response()->json(['success' => 'Post berhasil dihapus!']);
    }

    public function deleteComment($commentId)
    {
        $comment = ForumComment::findOrFail($commentId);

        // Only allow deletion by comment owner or admin
        if ($comment->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $post = $comment->post;
        $comment->delete();
        $post->decrement('comments_count');

        return response()->json(['success' => 'Komentar berhasil dihapus!']);
    }

    public function getPost($postId)
    {
        $post = ForumPost::with(['user', 'likes'])
            ->withCount(['comments', 'likes'])
            ->findOrFail($postId);

        $post->is_liked = $post->likes()->where('user_id', Auth::id())->exists();
        $post->created_at_human = $post->created_at->diffForHumans();

        return response()->json([
            'post' => $post
        ]);
    }

    public function getComments($postId)
    {
        try {
            $post = ForumPost::findOrFail($postId);

            $comments = ForumComment::with(['user', 'replies.user'])
                ->where('post_id', $postId)
                ->whereNull('parent_id')
                ->orderBy('created_at', 'desc')
                ->get();

            // Format comments for response
            $formattedComments = $comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at->toISOString(),
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'nama_lengkap' => $comment->user->nama_lengkap ?? $comment->user->name,
                        'avatar' => $comment->user->avatar ?? 'default.jpg',
                        'role' => $comment->user->role ?? 'alumni'
                    ],
                    'replies' => $comment->replies->map(function ($reply) {
                        return [
                            'id' => $reply->id,
                            'content' => $reply->content,
                            'created_at' => $reply->created_at->toISOString(),
                            'parent_id' => $reply->parent_id,
                            'user' => [
                                'id' => $reply->user->id,
                                'name' => $reply->user->name,
                                'nama_lengkap' => $reply->user->nama_lengkap ?? $reply->user->name,
                                'avatar' => $reply->user->avatar ?? 'default.jpg',
                                'role' => $reply->user->role ?? 'alumni'
                            ]
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'comments' => $formattedComments,
                'comments_count' => $post->comments_count
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading comments: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load comments: ' . $e->getMessage(),
                'comments' => [],
                'comments_count' => 0
            ], 500);
        }
    }

    public function toggleCommentLike($commentId)
    {
        $comment = ForumComment::findOrFail($commentId);

        $existingLike = $comment->likes()->where('user_id', Auth::id())->first();

        if ($existingLike) {
            $existingLike->delete();
            $isLiked = false;
        } else {
            $comment->likes()->create([
                'user_id' => Auth::id()
            ]);
            $isLiked = true;
        }

        $likesCount = $comment->likes()->count();

        return response()->json([
            'liked' => $isLiked,
            'likes_count' => $likesCount
        ]);
    }
}
