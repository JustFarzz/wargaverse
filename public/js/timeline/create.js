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

    // Store selected files - menggunakan array biasa tanpa DataTransfer
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

    // Check for duplicate files
    function isDuplicateFile(file) {
        return selectedFiles.some(existingFile =>
            existingFile.name === file.name &&
            existingFile.size === file.size &&
            existingFile.lastModified === file.lastModified
        );
    }

    // Update file input with selected files - PERBAIKAN UTAMA
    function updateFileInput() {
        // Buat DataTransfer baru setiap kali
        const dt = new DataTransfer();

        // Tambahkan semua file yang dipilih
        selectedFiles.forEach(file => {
            dt.items.add(file);
        });

        // Set files ke input
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

        // Update upload area visibility
        updateUploadAreaVisibility();

        // Debug: log file count
        console.log('Files in input:', imageInput.files.length);
        console.log('Selected files:', selectedFiles.length);
    }

    // Update upload area visibility based on file count
    function updateUploadAreaVisibility() {
        const uploadPlaceholder = uploadArea.querySelector('.upload-placeholder');
        if (selectedFiles.length >= 5) {
            uploadPlaceholder.style.display = 'none';
            uploadArea.style.pointerEvents = 'none';
            uploadArea.style.opacity = '0.5';
        } else {
            uploadPlaceholder.style.display = 'block';
            uploadArea.style.pointerEvents = 'auto';
            uploadArea.style.opacity = '1';
        }
    }

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Render image previews
    function renderImagePreviews() {
        imagePreview.innerHTML = '';

        if (selectedFiles.length === 0) {
            imagePreview.style.display = 'none';
            return;
        }

        imagePreview.style.display = 'block';

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                previewItem.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${index + 1}" />
                    <div class="image-info">
                        <span class="image-name" title="${file.name}">${truncateFileName(file.name, 15)}</span>
                        <span class="image-size">${formatFileSize(file.size)}</span>
                    </div>
                    <button type="button" class="remove-image" onclick="removeImage(${index})" title="Hapus gambar">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                imagePreview.appendChild(previewItem);
            };
            reader.readAsDataURL(file);
        });
    }

    // Truncate filename for display
    function truncateFileName(filename, maxLength) {
        if (filename.length <= maxLength) return filename;

        const extension = filename.split('.').pop();
        const nameWithoutExt = filename.substring(0, filename.lastIndexOf('.'));
        const truncatedName = nameWithoutExt.substring(0, maxLength - extension.length - 4) + '...';

        return truncatedName + '.' + extension;
    }

    // Handle file selection - PERBAIKAN
    function handleFileSelection(files) {
        const newFiles = Array.from(files);
        let addedCount = 0;

        newFiles.forEach(file => {
            // Check file limit
            if (selectedFiles.length >= 5) {
                if (addedCount === 0) {
                    alert('Maksimal 5 gambar yang dapat diupload.');
                }
                return;
            }

            // Check for duplicates
            if (isDuplicateFile(file)) {
                alert(`File ${file.name} sudah dipilih sebelumnya.`);
                return;
            }

            // Validate file
            if (validateFile(file)) {
                selectedFiles.push(file);
                addedCount++;
                console.log('Added file:', file.name, 'Size:', file.size);
            }
        });

        if (addedCount > 0) {
            updateFileInput();
            renderImagePreviews();
        }
    }

    // Handle image input change - PERBAIKAN
    imageInput.addEventListener('change', function (e) {
        console.log('Input change event triggered');
        console.log('Files from input:', e.target.files.length);

        if (e.target.files.length > 0) {
            // Reset selected files jika ini adalah input langsung (bukan dari DataTransfer)
            if (e.target.files.length !== selectedFiles.length) {
                selectedFiles = [];
            }
            handleFileSelection(e.target.files);
        }
    });

    // Handle drag and drop
    uploadArea.addEventListener('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (selectedFiles.length < 5) {
            this.classList.add('dragover');
        }
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

        if (selectedFiles.length >= 5) {
            alert('Maksimal 5 gambar yang dapat diupload.');
            return;
        }

        const files = e.dataTransfer.files;
        handleFileSelection(files);
    });

    // Handle click on upload area
    uploadArea.addEventListener('click', function (e) {
        // Don't trigger if clicking on remove button or if max files reached
        if (e.target.closest('.remove-image') || selectedFiles.length >= 5) {
            return;
        }
        imageInput.click();
    });

    // Make removeImage function global
    window.removeImage = function (index) {
        if (index >= 0 && index < selectedFiles.length) {
            selectedFiles.splice(index, 1);
            updateFileInput();
            renderImagePreviews();
        }
    };

    // Handle form submission - PERBAIKAN
    let isSubmitting = false;
    document.getElementById('createPostForm').addEventListener('submit', function (e) {
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }

        // Debug: Check files before submit
        console.log('Form submission - Files in input:', imageInput.files.length);
        console.log('Selected files array:', selectedFiles.length);

        isSubmitting = true;
        const btnText = document.querySelector('.btn-text');
        const loadingSpinner = document.querySelector('.loading-spinner');

        // Show loading state
        if (btnText) btnText.style.display = 'none';
        if (loadingSpinner) loadingSpinner.style.display = 'inline-block';
        if (submitBtn) submitBtn.disabled = true;

        // Show loading overlay
        if (loadingOverlay) loadingOverlay.style.display = 'flex';

        // Reset form if there's an error (will be handled by page reload)
        setTimeout(() => {
            if (btnText) btnText.style.display = 'inline-block';
            if (loadingSpinner) loadingSpinner.style.display = 'none';
            if (submitBtn) submitBtn.disabled = false;
            if (loadingOverlay) loadingOverlay.style.display = 'none';
            isSubmitting = false;
        }, 30000); // Reset after 30 seconds if no response
    });

    // Initialize upload area visibility
    updateUploadAreaVisibility();

    // Clear button functionality
    window.clearAllImages = function () {
        selectedFiles = [];
        updateFileInput();
        renderImagePreviews();
    };
});