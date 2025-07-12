@extends('layouts.app')

@section('title', 'Buat Laporan Baru')

@section('content')
    <div class="create-laporan-container">
        <div class="header-section">
            <div class="breadcrumb">
                <a href="{{ route('laporan.index') }}" class="breadcrumb-item">
                    <i class="icon-arrow-left"></i>
                    Kembali ke Daftar Laporan
                </a>
            </div>

            <div class="header-content">
                <h1 class="page-title">
                    <i class="icon-report-new"></i>
                    Buat Laporan Baru
                </h1>
                <p class="page-subtitle">Laporkan masalah atau keluhan yang perlu ditangani oleh RT</p>
            </div>
        </div>

        <div class="form-container">
            <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data" id="reportForm">
                @csrf

                <div class="form-sections">
                    <!-- Section 1: Informasi Dasar -->
                    <div class="form-section active" data-section="1">
                        <div class="section-header">
                            <h3>
                                <span class="step-number">1</span>
                                Informasi Dasar
                            </h3>
                            <p>Berikan judul dan kategori laporan Anda</p>
                        </div>

                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label for="title" class="form-label">
                                    <i class="icon-title"></i>
                                    Judul Laporan
                                </label>
                                <input type="text" id="title" name="title"
                                    class="form-input @error('title') error @enderror"
                                    placeholder="Contoh: Jalan rusak di depan rumah nomor 15" value="{{ old('title') }}"
                                    required>
                                @error('title')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="category" class="form-label">
                                    <i class="icon-category"></i>
                                    Kategori
                                </label>
                                <select id="category" name="category" class="form-select @error('category') error @enderror"
                                    required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="infrastruktur" {{ old('category') == 'infrastruktur' ? 'selected' : '' }}>
                                        🏗️ Infrastruktur
                                    </option>
                                    <option value="kebersihan" {{ old('category') == 'kebersihan' ? 'selected' : '' }}>
                                        🧹 Kebersihan
                                    </option>
                                    <option value="keamanan" {{ old('category') == 'keamanan' ? 'selected' : '' }}>
                                        🔒 Keamanan
                                    </option>
                                    <option value="sosial" {{ old('sosial') == 'sosial' ? 'selected' : '' }}>
                                        👥 Sosial
                                    </option>
                                    <option value="lainnya" {{ old('category') == 'lainnya' ? 'selected' : '' }}>
                                        ⚡ Lainnya
                                    </option>
                                </select>
                                @error('category')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="priority" class="form-label">
                                    <i class="icon-priority"></i>
                                    Tingkat Kepentingan
                                </label>
                                <select id="priority" name="priority" class="form-select @error('priority') error @enderror"
                                    required>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                        🟢 Rendah
                                    </option>
                                    <option value="medium" {{ old('priority') == 'medium' || old('priority') == null ? 'selected' : '' }}>
                                        🟡 Sedang
                                    </option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                        🔴 Tinggi
                                    </option>
                                </select>
                                @error('priority')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Detail Laporan -->
                    <div class="form-section" data-section="2">
                        <div class="section-header">
                            <h3>
                                <span class="step-number">2</span>
                                Detail Laporan
                            </h3>
                            <p>Jelaskan masalah secara detail dan lokasi kejadian</p>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">
                                <i class="icon-description"></i>
                                Deskripsi Masalah
                            </label>
                            <textarea id="description" name="description"
                                class="form-textarea @error('description') error @enderror" rows="6"
                                placeholder="Jelaskan masalah secara detail. Semakin jelas deskripsi, semakin mudah untuk ditindaklanjuti..."
                                required>{{ old('description') }}</textarea>
                            <div class="textarea-counter">
                                <span id="charCount">0</span>/1000 karakter
                            </div>
                            @error('description')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="location" class="form-label">
                                <i class="icon-location"></i>
                                Lokasi Kejadian
                            </label>
                            <input type="text" id="location" name="location"
                                class="form-input @error('location') error @enderror"
                                placeholder="Contoh: Jalan Mawar No. 15, RT 05" value="{{ old('location') }}" required>
                            @error('location')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Section 3: Dokumentasi -->
                    <div class="form-section" data-section="3">
                        <div class="section-header">
                            <h3>
                                <span class="step-number">3</span>
                                Dokumentasi
                            </h3>
                            <p>Lampirkan foto untuk memperjelas laporan (opsional)</p>
                        </div>

                        <div class="form-group">
                            <label for="image" class="form-label">
                                <i class="icon-camera"></i>
                                Foto Pendukung
                            </label>
                            <div class="file-upload-area" id="fileUploadArea">
                                <input type="file" id="image" name="image"
                                    class="file-input @error('image') error @enderror" accept="image/*">
                                <div class="upload-content" id="uploadContent">
                                    <div class="upload-icon">
                                        <i class="icon-upload"></i>
                                    </div>
                                    <p class="upload-text">
                                        <strong>Klik untuk upload foto</strong> atau drag & drop
                                    </p>
                                    <p class="upload-hint">Format: JPG, PNG, GIF (Max: 5MB)</p>
                                </div>
                                <div class="image-preview" id="imagePreview" style="display: none;">
                                    <img id="previewImg" src="" alt="Preview">
                                    <button type="button" class="remove-image" id="removeImage">
                                        <i class="icon-close"></i>
                                    </button>
                                </div>
                            </div>
                            @error('image')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="form-navigation">
                    <button type="button" class="btn-secondary" id="prevBtn" style="display: none;">
                        <i class="icon-arrow-left"></i>
                        Sebelumnya
                    </button>

                    <div class="progress-steps">
                        <div class="step active" data-step="1">1</div>
                        <div class="step" data-step="2">2</div>
                        <div class="step" data-step="3">3</div>
                    </div>

                    <button type="button" class="btn-primary" id="nextBtn">
                        Selanjutnya
                        <i class="icon-arrow-right"></i>
                    </button>

                    <button type="submit" class="btn-success" id="submitBtn" style="display: none;">
                        <i class="icon-send"></i>
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>

        <!-- Confirmation Modal -->
        <div class="modal-overlay" id="confirmModal" style="display: none;">
            <div class="modal">
                <div class="modal-header">
                    <h3>Konfirmasi Laporan</h3>
                    <button type="button" class="modal-close" id="closeModal">
                        <i class="icon-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin mengirim laporan ini?</p>
                    <p class="modal-hint">Setelah dikirim, laporan akan segera ditinjau oleh pengurus RT.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="cancelSubmit">Batal</button>
                    <button type="button" class="btn-success" id="confirmSubmit">
                        <i class="icon-send"></i>
                        Ya, Kirim Laporan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Multi-step form functionality
        let currentStep = 1;
        const totalSteps = 3;

        // DOM elements
        const sections = document.querySelectorAll('.form-section');
        const steps = document.querySelectorAll('.step');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('reportForm');
        const confirmModal = document.getElementById('confirmModal');

        // Initialize character counter on page load
        document.addEventListener('DOMContentLoaded', function () {
            const description = document.getElementById('description');
            const charCount = document.getElementById('charCount');

            // Set initial character count
            if (description.value) {
                charCount.textContent = description.value.length;
            }
        });

        // Character counter
        const description = document.getElementById('description');
        const charCount = document.getElementById('charCount');

        if (description && charCount) {
            description.addEventListener('input', function () {
                const count = this.value.length;
                charCount.textContent = count;

                if (count > 1000) {
                    charCount.style.color = '#e53e3e';
                    this.style.borderColor = '#e53e3e';
                } else if (count > 800) {
                    charCount.style.color = '#d69e2e';
                    this.style.borderColor = '#d69e2e';
                } else {
                    charCount.style.color = '#38a169';
                    this.style.borderColor = '';
                }
            });
        }

        // File upload functionality
        const fileInput = document.getElementById('image');
        const uploadArea = document.getElementById('fileUploadArea');
        const uploadContent = document.getElementById('uploadContent');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        const removeImage = document.getElementById('removeImage');

        if (uploadArea && fileInput) {
            uploadArea.addEventListener('click', () => fileInput.click());

            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('drag-over');
            });

            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('drag-over');
            });

            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('drag-over');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFileSelect(files[0]);
                }
            });

            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    handleFileSelect(e.target.files[0]);
                }
            });
        }

        if (removeImage) {
            removeImage.addEventListener('click', (e) => {
                e.stopPropagation();
                fileInput.value = '';
                uploadContent.style.display = 'block';
                imagePreview.style.display = 'none';
            });
        }

        function handleFileSelect(file) {
            if (file && file.type.startsWith('image/')) {
                // Check file size (5MB limit)
                if (file.size > 5 * 1024 * 1024) {
                    showToast('File terlalu besar. Maksimal 5MB.', 'error');
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImg.src = e.target.result;
                    uploadContent.style.display = 'none';
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                showToast('File harus berupa gambar (JPG, PNG, GIF)', 'error');
            }
        }

        // Step navigation
        function showStep(step) {
            sections.forEach((section, index) => {
                section.classList.toggle('active', index + 1 === step);
            });

            steps.forEach((stepEl, index) => {
                stepEl.classList.toggle('active', index + 1 <= step);
                stepEl.classList.toggle('completed', index + 1 < step);
            });

            prevBtn.style.display = step === 1 ? 'none' : 'flex';
            nextBtn.style.display = step === totalSteps ? 'none' : 'flex';
            submitBtn.style.display = step === totalSteps ? 'flex' : 'none';
        }

        function validateStep(step) {
            let isValid = true;
            const currentSection = document.querySelector(`[data-section="${step}"]`);
            const requiredFields = currentSection.querySelectorAll('[required]');

            requiredFields.forEach(field => {
                field.classList.remove('error');

                if (field.type === 'select-one' && !field.value) {
                    field.classList.add('error');
                    isValid = false;
                } else if (field.type !== 'select-one' && !field.value.trim()) {
                    field.classList.add('error');
                    isValid = false;
                }

                // Special validation for description length
                if (field.name === 'description' && field.value.length > 1000) {
                    field.classList.add('error');
                    isValid = false;
                }
            });

            return isValid;
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `<i class="icon-${type === 'error' ? 'warning' : 'info'}"></i> ${message}`;

            // Add styles if not already defined
            if (!document.querySelector('.toast-styles')) {
                const style = document.createElement('style');
                style.className = 'toast-styles';
                style.textContent = `
                    .toast {
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        padding: 12px 20px;
                        border-radius: 8px;
                        color: white;
                        font-weight: 500;
                        z-index: 10000;
                        min-width: 300px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                        animation: slideIn 0.3s ease;
                    }
                    .toast.error { background-color: #e53e3e; }
                    .toast.success { background-color: #38a169; }
                    .toast.info { background-color: #3182ce; }
                    @keyframes slideIn {
                        from { transform: translateX(100%); opacity: 0; }
                        to { transform: translateX(0); opacity: 1; }
                    }
                `;
                document.head.appendChild(style);
            }

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Navigation event listeners
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                if (validateStep(currentStep)) {
                    if (currentStep < totalSteps) {
                        currentStep++;
                        showStep(currentStep);
                    }
                } else {
                    showToast('Mohon lengkapi semua field yang wajib diisi', 'error');
                }
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
        }

        // Form submission
        if (submitBtn) {
            submitBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (validateStep(currentStep)) {
                    confirmModal.style.display = 'flex';
                } else {
                    showToast('Mohon lengkapi semua field yang wajib diisi', 'error');
                }
            });
        }

        // Modal handlers
        if (document.getElementById('confirmSubmit')) {
            document.getElementById('confirmSubmit').addEventListener('click', () => {
                confirmModal.style.display = 'none';
                showToast('Mengirim laporan...', 'info');
                form.submit();
            });
        }

        if (document.getElementById('cancelSubmit')) {
            document.getElementById('cancelSubmit').addEventListener('click', () => {
                confirmModal.style.display = 'none';
            });
        }

        if (document.getElementById('closeModal')) {
            document.getElementById('closeModal').addEventListener('click', () => {
                confirmModal.style.display = 'none';
            });
        }

        // Close modal on background click
        if (confirmModal) {
            confirmModal.addEventListener('click', (e) => {
                if (e.target === confirmModal) {
                    confirmModal.style.display = 'none';
                }
            });
        }

        // Initialize form
        showStep(1);
    </script>
@endsection