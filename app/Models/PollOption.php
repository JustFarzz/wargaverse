<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'option_text',
        'order',
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'poll_option_id');
    }

    public function getVoteCountAttribute()
    {
        return $this->votes()->count();
    }

    public function getVotePercentageAttribute()
    {
        $totalVotes = $this->poll->votes()->count();
        if ($totalVotes === 0) {
            return 0;
        }
        return round(($this->votes()->count() / $totalVotes) * 100, 1);
    }
}