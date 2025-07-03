@extends('layouts.app')

@section('title', 'Kas RT - Ringkasan Keuangan')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/kas.css') }}">
@endsection

@section('content')
<div class="kas-container">
    <!-- Header Section -->
    <div class="kas-header">
        <div class="header-content">
            <h1><i class="fas fa-coins"></i> Kas RT 007</h1>
            <p>Kelola dan pantau keuangan RT secara transparan</p>
            <div class="current-period">Periode: {{ date('F Y') }}</div>
        </div>
        <div class="header-actions">
            <a href="{{ route('kas.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i>
                Tambah Transaksi
            </a>
            <a href="#" class="btn-secondary">
                <i class="fas fa-download"></i>
                Export Laporan
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid">
        <div class="summary-card income">
            <div class="card-icon">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="card-content">
                <h3>Rp {{ number_format($totalIncome ?? 2750000, 0, ',', '.') }}</h3>
                <p>Total Pemasukan</p>
                <span class="trend positive">+12% dari bulan lalu</span>
            </div>
        </div>
        
        <div class="summary-card expense">
            <div class="card-icon">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="card-content">
                <h3>Rp {{ number_format($totalExpense ?? 1850000, 0, ',', '.') }}</h3>
                <p>Total Pengeluaran</p>
                <span class="trend negative">+5% dari bulan lalu</span>
            </div>
        </div>
        
        <div class="summary-card balance">
            <div class="card-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="card-content">
                <h3>Rp {{ number_format($balance ?? 900000, 0, ',', '.') }}</h3>
                <p>Saldo Saat Ini</p>
                <span class="trend positive">Sehat</span>
            </div>
        </div>
        
        <div class="summary-card transactions">
            <div class="card-icon">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="card-content">
                <h3>{{ $totalTransactions ?? 24 }}</h3>
                <p>Total Transaksi</p>
                <span class="trend neutral">Bulan ini</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="main-grid">
        <!-- Recent Transactions -->
        <div class="transactions-section">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-history"></i> Transaksi Terbaru</h2>
                    <div class="header-filters">
                        <select class="filter-select" id="typeFilter">
                            <option value="">Semua Jenis</option>
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                        <select class="filter-select" id="monthFilter">
                            <option value="">Bulan Ini</option>
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                        </select>
                    </div>
                </div>
                <div class="card-content">
                    <div class="transactions-list">
                        <!-- Sample Transaction 1 -->
                        <div class="transaction-item income">
                            <div class="transaction-icon">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <div class="transaction-details">
                                <h4>Iuran Bulanan Warga</h4>
                                <p>Pembayaran iuran RT bulan Desember</p>
                                <div class="transaction-meta">
                                    <span class="date">2 Juli 2025</span>
                                    <span class="category">Iuran</span>
                                </div>
                            </div>
                            <div class="transaction-amount positive">
                                +Rp 750.000
                            </div>
                        </div>

                        <!-- Sample Transaction 2 -->
                        <div class="transaction-item expense">
                            <div class="transaction-icon">
                                <i class="fas fa-minus-circle"></i>
                            </div>
                            <div class="transaction-details">
                                <h4>Pembelian Lampu Jalan</h4>
                                <p>Penggantian lampu jalan yang rusak</p>
                                <div class="transaction-meta">
                                    <span class="date">1 Juli 2025</span>
                                    <span class="category">Infrastruktur</span>
                                </div>
                            </div>
                            <div class="transaction-amount negative">
                                -Rp 150.000
                            </div>
                        </div>

                        <!-- Sample Transaction 3 -->
                        <div class="transaction-item income">
                            <div class="transaction-icon">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <div class="transaction-details">
                                <h4>Sumbangan Acara 17 Agustus</h4>
                                <p>Donasi warga untuk perayaan kemerdekaan</p>
                                <div class="transaction-meta">
                                    <span class="date">30 Juni 2025</span>
                                    <span class="category">Donasi</span>
                                </div>
                            </div>
                            <div class="transaction-amount positive">
                                +Rp 500.000
                            </div>
                        </div>

                        <!-- Sample Transaction 4 -->
                        <div class="transaction-item expense">
                            <div class="transaction-icon">
                                <i class="fas fa-minus-circle"></i>
                            </div>
                            <div class="transaction-details">
                                <h4>Biaya Keamanan</h4>
                                <p>Gaji satpam RT bulan ini</p>
                                <div class="transaction-meta">
                                    <span class="date">29 Juni 2025</span>
                                    <span class="category">Operasional</span>
                                </div>
                            </div>
                            <div class="transaction-amount negative">
                                -Rp 800.000
                            </div>
                        </div>
                    </div>
                    
                    <div class="load-more">
                        <button class="btn-outline">
                            <i class="fas fa-chevron-down"></i>
                            Muat Lebih Banyak
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Categories -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-tags"></i> Kategori Populer</h3>
                </div>
                <div class="card-content">
                    <div class="category-list">
                        <div class="category-item">
                            <span class="category-name">Iuran Warga</span>
                            <span class="category-amount">Rp 2.250.000</span>
                        </div>
                        <div class="category-item">
                            <span class="category-name">Keamanan</span>
                            <span class="category-amount">Rp 800.000</span>
                        </div>
                        <div class="category-item">
                            <span class="category-name">Infrastruktur</span>
                            <span class="category-amount">Rp 450.000</span>
                        </div>
                        <div class="category-item">
                            <span class="category-name">Acara RT</span>
                            <span class="category-amount">Rp 600.000</span>
                        </div>
                        <div class="category-item">
                            <span class="category-name">Donasi</span>
                            <span class="category-amount">Rp 500.000</span>
                        </div>
                    </div>
                    <a href="#" class="view-all-categories">
                        Lihat Semua Kategori <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-pie"></i> Statistik Cepat</h3>
                </div>
                <div class="card-content">
                    <div class="quick-stats">
                        <div class="stat-item">
                            <div class="stat-label">Rata-rata Bulanan</div>
                            <div class="stat-value">Rp 850.000</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Transaksi Terbanyak</div>
                            <div class="stat-value">Iuran Warga</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Warga Aktif</div>
                            <div class="stat-value">42 dari 50</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Target Kas</div>
                            <div class="stat-value progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 75%"></div>
                                </div>
                                <span>75%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const typeFilter = document.getElementById('typeFilter');
    const monthFilter = document.getElementById('monthFilter');
    
    typeFilter.addEventListener('change', function() {
        filterTransactions();
    });
    
    monthFilter.addEventListener('change', function() {
        filterTransactions();
    });
    
    function filterTransactions() {
        const type = typeFilter.value;
        const month = monthFilter.value;
        
        // Filter logic here
        console.log('Filter by type:', type, 'month:', month);
    }
    
    // Load more functionality
    const loadMoreBtn = document.querySelector('.load-more button');
    loadMoreBtn.addEventListener('click', function() {
        // Load more transactions
        console.log('Loading more transactions...');
    });
});
</script>
@endsection