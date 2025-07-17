<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Report;
use App\Models\Poll;
use App\Models\CalendarEvent;
use App\Models\FinanceTransaction;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Statistik utama
        $totalPosts = Post::where('status', 'published')
            ->where('rt', $user->rt)
            ->where('rw', $user->rw)
            ->count();

        $totalReports = Report::where('rt', $user->rt)
            ->where('rw', $user->rw)
            ->count();

        $activePollsCount = Poll::where('status', 'active')
            ->where('rt', $user->rt)
            ->where('rw', $user->rw)
            ->where('end_date', '>', now())
            ->count();

        // Saldo kas RT - gunakan method yang sama dengan FinanceController
        $kasBalance = $this->getCurrentBalance($user);

        // PERBAIKAN: Hapus filter user_id untuk kas RT
        $monthlyIncome = FinanceTransaction::where('type', 'income')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        $monthlyExpense = FinanceTransaction::where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        // Postingan terbaru
        $recentPosts = Post::with('user')
            ->where('status', 'published')
            ->where('rt', $user->rt)
            ->where('rw', $user->rw)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Kegiatan mendatang
        $upcomingEvents = CalendarEvent::where('rt', $user->rt)
            ->where('rw', $user->rw)
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->limit(5)
            ->get();

        // Polling aktif
        $activePolls = Poll::withCount('votes')
            ->where('status', 'active')
            ->where('rt', $user->rt)
            ->where('rw', $user->rw)
            ->where('end_date', '>', now())
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Laporan terbaru
        $recentReports = Report::where('rt', $user->rt)
            ->where('rw', $user->rw)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Transaksi terakhir - untuk kas RT, ambil transaksi terakhir secara umum
        $lastTransaction = FinanceTransaction::orderBy('created_at', 'desc')->first();

        return view('home.index', compact(
            'totalPosts',
            'totalReports',
            'activePollsCount',
            'kasBalance',
            'monthlyIncome',
            'monthlyExpense',
            'recentPosts',
            'upcomingEvents',
            'activePolls',
            'recentReports',
            'lastTransaction'
        ));
    }

    /**
     * Get current balance - PERBAIKAN: Sama seperti di FinanceController
     */
    private function getCurrentBalance($user)
    {
        $totalIncome = FinanceTransaction::where('type', 'income')->sum('amount');
        $totalExpense = FinanceTransaction::where('type', 'expense')->sum('amount');
        return $totalIncome - $totalExpense;
    }

    /**
     * Get stats data for AJAX refresh
     */
    public function getStats()
    {
        $user = Auth::user();

        $stats = [
            'totalPosts' => Post::where('status', 'published')
                ->where('rt', $user->rt)
                ->where('rw', $user->rw)
                ->count(),

            'totalReports' => Report::where('rt', $user->rt)
                ->where('rw', $user->rw)
                ->count(),

            'activePolls' => Poll::where('status', 'active')
                ->where('rt', $user->rt)
                ->where('rw', $user->rw)
                ->where('end_date', '>', now())
                ->count(),

            'kasBalance' => $this->getCurrentBalance($user)
        ];

        return response()->json($stats);
    }

    /**
     * Get recent activities for notification
     */
    public function getRecentActivities()
    {
        $user = Auth::user();

        $activities = collect();

        // Recent posts
        $recentPosts = Post::with('user')
            ->where('status', 'published')
            ->where('rt', $user->rt)
            ->where('rw', $user->rw)
            ->where('created_at', '>=', now()->subDays(7))
            ->get()
            ->map(function ($post) {
                return [
                    'type' => 'post',
                    'title' => 'Postingan baru dari ' . $post->user->name,
                    'content' => \Str::limit($post->content, 100),
                    'created_at' => $post->created_at,
                    'url' => route('timeline.show', $post->id)
                ];
            });

        // Recent reports
        $recentReports = Report::where('rt', $user->rt)
            ->where('rw', $user->rw)
            ->where('created_at', '>=', now()->subDays(7))
            ->get()
            ->map(function ($report) {
                return [
                    'type' => 'report',
                    'title' => 'Laporan baru: ' . $report->title,
                    'content' => 'Status: ' . $report->status,
                    'created_at' => $report->created_at,
                    'url' => route('laporan.show', $report->id)
                ];
            });

        // Upcoming events
        $upcomingEvents = CalendarEvent::where('rt', $user->rt)
            ->where('rw', $user->rw)
            ->where('event_date', '>=', now())
            ->where('event_date', '<=', now()->addDays(3))
            ->get()
            ->map(function ($event) {
                return [
                    'type' => 'event',
                    'title' => 'Kegiatan akan dimulai: ' . $event->title,
                    'content' => $event->event_date->format('d/m/Y H:i') . ' di ' . $event->location,
                    'created_at' => $event->event_date,
                    'url' => route('kalender.show', $event->id)
                ];
            });

        $activities = $activities->merge($recentPosts)
            ->merge($recentReports)
            ->merge($upcomingEvents)
            ->sortByDesc('created_at')
            ->take(10);

        return response()->json($activities->values());
    }

    /**
     * Get dashboard summary for specific date range
     */
    public function getDashboardSummary(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $summary = [
            'posts' => [
                'total' => Post::where('status', 'published')
                    ->where('rt', $user->rt)
                    ->where('rw', $user->rw)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'by_type' => Post::where('status', 'published')
                    ->where('rt', $user->rt)
                    ->where('rw', $user->rw)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->selectRaw('type, count(*) as total')
                    ->groupBy('type')
                    ->pluck('total', 'type')
            ],

            'reports' => [
                'total' => Report::where('rt', $user->rt)
                    ->where('rw', $user->rw)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'by_status' => Report::where('rt', $user->rt)
                    ->where('rw', $user->rw)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->selectRaw('status, count(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status')
            ],

            'financial' => [
                // PERBAIKAN: Hapus filter user_id untuk kas RT
                'income' => FinanceTransaction::where('type', 'income')
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->sum('amount'),
                'expense' => FinanceTransaction::where('type', 'expense')
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->sum('amount')
            ],

            'events' => CalendarEvent::where('rt', $user->rt)
                ->where('rw', $user->rw)
                ->whereBetween('event_date', [$startDate, $endDate])
                ->count()
        ];

        return response()->json($summary);
    }
}