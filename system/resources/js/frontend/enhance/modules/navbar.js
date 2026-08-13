/**
 * Navbar module
 * Adds `.is-stuck` to the site header once the page scrolls past a threshold,
 * and marks the current section link while scrolling through the landing page.
 */

'use strict';

const SCROLL_THRESHOLD = 24;

export function initNavbar(selector = '[data-navbar]') {
    const header = document.querySelector(selector);
    if (!header) return;

    let ticking = false;

    const sync = () => {
        header.classList.toggle('is-stuck', window.scrollY > SCROLL_THRESHOLD);
        ticking = false;
    };

    const onScroll = () => {
        if (ticking) return;
        ticking = true;
        window.requestAnimationFrame(sync);
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    sync();
}

/**
 * Highlights the nav link matching the section currently in view.
 */
export function initScrollSpy(linkSelector = '[data-spy]') {
    const links = Array.from(document.querySelectorAll(linkSelector));
    if (!links.length || !('IntersectionObserver' in window)) return;

    const map = new Map();

    links.forEach((link) => {
        const id = link.getAttribute('href');
        if (!id || !id.startsWith('#')) return;

        const section = document.querySelector(id);
        if (section) map.set(section, link);
    });

    if (!map.size) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;

                links.forEach((link) => link.classList.remove('is-active'));
                map.get(entry.target)?.classList.add('is-active');
            });
        },
        { rootMargin: '-45% 0px -50% 0px', threshold: 0 }
    );

    map.forEach((_link, section) => observer.observe(section));
}

export default { initNavbar, initScrollSpy };
