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
    public function index(Request $request, $act = null)
    {
   
        if ($act === 'create') {
            return view('Dashboard.BlogForm', [
                'mode' => 'create',
                'blog' => null
            ]);
        }

      
        $perPage = $request->get('per_page', 9);
        $search = $request->get('search', '');
        $page = $request->get('page', 1);

       
        $query = Blog::with('creator')->latest();

    
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

     
        $blogsPaginated = $query->paginate($perPage);

       
        $blogs = $blogsPaginated->map(function ($blog) {
            return [
                'id' => $blog->id,
                'thumbnail' => $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : asset('images/default-blog.jpg'),
                'judul' => $blog->judul,
                'slug' => $blog->slug,
                'type' => $blog->type,
                'type_badge' => $this->getTypeBadge($blog->type),
                'excerpt' => Str::limit(strip_tags($blog->content), 150),
                'created_by' => $blog->creator?->name ?? 'Unknown',
                'created_at' => $blog->created_at->format('d M Y'),
                'is_published' => $blog->is_published,
            ];
        });

      
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $blogs,
                'meta' => [
                    'current_page' => $blogsPaginated->currentPage(),
                    'last_page' => $blogsPaginated->lastPage(),
                    'per_page' => $blogsPaginated->perPage(),
                    'total' => $blogsPaginated->total(),
                    'from' => $blogsPaginated->firstItem(),
                    'to' => $blogsPaginated->lastItem(),
                ]
            ]);
        }

       
        return view('Dashboard.Blog', compact('blogs'));
    }

    public function show( $slug, $act = null)
    {
        $blog = Blog::where('slug', $slug)->with('creator')->firstOrFail();

        if ($act === 'edit') {
            return view('Dashboard.BlogForm', [
                'mode' => 'edit',
                'blog' => $blog
            ]);
        }

        if ($blog) {
            log::info('data blog ditemukan', [
                'data' => $blog
            ]);
        }else{
            log::error('ga ada jir');
        }

   
        return response()->json([
            'success' => true,
            'data' => [
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
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Store Blog Request', [
            'all_data' => $request->all(),
            'has_file' => $request->hasFile('thumbnail'),
            'files' => $request->allFiles()
        ]);

        $validated = $request->validate([
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'judul' => 'required|string|max:255',
            'type' => 'required|in:acara,tutorial,pengumuman,berita,tips',
            'content' => 'required|string',
            'is_published' => 'boolean',
        ]);

        $validated['slug'] = $this->generateUniqueSlug($validated['judul']);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('blog-thumbnails', 'public');
            Log::info('Thumbnail uploaded', ['path' => $validated['thumbnail']]);
        }

        $validated['created_by'] = Auth::id();

        $blog = Blog::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Blog berhasil dibuat!',
            'data' => $blog
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
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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

        Log::info('Update Blog Request', [
            'blog_id' => $id,
            'all_data' => $request->all(),
            'has_file' => $request->hasFile('thumbnail'),
            'files' => $request->allFiles()
        ]);

        $validated = $request->validate([
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'judul' => 'required|string|max:255',
            'type' => 'required|in:acara,tutorial,pengumuman,berita,tips',
            'content' => 'required|string',
            'is_published' => 'boolean',
        ]);

 
        if ($validated['judul'] !== $blog->judul) {
            $validated['slug'] = $this->generateUniqueSlug($validated['judul'], $id);
        }

        if ($request->hasFile('thumbnail')) {
            if ($blog->thumbnail) {
                Storage::disk('public')->delete($blog->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('blog-thumbnails', 'public');
            Log::info('Thumbnail updated', ['path' => $validated['thumbnail']]);
        }

        $this->cleanupOrphanImages($blog->content, $validated['content']);

        $blog->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Blog berhasil diupdate!',
            'data' => $blog
        ]);
    }

    public function destroy($id)
    {
        try {
            $blog = Blog::findOrFail($id);

            if ($blog->thumbnail) {
                Storage::disk('public')->delete($blog->thumbnail);
            }

            $contentImages = $this->extractImageUrls($blog->content);
            foreach ($contentImages as $imageUrl) {
                if (strpos($imageUrl, 'storage/blog-content/') !== false) {
                    $path = str_replace(asset('storage/'), '', $imageUrl);
                    Storage::disk('public')->delete($path);
                }
            }

            $blog->delete();

            return response()->json([
                'success' => true,
                'message' => 'Blog berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting blog', [
                'blog_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus blog: ' . $e->getMessage()
            ], 500);
        }
    }

 
    private function generateUniqueSlug($judul, $ignoreId = null)
    {
        $slug = Str::slug($judul);
        $originalSlug = $slug;
        $count = 1;

        while (true) {
            $query = Blog::where('slug', $slug);
            
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                break;
            }

            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }


    private function getTypeBadge($type)
    {
        $badges = [
            'acara' => '<span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full font-medium">Acara</span>',
            'tutorial' => '<span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-medium">Tutorial</span>',
            'pengumuman' => '<span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-medium">Pengumuman</span>',
            'berita' => '<span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-medium">Berita</span>',
            'tips' => '<span class="bg-pink-100 text-pink-800 text-xs px-2 py-1 rounded-full font-medium">Tips</span>',
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
            if (strpos($imageUrl, 'storage/blog-content/') !== false) {
                $path = str_replace(asset('storage/'), '', $imageUrl);
                Storage::disk('public')->delete($path);
                Log::info('Orphan image deleted', ['path' => $path]);
            }
        }
    }
}