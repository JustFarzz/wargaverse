<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Post;
use App\Models\Report;
use App\Models\Poll;
use App\Models\CalendarEvent;
use App\Models\FinanceTransaction; // Gunakan model yang konsisten
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard - mengarah ke views/admin/dashboard.blade.php
     */
    public function index(): View
    {
        // Pastikan user adalah admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // Ambil statistik untuk dashboard
        $stats = [
            'total_warga' => User::where('role', 'warga')->count(),
            'total_posts' => Post::count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'active_polls' => Poll::where('status', 'active')->count(),
            'upcoming_events' => CalendarEvent::where('event_date', '>', now())->count(),
        ];

        // Ambil data recent activities
        $recentActivities = $this->getRecentActivities();

        // Ambil data transaksi terbaru untuk dashboard - menggunakan FinanceTransaction
        $recentTransactions = FinanceTransaction::with('user')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Ambil data laporan yang perlu ditangani
        $pendingReports = Report::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Hitung current balance menggunakan method yang konsisten
        $currentBalance = $this->getCurrentBalance();

        // Hitung summary menggunakan method yang konsisten
        $summary = $this->getSummary();

        return view('admin.dashboard', compact(
            'stats',
            'recentActivities',
            'recentTransactions',
            'pendingReports',
            'currentBalance',
            'summary'
        ));
    }

    /**
     * Get current balance - Method yang disesuaikan dengan FinanceTransaction
     */
    private function getCurrentBalance()
    {
        // Hitung total income dan expense menggunakan FinanceTransaction
        $totalIncome = FinanceTransaction::where('type', 'income')
            ->sum('amount');

        $totalExpense = FinanceTransaction::where('type', 'expense')
            ->sum('amount');

        return $totalIncome - $totalExpense;
    }

    /**
     * Get financial summary - Method yang disesuaikan dengan FinanceTransaction
     */
    private function getSummary()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Gunakan transaction_date sesuai dengan struktur FinanceTransaction
        $monthlyIncome = FinanceTransaction::where('type', 'income')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');

        $monthlyExpense = FinanceTransaction::where('type', 'expense')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');

        $yearlyIncome = FinanceTransaction::where('type', 'income')
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');

        $yearlyExpense = FinanceTransaction::where('type', 'expense')
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');

        return [
            'monthly_income' => $monthlyIncome,
            'monthly_expense' => $monthlyExpense,
            'monthly_balance' => $monthlyIncome - $monthlyExpense,
            'yearly_income' => $yearlyIncome,
            'yearly_expense' => $yearlyExpense,
            'yearly_balance' => $yearlyIncome - $yearlyExpense,
        ];
    }

    /**
     * Get transaction detail for modal (AJAX)
     */
    public function getTransactionDetail($id)
    {
        try {
            $transaction = FinanceTransaction::with('user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'transaction' => [
                    'id' => $transaction->id,
                    'title' => $transaction->title,
                    'description' => $transaction->description,
                    'category' => $transaction->category,
                    'type' => $transaction->type,
                    'amount' => $transaction->amount,
                    'payment_method' => $transaction->payment_method,
                    'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
                    'formatted_amount' => $transaction->formatted_amount,
                    'notes' => $transaction->notes,
                    'attachments' => $transaction->attachments,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                    'user' => [
                        'name' => $transaction->user->name,
                        'email' => $transaction->user->email
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Handle creating new transaction from admin dashboard
     */
    public function createTransaction(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $transaction = FinanceTransaction::create([
            'user_id' => Auth::id(),
            'transaction_date' => $request->transaction_date,
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'type' => $request->type,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil disimpan',
            'transaction' => $transaction
        ]);
    }

    /**
     * Get dashboard statistics - Update untuk menggunakan FinanceTransaction
     */
    public function getStats()
    {
        $stats = [
            'total_warga' => User::where('role', 'warga')->count(),
            'active_warga' => User::where('role', 'warga')
                ->where('status', 'active')
                ->count(),
            'total_posts' => Post::count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'active_polls' => Poll::where('status', 'active')->count(),
            'upcoming_events' => CalendarEvent::where('event_date', '>', now())->count(),
            'total_balance' => $this->getCurrentBalance(),
            'monthly_income' => FinanceTransaction::where('type', 'income')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount'),
            'monthly_expense' => FinanceTransaction::where('type', 'expense')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount'),
        ];

        return response()->json($stats);
    }

    /**
     * Get report detail for modal (AJAX)
     */
    public function getReportDetail($id)
    {
        try {
            $report = Report::with('user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'report' => [
                    'id' => $report->id,
                    'title' => $report->title,
                    'category' => $report->category,
                    'priority' => $report->priority,
                    'description' => $report->description,
                    'location' => $report->location,
                    'status' => $report->status,
                    'response' => $report->response,
                    'created_at' => $report->created_at->format('Y-m-d H:i:s'),
                    'user' => [
                        'name' => $report->user->name,
                        'email' => $report->user->email
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update report status (Admin only) - Database update only
     */
    public function updateReportStatus(Request $request, $id)
    {
        try {
            // Pastikan user adalah admin
            if (Auth::user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Validasi input
            $request->validate([
                'status' => 'required|in:pending,in_progress,completed,rejected',
                'response' => 'nullable|string|max:1000'
            ]);

            // Cari laporan
            $report = Report::findOrFail($id);

            // Prepare data untuk update
            $updateData = [
                'status' => $request->status,
            ];

            // Jika ada response, tambahkan ke data update
            if ($request->has('response') && !empty($request->response)) {
                $updateData['response'] = $request->response;
                $updateData['responded_at'] = now();
                $updateData['responded_by'] = Auth::id();
            }

            // Update laporan di database
            $report->update($updateData);

            // Return success response tanpa data tambahan
            return response()->json([
                'success' => true,
                'message' => 'Status laporan berhasil diperbarui'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Error updating report status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Get all reports with filtering for admin dashboard
     */
    public function getReports(Request $request)
    {
        try {
            $query = Report::with('user');

            // Filter berdasarkan status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter berdasarkan kategori
            if ($request->has('category') && $request->category !== 'all') {
                $query->where('category', $request->category);
            }

            // Filter berdasarkan prioritas
            if ($request->has('priority') && $request->priority !== 'all') {
                $query->where('priority', $request->priority);
            }

            // Search
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('location', 'like', '%' . $search . '%');
                });
            }

            $reports = $query->orderBy('created_at', 'desc')->paginate(10);

            return response()->json([
                'success' => true,
                'reports' => $reports
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data laporan'
            ], 500);
        }
    }

    /**
     * Bulk update report status (untuk multiple selection)
     */
    public function bulkUpdateReportStatus(Request $request)
    {
        try {
            // Pastikan user adalah admin
            if (Auth::user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Validasi input
            $request->validate([
                'report_ids' => 'required|array',
                'report_ids.*' => 'exists:reports,id',
                'status' => 'required|in:pending,in_progress,completed,rejected',
                'response' => 'nullable|string|max:1000'
            ]);

            $updateData = [
                'status' => $request->status,
            ];

            if ($request->has('response') && !empty($request->response)) {
                $updateData['response'] = $request->response;
                $updateData['responded_at'] = now();
                $updateData['responded_by'] = Auth::id();
            }

            // Update multiple reports
            $updatedCount = Report::whereIn('id', $request->report_ids)
                ->update($updateData);

            return response()->json([
                'success' => true,
                'message' => "Berhasil memperbarui {$updatedCount} laporan",
                'updated_count' => $updatedCount
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error bulk updating report status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Handle creating new poll from admin dashboard
     */
    public function createPoll(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:255',
            'end_date' => 'required|date|after:now',
        ]);

        $poll = Poll::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'options' => json_encode($request->options),
            'end_date' => $request->end_date,
            'user_id' => Auth::id(),
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Polling berhasil dibuat',
            'poll' => $poll
        ]);
    }

    /**
     * Handle creating new event from admin dashboard
     */
    public function createEvent(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'category' => 'required|string',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $event = CalendarEvent::create([
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'location' => $request->location,
            'category' => $request->category,
            'capacity' => $request->capacity,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kegiatan berhasil dibuat',
            'event' => $event
        ]);
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities()
    {
        $activities = collect();

        // Recent posts
        $posts = Post::with('user')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($post) {
                return [
                    'type' => 'post',
                    'title' => $post->title,
                    'user' => $post->user->name,
                    'created_at' => $post->created_at,
                    'icon' => '📝'
                ];
            });

        // Recent reports
        $reports = Report::with('user')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($report) {
                return [
                    'type' => 'report',
                    'title' => $report->title,
                    'user' => $report->user->name,
                    'created_at' => $report->created_at,
                    'icon' => '📋'
                ];
            });

        // Recent registrations
        $users = User::where('role', 'warga')
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'registration',
                    'title' => 'Pendaftaran baru',
                    'user' => $user->name,
                    'created_at' => $user->created_at,
                    'icon' => '👤'
                ];
            });

        return $activities->merge($posts)
            ->merge($reports)
            ->merge($users)
            ->sortByDesc('created_at')
            ->take(10)
            ->values();
    }

    /**
     * Settings page
     */
    public function settingsIndex(): View
    {
        return view('admin.settings.index');
    }

    /**
     * Update general settings
     */
    public function updateGeneralSettings(Request $request)
    {
        // Handle general settings update
        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui');
    }

    /**
     * Update notification settings
     */
    public function updateNotificationSettings(Request $request)
    {
        // Handle notification settings update
        return redirect()->back()->with('success', 'Pengaturan notifikasi berhasil diperbarui');
    }
}