<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
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
            return response()->json([
                'success' => true,
                'data' => [
                    'dataUpdate' => [
                        'id' => $blog->id,
                        'thumbnail' => $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : null,
                        'judul' => $blog->judul,
                        'slug' => $blog->slug,
                        'type' => $blog->type,
                        'content' => $blog->content,
                        'is_published' => $blog->is_published,
                    ]
                ]
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
        $validated = $request->validate([
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'judul' => 'required|string|max:255',
            'type' => 'required|in:acara,tutorial,pengumuman,berita,tips',
            'content' => 'required|string',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('blog-thumbnails', 'public');
        }

        $validated['created_by'] = Auth::user()->id;
        $validated['slug'] = Str::slug($validated['judul']);

        Blog::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Blog berhasil dibuat!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $validated = $request->validate([
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'judul' => 'required|string|max:255',
            'type' => 'required|in:acara,tutorial,pengumuman,berita,tips',
            'content' => 'required|string',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            // Hapus thumbnail lama
            if ($blog->thumbnail) {
                Storage::disk('public')->delete($blog->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('blog-thumbnails', 'public');
        }

        // Slug otomatis update jika judul berubah (handled by model)
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
}