<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Kas</title>

    <link href="{{ asset('css/createkas.css') }}" rel="stylesheet">
</head>
<body>
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
    
        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    
        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    
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
                                                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
                                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                                            </select>
                                            @error('type')
                                                <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group half">
                                            <label for="amount">Jumlah (Rp) *</label>
                                            <input type="number" name="amount" id="amount" class="form-control" 
                                                   placeholder="0" min="1" value="{{ old('amount') }}" required>
                                            <div class="amount-display" id="amountDisplay"></div>
                                            @error('amount')
                                                <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
    
                                    <div class="form-group">
                                        <label for="title">Judul Transaksi *</label>
                                        <input type="text" name="title" id="title" class="form-control" 
                                               placeholder="Contoh: Iuran Bulanan, Pembelian Lampu Jalan, dll" 
                                               value="{{ old('title') }}" required>
                                        @error('title')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
    
                                    <div class="form-group">
                                        <label for="description">Deskripsi</label>
                                        <textarea name="description" id="description" class="form-control" rows="4"
                                                  placeholder="Jelaskan detail transaksi ini...">{{ old('description') }}</textarea>
                                        <small class="form-hint">Berikan penjelasan yang jelas untuk transparansi</small>
                                        @error('description')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
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
                                                    <option value="iuran" {{ old('category') == 'iuran' ? 'selected' : '' }}>Iuran Warga</option>
                                                    <option value="donasi" {{ old('category') == 'donasi' ? 'selected' : '' }}>Donasi</option>
                                                    <option value="bantuan" {{ old('category') == 'bantuan' ? 'selected' : '' }}>Bantuan Pemerintah</option>
                                                    <option value="lainnya_masuk" {{ old('category') == 'lainnya_masuk' ? 'selected' : '' }}>Lainnya</option>
                                                </optgroup>
                                                <optgroup label="Pengeluaran">
                                                    <option value="keamanan" {{ old('category') == 'keamanan' ? 'selected' : '' }}>Keamanan</option>
                                                    <option value="infrastruktur" {{ old('category') == 'infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                                                    <option value="acara" {{ old('category') == 'acara' ? 'selected' : '' }}>Acara RT</option>
                                                    <option value="operasional" {{ old('category') == 'operasional' ? 'selected' : '' }}>Operasional</option>
                                                    <option value="lainnya_keluar" {{ old('category') == 'lainnya_keluar' ? 'selected' : '' }}>Lainnya</option>
                                                </optgroup>
                                            </select>
                                            @error('category')
                                                <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group half">
                                            <label for="transaction_date">Tanggal Transaksi *</label>
                                            <input type="date" name="transaction_date" id="transaction_date" 
                                                   class="form-control" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                                            @error('transaction_date')
                                                <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
    
                                    <div class="form-group">
                                        <label for="payment_method">Metode Pembayaran</label>
                                        <select name="payment_method" id="payment_method" class="form-control">
                                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                                            <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                            <option value="ewallet" {{ old('payment_method') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                                            <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Cek</option>
                                        </select>
                                        @error('payment_method')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
    
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="is_recurring" id="is_recurring" value="1" 
                                                   {{ old('is_recurring') ? 'checked' : '' }}>
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
                                        @error('attachments')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                        @error('attachments.*')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
    
                                    <div class="form-group">
                                        <label for="notes">Catatan Tambahan</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="3"
                                                  placeholder="Catatan khusus untuk transparansi...">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
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
                            <div class="balance-amount">
                                Rp {{ number_format($currentBalance ?? 900000, 0, ',', '.') }}
                            </div>
                            <div class="balance-status {{ ($currentBalance ?? 900000) > 500000 ? 'positive' : 'warning' }}">
                                {{ ($currentBalance ?? 900000) > 500000 ? 'Kondisi Sehat' : 'Perlu Perhatian' }}
                            </div>
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
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentStep = 1;
        const totalSteps = 3;
        
        // Get current balance from server
        const currentBalance = {{ $currentBalance ?? 900000 }};
        
        // Form navigation
        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const submitBtn = document.getElementById('submitBtn');
        
        // Initialize form with old values if they exist
        initializeForm();
        
        nextBtn.addEventListener('click', function() {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        });
        
        prevBtn.addEventListener('click', function() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });
        
        function initializeForm() {
            // Initialize amount display if there's an old value
            const amountInput = document.getElementById('amount');
            if (amountInput.value) {
                updateAmountDisplay();
            }
            
            // Initialize category filter based on type
            const typeSelect = document.getElementById('type');
            if (typeSelect.value) {
                filterCategoriesByType(typeSelect.value);
            }
        }
        
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
        
        function updateSummary() {
            const typeSelect = document.getElementById('type');
            const titleInput = document.getElementById('title');
            const amountInput = document.getElementById('amount');
            const categorySelect = document.getElementById('category');
            const dateInput = document.getElementById('transaction_date');
            
            document.getElementById('summaryType').textContent = 
                typeSelect.selectedOptions[0]?.text || '-';
            document.getElementById('summaryTitle').textContent = 
                titleInput.value || '-';
            document.getElementById('summaryAmount').textContent = 
                'Rp ' + (parseInt(amountInput.value) || 0).toLocaleString('id-ID');
            document.getElementById('summaryCategory').textContent = 
                categorySelect.selectedOptions[0]?.text || '-';
            document.getElementById('summaryDate').textContent = 
                dateInput.value || '-';
        }
        
        // Type change handler
        document.getElementById('type').addEventListener('change', function() {
            filterCategoriesByType(this.value);
            updateBalanceWarning();
        });
        
        function filterCategoriesByType(type) {
            const categorySelect = document.getElementById('category');
            const optgroups = categorySelect.querySelectorAll('optgroup');
            
            optgroups.forEach(optgroup => {
                const label = optgroup.label;
                if (type === 'income' && label === 'Pemasukan') {
                    optgroup.style.display = 'block';
                } else if (type === 'expense' && label === 'Pengeluaran') {
                    optgroup.style.display = 'block';
                } else {
                    optgroup.style.display = 'none';
                }
            });
            
            // Reset category selection
            categorySelect.value = '';
        }
        
        // Amount formatting
        const amountInput = document.getElementById('amount');
        const amountDisplay = document.getElementById('amountDisplay');
        
        amountInput.addEventListener('input', function() {
            updateAmountDisplay();
            updateBalanceWarning();
        });
        
        function updateAmountDisplay() {
            const value = parseInt(amountInput.value) || 0;
            amountDisplay.textContent = 'Rp ' + value.toLocaleString('id-ID');
        }
        
        function updateBalanceWarning() {
            const value = parseInt(amountInput.value) || 0;
            const type = document.getElementById('type').value;
            const warningEl = document.getElementById('balanceWarning');
            
            if (type === 'expense' && value > currentBalance) {
                warningEl.style.display = 'block';
            } else {
                warningEl.style.display = 'none';
            }
        }
        
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
            const files = e.dataTransfer.files;
            fileInput.files = files;
            handleFiles(files);
        });
        
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });
        
        function handleFiles(files) {
            uploadedFiles.innerHTML = '';
            Array.from(files).forEach((file, index) => {
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert(`File ${file.name} terlalu besar. Maksimal 5MB.`);
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                if (!allowedTypes.includes(file.type)) {
                    alert(`File ${file.name} tidak diizinkan. Hanya JPG, PNG, dan PDF.`);
                    return;
                }
                
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.innerHTML = `
                    <i class="fas fa-file"></i>
                    <span>${file.name}</span>
                    <button type="button" class="remove-file" onclick="removeFile(${index})">×</button>
                `;
                uploadedFiles.appendChild(fileItem);
            });
        }
        
        // Remove file function
        window.removeFile = function(index) {
            const dt = new DataTransfer();
            const files = fileInput.files;
            
            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    dt.items.add(files[i]);
                }
            }
            
            fileInput.files = dt.files;
            handleFiles(fileInput.files);
        }
        
        // Category quick select
        document.querySelectorAll('.category-tag').forEach(tag => {
            tag.addEventListener('click', function() {
                const category = this.dataset.category;
                document.getElementById('category').value = category;
                this.classList.add('selected');
                
                // Remove selected class from others
                document.querySelectorAll('.category-tag').forEach(t => {
                    if (t !== this) t.classList.remove('selected');
                });
            });
        });
        
        // Form submission
        document.getElementById('kasForm').addEventListener('submit', function(e) {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        });
    });
    </script>
</body>
</html>