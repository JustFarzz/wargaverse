<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan - WargaVerse</title>

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
        </div>

    
        <!-- Reports List -->
        <div class="reports-section">
            @if(isset($reports) && $reports->count() > 0)
                <div class="reports-grid">
                    @foreach($reports as $report)
                    <div class="report-card" data-status="{{ $report->status }}" data-category="{{ $report->category }}">
                        <div class="report-header">
                            <div class="report-meta">
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
    
    <script src="{{ asset('js/laporan/index.js') }}"></script>
</body>
</body>
</html>