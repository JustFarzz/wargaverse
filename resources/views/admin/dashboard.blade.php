<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Dashboard - Sistem Informasi RT</title>

    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

</head>

<body>
    <div class="admin-container">
        <!-- Header Welcome -->
        <div class="welcome-section">
            <div class="welcome-content">
                <h1>Dashboard Admin RT</h1>
                <p>Sistem Informasi RT 001/RW 001</p>
                <span class="current-date">Rabu, 16 Juli 2025</span>
            </div>
            <div class="admin-badge">
                <span>👨‍💼</span>
                <span>Administrator</span>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <h3>{{ $stats['total_warga'] }}</h3>
                    <p>Total Warga</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📝</div>
                <div class="stat-content">
                    <h3>{{ $stats['total_posts'] }}</h3>
                    <p>Postingan Aktif</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🗳️</div>
                <div class="stat-content">
                    <h3>{{ $stats['active_polls'] }}</h3>
                    <p>Polling Aktif</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-content">
                    <h3>{{ $stats['upcoming_events'] }}</h3>
                    <p>Kegiatan Bulan Ini</p>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="main-grid">
            <!-- Buat Polling -->
            <div class="card">
                <div class="card-header">
                    <h2>🗳️ Polling Warga</h2>
                </div>
                <div class="card-content">
                    <p>Kelola polling dan survey untuk warga RT. Buat polling baru untuk mengumpulkan pendapat warga
                        tentang berbagai kegiatan dan keputusan RT.</p>

                    <div style="margin-top: 20px;">
                        <a href="{{ route('admin.polling.create') }}" class="btn btn-primary">
                            <span>📊</span>
                            Buat Polling Baru
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tambah Kegiatan -->
            <div class="card">
                <div class="card-header">
                    <h2>📅 Tambah Kegiatan RT</h2>
                </div>

                <div class="card-content">
                    <p>Tambahkan kegiatan baru untuk warga RT. Kelola jadwal dan informasi kegiatan yang akan datang.
                    </p>

                    <div style="margin-top: 20px;">
                        <a href="{{ route('admin.event.create') }}" class="btn btn-primary">
                            <span>📅</span>
                            Tambah Kegiatan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Kas RT -->
        <div class="card full-width-card">
            <div class="card-header">
                <h2>💰 Detail Kas RT</h2>
                <a href="{{ route('admin.kas.create') }}" class="btn btn-primary">
                    <span>📅</span>
                    Tambah Transaksi
                </a>
            </div>
            <div class="card-content">
                <!-- Ringkasan Keuangan -->
                <div class="stats-grid" style="margin-bottom: 20px;">
                    <div class="stat-card">
                        <div class="stat-icon">💳</div>
                        <div class="stat-content">
                            <h3>Rp {{ number_format($currentBalance, 0, ',', '.') }}</h3>
                            <p>Saldo Kas Saat Ini</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📈</div>
                        <div class="stat-content">
                            <h3>Rp {{ number_format($summary['monthly_income'], 0, ',', '.') }}</h3>
                            <p>Pemasukan Bulan Ini</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📉</div>
                        <div class="stat-content">
                            <h3>Rp {{ number_format($summary['monthly_expense'], 0, ',', '.') }}</h3>
                            <p>Pengeluaran Bulan Ini</p>
                        </div>
                    </div>
                </div>

                <!-- Tabel Transaksi -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Kategori</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                {{-- <th>Status</th> --}}
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="transactionTable">
                            @forelse($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                    <td>{{ $transaction->title }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $transaction->category)) }}</td>
                                    <td>
                                        @if($transaction->type == 'income')
                                            <span class="badge badge-success">Pemasukan</span>
                                        @else
                                            <span class="badge badge-danger">Pengeluaran</span>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    {{-- <td>
                                        @if($transaction->status == 'verified')
                                            <span class="badge badge-success">Terverifikasi</span>
                                        @elseif($transaction->status == 'pending')
                                            <span class="badge badge-warning">Menunggu</span>
                                        @else
                                            <span class="badge badge-danger">Ditolak</span>
                                        @endif
                                    </td> --}}
                                    {{-- <td>
                                        <button class="btn btn-info btn-sm"
                                            onclick="showTransactionDetail({{ $transaction->id }})">Detail</button>
                                        @if($transaction->status == 'pending')
                                            <button class="btn btn-success btn-sm"
                                                onclick="verifyTransaction({{ $transaction->id }})">Verifikasi</button>
                                        @endif
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center;">Belum ada transaksi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Detail Transaksi -->
    <div id="transactionModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Detail Transaksi</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="transactionDetail">
                <!-- Detail akan dimuat di sini -->
            </div>
        </div>
    </div>

    <script src="{{ asset('js/admin/dashboard.js') }}"></script>
</body>

</html>