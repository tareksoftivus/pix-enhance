/**
 * Icon Registry — Lucide
 * ---------------------------------------------------------------------------
 * Only the icons the template actually uses are imported, so the bundle stays
 * small instead of shipping the full ~1,500-icon set.
 *
 * Usage in markup:   <i data-lucide="sparkles" aria-hidden="true"></i>
 * Adding a new icon: import its PascalCase name below and add it to `registry`.
 * ---------------------------------------------------------------------------
 */

'use strict';

import {
    createIcons,
    Activity,
    Aperture,
    ArrowDown,
    ArrowLeft,
    ArrowRight,
    ArrowUpRight,
    BadgeCheck,
    Bell,
    BookOpen,
    Brain,
    Check,
    CheckCheck,
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    ChevronUp,
    CircleAlert,
    CircleCheck,
    CircleHelp,
    CirclePlay,
    Clock,
    CloudUpload,
    Coins,
    Copy,
    Cpu,
    CreditCard,
    Crop,
    Crown,
    Database,
    Download,
    Eraser,
    Eye,
    EyeOff,
    FileText,
    Filter,
    Fingerprint,
    Flame,
    Focus,
    Folder,
    Gauge,
    Gem,
    Globe,
    Image,
    ImagePlus,
    Images,
    Info,
    Key,
    Layers,
    LifeBuoy,
    Lightbulb,
    Lock,
    LogOut,
    Mail,
    MapPin,
    Maximize2,
    Menu,
    Monitor,
    MoveHorizontal,
    Palette,
    Phone,
    Play,
    Plus,
    Quote,
    RefreshCw,
    Rocket,
    Scan,
    ScanFace,
    ScanSearch,
    Search,
    Server,
    Settings,
    Share2,
    ShieldCheck,
    SlidersHorizontal,
    Smartphone,
    Sparkles,
    Star,
    Terminal,
    Timer,
    Trash2,
    TrendingUp,
    TriangleAlert,
    User,
    Users,
    WandSparkles,
    X,
    Zap,
} from 'lucide';

const registry = {
    Activity,
    Aperture,
    ArrowDown,
    ArrowLeft,
    ArrowRight,
    ArrowUpRight,
    BadgeCheck,
    Bell,
    BookOpen,
    Brain,
    Check,
    CheckCheck,
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    ChevronUp,
    CircleAlert,
    CircleCheck,
    CircleHelp,
    CirclePlay,
    Clock,
    CloudUpload,
    Coins,
    Copy,
    Cpu,
    CreditCard,
    Crop,
    Crown,
    Database,
    Download,
    Eraser,
    Eye,
    EyeOff,
    FileText,
    Filter,
    Fingerprint,
    Flame,
    Focus,
    Folder,
    Gauge,
    Gem,
    Globe,
    Image,
    ImagePlus,
    Images,
    Info,
    Key,
    Layers,
    LifeBuoy,
    Lightbulb,
    Lock,
    LogOut,
    Mail,
    MapPin,
    Maximize2,
    Menu,
    Monitor,
    MoveHorizontal,
    Palette,
    Phone,
    Play,
    Plus,
    Quote,
    RefreshCw,
    Rocket,
    Scan,
    ScanFace,
    ScanSearch,
    Search,
    Server,
    Settings,
    Share2,
    ShieldCheck,
    SlidersHorizontal,
    Smartphone,
    Sparkles,
    Star,
    Terminal,
    Timer,
    Trash2,
    TrendingUp,
    TriangleAlert,
    User,
    Users,
    WandSparkles,
    X,
    Zap,
};

/**
 * Replaces every `[data-lucide]` placeholder inside `root` with an inline SVG.
 * Safe to call repeatedly — call it again after injecting markup at runtime.
 *
 * @param {ParentNode} [root=document] Scope to render within.
 */
export function renderIcons(root = document) {
    createIcons({
        icons: registry,
        nameAttr: 'data-lucide',
        attrs: {
            'stroke-width': 1.75,
            'aria-hidden': 'true',
            focusable: 'false',
        },
        ...(root !== document ? { root } : {}),
    });
}

/**
 * Watches for `[data-lucide]` placeholders added at runtime (Alpine templates,
 * toasts, modals, AJAX fragments) and renders them without any markup hooks.
 */
export function observeIcons(target = document.body) {
    if (!('MutationObserver' in window) || !target) return;

    let pending = false;

    const observer = new MutationObserver((mutations) => {
        if (pending) return;

        const needsRender = mutations.some((mutation) =>
            Array.from(mutation.addedNodes).some(
                (node) =>
                    node.nodeType === 1 &&
                    (node.matches?.('[data-lucide]') || node.querySelector?.('[data-lucide]'))
            )
        );

        if (!needsRender) return;

        pending = true;
        window.requestAnimationFrame(() => {
            renderIcons();
            pending = false;
        });
    });

    observer.observe(target, { childList: true, subtree: true });
}

export default { renderIcons, observeIcons };
