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

    /**
     * Get the full URL of the image
     */
    public function getUrlAttribute(): string
    {
        $filename = $this->filename;

        // Path di storage
        $storagePath = storage_path('app/public/posts/' . $filename);

        // Path di public (melalui symbolic link)
        $publicPath = public_path('storage/posts/' . $filename);

        // Periksa apakah file ada di storage
        if (file_exists($storagePath)) {
            // Periksa apakah symbolic link berfungsi
            if (file_exists($publicPath)) {
                return asset('storage/posts/' . $filename);
            } else {
                // Symbolic link tidak ada atau rusak
                \Log::warning('Symbolic link issue detected', [
                    'filename' => $filename,
                    'storage_exists' => file_exists($storagePath),
                    'public_exists' => file_exists($publicPath),
                    'symlink_exists' => is_link(public_path('storage')),
                ]);

                // Fallback: buat URL langsung ke storage (tidak direkomendasikan untuk production)
                return route('image.serve', ['filename' => $filename]);
            }
        }

        // File tidak ditemukan
        \Log::error('Image file not found', [
            'filename' => $filename,
            'storage_path' => $storagePath,
            'public_path' => $publicPath,
        ]);

        return asset('images/default-image.png');
    }

    /**
     * Alternative method untuk mendapatkan URL
     */
    public function getAssetUrlAttribute(): string
    {
        return asset('storage/posts/' . $this->filename);
    }

    /**
     * Method untuk mendapatkan URL langsung
     */
    public function getDirectUrlAttribute(): string
    {
        return url('storage/posts/' . $this->filename);
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