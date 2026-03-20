# Skill: Codebase Audit

Apply this skill before writing or modifying any code for the Big Blue Box Podcast theme. The purpose is to prevent hallucinated file paths, incorrect helper references, and code that conflicts with established patterns.

---

## Rule: search before you write

Before generating any code that references a file path, function name, SCSS partial, template part, or data structure — search the project knowledge first.

Never assume a file exists. Never invent a helper function. Never guess at a directory structure.

If the information is not in project knowledge, say so explicitly and ask before proceeding.

---

## Pre-coding checklist

Work through this before producing any code output:

**1. File location**
- Does the file I need to edit or create already exist?
- If editing: confirm the exact path from project knowledge
- If creating: confirm the correct directory per the placement rules in `CLAUDE.md`

**2. Functions and helpers**
- Does a helper function already exist for what I need?
- Search for `bbb_` prefixed functions before writing a new one
- Check `/inc/` files — all are auto-loaded; no manual `require` needed

**3. SCSS**
- Does a partial already exist for this component or pattern?
- Check `src/scss/blocks/`, `src/scss/global/`, `src/scss/utilities/`
- Confirm which `_index.scss` to add a new partial to via `@forward`
- Confirm the correct CSS custom properties from `_variables.scss` and `_colour-tokens.scss` — do not hardcode values

**4. JavaScript**
- All JS goes in `src/scripts/bbb-scripts.js` unless it is a Gutenberg block
- Confirm the existing structure before adding — do not create a new file
- No jQuery. No inline scripts in templates.

**5. Data and templates**
- If the feature uses JSON data, check `/data/` for existing files before creating new ones
- If the feature needs a template part, check `/template-parts/` first
- Page templates go in `/page-templates/`

---

## Pattern-matching rules

When writing code, match the established patterns exactly:

| Context | Pattern to follow |
|---|---|
| New PHP helper | `bbb_` prefix, file in `/inc/`, tabs for indentation, escape all output |
| New SCSS component | `_component-name.scss` in `src/scss/blocks/`, `@forward` in `_index.scss` |
| New page styles | `src/scss/pages/` |
| Colour values | CSS custom properties only — `var(--clr-*)` |
| Spacing | `var(--space-*)` |
| Typography scale | `var(--step-*)` |
| Breakpoints | Mobile-first — `min-width: 37.5rem`, `min-width: 55rem` |
| Translation strings | `esc_html__( 'string', 'bigbluebox' )` |
| Debug output | `bbb_log()` only — never `echo`, `var_dump`, or `console.log` in committed code |

---

## Before proposing a new abstraction

Only create a new helper function, mixin, or utility if it will be used in three or more places. If it is used in fewer than three places, write it inline. State the reason if creating something new.

---

## Conflict check

Before finalising any code:
- Does this conflict with anything in `CLAUDE.md`?
- Does this introduce a new npm or Composer dependency? (Discuss before proceeding — do not assume approval)
- Does this touch `functions.php` directly? (Only for hooks and theme setup — helpers go in `/inc/`)
- Does this write inline styles or inline scripts into a template? (It should not)

---

## Output format

When producing code:
1. State which file(s) are being modified or created and confirm the path is verified from project knowledge
2. Show only the relevant addition — not the entire file unless specifically asked
3. Note any follow-up steps (e.g. adding a `@forward` to an index file, flushing rewrite rules, clearing transients)
4. Flag anything that could not be confirmed from project knowledge
