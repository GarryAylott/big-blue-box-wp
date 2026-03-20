# Big Blue Box Podcast — WordPress Theme

Custom WordPress theme for The Big Blue Box Podcast (Doctor Who). Hybrid site handling **podcast episodes** and **blog articles** using standard WP posts differentiated by category.

## Tech Stack

-   WordPress 6.8.2, PHP 8.3.17, Node 20.9.0
-   Build: Gulp 5 (SCSS + Rollup JS) and Webpack via `@wordpress/scripts` (Gutenberg blocks)
-   JS: Vanilla ES6+ only (no jQuery), ESM modules, single bundle
-   CSS: SCSS with `@use`/`@forward`, CSS custom properties, mobile-first fluid design
-   Libraries: Vlitejs (audio player), Lucide (icons — 18 tree-shaken imports)
-   Plugins: ACF, Yoast SEO, Jetpack, SVG Support
-   Text domain: `bigbluebox`

## Build Commands

```bash
npm run dev              # Gulp dev server + BrowserSync (proxies https://big-blue-box.local)
npm run build            # Production build (NODE_ENV=production, minified, no sourcemaps)
npm run blocks:build     # Build Gutenberg blocks (webpack)
npm run blocks:dev       # Block dev with watch mode
npm run build:compendium # Build reviews JSON data store
```

`style.css`, `editor.css` at root are **compiled output** (gitignored). Source lives in `src/scss/`.
`gulpfile.js` is also gitignored — deployed via GitHub Action.

## Project Structure

```
Root templates:     header.php, footer.php, functions.php, index.php, single.php,
                    archive.php, category.php, search.php, page.php, 404.php, etc.
/inc/               PHP helpers (23 files) — auto-loaded by functions.php via glob
/template-parts/    Reusable PHP components (21 files) via get_template_part()
/page-templates/    Custom page templates: about, reviews-compendium, team-openings, legal
/src/scss/          SCSS source (entry points: style.scss, editor.scss)
  global/           Reset, typography, header, footer, buttons, forms, variables, colours
  blocks/           Component styles (~32 files: post cards, audio player, etc.)
  utilities/        Helpers: flow, wrapper, region, flex, icons, images
  vendors/          Third-party overrides (Vlitejs)
  pages/            Page-specific styles
  wp-blocks/        WP core block overrides
/src/scripts/       JS source (bbb-scripts.js ~1164 lines)
/src/blocks/        Gutenberg block sources: info-block.js, thoughts-from-team.js
/inc/blocks/        Compiled block output (webpack)
/scripts/           Compiled JS output (bbb-scripts.min.js)
/data/              JSON data stores (reviews-compendium.json)
/acf-json/          ACF field group sync (committed to repo)
/images/            Static images (AVIF/WebP preferred)
/fonts/             Self-hosted WOFF2 fonts (Bitter body, Pmath headings)
/favicons/          Favicon and PWA manifest assets
```

## Content Architecture

-   **All content uses standard WP posts** — no custom post types
-   Two primary categories: `podcasts` and `articles` (subcategories: `news`, `opinions`, `reviews`, `features`)
-   **Gotcha**: The `articles` archive excludes podcasts via `tax_query` with `NOT IN` — see `functions.php` `pre_get_posts`
-   Tags for Doctor Who metadata: Doctor incarnation, era, story name, media type
-   Podcast detection: `has_category('podcasts')` or `in_category('podcasts')`
-   ACF fields store: author meta, review scores, closing thoughts, Captivate episode data

## PHP Conventions

-   WordPress Coding Standards. **Tabs** for indentation.
-   Function prefix: `bbb_` (e.g. `bbb_get_icon()`, `bbb_estimated_reading_time()`, `bbb_log()`)
-   All `/inc/*.php` auto-loaded via glob in `functions.php` — no manual requires needed
-   Escaping: `esc_html__()`, `wp_kses_post()`, `sanitize_text_field()`, `sanitize_key()` on all output
-   Translation: `__()`, `_e()`, `esc_html__()` with text domain `bigbluebox`
-   Security: `wp_verify_nonce()`, `check_ajax_referer()` on all form/AJAX handlers
-   AJAX: `wp_ajax_` / `wp_ajax_nopriv_` hooks → `wp_send_json_success()` / `wp_send_json_error()`
-   Debug: `bbb_log()` wraps `error_log()`, only fires when `WP_DEBUG` is true

## SCSS Conventions

-   Entry: `src/scss/style.scss` → compiled to root `style.css`
-   Module system: `@use`/`@forward` only (no `@import`)
-   Colors: HSL via CSS custom properties (`--clr-text-400`, `--clr-brand-primary`, etc.)
-   Variable definitions: `src/scss/global/_variables.scss`, `src/scss/global/_colour-tokens.scss`
-   Sizing: fluid `clamp()` via `--step-{n}` (typography) and `--space-{size}` (spacing)
-   Breakpoints: mobile-first — `@media (min-width: 37.5rem)` and `@media (min-width: 55rem)`
-   Naming: BEM-like for components (`.post-card__title`), utility classes (`.flow`, `.wrapper`, `.region`)
-   Units: `rem` preferred, `px` only where specifically needed

## JavaScript Conventions

-   Single source: `src/scripts/bbb-scripts.js` → bundled via Rollup to `scripts/bbb-scripts.min.js`
-   Loaded as ESM (`type="module"`)
-   Vanilla ES6+: arrow functions, `const`/`let`, template literals, destructuring, optional chaining
-   DOM APIs: `IntersectionObserver`, `ResizeObserver`, `fetch`, `matchMedia`
-   Behavior hooks: `data-*` attributes (not class names) for JS targeting
-   Global: `themeSettings` object via `wp_localize_script()` — contains `themeUrl`, `ajaxUrl`, `filterNonce`
-   No jQuery — it is deregistered on the frontend

## Gutenberg Blocks

Two custom blocks registered under category `bbb-blocks`:

| Block              | Source                             | Output                           | Description                                   |
| ------------------ | ---------------------------------- | -------------------------------- | --------------------------------------------- |
| info-block         | `src/blocks/info-block.js`         | `inc/blocks/info-block/`         | Simple callout with Lucide info icon          |
| thoughts-from-team | `src/blocks/thoughts-from-team.js` | `inc/blocks/thoughts-from-team/` | Team member testimonials (uses REST endpoint) |

-   Registration: `inc/register-blocks.php` auto-discovers `block.json` in `inc/blocks/*/`
-   Webpack config: `webpack.config.cjs` (extends `@wordpress/scripts` default)
-   Custom REST endpoint: `bbb/v1/team-members` (GET, requires `edit_posts` capability)

## Captivate API Integration

Podcast episodes are hosted on Captivate.fm. The theme integrates via their REST API.

### Key Files

| File                                   | Purpose                                                                                     |
| -------------------------------------- | ------------------------------------------------------------------------------------------- |
| `inc/acf-fields.php`                   | Populates episode selector dropdown, auto-saves audio URL + metadata on post save           |
| `inc/captivate-external-audio.php`     | `bbb_get_captivate_audio_url($guid)` — auth, fetch, cache (24h transients)                  |
| `inc/sync-captivate-episodes.php`      | Batch sync admin tool (dry-run + apply modes, fuzzy title matching)                         |
| `inc/sync-captivate-transcripts.php`   | Batch transcript sync                                                                       |
| `inc/captivate-transcript-metabox.php` | Per-post "Fetch Transcript" button (AJAX)                                                   |
| `inc/api-shutdown.php`                 | Admin page "Captivate API Tools" — 3 tabs: shutdown toggle, sync episodes, sync transcripts |

### API Auth Flow

1. `POST https://api.captivate.fm/authenticate/token` with `CAPTIVATE_USER_ID` + `CAPTIVATE_API_TOKEN`
2. Bearer token from `response.user.token`
3. `GET https://api.captivate.fm/episodes/{guid}` → `media_id`
4. `GET https://api.captivate.fm/media/{media_id}` → `media_url`

### Required Constants (in wp-config.php, not in theme)

```php
CAPTIVATE_USER_ID
CAPTIVATE_API_TOKEN
CAPTIVATE_SHOW_ID
```

### ACF Fields for Podcast Posts

-   `captivate_episode_selector` — episode GUID (dropdown populated from API)
-   `captivate_audio_url` — resolved audio URL (read-only, auto-populated)
-   `podcast_episode_number` — episode number (read-only, auto-populated)
-   `podcast_episode_type` — e.g. "Standard Weekly Episode" (read-only, auto-populated)
-   `captivate_media_id` — Captivate media ID (read-only)
-   `podcast_transcript` — HTML transcript (read-only, fetched from API)

### Gotchas

-   API calls skipped during RSS feed generation (`is_feed()` check)
-   Frontend audio fetches rate-limited: one call per GUID per 5 minutes
-   Global kill switch: `get_option('disable_captivate_api')` — toggle via admin page
-   With API disabled in local dev, `bbb_get_captivate_audio_url()` returns a hardcoded fallback MP3
-   Several ACF fields set to read-only via `acf/load_field` filter

## Image Handling

-   6 custom sizes in `functions.php`: `post-featured-card` (1200×675), `post-featured-large` (2400×1350), `latest-podcast-ep-thumb` (640×360), `singlepost-wide` (1200×675), `singlepost-square` (1200×9999), `post-list-thumb` (400×225)
-   Default WP sizes (thumbnail, medium, medium_large, large) removed
-   Inline `width`/`height` stripped from content images via DOMDocument filter
-   Format preference: AVIF > WebP > JPEG/PNG fallback
-   Fonts: self-hosted WOFF2 only, `font-display: swap`

## Accessibility

-   ARIA attributes on interactive elements, keyboard navigation, visible focus states
-   Proper heading hierarchy maintained across all templates
-   Color contrast compliance
-   Captions and transcripts for podcast media
-   `visually-hidden` SCSS utility class for screen-reader-only content

## Working Style

-   **Minimal footprint**: Only change what the task requires. Don't refactor surrounding code, rename variables, or reformat files you're editing unless that is the explicit task.
-   **No speculative additions**: Don't add error handling, fallbacks, or validation for scenarios that can't happen in practice. Don't add docstrings or inline comments to code you didn't write or change.
-   **No over-abstraction**: Three similar lines of code is better than a premature helper function. Only create utilities or abstractions when they'll be reused in three or more places.
-   **Read before editing**: Always read a file before proposing changes. Understand the existing pattern first.
-   **Prefer editing over creating**: Add to an existing SCSS file or PHP helper before creating a new one. New files are the exception.
-   **Delete cleanly**: Don't add `// removed` comments, rename to `_unused`, or leave dead code. Remove it.

## Code Placement Rules

**PHP:**

-   New helper functions → a new or existing file in `/inc/`, not in `functions.php`
-   `functions.php` is for theme setup, hooks, and the auto-loader only
-   New page-level template logic → `/page-templates/`
-   Reusable display components → `/template-parts/`

**SCSS:**

-   New component styles → new `_component-name.scss` in `src/scss/blocks/`, added to `src/scss/blocks/_index.scss` via `@forward`
-   Page-specific overrides → `src/scss/pages/`
-   WP block overrides → `src/scss/wp-blocks/`
-   Never write styles directly in PHP template files
-   Never modify compiled `style.css` or `editor.css` directly — they are build output

**JavaScript:**

-   All JS additions go in `src/scripts/bbb-scripts.js`
-   No inline `<script>` tags in templates
-   No separate JS files — everything is bundled via Rollup
-   Exception: Gutenberg block JS goes in `src/blocks/`

**New Gutenberg blocks:**

-   Source in `src/blocks/{block-name}.js` + add entry to `webpack.config.cjs`
-   Register via `block.json` in `inc/blocks/{block-name}/`
-   Render via `inc/blocks/{block-name}/render.php`

## Standards & Don'ts

**Always:**

-   Escape all PHP output: `esc_html__()`, `wp_kses_post()`, `esc_url()`, `esc_attr()`
-   Sanitise all input: `sanitize_text_field()`, `sanitize_key()`, `absint()` as appropriate
-   Prefix all new functions with `bbb_`
-   Use `bbb_log()` for debug output — never `echo` or `var_dump` in committed code
-   Use CSS custom properties from `src/scss/global/_variables.scss` for colours, spacing, and type — don't hardcode values
-   Target JS behaviour with `data-*` attributes, not class names

**Never:**

-   No jQuery — it is deregistered on the frontend
-   No `@import` in SCSS — use `@use`/`@forward` only
-   No inline styles in PHP templates or via JS DOM manipulation
-   No hardcoded URLs — use `get_template_directory_uri()`, `home_url()`, etc.
-   No `console.log()` in committed JS
-   Don't install new npm packages or Composer dependencies without discussion

**Verify after changes:**

-   PHP: run `php -l` on modified files
-   SCSS/JS: run `npm run build` and confirm no errors
-   New interactive elements: check keyboard navigation and focus states

## Deployment

The theme auto-deploys to **20i shared hosting** via GitHub Actions on every push or merged PR to `main`.

**Pipeline (`.github/workflows/deploy.yaml`):**

1. Checkout code
2. `npm ci` — install dependencies
3. `npm run build` — compile `style.css`, `editor.css`, `bbb-scripts.min.js` fresh
4. rsync to production server over SSH, excluding dev-only files

**Why certain files are gitignored:**

-   `style.css`, `editor.css` (+ map files) — compiled by the Action on each deploy; committing them creates noise
-   `gulpfile.js` — local dev only, excluded from rsync and therefore gitignored
-   `node_modules/` — installed fresh by `npm ci` in the Action

**What is excluded from rsync (not deployed to production):**
`node_modules/`, `src/`, `.git/`, `.github/`, `.gitignore`, `package.json`, `package-lock.json`, `gulpfile.js`, `webpack.config.cjs`, `README.md`, `CLAUDE.md`, `.claude/`, `.nvmrc`, `.prettierignore`, `bigbluebox/`

**Implications:**

-   `main` is production — every merge triggers a live deploy
-   Use feature branches + PRs for all changes; never commit directly to `main`
-   `npm run build` locally is for verification only — the Action runs its own build
-   Never commit compiled output (`style.css`, `bbb-scripts.min.js`) — the Action regenerates them
-   Secrets (`CAPTIVATE_USER_ID`, `CAPTIVATE_API_TOKEN`, `CAPTIVATE_SHOW_ID`, `SSH_PRIVATE_KEY`, `SSH_USER`, `SSH_PATH`) are stored in GitHub repository settings, not in the codebase

**Commit format:** Conventional Commits — `feat:`, `fix:`, `chore:`, `refactor:`, `style:`, `docs:`

## Files You Should Know

| Purpose                      | File                                                                         |
| ---------------------------- | ---------------------------------------------------------------------------- |
| Theme setup & core functions | `functions.php`                                                              |
| Script/style enqueuing       | `inc/enqueue.php`                                                            |
| AJAX post filtering          | `functions.php` → search `filter_posts_by_category`                          |
| Captivate ACF integration    | `inc/acf-fields.php`                                                         |
| Audio URL fetching + caching | `inc/captivate-external-audio.php`                                           |
| Episode batch sync           | `inc/sync-captivate-episodes.php`                                            |
| Transcript fetching          | `inc/captivate-transcript-metabox.php`, `inc/sync-captivate-transcripts.php` |
| API admin tools page         | `inc/api-shutdown.php`                                                       |
| Custom pagination            | `inc/pagination.php`                                                         |
| Related articles logic       | `inc/related-articles.php`                                                   |
| Block registration           | `inc/register-blocks.php`                                                    |
| RSS feed customisation       | `inc/feeds.php`                                                              |
| Custom comment rendering     | `inc/custom-comments.php`                                                    |
| Gulp build config            | `gulpfile.js`                                                                |
| Webpack block config         | `webpack.config.cjs`                                                         |
| SCSS variables & tokens      | `src/scss/global/_variables.scss`, `src/scss/global/_colour-tokens.scss`     |
| Main JS source               | `src/scripts/bbb-scripts.js`                                                 |
| Reviews data                 | `data/reviews-compendium.json`                                               |

## Skills

Specialised instruction sets live in `.claude/skills/`. Read the relevant file before starting work on the corresponding task type.

| Skill file                         | When to apply                                                                             |
| ---------------------------------- | ----------------------------------------------------------------------------------------- |
| `.claude/skills/ux-copy.md`        | Any written content — UI copy, page copy, CTAs, meta descriptions, social posts, outreach |
| `.claude/skills/a11y.md`           | Any HTML, PHP template, SCSS, or JS — apply to every component and interaction            |
| `.claude/skills/codebase-audit.md` | Before writing or modifying any code — confirm file paths, helpers, and patterns first    |
