<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polling: {{ $polling->title }} - RT Digital</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('css/createpolling.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbarcomponents.css') }}">
    <link rel="stylesheet" href="{{ asset('css/votepolling.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>
    @include('components.navbar')

    <div class="polling-container">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') ?? $errors->first() }}
            </div>
        @endif

        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('polling.index') }}">
                <i class="fas fa-poll"></i> Polling
            </a>
            <i class="fas fa-chevron-right"></i>
            <span>{{ $polling->title }}</span>
        </div>

        <!-- Poll Detail -->
        <div class="poll-detail">
            <!-- Poll Header -->
            <div class="poll-detail-header">
                <div class="poll-status-bar">
                    <span class="poll-status {{ $polling->end_date > now() ? 'status-active' : 'status-ended' }}">
                        <i class="fas fa-circle {{ $polling->end_date > now() ? 'pulse' : '' }}"></i> 
                        {{ $polling->end_date > now() ? 'Polling Aktif' : 'Polling Berakhir' }}
                    </span>
                    @if($polling->end_date > now())
                        <div class="poll-countdown">
                            <i class="fas fa-clock"></i>
                            <span id="countdown" data-end-date="{{ $polling->end_date->toISOString() }}">
                                {{ $polling->end_date->diffForHumans() }}
                            </span>
                        </div>
                    @endif
                </div>
                
                <h1>{{ $polling->title }}</h1>
                
                <div class="poll-meta">
                    <div class="poll-creator">
                        <img src="{{ $polling->user->avatar_url ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=50&h=50&fit=crop&crop=face' }}" 
                             alt="{{ $polling->user->name }}">
                        <div class="creator-info">
                            <span class="creator-name">{{ $polling->user->name }}</span>
                            <span class="creator-role">{{ $polling->user->role_display ?? 'Warga' }}</span>
                            <span class="created-date">Dibuat {{ $polling->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="poll-stats-header">
                        <div class="stat-item">
                            <span class="stat-number">{{ $totalVotes }}</span>
                            <span class="stat-label">Total Suara</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">{{ $totalParticipants }}</span>
                            <span class="stat-label">Partisipan</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Poll Description -->
            <div class="poll-description-card">
                <h3><i class="fas fa-info-circle"></i> Deskripsi</h3>
                <p>{{ $polling->description }}</p>
                
                <div class="poll-details-grid">
                    <div class="detail-item">
                        <i class="fas fa-tag"></i>
                        <span>Kategori: <strong>{{ ucfirst($polling->category) }}</strong></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-calendar-check"></i>
                        <span>Berakhir: <strong>{{ $polling->end_date->format('d M Y, H:i') }}</strong></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-users"></i>
                        <span>RT {{ $polling->rt }}/RW {{ $polling->rw }}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-check-double"></i>
                        <span>Tipe: <strong>{{ $polling->allow_multiple ? 'Pilihan Ganda' : 'Pilihan Tunggal' }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Voting Section -->
            @if(!$userVote && $polling->end_date > now())
                <div class="voting-section" id="votingSection">
                    <div class="section-header">
                        <h3><i class="fas fa-vote-yea"></i> Pilih Opsi Anda</h3>
                        <p>{{ $polling->allow_multiple ? 'Pilih satu atau lebih opsi' : 'Pilih satu opsi yang paling sesuai' }}</p>
                    </div>

                    <form id="voteForm" method="POST" action="{{ route('polling.vote', $polling) }}" class="vote-form">
                        @csrf
                        <div class="vote-options">
                            @foreach($optionStats as $option)
                                <div class="vote-option">
                                    <input type="{{ $polling->allow_multiple ? 'checkbox' : 'radio' }}" 
                                           name="options[]" 
                                           id="option{{ $option['id'] }}" 
                                           value="{{ $option['id'] }}">
                                    <label for="option{{ $option['id'] }}" class="option-card">
                                        <div class="option-content">
                                            <div class="option-icon">
                                                <i class="fas fa-{{ $loop->index == 0 ? 'sun' : ($loop->index == 1 ? 'cloud-sun' : 'moon') }}"></i>
                                            </div>
                                            <div class="option-text">
                                                <h4>{{ $option['text'] }}</h4>
                                            </div>
                                        </div>
                                        <div class="option-stats">
                                            <span class="vote-count">{{ $option['votes'] }} suara</span>
                                            <div class="vote-bar">
                                                <div class="vote-progress" style="width: {{ $option['percentage'] }}%"></div>
                                            </div>
                                            <span class="vote-percentage">{{ $option['percentage'] }}%</span>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="vote-actions">
                            <button type="submit" class="btn btn-primary" id="submitVote">
                                <i class="fas fa-paper-plane"></i>
                                <span class="button-text">Kirim Suara Saya</span>
                                <span class="loading-spinner" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </button>
                            <p class="vote-notice">
                                <i class="fas fa-info-circle"></i>
                                Suara Anda akan tersimpan dan tidak dapat diubah
                            </p>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Results Section -->
            @if($userVote || $polling->end_date <= now())
                <div class="results-section" id="resultsSection">
                    <div class="section-header">
                        <h3><i class="fas fa-chart-bar"></i> Hasil Polling</h3>
                        <p>{{ $userVote ? 'Terima kasih atas partisipasi Anda!' : 'Polling telah berakhir' }}</p>
                    </div>

                    <div class="results-grid">
                        <!-- Chart Section -->
                        <div class="chart-container">
                            <canvas id="pollChart"></canvas>
                        </div>

                        <!-- Detailed Results -->
                        <div class="results-details">
                            @foreach($optionStats->sortByDesc('votes') as $index => $option)
                                <div class="result-item {{ $index === 0 ? 'winner' : '' }}">
                                    <div class="result-rank">
                                        @if($index === 0)
                                            <i class="fas fa-trophy"></i>
                                            <span>Pemenang</span>
                                        @else
                                            <span class="rank-number">{{ $index + 1 }}</span>
                                        @endif
                                    </div>
                                    <div class="result-content">
                                        <h4>{{ $option['text'] }}</h4>
                                        <div class="result-stats">
                                            <span class="votes">{{ $option['votes'] }} suara</span>
                                            <span class="percentage">{{ $option['percentage'] }}%</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Your Vote -->
                    @if($userVote)
                        <div class="your-vote-card">
                            <h4><i class="fas fa-user-check"></i> Suara Anda</h4>
                            <div class="vote-confirmation">
                                <div class="vote-choice">
                                    <i class="fas fa-check-circle"></i>
                                    <span>{{ $userVote->pollOption->option_text }}</span>
                                </div>
                                <div class="vote-time">
                                    Dipilih pada: {{ $userVote->created_at->format('d M Y, H:i') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Participants List -->
            <div class="participants-section">
                <div class="section-header">
                    <h3><i class="fas fa-users"></i> Partisipan</h3>
                    <p>Warga yang telah berpartisipasi dalam polling ini</p>
                </div>

                <div class="participants-stats">
                    <div class="stat-card">
                        <span class="stat-number">{{ $totalParticipants }}</span>
                        <span class="stat-label">Sudah Vote</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">{{ $totalUsers - $totalParticipants }}</span>
                        <span class="stat-label">Belum Vote</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">{{ $totalUsers > 0 ? round(($totalParticipants / $totalUsers) * 100) : 0 }}%</span>
                        <span class="stat-label">Partisipasi</span>
                    </div>
                </div>

                <div class="participants-list">
                    @foreach($polling->votes->unique('user_id')->take(5) as $vote)
                        <div class="participant-item">
                            <img src="{{ $vote->user->avatar_url ?? 'https://images.unsplash.com/photo-1494790108755-2616b2e2e5cc?w=40&h=40&fit=crop&crop=face' }}" 
                                 alt="{{ $vote->user->name }}">
                            <div class="participant-info">
                                <span class="participant-name">{{ $vote->user->name }}</span>
                                <span class="vote-time">{{ $vote->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="participant-status voted">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    @endforeach

                    @if($totalParticipants > 5)
                        <button class="btn btn-secondary load-more" onclick="loadMoreParticipants()">
                            <i class="fas fa-chevron-down"></i>
                            Lihat Semua ({{ $totalParticipants }} partisipan)
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Set up CSRF token for all AJAX requests
        document.addEventListener('DOMContentLoaded', function() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Vote form handling
            const voteForm = document.getElementById('voteForm');
            if (voteForm) {
                voteForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const submitButton = document.getElementById('submitVote');
                    const buttonText = submitButton.querySelector('.button-text');
                    const loadingSpinner = submitButton.querySelector('.loading-spinner');
                    
                    // Check if any option is selected
                    const selectedOptions = this.querySelectorAll('input[name="options[]"]:checked');
                    if (selectedOptions.length === 0) {
                        alert('Pilih salah satu opsi terlebih dahulu');
                        return;
                    }
                    
                    // Show loading state
                    submitButton.disabled = true;
                    buttonText.style.display = 'none';
                    loadingSpinner.style.display = 'inline-block';
                    
                    // Submit vote
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            // Redirect to show results
                            window.location.reload();
                        } else {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Terjadi kesalahan');
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || 'Terjadi kesalahan saat menyimpan suara');
                        
                        // Reset button state
                        submitButton.disabled = false;
                        buttonText.style.display = 'inline-block';
                        loadingSpinner.style.display = 'none';
                    });
                });
            }
            
            // Initialize chart if results are shown
            if (document.getElementById('resultsSection') && document.getElementById('pollChart')) {
                initPollChart();
            }
            
            // Update countdown
            updateCountdown();
            setInterval(updateCountdown, 60000);
            
            // Option selection animation
            document.querySelectorAll('input[name="options[]"]').forEach(input => {
                input.addEventListener('change', function() {
                    const isRadio = this.type === 'radio';
                    
                    if (isRadio) {
                        // For radio buttons, remove selected class from all options
                        document.querySelectorAll('.option-card').forEach(card => {
                            card.classList.remove('selected');
                        });
                    }
                    
                    // Add/remove selected class based on checked state
                    const card = this.nextElementSibling;
                    if (this.checked) {
                        card.classList.add('selected');
                    } else {
                        card.classList.remove('selected');
                    }
                });
            });
        });

        // Initialize poll chart
        function initPollChart() {
            const ctx = document.getElementById('pollChart').getContext('2d');
            const optionStats = @json($optionStats->values());
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: optionStats.map(option => option.text),
                    datasets: [{
                        data: optionStats.map(option => option.votes),
                        backgroundColor: [
                            '#667eea',
                            '#764ba2',
                            '#f093fb',
                            '#4facfe',
                            '#43e97b',
                            '#fa709a',
                            '#ffecd2',
                            '#fcb69f'
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
            const countdownElement = document.getElementById('countdown');
            if (!countdownElement) return;
            
            const endDate = new Date(countdownElement.dataset.endDate);
            const now = new Date();
            const difference = endDate - now;

            if (difference > 0) {
                const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));

                countdownElement.textContent = `${days} hari ${hours} jam ${minutes} menit`;
            } else {
                countdownElement.textContent = 'Polling telah berakhir';
                // Optionally reload page when poll ends
                setTimeout(() => window.location.reload(), 5000);
            }
        }

        // Load more participants
        function loadMoreParticipants() {
            // Implementation for loading more participants via AJAX
            // This would require a separate API endpoint
            console.log('Loading more participants...');
        }

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });
    </script>

    <style>
        /* Additional styles for improved functionality */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: opacity 0.3s ease;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert i {
            margin-right: 8px;
        }

        .option-card.selected {
            border-color: #667eea;
            background-color: #f8f9ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }

        .loading-spinner {
            display: none;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .status-ended {
            background-color: #6c757d;
            color: white;
        }

        .status-ended .pulse {
            animation: none;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .pulse {
            animation: pulse 2s infinite;
        }
    </style>
</body>
</html>