<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WargaVERSE - Komunitas Digital RT/RW')</title>
    
    <!-- CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Additional CSS -->
    @stack('styles')
</head>
<body class="@yield('body-class', '')">
    <!-- Loading Screen -->
    <div id="loading-screen">
        <div class="loading-spinner">
            <i class="fas fa-home"></i>
            <span>WargaVERSE</span>
        </div>
    </div>

    <!-- Main Wrapper -->
    <div class="app-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-home"></i>
                    <span>WargaVERSE</span>
                </div>
                <button class="sidebar-toggle" id="sidebar-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="sidebar-content">
                <!-- User Profile -->
                <div class="user-profile">
                    <div class="user-avatar">
                        <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}" 
                             alt="{{ Auth::user()->name }}">
                        <div class="status-indicator online"></div>
                    </div>
                    <div class="user-info">
                        <h4>{{ Auth::user()->name }}</h4>
                        <p>RT {{ Auth::user()->rt ?? '01' }} / RW {{ Auth::user()->rw ?? '01' }}</p>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <nav class="sidebar-nav">
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                                <i class="fas fa-home"></i>
                                <span>Beranda</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('timeline.index') }}" class="nav-link {{ request()->routeIs('timeline.*') ? 'active' : '' }}">
                                <i class="fas fa-newspaper"></i>
                                <span>Timeline</span>
                                <span class="nav-badge">12</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Laporan</span>
                                @if($unreadReports ?? 0 > 0)
                                    <span class="nav-badge urgent">{{ $unreadReports }}</span>
                                @endif
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('polling.index') }}" class="nav-link {{ request()->routeIs('polling.*') ? 'active' : '' }}">
                                <i class="fas fa-vote-yea"></i>
                                <span>Polling</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('kalender.index') }}" class="nav-link {{ request()->routeIs('kalender.*') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Kalender</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('kas.index') }}" class="nav-link {{ request()->routeIs('kas.*') ? 'active' : '' }}">
                                <i class="fas fa-wallet"></i>
                                <span>Kas RT</span>
                            </a>
                        </li>
                    </ul>

                    <!-- Divider -->
                    <div class="nav-divider"></div>

                    <!-- Secondary Menu -->
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="{{ route('profile.index') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                                <i class="fas fa-user-cog"></i>
                                <span>Profil</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-cog"></i>
                                <span>Pengaturan</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <header class="topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" id="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="breadcrumb">
                        @yield('breadcrumb')
                    </div>
                </div>

                <div class="topbar-right">
                    <!-- Search -->
                    <div class="search-box">
                        <input type="text" placeholder="Cari di WargaVERSE..." id="global-search">
                        <i class="fas fa-search"></i>
                    </div>

                    <!-- Notifications -->
                    <div class="notification-dropdown">
                        <button class="notification-btn" id="notification-toggle">
                            <i class="fas fa-bell"></i>
                            <span class="notification-count">3</span>
                        </button>

                        <div class="notification-menu" id="notification-menu">
                            <div class="notification-header">
                                <h4>Notifikasi</h4>
                                <button class="mark-all-read">Tandai semua dibaca</button>
                            </div>
                            <div class="notification-list">
                                <div class="notification-item unread">
                                    <div class="notification-icon">
                                        <i class="fas fa-vote-yea"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p><strong>Polling Baru:</strong> Iuran Bulanan Bulan Ini</p>
                                        <span class="notification-time">2 menit lalu</span>
                                    </div>
                                </div>
                                
                                <div class="notification-item">
                                    <div class="notification-icon">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p><strong>Laporan Baru:</strong> Lampu jalan mati di Blok A</p>
                                        <span class="notification-time">1 jam lalu</span>
                                    </div>
                                </div>
                                
                                <div class="notification-item">
                                    <div class="notification-icon">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p><strong>Reminder:</strong> Ronda malam besok</p>
                                        <span class="notification-time">3 jam lalu</span>
                                    </div>
                                </div>
                            </div>
                            <div class="notification-footer">
                                <a href="#" class="view-all-notifications">Lihat semua notifikasi</a>
                            </div>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="user-dropdown">
                        <button class="user-menu-btn" id="user-menu-toggle">
                            <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}" 
                                 alt="{{ Auth::user()->name }}" class="user-avatar-small">
                            <i class="fas fa-chevron-down"></i>
                        </button>

                        <div class="user-menu" id="user-menu">
                            <div class="user-menu-header">
                                <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}" 
                                     alt="{{ Auth::user()->name }}">
                                <div>
                                    <h4>{{ Auth::user()->name }}</h4>
                                    <p>{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                            <div class="user-menu-items">
                                <a href="{{ route('profile.index') }}" class="user-menu-item">
                                    <i class="fas fa-user"></i>
                                    <span>Profil Saya</span>
                                </a>
                                <a href="#" class="user-menu-item">
                                    <i class="fas fa-cog"></i>
                                    <span>Pengaturan</span>
                                </a>
                                <a href="#" class="user-menu-item">
                                    <i class="fas fa-question-circle"></i>
                                    <span>Bantuan</span>
                                </a>
                                <hr class="user-menu-divider">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="user-menu-item logout">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="page-content">
                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="alert alert-success" id="success-alert">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                        <button class="alert-close">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error" id="error-alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                        <button class="alert-close">&times;</button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning" id="warning-alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>{{ session('warning') }}</span>
                        <button class="alert-close">&times;</button>
                    </div>
                @endif

                <!-- Main Content Area -->
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="app-footer">
                <div class="footer-content">
                    <div class="footer-left">
                        <p>&copy; {{ date('Y') }} WargaVERSE. Membangun komunitas digital yang lebih baik.</p>
                    </div>
                    <div class="footer-right">
                        <a href="#" class="footer-link">Tentang</a>
                        <a href="#" class="footer-link">Bantuan</a>
                        <a href="#" class="footer-link">Privasi</a>
                        <a href="#" class="footer-link">Syarat</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    <!-- Additional Scripts -->
    @stack('scripts')

    <script src="{{ asset('js/layouts/app.js') }}"></script> 
</body>
</html>