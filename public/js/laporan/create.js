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