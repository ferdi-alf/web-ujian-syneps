<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'content',
        'media_path',
        'media_type',
        'likes_count',
        'comments_count'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class, 'post_id')->whereNull('parent_id')->with('replies');
    }

    public function likes()
    {
        return $this->hasMany(ForumLike::class, 'post_id');
    }

    // Helper methods
    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function toggleLike($userId)
    {
        $like = $this->likes()->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
            $this->decrement('likes_count');
            return false;
        } else {
            $this->likes()->create(['user_id' => $userId]);
            $this->increment('likes_count');
            return true;
        }
    }

    // Scopes
    public function scopeWithUserAndCounts($query)
    {
        return $query->with('user')
            ->withCount(['comments', 'likes'])
            ->orderBy('created_at', 'desc');
    }
}
