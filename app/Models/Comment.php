<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'post_comments';

    protected $fillable = [
        'user_id',
        'post_id',
        'parent_id',
        'content'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function forumPost()
    {
        return $this->belongsTo(ForumPost::class, 'post_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user', 'replies');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'comment_id');
    }

    public function isReply()
    {
        return !is_null($this->parent_id);
    }
}
