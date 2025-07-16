// Avatar preview
        document.getElementById('avatar-input').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Remove avatar
        function removeAvatar() {
            if (confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
                document.getElementById('avatar-preview').src = '{{ asset("images/default-avatar.png") }}';
                document.getElementById('avatar-input').value = '';

                // Add hidden input to mark for deletion
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'remove_avatar';
                deleteInput.value = '1';
                document.querySelector('.profile-form').appendChild(deleteInput);
            }
        }

        // Form validation
        document.querySelector('.profile-form').addEventListener('submit', function (e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();

            if (!name || !email) {
                e.preventDefault();
                alert('Nama dan Email wajib diisi!');
                return false;
            }

            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;

            if (newPassword && !currentPassword) {
                e.preventDefault();
                alert('Password saat ini wajib diisi untuk mengubah password!');
                return false;
            }

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Konfirmasi password tidak cocok!');
                return false;
            }
        });