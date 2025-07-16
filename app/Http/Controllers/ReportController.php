<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Report::with('user')
            ->when($request->category, fn($q) => $q->byCategory($request->category))
            ->when($request->priority, fn($q) => $q->byPriority($request->priority))
            ->when($request->status, fn($q) => $q->byStatus($request->status))
            ->when($request->search, function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->orderBy('created_at', 'desc');

        // Filter by user's RT if not admin
        if (Auth::user()->role !== 'admin') {
            $query->byRt(Auth::user()->rt);
        }

        $reports = $query->paginate(12);

        return view('laporan.index', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('laporan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:infrastruktur,kebersihan,keamanan,sosial,lainnya',
            'priority' => 'required|in:low,medium,high',
            'description' => 'required|string|max:1000',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        $imagePath = null;

        // Handle file upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Generate unique filename
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

            // Store image
            $imagePath = $image->storeAs('reports', $imageName, 'public');
        }

        // Create report
        Report::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'category' => $request->category,
            'priority' => $request->priority,
            'description' => $request->description,
            'location' => $request->location,
            'image' => $imagePath,
            'status' => 'pending',
        ]);

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dibuat dan akan segera ditinjau oleh pengurus RT.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        // Load relationships
        $report->load(['user', 'respondedBy']);

        // Check if user can view this report
        if (Auth::user()->role !== 'admin' && $report->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat laporan ini.');
        }

        return view('laporan.show', compact('report'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        // Check if user can edit this report
        if (Auth::user()->role !== 'admin' && $report->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit laporan ini.');
        }

        // Only allow editing if report is still pending
        if ($report->status !== 'pending') {
            return redirect()->route('laporan.show', $report)->with('error', 'Laporan tidak dapat diedit karena sudah diproses.');
        }

        return view('laporan.edit', compact('report'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        // Check if user can update this report
        if (Auth::user()->role !== 'admin' && $report->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate laporan ini.');
        }

        // Only allow updating if report is still pending
        if ($report->status !== 'pending') {
            return redirect()->route('laporan.show', $report)->with('error', 'Laporan tidak dapat diupdate karena sudah diproses.');
        }

        // Validation
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:infrastruktur,kebersihan,keamanan,sosial,lainnya',
            'priority' => 'required|in:low,medium,high',
            'description' => 'required|string|max:1000',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        $imagePath = $report->image;

        // Handle file upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($report->image && Storage::disk('public')->exists($report->image)) {
                Storage::disk('public')->delete($report->image);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('reports', $imageName, 'public');
        }

        // Update report
        $report->update([
            'title' => $request->title,
            'category' => $request->category,
            'priority' => $request->priority,
            'description' => $request->description,
            'location' => $request->location,
            'image' => $imagePath,
        ]);

        return redirect()->route('laporan.show', $report)->with('success', 'Laporan berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        // Check if user can delete this report
        if (Auth::user()->role !== 'admin' && $report->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus laporan ini.');
        }

        // Delete image if exists
        if ($report->image && Storage::disk('public')->exists($report->image)) {
            Storage::disk('public')->delete($report->image);
        }

        $report->delete();

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dihapus.');
    }

    /**
     * Update report status (Admin only)
     */
    public function updateStatus(Request $request, Report $report)
    {
        // Only admin can update status
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengubah status laporan.');
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,rejected'
        ]);

        $report->update([
            'status' => $request->status,
        ]);

        return redirect()->route('laporan.show', $report)->with('success', 'Status laporan berhasil diupdate.');
    }

    /**
     * Add response to report (Admin only)
     */
    public function respond(Request $request, Report $report)
    {
        // Only admin can respond
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk menanggapi laporan.');
        }

        $request->validate([
            'response' => 'required|string|max:1000',
            'status' => 'nullable|in:pending,in_progress,completed,rejected'
        ]);

        $report->update([
            'response' => $request->response,
            'responded_at' => now(),
            'responded_by' => Auth::id(),
            'status' => $request->status ?? $report->status,
        ]);

        return redirect()->route('laporan.show', $report)->with('success', 'Tanggapan berhasil ditambahkan.');
    }

    /**
     * Get report statistics (for dashboard)
     */
    public function getStats()
    {
        $stats = [
            'total' => Report::count(),
            'pending' => Report::where('status', 'pending')->count(),
            'in_progress' => Report::where('status', 'in_progress')->count(),
            'completed' => Report::where('status', 'completed')->count(),
            'rejected' => Report::where('status', 'rejected')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get recent reports for dashboard
     */
    public function getRecentReports($limit = 5)
    {
        $reports = Report::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($reports);
    }
}