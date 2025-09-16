# AGENTS.md – Big Blue Box Podcast Theme

## Project Overview

-   Hybrid WordPress theme for both **podcast episodes** and **general blog posts**
-   Uses standard posts for all content
-   Primary categories: `podcasts`, `articles` (with subcategories like `news`, `opinions`, `reviews`)
-   Tags for Doctor Who-specific metadata (Doctor, Era, media type, etc.)
-   ACF fields used for author meta, review score, closing thoughts, Captivate episode data

## Tech Stack & Targets

-   WordPress version 6.8.2
-   PHP 8.3.17
-   Node 20.9.0
-   Support modern browsers: latest 2 versions
-   Currently using classic editor, with custom Gutenberg blocks planned

## Repo Structure & Build Commands

-   Source files: `/src` for SCSS & JS
-   Compiled CSS → `style.css` in theme root
-   Compiled JS → `/scripts/bbb-scripts.min.js`
-   Gulp workflow: sourcemaps enabled in dev only, minification in both dev & prod
-   Prettier is used for formatting SCSS & JS

## Coding & Templating Standards

-   Follow WordPress Coding Standards
-   Use helper functions in `/inc` directory where appropriate
-   ACF JSON sync stored in `/acf-json` and committed to repo
-   Escape output appropriately (e.g. `esc_html__()`, `wp_kses_post()`, `sanitize_text_field()`)
-   Wrap user-facing strings in translation functions (e.g. `__()`, `_e()`, `esc_html__()`, `esc_html_e()`)
-   Textdomain: `bigbluebox`

## CSS / SCSS Conventions

-   Hybrid naming (BEM + utilities) where appropriate
-   Use `rem` for most sizes; `px` where necessary
-   Mobile-first breakpoints; content-led, not fixed only sizes
-   Directory organization:
    -   `global/` for site-wide styles
    -   `blocks/` for reusable block components
    -   `utilities/` for helpers, mixins, etc.
    -   `vendors/` for third-party styles
-   Single entry point: `style.scss`
-   No class name prefixes needed by default

## JavaScript Conventions

-   Vanilla JS only; avoid jQuery
-   Use ES modules whenever possible
-   Single bundle: `/scripts/bbb-scripts.min.js`
-   Behavior hooks via `data-*` attributes
-   Captivate API logic in `/inc/acf-fields.php` & `/inc/captivate-external-audio.php`

## Accessibility, Performance & SEO

-   Accessibility is top priority: proper ARIA, keyboard navigation, focus states, heading hierarchy, color contrast, captions/transcripts for media
-   Sanitize / escape all dynamic output
-   Performance: use AVIF/WebP images, lazy-load images, preload critical fonts, minimize CSS/JS bundle size, avoid unnecessary third-party scripts
-   SEO: correct title/meta, schema (Podcast, Article, Breadcrumbs), Open Graph & Twitter Cards, canonical tags

## Commit & PR Guidelines

-   Follow Conventional Commits
-   Pull Requests should reference issue (if any), have descriptive title
-   Ensure build passes, formatting/linting tools are applied

---

**Rule priority**: Mandatory sections above are required. Smaller, preference rules are suggested unless otherwise stated.
