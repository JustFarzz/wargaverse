<?php

use App\Models\KasTransaction;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ContactController;

// Redirect root ke beranda
Route::get('/', function () {
    return redirect()->route('home');
});

// Authentication Routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login')->name('login.post');
    Route::get('/register', 'showRegisterForm')->name('register');
    Route::post('/register', 'register')->name('register.post');
    Route::post('/logout', 'logout')->name('logout');
});

// Protected Routes - Harus login sebagai warga
Route::middleware(['auth', 'role:warga'])->group(function () {

    Route::delete('/timeline/{post}', [PostController::class, 'destroy'])->name('timeline.destroy');

    Route::get('/home/stats', [HomeController::class, 'getStats'])->name('home.stats');
    Route::get('/home/activities', [HomeController::class, 'getRecentActivities'])->name('home.activities');
    Route::get('/home/dashboard-summary', [HomeController::class, 'getDashboardSummary'])->name('home.dashboard-summary');
    
    // Beranda/Dashboard Routes
    Route::controller(HomeController::class)->group(function () {
        Route::get('/beranda', 'index')->name('home');
        Route::get('/beranda/stats', 'getStats')->name('home.stats');
        Route::get('/beranda/activities', 'getRecentActivities')->name('home.activities');
        Route::get('/beranda/summary', 'getDashboardSummary')->name('home.summary');
    });

    // Timeline Routes (Jual Beli & Jasa)
    Route::prefix('timeline')->name('timeline.')->controller(PostController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/buat', 'create')->name('create');
        Route::post('/buat', 'store')->name('store');
        Route::get('/{post}', 'show')->name('show');
        Route::get('/{post}/edit', 'edit')->name('edit');
        Route::put('/{post}', 'update')->name('update');
    });

    // Laporan Warga Routes - PERBAIKAN: Hapus duplikasi, gunakan satu definisi saja
    Route::prefix('laporan')->name('laporan.')->controller(ReportController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/buat', 'create')->name('create');
        Route::post('/buat', 'store')->name('store');
        Route::get('/{report}', 'show')->name('show');
        Route::get('/{report}/edit', 'edit')->name('edit');
        Route::put('/{report}', 'update')->name('update');
        Route::delete('/{report}', 'destroy')->name('destroy');
    });

    // Polling Routes
    Route::prefix('polling')->name('polling.')->controller(PollController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/buat', 'create')->name('create');
        Route::post('/buat', 'store')->name('store');
        Route::get('/{polling}', 'show')->name('show');
        Route::post('/{polling}/vote', 'vote')->name('vote');
        Route::get('/{polling}/edit', 'edit')->name('edit');
        Route::put('/{polling}', 'update')->name('update');
        Route::delete('/{polling}', 'destroy')->name('destroy');
        Route::get('/{polling}/participants', 'loadMoreParticipants')->name('participants');
    });

    // Kalender Kegiatan RT Routes
    Route::prefix('kalender')->name('kalender.')->controller(CalendarController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/buat', 'create')->name('create');
        Route::post('/buat', 'store')->name('store');
        Route::get('/{event}', 'show')->name('show');
        Route::get('/{event}/edit', 'edit')->name('edit');
        Route::put('/{event}', 'update')->name('update');
        Route::delete('/{event}', 'destroy')->name('destroy');
        Route::get('/api/events', 'getEvents')->name('api.events');
        Route::post('/{event}/hadir', 'attend')->name('attend');
        Route::delete('/{event}/batal-hadir', 'cancelAttendance')->name('cancel-attend');
    });

    // Dana/Kas RT Routes
    Route::prefix('kas')->name('kas.')->controller(FinanceController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/input', 'create')->name('create');
        Route::post('/input', 'store')->name('store');
        Route::get('/{transaction}', 'show')->name('show');
        Route::get('/{transaction}/edit', 'edit')->name('edit');
        Route::put('/{transaction}', 'update')->name('update');
        Route::delete('/{transaction}', 'destroy')->name('destroy');
        Route::get('/laporan/bulanan', 'monthlyReport')->name('monthly-report');
        Route::get('/laporan/tahunan', 'yearlyReport')->name('yearly-report');
        Route::get('/export/excel', 'exportExcel')->name('export.excel');
        Route::get('/export/pdf', 'exportPdf')->name('export.pdf');
    });

    // Profile Routes
    Route::prefix('profil')->name('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/edit', 'edit')->name('edit');
        Route::put('/update', 'update')->name('update');
        Route::put('/password', 'updatePassword')->name('update-password');
        Route::post('/avatar', 'uploadAvatar')->name('upload-avatar');
        Route::delete('/avatar', 'deleteAvatar')->name('delete-avatar');
    });

    // Search Routes
    Route::get('/cari', function () {
        return view('search.index');
    })->name('search');

    Route::get('/cari/hasil', [SearchController::class, 'search'])->name('search.results');

    // Help & Support
    Route::get('/bantuan', function () {
        return view('help.index');
    })->name('help');

    Route::get('/kontak-admin', function () {
        return view('contact.admin');
    })->name('contact.admin');

    Route::post('/kontak-admin', [ContactController::class, 'sendToAdmin'])->name('contact.admin.send');

    // API Routes for AJAX requests
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/posts', [PostController::class, 'apiIndex'])->name('posts');
        Route::get('/reports/stats', [ReportController::class, 'getStats'])->name('reports.stats');
        Route::get('/polls/active', [PollController::class, 'getActivePolls'])->name('polls.active');
        Route::get('/events/upcoming', [CalendarController::class, 'getUpcomingEvents'])->name('events.upcoming');
        Route::get('/finance/summary', [FinanceController::class, 'getSummary'])->name('finance.summary');
        Route::get('/notifications', [ProfileController::class, 'getNotifications'])->name('notifications');
        Route::patch('/notifications/{id}/read', [ProfileController::class, 'markNotificationRead'])->name('notifications.read');
    });

    // HAPUS BARIS INI - Route::resource('laporan', ReportController::class);
});

// Admin Routes - Harus login sebagai admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard Admin
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Quick create routes
    Route::get('/create-polling', function () {
        return view('admin.createpolling');
    })->name('createpolling');

    Route::get('/create-event', function () {
        return view('admin.createevent');
    })->name('createevent');

    Route::get('/create-kas', function () {
        return view('admin.createkas');
    })->name('createkas');

    // User Management
    Route::prefix('warga')->name('warga.')->group(function () {
        Route::get('/', [ProfileController::class, 'adminIndex'])->name('index');
        Route::get('/{user}', [ProfileController::class, 'adminShow'])->name('show');
        Route::patch('/{user}/verifikasi', [ProfileController::class, 'verify'])->name('verify');
        Route::patch('/{user}/blokir', [ProfileController::class, 'block'])->name('block');
        Route::patch('/{user}/aktifkan', [ProfileController::class, 'activate'])->name('activate');
        Route::delete('/{user}', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Content Moderation
    Route::prefix('moderasi')->name('moderasi.')->group(function () {
        Route::get('/postingan', [PostController::class, 'adminIndex'])->name('posts');
        Route::patch('/postingan/{post}/sembunyikan', [PostController::class, 'hide'])->name('posts.hide');
        Route::patch('/postingan/{post}/tampilkan', [PostController::class, 'show'])->name('posts.show');
        Route::get('/laporan-konten', [ReportController::class, 'contentReports'])->name('content-reports');
        Route::patch('/laporan-konten/{report}/proses', [ReportController::class, 'processContentReport'])->name('content-reports.process');
    });

    // Admin Polling Routes
    Route::prefix('polling')->name('polling.')->controller(PollController::class)->group(function () {
        Route::get('/', 'adminIndex')->name('index');
        Route::get('/buat', 'adminCreate')->name('create');
        Route::post('/buat', 'adminStore')->name('store');
        Route::get('/{polling}', 'adminShow')->name('show');
        Route::get('/{polling}/edit', 'adminEdit')->name('edit');
        Route::put('/{polling}', 'adminUpdate')->name('update');
        Route::delete('/{polling}', 'adminDestroy')->name('destroy');
    });

    // Admin Event Routes
    Route::prefix('event')->name('event.')->controller(CalendarController::class)->group(function () {
        Route::get('/', 'adminIndex')->name('index');
        Route::get('/buat', 'adminCreateevent')->name('create');
        Route::post('/buat', 'adminStore')->name('store');
        Route::get('/{event}', 'adminShow')->name('show');
        Route::get('/{event}/edit', 'adminEdit')->name('edit');
        Route::put('/{event}', 'adminUpdate')->name('update');
        Route::delete('/{event}', 'adminDestroy')->name('destroy');
        Route::patch('/{event}/tutup', 'closePoll')->name('close');
        Route::patch('/{event}/buka', 'openPoll')->name('open');
    });

    // Admin Kas Routes
    Route::prefix('kas')->name('kas.')->controller(FinanceController::class)->group(function () {
        Route::get('/', 'adminIndex')->name('index');
        Route::get('/buat', 'adminCreatekas')->name('create');
        Route::post('/buat', 'adminStore')->name('store');
        Route::get('/{kas}', 'adminShow')->name('show');
        Route::get('/{kas}/edit', 'adminEdit')->name('edit');
        Route::put('/{kas}', 'adminUpdate')->name('update');
        Route::delete('/{kas}', 'adminDestroy')->name('destroy');
        Route::patch('/{kas}/tutup', 'closePoll')->name('close');
        Route::patch('/{kas}/buka', 'openPoll')->name('open');
    });

    // Report Management Routes untuk Admin
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/{id}', [AdminController::class, 'getReportDetail'])->name('detail');
        Route::post('/{id}/update-status', [AdminController::class, 'updateReportStatus'])->name('update-status');
        Route::get('/', [AdminController::class, 'getReports'])->name('index');
        Route::post('/bulk-update', [AdminController::class, 'bulkUpdateReportStatus'])->name('bulk-update');
    });

    // Statistics
    Route::get('/stats', [AdminController::class, 'getStats'])->name('stats');

    // Admin specific routes for forms
    Route::prefix('data')->name('data.')->controller(AdminController::class)->group(function () {
        Route::post('/kegiatan', 'createEvent')->name('event.store');
        Route::get('/kegiatan/{event}', 'showEvent')->name('event.show');
        Route::put('/kegiatan/{event}', 'updateEvent')->name('event.update');
        Route::delete('/kegiatan/{event}', 'deleteEvent')->name('event.destroy');

        Route::post('/transaksi', 'createTransaction')->name('transaction.store');
        Route::get('/transaksi/{transaction}', 'showTransaction')->name('transaction.show');
        Route::put('/transaksi/{transaction}', 'updateTransaction')->name('transaction.update');
        Route::delete('/transaksi/{transaction}', 'deleteTransaction')->name('transaction.destroy');
        Route::patch('/transaksi/{transaction}/verify', 'verifyTransaction')->name('transaction.verify');

        Route::get('/stats', 'getStats')->name('stats');
        Route::get('/laporan/keuangan', 'getFinanceReport')->name('finance.report');
        Route::get('/laporan/aktivitas', 'getActivityReport')->name('activity.report');
    });

    // Admin only routes for reports
    Route::prefix('laporan')->name('laporan.')->controller(ReportController::class)->group(function () {
        Route::patch('/{report}/status', 'updateStatus')->name('update-status');
        Route::post('/{report}/tanggapi', 'respond')->name('respond');
    });

    // Admin kas verification
    Route::prefix('kas')->name('kas.')->controller(FinanceController::class)->group(function () {
        Route::patch('/{transaction}/verifikasi', 'verify')->name('verify');
        Route::patch('/{transaction}/tolak', 'reject')->name('reject');
    });

    // System Settings
    Route::prefix('pengaturan')->name('settings.')->group(function () {
        Route::get('/', [AdminController::class, 'settingsIndex'])->name('index');
        Route::put('/umum', [AdminController::class, 'updateGeneralSettings'])->name('general');
        Route::put('/notifikasi', [AdminController::class, 'updateNotificationSettings'])->name('notification');
    });
});

// Public Routes (tidak perlu login)
Route::get('/tentang', function () {
    return view('public.about');
})->name('about');

Route::get('/kontak', function () {
    return view('public.contact');
})->name('contact');

// Error Pages
Route::fallback(function () {
    return view('errors.404');
});