<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Timeline</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/createtimeline.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbarcomponents.css') }}">


</head>

<body>
    @include('components.navbar')

    <div class="create-post-container">
        <!-- Header Section -->
        <div class="create-header">
            <div class="header-content">
                <a href="{{ route('timeline.index') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="header-text">
                    <h1 class="page-title">Buat Posting Baru</h1>
                    <p class="page-subtitle">Bagikan informasi jual beli atau jasa dengan warga RT</p>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Messages -->
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form Section -->
        <div class="form-container">
            <form action="{{ route('timeline.store') }}" method="POST" enctype="multipart/form-data"
                id="createPostForm">
                @csrf

                <!-- Category Selection -->
                <div class="form-group category-group">
                    <label class="form-label">Kategori Posting <span class="required">*</span></label>
                    <div class="category-options">
                        <input type="radio" name="category" value="jual" id="cat-jual" {{ old('category') == 'jual' ? 'checked' : '' }} required>
                        <label for="cat-jual" class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-tag"></i>
                            </div>
                            <div class="category-text">
                                <h4>Jual Beli</h4>
                                <p>Jual atau beli barang</p>
                            </div>
                        </label>

                        <input type="radio" name="category" value="jasa" id="cat-jasa" {{ old('category') == 'jasa' ? 'checked' : '' }} required>
                        <label for="cat-jasa" class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <div class="category-text">
                                <h4>Jasa</h4>
                                <p>Tawarkan atau cari jasa</p>
                            </div>
                        </label>

                        <input type="radio" name="category" value="info" id="cat-info" {{ old('category') == 'info' ? 'checked' : '' }} required>
                        <label for="cat-info" class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="category-text">
                                <h4>Info Umum</h4>
                                <p>Berbagi informasi umum</p>
                            </div>
                        </label>
                    </div>
                    @error('category')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Title -->
                <div class="form-group">
                    <label for="title" class="form-label">Judul Posting <span class="required">*</span></label>
                    <input type="text" name="title" id="title" class="form-input @error('title') is-invalid @enderror"
                        placeholder="Contoh: Jual Motor Bekas / Jasa Servis AC / Info Kerja Bakti"
                        value="{{ old('title') }}" maxlength="100" required>
                    @error('title')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Content -->
                <div class="form-group">
                    <label for="content" class="form-label">Deskripsi Detail <span class="required">*</span></label>
                    <textarea name="content" id="content" class="form-textarea @error('content') is-invalid @enderror"
                        rows="6" placeholder="Jelaskan detail posting Anda..." maxlength="1000"
                        required>{{ old('content') }}</textarea>
                    <div class="char-counter">
                        <span id="charCount">{{ old('content') ? strlen(old('content')) : 0 }}</span>/1000 karakter
                    </div>
                    @error('content')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Price (Show only for Jual Beli) -->
                <div class="form-group price-group" id="priceGroup" style="display: none;">
                    <label for="price" class="form-label">Harga</label>
                    <div class="price-input-wrapper">
                        <span class="currency">Rp</span>
                        <input type="number" name="price" id="price"
                            class="form-input price-input @error('price') is-invalid @enderror" placeholder="0" min="0"
                            step="1000" value="{{ old('price') }}">
                    </div>
                    <small class="form-hint">Kosongkan jika harga nego atau gratis</small>
                    @error('price')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Contact Information -->
                <div class="form-group contact-group" id="contactGroup">
                    <label class="form-label">Informasi Kontak</label>
                    <div class="contact-inputs">
                        <div class="contact-field">
                            <label for="phone" class="contact-label">
                                <i class="fas fa-phone"></i>
                                Nomor Telepon
                            </label>
                            <input type="tel" name="phone" id="phone"
                                class="form-input @error('phone') is-invalid @enderror" placeholder="08123456789"
                                pattern="[0-9]{10,15}" value="{{ old('phone') }}">
                        </div>
                        <div class="contact-field">
                            <label for="whatsapp" class="contact-label">
                                <i class="fab fa-whatsapp"></i>
                                WhatsApp
                            </label>
                            <input type="tel" name="whatsapp" id="whatsapp"
                                class="form-input @error('whatsapp') is-invalid @enderror" placeholder="628123456789"
                                pattern="[0-9]{10,15}" value="{{ old('whatsapp') }}">
                        </div>
                    </div>
                    <small class="form-hint" id="contactHint">Minimal satu kontak harus diisi untuk kategori Jual Beli
                        dan
                        Jasa</small>
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    @error('whatsapp')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Image Upload -->
                <div class="form-group">
                    <label for="images" class="form-label">Upload Gambar</label>
                    <div class="image-upload-area" id="imageUploadArea">
                        <div class="upload-placeholder">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Klik untuk upload gambar atau drag & drop</p>
                            <small>Maksimal 5 gambar, ukuran maks 2MB per file (JPG, PNG, GIF)</small>
                        </div>
                        <input type="file" name="images[]" id="images" multiple
                            accept="image/jpeg,image/png,image/gif,image/jpg"
                            class="image-input @error('images') is-invalid @enderror">
                    </div>
                    <div class="image-preview" id="imagePreview"></div>
                    <div class="image-counter">
                        <span id="imageCount">0</span>/5 gambar
                    </div>
                    @error('images')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    @error('images.*')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="history.back()">
                        <i class="fas fa-times"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn-primary" id="submitBtn">
                        <i class="fas fa-paper-plane"></i>
                        <span class="btn-text">Posting Sekarang</span>
                        <span class="loading-spinner" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i>
                            Memposting...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview Section -->
        <div class="preview-container" id="previewContainer" style="display: none;">
            <h3 class="preview-title">Preview Posting</h3>
            <div class="preview-card" id="previewCard">
                <!-- Preview content will be generated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-content">
            <div class="spinner"></div>
            <p>Sedang memposting...</p>
        </div>
    </div>


    <script src="{{ asset('js/timeline/create.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categoryRadios = document.querySelectorAll('input[name="category"]');
            const priceGroup = document.getElementById('priceGroup');
            const contactGroup = document.getElementById('contactGroup');
            const contactHint = document.getElementById('contactHint');
            const contentTextarea = document.getElementById('content');
            const charCount = document.getElementById('charCount');
            const submitBtn = document.getElementById('submitBtn');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const imageInput = document.getElementById('images');
            const imagePreview = document.getElementById('imagePreview');
            const imageCount = document.getElementById('imageCount');
            const uploadArea = document.getElementById('imageUploadArea');

            // Store selected files
            let selectedFiles = [];

            // Handle category change
            categoryRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    const selectedCategory = this.value;

                    if (selectedCategory === 'jual') {
                        priceGroup.style.display = 'block';
                        contactGroup.style.display = 'block';
                        contactHint.style.display = 'block';
                    } else if (selectedCategory === 'jasa') {
                        priceGroup.style.display = 'none';
                        contactGroup.style.display = 'block';
                        contactHint.style.display = 'block';
                    } else if (selectedCategory === 'info') {
                        priceGroup.style.display = 'none';
                        contactGroup.style.display = 'block';
                        contactHint.style.display = 'none';
                    }
                });
            });

            // Character counter
            contentTextarea.addEventListener('input', function () {
                const currentLength = this.value.length;
                charCount.textContent = currentLength;

                if (currentLength > 1000) {
                    charCount.style.color = 'red';
                } else if (currentLength > 800) {
                    charCount.style.color = 'orange';
                } else {
                    charCount.style.color = 'inherit';
                }
            });

            // Initialize category state if old value exists
            const checkedCategory = document.querySelector('input[name="category"]:checked');
            if (checkedCategory) {
                checkedCategory.dispatchEvent(new Event('change'));
            }

            // File validation function
            function validateFile(file) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (!allowedTypes.includes(file.type)) {
                    alert(`File ${file.name} bukan format gambar yang diizinkan. Gunakan JPG, PNG, atau GIF.`);
                    return false;
                }

                if (file.size > maxSize) {
                    alert(`File ${file.name} terlalu besar. Maksimal 2MB per file.`);
                    return false;
                }

                return true;
            }

            // Update file input with selected files
            function updateFileInput() {
                const dt = new DataTransfer();
                selectedFiles.forEach(file => dt.items.add(file));
                imageInput.files = dt.files;

                // Update counter
                imageCount.textContent = selectedFiles.length;

                // Update counter color
                if (selectedFiles.length >= 5) {
                    imageCount.style.color = 'red';
                } else if (selectedFiles.length >= 4) {
                    imageCount.style.color = 'orange';
                } else {
                    imageCount.style.color = 'inherit';
                }
            }

            // Render image previews
            function renderImagePreviews() {
                imagePreview.innerHTML = '';

                selectedFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'preview-item';
                        previewItem.innerHTML = `
                            <img src="${e.target.result}" alt="Preview ${index + 1}">
                            <div class="image-info">
                                <span class="image-name">${file.name}</span>
                                <span class="image-size">${formatFileSize(file.size)}</span>
                            </div>
                            <button type="button" class="remove-image" onclick="removeImage(${index})">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                        imagePreview.appendChild(previewItem);
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Handle file selection
            function handleFileSelection(files) {
                const newFiles = Array.from(files);

                // Check if adding new files would exceed limit
                if (selectedFiles.length + newFiles.length > 5) {
                    alert('Maksimal 5 gambar yang dapat diupload.');
                    return;
                }

                // Validate each file
                const validFiles = newFiles.filter(file => validateFile(file));

                // Add valid files to selected files
                selectedFiles = selectedFiles.concat(validFiles);

                // Update input and preview
                updateFileInput();
                renderImagePreviews();
            }

            // Handle image input change
            imageInput.addEventListener('change', function () {
                // Clear previous selections and start fresh
                selectedFiles = [];
                handleFileSelection(this.files);
            });

            // Handle drag and drop
            uploadArea.addEventListener('dragover', function (e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', function (e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('dragover');

                const files = e.dataTransfer.files;
                handleFileSelection(files);
            });

            // Handle click on upload area
            uploadArea.addEventListener('click', function (e) {
                // Don't trigger if clicking on remove button
                if (e.target.closest('.remove-image')) {
                    return;
                }
                imageInput.click();
            });

            // Make removeImage function global
            window.removeImage = function (index) {
                selectedFiles.splice(index, 1);
                updateFileInput();
                renderImagePreviews();
            };

            // Handle form submission
            document.getElementById('createPostForm').addEventListener('submit', function (e) {
                const btnText = document.querySelector('.btn-text');
                const loadingSpinner = document.querySelector('.loading-spinner');

                // Show loading state
                btnText.style.display = 'none';
                loadingSpinner.style.display = 'inline-block';
                submitBtn.disabled = true;

                // Show loading overlay
                loadingOverlay.style.display = 'flex';
            });

            // Prevent multiple form submissions
            let isSubmitting = false;
            document.getElementById('createPostForm').addEventListener('submit', function (e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return false;
                }
                isSubmitting = true;
            });
        });
    </script>

    <style>
        .required {
            color: red;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-content {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loading-spinner {
            display: none;
        }

        .preview-item {
            position: relative;
            display: inline-block;
            margin: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 8px;
            background: #f9f9f9;
            max-width: 200px;
        }

        .preview-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
            display: block;
        }

        .image-info {
            margin-top: 4px;
            font-size: 12px;
            color: #666;
        }

        .image-name {
            display: block;
            font-weight: 500;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100px;
        }

        .image-size {
            color: #999;
        }

        .remove-image {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .remove-image:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        .image-upload-area {
            position: relative;
            cursor: pointer;
        }

        .image-upload-area.dragover {
            border-color: #3498db;
            background-color: #f8f9fa;
        }

        .image-upload-area.dragover .upload-placeholder {
            color: #3498db;
        }

        .image-counter {
            margin-top: 8px;
            font-size: 14px;
            color: #666;
            text-align: right;
        }

        .image-preview {
            margin-top: 16px;
            min-height: 50px;
        }

        .image-preview:empty {
            display: none;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        #submitBtn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .image-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
    </style>
</body>

</html>