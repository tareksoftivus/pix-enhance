# Making Frontend Sections Dynamic

This guide documents the complete pattern now used by `demo_section`, and it is the same pattern you should follow for any other section in `resources/views/frontend/themes/leadatlas/sections/`.

Use this when converting a static section into an admin-managed dynamic section.

---

## Overview

Every frontend section blade receives a `$section` variable (`App\Modules\Frontend\Models\FrontendSection`). Its `data` column is already cast to a PHP array.

The dynamic workflow always has the same 4 steps:

1. **Declare the schema** in `config/frontend-sections.php`
2. **Resolve section data** at the top of the blade
3. **Replace hardcoded markup** with those resolved variables
4. **Seed sensible defaults** in `FrontendSectionSeeder.php`

If you skip one of these, the section is only partially dynamic.

---

## Reference Example: `demo_section`

If you want a working reference, compare these files together:

- `config/frontend-sections.php`
- `resources/views/frontend/themes/leadatlas/sections/demo_section.blade.php`
- `app/Modules/Frontend/Database/Seeders/FrontendSectionSeeder.php`

That section demonstrates:

- grouped intro fields
- repeater items
- repeater icon picker fields
- repeater media picker fields
- relative CTA links like `/pricing.html`
- per-item transformation in the blade before rendering

---

## Step 1 — Declare Fields In `config/frontend-sections.php`

Find the section definition by `type` and fully describe its editable data inside `fields`.

Example using the same structure as `demo_section`:

```php
'demo_section' => [
    'type' => 'demo_section',
    'label' => 'Home Features',
    'icon' => 'ph ph-squares-four',
    'description' => 'Sticky-stacked feature cards with images, links, and concise summaries.',
    'category' => 'Marketing',
    'supported_themes' => ['leadatlas'],
    'fallback_renderer' => 'frontend.shared.sections.unsupported',
    'fields' => [
        'eyebrow' => [
            'type' => 'text',
            'label' => 'Eyebrow',
            'default' => 'The platform',
            'rules' => 'nullable|string|max:120',
            'group' => 'intro',
            'group_label' => 'Intro Copy',
            'group_hint' => 'Header eyebrow, main title, and description for feature stacked cards.',
        ],
        'title' => [
            'type' => 'text',
            'label' => 'Title',
            'default' => 'Everything you need to make recognition stick',
            'rules' => 'required|string|max:255',
            'group' => 'intro',
            'group_label' => 'Intro Copy',
            'group_hint' => 'Header eyebrow, main title, and description for feature stacked cards.',
        ],
        'subtitle' => [
            'type' => 'textarea',
            'label' => 'Subtitle',
            'default' => 'Six building blocks that turn everyday appreciation into a habit your whole team looks forward to.',
            'rules' => 'nullable|string|max:1000',
            'group' => 'intro',
            'group_label' => 'Intro Copy',
            'group_hint' => 'Header eyebrow, main title, and description for feature stacked cards.',
        ],
        'items' => [
            'type' => 'repeater',
            'label' => 'Features',
            'default' => [],
            'rules' => 'nullable',
            'schema' => [
                'title' => [
                    'type' => 'text',
                    'label' => 'Feature Eyebrow',
                ],
                'heading' => [
                    'type' => 'text',
                    'label' => 'Feature Heading',
                ],
                'description' => [
                    'type' => 'textarea',
                    'label' => 'Feature Description',
                ],
                'tint' => [
                    'type' => 'text',
                    'label' => 'Tint Key',
                ],
                'icon' => [
                    'type' => 'icon',
                    'label' => 'Phosphor Icon Class',
                ],
                'link_text' => [
                    'type' => 'text',
                    'label' => 'CTA Text',
                ],
                'link_url' => [
                    'type' => 'url',
                    'label' => 'CTA URL',
                ],
                'image' => [
                    'type' => 'media',
                    'label' => 'Card Image',
                    'accept' => 'image',
                    'recommended_size' => 'Recommended: 900×720px',
                ],
            ],
        ],
    ],
],
```

### Supported Field Types

| `type` | Renders as | Notes |
|---|---|---|
| `text` | Single-line input | Best for headings, labels, links, short copy |
| `textarea` | Multi-line textarea | Best for paragraphs and descriptions |
| `media` | Media library picker | Use `accept: 'image'` for image-only fields |
| `repeater` | Add-more rows with sub-fields | Each sub-field uses the same type rules |
| `select` | Dropdown | Provide `options` |
| `boolean` / `feature` | Toggle | Stored as boolean-ish values |
| `icon` | Shared icon picker | Use this for Phosphor icon selection |
| `url` | URL-style field | In repeaters this still supports relative internal paths like `/pricing.html` |

### Important Field Options

| Key | Purpose |
|---|---|
| `type` | Field type |
| `label` | Label shown in admin |
| `default` | Fallback value when nothing is saved |
| `rules` | Laravel validation rules |
| `group` | Visual grouping in the admin form |
| `group_label` | Group heading |
| `group_hint` | Helper text shown above grouped fields |
| `recommended_size` | Media guidance shown in the picker |
| `accept` | Media filter such as `image` |
| `schema` | Repeater row field definitions |

### Rules To Follow

- Use `type: 'icon'` for Phosphor icons instead of plain text fields.
- Use `type: 'media'` for images/files instead of manual paths.
- Prefer `accept: 'image'`, not `image/*`, for section config.
- Keep CTA links as normal strings like `/about`, `/pricing.html`, or `#contact`.
- Put related editor fields in the same `group` so the admin form stays understandable.

---

## Step 2 — Resolve Variables At The Top Of The Blade

At the top of the blade, always start from:

```blade
@php
    $d = $section->data ?? [];
@endphp
```

Then resolve every field into explicit variables before the HTML starts.

Example using the `demo_section` pattern:

```blade
@php
    $d = $section->data ?? [];

    $eyebrow = $d['eyebrow'] ?? 'The platform';
    $title = $d['title'] ?? 'Everything you need to make recognition stick';
    $subtitle = $d['subtitle'] ?? 'Six building blocks that turn everyday appreciation into a habit your whole team looks forward to.';

    $defaultFeatureImages = [
        0 => asset('assets/images/feature-1.png'),
        1 => asset('assets/images/feature-2.png'),
        2 => asset('assets/images/feature-3.png'),
        3 => asset('assets/images/feature-4.png'),
        4 => asset('assets/images/feature-5.png'),
        5 => asset('assets/images/feature-6.png'),
    ];

    $tintStyles = [
        'primary' => [
            'text' => '',
            'shadow' => 'box-shadow: 0 24px 60px -24px rgba(124,58,237,0.45)',
        ],
        'accent' => [
            'text' => 'text-accent',
            'shadow' => 'box-shadow: 0 24px 60px -24px rgba(245,158,11,0.45)',
        ],
        'info' => [
            'text' => 'text-info',
            'shadow' => 'box-shadow: 0 24px 60px -24px rgba(14,165,233,0.4)',
        ],
    ];

    $items = $d['items'] ?? [];

    $features = collect($items)
        ->values()
        ->map(function ($item, $index) use ($defaultFeatureImages, $tintStyles) {
            $tint = $item['tint'] ?? 'primary';
            $resolvedTint = $tintStyles[$tint] ?? $tintStyles['primary'];

            return [
                'title' => $item['title'] ?? '',
                'heading' => $item['heading'] ?? '',
                'description' => $item['description'] ?? '',
                'icon' => $item['icon'] ?? '',
                'link_text' => $item['link_text'] ?? 'Learn more',
                'link_url' => $item['link_url'] ?? '#',
                'text_class' => $resolvedTint['text'],
                'stack_tint' => 'var(--color-' . $tint . ')',
                'image_url' => media_url($item['image'] ?? null) ?: ($defaultFeatureImages[$index] ?? null),
                'image_shadow' => $resolvedTint['shadow'],
            ];
        });
@endphp
```

### Resolution Rules

- Resolve simple scalar fields first.
- Pull repeater rows into a local variable like `$items = $d['items'] ?? [];`.
- If the section needs display-specific transforms, do them here before the markup.
- Resolve media IDs with `media_url(...)`.
- If a media field needs a design fallback, apply that fallback during resolution.
- If repeater rows contain optional keys, always guard them with `??`.

### When To Transform Data In The Blade

Do data shaping in the `@php` block when the view needs:

- fallback images
- computed classes
- resolved color/tint maps
- normalized CTA labels
- flattened lists from repeater rows

That keeps the HTML simple and readable.

---

## Step 3 — Replace Hardcoded Markup With Resolved Variables

Once the variables are resolved, the markup should render only those variables.

### Simple Text Example

```blade
{{-- Before --}}
<h2>Everything you need to make recognition stick</h2>
<p>Six building blocks that turn everyday appreciation into a habit your whole team looks forward to.</p>

{{-- After --}}
<h2>{{ $title }}</h2>
<p>{{ $subtitle }}</p>
```

### Media Example

```blade
@if ($feature['image_url'])
    <img
        src="{{ $feature['image_url'] }}"
        alt="{{ $feature['heading'] }}"
        class="w-full max-w-sm rounded-2xl"
        style="{{ $feature['image_shadow'] }}"
    />
@endif
```

### Repeater Example

```blade
@if ($features->isNotEmpty())
    @foreach ($features as $feature)
        <article>
            @if ($feature['title'])
                <span>
                    @if ($feature['icon'])
                        <i class="{{ $feature['icon'] }}"></i>
                    @endif
                    {{ $feature['title'] }}
                </span>
            @endif

            @if ($feature['heading'])
                <h3>{{ $feature['heading'] }}</h3>
            @endif

            @if ($feature['description'])
                <p>{{ $feature['description'] }}</p>
            @endif

            @if ($feature['link_text'])
                <a href="{{ $feature['link_url'] }}">{{ $feature['link_text'] }}</a>
            @endif
        </article>
    @endforeach
@endif
```

### Rendering Rules

- Do not leave hardcoded copy in the HTML if that content exists in `fields`.
- Always guard optional content with `@if`.
- Always use `$item['key'] ?? ''` or resolved transformed values.
- For repeater icons, render the stored class directly in `<i class="{{ $item['icon'] ?? '' }}"></i>`.
- For repeater links, render the saved path as-is; relative links are valid.

---

## Step 4 — Seed Defaults In `FrontendSectionSeeder.php`

Find the matching definition in `app/Modules/Frontend/Database/Seeders/FrontendSectionSeeder.php` and seed the section with realistic defaults.

Example matching `demo_section`:

```php
[
    'name' => 'Homepage Features',
    'slug' => 'homepage-features',
    'type' => 'demo_section',
    'status' => 'published',
    'description' => 'Feature highlights for the homepage.',
    'data' => [
        'eyebrow' => 'The platform',
        'title' => 'Everything you need to make recognition stick',
        'subtitle' => 'Six building blocks that turn everyday appreciation into a habit your whole team looks forward to.',
        'items' => [
            [
                'title' => 'Shout-outs',
                'heading' => 'Peer recognition in seconds',
                'description' => 'Tag a teammate, attach a company value, add points, and post it to the feed. Reactions and comments keep the momentum going.',
                'tint' => 'primary',
                'icon' => 'ph-fill ph-hand-heart',
                'link_text' => 'Learn more',
                'link_url' => '/pricing.html',
                'image' => null,
            ],
            [
                'title' => 'Points & rewards',
                'heading' => 'Points that mean something',
                'description' => 'Every recognition awards points teammates redeem for gift cards, swag, experiences, or time off — from a catalog you control.',
                'tint' => 'accent',
                'icon' => 'ph-fill ph-coin',
                'link_text' => 'Learn more',
                'link_url' => '/pricing.html',
                'image' => null,
            ],
        ],
    ],
],
```

### Seeder Rules

- Seed every major visible field with realistic defaults.
- Include repeater rows if the layout expects them.
- Use `null` for media IDs when no media is bundled by default.
- Keep the seeded structure aligned with the schema keys exactly.

After editing the seeder, re-seed just the frontend sections:

```bash
php artisan db:seed --class="App\\Modules\\Frontend\\Database\\Seeders\\FrontendSectionSeeder"
```

---

## Full Checklist Per Section

- [ ] `config/frontend-sections.php` contains a complete `fields` schema for the section type
- [ ] Text, textarea, repeater, icon, media, and select fields are declared with the correct types
- [ ] Related admin fields are grouped with `group`, `group_label`, and `group_hint` where useful
- [ ] The blade starts with `@php $d = $section->data ?? []; @endphp`
- [ ] Every dynamic field is resolved to a local variable before HTML starts
- [ ] Repeater data is pulled into a local variable and transformed before rendering if needed
- [ ] Media IDs are resolved with `media_url(...)`
- [ ] Any design fallback assets are applied during variable resolution, not buried in the markup
- [ ] All hardcoded content that belongs to the schema has been replaced in the HTML
- [ ] Repeater output uses guarded keys like `$item['title'] ?? ''`
- [ ] The matching section in `FrontendSectionSeeder.php` contains sensible default `data`
- [ ] `php artisan db:seed --class="App\\Modules\\Frontend\\Database\\Seeders\\FrontendSectionSeeder"` has been run
- [ ] `vendor/bin/pint --dirty --format agent` has been run

---

## Practical Notes

- `FrontendSectionService::normalizeData()` merges saved values with config defaults before the section is rendered.
- `media_url(null)` returns `null`, so always guard image output with `@if` or provide a fallback URL.
- Repeater rows are stored as plain associative arrays, not objects.
- When adding a new repeater sub-field later, older rows may not contain that key yet, so always use `??`.
- Use `type: 'icon'` for icon selection. Do not switch back to free-text icon entry.
- In section config, `type: 'url'` can still be used for internal relative paths like `/pricing.html`.
- Group settings affect admin presentation only; they do not change storage format.

---

## If You Are Converting Another Section

Use `demo_section` as your template when a section has:

- a section intro
- repeated cards/items
- icons per row
- CTA text/link per row
- image uploads per row
- computed display styles based on saved values

If the target section is simpler than `demo_section`, follow the same process with fewer field types, not a different process.
