<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polling: Pemilihan Jadwal Kerja Bakti Bulanan - RT Digital</title>
    <link rel="stylesheet" href="{{ asset('css/createpolling.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbarcomponents.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>
    @include('components.navbar')

    <div class="polling-container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('polling.index') }}">
                <i class="fas fa-poll"></i> Polling
            </a>
            <i class="fas fa-chevron-right"></i>
            <span>Pemilihan Jadwal Kerja Bakti Bulanan</span>
        </div>

        <!-- Poll Detail -->
        <div class="poll-detail">
            <!-- Poll Header -->
            <div class="poll-detail-header">
                <div class="poll-status-bar">
                    <span class="poll-status status-active">
                        <i class="fas fa-circle pulse"></i> Polling Aktif
                    </span>
                    <div class="poll-countdown">
                        <i class="fas fa-clock"></i>
                        <span id="countdown">3 hari 14 jam 25 menit</span>
                    </div>
                </div>
                
                <h1>Pemilihan Jadwal Kerja Bakti Bulanan</h1>
                
                <div class="poll-meta">
                    <div class="poll-creator">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=50&h=50&fit=crop&crop=face" alt="Pak RT">
                        <div class="creator-info">
                            <span class="creator-name">Pak RT</span>
                            <span class="creator-role">Ketua RT</span>
                            <span class="created-date">Dibuat 2 hari yang lalu</span>
                        </div>
                    </div>
                    <div class="poll-stats-header">
                        <div class="stat-item">
                            <span class="stat-number">42</span>
                            <span class="stat-label">Total Suara</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">78%</span>
                            <span class="stat-label">Partisipasi</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Poll Description -->
            <div class="poll-description-card">
                <h3><i class="fas fa-info-circle"></i> Deskripsi</h3>
                <p>Mari tentukan jadwal kerja bakti yang paling cocok untuk semua warga RT 05. Kegiatan ini penting untuk menjaga kebersihan dan keindahan lingkungan kita. Hasil polling ini akan menentukan jadwal tetap kerja bakti bulanan untuk 6 bulan ke depan.</p>
                
                <div class="poll-details-grid">
                    <div class="detail-item">
                        <i class="fas fa-tag"></i>
                        <span>Kategori: <strong>Kebersihan</strong></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-calendar-check"></i>
                        <span>Berakhir: <strong>15 Juli 2025, 23:59</strong></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-users"></i>
                        <span>Target: <strong>54 warga</strong></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-check-double"></i>
                        <span>Tipe: <strong>Pilihan Tunggal</strong></span>
                    </div>
                </div>
            </div>

            <!-- Voting Section -->
            <div class="voting-section" id="votingSection">
                <div class="section-header">
                    <h3><i class="fas fa-vote-yea"></i> Pilih Opsi Anda</h3>
                    <p>Pilih satu jadwal yang paling sesuai untuk Anda</p>
                </div>

                <form id="voteForm" class="vote-form">
                    <div class="vote-options">
                        <div class="vote-option">
                            <input type="radio" name="vote" id="option1" value="1">
                            <label for="option1" class="option-card">
                                <div class="option-content">
                                    <div class="option-icon">
                                        <i class="fas fa-sun"></i>
                                    </div>
                                    <div class="option-text">
                                        <h4>Sabtu Pagi (08:00 - 10:00)</h4>
                                        <p>Kerja bakti di pagi hari saat udara masih sejuk</p>
                                    </div>
                                </div>
                                <div class="option-stats">
                                    <span class="vote-count">18 suara</span>
                                    <div class="vote-bar">
                                        <div class="vote-progress" style="width: 43%"></div>
                                    </div>
                                    <span class="vote-percentage">43%</span>
                                </div>
                            </label>
                        </div>

                        <div class="vote-option">
                            <input type="radio" name="vote" id="option2" value="2">
                            <label for="option2" class="option-card">
                                <div class="option-content">
                                    <div class="option-icon">
                                        <i class="fas fa-cloud-sun"></i>
                                    </div>
                                    <div class="option-text">
                                        <h4>Minggu Pagi (08:00 - 10:00)</h4>
                                        <p>Hari libur, lebih fleksibel untuk semua warga</p>
                                    </div>
                                </div>
                                <div class="option-stats">
                                    <span class="vote-count">15 suara</span>
                                    <div class="vote-bar">
                                        <div class="vote-progress" style="width: 36%"></div>
                                    </div>
                                    <span class="vote-percentage">36%</span>
                                </div>
                            </label>
                        </div>

                        <div class="vote-option">
                            <input type="radio" name="vote" id="option3" value="3">
                            <label for="option3" class="option-card">
                                <div class="option-content">
                                    <div class="option-icon">
                                        <i class="fas fa-moon"></i>
                                    </div>
                                    <div class="option-text">
                                        <h4>Sabtu Sore (16:00 - 18:00)</h4>
                                        <p>Sore hari setelah istirahat siang</p>
                                    </div>
                                </div>
                                <div class="option-stats">
                                    <span class="vote-count">9 suara</span>
                                    <div class="vote-bar">
                                        <div class="vote-progress" style="width: 21%"></div>
                                    </div>
                                    <span class="vote-percentage">21%</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="vote-actions">
                        <button type="submit" class="btn btn-primary" id="submitVote">
                            <i class="fas fa-paper-plane"></i>
                            Kirim Suara Saya
                        </button>
                        <p class="vote-notice">
                            <i class="fas fa-info-circle"></i>
                            Suara Anda akan tersimpan dan tidak dapat diubah
                        </p>
                    </div>
                </form>
            </div>

            <!-- Results Section (Initially Hidden) -->
            <div class="results-section" id="resultsSection" style="display: none;">
                <div class="section-header">
                    <h3><i class="fas fa-chart-bar"></i> Hasil Polling</h3>
                    <p>Terima kasih atas partisipasi Anda!</p>
                </div>

                <div class="results-grid">
                    <!-- Chart Section -->
                    <div class="chart-container">
                        <canvas id="pollChart"></canvas>
                    </div>

                    <!-- Detailed Results -->
                    <div class="results-details">
                        <div class="result-item winner">
                            <div class="result-rank">
                                <i class="fas fa-trophy"></i>
                                <span>Pemenang</span>
                            </div>
                            <div class="result-content">
                                <h4>Sabtu Pagi (08:00 - 10:00)</h4>
                                <div class="result-stats">
                                    <span class="votes">18 suara</span>
                                    <span class="percentage">43%</span>
                                </div>
                            </div>
                        </div>

                        <div class="result-item">
                            <div class="result-rank">
                                <span class="rank-number">2</span>
                            </div>
                            <div class="result-content">
                                <h4>Minggu Pagi (08:00 - 10:00)</h4>
                                <div class="result-stats">
                                    <span class="votes">15 suara</span>
                                    <span class="percentage">36%</span>
                                </div>
                            </div>
                        </div>

                        <div class="result-item">
                            <div class="result-rank">
                                <span class="rank-number">3</span>
                            </div>
                            <div class="result-content">
                                <h4>Sabtu Sore (16:00 - 18:00)</h4>
                                <div class="result-stats">
                                    <span class="votes">9 suara</span>
                                    <span class="percentage">21%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Your Vote -->
                <div class="your-vote-card">
                    <h4><i class="fas fa-user-check"></i> Suara Anda</h4>
                    <div class="vote-confirmation">
                        <div class="vote-choice">
                            <i class="fas fa-check-circle"></i>
                            <span>Sabtu Pagi (08:00 - 10:00)</span>
                        </div>
                        <div class="vote-time">
                            Dipilih pada: 10 Juli 2025, 14:30
                        </div>
                    </div>
                </div>
            </div>

            <!-- Participants List -->
            <div class="participants-section">
                <div class="section-header">
                    <h3><i class="fas fa-users"></i> Partisipan</h3>
                    <p>Warga yang telah berpartisipasi dalam polling ini</p>
                </div>

                <div class="participants-stats">
                    <div class="stat-card">
                        <span class="stat-number">42</span>
                        <span class="stat-label">Sudah Vote</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">12</span>
                        <span class="stat-label">Belum Vote</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">78%</span>
                        <span class="stat-label">Partisipasi</span>
                    </div>
                </div>

                <div class="participants-list">
                    <div class="participant-item">
                        <img src="https://images.unsplash.com/photo-1494790108755-2616b2e2e5cc?w=40&h=40&fit=crop&crop=face" alt="Bu Sari">
                        <div class="participant-info">
                            <span class="participant-name">Bu Sari</span>
                            <span class="vote-time">2 jam yang lalu</span>
                        </div>
                        <div class="participant-status voted">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>

                    <div class="participant-item">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face" alt="Pak Budi">
                        <div class="participant-info">
                            <span class="participant-name">Pak Budi</span>
                            <span class="vote-time">5 jam yang lalu</span>
                        </div>
                        <div class="participant-status voted">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>

                    <div class="participant-item">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&crop=face" alt="Pak RT">
                        <div class="participant-info">
                            <span class="participant-name">Pak RT</span>
                            <span class="vote-time">1 hari yang lalu</span>
                        </div>
                        <div class="participant-status voted">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>

                    <div class="participant-item">
                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=40&h=40&fit=crop&crop=face" alt="Bu Rina">
                        <div class="participant-info">
                            <span class="participant-name">Bu Rina</span>
                            <span class="vote-time">Belum vote</span>
                        </div>
                        <div class="participant-status pending">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>

                    <button class="btn btn-secondary load-more">
                        <i class="fas fa-chevron-down"></i>
                        Lihat Semua (54 warga)
                    </button>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="comments-section">
                <div class="section-header">
                    <h3><i class="fas fa-comments"></i> Diskusi</h3>
                    <p>Berbagi pendapat dan diskusi terkait polling ini</p>
                </div>

                <div class="comment-form">
                    <div class="comment-input">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&crop=face" alt="You" class="user-avatar">
                        <div class="input-group">
                            <textarea placeholder="Bagikan pendapat Anda tentang pilihan jadwal..." rows="3"></textarea>
                            <button class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                                Kirim
                            </button>
                        </div>
                    </div>
                </div>

                <div class="comments-list">
                    <div class="comment-item">
                        <img src="https://images.unsplash.com/photo-1494790108755-2616b2e2e5cc?w=45&h=45&fit=crop&crop=face" alt="Bu Sari" class="comment-avatar">
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="comment-author">Bu Sari</span>
                                <span class="comment-time">2 jam yang lalu</span>
                            </div>
                            <p class="comment-text">Saya pilih Sabtu pagi karena cuaca masih sejuk dan anak-anak bisa ikut membantu. Bagaimana menurut yang lain?</p>
                            <div class="comment-actions">
                                <button class="comment-action">
                                    <i class="fas fa-thumbs-up"></i> 5
                                </button>
                                <button class="comment-action">
                                    <i class="fas fa-reply"></i> Balas
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="comment-item">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=45&h=45&fit=crop&crop=face" alt="Pak Budi" class="comment-avatar">
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="comment-author">Pak Budi</span>
                                <span class="comment-time">5 jam yang lalu</span>
                            </div>
                            <p class="comment-text">Setuju dengan Bu Sari. Minggu pagi juga oke sih, tapi Sabtu pagi lebih fresh menurut saya.</p>
                            <div class="comment-actions">
                                <button class="comment-action">
                                    <i class="fas fa-thumbs-up"></i> 8
                                </button>
                                <button class="comment-action">
                                    <i class="fas fa-reply"></i> Balas
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Vote form handling
        document.getElementById('voteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const selectedOption = document.querySelector('input[name="vote"]:checked');
            if (!selectedOption) {
                alert('Pilih salah satu opsi terlebih dahulu');
                return;
            }

            // Simulate vote submission
            setTimeout(() => {
                document.getElementById('votingSection').style.display = 'none';
                document.getElementById('resultsSection').style.display = 'block';
                
                // Initialize chart
                initPollChart();
                
                // Scroll to results
                document.getElementById('resultsSection').scrollIntoView({ behavior: 'smooth' });
            }, 1000);
        });

        // Initialize poll chart
        function initPollChart() {
            const ctx = document.getElementById('pollChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sabtu Pagi', 'Minggu Pagi', 'Sabtu Sore'],
                    datasets: [{
                        data: [18, 15, 9],
                        backgroundColor: [
                            '#667eea',
                            '#764ba2',
                            '#f093fb'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }

        // Countdown timer
        function updateCountdown() {
            const endDate = new Date('2025-07-15T23:59:59');
            const now = new Date();
            const difference = endDate - now;

            if (difference > 0) {
                const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));

                document.getElementById('countdown').textContent = 
                    `${days} hari ${hours} jam ${minutes} menit`;
            } else {
                document.getElementById('countdown').textContent = 'Polling telah berakhir';
            }
        }

        // Update countdown every minute
        updateCountdown();
        setInterval(updateCountdown, 60000);

        // Option selection animation
        document.querySelectorAll('.vote-option input').forEach(input => {
            input.addEventListener('change', function() {
                document.querySelectorAll('.option-card').forEach(card => {
                    card.classList.remove('selected');
                });
                if (this.checked) {
                    this.nextElementSibling.classList.add('selected');
                }
            });
        });

        // Comment form
        document.querySelector('.comment-form button').addEventListener('click', function() {
            const textarea = document.querySelector('.comment-form textarea');
            if (textarea.value.trim()) {
                // Simulate comment submission
                alert('Komentar berhasil dikirim!');
                textarea.value = '';
            }
        });
    </script>
</body>
</html>