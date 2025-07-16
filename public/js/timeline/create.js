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