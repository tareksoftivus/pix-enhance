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
        :root { --primary: {{ $themeVars['primary_color'] ?? '#D97706' }}; --accent: {{ $themeVars['accent_color'] ?? '#1F2937' }}; --surface: #fffaf2; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Georgia, Cambria, "Times New Roman", serif; background: linear-gradient(180deg, #fffdf8 0%, var(--surface) 100%); color: #1f2937; }
        .shell { max-width: 1120px; margin: 0 auto; padding: 0 20px; }
        .section { padding: 72px 0; }
        .eyebrow { letter-spacing: .18em; text-transform: uppercase; font-size: 12px; color: var(--primary); font-weight: 700; }
        .title { font-size: clamp(2rem, 4vw, 4rem); line-height: 1.05; margin: 12px 0 18px; color: var(--accent); }
        .lead { max-width: 720px; font-size: 1.1rem; color: #4b5563; line-height: 1.7; }
        .grid { display: grid; gap: 20px; }
        .grid-3 { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
        .card { background: rgba(255,255,255,.86); border: 1px solid rgba(31,41,55,.08); border-radius: 24px; padding: 24px; box-shadow: 0 24px 60px rgba(31,41,55,.06); }
        .btn-row { display: flex; gap: 14px; flex-wrap: wrap; margin-top: 26px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 14px 22px; border-radius: 999px; text-decoration: none; font-weight: 700; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-secondary { border: 1px solid rgba(31,41,55,.15); color: var(--accent); background: white; }
        .faq-item + .faq-item { margin-top: 14px; }
        .site-header { position: sticky; top: 0; z-index: 20; backdrop-filter: blur(12px); background: rgba(255,253,248,.82); border-bottom: 1px solid rgba(31,41,55,.08); }
        .site-header-shell { min-height: 76px; display: flex; align-items: center; justify-content: space-between; gap: 24px; }
        .site-brand { color: var(--accent); text-decoration: none; font-weight: 700; font-size: 1.1rem; }
        .site-nav { margin-left: auto; }
        .site-nav-list, .site-submenu, .site-mobile-nav-list, .site-footer-nav { list-style: none; padding: 0; margin: 0; }
        .site-nav-list { display: flex; align-items: center; gap: 18px; }
        .site-nav-item, .site-submenu-item { position: relative; }
        .site-nav-parent { position: relative; }
        .site-nav-link { color: var(--accent); text-decoration: none; font-weight: 600; background: transparent; border: 0; font: inherit; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
        .site-nav-text { opacity: .8; cursor: default; }
        .site-submenu { position: absolute; left: 0; top: calc(100% + 12px); min-width: 220px; display: none; border-radius: 18px; padding: 12px; background: white; border: 1px solid rgba(31,41,55,.08); box-shadow: 0 24px 60px rgba(31,41,55,.10); }
        .site-nav-parent:hover > .site-submenu, .site-nav-parent:focus-within > .site-submenu { display: grid; gap: 10px; }
        .site-mobile-nav { display: none; margin-left: auto; }
        .site-mobile-nav summary { list-style: none; cursor: pointer; padding: 10px 16px; border-radius: 999px; background: white; border: 1px solid rgba(31,41,55,.08); font-weight: 700; color: var(--accent); }
        .site-mobile-nav-list { display: grid; gap: 12px; margin-top: 12px; padding: 14px; border-radius: 18px; background: white; border: 1px solid rgba(31,41,55,.08); }
        .site-mobile-nav-list .site-submenu { position: static; display: grid; margin-top: 10px; padding: 0 0 0 16px; min-width: 0; background: transparent; border: 0; box-shadow: none; gap: 8px; }
        .site-menu-footer { background: rgba(31,41,55,.96); color: white; padding: 36px 0; }
        .site-menu-footer-inner { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 18px; }
        .site-menu-footer-title { margin: 0; font-size: 1rem; font-weight: 700; }
        .site-footer-nav { display: flex; flex-wrap: wrap; gap: 16px 24px; }
        .site-menu-footer .site-nav-link, .site-menu-footer .site-nav-text { color: white; }
        .site-menu-footer .site-submenu { position: static; display: grid; margin-top: 10px; padding: 0; min-width: 0; background: transparent; border: 0; box-shadow: none; gap: 8px; }
        .footer { background: var(--accent); color: white; }
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
