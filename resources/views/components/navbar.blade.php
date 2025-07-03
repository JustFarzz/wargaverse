{{-- resources/views/components/navbar.blade.php --}}
<nav class="navbar">
    <div class="nav-container">
        <!-- Logo & Brand -->
        <div class="nav-brand">
            <a href="{{ route('home') }}" class="brand-link">
                <div class="brand-icon">
                    <i class="fas fa-home"></i>
                </div>
                <span class="brand-text">RT Digital</span>
            </a>
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="mobile-toggle" id="mobileToggle">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="nav-menu" id="navMenu">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Beranda</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('timeline.index') }}" class="nav-link {{ request()->routeIs('timeline.*') ? 'active' : '' }}">
                        <i class="fas fa-comments"></i>
                        <span>Timeline</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Laporan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('polling.index') }}" class="nav-link {{ request()->routeIs('polling.*') ? 'active' : '' }}">
                        <i class="fas fa-poll"></i>
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

            <!-- User Menu -->
            <div class="user-menu">
                <div class="user-dropdown">
                    <button class="user-toggle" id="userToggle">
                        <div class="user-avatar">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar">
                            @else
                                <i class="fas fa-user"></i>
                            @endif
                        </div>
                        <div class="user-info">
                            <span class="user-name">{{ Auth::user()->name }}</span>
                            <span class="user-role">{{ Auth::user()->role == 'admin' ? 'Admin RT' : 'Warga' }}</span>
                        </div>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </button>
                    
                    <div class="dropdown-menu" id="userDropdown">
                        <a href="{{ route('profile.index') }}" class="dropdown-item">
                            <i class="fas fa-user-circle"></i>
                            <span>Profil Saya</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i class="fas fa-cog"></i>
                            <span>Pengaturan</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" class="dropdown-form">
                            @csrf
                            <button type="submit" class="dropdown-item logout-btn">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileToggle = document.getElementById('mobileToggle');
    const navMenu = document.getElementById('navMenu');
    
    mobileToggle.addEventListener('click', function() {
        navMenu.classList.toggle('active');
        mobileToggle.classList.toggle('active');
    });

    // User dropdown toggle
    const userToggle = document.getElementById('userToggle');
    const userDropdown = document.getElementById('userDropdown');
    
    userToggle.addEventListener('click', function(e) {
        e.preventDefault();
        userDropdown.classList.toggle('active');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!userToggle.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.classList.remove('active');
        }
        
        if (!mobileToggle.contains(e.target) && !navMenu.contains(e.target)) {
            navMenu.classList.remove('active');
            mobileToggle.classList.remove('active');
        }
    });
});
</script>