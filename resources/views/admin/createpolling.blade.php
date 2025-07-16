<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Polling Baru - RT Digital</title>
    
    <link rel="stylesheet" href="{{ asset('css/createpolling.css') }}">
</head>
<body>
    <div class="polling-container">
        <!-- Display Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Display Success Message -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('polling.index') }}">
                <i class="fas fa-poll"></i> Polling
            </a>
            <i class="fas fa-chevron-right"></i>
            <span>Buat Polling Baru</span>
        </div>

        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-info">
                    <h1><i class="fas fa-plus-circle"></i> Buat Polling Baru</h1>
                    <p>Libatkan seluruh warga dalam pengambilan keputusan penting untuk RT</p>
                </div>

                <a href="{{ route('kalender.index') }}" class="btn-secondary">
                    <i class="icon">🔙</i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form Section -->
        <div class="form-section">
            <form id="pollForm" class="poll-form" action="{{ route('admin.polling.store') }}" method="POST">
                @csrf
                
                <!-- Basic Information -->
                <div class="form-card">
                    <div class="card-header">
                        <h3><i class="fas fa-info-circle"></i> Informasi Dasar</h3>
                        <p>Masukkan informasi dasar tentang polling yang akan dibuat</p>
                    </div>
                    <div class="card-content">
                        <div class="form-group">
                            <label for="title">
                                <i class="fas fa-heading"></i>
                                Judul Polling <span class="required">*</span>
                            </label>
                            <input type="text" id="title" name="title" required 
                                   placeholder="Contoh: Pemilihan Jadwal Kerja Bakti Bulanan"
                                   maxlength="100" value="{{ old('title') }}">
                            <div class="char-counter">
                                <span id="titleCounter">{{ old('title') ? strlen(old('title')) : 0 }}</span>/100 karakter
                            </div>
                            @error('title')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">
                                <i class="fas fa-align-left"></i>
                                Deskripsi Polling <span class="required">*</span>
                            </label>
                            <textarea id="description" name="description" required 
                                      placeholder="Jelaskan tujuan polling, latar belakang, dan informasi penting lainnya..."
                                      rows="4" maxlength="500">{{ old('description') }}</textarea>
                            <div class="char-counter">
                                <span id="descCounter">{{ old('description') ? strlen(old('description')) : 0 }}</span>/500 karakter
                            </div>
                            @error('description')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="category">
                                    <i class="fas fa-tag"></i>
                                    Kategori
                                </label>
                                <select id="category" name="category">
                                    <option value="umum" {{ old('category') == 'umum' ? 'selected' : '' }}>Umum</option>
                                    <option value="keamanan" {{ old('category') == 'keamanan' ? 'selected' : '' }}>Keamanan</option>
                                    <option value="kebersihan" {{ old('category') == 'kebersihan' ? 'selected' : '' }}>Kebersihan</option>
                                    <option value="keuangan" {{ old('category') == 'keuangan' ? 'selected' : '' }}>Keuangan</option>
                                    <option value="fasilitas" {{ old('category') == 'fasilitas' ? 'selected' : '' }}>Fasilitas</option>
                                    <option value="kegiatan" {{ old('category') == 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                                    <option value="lainnya" {{ old('category') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('category')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="end_date">
                                    <i class="fas fa-calendar-alt"></i>
                                    Tanggal Berakhir <span class="required">*</span>
                                </label>
                                <input type="datetime-local" id="end_date" name="end_date" required 
                                       value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Poll Options -->
                <div class="form-card">
                    <div class="card-header">
                        <h3><i class="fas fa-list-ul"></i> Pilihan Jawaban</h3>
                        <p>Tambahkan minimal 2 pilihan jawaban untuk polling</p>
                    </div>
                    <div class="card-content">
                        <div class="options-container" id="optionsContainer">
                            @if(old('options'))
                                @foreach(old('options') as $index => $option)
                                    <div class="option-group">
                                        <label>
                                            <i class="fas fa-check-circle"></i>
                                            Pilihan {{ $index + 1 }} @if($index < 2)<span class="required">*</span>@endif
                                        </label>
                                        <div class="option-input">
                                            <input type="text" name="options[]" 
                                                   placeholder="Masukkan pilihan..."
                                                   maxlength="100" value="{{ $option }}"
                                                   @if($index < 2) required @endif>
                                            <button type="button" class="btn-remove-option" @if($index < 2) disabled @endif>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="option-group">
                                    <label>
                                        <i class="fas fa-check-circle"></i>
                                        Pilihan 1 <span class="required">*</span>
                                    </label>
                                    <div class="option-input">
                                        <input type="text" name="options[]" required 
                                               placeholder="Contoh: Sabtu pagi (08:00-10:00)"
                                               maxlength="100">
                                        <button type="button" class="btn-remove-option" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="option-group">
                                    <label>
                                        <i class="fas fa-check-circle"></i>
                                        Pilihan 2 <span class="required">*</span>
                                    </label>
                                    <div class="option-input">
                                        <input type="text" name="options[]" required 
                                               placeholder="Contoh: Minggu pagi (08:00-10:00)"
                                               maxlength="100">
                                        <button type="button" class="btn-remove-option" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <button type="button" id="addOption" class="btn btn-secondary">
                            <i class="fas fa-plus"></i>
                            Tambah Pilihan
                        </button>

                        <div class="options-help">
                            <i class="fas fa-info-circle"></i>
                            <span>Maksimal 8 pilihan. Setiap pilihan maksimal 100 karakter</span>
                        </div>

                        @error('options')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        @error('options.*')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Additional Settings -->
                <div class="form-card">
                    <div class="card-header">
                        <h3><i class="fas fa-cog"></i> Pengaturan Tambahan</h3>
                        <p>Konfigurasi tambahan untuk polling</p>
                    </div>
                    <div class="card-content">
                        <div class="settings-group">
                            <div class="setting-item">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="allow_multiple" id="allowMultiple" value="1" 
                                           {{ old('allow_multiple') ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    <div class="setting-info">
                                        <strong>Izinkan Pilihan Ganda</strong>
                                        <p>Warga dapat memilih lebih dari satu opsi</p>
                                    </div>
                                </label>
                            </div>

                            <div class="setting-item">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="anonymous" id="anonymous" value="1" 
                                           {{ old('anonymous') ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    <div class="setting-info">
                                        <strong>Voting Anonim</strong>
                                        <p>Nama pemilih tidak akan ditampilkan di hasil</p>
                                    </div>
                                </label>
                            </div>

                            <div class="setting-item">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="notify_result" id="notifyResult" value="1" 
                                           {{ old('notify_result') ? 'checked' : '' }} checked>
                                    <span class="checkmark"></span>
                                    <div class="setting-info">
                                        <strong>Notifikasi Hasil</strong>
                                        <p>Kirim notifikasi ketika polling berakhir</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('polling.index') }}" class="btn btn-cancel">
                        <i class="fas fa-times"></i>
                        Batal
                    </a> 
                    <button type="button" id="previewBtn" class="btn btn-secondary">
                        <i class="fas fa-eye"></i>
                        Preview
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Publikasikan Polling
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview Modal -->
        <div id="previewModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="fas fa-eye"></i> Preview Polling</h3>
                    <button type="button" class="btn-close" onclick="closePreview()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="preview-poll">
                        <div class="poll-card active-poll">
                            <div class="poll-header">
                                <div class="poll-meta">
                                    <span class="poll-status status-active">
                                        <i class="fas fa-circle"></i> Aktif
                                    </span>
                                    <span class="poll-date">
                                        <i class="fas fa-calendar"></i> <span id="previewEndDate">-</span>
                                    </span>
                                </div>
                                <div class="poll-votes">
                                    <i class="fas fa-users"></i>
                                    <span class="vote-count">0</span> suara
                                </div>
                            </div>
                            <div class="poll-content">
                                <h3 id="previewTitle">Judul Polling</h3>
                                <p class="poll-description" id="previewDescription">Deskripsi polling...</p>
                                <div class="poll-creator">
                                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&crop=face" alt="You">
                                    <div class="creator-info">
                                        <span class="creator-name">{{ Auth::user()->name ?? 'Anda' }}</span>
                                        <span class="creator-role">Warga</span>
                                    </div>
                                </div>
                            </div>
                            <div class="poll-actions">
                                <button class="btn btn-vote" disabled>
                                    <i class="fas fa-vote-yea"></i>
                                    Lihat & Vote
                                </button>
                                <div class="poll-stats">
                                    <span class="participation">
                                        <i class="fas fa-chart-line"></i>
                                        0% partisipasi
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="preview-options">
                            <h4>Preview Pilihan:</h4>
                            <div id="previewOptionsList"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize option count based on existing options
        let optionCount = 2; // Start with 2 default options
        const maxOptions = 8;

        // Initialize character counters on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Update option count based on existing options
            const existingOptions = document.querySelectorAll('.option-group');
            optionCount = existingOptions.length;
            
            // Initialize character counters
            const titleInput = document.getElementById('title');
            const descInput = document.getElementById('description');
            const titleCounter = document.getElementById('titleCounter');
            const descCounter = document.getElementById('descCounter');
            
            updateCharCounter(titleInput, titleCounter);
            updateCharCounter(descInput, descCounter);
            
            // Set minimum date to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            tomorrow.setHours(0, 0, 0, 0); // Set to beginning of day
            
            const endDateInput = document.getElementById('end_date');
            endDateInput.min = tomorrow.toISOString().slice(0, 16);
            
            // If no value is set, set default to tomorrow at 23:59
            if (!endDateInput.value) {
                tomorrow.setHours(23, 59, 0, 0);
                endDateInput.value = tomorrow.toISOString().slice(0, 16);
            }
            
            // Update option count and buttons
            updateRemoveButtons();
            updateAddButtonVisibility();
        });

        // Character counter
        function updateCharCounter(input, counter) {
            const current = input.value.length;
            const max = parseInt(input.getAttribute('maxlength'));
            counter.textContent = current;
            
            if (current > max * 0.8) {
                counter.style.color = '#e53e3e';
            } else {
                counter.style.color = '#718096';
            }
        }

        document.getElementById('title').addEventListener('input', function() {
            updateCharCounter(this, document.getElementById('titleCounter'));
        });

        document.getElementById('description').addEventListener('input', function() {
            updateCharCounter(this, document.getElementById('descCounter'));
        });

        // Add option functionality
        document.getElementById('addOption').addEventListener('click', function() {
            if (optionCount >= maxOptions) {
                alert('Maksimal 8 pilihan yang diperbolehkan');
                return;
            }

            optionCount++;
            const optionsContainer = document.getElementById('optionsContainer');
            
            const optionGroup = document.createElement('div');
            optionGroup.className = 'option-group';
            optionGroup.innerHTML = `
                <label>
                    <i class="fas fa-check-circle"></i>
                    Pilihan ${optionCount}
                </label>
                <div class="option-input">
                    <input type="text" name="options[]" 
                           placeholder="Masukkan pilihan..."
                           maxlength="100">
                    <button type="button" class="btn-remove-option">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            optionsContainer.appendChild(optionGroup);
            updateRemoveButtons();
            updateAddButtonVisibility();
        });

        // Remove option functionality
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-option') && !e.target.closest('.btn-remove-option').disabled) {
                const optionGroup = e.target.closest('.option-group');
                optionGroup.remove();
                optionCount--;
                updateOptionLabels();
                updateRemoveButtons();
                updateAddButtonVisibility();
            }
        });

        function updateRemoveButtons() {
            const removeButtons = document.querySelectorAll('.btn-remove-option');
            removeButtons.forEach((btn, index) => {
                btn.disabled = index < 2; // First two options can't be removed
            });
        }

        function updateAddButtonVisibility() {
            const addButton = document.getElementById('addOption');
            if (optionCount >= maxOptions) {
                addButton.style.display = 'none';
            } else {
                addButton.style.display = 'inline-flex';
            }
        }

        function updateOptionLabels() {
            const optionGroups = document.querySelectorAll('.option-group');
            optionGroups.forEach((group, index) => {
                const label = group.querySelector('label');
                const icon = label.querySelector('i');
                const required = index < 2 ? ' <span class="required">*</span>' : '';
                label.innerHTML = `${icon.outerHTML} Pilihan ${index + 1}${required}`;
                
                // Update required attribute on input
                const input = group.querySelector('input');
                if (index < 2) {
                    input.setAttribute('required', 'required');
                } else {
                    input.removeAttribute('required');
                }
            });
        }

        // Preview functionality
        document.getElementById('previewBtn').addEventListener('click', function() {
            const title = document.getElementById('title').value || 'Judul Polling';
            const description = document.getElementById('description').value || 'Deskripsi polling...';
            const endDate = document.getElementById('end_date').value;
            
            // Update preview content
            document.getElementById('previewTitle').textContent = title;
            document.getElementById('previewDescription').textContent = description;
            
            if (endDate) {
                const date = new Date(endDate);
                document.getElementById('previewEndDate').textContent = 
                    'Berakhir: ' + date.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
            }
            
            // Update preview options
            const options = Array.from(document.querySelectorAll('input[name="options[]"]'))
                .map(input => input.value.trim())
                .filter(value => value !== '');
            
            const optionsList = document.getElementById('previewOptionsList');
            optionsList.innerHTML = '';
            
            if (options.length > 0) {
                options.forEach((option, index) => {
                    const optionDiv = document.createElement('div');
                    optionDiv.className = 'preview-option';
                    optionDiv.innerHTML = `
                        <input type="radio" name="preview-vote" id="preview-${index}" disabled>
                        <label for="preview-${index}">${option}</label>
                    `;
                    optionsList.appendChild(optionDiv);
                });
            } else {
                optionsList.innerHTML = '<p>Belum ada pilihan yang diisi</p>';
            }
            
            document.getElementById('previewModal').style.display = 'flex';
        });

        function closePreview() {
            document.getElementById('previewModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('previewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePreview();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('previewModal').style.display === 'flex') {
                closePreview();
            }
        });

        // Form validation
        document.getElementById('pollForm').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();
            const endDate = document.getElementById('end_date').value;
            
            // Check required fields
            if (!title || !description || !endDate) {
                e.preventDefault();
                alert('Harap isi semua field yang wajib');
                return;
            }
            
            // Get all option values
            const options = Array.from(document.querySelectorAll('input[name="options[]"]'))
                .map(input => input.value.trim())
                .filter(value => value !== '');
            
            // Check minimum options
            if (options.length < 2) {
                e.preventDefault();
                alert('Minimal 2 pilihan harus diisi');
                return;
            }
            
            // Check for duplicate options
            const uniqueOptions = [...new Set(options.map(opt => opt.toLowerCase()))];
            if (uniqueOptions.length !== options.length) {
                e.preventDefault();
                alert('Pilihan tidak boleh sama');
                return;
            }
            
            // Check if end date is in the future
            const endDateTime = new Date(endDate);
            const now = new Date();
            if (endDateTime <= now) {
                e.preventDefault();
                alert('Tanggal berakhir harus di masa depan');
                return;
            }
            
            // Check if end date is not too far in the future (optional validation)
            const maxFutureDate = new Date();
            maxFutureDate.setFullYear(maxFutureDate.getFullYear() + 1);
            if (endDateTime > maxFutureDate) {
                if (!confirm('Tanggal berakhir lebih dari 1 tahun. Apakah Anda yakin?')) {
                    e.preventDefault();
                    return;
                }
            }
            
            // Show loading state
            const submitBtn = document.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            
            // Reset button after 5 seconds as fallback
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 5000);
        });
    </script>
</body>
</html>