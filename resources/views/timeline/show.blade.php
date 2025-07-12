@extends('layouts.app')

@section('title', $post->title)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/timeline/show.css') }}">
@endsection

@section('content')
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
                <a href="{{ route('timeline.edit', $post->id) }}" class="btn-secondary">
                    <i class="fas fa-edit"></i>
                    Edit
                </a>
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

            @if($post->images)
            <div class="post-gallery">
                <div class="gallery-grid">
                    @foreach(json_decode($post->images) as $index => $image)
                    <div class="gallery-item" data-index="{{ $index }}">
                        <img src="{{ asset('storage/' . $image) }}" alt="Post image {{ $index + 1 }}" class="gallery-image">
                        <div class="image-overlay">
                            <button class="view-fullscreen" data-image="{{ asset('storage/' . $image) }}">
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

        <!-- Post Actions -->
        <div class="post-actions">
            <button class="action-btn like-btn {{ $userHasLiked ? 'liked' : '' }}" data-post-id="{{ $post->id }}">
                <i class="fas fa-heart"></i>
                <span class="action-text">Suka</span>
                <span class="action-count">{{ $post->likes_count ?? 0 }}</span>
            </button>
            <button class="action-btn comment-btn" onclick="document.getElementById('commentForm').scrollIntoView()">
                <i class="fas fa-comment"></i>
                <span class="action-text">Komentar</span>
                <span class="action-count">{{ $comments->count() }}</span>
            </button>
            <button class="action-btn save-btn" data-post-id="{{ $post->id }}">
                <i class="fas fa-bookmark"></i>
                <span class="action-text">Simpan</span>
            </button>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="comments-section">
        <div class="comments-header">
            <h3 class="comments-title">
                <i class="fas fa-comments"></i>
                Komentar ({{ $comments->count() }})
            </h3>
        </div>

        <!-- Comment Form -->
        @auth
        <div class="comment-form-container">
            <form action="{{ route('comments.store', $post->id) }}" method="POST" id="commentForm">
                @csrf
                <div class="comment-input-group">
                    <div class="commenter-avatar">
                        <img src="{{ auth()->user()->avatar ?? asset('images/default-avatar.png') }}" 
                             alt="{{ auth()->user()->name }}">
                    </div>
                    <div class="comment-input-wrapper">
                        <textarea name="content" 
                                  placeholder="Tulis komentar Anda..." 
                                  class="comment-input" 
                                  rows="3" 
                                  required></textarea>
                        <div class="comment-actions">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-paper-plane"></i>
                                Kirim Komentar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @else
        <div class="login-prompt">
            <p>
                <a href="{{ route('login') }}">Login</a> untuk memberikan komentar
            </p>
        </div>
        @endauth

        <!-- Comments List -->
        <div class="comments-list">
            @forelse($comments as $comment)
            <div class="comment-item">
                <div class="comment-avatar">
                    <img src="{{ $comment->user->avatar ?? asset('images/default-avatar.png') }}" 
                         alt="{{ $comment->user->name }}">
                </div>
                <div class="comment-content">
                    <div class="comment-header">
                        <h4 class="commenter-name">{{ $comment->user->name }}</h4>
                        <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                        @if(auth()->id() === $comment->user_id)
                        <div class="comment-actions-dropdown">
                            <button class="comment-menu-btn">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="comment-dropdown-menu">
                                <button class="edit-comment-btn" data-comment-id="{{ $comment->id }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-comment-btn" onclick="return confirm('Hapus komentar ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                    <p class="comment-text">{{ $comment->content }}</p>
                    <div class="comment-reactions">
                        <button class="reaction-btn like-comment-btn" data-comment-id="{{ $comment->id }}">
                            <i class="fas fa-thumbs-up"></i>
                            <span>{{ $comment->likes_count ?? 0 }}</span>
                        </button>
                        <button class="reaction-btn reply-btn" data-comment-id="{{ $comment->id }}">
                            <i class="fas fa-reply"></i>
                            Balas
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="no-comments">
                <div class="no-comments-icon">
                    <i class="fas fa-comment-slash"></i>
                </div>
                <p>Belum ada komentar. Jadilah yang pertama berkomentar!</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Related Posts -->
    @if($relatedPosts && $relatedPosts->count() > 0)
    <div class="related-posts">
        <h3 class="related-title">
            <i class="fas fa-th-large"></i>
            Posting Serupa
        </h3>
        <div class="related-grid">
            @foreach($relatedPosts as $relatedPost)
            <a href="{{ route('timeline.show', $relatedPost->id) }}" class="related-card">
                @if($relatedPost->images)
                <div class="related-image">
                    <img src="{{ asset('storage/' . json_decode($relatedPost->images)[0]) }}" 
                         alt="{{ $relatedPost->title }}">
                </div>
                @endif
                <div class="related-content">
                    <h4 class="related-post-title">{{ Str::limit($relatedPost->title, 60) }}</h4>
                    <div class="related-meta">
                        <span class="related-author">{{ $relatedPost->user->name }}</span>
                        <span class="related-date">{{ $relatedPost->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
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
@endsection

@section('scripts')
<script src="{{ asset('js/timeline/show.js') }}"></script>
@endsection