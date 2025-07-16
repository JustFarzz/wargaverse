<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CalendarController extends Controller
{
    /**
     * Display calendar events
     */

    public function adminCreateevent()
    {
        return view('admin.createevent');
    }
    
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $category = $request->get('category');
        $view = $request->get('view', 'calendar'); // calendar, list

        $query = CalendarEvent::published()
            ->with('creator')
            ->inMonth($year, $month);

        if ($category) {
            $query->byCategory($category);
        }

        $events = $query->get();

        // Get upcoming events for sidebar
        $upcomingEvents = CalendarEvent::upcoming()
            ->with('creator')
            ->limit(5)
            ->get();

        // Get past events for this month
        $pastEvents = CalendarEvent::published()
            ->with('creator')
            ->whereDate('event_date', '<', now())
            ->inMonth($year, $month)
            ->orderBy('event_date', 'desc')
            ->limit(5)
            ->get();

        // Calculate statistics
        $totalEvents = CalendarEvent::published()->count();
        $upcomingCount = CalendarEvent::upcoming()->count();
        $completedCount = CalendarEvent::published()
            ->whereDate('event_date', '<', now())
            ->count();
        $totalParticipants = 0; // This would need to be calculated based on your attendance system

        return view('kalender.index', compact(
            'events',
            'upcomingEvents',
            'pastEvents',
            'month',
            'year',
            'category',
            'view',
            'totalEvents',
            'upcomingCount',
            'completedCount',
            'totalParticipants'
        ));
    }

    /**
     * Show create event form
     */
    public function create()
    {
        return view('kalender.create');
    }

    /**
     * Store new event
     */
    public function adminStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|in:rapat,gotong_royong,keamanan,sosial,olahraga,keagamaan,perayaan,lainnya',
            'event_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'location_detail' => 'nullable|string',
            'organizer' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'requirements' => 'nullable|string',
            'is_registration_required' => 'boolean',
            'is_reminder_active' => 'boolean',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['is_registration_required'] = $request->has('is_registration_required');
        $data['is_reminder_active'] = $request->has('is_reminder_active');

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['attachment'] = $file->storeAs('calendar_events', $filename, 'public');
        }

        $event = CalendarEvent::create($data);

        return redirect()->route('kalender.index')
            ->with('success', 'Kegiatan berhasil ditambahkan!');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|in:rapat,gotong_royong,keamanan,sosial,olahraga,keagamaan,perayaan,lainnya',
            'event_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'location_detail' => 'nullable|string',
            'organizer' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'requirements' => 'nullable|string',
            'is_registration_required' => 'boolean',
            'is_reminder_active' => 'boolean',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['is_registration_required'] = $request->has('is_registration_required');
        $data['is_reminder_active'] = $request->has('is_reminder_active');

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['attachment'] = $file->storeAs('calendar_events', $filename, 'public');
        }

        $event = CalendarEvent::create($data);

        return redirect()->route('kalender.index')
            ->with('success', 'Kegiatan berhasil ditambahkan!');
    }

    /**
     * Display specific event
     */
    public function show(CalendarEvent $event)
    {
        $event->load('creator');
        return view('kalender.show', compact('event'));
    }

    /**
     * Show edit form
     */
    public function edit(CalendarEvent $event)
    {
        // Check if user can edit this event
        if ($event->created_by !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki izin untuk mengedit kegiatan ini.');
        }

        return view('kalender.edit', compact('event'));
    }

    /**
     * Update event
     */
    public function update(Request $request, CalendarEvent $event)
    {
        // Check if user can edit this event
        if ($event->created_by !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki izin untuk mengedit kegiatan ini.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|in:rapat,gotong_royong,keamanan,sosial,olahraga,keagamaan,perayaan,lainnya',
            'event_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'location_detail' => 'nullable|string',
            'organizer' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'requirements' => 'nullable|string',
            'is_registration_required' => 'boolean',
            'is_reminder_active' => 'boolean',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['is_registration_required'] = $request->has('is_registration_required');
        $data['is_reminder_active'] = $request->has('is_reminder_active');

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($event->attachment) {
                Storage::delete($event->attachment);
            }

            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['attachment'] = $file->storeAs('calendar_events', $filename, 'public');
        }

        $event->update($data);

        return redirect()->route('kalender.show', $event)
            ->with('success', 'Kegiatan berhasil diperbarui!');
    }

    /**
     * Delete event
     */
    public function destroy(CalendarEvent $event)
    {
        // Check if user can delete this event
        if ($event->created_by !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki izin untuk menghapus kegiatan ini.');
        }

        $event->delete();

        return redirect()->route('kalender.index')
            ->with('success', 'Kegiatan berhasil dihapus!');
    }

    /**
     * Get events for calendar API
     */
    public function getEvents(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $events = CalendarEvent::published()
            ->with('creator')
            ->inMonth($year, $month)
            ->get();

        $eventsData = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'event_date' => $event->event_date->format('Y-m-d'),
                'start_time' => $event->formatted_start_time,
                'end_time' => $event->formatted_end_time,
                'time_range' => $event->time_range,
                'location' => $event->location,
                'category' => $event->category,
                'category_display' => $event->category_display,
                'url' => route('kalender.show', $event->id)
            ];
        });

        return response()->json($eventsData);
    }

    /**
     * Get upcoming events
     */
    public function getUpcomingEvents()
    {
        $events = CalendarEvent::upcoming()
            ->with('creator')
            ->limit(10)
            ->get();

        return response()->json($events);
    }

    /**
     * Mark attendance for event
     */
    public function attend(CalendarEvent $event)
    {
        // This would be implemented if you want attendance tracking
        // For now, just return success
        return response()->json(['message' => 'Kehadiran berhasil dikonfirmasi']);
    }

    /**
     * Cancel attendance
     */
    public function cancelAttendance(CalendarEvent $event)
    {
        // This would be implemented if you want attendance tracking
        // For now, just return success
        return response()->json(['message' => 'Kehadiran berhasil dibatalkan']);
    }

    /**
     * Get color for event category
     */
    private function getCategoryColor($category)
    {
        $colors = [
            'rapat' => '#3498db',
            'gotong_royong' => '#2ecc71',
            'keamanan' => '#e74c3c',
            'sosial' => '#9b59b6',
            'olahraga' => '#f39c12',
            'keagamaan' => '#1abc9c',
            'perayaan' => '#e67e22',
            'lainnya' => '#95a5a6'
        ];

        return $colors[$category] ?? '#95a5a6';
    }
}