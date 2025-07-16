<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

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
        'birth_date',
        'gender',
        'occupation',
        'bio',
        'address',
        'rt',
        'rw',
        'rt_number',
        'rw_number',
        'kelurahan',
        'kecamatan',
        'city',
        'postal_code',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'whatsapp',
        'password',
        'role',
        'status',
        'avatar',
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
            'birth_date' => 'date',
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
        $address = $this->address;

        if ($this->rt) {
            $address .= ' RT ' . $this->rt;
        }

        if ($this->rw) {
            $address .= '/RW ' . $this->rw;
        }

        return $address;
    }

    /**
     * Get complete address with all fields
     */
    public function getCompleteAddressAttribute(): string
    {
        $addressParts = [];

        if ($this->address) {
            $addressParts[] = $this->address;
        }

        if ($this->rt_number) {
            $addressParts[] = 'RT ' . $this->rt_number;
        }

        if ($this->rw_number) {
            $addressParts[] = 'RW ' . $this->rw_number;
        }

        if ($this->kelurahan) {
            $addressParts[] = $this->kelurahan;
        }

        if ($this->kecamatan) {
            $addressParts[] = $this->kecamatan;
        }

        if ($this->city) {
            $addressParts[] = $this->city;
        }

        if ($this->postal_code) {
            $addressParts[] = $this->postal_code;
        }

        return implode(', ', $addressParts);
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute(): string
    {
        return preg_replace('/(\d{2})(\d{4})(\d{4,})/', '$1-$2-$3', $this->phone);
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return Storage::url('avatars/' . $this->avatar);
        }

        // Default avatar berdasarkan inisial nama
        $initials = collect(explode(' ', $this->name))
            ->map(function ($name) {
                return strtoupper(substr($name, 0, 1));
            })
            ->take(2)
            ->implode('');

        return "https://ui-avatars.com/api/?name={$initials}&background=random&color=fff&size=150";
    }

    /**
     * Get user initials
     */
    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))
            ->map(function ($name) {
                return strtoupper(substr($name, 0, 1));
            })
            ->take(2)
            ->implode('');
    }

    /**
     * Get age from birth date
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->birth_date) {
            return null;
        }

        return $this->birth_date->diffInYears(now());
    }

    /**
     * Get formatted WhatsApp number
     */
    public function getFormattedWhatsappAttribute(): ?string
    {
        if (!$this->whatsapp) {
            return null;
        }

        // Format WhatsApp number for URL
        $number = preg_replace('/[^0-9]/', '', $this->whatsapp);

        // Add country code if not present
        if (!str_starts_with($number, '62')) {
            $number = '62' . ltrim($number, '0');
        }

        return $number;
    }

    /**
     * Get WhatsApp URL
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        if (!$this->whatsapp) {
            return null;
        }

        return 'https://wa.me/' . $this->formatted_whatsapp;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is warga
     */
    public function isWarga(): bool
    {
        return $this->role === 'warga';
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is verified
     */
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    /**
     * Check if user is blocked
     */
    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    /**
     * Check if profile is complete
     */
    public function isProfileComplete(): bool
    {
        return !empty($this->name) &&
            !empty($this->email) &&
            !empty($this->phone) &&
            !empty($this->address) &&
            !empty($this->rt) &&
            !empty($this->rw);
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
     * Scope for filtering by RT number
     */
    public function scopeByRtNumber($query, $rtNumber)
    {
        return $query->where('rt_number', $rtNumber);
    }

    /**
     * Scope for filtering by RW number
     */
    public function scopeByRwNumber($query, $rwNumber)
    {
        return $query->where('rw_number', $rwNumber);
    }

    /**
     * Scope for active users only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for verified users only
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope for admin users only
     */
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope for warga users only
     */
    public function scopeWarga($query)
    {
        return $query->where('role', 'warga');
    }

    /**
     * Scope for users with complete profile
     */
    public function scopeCompleteProfile($query)
    {
        return $query->whereNotNull('name')
            ->whereNotNull('email')
            ->whereNotNull('phone')
            ->whereNotNull('address')
            ->whereNotNull('rt')
            ->whereNotNull('rw');
    }

    /**
     * Relationship with posts (if you have posts table)
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Relationship with reports (if you have reports table)
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Relationship with poll votes (if you have poll_votes table)
     */
    public function pollVotes()
    {
        return $this->hasMany(PollVote::class);
    }

    /**
     * Relationship with event attendances (if you have event_attendances table)
     */
    public function eventAttendances()
    {
        return $this->hasMany(EventAttendance::class);
    }

    /**
     * Relationship with finance transactions (if you have finance_transactions table)
     */
    public function financeTransactions()
    {
        return $this->hasMany(FinanceTransaction::class);
    }
}