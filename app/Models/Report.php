<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'category',
        'priority',
        'description',
        'location',
        'image',
        'status',
        'response',
        'responded_at',
        'responded_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the report.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who responded to the report.
     */
    public function respondedBy()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    /**
     * Get the category emoji.
     */
    public function getCategoryEmojiAttribute(): string
    {
        return match ($this->category) {
            'infrastruktur' => '🏗️',
            'kebersihan' => '🧹',
            'keamanan' => '🔒',
            'sosial' => '👥',
            'lainnya' => '⚡',
            default => '📋'
        };
    }

    /**
     * Get the priority emoji.
     */
    public function getPriorityEmojiAttribute(): string
    {
        return match ($this->priority) {
            'low' => '🟢',
            'medium' => '🟡',
            'high' => '🔴',
            default => '⚪'
        };
    }

    /**
     * Get the status color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => '#f59e0b',
            'in_progress' => '#3b82f6',
            'completed' => '#10b981',
            'rejected' => '#ef4444',
            default => '#6b7280'
        };
    }

    /**
     * Get the status text.
     */
    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'in_progress' => 'Sedang Ditangani',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Get the category text.
     */
    public function getCategoryTextAttribute(): string
    {
        return match ($this->category) {
            'infrastruktur' => 'Infrastruktur',
            'kebersihan' => 'Kebersihan',
            'keamanan' => 'Keamanan',
            'sosial' => 'Sosial',
            'lainnya' => 'Lainnya',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Get the priority text.
     */
    public function getPriorityTextAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for filtering by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by user's RT
     */
    public function scopeByRt($query, $rt)
    {
        return $query->whereHas('user', function ($q) use ($rt) {
            $q->where('rt', $rt);
        });
    }

    /**
     * Scope for pending reports
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for active reports (not completed or rejected)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'rejected']);
    }
}