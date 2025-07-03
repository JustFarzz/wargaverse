<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'address',
        'rt',
        'rw',
        'password',
        'role',
        'status',
        'last_login_at',
        'last_login_ip',
        'last_seen_at',
        'registered_at',
        'registered_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_login_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'registered_at' => 'datetime',
        ];
    }

    /**
     * Get full address with RT/RW
     */
    public function getFullAddressAttribute(): string
    {
        return $this->address . ' RT ' . $this->rt . '/RW ' . $this->rw;
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute(): string
    {
        return preg_replace('/(\d{2})(\d{4})(\d{4,})/', '$1-$2-$3', $this->phone);
    }

    /**
     * Scope for filtering by RT
     */
    public function scopeByRt($query, $rt)
    {
        return $query->where('rt', $rt);
    }

    /**
     * Scope for filtering by RW
     */
    public function scopeByRw($query, $rw)
    {
        return $query->where('rw', $rw);
    }

    /**
     * Scope for active users only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
