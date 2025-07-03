@extends('layouts.app')

@section('title', 'Tambah Transaksi Kas RT')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/kas.css') }}">
@endsection

@section('content')
<div class="kas-container">
    <!-- Header Section -->
    <div class="form-header">
        <div class="header-content">
            <h1><i class="fas fa-plus-circle"></i> Tambah Transaksi Kas</h1>
            <p>Input transaksi pemasukan atau pengeluaran kas RT secara transparan</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('kas.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Ringkasan
            </a>
        </div>
    </div>

    <!-- Form Section -->
    <div class="form-grid">
        <div class="main-form">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-edit"></i> Detail Transaksi</h2>
                    <div class="form-progress">
                        <span class="step active">1</span>
                        <span class="step">2</span>
                        <span class="step">3</span>
                    </div>
                </div>
                <div class="card-content">
                    <form id="kasForm" action="{{ route('kas.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Step 1: Basic Info -->
                        <div class="form-step active" id="step1">
                            <div class="form-section">
                                <h3>Informasi Dasar</h3>
                                
                                <div class="form-row">
                                    <div class="form-group half">
                                        <label for="type">Jenis Transaksi *</label>
                                        <select name="type" id="type" class="form-control" required>
                                            <option value="">Pilih jenis transaksi</option>
                                            <option value="income">Pemasukan</option>
                                            <option value="expense">Pengeluaran</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group half">
                                        <label for="amount">Jumlah (Rp) *</label>
                                        <input type="number" name="amount" id="amount" class="form-control" 
                                               placeholder="0" min="1" required>
                                        <div class="amount-display" id="amountDisplay"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="title">Judul Transaksi *</label>
                                    <input type="text" name="title" id="title" class="form-control" 
                                           placeholder="Contoh: Iuran Bulanan, Pembelian Lampu Jalan, dll" required>
                                </div>

                                <div class="form-group">
                                    <label for="description">Deskripsi</label>
                                    <textarea name="description" id="description" class="form-control" rows="4"
                                              placeholder="Jelaskan detail transaksi ini..."></textarea>
                                    <small class="form-hint">Berikan penjelasan yang jelas untuk transparansi</small>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Categories & Date -->
                        <div class="form-step" id="step2">
                            <div class="form-section">
                                <h3>Kategori & Waktu</h3>
                                
                                <div class="form-row">
                                    <div class="form-group half">
                                        <label for="category">Kategori *</label>
                                        <select name="category" id="category" class="form-control" required>
                                            <option value="">Pilih kategori</option>
                                            <optgroup label="Pemasukan">
                                                <option value="iuran">Iuran Warga</option>
                                                <option value="donasi">Donasi</option>
                                                <option value="bantuan">Bantuan Pemerintah</option>
                                                <option value="lainnya_masuk">Lainnya</option>
                                            </optgroup>
                                            <optgroup label="Pengeluaran">
                                                <option value="keamanan">Keamanan</option>
                                                <option value="infrastruktur">Infrastruktur</option>
                                                <option value="acara">Acara RT</option>
                                                <option value="operasional">Operasional</option>
                                                <option value="lainnya_keluar">Lainnya</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group half">
                                        <label for="transaction_date">Tanggal Transaksi *</label>
                                        <input type="date" name="transaction_date" id="transaction_date" 
                                               class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="payment_method">Metode Pembayaran</label>
                                    <select name="payment_method" id="payment_method" class="form-control">
                                        <option value="cash">Tunai</option>
                                        <option value="transfer">Transfer Bank</option>
                                        <option value="ewallet">E-Wallet</option>
                                        <option value="check">Cek</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="is_recurring" id="is_recurring" value="1">
                                        Transaksi Berulang (Bulanan)
                                    </label>
                                    <small class="form-hint">Centang jika ini adalah transaksi rutin setiap bulan</small>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Attachments & Confirmation -->
                        <div class="form-step" id="step3">
                            <div class="form-section">
                                <h3>Lampiran & Konfirmasi</h3>
                                
                                <div class="form-group">
                                    <label for="attachments">Lampiran (Foto/Dokumen)</label>
                                    <div class="file-upload-area" id="fileUploadArea">
                                        <div class="upload-placeholder">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>Klik atau drag & drop file ke sini</p>
                                            <small>Maksimal 5MB per file (JPG, PNG, PDF)</small>
                                        </div>
                                        <input type="file" name="attachments[]" id="attachments" 
                                               multiple accept=".jpg,.jpeg,.png,.pdf" hidden>
                                    </div>
                                    <div class="uploaded-files" id="uploadedFiles"></div>
                                </div>

                                <div class="form-group">
                                    <label for="notes">Catatan Tambahan</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3"
                                              placeholder="Catatan khusus untuk transparansi..."></textarea>
                                </div>

                                <!-- Summary -->
                                <div class="transaction-summary">
                                    <h4>Ringkasan Transaksi</h4>
                                    <div class="summary-item">
                                        <span>Jenis:</span>
                                        <span id="summaryType">-</span>
                                    </div>
                                    <div class="summary-item">
                                        <span>Judul:</span>
                                        <span id="summaryTitle">-</span>
                                    </div>
                                    <div class="summary-item">
                                        <span>Jumlah:</span>
                                        <span id="summaryAmount">Rp 0</span>
                                    </div>
                                    <div class="summary-item">
                                        <span>Kategori:</span>
                                        <span id="summaryCategory">-</span>
                                    </div>
                                    <div class="summary-item">
                                        <span>Tanggal:</span>
                                        <span id="summaryDate">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Navigation -->
                        <div class="form-navigation">
                            <button type="button" id="prevBtn" class="btn-secondary" style="display: none;">
                                <i class="fas fa-arrow-left"></i>
                                Sebelumnya
                            </button>
                            
                            <div class="nav-spacer"></div>
                            
                            <button type="button" id="nextBtn" class="btn-primary">
                                Selanjutnya
                                <i class="fas fa-arrow-right"></i>
                            </button>
                            
                            <button type="submit" id="submitBtn" class="btn-success" style="display: none;">
                                <i class="fas fa-save"></i>
                                Simpan Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="form-sidebar">
            <!-- Tips -->
            <div class="card tips-card">
                <div class="card-header">
                    <h3><i class="fas fa-lightbulb"></i> Tips Pengisian</h3>
                </div>
                <div class="card-content">
                    <div class="tip-item">
                        <i class="fas fa-check-circle"></i>
                        <p>Berikan judul yang jelas dan mudah dipahami warga</p>
                    </div>
                    <div class="tip-item">
                        <i class="fas fa-check-circle"></i>
                        <p>Lampirkan foto/nota untuk transparansi</p>
                    </div>
                    <div class="tip-item">
                        <i class="fas fa-check-circle"></i>
                        <p>Pilih kategori yang sesuai untuk laporan</p>
                    </div>
                    <div class="tip-item">
                        <i class="fas fa-check-circle"></i>
                        <p>Centang "Berulang" untuk transaksi rutin</p>
                    </div>
                </div>
            </div>

            <!-- Quick Balance -->
            <div class="card balance-card">
                <div class="card-header">
                    <h3><i class="fas fa-wallet"></i> Saldo Saat Ini</h3>
                </div>
                <div class="card-content">
                    <div class="current-balance">
                        <div class="balance-amount">Rp 900.000</div>
                        <div class="balance-status positive">Kondisi Sehat</div>
                    </div>
                    <div class="balance-warning" id="balanceWarning" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Perhatian: Pengeluaran akan melebihi saldo yang tersedia!</p>
                    </div>
                </div>
            </div>

            <!-- Recent Categories -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> Kategori Terakhir</h3>
                </div>
                <div class="card-content">
                    <div class="recent-categories">
                        <span class="category-tag" data-category="iuran">Iuran Warga</span>
                        <span class="category-tag" data-category="keamanan">Keamanan</span>
                        <span class="category-tag" data-category="infrastruktur">Infrastruktur</span>
                        <span class="category-tag" data-category="acara">Acara RT</span>
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
    let currentStep = 1;
    const totalSteps = 3;
    
    // Form navigation
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    nextBtn.addEventListener('click', function() {
        if (validateStep(currentStep)) {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        }
    });
    
    prevBtn.addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });
    
    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
        document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
        
        // Show current step
        document.getElementById('step' + step).classList.add('active');
        document.querySelector('.step:nth-child(' + step + ')').classList.add('active');
        
        // Update navigation buttons
        prevBtn.style.display = step > 1 ? 'block' : 'none';
        nextBtn.style.display = step < totalSteps ? 'block' : 'none';
        submitBtn.style.display = step === totalSteps ? 'block' : 'none';
        
        // Update summary
        if (step === totalSteps) {
            updateSummary();
        }
    }
    
    function validateStep(step) {
        const currentStepElement = document.getElementById('step' + step);
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('error');
                isValid = false;
            } else {
                field.classList.remove('error');
            }
        });
        
        return isValid;
    }
    
    function updateSummary() {
        document.getElementById('summaryType').textContent = 
            document.getElementById('type').selectedOptions[0]?.text || '-';
        document.getElementById('summaryTitle').textContent = 
            document.getElementById('title').value || '-';
        document.getElementById('summaryAmount').textContent = 
            'Rp ' + (parseInt(document.getElementById('amount').value) || 0).toLocaleString('id-ID');
        document.getElementById('summaryCategory').textContent = 
            document.getElementById('category').selectedOptions[0]?.text || '-';
        document.getElementById('summaryDate').textContent = 
            document.getElementById('transaction_date').value || '-';
    }
    
    // Amount formatting
    const amountInput = document.getElementById('amount');
    const amountDisplay = document.getElementById('amountDisplay');
    
    amountInput.addEventListener('input', function() {
        const value = parseInt(this.value) || 0;
        amountDisplay.textContent = 'Rp ' + value.toLocaleString('id-ID');
        
        // Balance warning
        const currentBalance = 900000; // From server
        const type = document.getElementById('type').value;
        const warningEl = document.getElementById('balanceWarning');
        
        if (type === 'expense' && value > currentBalance) {
            warningEl.style.display = 'block';
        } else {
            warningEl.style.display = 'none';
        }
    });
    
    // File upload
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('attachments');
    const uploadedFiles = document.getElementById('uploadedFiles');
    
    fileUploadArea.addEventListener('click', () => fileInput.click());
    
    fileUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    fileUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    fileUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });
    
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });
    
    function handleFiles(files) {
        uploadedFiles.innerHTML = '';
        Array.from(files).forEach(file => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                <i class="fas fa-file"></i>
                <span>${file.name}</span>
                <button type="button" class="remove-file">×</button>
            `;
            uploadedFiles.appendChild(fileItem);
        });
    }
    
    // Category quick select
    document.querySelectorAll('.category-tag').forEach(tag => {
        tag.addEventListener('click', function() {
            const category = this.dataset.category;
            document.getElementById('category').value = category;
        });
    });
});
</script>
@endsection