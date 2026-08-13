<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->meta_title ?: $page->title }}</title>
    @if($page->meta_description)
        <meta name="description" content="{{ $page->meta_description }}">
    @endif
    <style>
        :root { --primary: {{ $themeVars['primary_color'] ?? '#0F766E' }}; --accent: {{ $themeVars['accent_color'] ?? '#111827' }}; --surface: #f3fbfa; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Trebuchet MS", "Segoe UI", sans-serif; background:
            radial-gradient(circle at top left, rgba(15,118,110,.12), transparent 35%),
            linear-gradient(180deg, #fcfffe 0%, var(--surface) 100%); color: #111827; }
        .shell { max-width: 1180px; margin: 0 auto; padding: 0 20px; }
        .section { padding: 76px 0; }
        .eyebrow { letter-spacing: .18em; text-transform: uppercase; font-size: 12px; color: var(--primary); font-weight: 800; }
        .title { font-size: clamp(2.2rem, 4.2vw, 4.6rem); line-height: 0.98; margin: 14px 0 18px; color: var(--accent); text-transform: {{ !empty($themeVars['uppercase_headings']) ? 'uppercase' : 'none' }}; }
        .lead { max-width: 760px; font-size: 1.08rem; color: #475569; line-height: 1.75; }
        .grid { display: grid; gap: 18px; }
        .grid-3 { grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); }
        .card { background: white; border: 1px solid rgba(15,118,110,.12); border-radius: 28px; padding: 24px; box-shadow: 0 24px 60px rgba(15,118,110,.08); }
        .btn-row { display: flex; gap: 14px; flex-wrap: wrap; margin-top: 28px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 14px 22px; border-radius: 16px; text-decoration: none; font-weight: 800; }
        .btn-primary { background: var(--accent); color: white; }
        .btn-secondary { border: 1px solid rgba(15,118,110,.2); color: var(--accent); background: rgba(255,255,255,.8); }
        .faq-item + .faq-item { margin-top: 14px; }
        .site-header { position: sticky; top: 0; z-index: 20; backdrop-filter: blur(12px); background: rgba(252,255,254,.84); border-bottom: 1px solid rgba(15,118,110,.12); }
        .site-header-shell { min-height: 76px; display: flex; align-items: center; justify-content: space-between; gap: 24px; }
        .site-brand { color: var(--accent); text-decoration: none; font-weight: 800; font-size: 1.05rem; letter-spacing: .08em; text-transform: uppercase; }
        .site-nav { margin-left: auto; }
        .site-nav-list, .site-submenu, .site-mobile-nav-list, .site-footer-nav { list-style: none; padding: 0; margin: 0; }
        .site-nav-list { display: flex; align-items: center; gap: 18px; }
        .site-nav-item, .site-submenu-item { position: relative; }
        .site-nav-parent { position: relative; }
        .site-nav-link { color: var(--accent); text-decoration: none; font-weight: 700; background: transparent; border: 0; font: inherit; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
        .site-nav-text { opacity: .8; cursor: default; }
        .site-submenu { position: absolute; left: 0; top: calc(100% + 12px); min-width: 240px; display: none; border-radius: 22px; padding: 14px; background: white; border: 1px solid rgba(15,118,110,.14); box-shadow: 0 24px 60px rgba(15,118,110,.12); }
        .site-nav-parent:hover > .site-submenu, .site-nav-parent:focus-within > .site-submenu { display: grid; gap: 10px; }
        .site-mobile-nav { display: none; margin-left: auto; }
        .site-mobile-nav summary { list-style: none; cursor: pointer; padding: 10px 16px; border-radius: 16px; background: white; border: 1px solid rgba(15,118,110,.12); font-weight: 800; color: var(--accent); }
        .site-mobile-nav-list { display: grid; gap: 12px; margin-top: 12px; padding: 14px; border-radius: 18px; background: white; border: 1px solid rgba(15,118,110,.12); }
        .site-mobile-nav-list .site-submenu { position: static; display: grid; margin-top: 10px; padding: 0 0 0 16px; min-width: 0; background: transparent; border: 0; box-shadow: none; gap: 8px; }
        .site-menu-footer { background: #0b1320; color: white; padding: 40px 0; }
        .site-menu-footer-inner { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 18px; }
        .site-menu-footer-title { margin: 0; font-size: 1rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; }
        .site-footer-nav { display: flex; flex-wrap: wrap; gap: 16px 24px; }
        .site-menu-footer .site-nav-link, .site-menu-footer .site-nav-text { color: white; }
        .site-menu-footer .site-submenu { position: static; display: grid; margin-top: 10px; padding: 0; min-width: 0; background: transparent; border: 0; box-shadow: none; gap: 8px; }
        .footer { background: #0b1320; color: white; }
        .footer a { color: white; opacity: .9; text-decoration: none; }
        @media (max-width: 860px) {
            .site-nav { display: none; }
            .site-mobile-nav { display: block; }
        }
        @media (max-width: 640px) { .section { padding: 56px 0; } }
    </style>
</head>
<body>
    @includeFirst([$theme['view_namespace'].'.navigation.header', 'frontend.shared.navigation.header'], ['theme' => $theme, 'themeVars' => $themeVars, 'resolvedMenus' => $resolvedMenus])
    @foreach($resolvedSections as $resolved)
        @include($resolved['view'], ['section' => $resolved['section'], 'themeKey' => $themeKey, 'themeVars' => $themeVars, 'supported' => $resolved['supported']])
    @endforeach
    @includeFirst([$theme['view_namespace'].'.navigation.footer', 'frontend.shared.navigation.footer'], ['theme' => $theme, 'themeVars' => $themeVars, 'resolvedMenus' => $resolvedMenus])
</body>
</html>
