<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Poll extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category',
        'end_date',
        'allow_multiple',
        'anonymous',
        'notify_result',
        'status',
        'created_by'
    ];

    protected $casts = [
        'end_date' => 'datetime',
        'allow_multiple' => 'boolean',
        'anonymous' => 'boolean',
        'notify_result' => 'boolean',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function options()
    {
        return $this->hasMany(PollOption::class)->orderBy('order');
    }

    public function votes()
    {
        return $this->hasMany(PollVote::class);
    }

    public function comments()
    {
        return $this->hasMany(PollComment::class)->whereNull('parent_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('end_date', '>', now());
    }

    public function scopeEnded($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'closed')
                ->orWhere('end_date', '<=', now());
        });
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' && $this->end_date > now();
    }

    public function getHasEndedAttribute()
    {
        return $this->status === 'closed' || $this->end_date <= now();
    }

    public function getTotalVotesAttribute()
    {
        return $this->votes()->count();
    }

    public function getUniqueVotersAttribute()
    {
        return $this->votes()->distinct('user_id')->count();
    }

    public function getParticipationPercentageAttribute()
    {
        $totalUsers = User::where('status', 'active')->count();
        $uniqueVoters = $this->unique_voters;

        return $totalUsers > 0 ? round(($uniqueVoters / $totalUsers) * 100, 1) : 0;
    }

    // Methods
    public function hasUserVoted($userId)
    {
        if (!$userId)
            return false;

        return $this->votes()->where('user_id', $userId)->exists();
    }

    public function getUserVote($userId)
    {
        return $this->votes()->where('user_id', $userId)->with('option')->get();
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->end_date > now();
    }

    public function hasEnded()
    {
        return $this->status === 'closed' || $this->end_date <= now();
    }
}