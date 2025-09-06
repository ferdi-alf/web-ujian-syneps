    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
>>>>>>> forum
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
<<<<<<< HEAD
        return $this->hasMany(Comment::class, 'post_id')->whereNull('parent_id')->with('user', 'replies');
    }

    public function allComments()
    {
        return $this->hasMany(Comment::class, 'post_id');
        return $this->hasMany(ForumComment::class, 'post_id')->whereNull('parent_id')->with('replies');
>>>>>>> forum
    }

    public function likes()
    {
<<<<<<< HEAD
        return $this->hasMany(Like::class, 'post_id');
    }

        return $this->hasMany(ForumLike::class, 'post_id');
    }

    // Helper methods
>>>>>>> forum
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
<<<<<<< HEAD
            $this->likes()->create(['user_id' => $userId, 'post_id' => $this->id]);
            $this->likes()->create(['user_id' => $userId]);
>>>>>>> forum
            $this->increment('likes_count');
            return true;
        }
    }
<<<<<<< HEAD

    // Scopes
    public function scopeWithUserAndCounts($query)
    {
        return $query->with('user')
                    ->withCount(['comments', 'likes'])
                    ->orderBy('created_at', 'desc');
    }
>>>>>>> forum
}
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
=======
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
>>>>>>> forum
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
<<<<<<< HEAD
        return $this->hasMany(Comment::class, 'post_id')->whereNull('parent_id')->with('user', 'replies');
    }

    public function allComments()
    {
        return $this->hasMany(Comment::class, 'post_id');
=======
        return $this->hasMany(ForumComment::class, 'post_id')->whereNull('parent_id')->with('replies');
>>>>>>> forum
    }

    public function likes()
    {
<<<<<<< HEAD
        return $this->hasMany(Like::class, 'post_id');
    }

=======
        return $this->hasMany(ForumLike::class, 'post_id');
    }

    // Helper methods
>>>>>>> forum
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
<<<<<<< HEAD
            $this->likes()->create(['user_id' => $userId, 'post_id' => $this->id]);
=======
            $this->likes()->create(['user_id' => $userId]);
>>>>>>> forum
            $this->increment('likes_count');
            return true;
        }
    }
<<<<<<< HEAD
=======

    // Scopes
    public function scopeWithUserAndCounts($query)
    {
        return $query->with('user')
                    ->withCount(['comments', 'likes'])
                    ->orderBy('created_at', 'desc');
    }
>>>>>>> forum
}
