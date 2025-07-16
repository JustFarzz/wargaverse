<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WargaVERSE</title>
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <!-- Header -->
            <div class="auth-header">
                <div class="logo">
                    <i class="fas fa-home"></i>
                    <span>WargaVERSE</span>
                </div>
                <h2>Selamat Datang</h2>
                <p>Masuk ke komunitas digital RT/RW Anda</p>
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
                        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                    </div>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
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
                    <a href="#" class="forgot-link">Lupa password?</a>
                </div>

                <!-- General Error Message -->
                @error('general')
                    <div class="error-message" style="text-align: center; margin-bottom: 15px;">
                        {{ $message }}
                    </div>
                @enderror

                <!-- Submit Button -->
                <button type="submit" class="auth-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    <span class="btn-text">Masuk</span>
                </button>
            </form>

            <!-- Footer -->
            <div class="auth-footer">
                <p class="register-text">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
                <p class="admin-info" style="display: none;">
                    <i class="fas fa-info-circle"></i>
                    Login khusus untuk Admin RT/RW
                </p>
            </div>
        </div>

        <!-- Background Elements -->
        <div class="bg-decoration">
            <div class="circle circle-1"></div>
            <div class="circle circle-2"></div>
            <div class="circle circle-3"></div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.querySelector('.toggle-password').addEventListener('click', function () {
            const passwordInput = document.querySelector('input[name="password"]');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Initialize login type based on old input
        document.addEventListener('DOMContentLoaded', function () {
            const oldLoginType = "{{ old('login_type', 'warga') }}";

            // Set initial state based on old input
            if (oldLoginType === 'admin') {
                document.querySelector('[data-type="admin"]').click();
            }
        });

        // Login type selector
        document.querySelectorAll('.type-btn').forEach(button => {
            button.addEventListener('click', function () {
                // Remove active class from all buttons
                document.querySelectorAll('.type-btn').forEach(btn => btn.classList.remove('active'));

                // Add active class to clicked button
                this.classList.add('active');

                // Get login type
                const loginType = this.dataset.type;

                // Update hidden field
                document.querySelector('input[name="login_type"]').value = loginType;

                // Update form elements based on login type
                const btnText = document.querySelector('.btn-text');
                const registerText = document.querySelector('.register-text');
                const adminInfo = document.querySelector('.admin-info');
                const emailInput = document.querySelector('input[name="email"]');
                const formHeader = document.querySelector('.auth-header h2');
                const formDescription = document.querySelector('.auth-header p');

                if (loginType === 'admin') {
                    btnText.textContent = 'Masuk sebagai Admin';
                    registerText.style.display = 'none';
                    adminInfo.style.display = 'block';
                    emailInput.placeholder = 'Email Admin';
                    formHeader.textContent = 'Admin Dashboard';
                    formDescription.textContent = 'Masuk ke panel administrasi RT/RW';
                } else {
                    btnText.textContent = 'Masuk';
                    registerText.style.display = 'block';
                    adminInfo.style.display = 'none';
                    emailInput.placeholder = 'Email';
                    formHeader.textContent = 'Selamat Datang';
                    formDescription.textContent = 'Masuk ke komunitas digital RT/RW Anda';
                }
            });
        });

        // Form validation
        document.querySelector('.auth-form').addEventListener('submit', function (e) {
            const email = document.querySelector('input[name="email"]').value;
            const password = document.querySelector('input[name="password"]').value;
            const loginType = document.querySelector('input[name="login_type"]').value;

            if (!email || !password) {
                e.preventDefault();
                alert('Mohon lengkapi email dan password.');
                return;
            }

            if (!email.includes('@')) {
                e.preventDefault();
                alert('Format email tidak valid.');
                return;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('Password minimal 6 karakter.');
                return;
            }

            // Show loading state
            const submitButton = document.querySelector('.auth-btn');
            const btnText = document.querySelector('.btn-text');
            const originalText = btnText.textContent;

            submitButton.disabled = true;
            btnText.textContent = 'Memproses...';

            // Re-enable button if there's an error (fallback)
            setTimeout(() => {
                submitButton.disabled = false;
                btnText.textContent = originalText;
            }, 10000);
        });
    </script>
</body>

</html> 