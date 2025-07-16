{{-- resources/views/components/footer.blade.php --}}
<footer class="footer">
    <div class="footer-container">
        <!-- Footer Main Content -->
        <div class="footer-content">
            <!-- About Section -->
            <div class="footer-section">
                <div class="footer-brand">
                    <div class="footer-logo">
                        <i class="fas fa-home"></i>
                        <span>RT Digital</span>
                    </div>
                    <p class="footer-description">
                        Platform digital untuk memudahkan komunikasi dan koordinasi warga RT.
                        Menghubungkan tetangga, membangun komunitas yang lebih solid.
                    </p>
                    <div class="footer-stats">
                        <div class="stat-item">
                            <span class="stat-number">{{ $totalWarga ?? '0' }}</span>
                            <span class="stat-label">Warga Terdaftar</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">{{ $totalPost ?? '0' }}</span>
                            <span class="stat-label">Post Timeline</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">{{ $totalLaporan ?? '0' }}</span>
                            <span class="stat-label">Laporan</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-section">
                <h3 class="footer-title">Menu Utama</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Beranda</span>
                    </a></li>
                    <li><a href="{{ route('timeline.index') }}">
                        <i class="fas fa-comments"></i>
                        <span>Timeline</span>
                    </a></li>
                    <li><a href="{{ route('laporan.index') }}">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Laporan Warga</span>
                    </a></li>
                    <li><a href="{{ route('polling.index') }}">
                        <i class="fas fa-poll"></i>
                        <span>Polling</span>
                    </a></li>
                    <li><a href="{{ route('kalender.index') }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Kalender RT</span>
                    </a></li>
                    <li><a href="{{ route('kas.index') }}">
                        <i class="fas fa-wallet"></i>
                        <span>Kas RT</span>
                    </a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="footer-section">
                <h3 class="footer-title">Kontak RT</h3>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div class="contact-details">
                            <span class="contact-label">Alamat</span>
                            <span class="contact-value">{{ $alamatRT ?? 'Jl. Contoh No. 123, RT 01/RW 05' }}</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-user-tie"></i>
                        <div class="contact-details">
                            <span class="contact-label">Ketua RT</span>
                            <span class="contact-value">{{ $ketuaRT ?? 'Bapak Suyanto' }}</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <div class="contact-details">
                            <span class="contact-label">Telepon</span>
                            <span class="contact-value">{{ $teleponRT ?? '0341-123456' }}</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div class="contact-details">
                            <span class="contact-label">Email</span>
                            <span class="contact-value">{{ $emailRT ?? 'rt01@example.com' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="footer-section">
                <h3 class="footer-title">Aksi Cepat</h3>
                <div class="footer-actions">
                    <a href="{{ route('timeline.create') }}" class="footer-action-btn primary">
                        <i class="fas fa-plus-circle"></i>
                        <span>Buat Postingan</span>
                    </a>
                    <a href="{{ route('laporan.create') }}" class="footer-action-btn warning">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Laporkan Masalah</span>
                    </a>
                    <a href="{{ route('polling.create') }}" class="footer-action-btn info">
                        <i class="fas fa-poll-h"></i>
                        <span>Buat Polling</span>
                    </a>
                    <a href="{{ route('kalender.create') }}" class="footer-action-btn success">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Tambah Kegiatan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="footer-copyright">
                    <p>&copy; {{ date('Y') }} RT Digital. Dibuat dengan ❤️ untuk kemudahan warga RT.</p>
                    <p class="footer-version">Versi 1.0.0 | Update terakhir: {{ date('d M Y') }}</p>
                </div>
                
                <div class="footer-social">
                    <span class="social-label">Ikuti Kami:</span>
                    <div class="social-links">
                        @if($whatsappRT ?? null)
                        <a href="https://wa.me/{{ $whatsappRT }}" class="social-link whatsapp" target="_blank">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        @endif
                        @if($facebookRT ?? null)
                        <a href="{{ $facebookRT }}" class="social-link facebook" target="_blank">
                            <i class="fab fa-facebook"></i>
                        </a>
                        @endif
                        @if($instagramRT ?? null)
                        <a href="{{ $instagramRT }}" class="social-link instagram" target="_blank">
                            <i class="fab fa-instagram"></i>
                        </a>
                        @endif
                        @if($telegramRT ?? null)
                        <a href="{{ $telegramRT }}" class="social-link telegram" target="_blank">
                            <i class="fab fa-telegram"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" title="Kembali ke atas">
        <i class="fas fa-chevron-up"></i>
    </button>
</footer>

<script src="{{ asset('js/components/footer.js') }}"></script>