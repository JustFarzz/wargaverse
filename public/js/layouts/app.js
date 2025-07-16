        // Initialize App
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loading screen
            setTimeout(() => {
                document.getElementById('loading-screen').style.display = 'none';
            }, 500);

            // Sidebar toggle
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebarClose = document.getElementById('sidebar-close');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
                document.body.classList.toggle('sidebar-open');
            }

            sidebarToggle?.addEventListener('click', toggleSidebar);
            sidebarClose?.addEventListener('click', toggleSidebar);
            overlay?.addEventListener('click', toggleSidebar);

            // Notification dropdown
            const notificationToggle = document.getElementById('notification-toggle');
            const notificationMenu = document.getElementById('notification-menu');

            notificationToggle?.addEventListener('click', (e) => {
                e.stopPropagation();
                notificationMenu.classList.toggle('active');
                document.getElementById('user-menu')?.classList.remove('active');
            });

            // User menu dropdown
            const userMenuToggle = document.getElementById('user-menu-toggle');
            const userMenu = document.getElementById('user-menu');

            userMenuToggle?.addEventListener('click', (e) => {
                e.stopPropagation();
                userMenu.classList.toggle('active');
                notificationMenu?.classList.remove('active');
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', () => {
                notificationMenu?.classList.remove('active');
                userMenu?.classList.remove('active');
            });

            // Alert auto-close
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const closeBtn = alert.querySelector('.alert-close');
                closeBtn?.addEventListener('click', () => {
                    alert.style.animation = 'slideOut 0.3s ease-in-out';
                    setTimeout(() => alert.remove(), 300);
                });

                // Auto close after 5 seconds
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.style.animation = 'slideOut 0.3s ease-in-out';
                        setTimeout(() => alert.remove(), 300);
                    }
                }, 5000);
            });

            // Search functionality
            const globalSearch = document.getElementById('global-search');
            globalSearch?.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    const query = e.target.value.trim();
                    if (query) {
                        window.location.href = `/search?q=${encodeURIComponent(query)}`;
                    }
                }
            });

            // Mark notifications as read
            const markAllRead = document.querySelector('.mark-all-read');
            markAllRead?.addEventListener('click', () => {
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                });
                document.querySelector('.notification-count').textContent = '0';
            });
        });