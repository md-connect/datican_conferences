// Mobile menu functionality
console.log('App JS loaded');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
});