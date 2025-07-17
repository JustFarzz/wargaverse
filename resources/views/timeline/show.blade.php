<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Timeline - WargaVerse</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/navbarcomponents.css') }}">

    <link rel="stylesheet" href="{{ asset('css/showtimeline.css') }}">

</head>
<body>
    @include('components.navbar')
    
    <div class="post-detail-container">
    <!-- Header Section -->
    <div class="detail-header">
        <div class="header-navigation">
            <a href="{{ route('timeline.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Timeline
            </a>
            <div class="header-actions">
                @if(auth()->id() === $post->user_id)
                {{-- <a href="{{ route('timeline.edit', $post->id) }}" class="btn-secondary">
                    <i class="fas fa-edit"></i>
                    Edit
                </a> --}}
                <form action="{{ route('timeline.destroy', $post->id) }}" method="POST" class="delete-form" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger" onclick="return confirm('Yakin ingin menghapus posting ini?')">
                        <i class="fas fa-trash"></i>
                        Hapus
                    </button>
                </form>
                @endif
                <button class="btn-outline share-post-btn" data-post-id="{{ $post->id }}">
                    <i class="fas fa-share-alt"></i>
                    Bagikan
                </button>
            </div>
        </div>
    </div>

    <!-- Main Post Content -->
    <div class="post-main">
        <div class="post-header">
            <div class="user-section">
                <div class="user-avatar">
                    <img src="{{ $post->user->avatar ?? asset('images/default-avatar.png') }}" 
                         alt="{{ $post->user->name }}">
                </div>
                <div class="user-info">
                    <h3 class="user-name">{{ $post->user->name }}</h3>
                    <div class="user-meta">
                        <span class="user-address">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $post->user->address }}
                        </span>
                        <span class="post-date">
                            <i class="fas fa-clock"></i>
                            {{ $post->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="category-badge category-{{ $post->category }}">
                @switch($post->category)
                    @case('jual')
                        <i class="fas fa-tag"></i> Jual Beli
                        @break
                    @case('jasa')
                        <i class="fas fa-handshake"></i> Jasa
                        @break
                    @case('info')
                        <i class="fas fa-info-circle"></i> Info Umum
                        @break
                    @default
                        <i class="fas fa-circle"></i> Umum
                @endswitch
            </div>
        </div>

        <div class="post-content">
            <h1 class="post-title">{{ $post->title }}</h1>
            
            @if($post->price && $post->category === 'jual')
            <div class="price-section">
                <span class="price-label">Harga:</span>
                <span class="price-value">Rp {{ number_format($post->price, 0, ',', '.') }}</span>
            </div>
            @endif

            <div class="post-description">
                {!! nl2br(e($post->content)) !!}
            </div>

            @if($post->images && $post->images->count() > 0)
            <div class="post-gallery">
                <div class="gallery-grid">
                    @foreach($post->images as $index => $image)
                    <div class="gallery-item" data-index="{{ $index }}">
                        <img src="{{ $image->url }}" alt="Post image {{ $index + 1 }}" class="gallery-image">
                        <div class="image-overlay">
                            <button class="view-fullscreen" data-image="{{ $image->url }}">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Contact Section -->
        @if(in_array($post->category, ['jual', 'jasa']))
        <div class="contact-section">
            <h3 class="contact-title">
                <i class="fas fa-phone-alt"></i>
                Hubungi Penjual
            </h3>
            <div class="contact-methods">
                @if($post->phone)
                <a href="tel:{{ $post->phone }}" class="contact-btn phone-btn">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="contact-info">
                        <span class="contact-label">Telepon</span>
                        <span class="contact-value">{{ $post->phone }}</span>
                    </div>
                </a>
                @endif
                
                @if($post->whatsapp)
                <a href="https://wa.me/{{ $post->whatsapp }}" class="contact-btn whatsapp-btn" target="_blank">
                    <div class="contact-icon">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="contact-info">
                        <span class="contact-label">WhatsApp</span>
                        <span class="contact-value">{{ $post->whatsapp }}</span>
                    </div>
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Image Lightbox Modal -->
<div class="lightbox-modal" id="lightboxModal">
    <div class="lightbox-content">
        <button class="lightbox-close">&times;</button>
        <img src="" alt="Full size image" class="lightbox-image">
        <div class="lightbox-navigation">
            <button class="lightbox-prev"><i class="fas fa-chevron-left"></i></button>
            <button class="lightbox-next"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</div>
</body>
</html>