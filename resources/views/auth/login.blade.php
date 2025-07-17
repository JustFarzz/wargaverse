<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WargaVERSE</title>
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <!-- Header -->
            <div class="auth-header">
                <div class="logo">
                    <i class="fas fa-leaf"></i>
                    <span>WargaVERSE</span>
                </div>
                <h2>Selamat Datang</h2>
                <p>Bergabunglah dengan komunitas hijau digital RT/RW Anda</p>
            </div>

            <!-- Login Type Selector -->
            <div class="login-type-selector">
                <button type="button" class="type-btn active" data-type="warga">
                    <i class="fas fa-users"></i>
                    Login Warga
                </button>
                <button type="button" class="type-btn" data-type="admin">
                    <i class="fas fa-user-shield"></i>
                    Login Admin
                </button>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf

                <!-- Login Type Hidden Field -->
                <input type="hidden" name="login_type" value="warga">

                <!-- Email Field -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Alamat Email" value="{{ old('email') }}" required>
                    </div>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Kata Sandi" required>
                        <button type="button" class="toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="form-options">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        Ingat saya
                    </label>
                    <a href="#" class="forgot-link">Lupa kata sandi?</a>
                </div>

                <!-- General Error Message -->
                @error('general')
                    <div class="error-message" style="text-align: center; margin-bottom: 18px;">
                        {{ $message }}
                    </div>
                @enderror

                <!-- Submit Button -->
                <button type="submit" class="auth-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    <span class="btn-text">Masuk ke Komunitas</span>
                </button>
            </form>

            <!-- Footer -->
            <div class="auth-footer">
                <p class="register-text">Belum bergabung? <a href="{{ route('register') }}">Daftar sekarang</a></p>
                <p class="admin-info" style="display: none;">
                    <i class="fas fa-shield-alt"></i>
                    Portal khusus Admin RT/RW
                </p>
            </div>
        </div>

        <!-- Background Elements -->
        <div class="bg-decoration">
            <div class="circle circle-1"></div>
            <div class="circle circle-2"></div>
            <div class="circle circle-3"></div>
            <div class="circle circle-4"></div>
            <div class="circle circle-5"></div>
        </div>
    </div>

    <script src="{{ asset('js/auth/login.js') }}"></script>
</body>

</html>