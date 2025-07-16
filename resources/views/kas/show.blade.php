@extends('layouts.app')

@section('title', 'Detail Transaksi Kas')

@section('styles')

<link rel="stylesheet" href="{{ asset('css/kas.css') }}">
@endsection

@section('content')
<div class="kas-container">
    <!-- Header Section -->
    <div class="detail-header">
        <div class="header-content">
            <div class="breadcrumb">
                <a href="{{ route('kas.index') }}">Kas RT</a>
                <i class="fas fa-chevron-right"></i>
                <span>Detail Transaksi</span>
            </div>
            <h1><i class="fas fa-receipt"></i> {{ $transaction->title ?? 'Iuran Bulanan Warga' }}</h1>
            <div class="transaction-badge {{ $transaction->type ?? 'income' }}">
                <i class="fas fa-{{ ($transaction->type ?? 'income') == 'income' ? 'plus' : 'minus' }}-circle"></i>
                {{ ($transaction->type ?? 'income') == 'income' ? 'Pemasukan' : 'Pengeluaran' }}
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('kas.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
            @if(auth()->user()->role == 'admin' || auth()->id() == ($transaction->user_id ?? 1))
            <a href="#" class="btn-warning">
                <i class="fas fa-edit"></i>
                Edit
            </a>
            <button class="btn-danger" onclick="deleteTransaction()">
                <i class="fas fa-trash"></i>
                Hapus
            </button>
            @endif
        </div>
    </div>

    <!-- Main Content -->
    <div class="detail-grid">
        <!-- Transaction Details -->
        <div class="main-content">
            <div class="card transaction-card">
                <div class="card-header">
                    <h2><i class="fas fa-info-circle"></i> Informasi Transaksi</h2>
                    <div class="transaction-status">
                        <span class="status-badge verified">
                            <i class="fas fa-check-circle"></i>
                            Terverifikasi
                        </span>
                    </div>
                </div>
                <div class="card-content">
                    <div class="transaction-details">
                        <div class="detail-row">
                            <div class="detail-label">Jumlah</div>
                            <div class="detail-value amount {{ ($transaction->type ?? 'income') == 'income' ? 'positive' : 'negative' }}">
                                {{ ($transaction->type ?? 'income') == 'income' ? '+' : '-' }}Rp {{ number_format($transaction->amount ?? 750000, 0, ',', '.') }}
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Kategori</div>
                            <div class="detail-value">
                                <span class="category-badge">{{ $transaction->category ?? 'Iuran' }}</span>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Tanggal Transaksi</div>
                            <div class="detail-value">{{ $transaction->transaction_date ?? '2 Juli 2025' }}</div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Metode Pembayaran</div>
                            <div class="detail-value">
                                <i class="fas fa-{{ ($transaction->payment_method ?? 'cash') == 'cash' ? 'money-bill' : 'credit-card' }}"></i>
                                {{ ucfirst($transaction->payment_method ?? 'Tunai') }}
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Dibuat Oleh</div>
                            <div class="detail-value">
                                <div class="user-info">
                                    <img src="{{ $transaction->user->avatar ?? '/images/avatar-default.png' }}" alt="Avatar" class="user-avatar">
                                    <div>
                                        <div class="user-name">{{ $transaction->user->name ?? 'Budi Santoso' }}</div>
                                        <div class="user-role">{{ $transaction->user->role ?? 'Warga' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($transaction->is_recurring ?? false)
                        <div class="detail-row">
                            <div class="detail-label">Transaksi Berulang</div>
                            <div class="detail-value">
                                <span class="recurring-badge">
                                    <i class="fas fa-sync-alt"></i>
                                    Bulanan
                                </span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-align-left"></i> Deskripsi</h3>
                </div>
                <div class="card-content">
                    <p class="description">
                        {{ $transaction->description ?? 'Pembayaran iuran bulanan RT 007 untuk bulan Desember 2024. Iuran ini digunakan untuk keperluan operasional RT seperti gaji satpam, listrik pos kamling, dan kebutuhan administrasi RT.' }}
                    </p>
                </div>
            </div>

            <!-- Attachments -->
            @if(isset($transaction->attachments) && count($transaction->attachments) > 0)
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-paperclip"></i> Lampiran</h3>
                </div>
                <div class="card-content">
                    <div class="attachments-grid">
                        @foreach($transaction->attachments as $attachment)
                        <div class="attachment-item">
                            <div class="attachment-preview">
                                @if(in_array($attachment->type, ['jpg', 'jpeg', 'png']))
                                <img src="{{ $attachment->path }}" alt="Lampiran">
                                @else
                                <div class="file-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                @endif
                            </div>
                            <div class="attachment-info">
                                <div class="attachment-name">{{ $attachment->name }}</div>
                                <div class="attachment-size">{{ $attachment->size }}</div>
                            </div>
                            <a href="{{ $attachment->path }}" class="attachment-download" target="_blank">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-paperclip"></i> Lampiran</h3>
                </div>
                <div class="card-content">
                    <div class="attachments-grid">
                        <!-- Sample attachments -->
                        <div class="attachment-item">
                            <div class="attachment-preview">
                                <img src="/images/sample-receipt.jpg" alt="Bukti Pembayaran">
                            </div>
                            <div class="attachment-info">
                                <div class="attachment-name">bukti_pembayaran.jpg</div>
                                <div class="attachment-size">256 KB</div>
                            </div>
                            <a href="#" class="attachment-download">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                        
                        <div class="attachment-item">
                            <div class="attachment-preview">
                                <div class="file-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                            </div>
                            <div class="attachment-info">
                                <div class="attachment-name">laporan_iuran.pdf</div>
                                <div class="attachment-size">1.2 MB</div>
                            </div>
                            <a href="#" class="attachment-download">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($transaction->notes ?? true)
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-sticky-note"></i> Catatan</h3>
                </div>
                <div class="card-content">
                    <p class="notes">
                        {{ $transaction->notes ?? 'Pembayaran iuran sudah diterima dengan lengkap. Terima kasih atas partisipasi warga dalam menjaga kelancaran operasional RT.' }}
                    </p>
                </div>
            </div>
            @endif

            <!-- Comments Section -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-comments"></i> Komentar Warga</h3>
                    <span class="comment-count">3 komentar</span>
                </div>
                <div class="card-content">
                    <div class="comments-list">
                        <!-- Sample Comment 1 -->
                        <div class="comment-item">
                            <div class="comment-avatar">
                                <img src="/images/avatar1.jpg" alt="Avatar">
                            </div>
                            <div class="comment-content">
                                <div class="comment-header">
                                    <div class="comment-author">Siti Aminah</div>
                                    <div class="comment-time">2 jam lalu</div>
                                </div>
                                <div class="comment-text">
                                    Terima kasih pak RT atas transparansi keuangan yang selalu diberikan. Sangat membantu kita sebagai warga.
                                </div>
                                <div class="comment-actions">
                                    <button class="comment-like">
                                        <i class="fas fa-thumbs-up"></i> 5
                                    </button>
                                    <button class="comment-reply">
                                        <i class="fas fa-reply"></i> Balas
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Sample Comment 2 -->
                        <div class="comment-item">
                            <div class="comment-avatar">
                                <img src="/images/avatar2.jpg" alt="Avatar">
                            </div>
                            <div class="comment-content">
                                <div class="comment-header">
                                    <div class="comment-author">Ahmad Wijaya</div>
                                    <div class="comment-time">5 jam lalu</div>
                                </div>
                                <div class="comment-text">
                                    Alhamdulillah, kas RT kita dalam kondisi sehat. Semoga bisa terus dipertahankan.
                                </div>
                                <div class="comment-actions">
                                    <button class="comment-like">
                                        <i class="fas fa-thumbs-up"></i> 3
                                    </button>
                                    <button class="comment-reply">
                                        <i class="fas fa-reply"></i> Balas
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Sample Comment 3 -->
                        <div class="comment-item">
                            <div class="comment-avatar">
                                <img src="/images/avatar3.jpg" alt="Avatar">
                            </div>
                            <div class="comment-content">
                                <div class="comment-header">
                                    <div class="comment-author">Pak RT</div>
                                    <div class="comment-time">1 hari lalu</div>
                                    <span class="admin-badge">Admin</span>
                                </div>
                                <div class="comment-text">
                                    Pembayaran iuran bulan ini sudah mencapai 90%. Terima kasih kepada seluruh warga yang sudah membayar tepat waktu.
                                </div>
                                <div class="comment-actions">
                                    <button class="comment-like">
                                        <i class="fas fa-thumbs-up"></i> 12
                                    </button>
                                    <button class="comment-reply">
                                        <i class="fas fa-reply"></i> Balas
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Comment -->
                    <div class="add-comment">
                        <div class="comment-avatar">
                            <img src="{{ auth()->user()->avatar ?? '/images/avatar-default.png' }}" alt="Avatar">
                        </div>
                        <div class="comment-form">
                            <textarea placeholder="Tulis komentar..." rows="3"></textarea>
                            <div class="comment-form-actions">
                                <button class="btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                    Kirim
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Transaction Timeline -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> Riwayat</h3>
                </div>
                <div class="card-content">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-icon created">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-title">Transaksi Dibuat</div>
                                <div class="timeline-date">2 Juli 2025, 09:15</div>
                                <div class="timeline-user">oleh Budi Santoso</div>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-icon verified">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-title">Terverifikasi</div>
                                <div class="timeline-date">2 Juli 2025, 10:30</div>
                                <div class="timeline-user">oleh Pak RT</div>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-icon published">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-title">Dipublikasikan</div>
                                <div class="timeline-date">2 Juli 2025, 10:35</div>
                                <div class="timeline-user">Visible to all</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Transactions -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-link"></i> Transaksi Terkait</h3>
                </div>
                <div class="card-content">
                    <div class="related-transactions">
                        <div class="related-item">
                            <div class="related-icon income">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <div class="related-details">
                                <div class="related-title">Iuran Bulanan November</div>
                                <div class="related-amount">+Rp 750.000</div>
                            </div>
                        </div>
                        
                        <div class="related-item">
                            <div class="related-icon income">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <div class="related-details">
                                <div class="related-title">Iuran Bulanan Oktober</div>
                                <div class="related-amount">+Rp 725.000</div>
                            </div>
                        </div>
                        
                        <div class="related-item">
                            <div class="related-icon income">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <div class="related-details">
                                <div class="related-title">Iuran Bulanan September</div>
                                <div class="related-amount">+Rp 700.000</div>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="view-all-related">
                        Lihat Semua <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Share -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-share-alt"></i> Bagikan</h3>
                </div>
                <div class="card-content">
                    <div class="share-buttons">
                        <button class="share-btn whatsapp">
                            <i class="fab fa-whatsapp"></i>
                            WhatsApp
                        </button>
                        <button class="share-btn telegram">
                            <i class="fab fa-telegram"></i>
                            Telegram
                        </button>
                        <button class="share-btn link">
                            <i class="fas fa-link"></i>
                            Salin Link
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Konfirmasi Hapus</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus transaksi ini?</p>
            <p class="warning">Tindakan ini tidak dapat dibatalkan!</p>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeModal()">Batal</button>
            <button class="btn-danger" onclick="confirmDelete()">Ya, Hapus</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/kas/show.js') }}"></script>
@endsection