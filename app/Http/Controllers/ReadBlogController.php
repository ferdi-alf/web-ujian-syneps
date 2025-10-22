<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReadBlogController extends Controller
{
    public function index() 
    {
        $carouselBlogs = Cache::remember('carousel_blogs', 3600, function() {
            return Blog::where('is_published', true)
                ->latest()
                ->take(5)
                ->get(['id', 'thumbnail', 'judul', 'slug', 'type']);
        });

        $pengumumanBlogs = Cache::remember('pengumuman_blogs', 1800, function() {
            return Blog::where('is_published', true)
                ->where('type', 'pengumuman')
                ->latest()
                ->take(3)
                ->get(['id', 'thumbnail', 'judul', 'slug', 'content', 'created_at']);
        });

        $tutorialBlogs = Cache::remember('tutorial_blogs', 1800, function() {
            return Blog::where('is_published', true)
                ->where('type', 'tutorial')
                ->latest()
                ->take(3)
                ->get(['id', 'thumbnail', 'judul', 'slug', 'content', 'created_at']);
        });

        $beritaBlogs = Cache::remember('berita_blogs', 1800, function() {
            return Blog::where('is_published', true)
                ->where('type', 'berita')
                ->latest()
                ->take(3)
                ->get(['id', 'thumbnail', 'judul', 'slug', 'content', 'created_at']);
        });

        $tipsBlogs = Cache::remember('tips_blogs', 1800, function() {
            return Blog::where('is_published', true)
                ->where('type', 'tips')
                ->latest()
                ->take(3)
                ->get(['id', 'thumbnail', 'judul', 'slug', 'content', 'created_at']);
        });

        $acaraBlogs = Cache::remember('acara_blogs', 1800, function() {
            return Blog::where('is_published', true)
                ->where('type', 'acara')
                ->latest()
                ->take(3)
                ->get(['id', 'thumbnail', 'judul', 'slug', 'content', 'created_at']);
        });

        return view('blog.blog-page', compact(
            'carouselBlogs',
            'pengumumanBlogs',
            'tutorialBlogs',
            'beritaBlogs',
            'tipsBlogs',
            'acaraBlogs'
        ));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        // Sanitize input untuk proteksi XSS
        $query = strip_tags($query);
        $query = htmlspecialchars($query, ENT_QUOTES, 'UTF-8');

        $results = Blog::where('is_published', true)
            ->where(function($q) use ($query) {
                $q->where('judul', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->latest()
            ->take(5)
            ->get(['id', 'thumbnail', 'judul', 'slug', 'type'])
            ->map(function($blog) {
                return [
                    'id' => $blog->id,
                    'thumbnail' => $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : asset('images/default-blog.jpg'),
                    'judul' => e($blog->judul), // XSS protection
                    'slug' => $blog->slug,
                    'type' => $blog->type,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    public function read($slug)
    {
        $blog = Blog::where('slug', $slug)
            ->where('is_published', true)
            ->with('creator:id,name')
            ->firstOrFail();

        $recommendations = Blog::where('is_published', true)
            ->where('type', $blog->type)
            ->where('id', '!=', $blog->id)
            ->latest()
            ->take(3)
            ->get(['id', 'thumbnail', 'judul', 'slug', 'content', 'created_at']);

        if ($recommendations->count() < 3) {
            $additionalRecommendations = Blog::where('is_published', true)
                ->where('id', '!=', $blog->id)
                ->whereNotIn('id', $recommendations->pluck('id'))
                ->latest()
                ->take(3 - $recommendations->count())
                ->get(['id', 'thumbnail', 'judul', 'slug', 'content', 'created_at']);
            
            $recommendations = $recommendations->merge($additionalRecommendations);
        }

        return view('blog.read-blog', compact('blog', 'recommendations'));
    }

    private function getTypeBadge($type)
    {
        $badges = [
            'acara' => 'bg-purple-100 text-purple-800',
            'tutorial' => 'bg-blue-100 text-blue-800',
            'pengumuman' => 'bg-red-100 text-red-800',
            'berita' => 'bg-green-100 text-green-800',
            'tips' => 'bg-yellow-100 text-yellow-800',
        ];

        return $badges[$type] ?? 'bg-gray-100 text-gray-800';
    }
}