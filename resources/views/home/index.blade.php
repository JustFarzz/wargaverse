<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="{{ asset('css/navbarcomponents.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link href="{{ asset('css/home.css') }}" rel="stylesheet">

    <title>Sistem Informasi RT</title>
</head>

<body>
    @include('components.navbar')

    <div class="home-container">
        <!-- Header Welcome -->
        <div class="welcome-section">
            <div class="welcome-content">
                <h1>Selamat Datang, {{ Auth::user()->name }}</h1>
                <p>Sistem Informasi RT {{ Auth::user()->rt ?? '001' }}/RW {{ Auth::user()->rw ?? '001' }}</p>
                <span class="current-date">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
            </div>
            <div class="weather-widget">
                <div class="weather-icon"></div>
                <div class="weather-info">
                    <span class="temperature">28°C</span>
                    <span class="condition">Cerah Berawan</span>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-content">
                    <h3>{{ $totalPosts ?? 0 }}</h3>
                    <p>Postingan Aktif</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-content">
                    <h3>{{ $totalReports ?? 0 }}</h3>
                    <p>Laporan Warga</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-content">
                    <h3>{{ $activePollsCount ?? 0 }}</h3>
                    <p>Polling Aktif</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-content">
                    <h3>Rp {{ number_format($kasBalance ?? 0, 0, ',', '.') }}</h3>
                    <p>Saldo Kas RT</p>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="main-grid">
            <!-- Left Column -->
            <div class="left-column">
                <!-- Quick Actions -->
                <div class="card quick-actions">
                    <div class="card-header">
                        <h2>Aksi Cepat</h2>
                    </div>
                    <div class="card-content">
                        <div class="action-buttons">
                            <a href="{{ route('timeline.create') }}" class="action-btn primary">
                                <span class="btn-icon"></span>
                                Buat Postingan
                            </a>
                            <a href="{{ route('laporan.create') }}" class="action-btn warning">
                                <span class="btn-icon"></span>
                                Buat Laporan
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Posts -->
                <div class="card recent-posts">
                    <div class="card-header">
                        <h2>Postingan Terbaru</h2>
                        <a href="{{ route('timeline.index') }}" class="view-all">Lihat Semua</a>
                    </div>
                    <div class="card-content">
                        @if(isset($recentPosts) && $recentPosts->count() > 0)
                            @foreach($recentPosts as $post)
                                <div class="post-item">
                                    <div class="post-avatar">
                                        <img src="{{ $post->user->avatar ?? asset('images/default-avatar.png') }}" alt="Avatar">
                                    </div>
                                    <div class="post-content">
                                        <div class="post-header">
                                            <span class="post-author">{{ $post->user->name }}</span>
                                            <span class="post-time">{{ $post->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="post-excerpt">{{ Str::limit($post->content, 100) }}</p>
                                        <div class="post-meta">
                                            <span class="post-category">{{ $post->type ?? 'Umum' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <p>Belum ada postingan terbaru</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-column">
                <!-- Upcoming Events -->
                <div class="card upcoming-events">
                    <div class="card-header">
                        <h2>Kegiatan Mendatang</h2>
                        <a href="{{ route('kalender.index') }}" class="view-all">Lihat Kalender</a>
                    </div>
                    <div class="card-content">
                        @if(isset($upcomingEvents) && $upcomingEvents->count() > 0)
                            @foreach($upcomingEvents as $event)
                                <div class="event-item">
                                    <div class="event-date">
                                        <span class="day">{{ $event->event_date->format('d') }}</span>
                                        <span class="month">{{ $event->event_date->format('M') }}</span>
                                    </div>
                                    <div class="event-details">
                                        <h4>{{ $event->title }}</h4>
                                        <p>{{ $event->event_date->format('H:i') }} WIB</p>
                                        <span class="event-location">{{ $event->location }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <p>Tidak ada kegiatan mendatang</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Active Polls -->
                <div class="card active-polls">
                    <div class="card-header">
                        <h2>Polling Aktif</h2>
                        <a href="{{ route('polling.index') }}" class="view-all">Lihat Semua</a>
                    </div>
                    <div class="card-content">
                        @if(isset($activePolls) && $activePolls->count() > 0)
                            @foreach($activePolls as $poll)
                                <div class="poll-item">
                                    <h4>{{ $poll->title }}</h4>
                                    <p class="poll-description">{{ Str::limit($poll->description, 80) }}</p>
                                    <p class="poll-meta">
                                        <span>{{ $poll->votes_count ?? 0 }} suara</span>
                                        <span>•</span>
                                        <span>Berakhir {{ \Carbon\Carbon::parse($poll->end_date)->diffForHumans() }}</span>
                                    </p>
                                    <div class="poll-actions">
                                        <a href="{{ route('polling.show', $poll->id) }}" class="poll-vote-btn">Ikut Voting</a>
                                        <span class="poll-category">{{ ucfirst($poll->category) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <div class="empty-icon"></div>
                                <p>Tidak ada polling aktif saat ini</p>
                                <a href="{{ route('polling.create') }}" class="btn btn-primary btn-sm">Buat Polling Baru</a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Reports Status -->
                <div class="card recent-reports">
                    <div class="card-header">
                        <h2>Status Laporan</h2>
                        <a href="{{ route('laporan.index') }}" class="view-all">Lihat Semua</a>
                    </div>
                    <div class="card-content">
                        @if(isset($recentReports) && $recentReports->count() > 0)
                            @foreach($recentReports as $report)
                                <div class="report-item">
                                    <div class="report-status {{ strtolower($report->status) }}">
                                        {{ $report->status }}
                                    </div>
                                    <div class="report-details">
                                        <h4>{{ Str::limit($report->title, 50) }}</h4>
                                        <p>{{ $report->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <p>Tidak ada laporan terbaru</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary (for transparency) -->
        <div class="card financial-summary">
            <div class="card-header">
                <h2>Ringkasan Keuangan RT</h2>
                <a href="{{ route('kas.index') }}" class="view-all">Detail Kas</a>
            </div>
            <div class="card-content">
                <div class="finance-grid">
                    <div class="finance-item">
                        <div class="finance-label">Saldo Kas</div>
                        <div class="finance-amount positive">Rp {{ number_format($kasBalance ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="finance-item">
                        <div class="finance-label">Pemasukan Bulan Ini</div>
                        <div class="finance-amount positive">Rp {{ number_format($monthlyIncome ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="finance-item">
                        <div class="finance-label">Pengeluaran Bulan Ini</div>
                        <div class="finance-amount negative">Rp {{ number_format($monthlyExpense ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="finance-item">
                        <div class="finance-label">Saldo Bersih Bulan Ini</div>
                        <div
                            class="finance-amount {{ ($monthlyIncome - $monthlyExpense) >= 0 ? 'positive' : 'negative' }}">
                            Rp {{ number_format(($monthlyIncome ?? 0) - ($monthlyExpense ?? 0), 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="finance-item">
                        <div class="finance-label">Transaksi Terakhir</div>
                        <div class="finance-date">
                            @if(isset($lastTransaction))
                                {{ $lastTransaction->created_at->format('d/m/Y') }}
                                <br>
                                <small>{{ $lastTransaction->title }}</small>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pass routes to JavaScript
        const routes = {
            stats: '{{ route("home.stats") }}',
            activities: '{{ route("home.activities") }}',
            dashboardSummary: '{{ route("home.dashboard-summary") }}'
        };
    </script>
    <script src="{{ asset('js/home/index.js') }}"></script>
</body>

</html>