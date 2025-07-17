// Auto refresh untuk data realtime (opsional)
setInterval(function () {
    // Refresh stats setiap 5 menit
    fetch('/home/stats')
        .then(response => response.json())
        .then(data => {
            // Update stats numbers
            const statCards = document.querySelectorAll('.stat-card h3');
            if (statCards.length >= 4) {
                statCards[0].textContent = data.totalPosts;
                statCards[1].textContent = data.totalReports;
                statCards[2].textContent = data.activePolls;
                statCards[3].textContent = 'Rp ' + data.kasBalance.toLocaleString('id-ID');
            }

            // Update financial summary di bagian bawah
            updateFinancialSummary(data.kasBalance);
        })
        .catch(error => console.log('Stats refresh error:', error));
}, 300000); // 5 minutes

// Function untuk update financial summary
function updateFinancialSummary(balance) {
    const balanceElement = document.querySelector('.finance-item .finance-amount.positive');
    if (balanceElement) {
        balanceElement.textContent = 'Rp ' + balance.toLocaleString('id-ID');
    }
}

// Function untuk load recent activities (jika diperlukan)
function loadRecentActivities() {
    fetch('/home/activities')
        .then(response => response.json())
        .then(data => {
            // Update notifications atau activity feed jika ada
            console.log('Recent activities:', data);
        })
        .catch(error => console.log('Activities load error:', error));
}

// Function untuk refresh dashboard summary
function refreshDashboardSummary(startDate = null, endDate = null) {
    const params = new URLSearchParams();
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);

    fetch('/home/dashboard-summary?' + params.toString())
        .then(response => response.json())
        .then(data => {
            // Update dashboard summary jika diperlukan
            console.log('Dashboard summary:', data);
        })
        .catch(error => console.log('Dashboard summary error:', error));
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    console.log('Home dashboard initialized');

    // Load initial activities jika diperlukan
    // loadRecentActivities();
});