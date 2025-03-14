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

document.addEventListener('DOMContentLoaded', function() {
    // Fade background image on scroll
    const backgroundImage = document.querySelector('.bg-image-fade img');
    
    if (backgroundImage) {
        // Set initial opacity based on current scroll position
        const setOpacity = () => {
            const scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
            const opacity = 1 - (scrollPosition / 600);
            backgroundImage.style.opacity = Math.max(opacity, 0);
        };

        // Set initial opacity on page load
        setOpacity();

        // Update opacity on scroll
        window.addEventListener('scroll', setOpacity);
    }

    // Search function
    const searchIcon = document.querySelector('.nav-search-icon');
    const searchOverlay = document.getElementById('searchOverlay');
    const body = document.body;
    
    searchIcon.addEventListener('click', (e) => {
        e.preventDefault();
        searchOverlay.setAttribute('aria-hidden', 'false');
        body.classList.add('overlay-active');
    });
    
    searchOverlay.addEventListener('click', (e) => {
        if (e.target === searchOverlay) {
            searchOverlay.setAttribute('aria-hidden', 'true');
            body.classList.remove('overlay-active');
        }
    });
    
    // Add keyboard support to close on Esc
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && searchOverlay.getAttribute('aria-hidden') === 'false') {
            searchOverlay.setAttribute('aria-hidden', 'true');
            body.classList.remove('overlay-active');
        }
    });
});