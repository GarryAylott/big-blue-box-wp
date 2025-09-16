# Copilot Instructions – The Big Blue Box Podcast Theme

These instructions define how code should be written, organized, and maintained for the **Big Blue Box Podcast** WordPress theme.

## 1. Project Overview

-   Hybrid WordPress theme for both **podcast episodes** and **general blog posts**.
-   Uses **standard posts** for all content.
-   Two primary categories:
    -   `podcasts`
    -   `articles`  
        (Subcategories may include `news`, `opinions`, `reviews`.)
-   Tags are used for Doctor Who–specific metadata (Doctor, Era, media type, etc.).
-   ACF is used for (currently at time of this file being generated): author meta, review score fields, closing thoughts, and Captivate API–based episode data.

## 2. Tech Targets

-   WordPress: **6.8.2**
-   PHP: **8.3.17**
-   Node: **20.9.0**
-   Modern browsers: **latest 2 versions**, no polyfills.
-   Editor: Hybrid (classic now, custom Gutenberg blocks later).
-   Theme supports enabled (see `functions.php` for list).
-   Main plugins: **ACF**, **Perfect Images**, **Jetpack** (light use), **Force Regenerate Thumbnails**, **SVG Support**, **Yoast SEO**.

## 3. Repo & Build

-   Source SCSS and JS live in `/src`.
-   Compiled CSS → `style.css` in theme root (required by WP).
-   Compiled JS → `/scripts/bbb-scripts.min.js`.
-   Gulp build:
    -   Sourcemaps for **dev only**.
    -   Minification in **both dev and prod**.
-   Prettier is used for SCSS/JS formatting.

## 4. PHP & Templating Standards

-   Follow **WordPress Coding Standards**.
-   Use small helper functions in `/inc` if needed.
-   ACF JSON sync: store in `/acf-json` (commit to repo).
-   Escape on output using:
    -   `esc_html__()`, `wp_kses_post()`, `sanitize_text_field()`, etc.
-   Wrap user-facing strings in translation functions:
    -   `__()`, `_e()`, `esc_html__()`, `esc_html_e()`.
-   Textdomain: `bigbluebox`.

## 5. CSS / SCSS Conventions

-   **Hybrid** naming approach (BEM + utility where useful).
-   Units:
    -   Use `rem` for most sizes.
    -   `px` allowed where needed.
-   Breakpoints:
    -   Content-led, not fixed (common sizes used as needed).
    -   Mobile-first queries preferred.
-   Maintain current file structure:
    -   `global/` for site-wide styles.
    -   `blocks/` for reusable block components.
    -   `utilities/` for mixins, helpers, utilities.
    -   `vendors/` for third-party styles.
-   `style.scss` is single entry point.
-   No prefixes required for classes.
-   Dark mode: not implemented yet.

## 6. JavaScript Conventions

-   **Vanilla JS** only (no jQuery).
-   Single bundle: `/scripts/bbb-scripts.min.js`.
-   Use ES modules where possible.
-   Target latest 2 versions of browsers.
-   Use `data-*` attributes for behavior hooks.
-   API calls:
    -   Captivate API logic in `/inc/acf-fields.php` and `/inc/captivate-external-audio.php`.
    -   Logging as implemented in those files.

## 7. Accessibility

-   **Top priority**:
    -   Correct ARIA usage.
    -   Keyboard navigability.
    -   Focus states visible.
    -   Proper heading hierarchy.
    -   Color contrast compliance.
    -   Captions/transcripts for media.
-   Escape/sanitize all dynamic output.

## 8. Performance

-   Images:
    -   Use AVIF/WebP where possible, with fallbacks.
    -   WP core lazy-loading enabled.
-   Fonts:
    -   `font-display: swap` for all.
    -   Preload critical fonts if not already.
-   Third-party scripts: avoid unless essential.
-   Keep CSS/JS bundles lean.

## 9. SEO

-   Yoast handles sitemaps & robots.txt.
-   Implement:
    -   Proper titles/meta.
    -   Schema for Podcast, Article, Breadcrumbs.
    -   Canonical tags.
    -   Open Graph & Twitter Cards.

---

**Summary:**  
Copilot should generate code following the above structure, using the provided directories, coding standards, and performance/accessibility requirements. All output must adhere to WordPress best practices and integrate cleanly into the existing theme architecture.
