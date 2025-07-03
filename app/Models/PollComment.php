<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'user_id',
        'parent_id',
        'comment'
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(PollComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(PollComment::class, 'parent_id');
    }

    public function likes()
    {
        return $this->hasMany(PollCommentLike::class, 'comment_id');
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }
}
