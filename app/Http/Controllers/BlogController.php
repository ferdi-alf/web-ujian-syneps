<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class BlogController extends Controller
{
    public function index($act = null)
    {
        if ($act === 'create') {
            return view('Dashboard.BlogForm', [
                'mode' => 'create',
                'blog' => null
            ]);
        }

        $blogs = Blog::with('creator')->latest()->get()->map(function ($blog) {
            return [
                'id' => $blog->id,
                'thumbnail' => $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : asset('images/default-blog.jpg'),
                'judul' => $blog->judul,
                'slug' => $blog->slug,
                'type' => $blog->type,
                'type_badge' => $this->getTypeBadge($blog->type),
                'excerpt' => Str::limit(strip_tags($blog->content), 100),
                'created_by' => $blog->creator?->name ?? 'Unknown',
                'created_at' => $blog->created_at->format('d M Y'),
                'is_published' => $blog->is_published,
            ];
        });

        return view('Dashboard.Blog', compact('blogs'));
    }

    public function show($slug, $act = null)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();

        if ($act === 'edit') {
            return view('Dashboard.BlogForm', [
                'mode' => 'edit',
                'blog' => $blog
            ]);
        }


        return response()->json([
            'success' => true,
            'data' => [
                'viewData' => [
                    'id' => $blog->id,
                    'thumbnail' => $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : null,
                    'judul' => $blog->judul,
                    'slug' => $blog->slug,
                    'type' => $blog->type,
                    'type_badge' => $this->getTypeBadge($blog->type),
                    'content' => $blog->content,
                    'created_by' => $blog->creator?->name ?? 'Unknown',
                    'created_at' => $blog->created_at->format('d F Y, H:i'),
                    'is_published' => $blog->is_published,
                ]
            ]
        ]);
    }

    public function store(Request $request)
    {
       Log::info([
            'req' => $request->all()
       ]);
        $validated = $request->validate([
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'judul' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blogs,slug',
            'type' => 'required|in:acara,tutorial,pengumuman,berita,tips',
            'content' => 'required|string',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('blog-thumbnails', 'public');
        }

        $validated['created_by'] = Auth::user()->id;

        Blog::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Blog berhasil dibuat!'
        ]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('blog-content', 'public');
                
                return response()->json([
                    'success' => true,
                    'url' => asset('storage/' . $path)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image file provided'
            ], 400);

        } catch (\Exception $e) {
            Log::error("Error upload gambar", [
                'message' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $validated = $request->validate([
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'judul' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blogs,slug,' . $id,
            'type' => 'required|in:acara,tutorial,pengumuman,berita,tips',
            'content' => 'required|string',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($blog->thumbnail) {
                Storage::disk('public')->delete($blog->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('blog-thumbnails', 'public');
        }

        $this->cleanupOrphanImages($blog->content, $validated['content']);

        $blog->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Blog berhasil diupdate!'
        ]);
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);


        if ($blog->thumbnail) {
            Storage::disk('public')->delete($blog->thumbnail);
        }


        $contentImages = $this->extractImageUrls($blog->content);
        foreach ($contentImages as $imageUrl) {
            if (strpos($imageUrl, 'storage/blog-images/') !== false) {
                $path = str_replace(asset('storage/'), '', $imageUrl);
                Storage::disk('public')->delete($path);
            }
        }

        $blog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog berhasil dihapus!'
        ]);
    }

    private function getTypeBadge($type)
    {
        $badges = [
            'acara' => '<span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">Acara</span>',
            'tutorial' => '<span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Tutorial</span>',
            'pengumuman' => '<span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Pengumuman</span>',
            'berita' => '<span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Berita</span>',
            'tips' => '<span class="bg-pink-100 text-pink-800 text-xs px-2 py-1 rounded">Tips</span>',
        ];

        return $badges[$type] ?? $type;
    }

    private function extractImageUrls($content)
    {
        preg_match_all('/<img[^>]+src="([^">]+)"/', $content, $matches);
        return $matches[1] ?? [];
    }

    private function cleanupOrphanImages($oldContent, $newContent)
    {
        $oldImages = $this->extractImageUrls($oldContent);
        $newImages = $this->extractImageUrls($newContent);
        
    
        $deletedImages = array_diff($oldImages, $newImages);
        
        foreach ($deletedImages as $imageUrl) {
            
            if (strpos($imageUrl, 'storage/blog-images/') !== false) {
                $path = str_replace(asset('storage/'), '', $imageUrl);
                Storage::disk('public')->delete($path);
            }
        }
    }
}