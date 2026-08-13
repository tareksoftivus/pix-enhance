# Frontend Sections

Sections are typed, reusable content blocks stored in `frontend_sections`.

## Section registry

The registry lives in `config/frontend-sections.php`.

Each section type defines:

- `type`
- `label`
- `icon`
- `description`
- `category`
- `supported_themes`
- `fallback_renderer`
- `fields`

## Field model

Section fields are schema-driven and rendered through shared form components.

Currently supported in the frontend section editor:

- text
- textarea
- select
- boolean / feature
- media
- color
- checkbox
- tags
- date
- date range
- datetime
- time
- editor
- repeater

## Repeater fields

Repeaters are stored as JSON arrays inside a section’s `data` payload.

Use repeater fields for:

- FAQ items
- feature lists
- testimonial entries
- footer links

## Compatibility

Each section type declares `supported_themes`.

This is used in the admin area to:

- show which themes can render the section cleanly
- warn when the active theme would need fallback rendering
- help editors choose sections that remain portable across themes

## Fallback behavior

If a section type is unsupported by the current theme:

- the section resolves to the theme fallback renderer
- the fallback view explains that the section needs a compatible theme or a theme-specific renderer
