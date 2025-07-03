<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polling Warga - RT Digital</title>
    <link rel="stylesheet" href="{{ asset('css/createpolling.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbarcomponents.css') }}">
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
                <a href="{{ route('polling.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Buat Polling Baru
                </a>
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
            <!-- Active Poll -->
            <div class="poll-card active-poll" data-status="active">
                <div class="poll-header">
                    <div class="poll-meta">
                        <span class="poll-status status-active">
                            <i class="fas fa-circle"></i> Aktif
                        </span>
                        <span class="poll-date">
                            <i class="fas fa-calendar"></i> Berakhir: 15 Juli 2025
                        </span>
                    </div>
                    <div class="poll-votes">
                        <i class="fas fa-users"></i>
                        <span class="vote-count">42</span> suara
                    </div>
                </div>
                <div class="poll-content">
                    <h3>Pemilihan Jadwal Kerja Bakti Bulanan</h3>
                    <p class="poll-description">
                        Mari tentukan jadwal kerja bakti yang paling cocok untuk semua warga RT 05. 
                        Kegiatan ini penting untuk menjaga kebersihan dan keindahan lingkungan kita.
                    </p>
                    <div class="poll-creator">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&crop=face" alt="Admin">
                        <div class="creator-info">
                            <span class="creator-name">Pak RT</span>
                            <span class="creator-role">Ketua RT</span>
                        </div>
                    </div>
                </div>
                <div class="poll-actions">
                    <a href="{{ route('polling.vote', 1) }}" class="btn btn-vote">
                        <i class="fas fa-vote-yea"></i>
                        Lihat & Vote
                    </a>
                    <div class="poll-stats">
                        <span class="participation">
                            <i class="fas fa-chart-line"></i>
                            78% partisipasi
                        </span>
                    </div>
                </div>
            </div>

            <!-- Active Poll 2 -->
            <div class="poll-card active-poll" data-status="active">
                <div class="poll-header">
                    <div class="poll-meta">
                        <span class="poll-status status-active">
                            <i class="fas fa-circle"></i> Aktif
                        </span>
                        <span class="poll-date">
                            <i class="fas fa-calendar"></i> Berakhir: 18 Juli 2025
                        </span>
                    </div>
                    <div class="poll-votes">
                        <i class="fas fa-users"></i>
                        <span class="vote-count">28</span> suara
                    </div>
                </div>
                <div class="poll-content">
                    <h3>Proposal Pembangunan Taman Bermain Anak</h3>
                    <p class="poll-description">
                        Usulan untuk membangun taman bermain di area kosong dekat musholla. 
                        Apakah warga setuju dengan rencana ini dan bersedia berkontribusi?
                    </p>
                    <div class="poll-creator">
                        <img src="https://images.unsplash.com/photo-1494790108755-2616b2e2e5cc?w=40&h=40&fit=crop&crop=face" alt="Bu Sari">
                        <div class="creator-info">
                            <span class="creator-name">Bu Sari</span>
                            <span class="creator-role">Warga</span>
                        </div>
                    </div>
                </div>
                <div class="poll-actions">
                    <a href="{{ route('polling.vote', 2) }}" class="btn btn-vote">
                        <i class="fas fa-vote-yea"></i>
                        Lihat & Vote
                    </a>
                    <div class="poll-stats">
                        <span class="participation">
                            <i class="fas fa-chart-line"></i>
                            52% partisipasi
                        </span>
                    </div>
                </div>
            </div>

            <!-- Ended Poll -->
            <div class="poll-card ended-poll" data-status="ended">
                <div class="poll-header">
                    <div class="poll-meta">
                        <span class="poll-status status-ended">
                            <i class="fas fa-check-circle"></i> Selesai
                        </span>
                        <span class="poll-date">
                            <i class="fas fa-calendar"></i> Berakhir: 5 Juli 2025
                        </span>
                    </div>
                    <div class="poll-votes">
                        <i class="fas fa-users"></i>
                        <span class="vote-count">67</span> suara
                    </div>
                </div>
                <div class="poll-content">
                    <h3>Pilihan Sistem Keamanan RT</h3>
                    <p class="poll-description">
                        Polling untuk menentukan sistem keamanan yang akan diterapkan di RT 05. 
                        Hasil: CCTV + Satpam terpilih dengan 78% suara.
                    </p>
                    <div class="poll-creator">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&crop=face" alt="Admin">
                        <div class="creator-info">
                            <span class="creator-name">Pak RT</span>
                            <span class="creator-role">Ketua RT</span>
                        </div>
                    </div>
                </div>
                <div class="poll-actions">
                    <a href="{{ route('polling.vote', 3) }}" class="btn btn-result">
                        <i class="fas fa-chart-bar"></i>
                        Lihat Hasil
                    </a>
                    <div class="poll-stats">
                        <span class="participation completed">
                            <i class="fas fa-trophy"></i>
                            93% partisipasi
                        </span>
                    </div>
                </div>
            </div>

            <!-- Active Poll 3 -->
            <div class="poll-card active-poll" data-status="active">
                <div class="poll-header">
                    <div class="poll-meta">
                        <span class="poll-status status-active">
                            <i class="fas fa-circle"></i> Aktif
                        </span>
                        <span class="poll-date">
                            <i class="fas fa-calendar"></i> Berakhir: 25 Juli 2025
                        </span>
                    </div>
                    <div class="poll-votes">
                        <i class="fas fa-users"></i>
                        <span class="vote-count">15</span> suara
                    </div>
                </div>
                <div class="poll-content">
                    <h3>Peningkatan Iuran Bulanan RT</h3>
                    <p class="poll-description">
                        Usulan peningkatan iuran bulanan dari Rp 15.000 menjadi Rp 20.000 untuk 
                        memperbaiki fasilitas umum dan meningkatkan kegiatan sosial RT.
                    </p>
                    <div class="poll-creator">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face" alt="Pak Budi">
                        <div class="creator-info">
                            <span class="creator-name">Pak Budi</span>
                            <span class="creator-role">Bendahara RT</span>
                        </div>
                    </div>
                </div>
                <div class="poll-actions">
                    <a href="{{ route('polling.vote', 4) }}" class="btn btn-vote">
                        <i class="fas fa-vote-yea"></i>
                        Lihat & Vote
                    </a>
                    <div class="poll-stats">
                        <span class="participation">
                            <i class="fas fa-chart-line"></i>
                            28% partisipasi
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State (jika tidak ada polling) -->
        <div class="empty-state" style="display: none;">
            <div class="empty-icon">
                <i class="fas fa-vote-yea"></i>
            </div>
            <h3>Belum Ada Polling</h3>
            <p>Saat ini tidak ada polling yang sedang berlangsung. Buat polling baru untuk melibatkan warga dalam pengambilan keputusan.</p>
            <a href="{{ route('polling.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Buat Polling Pertama
            </a>
        </div>
    </div>

    <script>
        // Filter functionality
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const filter = this.getAttribute('data-filter');
                const polls = document.querySelectorAll('.poll-card');
                
                polls.forEach(poll => {
                    if (filter === 'all') {
                        poll.style.display = 'block';
                    } else {
                        const status = poll.getAttribute('data-status');
                        poll.style.display = status === filter ? 'block' : 'none';
                    }
                });
            });
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const polls = document.querySelectorAll('.poll-card');
            
            polls.forEach(poll => {
                const title = poll.querySelector('h3').textContent.toLowerCase();
                const description = poll.querySelector('.poll-description').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    poll.style.display = 'block';
                } else {
                    poll.style.display = 'none';
                }
            });
        });

        // Animate cards on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.poll-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>