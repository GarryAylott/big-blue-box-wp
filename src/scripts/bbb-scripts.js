import Vlitejs from "vlitejs";
import {
    createIcons,
    Mic,
    Headphones,
    ArrowUp,
    ArrowDown,
    ArrowRight,
    ChevronDown,
    Newspaper,
    Rss,
    ListTree,
    Clock,
    Search,
    X,
    Tag,
} from "lucide";

// Lucide setup
let icons = {};

if (process.env.NODE_ENV === "production") {
    icons = {
        Mic,
        Headphones,
        ArrowUp,
        ArrowDown,
        ArrowRight,
        ChevronDown,
        Newspaper,
        Rss,
        ListTree,
        Clock,
        Search,
        X,
        Tag,
    };
}

document.addEventListener("DOMContentLoaded", () => {
    if (Object.keys(icons).length > 0) {
        // Production: only registered icons
        createIcons({ icons });
    } else {
        // Development: load all icons
        createIcons();
    }
});

// Configuration object
const CONFIG = {
    FADE_DISTANCE: 900,
    SCROLL_ANIMATION_DELAY: 100,
    SELECTORS: {
        nav: ".nav",
        navToggle: ".menu-nav-toggle",
        bgImage: ".hero-bg-image img",
        searchIcon: ".nav-search-icon",
        searchOverlay: "#searchOverlay",
        podcastMenu: ".pod-links-menu details",
        scrollContainer: ".posts-hori-scroll",
        suggestedPosts: ".suggested-posts",
        categorySwitcher: ".switch-btn",
    },
};

// Cache DOM elements
const elements = {
    primaryNav: document.querySelector(CONFIG.SELECTORS.nav),
    navToggle: document.querySelector(CONFIG.SELECTORS.navToggle),
};

// Navigation toggle
const initNavigation = () => {
    elements.navToggle?.addEventListener("click", () => {
        const isVisible = elements.primaryNav?.dataset.visible === "true";
        elements.primaryNav?.setAttribute("data-visible", String(!isVisible));
        elements.navToggle?.setAttribute("aria-expanded", String(!isVisible));
    });
};

// Background fade
const initBackgroundFade = () => {
    const backgroundImage = document.querySelector(CONFIG.SELECTORS.bgImage);
    if (!backgroundImage) return;

    let ticking = false;
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const scrollHandler = () => {
                    if (!ticking) {
                        requestAnimationFrame(() => {
                            const opacity = Math.max(
                                1 - window.scrollY / CONFIG.FADE_DISTANCE,
                                0
                            );
                            backgroundImage.style.opacity = opacity;
                            ticking = false;
                        });
                        ticking = true;
                    }
                };
                scrollHandler(); // Set initial opacity
                window.addEventListener("scroll", scrollHandler, {
                    passive: true,
                });
            }
        });
    });
    observer.observe(backgroundImage);
};

// Search functionality
const initSearch = () => {
    const searchIcon = document.querySelector(CONFIG.SELECTORS.searchIcon);
    const searchOverlay = document.querySelector(
        CONFIG.SELECTORS.searchOverlay
    );
    if (!searchIcon || !searchOverlay) return;

    const closeOverlay = () => {
        const searchField = document.querySelector("#search-field");
        if (searchField && document.activeElement === searchField) {
            searchField.blur(); // ✅ Blur before hiding to prevent aria-hidden focus warning
        }

        searchOverlay.setAttribute("aria-hidden", "true");
        document.body.classList.remove("overlay-active");
    };

    searchIcon.addEventListener("click", (e) => {
        e.preventDefault();
        searchOverlay.setAttribute("aria-hidden", "false");
        document.body.classList.add("overlay-active");
        const searchField = document.querySelector("#search-field");
        if (searchField) searchField.focus(); // ✅ Only after aria-hidden is false
    });

    searchOverlay.addEventListener("click", ({ target }) => {
        if (target === searchOverlay) closeOverlay();
    });

    document.addEventListener("keydown", ({ key }) => {
        if (
            key === "Escape" &&
            searchOverlay.getAttribute("aria-hidden") === "false"
        ) {
            closeOverlay();
        }
    });
};

// Podcast links menu
const initPodcastMenu = () => {
    const podLinksDetails = document.querySelector(
        CONFIG.SELECTORS.podcastMenu
    );
    if (!podLinksDetails) return;

    const closeHandler = ({ target }) => {
        if (podLinksDetails.open && !podLinksDetails.contains(target)) {
            podLinksDetails.open = false;
        }
    };

    document.addEventListener("click", closeHandler, { passive: true });
};

// Scroll containers
const initScrollContainers = () => {
    const scrollContainers = document.querySelectorAll(
        CONFIG.SELECTORS.scrollContainer
    );
    if (!scrollContainers.length) return;

    // Create a Map to store container-specific update functions
    const updateFunctions = new Map();

    scrollContainers.forEach((container) => {
        const header = container
            .closest(CONFIG.SELECTORS.suggestedPosts)
            ?.querySelector(".suggested-posts-header");
        if (!header) return;

        const leftBtn = header.querySelector(".scroll-left");
        const rightBtn = header.querySelector(".scroll-right");
        const scrollAmount =
            container.querySelector("article")?.offsetWidth ?? 0 + 16;

        const updateButtonStates = () => {
            const maxScroll = container.scrollWidth - container.clientWidth;
            leftBtn.disabled = container.scrollLeft <= 0;
            rightBtn.disabled = container.scrollLeft >= maxScroll - 1;
        };

        // Store the update function in the Map
        updateFunctions.set(container, updateButtonStates);

        const scrollHandler = (direction) => {
            container.scrollBy({
                left: direction * scrollAmount,
                behavior: "smooth",
            });
            setTimeout(updateButtonStates, CONFIG.SCROLL_ANIMATION_DELAY);
        };

        leftBtn?.addEventListener("click", () => scrollHandler(-1));
        rightBtn?.addEventListener("click", () => scrollHandler(1));
        container.addEventListener("scroll", updateButtonStates, {
            passive: true,
        });

        updateButtonStates();
    });

    // Create ResizeObserver after the functions are defined
    const resizeObserver = new ResizeObserver((entries) => {
        entries.forEach((entry) => {
            const container = entry.target;
            const updateFn = updateFunctions.get(container);
            if (updateFn) {
                updateFn();
            }
        });
    });

    // Observe all containers
    scrollContainers.forEach((container) => {
        resizeObserver.observe(container);
    });
};

// Add external link icons
const initExternalLinkIcons = () => {
    const links = document.querySelectorAll(
        'a[target="_blank"]:not(.has-external-icon)'
    );
    const themeUrl = themeSettings.themeUrl || "";

    links.forEach((link) => {
        // Create <span> element for the icon
        const icon = document.createElement("span");
        icon.innerHTML = `<svg width="16" height="16" viewBox="0 0 16 16" class="external-link-icon">
            <path d="M13 3L3 13M13 3H4M13 3V12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>`;

        // Append icon and mark as processed
        link.appendChild(icon);
        link.classList.add("has-external-icon");
    });
};

// Tardis icon scroll progress
const initTardisScrollProgress = () => {
    const container = document.querySelector(".tardis-progress-container");
    const fillRect = document.getElementById("tardis-fill");
    const statusText = document.getElementById("tardisProgressStatus");
    const featuredImage = document.querySelector(".post-featured-image");
    const article = document.querySelector(".post-article");
    const comments = document.querySelector("#comments");

    if (!container || !fillRect || !statusText || !article) {
        return;
    }

    const showAfter = featuredImage?.offsetHeight || 100;
    const fillHeight = 864; // Match SVG height
    let ticking = false;

    const updateProgress = () => {
        const scrollY = window.scrollY || window.pageYOffset;

        // Calculate article end position
        const articleEnd = article.offsetTop + article.offsetHeight;
        const viewportHeight = window.innerHeight;
        const scrollDistance = articleEnd - viewportHeight;

        // Add offset to ensure fill completes exactly at article end
        const progress = Math.min(scrollY / (scrollDistance + 100), 1);
        const percent = Math.round(progress * 100);

        // Show/hide logic
        if (
            scrollY > showAfter &&
            scrollY < scrollDistance + viewportHeight + -700
        ) {
            container.classList.add("visible");
        } else {
            container.classList.remove("visible");
        }

        // Move the blue fill up
        const translateY = fillHeight * (1 - progress);
        fillRect.setAttribute("y", translateY);

        // Update screen reader text
        statusText.textContent = `Reading progress: ${percent}%`;

        ticking = false;
    };

    const onScroll = () => {
        if (!ticking) {
            requestAnimationFrame(updateProgress);
            ticking = true;
        }
    };

    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll, { passive: true });
    updateProgress(); // initial state
};

// AJAX request for category switcher
const initCategorySwitcher = () => {
    // Only enable on homepage or a specific template, NOT search results
    if (document.body.matches(".search-results, .search-no-results")) return;

    const switchButtons = document.querySelectorAll(
        CONFIG.SELECTORS.categorySwitcher
    );
    const postContainer = document.getElementById("ajax-posts-container");
    if (!switchButtons.length || !postContainer) return;

    let requestCounter = 0;

    const setButtonsDisabled = (disabled) => {
        switchButtons.forEach((btn) => {
            btn.disabled = disabled;
        });
    };

    const beginLoading = () => {
        postContainer.setAttribute("aria-busy", "true");
    };

    const endLoading = () => {
        postContainer.removeAttribute("aria-busy");
    };

    const fetchCategoryPosts = (category) => {
        const reqId = ++requestCounter;

        // Disable controls and mark busy (no pre-fade-out)
        setButtonsDisabled(true);
        beginLoading();

        // Only for homepage/archive, so no search stuff
        const params = {
            action: "filter_posts_by_category",
            category: category,
            context: "home",
        };

        fetch(themeSettings.ajaxUrl, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams(params),
        })
            .then((res) => res.text())
            .then((html) => {
                // Ignore stale responses from earlier clicks
                if (reqId !== requestCounter) return;

                // Swap content, then run entry-only animation
                postContainer.innerHTML = html;

                // Setup initial hidden state for children, then activate transition
                postContainer.classList.add("enter-setup");
                // Force reflow to commit styles
                void postContainer.offsetHeight;
                postContainer.classList.remove("enter-setup");
                postContainer.classList.add("enter-active");

                // Clean up after animation window
                setTimeout(() => {
                    postContainer.classList.remove("enter-active");
                }, 260);

                endLoading();
                setButtonsDisabled(false);
            })
            .catch((err) => {
                console.error("Category switch AJAX error:", err);
                endLoading();
                setButtonsDisabled(false);
            });
    };

    switchButtons.forEach((button) => {
        button.addEventListener("click", (e) => {
            e.preventDefault();
            const category = button.dataset.category;

            switchButtons.forEach((btn) => {
                btn.setAttribute("aria-pressed", "false");
                btn.classList.remove("is-active");
            });

            button.setAttribute("aria-pressed", "true");
            button.classList.add("is-active");

            fetchCategoryPosts(category);
        });
    });
};

// Rotating sentences in footer
const initRotatingSentence = () => {
    const container = document.querySelector(".rotating-sentence");
    if (!container) return;

    const dynamicSpan = container.querySelector(".rotating-sentence__dynamic");
    if (!dynamicSpan) return;

    let phrases;
    try {
        phrases = JSON.parse(container.dataset.phrases);
    } catch (err) {
        console.error("Invalid phrases JSON:", err);
        return;
    }

    if (!Array.isArray(phrases) || phrases.length < 2) return;

    let index = 0;

    // Show first phrase immediately
    dynamicSpan.textContent = phrases[index];
    dynamicSpan.classList.add("visible");
    index = 1;

    const rotateText = () => {
        dynamicSpan.classList.remove("visible");

        setTimeout(() => {
            dynamicSpan.textContent = phrases[index];
            dynamicSpan.classList.add("visible");
            index = (index + 1) % phrases.length;
        }, 250);
    };

    setInterval(rotateText, 5000);
};

// Audio player
const initAudioPlayer = () => {
    const waitForPlayer = setInterval(() => {
        const audioPlayer = document.querySelector("#player");

        if (audioPlayer && typeof Vlitejs !== "undefined") {
            console.log("🎧 Initializing vLite…");

            new Vlitejs("#player", {
                controls: [
                    "play",
                    "current-time",
                    "progress",
                    "duration",
                    "volume",
                ],
                volume: true,
                autoplay: false,
            });

            clearInterval(waitForPlayer);
        }
    }, 100); // Check every 100ms
};

// Smooth scroll + focus for the reviews compendium era jump dropdown
const initEraJumpDropdown = () => {
    const sel = document.getElementById("era-jump");
    if (!sel) return;
    sel.addEventListener("change", function () {
        const hash = this.value;
        if (!hash || !hash.startsWith("#")) return;

        // Use native anchor navigation (CSS handles smooth scroll)
        window.location.hash = hash;

        // After the browser scrolls, move focus for accessibility
        requestAnimationFrame(() => {
            const target = document.getElementById(hash.slice(1));
            if (!target) return;
            const heading = target.querySelector("h2, .table-heading");
            const focusEl = heading || target;
            focusEl.setAttribute("tabindex", "-1");
            focusEl.focus({ preventScroll: true });
            setTimeout(() => focusEl.removeAttribute("tabindex"), 150);
        });

        // Reset dropdown back to placeholder
        this.selectedIndex = 0;
    });
};

// Initialize all features
const init = () => {
    initNavigation();
    initBackgroundFade();
    initSearch();
    initPodcastMenu();
    initScrollContainers();
    initExternalLinkIcons();
    initTardisScrollProgress();
    initCategorySwitcher();
    initRotatingSentence();
    initEraJumpDropdown();
    window.addEventListener("DOMContentLoaded", initAudioPlayer);
};

// Start when DOM is ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}
