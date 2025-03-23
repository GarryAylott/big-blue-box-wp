// Responsive nav menu using event delegation
const primaryNav = document.querySelector('.nav');
const navToggle = document.querySelector('.menu-nav-toggle');

navToggle?.addEventListener('click', () => {
    const isVisible = primaryNav?.dataset.visible === 'true';
    primaryNav?.setAttribute('data-visible', String(!isVisible));
    navToggle?.setAttribute('aria-expanded', String(!isVisible));
});

document.addEventListener('DOMContentLoaded', () => {
    // Fade background image on scroll with optimized performance
    const backgroundImage = document.querySelector('.bg-image-fade img');
    
    if (backgroundImage) {
        let ticking = false;
        const setOpacity = () => {
            const scrollPosition = window.scrollY;
            const opacity = Math.max(1 - (scrollPosition / 600), 0);
            backgroundImage.style.opacity = opacity;
            ticking = false;
        };

        setOpacity(); // Initial opacity

        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    setOpacity();
                });
                ticking = true;
            }
        }, { passive: true });
    }

    // Search functionality with event delegation
    const searchIcon = document.querySelector('.nav-search-icon');
    const searchOverlay = document.getElementById('searchOverlay');
    
    searchIcon?.addEventListener('click', (e) => {
        e.preventDefault();
        searchOverlay?.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overlay-active');
    });
    
    // Use event delegation for overlay clicks
    searchOverlay?.addEventListener('click', ({ target }) => {
        if (target === searchOverlay) {
            searchOverlay.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overlay-active');
        }
    });
    
    // Global keyboard event handler
    document.addEventListener('keydown', ({ key }) => {
        if (key === 'Escape' && searchOverlay?.getAttribute('aria-hidden') === 'false') {
            searchOverlay.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overlay-active');
        }
    });

    // Podcast links menu with optimized click handling
    const podLinksDetails = document.querySelector('.pod-links-menu details');
    if (podLinksDetails) {
        document.addEventListener('click', ({ target }) => {
            if (podLinksDetails.open && !podLinksDetails.contains(target)) {
                podLinksDetails.open = false;
            }
        }, { passive: true });
    }
});