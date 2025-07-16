<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="{{ asset('css/indexprofile.css') }}">

</head>
<body>
    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar">
                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}"
                    alt="Avatar {{ $user->name }}" id="avatar-preview">
                <div class="avatar-overlay">
                    <i class="fas fa-camera"></i>
                </div>
            </div>
    
            <div class="profile-info">
                <h1>{{ $user->name }}</h1>
                <p class="user-role">{{ ucfirst($user->role) }} RT {{ $user->rt_number ?? '01' }}</p>
                <p class="join-date">Bergabung sejak {{ $user->created_at->format('d F Y') }}</p>
    
                <div class="profile-actions">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Edit Profil
                    </a>
                    <button class="btn btn-secondary" onclick="shareProfile()">
                        <i class="fas fa-share-alt"></i>
                        Bagikan
                    </button>
                </div>
            </div>
    
            <div class="profile-stats">
                <div class="stat-item">
                    <div class="stat-number">{{ $user->posts_count ?? 0 }}</div>
                    <div class="stat-label">Postingan</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $user->reports_count ?? 0 }}</div>
                    <div class="stat-label">Laporan</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $user->polls_count ?? 0 }}</div>
                    <div class="stat-label">Polling</div>
                </div>
            </div>
        </div>
    
        <!-- Profile Content -->
        <div class="profile-content">
            <div class="left-column">
                <!-- Personal Information Card -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-user"></i> Informasi Pribadi</h2>
                    </div>
                    <div class="card-content">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Nama Lengkap</div>
                                <div class="info-value">{{ $user->name }}</div>
                            </div>
    
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $user->email }}</div>
                            </div>
    
                            <div class="info-item">
                                <div class="info-label">Nomor Telepon</div>
                                <div class="info-value">{{ $user->phone ?? 'Belum diisi' }}</div>
                            </div>
    
                            <div class="info-item">
                                <div class="info-label">Tanggal Lahir</div>
                                <div class="info-value">
                                    {{ $user->birth_date ? $user->birth_date->format('d F Y') : 'Belum diisi' }}</div>
                            </div>
    
                            <div class="info-item">
                                <div class="info-label">Jenis Kelamin</div>
                                <div class="info-value">{{ $user->gender ?? 'Belum diisi' }}</div>
                            </div>
    
                            <div class="info-item">
                                <div class="info-label">Pekerjaan</div>
                                <div class="info-value">{{ $user->occupation ?? 'Belum diisi' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Address Information Card -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-map-marker-alt"></i> Informasi Alamat</h2>
                    </div>
                    <div class="card-content">
                        <div class="info-grid">
                            <div class="info-item full-width">
                                <div class="info-label">Alamat Lengkap</div>
                                <div class="info-value">{{ $user->address ?? 'Belum diisi' }}</div>
                            </div>
    
                            <div class="info-item">
                                <div class="info-label">RT</div>
                                <div class="info-value">{{ $user->rt_number ?? '01' }}</div>
                            </div>
    
                            <div class="info-item">
                                <div class="info-label">RW</div>
                                <div class="info-value">{{ $user->rw_number ?? '01' }}</div>
                            </div>
    
                            <div class="info-item">
                                <div class="info-label">Kelurahan</div>
                                <div class="info-value">{{ $user->kelurahan ?? 'Belum diisi' }}</div>
                            </div>
    
                            <div class="info-item">
                                <div class="info-label">Kecamatan</div>
                                <div class="info-value">{{ $user->kecamatan ?? 'Belum diisi' }}</div>
                            </div>
    
                            <div class="info-item">
                                <div class="info-label">Kota</div>
                                <div class="info-value">{{ $user->city ?? 'Belum diisi' }}</div>
                            </div>
    
                            <div class="info-item">
                                <div class="info-label">Kode Pos</div>
                                <div class="info-value">{{ $user->postal_code ?? 'Belum diisi' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="right-column">
                <!-- Bio Card -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-quote-left"></i> Tentang Saya</h2>
                    </div>
                    <div class="card-content">
                        <div class="bio-content">
                            @if($user->bio)
                                <p>{{ $user->bio }}</p>
                            @else
                                <div class="empty-state">
                                    <i class="fas fa-pen"></i>
                                    <p>Belum ada bio. Ceritakan tentang diri Anda!</p>
                                    <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-primary">Tambah Bio</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
    
                <!-- Recent Activity Card -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-clock"></i> Aktivitas Terbaru</h2>
                        <a href="#" class="view-all">Lihat Semua</a>
                    </div>
                    <div class="card-content">
                        <div class="activity-list">
                            @forelse($recentActivities ?? [] as $activity)
                                <div class="activity-item">
                                    <div class="activity-icon {{ $activity->type }}">
                                        <i class="fas fa-{{ $activity->icon }}"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p class="activity-text">{{ $activity->description }}</p>
                                        <span class="activity-time">{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="fas fa-history"></i>
                                    <p>Belum ada aktivitas terbaru</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
    
                <!-- Social Links Card -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-link"></i> Media Sosial</h2>
                    </div>
                    <div class="card-content">
                        <div class="social-links">
                            @if($user->facebook_url)
                                <a href="{{ $user->facebook_url }}" class="social-link facebook" target="_blank">
                                    <i class="fab fa-facebook-f"></i>
                                    <span>Facebook</span>
                                </a>
                            @endif
    
                            @if($user->instagram_url)
                                <a href="{{ $user->instagram_url }}" class="social-link instagram" target="_blank">
                                    <i class="fab fa-instagram"></i>
                                    <span>Instagram</span>
                                </a>
                            @endif
    
                            @if($user->twitter_url)
                                <a href="{{ $user->twitter_url }}" class="social-link twitter" target="_blank">
                                    <i class="fab fa-twitter"></i>
                                    <span>Twitter</span>
                                </a>
                            @endif
    
                            @if($user->whatsapp)
                                <a href="https://wa.me/{{ $user->whatsapp }}" class="social-link whatsapp" target="_blank">
                                    <i class="fab fa-whatsapp"></i>
                                    <span>WhatsApp</span>
                                </a>
                            @endif
    
                            @if(!$user->facebook_url && !$user->instagram_url && !$user->twitter_url && !$user->whatsapp)
                                <div class="empty-state">
                                    <i class="fas fa-share-alt"></i>
                                    <p>Belum ada media sosial</p>
                                    <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-primary">Tambah Link</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('js/profile/index.js') }}"></script>
</body>
</html>