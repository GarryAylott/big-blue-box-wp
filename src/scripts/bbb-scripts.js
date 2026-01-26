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
    Reply,
    Clock,
    Search,
    X,
    Tag,
    FileText,
    Info,
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
    Reply,
    Clock,
    Search,
    X,
    Tag,
    FileText,
    Info,
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
        nav: ".nav-drawer",
        navToggle: ".menu-nav-toggle",
        bgImage: ".hero-bg-image",
        featuredImage: ".post-thumb-img",
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

    const getOverlayBase = () => {
        const base = Number.parseFloat(
            backgroundImage.dataset.heroOverlayBase || "",
        );
        return Number.isFinite(base) ? base : null;
    };

    let ticking = false;
    let fadeDistance = CONFIG.FADE_DISTANCE;

    const getFadeDistance = () => {
        const maxScroll = Math.max(
            document.documentElement.scrollHeight - window.innerHeight,
            0,
        );
        return maxScroll > 0 ? Math.min(CONFIG.FADE_DISTANCE, maxScroll) : 1;
    };

    const updateFadeDistance = () => {
        fadeDistance = getFadeDistance();
        if (fadeDistance <= 1) {
            backgroundImage.style.opacity = "0";
            const overlayBase = getOverlayBase();
            if (overlayBase !== null) {
                backgroundImage.style.setProperty(
                    "--hero-overlay-opacity",
                    "0",
                );
            }
        }
    };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                updateFadeDistance();
                window.addEventListener("resize", updateFadeDistance, {
                    passive: true,
                });
                const scrollHandler = () => {
                    if (!ticking) {
                        requestAnimationFrame(() => {
                            const opacity = Math.max(
                                1 - window.scrollY / fadeDistance,
                                0,
                            );
                            backgroundImage.style.opacity = opacity;
                            const overlayBase = getOverlayBase();
                            if (overlayBase !== null) {
                                backgroundImage.style.setProperty(
                                    "--hero-overlay-opacity",
                                    `${overlayBase * opacity}`,
                                );
                            }
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

// Dynamic hero background image colour overlay
const initHeroOverlayFromFeaturedImage = () => {
    if (!document.body.classList.contains("single-post")) return;

    const backgroundImage = document.querySelector(CONFIG.SELECTORS.bgImage);
    const featuredImage = document.querySelector(
        CONFIG.SELECTORS.featuredImage,
    );

    if (!backgroundImage || !featuredImage) return;

    const sampleWidth = 12;
    const sampleHeight = 4;
    const overlayOpacity = 0.6;
    const monochromeThreshold = 16;

    const applyOverlay = () => {
        try {
            if (!featuredImage.naturalWidth || !featuredImage.naturalHeight) {
                return;
            }

            const canvas = document.createElement("canvas");
            canvas.width = sampleWidth;
            canvas.height = sampleHeight;

            const ctx = canvas.getContext("2d", { willReadFrequently: true });
            if (!ctx) return;

            ctx.drawImage(featuredImage, 0, 0, sampleWidth, sampleHeight);
            const { data } = ctx.getImageData(0, 0, sampleWidth, sampleHeight);

            const cols = new Array(sampleWidth)
                .fill(null)
                .map(() => ({ r: 0, g: 0, b: 0, count: 0 }));
            let chromaTotal = 0;
            let chromaCount = 0;

            for (let y = 0; y < sampleHeight; y += 1) {
                for (let x = 0; x < sampleWidth; x += 1) {
                    const idx = (y * sampleWidth + x) * 4;
                    const alpha = data[idx + 3];
                    if (alpha < 16) continue;
                    const bucket = cols[x];
                    bucket.r += data[idx];
                    bucket.g += data[idx + 1];
                    bucket.b += data[idx + 2];
                    bucket.count += 1;
                    const r = data[idx];
                    const g = data[idx + 1];
                    const b = data[idx + 2];
                    const max = Math.max(r, g, b);
                    const min = Math.min(r, g, b);
                    chromaTotal += max - min;
                    chromaCount += 1;
                }
            }

            if (
                chromaCount &&
                chromaTotal / chromaCount < monochromeThreshold
            ) {
                backgroundImage.style.setProperty(
                    "--hero-overlay-opacity",
                    "0",
                );
                backgroundImage.style.setProperty(
                    "--hero-overlay-gradient",
                    "none",
                );
                return;
            }

            const stops = [];
            cols.forEach((col, index) => {
                if (!col.count) return;
                const avgR = Math.round(col.r / col.count);
                const avgG = Math.round(col.g / col.count);
                const avgB = Math.round(col.b / col.count);
                const pct =
                    sampleWidth === 1
                        ? 0
                        : Math.round((index / (sampleWidth - 1)) * 100);
                stops.push(`rgb(${avgR}, ${avgG}, ${avgB}) ${pct}%`);
            });

            if (!stops.length) return;

            const mid = cols[Math.floor(sampleWidth / 2)];
            const fallbackR = Math.round(mid.r / Math.max(mid.count, 1));
            const fallbackG = Math.round(mid.g / Math.max(mid.count, 1));
            const fallbackB = Math.round(mid.b / Math.max(mid.count, 1));

            backgroundImage.style.setProperty(
                "--hero-overlay-color",
                `${fallbackR}, ${fallbackG}, ${fallbackB}`,
            );
            backgroundImage.style.setProperty(
                "--hero-overlay-gradient",
                `linear-gradient(90deg, ${stops.join(", ")})`,
            );
            backgroundImage.style.setProperty(
                "--hero-overlay-opacity",
                `${overlayOpacity}`,
            );
            backgroundImage.dataset.heroOverlayBase = `${overlayOpacity}`;
            const currentOpacity = Number.parseFloat(
                window.getComputedStyle(backgroundImage).opacity,
            );
            if (Number.isFinite(currentOpacity)) {
                backgroundImage.style.setProperty(
                    "--hero-overlay-opacity",
                    `${overlayOpacity * currentOpacity}`,
                );
            }
        } catch (err) {
            // Fail silently: no overlay if canvas access fails.
        }
    };

    const schedule = () => {
        if ("requestIdleCallback" in window) {
            window.requestIdleCallback(applyOverlay, { timeout: 800 });
        } else {
            window.setTimeout(applyOverlay, 0);
        }
    };

    if (featuredImage.complete && featuredImage.naturalWidth) {
        schedule();
        return;
    }

    if (typeof featuredImage.decode === "function") {
        featuredImage
            .decode()
            .then(schedule)
            .catch(() => {
                featuredImage.addEventListener("load", schedule, {
                    once: true,
                });
            });
        return;
    }

    featuredImage.addEventListener("load", schedule, { once: true });
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
        CONFIG.SELECTORS.searchOverlay,
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
        CONFIG.SELECTORS.podcastMenu,
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
            'a[target="_blank"]:not(.has-external-icon)',
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
        CONFIG.SELECTORS.scrollContainer,
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
            scrollY < scrollDistance + viewportHeight + -650
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
        CONFIG.SELECTORS.categorySwitcher,
    );
    const postContainer = document.getElementById("ajax-posts-container");
    const statusRegion = document.getElementById("ajax-posts-status");
    const paginationContainer = document.getElementById(
        "ajax-posts-pagination",
    );
    const switcher = switchButtons.length
        ? switchButtons[0].closest(".view-switcher")
        : null;
    if (!switchButtons.length || !postContainer) return;

    let requestCounter = 0;
    let skipAnimationNext = false;
    let activeCategory =
        switcher?.querySelector(".switch-btn.is-active")?.dataset.category ||
        switchButtons[0]?.dataset.category ||
        "all";
    const context = switcher?.dataset?.context || "home";

    // Response cache: Map of "category-paged" -> { content, pagination, timestamp }
    const responseCache = new Map();
    const CACHE_TTL = 5 * 60 * 1000; // 5 minutes
    const ANIMATION_DURATION = 150; // Reduced from 260ms

    const getCacheKey = (category, paged) => `${category}-${paged}`;

    const getCachedResponse = (category, paged) => {
        const key = getCacheKey(category, paged);
        const cached = responseCache.get(key);
        if (!cached) return null;
        if (Date.now() - cached.timestamp > CACHE_TTL) {
            responseCache.delete(key);
            return null;
        }
        return cached;
    };

    const setCachedResponse = (category, paged, content, pagination) => {
        const key = getCacheKey(category, paged);
        responseCache.set(key, { content, pagination, timestamp: Date.now() });
    };

    // Prefetch in-flight tracker to avoid duplicate requests
    const prefetchInFlight = new Set();

    const setButtonsDisabled = (disabled) => {
        switchButtons.forEach((btn) => {
            btn.disabled = disabled;
        });
    };

    const beginLoading = () => {
        postContainer.setAttribute("aria-busy", "true");
        if (statusRegion) {
            statusRegion.textContent = "Loading posts";
        }
    };

    const endLoading = () => {
        postContainer.removeAttribute("aria-busy");
        if (statusRegion) {
            statusRegion.textContent = "Posts updated";
        }
    };

    const getPagedFromUrl = (url) => {
        try {
            const parsedUrl = new URL(url, window.location.origin);
            const pagedParam = parsedUrl.searchParams.get("paged");
            if (pagedParam) {
                return Number.parseInt(pagedParam, 10);
            }
            const match = parsedUrl.pathname.match(/\/page\/(\d+)\//);
            return match ? Number.parseInt(match[1], 10) : 1;
        } catch (err) {
            return 1;
        }
    };

    // Core fetch logic extracted for reuse
    const doFetch = (category, paged) => {
        const params = new URLSearchParams({
            action: "filter_posts_by_category",
            category,
            context,
            nonce: themeSettings.filterNonce ?? "",
            paged,
        });

        return fetch(themeSettings.ajaxUrl, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params,
        }).then((res) => res.json());
    };

    // Prefetch a category (called on hover)
    const prefetchCategory = (category, paged = 1) => {
        // Skip if already cached or in-flight
        if (getCachedResponse(category, paged)) return;
        const key = getCacheKey(category, paged);
        if (prefetchInFlight.has(key)) return;

        prefetchInFlight.add(key);

        doFetch(category, paged)
            .then((payload) => {
                if (payload?.success) {
                    setCachedResponse(
                        category,
                        paged,
                        payload.data?.content ?? "",
                        payload.data?.pagination ?? "",
                    );
                }
            })
            .catch(() => {
                // Silently fail prefetch
            })
            .finally(() => {
                prefetchInFlight.delete(key);
            });
    };

    const renderContent = (html, pagination) => {
        postContainer.innerHTML = html;
        if (paginationContainer) {
            paginationContainer.innerHTML = pagination;
        }

        // Setup initial hidden state for children, then activate transition
        postContainer.classList.remove("enter-setup", "enter-active");
        if (!skipAnimationNext) {
            postContainer.classList.add("enter-setup");
            // Force reflow to commit styles
            void postContainer.offsetHeight;
            postContainer.classList.remove("enter-setup");
            postContainer.classList.add("enter-active");

            // Clean up after animation window
            setTimeout(() => {
                postContainer.classList.remove("enter-active");
            }, ANIMATION_DURATION);
        }
        skipAnimationNext = false;
    };

    const fetchCategoryPosts = (category, paged = 1) => {
        const reqId = ++requestCounter;

        // Check cache first for instant response
        const cached = getCachedResponse(category, paged);
        if (cached) {
            renderContent(cached.content, cached.pagination);
            endLoading();
            setButtonsDisabled(false);
            return;
        }

        // Disable controls and mark busy (no pre-fade-out)
        setButtonsDisabled(true);
        beginLoading();

        doFetch(category, paged)
            .then((payload) => {
                // Ignore stale responses from earlier clicks
                if (reqId !== requestCounter) return;

                if (!payload?.success) {
                    throw new Error(payload?.data || "Request failed");
                }

                const html = payload.data?.content ?? "";
                const pagination = payload.data?.pagination ?? "";

                // Cache the response
                setCachedResponse(category, paged, html, pagination);

                renderContent(html, pagination);
                endLoading();
                setButtonsDisabled(false);
            })
            .catch((err) => {
                console.error("Category switch AJAX error:", err);
                postContainer.innerHTML =
                    '<p class="ajax-error">' +
                    (err?.message || "Unable to load posts.") +
                    "</p>";
                if (statusRegion) {
                    statusRegion.textContent =
                        err?.message || "Unable to load posts.";
                }
                endLoading();
                setButtonsDisabled(false);
            });
    };

    switchButtons.forEach((button) => {
        const category = button.dataset.category;

        // Prefetch on hover (mouseenter) or focus
        button.addEventListener("mouseenter", () => {
            if (category !== activeCategory) {
                prefetchCategory(category, 1);
            }
        });
        button.addEventListener("focus", () => {
            if (category !== activeCategory) {
                prefetchCategory(category, 1);
            }
        });

        button.addEventListener("click", (e) => {
            e.preventDefault();

            switchButtons.forEach((btn) => {
                btn.setAttribute("aria-pressed", "false");
                btn.classList.remove("is-active");
            });

            button.setAttribute("aria-pressed", "true");
            button.classList.add("is-active");

            activeCategory = category;
            fetchCategoryPosts(category, 1);
        });
    });

    if (paginationContainer && context !== "category") {
        paginationContainer.addEventListener("click", (event) => {
            const link = event.target.closest("a");
            if (!link) return;

            event.preventDefault();
            const paged = getPagedFromUrl(link.href);
            document.documentElement.style.scrollBehavior = "auto";
            document.body.style.scrollBehavior = "auto";
            document.documentElement.scrollTop = 0;
            document.body.scrollTop = 0;
            window.scrollTo(0, 0);
            requestAnimationFrame(() => {
                document.documentElement.style.scrollBehavior = "";
                document.body.style.scrollBehavior = "";
            });
            skipAnimationNext = true;
            document.body.classList.add("nav-no-fade");
            fetchCategoryPosts(activeCategory, paged);
        });
    }
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

    setInterval(rotateText, 6000);
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
        "[data-compendium-rotation][data-image-dir]",
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

// Podcast transcript animation fix for Chrome
const initTranscriptAnimation = () => {
    document
        .querySelectorAll(".podcast-transcript__accordion")
        .forEach((details) => {
            details.addEventListener("toggle", () => {
                if (details.open) {
                    const content = details.querySelector(
                        ".podcast-transcript__content",
                    );
                    if (content) {
                        content.style.animation = "none";
                        content.offsetHeight; // Force reflow
                        content.style.animation = "";
                    }
                }
            });
        });
};

// Logos scrolling marquee without duped markup
const initLogosMarquee = () => {
    const scrollers = document.querySelectorAll(".brand-logos-marquee");

    if (!window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
        addAnimation();
    }

    function addAnimation() {
        scrollers.forEach((scroller) => {
            scroller.setAttribute("data-animated", true);

            const scrollerGroup = scroller.querySelector(
                ".brand-logos-marquee__group",
            );
            if (!scrollerGroup) return;

            const duplicatedGroup = scrollerGroup.cloneNode(true);
            duplicatedGroup.setAttribute("aria-hidden", true);
            scroller.appendChild(duplicatedGroup);
        });
    }
};

// Initialize all features
const init = () => {
    initNavigation();
    initBackgroundFade();
    initHeroOverlayFromFeaturedImage();
    initSearch();
    initPodcastMenu();
    initScrollContainers();
    initExternalLinkIcons();
    initTardisScrollProgress();
    initCategorySwitcher();
    initRotatingSentence();
    initEraJumpDropdown();
    initCompendiumLinkImages();
    initLogosMarquee();
    initTranscriptAnimation();
    window.addEventListener("DOMContentLoaded", initAudioPlayer);
};

// Start when DOM is ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}
