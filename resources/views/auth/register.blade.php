<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - WargaVERSE</title>
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card register-card">
            <!-- Header -->
            <div class="auth-header">
                <div class="logo">
                    <i class="fas fa-home"></i>
                    <span>WargaVERSE</span>
                </div>
                <h2>Bergabung dengan Komunitas</h2>
                <p>Daftarkan diri Anda sebagai warga digital RT/RW</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf
                
                <!-- Nama Lengkap -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" 
                               name="name" 
                               placeholder="Nama Lengkap" 
                               value="{{ old('name') }}" 
                               required>
                    </div>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" 
                               name="email" 
                               placeholder="Email" 
                               value="{{ old('email') }}" 
                               required>
                    </div>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Nomor HP -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-phone"></i>
                        <input type="tel" 
                               name="phone" 
                               placeholder="Nomor HP (08xxxxxxxxxx)" 
                               value="{{ old('phone') }}" 
                               required>
                    </div>
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Alamat -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-map-marker-alt"></i>
                        <input type="text" 
                               name="address" 
                               placeholder="Alamat (Blok/No. Rumah)" 
                               value="{{ old('address') }}" 
                               required>
                    </div>
                    @error('address')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- RT/RW -->
                <div class="form-row">
                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fas fa-building"></i>
                            <select name="rt" required>
                                <option value="">RT</option>
                                @for($i = 1; $i <= 20; $i++)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" 
                                            {{ old('rt') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        RT {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        @error('rt')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fas fa-city"></i>
                            <select name="rw" required>
                                <option value="">RW</option>
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" 
                                            {{ old('rw') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        RW {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        @error('rw')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" 
                               name="password" 
                               placeholder="Password (minimal 8 karakter)" 
                               required>
                        <button type="button" class="toggle-password" data-target="password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" 
                               name="password_confirmation" 
                               placeholder="Konfirmasi Password" 
                               required>
                        <button type="button" class="toggle-password" data-target="password_confirmation">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Terms -->
                <div class="form-group">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="terms" required>
                        <span class="checkmark"></span>
                        Saya setuju dengan <a href="#" class="terms-link">syarat dan ketentuan</a>
                    </label>
                    @error('terms')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="auth-btn">
                    <i class="fas fa-user-plus"></i>
                    Daftar Sekarang
                </button>
            </form>

            <!-- Footer -->
            <div class="auth-footer">
                <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
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
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const target = this.dataset.target;
                const passwordInput = document.querySelector(`input[name="${target}"]`);
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
        });

        // Phone number formatting
        document.querySelector('input[name="phone"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('0')) {
                value = value.substring(1);
            }
            if (value.length > 0) {
                value = '08' + value;
            }
            e.target.value = value;
        });
    </script>
</body>
</html>