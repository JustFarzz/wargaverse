<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Timeline - WargaVerse</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/navbarcomponents.css') }}">
    
    <link rel="stylesheet" href="{{ asset('css/indextimeline.css') }}">

</head>
<body>
    @include('components.navbar')

    <div class="timeline-container">
    <!-- Header Section -->
    <div class="timeline-header">
        <div class="header-content">
            <h1 class="page-title">
                <i class="fas fa-comments"></i>
                Timeline Warga
            </h1>
            <p class="page-subtitle">Berbagi informasi jual beli dan jasa di lingkungan RT</p>
        </div>
        <a href="{{ route('timeline.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i>
            Posting Baru
        </a>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-tabs">
            <button class="filter-tab active" data-filter="all">
                <i class="fas fa-th-large"></i>
                Semua
            </button>
            <button class="filter-tab" data-filter="jual">
                <i class="fas fa-tag"></i>
                Jual Beli
            </button>
            <button class="filter-tab" data-filter="jasa">
                <i class="fas fa-handshake"></i>
                Jasa
            </button>
            <button class="filter-tab" data-filter="info">
                <i class="fas fa-info-circle"></i>
                Info Umum
            </button>
        </div>
        <div class="search-box">
            <input type="text" placeholder="Cari postingan..." id="searchInput">
            <i class="fas fa-search"></i>
        </div>
    </div>

    <!-- Timeline Posts -->
    <div class="timeline-posts">
        @forelse($posts as $post)
        <div class="post-card" data-category="{{ $post->category }}">
            <div class="post-header">
                <div class="user-info">
                    <div class="user-avatar">
                        <img src="{{ $post->user->avatar ?? asset('images/default-avatar.png') }}" 
                             alt="{{ $post->user->name }}">
                    </div>
                    <div class="user-details">
                        <h4 class="user-name">{{ $post->user->name }}</h4>
                        <span class="user-address">{{ $post->user->address }}</span>
                        <span class="post-time">{{ $post->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="post-category">
                    <span class="category-badge category-{{ $post->category }}">
                        @switch($post->category)
                            @case('jual')
                                <i class="fas fa-tag"></i> Jual Beli
                                @break
                            @case('jasa')
                                <i class="fas fa-handshake"></i> Jasa
                                @break
                            @case('info')
                                <i class="fas fa-info-circle"></i> Info
                                @break
                            @default
                                <i class="fas fa-circle"></i> Umum
                        @endswitch
                    </span>
                </div>
            </div>

            <div class="post-content">
                <h3 class="post-title">{{ $post->title }}</h3>
                <p class="post-description">{{ Str::limit($post->content, 150) }}</p>
                
                @if($post->price && $post->category === 'jual')
                <div class="post-price">
                    <span class="price-label">Harga:</span>
                    <span class="price-value">Rp {{ number_format($post->price, 0, ',', '.') }}</span>
                </div>
                @endif

                @if($post->images->count() > 0)
                <div class="post-images">
                    @foreach($post->images as $image)
                    <img src="{{ asset('storage/posts/' . $image->filename) }}" 
                        alt="{{ $image->original_name }}" 
                        class="post-image"
                        onerror="this.src='{{ asset('images/default-image.png') }}'; console.log('Image not found: {{ $image->filename }}');">
                    @endforeach
                </div>
                @endif
            </div>

            <div class="post-actions">
                <a href="{{ route('timeline.show', $post->id) }}" class="action-btn view-btn">
                    <i class="fas fa-eye"></i>
                    Lihat Detail
                </a>
            </div>

            <!-- Quick Contact for Jual/Jasa -->
            @if(in_array($post->category, ['jual', 'jasa']))
            <div class="quick-contact">
                <span class="contact-label">Kontak:</span>
                @if($post->phone)
                <a href="tel:{{ $post->phone }}" class="contact-btn phone-btn">
                    <i class="fas fa-phone"></i>
                    {{ $post->phone }}
                </a>
                @endif
                @if($post->whatsapp)
                <a href="https://wa.me/{{ $post->whatsapp }}" class="contact-btn wa-btn" target="_blank">
                    <i class="fab fa-whatsapp"></i>
                    WhatsApp
                </a>
                @endif
            </div>
            @endif
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-comments"></i>
            </div>
            <h3>Belum Ada Postingan</h3>
            <p>Jadilah yang pertama membagikan informasi di timeline warga!</p>
            <a href="{{ route('timeline.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i>
                Buat Postingan Pertama
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($posts->hasPages())
    <div class="pagination-wrapper">
        {{ $posts->links() }}
    </div>
    @endif
</div>
</body>
</html>