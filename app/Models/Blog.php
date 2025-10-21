<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    protected $fillable = [
        'thumbnail',
        'judul',
        'slug',
        'type',
        'content',
        'is_published',
        'created_by'
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->judul);
            }
        });

        static::updating(function ($blog) {
            if ($blog->isDirty('judul')) {
                $blog->slug = Str::slug($blog->judul);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}