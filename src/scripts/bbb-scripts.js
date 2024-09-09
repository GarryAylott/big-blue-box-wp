// Responsive nav menu
const primaryNav = document.querySelector('.nav');
const navToggle = document.querySelector('.menu-nav-toggle');

navToggle.addEventListener('click', () => {
    const visibility = primaryNav.getAttribute('data-visible');

    if (visibility === 'false') {
        primaryNav.setAttribute('data-visible', true);  
        navToggle.setAttribute('aria-expanded', true);
    } else if (visibility === 'true') {
        primaryNav.setAttribute('data-visible', false);
        navToggle.setAttribute('aria-expanded', false);
    }
});

// Fade in background image on scroll
document.addEventListener('DOMContentLoaded', function() {
    const backgroundImage = document.querySelector('.bg-image-fade img');
    
    if (backgroundImage) {
        window.addEventListener('scroll', function() {
            const scrollPosition = window.scrollY;
            const opacity = 1 - (scrollPosition / 600);
            backgroundImage.style.opacity = Math.max(opacity, 0);
        });
    }
});