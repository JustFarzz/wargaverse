<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="{{ asset('css/editprofile.css') }}">

</head>
<body>
    <div class="profile-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <h1><i class="fas fa-user-edit"></i> Edit Profil</h1>
                <p>Perbarui informasi profil Anda</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            </div>
        </div>
    
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
            @csrf
            @method('PUT')
    
            <div class="form-grid">
                <!-- Avatar Upload Section -->
                <div class="avatar-section">
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-camera"></i> Foto Profil</h2>
                        </div>
                        <div class="card-content">
                            <div class="avatar-upload">
                                <div class="avatar-preview">
                                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}"
                                        alt="Avatar Preview" id="avatar-preview">
                                    <div class="avatar-overlay">
                                        <i class="fas fa-camera"></i>
                                        <span>Ubah Foto</span>
                                    </div>
                                </div>
                                <input type="file" id="avatar-input" name="avatar" accept="image/*" style="display: none;">
                                <div class="avatar-actions">
                                    <button type="button" class="btn btn-primary"
                                        onclick="document.getElementById('avatar-input').click()">
                                        <i class="fas fa-upload"></i>
                                        Pilih Foto
                                    </button>
                                    @if($user->avatar)
                                        <button type="button" class="btn btn-danger" onclick="removeAvatar()">
                                            <i class="fas fa-trash"></i>
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                                <small class="form-text">Format: JPG, PNG, GIF. Maksimal 2MB</small>
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Personal Information -->
                <div class="personal-info">
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-user"></i> Informasi Pribadi</h2>
                        </div>
                        <div class="card-content">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">Nama Lengkap <span class="required">*</span></label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                        required>
                                    @error('name')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
    
                                <div class="form-group">
                                    <label for="email">Email <span class="required">*</span></label>
                                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                        required>
                                    @error('email')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
    
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone">Nomor Telepon</label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                        placeholder="08xxxxxxxxxx">
                                    @error('phone')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
    
                                <div class="form-group">
                                    <label for="birth_date">Tanggal Lahir</label>
                                    <input type="date" id="birth_date" name="birth_date"
                                        value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}">
                                    @error('birth_date')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
    
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="gender">Jenis Kelamin</label>
                                    <select id="gender" name="gender">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="Laki-laki" {{ old('gender', $user->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ old('gender', $user->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('gender')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
    
                                <div class="form-group">
                                    <label for="occupation">Pekerjaan</label>
                                    <input type="text" id="occupation" name="occupation"
                                        value="{{ old('occupation', $user->occupation) }}"
                                        placeholder="Contoh: Pegawai Swasta">
                                    @error('occupation')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
    
                            <div class="form-group">
                                <label for="bio">Tentang Saya</label>
                                <textarea id="bio" name="bio" rows="4"
                                    placeholder="Ceritakan tentang diri Anda...">{{ old('bio', $user->bio) }}</textarea>
                                @error('bio')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Address Information -->
                <div class="address-info">
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-map-marker-alt"></i> Informasi Alamat</h2>
                        </div>
                        <div class="card-content">
                            <div class="form-group">
                                <label for="address">Alamat Lengkap</label>
                                <textarea id="address" name="address" rows="3"
                                    placeholder="Masukkan alamat lengkap Anda">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
    
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="rt_number">RT</label>
                                    <input type="text" id="rt_number" name="rt_number"
                                        value="{{ old('rt_number', $user->rt_number) }}" placeholder="01">
                                    @error('rt_number')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
    
                                <div class="form-group">
                                    <label for="rw_number">RW</label>
                                    <input type="text" id="rw_number" name="rw_number"
                                        value="{{ old('rw_number', $user->rw_number) }}" placeholder="01">
                                    @error('rw_number')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
    
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="kelurahan">Kelurahan</label>
                                    <input type="text" id="kelurahan" name="kelurahan"
                                        value="{{ old('kelurahan', $user->kelurahan) }}" placeholder="Nama Kelurahan">
                                    @error('kelurahan')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
    
                                <div class="form-group">
                                    <label for="kecamatan">Kecamatan</label>
                                    <input type="text" id="kecamatan" name="kecamatan"
                                        value="{{ old('kecamatan', $user->kecamatan) }}" placeholder="Nama Kecamatan">
                                    @error('kecamatan')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
    
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city">Kota</label>
                                    <input type="text" id="city" name="city" value="{{ old('city', $user->city) }}"
                                        placeholder="Nama Kota">
                                    @error('city')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
    
                                <div class="form-group">
                                    <label for="postal_code">Kode Pos</label>
                                    <input type="text" id="postal_code" name="postal_code"
                                        value="{{ old('postal_code', $user->postal_code) }}" placeholder="12345">
                                    @error('postal_code')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Social Media -->
                <div class="social-media">
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-share-alt"></i> Media Sosial</h2>
                        </div>
                        <div class="card-content">
                            <div class="form-group">
                                <label for="facebook_url">
                                    <i class="fab fa-facebook-f"></i>
                                    Facebook URL
                                </label>
                                <input type="url" id="facebook_url" name="facebook_url"
                                    value="{{ old('facebook_url', $user->facebook_url) }}"
                                    placeholder="https://facebook.com/username">
                                @error('facebook_url')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
    
                            <div class="form-group">
                                <label for="instagram_url">
                                    <i class="fab fa-instagram"></i>
                                    Instagram URL
                                </label>
                                <input type="url" id="instagram_url" name="instagram_url"
                                    value="{{ old('instagram_url', $user->instagram_url) }}"
                                    placeholder="https://instagram.com/username">
                                @error('instagram_url')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
    
                            <div class="form-group">
                                <label for="twitter_url">
                                    <i class="fab fa-twitter"></i>
                                    Twitter URL
                                </label>
                                <input type="url" id="twitter_url" name="twitter_url"
                                    value="{{ old('twitter_url', $user->twitter_url) }}"
                                    placeholder="https://twitter.com/username">
                                @error('twitter_url')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
    
                            <div class="form-group">
                                <label for="whatsapp">
                                    <i class="fab fa-whatsapp"></i>
                                    WhatsApp
                                </label>
                                <input type="tel" id="whatsapp" name="whatsapp"
                                    value="{{ old('whatsapp', $user->whatsapp) }}" placeholder="628123456789">
                                @error('whatsapp')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Password Change -->
                <div class="password-section">
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-key"></i> Ubah Password</h2>
                        </div>
                        <div class="card-content">
                            <div class="form-group">
                                <label for="current_password">Password Saat Ini</label>
                                <input type="password" id="current_password" name="current_password"
                                    placeholder="Masukkan password saat ini">
                                @error('current_password')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
    
                            <div class="form-group">
                                <label for="password">Password Baru</label>
                                <input type="password" id="password" name="password" placeholder="Masukkan password baru">
                                @error('password')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
    
                            <div class="form-group">
                                <label for="password_confirmation">Konfirmasi Password Baru</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    placeholder="Konfirmasi password baru">
                                @error('password_confirmation')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
    
    <script src="{{ asset('js/profile/edit.js') }}"></script>
</body>
</html>