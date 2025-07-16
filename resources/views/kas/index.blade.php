<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kas RT</title>

    <link rel="stylesheet" href="{{ asset('css/navbarcomponents.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/indexkas.css') }}">

</head>

<body>
    @include('components.navbar')

    <div class="kas-container">
        <!-- Header Section -->
        <div class="kas-header">
            <div class="header-content">
                <h1><i class="fas fa-coins"></i> Kas RT 007</h1>
                <p>Kelola dan pantau keuangan RT secara transparan</p>
                <div class="current-period">Periode: {{ date('F Y') }}</div>
            </div>
            {{-- <div class="header-actions">
                <a href="{{ route('kas.create') }}" class="btn-primary">
                    <i class="fas fa-plus"></i>
                    Tambah Transaksi
                </a>
                <a href="#" class="btn-secondary">
                    <i class="fas fa-download"></i>
                    Export Laporan
                </a>
            </div> --}}
        </div>

        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card income">
                <div class="card-icon">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="card-content">
                    <h3>Rp {{ number_format($summary['monthly_income'] ?? 0, 0, ',', '.') }}</h3>
                    <p>Pemasukan Bulan Ini</p>
                    <span
                        class="trend positive">{{ $summary['monthly_income'] > 0 ? '+' : '' }}{{ number_format($summary['monthly_income'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="summary-card expense">
                <div class="card-icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="card-content">
                    <h3>Rp {{ number_format($summary['monthly_expense'] ?? 0, 0, ',', '.') }}</h3>
                    <p>Pengeluaran Bulan Ini</p>
                    <span
                        class="trend negative">{{ $summary['monthly_expense'] > 0 ? '-' : '' }}{{ number_format($summary['monthly_expense'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="summary-card balance">
                <div class="card-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="card-content">
                    <h3>Rp {{ number_format($currentBalance ?? 0, 0, ',', '.') }}</h3>
                    <p>Saldo Saat Ini</p>
                    <span class="trend {{ $currentBalance >= 0 ? 'positive' : 'negative' }}">
                        {{ $currentBalance >= 0 ? 'Sehat' : 'Defisit' }}
                    </span>
                </div>
            </div>

            <div class="summary-card transactions">
                <div class="card-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="card-content">
                    <h3>{{ $transactions->total() ?? 0 }}</h3>
                    <p>Total Transaksi</p>
                    <span class="trend neutral">
                        Bulan ini: {{ $transactions->where('transaction_date', '>=', now()->startOfMonth())->count() }}
                    </span>
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
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="transactions-list">
                            @forelse($transactions as $transaction)
                                <div class="transaction-item {{ $transaction->type }}">
                                    <div class="transaction-icon">
                                        <i
                                            class="fas fa-{{ $transaction->type == 'income' ? 'plus' : 'minus' }}-circle"></i>
                                    </div>
                                    <div class="transaction-details">
                                        <h4>{{ $transaction->title }}</h4>
                                        <p>{{ $transaction->description ?? 'Tidak ada deskripsi' }}</p>
                                        <div class="transaction-meta">
                                            <span class="date">{{ $transaction->transaction_date->format('d M Y') }}</span>
                                            <span
                                                class="category">{{ ucfirst(str_replace('_', ' ', $transaction->category)) }}</span>
                                            <span class="payment-method">{{ ucfirst($transaction->payment_method) }}</span>
                                        </div>
                                    </div>
                                    <div
                                        class="transaction-amount {{ $transaction->type == 'income' ? 'positive' : 'negative' }}">
                                        {{ $transaction->type == 'income' ? '+' : '-' }}Rp
                                        {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>Belum ada transaksi untuk ditampilkan</p>
                                    <a href="{{ route('kas.create') }}" class="btn-primary">
                                        <i class="fas fa-plus"></i>
                                        Tambah Transaksi Pertama
                                    </a>
                                </div>
                            @endforelse
                        </div>

                        @if($transactions->hasPages())
                            <div class="pagination-wrapper">
                                {{ $transactions->links() }}
                            </div>
                        @endif
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
                            @php
$categories = [
    'iuran' => 'Iuran Warga',
    'keamanan' => 'Keamanan',
    'infrastruktur' => 'Infrastruktur',
    'acara' => 'Acara RT',
    'donasi' => 'Donasi',
    'operasional' => 'Operasional',
];
$categoryStats = [];
foreach ($categories as $key => $name) {
    $amount = $transactions->where('category', $key)->sum('amount');
    if ($amount > 0) {
        $categoryStats[$name] = $amount;
    }
}
arsort($categoryStats);
$topCategories = array_slice($categoryStats, 0, 5, true);
                            @endphp

                            @forelse($topCategories as $categoryName => $amount)
                                <div class="category-item">
                                    <span class="category-name">{{ $categoryName }}</span>
                                    <span class="category-amount">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <div class="category-item">
                                    <span class="category-name">Belum ada data</span>
                                    <span class="category-amount">Rp 0</span>
                                </div>
                            @endforelse
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
                                <div class="stat-label">Saldo Bulanan</div>
                                <div class="stat-value">Rp
                                    {{ number_format($summary['monthly_balance'] ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Saldo Tahunan</div>
                                <div class="stat-value">Rp
                                    {{ number_format($summary['yearly_balance'] ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Transaksi Bulan Ini</div>
                                <div class="stat-value">
                                    {{ $transactions->where('transaction_date', '>=', now()->startOfMonth())->count() }}
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Status Kas</div>
                                <div class="stat-value progress">
                                    @php
$healthPercentage = $currentBalance > 0 ? min(100, ($currentBalance / 1000000) * 100) : 0;
                                    @endphp
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: {{ $healthPercentage }}%"></div>
                                    </div>
                                    <span>{{ number_format($healthPercentage, 0) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Filter functionality
            const typeFilter = document.getElementById('typeFilter');
            const monthFilter = document.getElementById('monthFilter');

            typeFilter.addEventListener('change', function () {
                filterTransactions();
            });

            monthFilter.addEventListener('change', function () {
                filterTransactions();
            });

            function filterTransactions() {
                const type = typeFilter.value;
                const month = monthFilter.value;

                // Create URL with filters
                const url = new URL(window.location.href);

                if (type) {
                    url.searchParams.set('type', type);
                } else {
                    url.searchParams.delete('type');
                }

                if (month) {
                    url.searchParams.set('month', month);
                } else {
                    url.searchParams.delete('month');
                }

                // Redirect to filtered URL
                window.location.href = url.toString();
            }

            // Auto-refresh every 5 minutes to show latest data
            setInterval(function () {
                window.location.reload();
            }, 300000); // 5 minutes
        });
    </script>

    <style>
        .payment-method {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            background-color: #f3f4f6;
            color: #6b7280;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-state p {
            margin-bottom: 20px;
            font-size: 16px;
        }

        .pagination-wrapper {
            margin-top: 20px;
            text-align: center;
        }

        .trend.negative {
            color: #dc2626;
        }

        .trend.positive {
            color: #16a34a;
        }

        .trend.neutral {
            color: #6b7280;
        }
    </style>
</body>

</html>