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
