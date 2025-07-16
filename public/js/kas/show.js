document.addEventListener('DOMContentLoaded', function() {
    // Like buttons
    document.querySelectorAll('.comment-like').forEach(btn => {
        btn.addEventListener('click', function() {
            this.classList.toggle('liked');
            // Update like count logic here
        });
    });
    
    // Share buttons
    document.querySelectorAll('.share-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.classList[1];
            const url = window.location.href;
            const title = document.querySelector('h1').textContent;
            
            switch(type) {
                case 'whatsapp':
                    window.open(`https://wa.me/?text=${encodeURIComponent(title + ' - ' + url)}`);
                    break;
                case 'telegram':
                    window.open(`https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`);
                    break;
                case 'link':
                    navigator.clipboard.writeText(url);
                    alert('Link berhasil disalin!');
                    break;
            }
        });
    });
    
    // Comment form
    const commentForm = document.querySelector('.add-comment textarea');
    const sendBtn = document.querySelector('.comment-form-actions .btn-primary');
    
    sendBtn.addEventListener('click', function() {
        const comment = commentForm.value.trim();
        if (comment) {
            // Add comment logic here
            console.log('Adding comment:', comment);
            commentForm.value = '';
        }
    });
});

function deleteTransaction() {
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

function confirmDelete() {
    // Delete logic here
    console.log('Deleting transaction...');
    closeModal();
    // Redirect to kas.index
    window.location.href = '{{ route("kas.index") }}';
}