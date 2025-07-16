<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link rel="stylesheet" href="{{ asset('css/navbarcomponents.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/indexreport.css') }}">

</head>
<body>
    @include('components.navbar')
    
    <div class="laporan-container">
        <div class="header-section">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="icon-report"></i>
                    Laporan Warga
                </h1>
                <p class="page-subtitle">Daftar laporan dan keluhan dari warga RT</p>
            </div>
            <a href="{{ route('laporan.create') }}" class="btn-primary">
                <i class="icon-plus"></i>
                Buat Laporan Baru
            </a>
        </div>
    
        <!-- Filter & Search -->
        <div class="filter-section">
            <div class="search-box">
                <i class="icon-search"></i>
                <input type="text" placeholder="Cari laporan..." id="searchInput">
            </div>
            <div class="filter-controls">
                <select id="statusFilter" class="filter-select">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="progress">Dalam Proses</option>
                    <option value="resolved">Selesai</option>
                </select>
                <select id="categoryFilter" class="filter-select">
                    <option value="">Semua Kategori</option>
                    <option value="infrastruktur">Infrastruktur</option>
                    <option value="kebersihan">Kebersihan</option>
                    <option value="keamanan">Keamanan</option>
                    <option value="sosial">Sosial</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
        </div>
    
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card pending">
                <div class="stat-icon">
                    <i class="icon-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $pendingCount ?? 0 }}</h3>
                    <p>Menunggu</p>
                </div>
            </div>
            <div class="stat-card progress">
                <div class="stat-icon">
                    <i class="icon-gear"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $progressCount ?? 0 }}</h3>
                    <p>Dalam Proses</p>
                </div>
            </div>
            <div class="stat-card resolved">
                <div class="stat-icon">
                    <i class="icon-check"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $resolvedCount ?? 0 }}</h3>
                    <p>Selesai</p>
                </div>
            </div>
            <div class="stat-card total">
                <div class="stat-icon">
                    <i class="icon-list"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $totalCount ?? 0 }}</h3>
                    <p>Total Laporan</p>
                </div>
            </div>
        </div>
    
        <!-- Reports List -->
        <div class="reports-section">
            @if(isset($reports) && $reports->count() > 0)
                <div class="reports-grid">
                    @foreach($reports as $report)
                    <div class="report-card" data-status="{{ $report->status }}" data-category="{{ $report->category }}">
                        <div class="report-header">
                            <div class="report-meta">
                                <span class="status-badge status-{{ $report->status }}">
                                    @switch($report->status)
                                        @case('pending')
                                            <i class="icon-clock"></i> Menunggu
                                            @break
                                        @case('progress')
                                            <i class="icon-gear"></i> Dalam Proses
                                            @break
                                        @case('resolved')
                                            <i class="icon-check"></i> Selesai
                                            @break
                                    @endswitch
                                </span>
                                <span class="category-badge">{{ ucfirst($report->category) }}</span>
                            </div>
                            <div class="report-date">
                                {{ $report->created_at->format('d M Y') }}
                            </div>
                        </div>
                        
                        <div class="report-content">
                            <h3 class="report-title">{{ $report->title }}</h3>
                            <p class="report-description">{{ Str::limit($report->description, 100) }}</p>
                            
                            @if($report->image)
                            <div class="report-image">
                                <img src="{{ asset('storage/' . $report->image) }}" alt="Foto laporan">
                            </div>
                            @endif
                            
                            <div class="report-location">
                                <i class="icon-location"></i>
                                {{ $report->location }}
                            </div>
                        </div>
                        
                        <div class="report-footer">
                            <div class="reporter-info">
                                <div class="reporter-avatar">
                                    {{ strtoupper(substr($report->user->name, 0, 1)) }}
                                </div>
                                <span class="reporter-name">{{ $report->user->name }}</span>
                            </div>
                            
                            <div class="report-actions">
                                <a href="{{ route('laporan.show', $report->id) }}" class="btn-view">
                                    <i class="icon-eye"></i>
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $reports->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="icon-report"></i>
                    </div>
                    <h3>Belum Ada Laporan</h3>
                    <p>Belum ada laporan yang dibuat oleh warga. Mulai buat laporan pertama!</p>
                    <a href="{{ route('laporan.create') }}" class="btn-primary">
                        <i class="icon-plus"></i>
                        Buat Laporan Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
    
    <script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        filterReports();
    });
    
    // Filter functionality
    document.getElementById('statusFilter').addEventListener('change', filterReports);
    document.getElementById('categoryFilter').addEventListener('change', filterReports);
    
    function filterReports() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const categoryFilter = document.getElementById('categoryFilter').value;
        
        const reportCards = document.querySelectorAll('.report-card');
        
        reportCards.forEach(card => {
            const title = card.querySelector('.report-title').textContent.toLowerCase();
            const description = card.querySelector('.report-description').textContent.toLowerCase();
            const status = card.dataset.status;
            const category = card.dataset.category;
            
            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
            const matchesStatus = !statusFilter || status === statusFilter;
            const matchesCategory = !categoryFilter || category === categoryFilter;
            
            if (matchesSearch && matchesStatus && matchesCategory) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    </script>
</body>
</html>