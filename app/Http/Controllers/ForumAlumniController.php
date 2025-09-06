<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumPost;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:50000', // 50MB max
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

        $comment = Comment::create([
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
        $comment = Comment::findOrFail($commentId);

        // Only allow deletion by comment owner or admin
        if ($comment->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $post = $comment->forumPost;
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
        $post = ForumPost::findOrFail($postId);

        $comments = Comment::with(['user', 'replies.user', 'likes'])
            ->withCount('likes')
            ->where('post_id', $postId)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // Add is_liked status for each comment and reply
        $comments->each(function ($comment) {
            $comment->is_liked = $comment->likes()->where('user_id', Auth::id())->exists();
            if ($comment->replies) {
                $comment->replies->each(function ($reply) {
                    $reply->is_liked = $reply->likes()->where('user_id', Auth::id())->exists();
                });
            }
        });

        return response()->json([
            'comments' => $comments,
            'comments_count' => $post->comments_count
        ]);
    }

    public function toggleCommentLike($commentId)
    {
        $comment = Comment::findOrFail($commentId);

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
