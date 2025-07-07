<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;

    // Tentukan nama tabel yang benar
    protected $table = 'pollings';

    protected $fillable = [
        'title',
        'description',
        'category',
        'end_date',
        'allow_multiple',
        'anonymous',
        'notify_result',
        'user_id',
        'status',
        'rt',
        'rw',
    ];

    protected $casts = [
        'end_date' => 'datetime',
        'allow_multiple' => 'boolean',
        'anonymous' => 'boolean',
        'notify_result' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function options()
    {
        return $this->hasMany(PollOption::class, 'poll_id')->orderBy('order');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'poll_id');
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->end_date > now();
    }

    public function hasUserVoted($userId)
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }

    public function getTotalVotesAttribute()
    {
        return $this->votes()->count();
    }

    public function getTotalParticipantsAttribute()
    {
        return $this->votes()->distinct('user_id')->count();
    }

    // Scope untuk lokasi RT/RW
    public function scopeByLocation($query, $rt, $rw)
    {
        return $query->where('rt', $rt)->where('rw', $rw);
    }

    // Scope untuk polling aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('end_date', '>', now());
    }
}