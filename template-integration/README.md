# Template Integration — Guide for a Coding Agent

This folder is a **staging area**. You (a coding agent) drop a static HTML template
into it and port its markup into this Laravel application: page sections, header,
footer, menus, auth layouts, and the user dashboard — with all copy extracted into
translation keys.

> **Reference only.** Nothing here is served or shipped. It exists so the original
> design sits beside the codebase while you translate it. Delete it once porting is
> done.

---

## 0. Guardrails

These are **hard constraints**. Code that violates any of them will be rejected.
They apply to every file you touch or create while porting a template.

### 0.1 Markup & output

- **Never `{!! !!}`.** Never use Blade's unescaped echo. To emit HTML you have
  deliberately built, use an escaped `<?php echo … ?>` — escape everything.
- **Never `@$variable` and never `@json`.** Use explicit, readable constructs
  (`{{ $variable ?? '' }}`, `json_encode(...)` through an escaped echo where needed).
- **Valid, semantic HTML only.** Do **not** place block-level elements
  (`div`, `h2`, `p`, …) inside inline elements (`span`, `em`, `a` used inline, …).
- **100% W3C valid.** Excluding vendor-specific prefixes, all markup and CSS **must**
  validate against the W3C validators. Non-validating code is rejected.

### 0.2 CSS

- **No inline CSS.** No `style="…"` attributes and no `<style>` blocks in Blade.
  Move every rule into an external stylesheet under `/assets/frontend`
  (compiled via the app's build pipeline).
- **Distinguish the work.** Many free CSS files exist; a ported page must go
  **above and beyond** what is freely available. Clean design and proper
  abstraction are expected, not optional.

### 0.3 JavaScript

- **`'use strict';`** at the top of every auto-loaded function body — the callbacks
  that run on their own, e.g. `$(function () { 'use strict'; … })`,
  `DOMContentLoaded`, `$(document).ready(...)`.
- **Never `.click()`, `.hover()`, `.change()`** (the shorthand event binders). Bind
  explicitly with `.on('click', …)`, `.on('mouseenter mouseleave', …)`,
  `.on('change', …)` — or native `addEventListener`.

### 0.4 Assets & dependencies

- **No CDNs** except **Google Fonts**. Every other CSS/JS library, plugin, and font
  is vendored locally under `/assets/frontend`. Nothing loads from a third-party host.
- **Never hard-code asset URLs, hosts, or ports.** Use `asset('assets/…')` and
  `route('…')`. Assets are served **root-relative** so the build survives any
  host/port. (This app runs root-based: web root is the repo root, Laravel lives in
  `system/`, compiled assets in `/assets/`.)
- **No symlinks, ever.** This is a ship-and-play boilerplate for shared hosting.
- Rebuild assets after touching CSS/JS: `npm run build`.

### 0.5 Structure, security & quality

- **Do not invent structure.** Everything below maps to files that already exist.
  Follow the existing conventions; check sibling files before creating anything.
- **No new top-level folders** in the app without approval.
- **Never touch the admin views or layouts.** Porting is **frontend-only** — public
  frontend pages, the user-facing auth screens, and the user panel. Leave everything
  under `app/Panels/Admin/**`, `resources/views/admin/**`, and the admin layouts
  (`layouts.admin`, `layouts.admin-guest`) exactly as-is. If a template seems to call
  for an admin change, stop and ask.
- **Never hard-code text.** All user-visible strings go through `__('...')`
  (see §5). No raw English in Blade output.
- **Secure, well-commented, well-abstracted code.** A clean application design and
  proper abstraction are implicitly expected. Comment intent, not the obvious.

---

## 1. Where you drop the template

Put the raw template into this folder **however it arrives**. Templates don't come
in a fixed shape — a single `index.html`, a `dist/` build, a nested theme folder,
loose pages plus a `css/`+`js/`+`assets/` split, etc. Don't reorganize it.

**Start by scanning the folder** to discover what you actually have. Don't assume a
layout — look:

1. Find the pages — every `*.html` (recurse; they may be nested).
2. Find the stylesheets, scripts, images, fonts, and icons each page links to,
   wherever they sit relative to the HTML.
3. Map which pages map to which app surface (§3): landing/marketing → frontend
   sections; `login`/`register` → auth layouts; dashboard/account → user panel.

**Multiple home page variants → ask, don't guess.** Many templates ship several
home demos (`index.html`, `index-2.html`, `home-3.html`, `demo-*/index.html`, …).
If the scan turns up more than one home/landing variant, **stop and ask the user
which one to integrate** before porting — list the variants you found (file path
plus a one-line description of what makes each distinct) and let them pick. Never
silently pick the first one. The same applies to other duplicated page variants
(multiple about/pricing versions): integrate only the chosen variant; the rest
stay untouched in this folder as source material.

You read from here and write into the app. Nothing here is edited to "make it work" —
it is **source material**, left as-is.

---

## 2. How the system is built (orient before porting)

Repo root is the web root. **The Laravel application root is `system/`** — every app
path below is relative to `system/`.

Two structural layers:

| Layer      | What it is                                                        | Lives in            |
| ---------- | ---------------------------------------------------------------- | ------------------- |
| **Panels** | Shell layers (`admin`, `user`) — auth, dashboard, profile, chrome | `app/Panels/*`      |
| **Modules**| Feature layers (Users, Settings, Frontend, …)                    | `app/Modules/*`     |

Two distinct front-ends, do **not** mix them:

| Surface                | Rendered by                                    | Layout / views                                          |
| ---------------------- | ---------------------------------------------- | ------------------------------------------------------- |
| **Admin panel**        | `app/Panels/Admin` + admin modules             | `<x-layouts.admin>` — sidebar + topbar chrome           |
| **User dashboard**     | `app/Panels/User` + user modules               | `<x-layouts.user>` — same chrome, user scope            |
| **Public site (CMS)**  | `app/Modules/Frontend` — DB-driven pages       | theme layouts under `frontend/themes/{key}/layouts/`    |

**Golden rule of the CMS:** *content lives in the database* (pages, section
instances, menus) while *structure lives in code* (theme + section registries and
their Blade views). Porting a marketing/landing template means turning its repeated
blocks into **section types** (code) and its chrome into a **theme layout** (code);
the actual page and its content become **data** created in the admin UI.

Bootstrap / rebuild commands:

```bash
composer install && npm install
php artisan migrate && php artisan permission:sync
npm run build          # or: composer dev  (local dev server)
```

---

## 3. Porting decision tree

Start every page by asking **what surface it belongs to**:

- **Marketing / landing / content page** (home, about, pricing, features)
  → port into the **Frontend CMS**: build section types + a theme layout (§4).
- **Login / register / forgot / reset** → port into an **auth layout** (§6).
  - Choose the *user* auth stack **or** the *admin* auth stack — they are separate.
- **Logged-in dashboard / settings / list / detail page** → port into a **panel
  page** using `<x-layouts.admin>` or `<x-layouts.user>` (§7).

---

## 4. Frontend CMS — sections, themes, header/footer/menus

The public site is composed from **sections** wrapped by a **theme layout**, with
**menus** wired into header/footer slots. Render pipeline:

```
route  →  FrontendPageController  →  PageRenderService.payload($page, $theme)
       →  theme layout view  →  loops resolved sections + includes header/footer
```

### 4a. Section types (repeated content blocks)

A section type = **two** things:

1. **A registry entry** in `config/frontend-sections.php`, keyed by `type`:
   ```php
   'hero' => [
       'type' => 'hero',
       'label' => 'Hero',
       'icon' => 'ph ph-flag-banner',
       'description' => '…',
       'category' => 'Marketing',
       'supported_themes' => ['classic', 'studio'],
       'fallback_renderer' => 'frontend.shared.sections.unsupported',
       'fields' => [
           'title' => ['type' => 'text', 'label' => 'Title', 'default' => '…', 'rules' => 'required|string|max:255'],
           // field types: text, textarea, select, boolean/feature, media, color,
           // checkbox, tags, date, date_range, datetime, time, editor, repeater
           // (repeater fields carry a nested 'schema' for list items — FAQ, cards, links)
       ],
   ],
   ```
   The `fields` map **is** the schema — it drives the admin editor form and
   validation. There is no separate schema file.

2. **A Blade partial** at `resources/views/frontend/shared/sections/{type}.blade.php`.
   Paste the template's block here; replace static text with the instance payload:
   ```blade
   <h1 class="title">{{ $section->data['title'] ?? '' }}</h1>
   ```
   The partial receives `$section` (`$section->data[...]` = the instance's JSON),
   `$themeKey`, `$themeVars` (theme colors/toggles), `$supported`. Reuse the theme's
   shared CSS classes (`.section`, `.shell`, `.grid`, `.card`, `.btn`, `.eyebrow`,
   `.title`, `.lead`) so it inherits each theme's look. Field keys in the Blade
   **must** match the registry `fields` keys.

Shipped section types (mirror these): `hero`, `feature_grid`, `cta`, `faq`,
`testimonial_grid`, `rich_content`, `footer`, plus `unsupported` (the fallback).

**To add a section type:** (1) add the registry entry, (2) create the shared
partial, (3) list the `type` in each theme's `supported_section_types` in
`config/frontend-themes.php` (else that theme renders the fallback). Then create an
*instance* and attach it to a page from `/admin/frontend-sections` and
`/admin/frontend-pages`.

#### What makes a section "dynamic" (the key idea)

**The Blade partial is a template with holes; the content is DB data.** Nothing in
the markup is hard-coded copy. This is the whole point — port the template's block
**once**, and the site owner edits its text/images forever from the admin, with no
code changes.

The data flow:

```
config fields  ──►  auto-generated admin form  ──►  saved as JSON on the section
   (schema)              (/admin/frontend-sections)        instance's `data` column
                                                                    │
theme layout ──► @include section partial ──► reads $section->data['field_key'] ──► HTML
```

- One **section type** (code) can have **many instances** (DB rows), each with its
  own `data` payload — e.g. three different "hero" instances on three pages.
- Every `fields` key you declare becomes an editable field in the admin form **and**
  the key you read in Blade (`$section->data['title']`). They must match exactly.
- **Lists are dynamic too** via `repeater` fields — the admin can add/remove/reorder
  rows, stored as a JSON array you loop over. Real example (the `faq` section):
  ```blade
  @foreach(($section->data['items'] ?? []) as $item)
      <h3>{{ $item['question'] ?? '' }}</h3>
      <p>{{ $item['answer'] ?? '' }}</p>
  @endforeach
  ```
  (`items` is a repeater field whose nested `schema` defines `question` + `answer`.)
- Always guard with `?? ''` / `?? []` / `!empty(...)` so a not-yet-filled field
  renders cleanly, exactly as the shipped partials do.
- `$themeVars[...]` is a **theme-wide** setting (e.g. `show_hero_kicker`), edited
  once per theme — distinct from `$section->data[...]`, which is per-instance.

**So when porting a static block:** look at every piece of text/image/link that a
site owner would ever want to change, turn each into a `fields` entry, and replace
the static value in the markup with `$section->data['that_key']`. Anything genuinely
fixed (structural wrapper, decorative labels) can stay literal.

### 4b. Themes (the chrome + CSS)

A theme = a registry entry in `config/frontend-themes.php` (`key`, `view_namespace`
like `frontend.themes.classic`, `supported_section_types`, `page_layouts` map,
`fallback_renderer`, `theme_settings_schema`) **plus** layout Blade views under
`resources/views/frontend/themes/{key}/layouts/` (at least `page.blade.php`).

A layout is the full `<html>` document: `<head>` (uses `$page->meta_title`, meta
description, `<x-plugins.head-scripts />`), an inline `<style>` block that **is** the
theme's visual identity (wire `--primary` / `--accent` to `$themeVars`), and a
`<body>` that composes three parts:

```blade
@includeFirst([$theme['view_namespace'].'.navigation.header', 'frontend.shared.navigation.header'], [...])
@foreach($resolvedSections as $resolved)
    @include($resolved['view'], ['section' => $resolved['section'], 'themeKey' => $themeKey, 'themeVars' => $themeVars, 'supported' => $resolved['supported']])
@endforeach
@includeFirst([$theme['view_namespace'].'.navigation.footer', 'frontend.shared.navigation.footer'], [...])
```

Two themes ship (`classic`, `studio`) — structurally identical, differing only in CSS
and settings defaults. **A template's overall look is a new theme** (put its CSS in
the layout's `<style>`); its repeated blocks are sections.

### 4c. Header / footer / menus

Shared frontend nav markup lives in `resources/views/frontend/shared/navigation/`:
`header.blade.php`, `footer.blade.php`, and `items.blade.php` (the recursive item
renderer — handles nested `children`, `url`/`target`/`is_visible`). A theme may
override by adding `themes/{key}/navigation/header.blade.php`; otherwise the shared
ones are used.

Menus are **DB content** wired into **code-defined slots**:

- Slots are declared in `config/frontend-menus.php`: `header`, `footer`, `mobile`.
- Menu trees are built in the admin **Menu Management** (`/admin/frontend-menus`).
- The menu→slot assignment is a per-theme setting; assign it in **Frontend Themes**.
- At render, `MenuRenderService` exposes the resolved trees to layouts as
  `$resolvedMenus['header'|'footer'|'mobile']`.

Port the template's nav markup into the shared `header`/`footer`/`items` partials,
driving the links from `$resolvedMenus`, not hard-coded `<a>` tags.

### 4d. Plugins (third-party scripts: analytics, chat, captcha)

Plugins are **not** modules or sections — they are small, settings-gated snippets
that inject third-party markup into public pages. Shipped ones: Google Analytics 4,
Tawk.to live chat, Cloudflare Turnstile (captcha). The pattern is uniform and easy to
extend when a template ships with its own integrations (a chat widget, a pixel, a
cookie banner, etc.).

Every plugin is **four pieces**:

1. **Settings** — an enable toggle + its config fields, declared in the `plugins`
   group of `system/app/Modules/Settings/Config/settings.php`. Config fields use
   `visible_if` so they stay hidden until the plugin is enabled, and the card carries
   an `icon` + brand `color`:
   ```php
   'plugin_ga4_enabled' => [
       'type' => 'feature', 'label' => 'Google Analytics 4', 'default' => false,
       'card_group' => ['label' => 'Google Analytics 4', 'icon' => 'ph ph-chart-line-up', 'color' => '#E37400', ...],
   ],
   'plugin_ga4_measurement_id' => [
       'type' => 'text', 'label' => 'Measurement ID', 'default' => '',
       'card_group' => ['label' => 'Google Analytics 4'],
       'visible_if' => ['plugin_ga4_enabled' => [true, '1', 1]],   // hidden until enabled
   ],
   ```

2. **A Blade component** under `resources/views/components/plugins/`. It reads the
   settings with the `setting()` helper and emits its markup **only when enabled AND
   configured** — never unconditionally:
   ```blade
   @php
       $ga4Enabled = (bool) setting('plugin_ga4_enabled', false);
       $ga4Id = trim((string) setting('plugin_ga4_measurement_id', ''));
   @endphp
   @if($ga4Enabled && $ga4Id !== '')
       <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
       {{-- … --}}
   @endif
   ```
   Use `@once` for a script tag that must load only one time per page, and `@json(...)`
   when injecting a setting value into JS.

3. **An injection point** — where the component is rendered:
   - **Site-wide head scripts** (analytics, chat) go in `<x-plugins.head-scripts />`,
     already placed in the `<head>` of the frontend theme layouts and the guest/auth
     layouts. Add new head-level plugins *inside that one component* so they inherit
     the injection everywhere.
   - **A form widget** (captcha) is its own component dropped inside the relevant
     `<form>` — e.g. `<x-plugins.turnstile />` in the auth pages, which renders the
     challenge and the hidden response field.

4. **Server-side handling (only if the plugin verifies input).** Turnstile is checked
   on submit by `app/Rules/TurnstileValid.php`, applied in `LoginRequest` /
   `RegisterRequest`. A purely client-side plugin (analytics, chat) needs no backend.

**To add a plugin when porting a template:** (1) add its enable toggle + config to the
`plugins` settings group, (2) create/extend the plugin Blade component reading those
settings and guarding on enabled+configured, (3) inject it via `head-scripts`
(site-wide) or as a form component (inline), (4) add a validation rule only if the
server must verify something. The admin then flips it on and pastes keys in
**Settings → Plugins** — no redeploy to toggle.

---

## 5. Language keywords (i18n) — non-negotiable

Translations are **JSON files** at `resources/lang/{locale}.json`. Shipped locales:
`en`, `ar` (RTL), `bn`.

**Key style = the full English string as the key** (not dot notation):

```json
{
    "Sign In": "Sign In",
    "Enter your credentials to access your account": "Enter your credentials to access your account",
    "Don't have an account?": "Don't have an account?"
}
```

So in Blade you write the English through `__()`:

```blade
<h2>{{ __('Sign In') }}</h2>
<p>{{ __('Enter your credentials to access your account') }}</p>
```

**When porting HTML:** every visible string — headings, labels, buttons,
placeholders, alt text, aria-labels — must be wrapped in `__('…')`. Then add that
exact English key to **`resources/lang/en.json`** (and ideally the other locale
files, or leave them for a translator). Keep keys alphabetically sorted to match the
existing file. Never leave raw text in the markup.

---

## 6. Auth layouts (login / register / forgot / reset)

There are **two separate auth stacks** — pick the right one:

| Stack          | Controller                                  | Views                              | Layout                       |
| -------------- | ------------------------------------------- | ---------------------------------- | ---------------------------- |
| **User auth**  | `app/Http/Controllers/Auth/*`               | `resources/views/auth/*`           | `@extends('layouts.guest')`  |
| **Admin auth** | `app/Panels/Admin/Controllers/Auth/*`       | `resources/views/panels/admin/auth/*` | `@extends('layouts.admin-guest')` |

(User auth has `register` + `verify-email`; admin auth does **not** — admin accounts
are provisioned, not self-registered. The two guards are distinct: user = `web`
guard / `users` table / `/dashboard`; admin = `admin` guard / `admins` table /
`/admin`.)

Port an auth page by extending the matching layout and filling the `content`
section. Existing pattern (`resources/views/auth/login.blade.php`):

```blade
@extends('layouts.guest')
@section('title', __('Login'))
@section('content')
    <h2>{{ __('Sign In') }}</h2>
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <x-forms.input :label="__('Email Address')" name="email" type="email" :value="old('email')" required icon="ph ph-envelope-simple" />
        <x-forms.input :label="__('Password')" name="password" type="password" required icon="ph ph-lock" />
        <x-forms.submit :label="__('Sign In')" class="w-full" />
    </form>
@endsection
```

Reuse the form components (`<x-forms.input>`, `<x-forms.checkbox>`,
`<x-forms.submit>`) rather than raw `<input>`s — they carry the app's styling,
validation display, and icons. Take the template's *visual* auth design into the
**layout** (`layouts/guest.blade.php` / `layouts/admin-guest.blade.php`); keep the
per-page form in the view.

> **Trap:** `resources/views/layouts/guest.blade.php` (an `@extends` target) and
> `resources/views/components/layouts/guest.blade.php` (the `<x-layouts.guest>`
> component) are two different files that share a name. The **live auth pages use the
> `@extends` one.** Wire new auth pages to `@extends('layouts.guest')` /
> `@extends('layouts.admin-guest')`, not to the component.

---

## 7. Panel pages (admin panel & user dashboard)

Logged-in pages use component layouts (`@props(['title'])` + `$slot`):

```blade
<x-layouts.admin :title="__('Products')">
    {{-- ported page body here --}}
</x-layouts.admin>
```

`<x-layouts.admin>` / `<x-layouts.user>` already provide the document `<head>`
(Vite assets, fonts, Phosphor icons, CSRF, branding), the **sidebar**
(`<x-navigation.sidebar>`), the **topbar** (`<x-navigation.topbar :title>`), toast /
flash / modal containers, and these extension points — use them instead of adding
tags to `<head>`/`<body>`:

```blade
@push('styles')  …  @endpush
@push('scripts') …  @endpush
@push('modals')  …  @endpush
@push('drawers') …  @endpush
```

When porting a dashboard template: the **chrome** (sidebar, topbar) already exists —
do **not** re-port it. Extract only the page's **main content area** into the
`$slot`. If the template's sidebar/topbar *design* should replace the app's, edit the
render components in `resources/views/components/navigation/*` (`sidebar.blade.php`,
`sidebar-group.blade.php`, `sidebar-item.blade.php`, `topbar.blade.php`), not each
page.

### 7a. Sidebar menu items are DATA, not markup

The admin/user **sidebar is 100% data-driven** — never hand-write `<a>` items into
it. Items come from two places and are merged at request time:

- **Boilerplate-level items** → a PHP array under the panel's `navigation` key in
  `config/panels.php` (`label`, `icon`, `route` pattern, `group`, `permission`,
  `children`).
- **Feature-module items** → declared in code in `app/Modules/{Feature}/Module.php`
  via the fluent `adminNavigation()` builder:
  ```php
  public function adminNavigation(NavigationBuilder $navigation): void
  {
      $navigation
          ->group('System')
          ->item(label: 'Languages', route: 'admin.languages.*')
          ->icon('ph-translate')
          ->permission('languages.view')
          ->order(120);
  }
  ```

`route` must be a real named route (use the `.*` suffix for active-state matching).
`permission` gates visibility (`@can`). Labels are auto-wrapped in `__()`, so they
translate automatically. The `sidebar-item` component resolves the href, active
state, permission gate, and any `children` submenu for you. **To add a nav entry
when porting: add the data (config or module builder) — don't touch the Blade.**

(This is the *admin sidebar*. The *public-site* menus in §4c are a different,
DB-backed system — don't confuse the two.)

### 7b. Feature pages go in modules

Feature pages belong in a **module**, not a panel: keep routes/controllers/views
inside `app/Modules/{Feature}/` (views under `Resources/views/{admin|user}/`). New
CRUD screens should prefer the schema table system (`<x-tables.resource>` +
`TableDefinition`) — see `docs/datatable.md`.

---

## 8. Component & doc reference

Before writing markup, check what already exists (the convention is *reuse first*):

- **Blade components** — `resources/views/components/` (forms, ui, navigation,
  tables, media). Catalog: `docs/components.md`.
- **Frontend CMS internals** — `docs/frontend-sections.md`, `docs/frontend-themes.md`,
  `docs/frontend-menus.md`, `docs/frontend-public-rendering.md`,
  `docs/frontend-management.md`.
- **Menus** — `docs/menu-management.md`. **Settings fields** — `docs/settings_fields.md`.
- **Modules / architecture** — `docs/modules.md`, `docs/developer-guide.md`.
- **Project conventions (must-read)** — `system/CLAUDE.md`.

---

## 9. Porting checklist (per page)

1. Identify the surface (§3): CMS page / auth / panel.
   - Multiple variants of the same page (home demos especially)? Ask the user
     which one to integrate first (§1) — never pick silently.
2. Reuse existing layout + components; don't re-port chrome.
3. Break repeated blocks into section types (CMS) or components (panel).
4. Replace every asset URL with `asset('assets/…')`; every link with `route('…')`.
5. Wrap **all** visible text in `__('…')` and add keys to `resources/lang/en.json` (§5).
6. `npm run build`, then load the page and confirm it renders (no console errors).
7. Write/adjust a test where behavior changed (`php artisan test --compact`).
8. When every page is ported, delete this folder.
