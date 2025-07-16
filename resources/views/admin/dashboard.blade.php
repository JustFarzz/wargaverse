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

    <script>
        // Transaction functions
        function showAddTransaction() {
            const transactionHtml = `
        <tr id="newTransactionRow">
            <td><input type="date" class="form-control" style="width: 140px;" required></td>
            <td><input type="text" class="form-control" placeholder="Keterangan" required></td>
            <td>
                <select class="form-control" style="width: 120px;" required>
                    <option value="iuran">Iuran</option>
                    <option value="donasi">Donasi</option>
                    <option value="bantuan">Bantuan</option>
                    <option value="lainnya_masuk">Lainnya (Masuk)</option>
                    <option value="keamanan">Keamanan</option>
                    <option value="infrastruktur">Infrastruktur</option>
                    <option value="acara">Acara</option>
                    <option value="operasional">Operasional</option>
                    <option value="lainnya_keluar">Lainnya (Keluar)</option>
                </select>
            </td>
            <td>
                <select class="form-control" style="width: 120px;" required>
                    <option value="income">Pemasukan</option>
                    <option value="expense">Pengeluaran</option>
                </select>
            </td>
            <td><input type="number" class="form-control" placeholder="Jumlah" required></td>
            <td>
                <select class="form-control" style="width: 120px;" required>
                    <option value="cash">Tunai</option>
                    <option value="transfer">Transfer</option>
                    <option value="ewallet">E-Wallet</option>
                    <option value="check">Cek</option>
                </select>
            </td>
            <td>
                <button class="btn btn-success btn-sm" onclick="saveTransaction()">Simpan</button>
                <button class="btn btn-danger btn-sm" onclick="cancelTransaction()">Batal</button>
            </td>
        </tr>
    `;

            const tableBody = document.getElementById('transactionTable');
            tableBody.insertAdjacentHTML('afterbegin', transactionHtml);
        }

        function saveTransaction() {
            const row = document.getElementById('newTransactionRow');
            const inputs = row.querySelectorAll('input, select');

            // Validate inputs
            let isValid = true;
            const data = {};

            inputs.forEach(input => {
                if (input.required && !input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = 'red';
                } else {
                    input.style.borderColor = '';
                }
            });

            if (!isValid) {
                alert('Mohon lengkapi semua field yang wajib diisi');
                return;
            }

            // Collect data
            data.transaction_date = inputs[0].value;
            data.title = inputs[1].value;
            data.category = inputs[2].value;
            data.type = inputs[3].value;
            data.amount = inputs[4].value;
            data.payment_method = inputs[5].value;
            data.description = inputs[1].value; // Use title as description

            // Send AJAX request
            fetch('/admin/transactions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessMessage('Transaksi berhasil disimpan!');
                        cancelTransaction();

                        // Optionally refresh the transaction table
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        alert(data.message || 'Gagal menyimpan transaksi');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan transaksi');
                });
        }

        function cancelTransaction() {
            const newRow = document.getElementById('newTransactionRow');
            if (newRow) {
                newRow.remove();
            }
        }

        function showTransactionDetail(transactionId) {
            fetch(`/admin/transactions/${transactionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const transaction = data.transaction;
                        const detailHtml = `
                            <p><strong>Judul:</strong> ${transaction.title}</p>
                            <p><strong>Deskripsi:</strong> ${transaction.description || '-'}</p>
                            <p><strong>Kategori:</strong> ${transaction.category}</p>
                            <p><strong>Jenis:</strong> ${transaction.type}</p>
                            <p><strong>Jumlah:</strong> Rp ${transaction.amount.toLocaleString('id-ID')}</p>
                            <p><strong>Metode Pembayaran:</strong> ${transaction.payment_method}</p>
                            <p><strong>Tanggal:</strong> ${new Date(transaction.transaction_date).toLocaleDateString('id-ID')}</p>
                            
                            <p><strong>Dibuat oleh:</strong> ${transaction.user.name}</p>
                            <p><strong>Catatan:</strong> ${transaction.notes || '-'}</p>
                        `;
                        document.getElementById('transactionDetail').innerHTML = detailHtml;
                        document.getElementById('transactionModal').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat detail transaksi');
                });
        }

        function verifyTransaction(transactionId) {
            if (confirm('Apakah Anda yakin ingin memverifikasi transaksi ini?')) {
                fetch(`/admin/transactions/${transactionId}/verify`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSuccessMessage('Transaksi berhasil diverifikasi!');
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            alert(data.message || 'Gagal memverifikasi transaksi');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat memverifikasi transaksi');
                    });
            }
        }

        function closeModal() {
            document.getElementById('transactionModal').style.display = 'none';
        }

        function showSuccessMessage(message) {
            // Create and show success notification
            const notification = document.createElement('div');
            notification.className = 'alert alert-success';
            notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        padding: 15px;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 5px;
        color: #155724;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    `;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Auto refresh untuk data realtime (optional)
        setInterval(function () {
            console.log('Refreshing admin dashboard data...');
            // Implementasi refresh data jika diperlukan
        }, 300000); // 5 minutes
    </script>
</body>

</html>