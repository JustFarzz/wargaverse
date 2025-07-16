document.addEventListener('DOMContentLoaded', function () {
            // Filter functionality
            const typeFilter = document.getElementById('typeFilter');
            const monthFilter = document.getElementById('monthFilter');

            typeFilter.addEventListener('change', function () {
                filterTransactions();
            });

            monthFilter.addEventListener('change', function () {
                filterTransactions();
            });

            function filterTransactions() {
                const type = typeFilter.value;
                const month = monthFilter.value;

                // Create URL with filters
                const url = new URL(window.location.href);

                if (type) {
                    url.searchParams.set('type', type);
                } else {
                    url.searchParams.delete('type');
                }

                if (month) {
                    url.searchParams.set('month', month);
                } else {
                    url.searchParams.delete('month');
                }

                // Redirect to filtered URL
                window.location.href = url.toString();
            }

            // Auto-refresh every 5 minutes to show latest data
            setInterval(function () {
                window.location.reload();
            }, 300000); // 5 minutes
        });