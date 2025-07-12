@extends('layouts.app')

@section('title', 'Posting Baru')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/timeline/create.css') }}">
@endsection

@section('content')
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

    <!-- Form Section -->
    <div class="form-container">
        <form action="{{ route('timeline.store') }}" method="POST" enctype="multipart/form-data" id="createPostForm">
            @csrf
            
            <!-- Category Selection -->
            <div class="form-group category-group">
                <label class="form-label">Kategori Posting</label>
                <div class="category-options">
                    <input type="radio" name="category" value="jual" id="cat-jual" {{ old('category') == 'jual' ? 'checked' : '' }}>
                    <label for="cat-jual" class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="category-text">
                            <h4>Jual Beli</h4>
                            <p>Jual atau beli barang</p>
                        </div>
                    </label>

                    <input type="radio" name="category" value="jasa" id="cat-jasa" {{ old('category') == 'jasa' ? 'checked' : '' }}>
                    <label for="cat-jasa" class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <div class="category-text">
                            <h4>Jasa</h4>
                            <p>Tawarkan atau cari jasa</p>
                        </div>
                    </label>

                    <input type="radio" name="category" value="info" id="cat-info" {{ old('category') == 'info' ? 'checked' : '' }}>
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
                <label for="title" class="form-label">Judul Posting</label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       class="form-input" 
                       placeholder="Contoh: Jual Motor Bekas / Jasa Servis AC / Info Kerja Bakti"
                       value="{{ old('title') }}"
                       required>
                @error('title')
                <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Content -->
            <div class="form-group">
                <label for="content" class="form-label">Deskripsi Detail</label>
                <textarea name="content" 
                          id="content" 
                          class="form-textarea" 
                          rows="6" 
                          placeholder="Jelaskan detail posting Anda..."
                          required>{{ old('content') }}</textarea>
                <div class="char-counter">
                    <span id="charCount">0</span>/1000 karakter
                </div>
                @error('content')
                <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Price (Show only for Jual Beli) -->
            <div class="form-group price-group" style="display: none;">
                <label for="price" class="form-label">Harga</label>
                <div class="price-input-wrapper">
                    <span class="currency">Rp</span>
                    <input type="number" 
                           name="price" 
                           id="price" 
                           class="form-input price-input" 
                           placeholder="0"
                           value="{{ old('price') }}">
                </div>
                <small class="form-hint">Kosongkan jika harga nego atau gratis</small>
                @error('price')
                <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Contact Information -->
            <div class="form-group contact-group">
                <label class="form-label">Informasi Kontak</label>
                <div class="contact-inputs">
                    <div class="contact-field">
                        <label for="phone" class="contact-label">
                            <i class="fas fa-phone"></i>
                            Nomor Telepon
                        </label>
                        <input type="tel" 
                               name="phone" 
                               id="phone" 
                               class="form-input" 
                               placeholder="08123456789"
                               value="{{ old('phone') }}">
                    </div>
                    <div class="contact-field">
                        <label for="whatsapp" class="contact-label">
                            <i class="fab fa-whatsapp"></i>
                            WhatsApp
                        </label>
                        <input type="tel" 
                               name="whatsapp" 
                               id="whatsapp" 
                               class="form-input" 
                               placeholder="628123456789"
                               value="{{ old('whatsapp') }}">
                    </div>
                </div>
                <small class="form-hint">Minimal satu kontak harus diisi untuk kategori Jual Beli dan Jasa</small>
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
                        <small>Maksimal 5 gambar, ukuran maks 2MB per file</small>
                    </div>
                    <input type="file" 
                           name="images[]" 
                           id="images" 
                           multiple 
                           accept="image/*" 
                           class="image-input">
                </div>
                <div class="image-preview" id="imagePreview"></div>
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
                    Posting Sekarang
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
@endsection

@section('scripts')
<script src="{{ asset('js/timeline/create.js') }}"></script>
@endsection