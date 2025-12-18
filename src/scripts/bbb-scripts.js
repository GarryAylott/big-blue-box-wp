import Vlitejs from "vlitejs";
import { createIcons } from "lucide";
import {
    Mic,
    Headphones,
    ArrowUp,
    ArrowDown,
    ArrowLeft,
    ArrowRight,
    ArrowUpRight,
    ChevronDown,
    Newspaper,
    Rss,
    LayoutList,
    Reply,
    Clock,
    Search,
    X,
    Tag,
} from "lucide";

// Only register icons we use so the bundle stays lean.
const icons = {
    Mic,
    Headphones,
    ArrowUp,
    ArrowDown,
    ArrowLeft,
    ArrowRight,
    ArrowUpRight,
    ChevronDown,
    Newspaper,
    Rss,
    LayoutList,
    Reply,
    Clock,
    Search,
    X,
    Tag,
};
createIcons({ icons });

class EpisodeMetaPlugin {
    constructor({ player }) {
        this.player = player;
        this.providers = ["html5"];
        this.types = ["audio"];
        this.metaElement = null;
    }

    init() {
        const { media, elements } = this.player;
        if (!media || !elements?.controlBar) return;

        const number = media.dataset?.episodeNumber?.trim();
        const title = media.dataset?.episodeTitle?.trim();

        if (!number && !title) return;

        this.metaElement = document.createElement("div");
        this.metaElement.className = "v-player-meta";

        if (number) {
            const numberEl = document.createElement("span");
            numberEl.className = "v-player-meta__number";
            numberEl.textContent = number;
            this.metaElement.appendChild(numberEl);
        }

        if (title) {
            const titleEl = document.createElement("span");
            titleEl.className = "v-player-meta__title";
            titleEl.textContent = title;
            this.metaElement.appendChild(titleEl);
        }

        elements.controlBar
            .closest(".v-container")
            ?.insertBefore(this.metaElement, elements.controlBar);
    }

    destroy() {
        this.metaElement?.remove();
    }
}

Vlitejs.registerPlugin("episodeMeta", EpisodeMetaPlugin);

// Configuration object
const CONFIG = {
    FADE_DISTANCE: 900,
    SCROLL_ANIMATION_DELAY: 100,
    SELECTORS: {
        nav: ".nav",
        navToggle: ".menu-nav-toggle",
        bgImage: ".hero-bg-image",
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

// Search shortcut platform detection
(() => {
    const isMac = /Mac|iPhone|iPad/.test(navigator.platform);
    document.documentElement.dataset.os = isMac ? "mac" : "other";
})();

// Search functionality
const initSearch = () => {
    const searchIcon = document.querySelector(CONFIG.SELECTORS.searchIcon);
    const searchOverlay = document.querySelector(
        CONFIG.SELECTORS.searchOverlay
    );

    if (!searchIcon || !searchOverlay) return;

    const getOverlaySearchField = () =>
        searchOverlay.querySelector(".search-field");

    const closeOverlay = () => {
        const searchField = getOverlaySearchField();

        if (searchField && document.activeElement === searchField) {
            searchField.blur(); // Prevent aria-hidden focus warning
        }

        searchOverlay.setAttribute("aria-hidden", "true");
        document.body.classList.remove("overlay-active");
    };

    const openOverlay = () => {
        if (searchOverlay.getAttribute("aria-hidden") === "false") return;

        searchOverlay.setAttribute("aria-hidden", "false");
        document.body.classList.add("overlay-active");

        const searchField = getOverlaySearchField();
        if (searchField) {
            requestAnimationFrame(() => {
                searchField.focus();
            });
        }
    };

    // Click trigger (existing behaviour)
    searchIcon.addEventListener("click", (e) => {
        e.preventDefault();
        openOverlay();
    });

    // Click outside to close
    searchOverlay.addEventListener("click", ({ target }) => {
        if (target === searchOverlay) {
            closeOverlay();
        }
    });

    // Global keyboard handling
    document.addEventListener("keydown", (event) => {
        const { key, metaKey, ctrlKey } = event;

        // ESC closes when open
        if (
            key === "Escape" &&
            searchOverlay.getAttribute("aria-hidden") === "false"
        ) {
            closeOverlay();
            return;
        }

        // Cmd+K (macOS) or Ctrl+K (Windows/Linux)
        const isK = key.toLowerCase() === "k";
        const hasModifier = metaKey || ctrlKey;

        if (!isK || !hasModifier) return;

        // Don’t hijack typing contexts
        const activeEl = document.activeElement;
        if (
            activeEl &&
            (activeEl.tagName === "INPUT" ||
                activeEl.tagName === "TEXTAREA" ||
                activeEl.isContentEditable)
        ) {
            return;
        }

        event.preventDefault(); // Prevent browser find
        openOverlay();
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

// Add external link icons
const initExternalLinkIcons = () => {
    const selectors = [];

    if (document.body.classList.contains("single-post")) {
        selectors.push(".post-content");
    }

    if (document.body.classList.contains("page")) {
        selectors.push(".site-main");
    }

    if (!selectors.length) {
        return;
    }

    const contentAreas = document.querySelectorAll(selectors.join(","));
    if (!contentAreas.length) {
        return;
    }

    const themeUrl = themeSettings.themeUrl || "";

    contentAreas.forEach((contentArea) => {
        const links = contentArea.querySelectorAll(
            'a[target="_blank"]:not(.has-external-icon)'
        );

        links.forEach((link) => {
            if (themeUrl && link.href.startsWith(themeUrl)) {
                return;
            }

            const icon = document.createElement("span");
            icon.innerHTML = `<i data-lucide="arrow-up-right" class="icon-step-1"></i>`;

            link.appendChild(icon);
            createIcons({ icons, root: icon });
            link.classList.add("has-external-icon");
        });
    });
};

// TARDIS progress image on scroll
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

// Tardis icon scroll progress
const initTardisScrollProgress = () => {
    const container = document.querySelector(".tardis-progress-container");
    const fillRect = document.getElementById("tardis-fill");
    const statusText = document.getElementById("tardisProgressStatus");
    const featuredImage = document.querySelector(".post-featured-image");
    const article = document.querySelector(".post-content");
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
        const params = new URLSearchParams({
            action: "filter_posts_by_category",
            category,
            context: "home",
            nonce: themeSettings.filterNonce ?? "",
        });

        fetch(themeSettings.ajaxUrl, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params,
        })
            .then((res) => res.json())
            .then((payload) => {
                // Ignore stale responses from earlier clicks
                if (reqId !== requestCounter) return;

                if (!payload?.success) {
                    throw new Error(payload?.data || "Request failed");
                }

                const html = payload.data?.content ?? "";

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
                postContainer.innerHTML =
                    '<p class="ajax-error">' +
                    (err?.message || "Unable to load posts.") +
                    "</p>";
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
                plugins: ["episodeMeta"],
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

// Review compendium rotating artwork
const initCompendiumLinkImages = () => {
    const container = document.querySelector(
        "[data-compendium-rotation][data-image-dir]"
    );
    if (!container) return;

    let images;
    try {
        images = JSON.parse(container.dataset.compendiumRotation || "[]");
    } catch (err) {
        console.error("Invalid compendium rotation data:", err);
        return;
    }
    if (!Array.isArray(images) || images.length < 2) return;

    const dir = (container.dataset.imageDir || "").replace(/\/$/, "");
    if (!dir) return;
    const imageAlt = container.dataset.imageAlt || "";

    let active = container.querySelector(".review-compendium-link__image");
    if (!active) return;

    let clone = active.cloneNode(true);
    clone.classList.remove("is-active");
    container.appendChild(clone);

    const updateSources = (picture, imageName) => {
        const base = `${dir}/${imageName}`;
        picture
            .querySelector('source[type="image/avif"]')
            ?.setAttribute("srcset", `${base}.avif`);
        picture
            .querySelector('source[type="image/webp"]')
            ?.setAttribute("srcset", `${base}.webp`);
        const img = picture.querySelector("img");
        if (img) {
            img.src = `${base}.webp`;
            img.alt = imageAlt;
        }
        return img;
    };

    let index = Number.parseInt(container.dataset.initialIndex, 10);
    if (Number.isNaN(index) || index < 0 || index >= images.length) {
        index = 0;
    }

    const DISPLAY_DURATION = 6000;
    let idleTimer = null;

    const swapImages = () => {
        const nextIndex = (index + 1) % images.length;
        const nextImg = updateSources(clone, images[nextIndex]);
        if (!nextImg) return;

        const showNext = () => {
            clone.classList.add("is-active");
            active.classList.remove("is-active");
            [active, clone] = [clone, active];
            index = nextIndex;
            idleTimer = setTimeout(swapImages, DISPLAY_DURATION);
        };

        if (nextImg.complete && nextImg.naturalWidth) {
            showNext();
        } else {
            const once = () => {
                nextImg.removeEventListener("load", once);
                nextImg.removeEventListener("error", once);
                showNext();
            };
            nextImg.addEventListener("load", once);
            nextImg.addEventListener("error", once);
        }
    };

    idleTimer = setTimeout(swapImages, DISPLAY_DURATION);
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
    initCompendiumLinkImages();
    window.addEventListener("DOMContentLoaded", initAudioPlayer);
};

// Start when DOM is ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}
