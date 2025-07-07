<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'poll_option_id',
        'user_id',
        'rt',
        'rw',
        'ip_address',
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id');
    }

    public function pollOption()
    {
        return $this->belongsTo(PollOption::class, 'poll_option_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk lokasi RT/RW
    public function scopeByLocation($query, $rt, $rw)
    {
        return $query->where('rt', $rt)->where('rw', $rw);
    }

    // Scope untuk user tertentu
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}