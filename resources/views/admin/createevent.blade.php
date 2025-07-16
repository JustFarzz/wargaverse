<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event</title>


    <link rel="stylesheet" href="{{ asset('css/createkalender.css') }}">
</head>
<body>
    <div class="kalender-container">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <h1>➕ Tambah Kegiatan Baru</h1>
                <p>Buat jadwal kegiatan RT untuk warga</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('kalender.index') }}" class="btn-secondary">
                    <i class="icon">🔙</i>
                    Kembali
                </a>
            </div>
        </div>
    
        <!-- Form Section -->
        <div class="form-container">
            <form action="{{ route('admin.event.store') }}" method="POST" class="event-form" enctype="multipart/form-data">
                @csrf
                
                <div class="form-grid">
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h3>Informasi Dasar</h3>
                        
                        <div class="form-group">
                            <label for="title">Nama Kegiatan *</label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" 
                                   placeholder="Masukkan nama kegiatan"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
    
                        <div class="form-group">
                            <label for="description">Deskripsi Kegiatan</label>
                            <textarea id="description" 
                                      name="description" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      rows="4" 
                                      placeholder="Jelaskan detail kegiatan, tujuan, dan hal-hal penting lainnya">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
    
                        <div class="form-group">
                            <label for="category">Kategori Kegiatan</label>
                            <select id="category" 
                                    name="category" 
                                    class="form-control @error('category') is-invalid @enderror">
                                <option value="">Pilih Kategori</option>
                                <option value="rapat" {{ old('category') == 'rapat' ? 'selected' : '' }}>Rapat RT</option>
                                <option value="gotong_royong" {{ old('category') == 'gotong_royong' ? 'selected' : '' }}>Gotong Royong</option>
                                <option value="keamanan" {{ old('category') == 'keamanan' ? 'selected' : '' }}>Keamanan</option>
                                <option value="sosial" {{ old('category') == 'sosial' ? 'selected' : '' }}>Kegiatan Sosial</option>
                                <option value="olahraga" {{ old('category') == 'olahraga' ? 'selected' : '' }}>Olahraga</option>
                                <option value="keagamaan" {{ old('category') == 'keagamaan' ? 'selected' : '' }}>Kegiatan Keagamaan</option>
                                <option value="perayaan" {{ old('category') == 'perayaan' ? 'selected' : '' }}>Perayaan</option>
                                <option value="lainnya" {{ old('category') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
    
                    <!-- Date & Time -->
                    <div class="form-section">
                        <h3>Waktu & Tempat</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="event_date">Tanggal Kegiatan *</label>
                                <input type="date" 
                                       id="event_date" 
                                       name="event_date" 
                                       class="form-control @error('event_date') is-invalid @enderror" 
                                       value="{{ old('event_date') }}" 
                                       min="{{ date('Y-m-d') }}"
                                       required>
                                @error('event_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
    
                            <div class="form-group">
                                <label for="start_time">Waktu Mulai *</label>
                                <input type="time" 
                                       id="start_time" 
                                       name="start_time" 
                                       class="form-control @error('start_time') is-invalid @enderror" 
                                       value="{{ old('start_time') }}" 
                                       required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
    
                            <div class="form-group">
                                <label for="end_time">Waktu Selesai</label>
                                <input type="time" 
                                       id="end_time" 
                                       name="end_time" 
                                       class="form-control @error('end_time') is-invalid @enderror" 
                                       value="{{ old('end_time') }}">
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
    
                        <div class="form-group">
                            <label for="location">Lokasi Kegiatan *</label>
                            <input type="text" 
                                   id="location" 
                                   name="location" 
                                   class="form-control @error('location') is-invalid @enderror" 
                                   value="{{ old('location') }}" 
                                   placeholder="Contoh: Balai RT, Lapangan, Rumah Pak RT"
                                   required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
    
                        <div class="form-group">
                            <label for="location_detail">Detail Alamat</label>
                            <textarea id="location_detail" 
                                      name="location_detail" 
                                      class="form-control @error('location_detail') is-invalid @enderror" 
                                      rows="2" 
                                      placeholder="Alamat lengkap atau petunjuk arah">{{ old('location_detail') }}</textarea>
                            @error('location_detail')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
    
                    <!-- Additional Information -->
                    <div class="form-section">
                        <h3>Informasi Tambahan</h3>
                        
                        <div class="form-group">
                            <label for="organizer">Penyelenggara</label>
                            <input type="text" 
                                   id="organizer" 
                                   name="organizer" 
                                   class="form-control @error('organizer') is-invalid @enderror" 
                                   value="{{ old('organizer', auth()->user()->name ?? '') }}" 
                                   placeholder="Nama penyelenggara">
                            @error('organizer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
    
                        <div class="form-group">
                            <label for="contact_person">Kontak Person</label>
                            <input type="text" 
                                   id="contact_person" 
                                   name="contact_person" 
                                   class="form-control @error('contact_person') is-invalid @enderror" 
                                   value="{{ old('contact_person') }}" 
                                   placeholder="Nomor telepon atau nama yang bisa dihubungi">
                            @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
    
                        <div class="form-group">
                            <label for="max_participants">Maksimal Peserta</label>
                            <input type="number" 
                                   id="max_participants" 
                                   name="max_participants" 
                                   class="form-control @error('max_participants') is-invalid @enderror" 
                                   value="{{ old('max_participants') }}" 
                                   min="1" 
                                   placeholder="Kosongkan jika tidak ada batasan">
                            @error('max_participants')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
    
                        <div class="form-group">
                            <label for="requirements">Syarat & Ketentuan</label>
                            <textarea id="requirements" 
                                      name="requirements" 
                                      class="form-control @error('requirements') is-invalid @enderror" 
                                      rows="3" 
                                      placeholder="Apa saja yang perlu dibawa atau persyaratan khusus">{{ old('requirements') }}</textarea>
                            @error('requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
    
                    <!-- Settings -->
                    <div class="form-section">
                        <h3>Pengaturan</h3>
                        
                        <div class="form-group">
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" 
                                           name="is_registration_required" 
                                           value="1" 
                                           {{ old('is_registration_required') ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    Memerlukan pendaftaran peserta
                                </label>
                            </div>
                        </div>
    
                        <div class="form-group">
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" 
                                           name="send_notification" 
                                           value="1" 
                                           {{ old('send_notification') !== null ? (old('send_notification') ? 'checked' : '') : 'checked' }}>
                                    <span class="checkmark"></span>
                                    Kirim notifikasi ke semua warga
                                </label>
                            </div>
                        </div>
    
                        <div class="form-group">
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" 
                                           name="is_reminder_active" 
                                           value="1" 
                                           {{ old('is_reminder_active') !== null ? (old('is_reminder_active') ? 'checked' : '') : 'checked' }}>
                                    <span class="checkmark"></span>
                                    Aktifkan pengingat otomatis
                                </label>
                            </div>
                        </div>
                    </div>
    
                    <!-- File Upload -->
                    {{-- <div class="form-section">
                        <h3>Lampiran</h3>
                        
                        <div class="form-group">
                            <label for="attachment">Upload Gambar/Dokumen</label>
                            <div class="file-upload-area">
                                <input type="file" 
                                       id="attachment" 
                                       name="attachment" 
                                       class="file-input @error('attachment') is-invalid @enderror"
                                       accept="image/*,application/pdf,.doc,.docx">
                                <div class="file-upload-text">
                                    <div class="upload-icon">📁</div>
                                    <p>Klik untuk upload atau drag & drop</p>
                                    <small>Gambar, PDF, atau dokumen (Max: 5MB)</small>
                                </div>
                            </div>
                            @error('attachment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div> --}}
                </div>
    
                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn-preview" onclick="previewEvent()">
                        <i class="icon">👁️</i>
                        Preview
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="icon">💾</i>
                        Simpan Kegiatan
                    </button>
                    <a href="{{ route('kalender.index') }}" class="btn-secondary">
                        <i class="icon">❌</i>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Preview Modal -->
    <div id="previewModal" class="modal" style="display: none;">
        <div class="modal-content large">
            <div class="modal-header">
                <h3>Preview Kegiatan</h3>
                <button class="modal-close" onclick="closePreview()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="previewContent" class="preview-content">
                    <!-- Preview will be generated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-primary" onclick="closePreview()">Tutup Preview</button>
            </div>
        </div>
    </div>
    
    <script>
    // File upload handling
    document.getElementById('attachment').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const uploadText = document.querySelector('.file-upload-text');
        
        if (file) {
            // Check file size (5MB limit)
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 5MB.');
                this.value = '';
                return;
            }
            
            uploadText.innerHTML = `
                <div class="upload-icon">📄</div>
                <p><strong>${file.name}</strong></p>
                <small>Ukuran: ${(file.size / 1024 / 1024).toFixed(2)} MB</small>
            `;
        }
    });
    
    // Drag and drop for file upload
    const fileUploadArea = document.querySelector('.file-upload-area');
    const fileInput = document.getElementById('attachment');
    
    fileUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        fileUploadArea.classList.add('dragover');
    });
    
    fileUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        fileUploadArea.classList.remove('dragover');
    });
    
    fileUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        fileUploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });
    
    // Time validation
    document.getElementById('start_time').addEventListener('change', function() {
        const startTime = this.value;
        const endTimeInput = document.getElementById('end_time');
        
        if (startTime && !endTimeInput.value) {
            // Auto-set end time to 2 hours after start time
            const [hours, minutes] = startTime.split(':');
            const endHours = (parseInt(hours) + 2) % 24;
            endTimeInput.value = `${endHours.toString().padStart(2, '0')}:${minutes}`;
        }
    });
    
    // Preview function
    function previewEvent() {
        const formData = new FormData(document.querySelector('.event-form'));
        const previewContent = document.getElementById('previewContent');
        
        const title = formData.get('title') || 'Judul Kegiatan';
        const description = formData.get('description') || 'Belum ada deskripsi';
        const category = formData.get('category') || 'Tidak dikategorikan';
        const eventDate = formData.get('event_date') || '';
        const startTime = formData.get('start_time') || '';
        const endTime = formData.get('end_time') || '';
        const location = formData.get('location') || 'Belum ditentukan';
        const organizer = formData.get('organizer') || '';
        const maxParticipants = formData.get('max_participants') || 'Tidak terbatas';
        const requirements = formData.get('requirements') || 'Tidak ada persyaratan khusus';
        
        // Format category for display
        const categoryDisplay = {
            'rapat': 'Rapat RT',
            'gotong_royong': 'Gotong Royong',
            'keamanan': 'Keamanan',
            'sosial': 'Kegiatan Sosial',
            'olahraga': 'Olahraga',
            'keagamaan': 'Kegiatan Keagamaan',
            'perayaan': 'Perayaan',
            'lainnya': 'Lainnya'
        }[category] || category;
        
        previewContent.innerHTML = `
            <div class="preview-event">
                <div class="preview-header">
                    <h2>${title}</h2>
                    <span class="preview-category">${categoryDisplay}</span>
                </div>
                <div class="preview-details">
                    <div class="preview-item">
                        <strong>📅 Tanggal:</strong> ${eventDate ? new Date(eventDate).toLocaleDateString('id-ID', { 
                            weekday: 'long', 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        }) : 'Belum ditentukan'}
                    </div>
                    <div class="preview-item">
                        <strong>🕐 Waktu:</strong> ${startTime}${endTime ? ` - ${endTime}` : ''}
                    </div>
                    <div class="preview-item">
                        <strong>📍 Lokasi:</strong> ${location}
                    </div>
                    <div class="preview-item">
                        <strong>👤 Penyelenggara:</strong> ${organizer || 'Tidak disebutkan'}
                    </div>
                    <div class="preview-item">
                        <strong>👥 Max Peserta:</strong> ${maxParticipants}
                    </div>
                    <div class="preview-item">
                        <strong>📋 Persyaratan:</strong> ${requirements}
                    </div>
                </div>
                <div class="preview-description">
                    <h4>Deskripsi:</h4>
                    <p>${description}</p>
                </div>
            </div>
        `;
        
        document.getElementById('previewModal').style.display = 'flex';
    }
    
    function closePreview() {
        document.getElementById('previewModal').style.display = 'none';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const previewModal = document.getElementById('previewModal');
        if (event.target === previewModal) {
            previewModal.style.display = 'none';
        }
    }
    
    // Form validation
    document.querySelector('.event-form').addEventListener('submit', function(e) {
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        const eventDate = document.getElementById('event_date').value;
        const title = document.getElementById('title').value.trim();
        const location = document.getElementById('location').value.trim();
        
        // Validate required fields
        if (!title) {
            e.preventDefault();
            alert('Nama kegiatan wajib diisi!');
            document.getElementById('title').focus();
            return false;
        }
        
        if (!eventDate) {
            e.preventDefault();
            alert('Tanggal kegiatan wajib diisi!');
            document.getElementById('event_date').focus();
            return false;
        }
        
        if (!startTime) {
            e.preventDefault();
            alert('Waktu mulai wajib diisi!');
            document.getElementById('start_time').focus();
            return false;
        }
        
        if (!location) {
            e.preventDefault();
            alert('Lokasi kegiatan wajib diisi!');
            document.getElementById('location').focus();
            return false;
        }
        
        // Validate time
        if (startTime && endTime && startTime >= endTime) {
            e.preventDefault();
            alert('Waktu selesai harus lebih dari waktu mulai!');
            document.getElementById('end_time').focus();
            return false;
        }
        
        // Validate date is not in the past
        const today = new Date();
        const selectedDate = new Date(eventDate);
        today.setHours(0, 0, 0, 0);
        selectedDate.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            e.preventDefault();
            alert('Tanggal kegiatan tidak boleh di masa lalu!');
            document.getElementById('event_date').focus();
            return false;
        }
        
        return true;
    });
    
    // Add loading state to submit button
    document.querySelector('.event-form').addEventListener('submit', function(e) {
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="icon">⏳</i> Menyimpan...';
        submitBtn.disabled = true;
        
        // Re-enable after 5 seconds as fallback
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 5000);
    });
    </script>
</body>
</html>