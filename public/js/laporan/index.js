    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        filterReports();
    });
    
    // Filter functionality
    document.getElementById('statusFilter').addEventListener('change', filterReports);
    document.getElementById('categoryFilter').addEventListener('change', filterReports);
    
    function filterReports() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const categoryFilter = document.getElementById('categoryFilter').value;
        
        const reportCards = document.querySelectorAll('.report-card');
        
        reportCards.forEach(card => {
            const title = card.querySelector('.report-title').textContent.toLowerCase();
            const description = card.querySelector('.report-description').textContent.toLowerCase();
            const status = card.dataset.status;
            const category = card.dataset.category;
            
            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
            const matchesStatus = !statusFilter || status === statusFilter;
            const matchesCategory = !categoryFilter || category === categoryFilter;
            
            if (matchesSearch && matchesStatus && matchesCategory) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }