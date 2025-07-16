<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PostImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'post_id',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the post that owns the image
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the full URL of the image
     */
    public function getUrlAttribute(): string
    {
        return Storage::url('posts/' . $this->filename);
    }

    /**
     * Get the full path of the image
     */
    public function getPathAttribute(): string
    {
        return Storage::path('posts/' . $this->filename);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Delete the image file from storage
     */
    public function deleteFile(): bool
    {
        if (Storage::exists('posts/' . $this->filename)) {
            return Storage::delete('posts/' . $this->filename);
        }
        return true;
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            $image->deleteFile();
        });
    }
}