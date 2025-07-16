function shareProfile() {
            if (navigator.share) {
                navigator.share({
                    title: 'Profil {{ $user->name }}',
                    text: 'Lihat profil {{ $user->name }} di RT Digital',
                    url: window.location.href
                });
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('Link profil telah disalin ke clipboard!');
                });
            }
        }