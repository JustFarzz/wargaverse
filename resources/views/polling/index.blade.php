<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polling Warga - RT Digital</title>

    <link rel="stylesheet" href="{{ asset('css/createpolling.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbarcomponents.css') }}">
    <link rel="stylesheet" href="{{ asset('css/indexpolling.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    @include('components.navbar')

    <div class="polling-container">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-info">
                    <h1><i class="fas fa-poll"></i> Polling Warga</h1>
                    <p>Berpartisipasi dalam pengambilan keputusan bersama untuk kemajuan RT</p>
                </div>
                {{-- <a href="{{ route('polling.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Buat Polling Baru
                </a> --}}
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-tabs">
                <button class="filter-tab active" data-filter="all">
                    <i class="fas fa-list"></i> Semua Polling
                </button>
                <button class="filter-tab" data-filter="active">
                    <i class="fas fa-play-circle"></i> Aktif
                </button>
                <button class="filter-tab" data-filter="ended">
                    <i class="fas fa-check-circle"></i> Berakhir
                </button>
            </div>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Cari polling..." id="searchInput">
            </div>
        </div>

        <!-- Polling List -->
        <div class="polling-list">
            @forelse($pollings as $polling)
                            @php
                $isActive = $polling->end_date > now();
                $totalVotes = $polling->votes()->count();
                $totalUsers = \App\Models\User::count(); // Atau sesuaikan dengan jumlah warga RT
                $participationRate = $totalUsers > 0 ? round(($totalVotes / $totalUsers) * 100) : 0;
                            @endphp

                            <div class="poll-card {{ $isActive ? 'active-poll' : 'ended-poll' }}"
                                data-status="{{ $isActive ? 'active' : 'ended' }}">
                                <div class="poll-header">
                                    <div class="poll-meta">
                                        <span class="poll-status {{ $isActive ? 'status-active' : 'status-ended' }}">
                                            <i class="fas fa-{{ $isActive ? 'circle' : 'check-circle' }}"></i>
                                            {{ $isActive ? 'Aktif' : 'Selesai' }}
                                        </span>
                                        <span class="poll-date">
                                            <i class="fas fa-calendar"></i>
                                            {{ $isActive ? 'Berakhir' : 'Berakhir' }}:
                                            {{ \Carbon\Carbon::parse($polling->end_date)->format('d M Y') }}
                                        </span>
                                    </div>
                                    <div class="poll-votes">
                                        <i class="fas fa-users"></i>
                                        <span class="vote-count">{{ $totalVotes }}</span> suara
                                    </div>
                                </div>
                                <div class="poll-content">
                                    <h3>{{ $polling->title }}</h3>
                                    <p class="poll-description">
                                        {{ Str::limit($polling->description, 150) }}
                                    </p>
                                    <div class="poll-creator">
                                        <img src="{{ $polling->user->avatar ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&crop=face' }}"
                                            alt="{{ $polling->user->name }}">
                                        <div class="creator-info">
                                            <span class="creator-name">{{ $polling->user->name }}</span>
                                            <span
                                                class="creator-role">{{ $polling->user->role == 'admin' ? 'Ketua RT' : 'Warga' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="poll-actions">
                                    <a href="{{ route('polling.show', $polling->id) }}"
                                        class="btn {{ $isActive ? 'btn-vote' : 'btn-result' }}">
                                        <i class="fas fa-{{ $isActive ? 'vote-yea' : 'chart-bar' }}"></i>
                                        {{ $isActive ? 'Lihat & Vote' : 'Lihat Hasil' }}
                                    </a>
                                    <div class="poll-stats">
                                        <span class="participation {{ !$isActive ? 'completed' : '' }}">
                                            <i class="fas fa-{{ !$isActive ? 'trophy' : 'chart-line' }}"></i>
                                            {{ $participationRate }}% partisipasi
                                        </span>
                                    </div>
                                </div>
                            </div>
            @empty
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-vote-yea"></i>
                    </div>
                    <h3>Belum Ada Polling</h3>
                    <p>Saat ini tidak ada polling yang sedang berlangsung. Buat polling baru untuk melibatkan warga dalam
                        pengambilan keputusan.</p>
                    {{-- <a href="{{ route('polling.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Buat Polling Pertama
                    </a> --}}
                </div>
            @endforelse
        </div>
    </div>

    <script src="{{ asset('js/polling/index.js') }}"></script>
</body>

</html>