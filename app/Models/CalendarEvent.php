<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'event_date',
        'start_time',
        'end_time',
        'location',
        'location_detail',
        'organizer',
        'contact_person',
        'max_participants',
        'requirements',
        'is_registration_required',
        'is_reminder_active',
        'attachment',
        'created_by',
        'status',
    ];

    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_registration_required' => 'boolean',
        'is_reminder_active' => 'boolean',
    ];

    /**
     * Get the user who created the event
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get category display name
     */
    public function getCategoryDisplayAttribute()
    {
        $categories = [
            'rapat' => 'Rapat RT',
            'gotong_royong' => 'Gotong Royong',
            'keamanan' => 'Keamanan',
            'sosial' => 'Kegiatan Sosial',
            'olahraga' => 'Olahraga',
            'keagamaan' => 'Kegiatan Keagamaan',
            'perayaan' => 'Perayaan',
            'lainnya' => 'Lainnya'
        ];

        return $categories[$this->category] ?? $this->category;
    }

    /**
     * Get formatted event date
     */
    public function getFormattedDateAttribute()
    {
        return $this->event_date->format('d M Y');
    }

    /**
     * Get formatted date with day name
     */
    public function getFormattedDateWithDayAttribute()
    {
        return $this->event_date->format('l, d F Y');
    }

    /**
     * Get formatted start time
     */
    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time ? date('H:i', strtotime($this->start_time)) : null;
    }

    /**
     * Get formatted end time
     */
    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time ? date('H:i', strtotime($this->end_time)) : null;
    }

    /**
     * Get time range display
     */
    public function getTimeRangeAttribute()
    {
        $startTime = $this->formatted_start_time;
        $endTime = $this->formatted_end_time;

        if ($startTime && $endTime) {
            return $startTime . ' - ' . $endTime;
        }

        return $startTime ?? 'Waktu tidak ditentukan';
    }

    /**
     * Get attachment URL
     */
    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? Storage::url($this->attachment) : null;
    }

    /**
     * Get attachment file name
     */
    public function getAttachmentNameAttribute()
    {
        return $this->attachment ? basename($this->attachment) : null;
    }

    /**
     * Check if event is today
     */
    public function getIsTodayAttribute()
    {
        return $this->event_date->isToday();
    }

    /**
     * Check if event is upcoming
     */
    public function getIsUpcomingAttribute()
    {
        return $this->event_date->isFuture();
    }

    /**
     * Check if event is past
     */
    public function getIsPastAttribute()
    {
        return $this->event_date->isPast();
    }

    /**
     * Scope for upcoming events
     */
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now()->toDateString())
            ->where('status', 'published')
            ->orderBy('event_date', 'asc')
            ->orderBy('start_time', 'asc');
    }

    /**
     * Scope for events in a specific month
     */
    public function scopeInMonth($query, $year, $month)
    {
        return $query->whereYear('event_date', $year)
            ->whereMonth('event_date', $month);
    }

    /**
     * Scope for events by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for published events only
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Delete attachment file when event is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($event) {
            if ($event->attachment) {
                Storage::delete($event->attachment);
            }
        });
    }
}