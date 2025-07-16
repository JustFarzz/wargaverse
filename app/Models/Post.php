<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'category',
        'price',
        'phone',
        'whatsapp',
        'status',
        'views_count',
        'is_featured',
        'featured_until',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:0',
            'views_count' => 'integer',
            'is_featured' => 'boolean',
            'featured_until' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the post
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the images for the post
     */
    public function images()
    {
        return $this->hasMany(PostImage::class)->orderBy('order');
    }

    /**
     * Get the first image for the post
     */
    public function firstImage()
    {
        return $this->hasOne(PostImage::class)->orderBy('order');
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        if (!$this->price) {
            return 'Nego';
        }

        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute(): string
    {
        if (!$this->phone) {
            return '';
        }

        return preg_replace('/(\d{2,4})(\d{4})(\d{4,})/', '$1-$2-$3', $this->phone);
    }

    /**
     * Get formatted WhatsApp number
     */
    public function getFormattedWhatsappAttribute(): string
    {
        if (!$this->whatsapp) {
            return '';
        }

        return preg_replace('/(\d{2,4})(\d{4})(\d{4,})/', '$1-$2-$3', $this->whatsapp);
    }

    /**
     * Get WhatsApp URL
     */
    public function getWhatsappUrlAttribute(): string
    {
        if (!$this->whatsapp) {
            return '';
        }

        $number = preg_replace('/[^0-9]/', '', $this->whatsapp);

        // Ensure it starts with 62 (Indonesia country code)
        if (substr($number, 0, 1) === '0') {
            $number = '62' . substr($number, 1);
        } elseif (substr($number, 0, 2) !== '62') {
            $number = '62' . $number;
        }

        $message = urlencode("Halo, saya tertarik dengan posting Anda: " . $this->title);

        return "https://wa.me/{$number}?text={$message}";
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute(): string
    {
        $categories = [
            'jual' => 'Jual Beli',
            'jasa' => 'Jasa',
            'info' => 'Info Umum'
        ];

        return $categories[$this->category] ?? $this->category;
    }

    /**
     * Get category color
     */
    public function getCategoryColorAttribute(): string
    {
        $colors = [
            'jual' => 'success',
            'jasa' => 'info',
            'info' => 'warning'
        ];

        return $colors[$this->category] ?? 'secondary';
    }

    /**
     * Get time ago
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get excerpt from content
     */
    public function getExcerptAttribute(): string
    {
        if (strlen($this->content) <= 150) {
            return $this->content;
        }

        return substr($this->content, 0, 150) . '...';
    }

    /**
     * Check if post has images
     */
    public function hasImages(): bool
    {
        return $this->images()->count() > 0;
    }

    /**
     * Get thumbnail URL (first image)
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        $firstImage = $this->firstImage;
        return $firstImage ? $firstImage->url : null;
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for active posts only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for featured posts
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->where('featured_until', '>', now());
    }

    /**
     * Scope for posts by RT
     */
    public function scopeByRt($query, $rt)
    {
        return $query->whereHas('user', function ($q) use ($rt) {
            $q->where('rt', $rt);
        });
    }

    /**
     * Scope for posts by RW
     */
    public function scopeByRw($query, $rw)
    {
        return $query->whereHas('user', function ($q) use ($rw) {
            $q->where('rw', $rw);
        });
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', '%' . $search . '%')
                ->orWhere('content', 'like', '%' . $search . '%');
        });
    }

    /**
     * Increment views count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Check if post has contact information
     */
    public function hasContact(): bool
    {
        return !empty($this->phone) || !empty($this->whatsapp);
    }

    /**
     * Check if post is by current user
     */
    public function isOwnedBy($user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Check if post can be edited
     */
    public function canBeEditedBy($user): bool
    {
        return $this->isOwnedBy($user) && $this->status === 'active';
    }

    /**
     * Check if post can be deleted
     */
    public function canBeDeletedBy($user): bool
    {
        return $this->isOwnedBy($user) || $user->role === 'admin';
    }
}