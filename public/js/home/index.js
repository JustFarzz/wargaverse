// Auto refresh untuk data realtime (opsional)
        setInterval(function() {
            // Refresh stats setiap 5 menit
            fetch('{{ route("home.stats") }}')
                .then(response => response.json())
                .then(data => {
                    // Update stats numbers
                    document.querySelectorAll('.stat-card h3').forEach((element, index) => {
                        const statValues = [data.totalPosts, data.totalReports, data.activePolls, 'Rp ' + data.kasBalance.toLocaleString('id-ID')];
                        if (statValues[index]) {
                            element.textContent = statValues[index];
                        }
                    });
                })
                .catch(error => console.log('Stats refresh error:', error));
        }, 300000); // 5 minutes