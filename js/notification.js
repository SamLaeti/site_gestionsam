document.addEventListener('DOMContentLoaded', function() {
    const notification = document.getElementById('notification');
    if (notification) {
        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000); // Masquer après 3 secondes
    }
});