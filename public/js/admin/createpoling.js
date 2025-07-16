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