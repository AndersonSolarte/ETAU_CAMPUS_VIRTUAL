<?php
/**
 * TAU — Paleta vino tinto pura con modelo de tarjetas institucionales
 * Color único: vino #c62b3a  |  texto: blanco sobre oscuro
 * Botones: vino sólido con blanco, o blanco con borde vino
 */
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$patch = <<<'PATCH'

/* ═══════════════════════════════════════════════════════════════════
   TAU — PALETA VINO ÚNICO v1
   Primario: #c62b3a  |  Oscuro: #8e1e2c  |  Claro: #e05567
   Texto sobre oscuro: #ffffff
   ═══════════════════════════════════════════════════════════════════ */

/* ── LINKS (Excluyendo .coursename para no pintar los títulos de rojo) ── */
a:not(.btn):not(.nav-link):not(.navbar-brand):not(.dropdown-item):not([class*="tau-"]):not(.coursename) {
    color: #c62b3a !important;
}
a:not(.btn):not(.nav-link):not(.navbar-brand):not(.dropdown-item):not([class*="tau-"]):not(.coursename):hover {
    color: #8e1e2c !important;
    text-decoration: underline !important;
}

/* ── BOTÓN PRIMARIO — vino sólido, texto blanco ── */
.btn-primary,
button.btn-primary,
input[type="submit"].btn-primary,
.btn[data-action="enrolself"] {
    background: linear-gradient(135deg, #a32230 0%, #c62b3a 60%, #d63d4d 100%) !important;
    border: none !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    letter-spacing: .02em !important;
    box-shadow: 0 4px 16px rgba(198,43,58,.35), inset 0 1px 0 rgba(255,255,255,.15) !important;
    border-radius: 10px !important;
    transition: all .2s !important;
}
.btn-primary:hover, .btn-primary:focus,
button.btn-primary:hover, input[type="submit"].btn-primary:hover {
    background: linear-gradient(135deg, #8e1e2c 0%, #a32230 60%, #c62b3a 100%) !important;
    box-shadow: 0 6px 22px rgba(198,43,58,.42), inset 0 1px 0 rgba(255,255,255,.12) !important;
    transform: translateY(-1px) !important;
    color: #ffffff !important;
}
.btn-primary:active {
    transform: translateY(0) !important;
    box-shadow: 0 2px 8px rgba(198,43,58,.30) !important;
}

/* ── BOTÓN SECUNDARIO — blanco con borde vino, texto vino ── */
.btn-secondary,
.btn-outline-primary,
.btn-outline-secondary,
.btn-default {
    background: #ffffff !important;
    border: 2px solid #c62b3a !important;
    color: #c62b3a !important;
    font-weight: 700 !important;
    border-radius: 10px !important;
    transition: all .2s !important;
    box-shadow: none !important;
}
.btn-secondary:hover, .btn-outline-primary:hover,
.btn-outline-secondary:hover, .btn-default:hover {
    background: #c62b3a !important;
    border-color: #c62b3a !important;
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(198,43,58,.28) !important;
    transform: translateY(-1px) !important;
}

/* ── BOTÓN TEXTO / LINK ── */
.btn-link { color: #c62b3a !important; }
.btn-link:hover { color: #8e1e2c !important; }

/* ── BARRA DE PROGRESO ── */
.progress-bar { background: linear-gradient(90deg, #8e1e2c 0%, #c62b3a 100%) !important; }

/* ── FORM CONTROLS ── */
.form-control:focus, .form-select:focus, textarea:focus, input:focus {
    border-color: #c62b3a !important;
    box-shadow: 0 0 0 3px rgba(198,43,58,.14) !important;
    outline: none !important;
}
.form-check-input:checked, input[type="checkbox"]:checked, input[type="radio"]:checked {
    background-color: #c62b3a !important;
    border-color: #c62b3a !important;
}

/* ── TABS ACTIVAS ── */
.nav-tabs .nav-link.active, .nav-pills .nav-link.active {
    background-color: #c62b3a !important;
    border-color: #c62b3a !important;
    color: #ffffff !important;
}
.secondary-navigation .nav-tabs .nav-link.active {
    color: #c62b3a !important;
    border-bottom-color: #c62b3a !important;
    background: transparent !important;
    font-weight: 700 !important;
}
.secondary-navigation .nav-tabs .nav-link:hover { color: #c62b3a !important; }

/* ── BADGES ── */
.badge.bg-primary, .badge-primary,
.badge.bg-danger,  .badge-danger {
    background-color: #c62b3a !important;
    color: #ffffff !important;
}

/* ── DASHBOARD BIENVENIDA — vino oscuro ── */
.tau-pd-header {
    background:
        radial-gradient(circle at 85% 20%, rgba(255,255,255,.10) 0%, transparent 35%),
        radial-gradient(circle at 10% 80%, rgba(0,0,0,.20) 0%, transparent 40%),
        linear-gradient(135deg, #4b0f18 0%, #6f1520 25%, #9e1b2e 55%, #c62b3a 85%, #d63d4d 100%) !important;
    color: #ffffff !important;
}
.tau-pd-header::after {
    content: "" !important; position: absolute !important;
    bottom: 0 !important; left: 0 !important;
    width: 100% !important; height: 2px !important;
    background: rgba(255,255,255,.20) !important;
}

/* ── TARJETAS CURSO (Native Overview & Dashboard Cards as 3D Books) ── */
.card.dashboard-card.tau-card-ready, .card.course-card.tau-card-ready {
    perspective: 2500px !important;
    transform-style: preserve-3d !important;
    background: #faf8f5 !important; /* Solid book back cover base */
    border: 1px solid rgba(12, 57, 90, 0.15) !important;
    border-radius: 18px !important;
    box-shadow: 0 4px 14px rgba(0,0,0,0.06) !important;
    position: relative !important;
    display: flex !important;
    flex-direction: column !important;
    cursor: pointer !important;
    overflow: visible !important; /* Allow 3D book cover rotation and dropdown overflow */
    transition: box-shadow 0.5s ease, border-color 0.5s ease !important;
}

[data-bs-theme="dark"] .card.dashboard-card.tau-card-ready,
[data-bs-theme="dark"] .card.course-card.tau-card-ready {
    background: #252538 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 4px 20px rgba(0,0,0,0.4) !important;
}

.card.dashboard-card.tau-card-ready:hover,
.card.course-card.tau-card-ready:hover {
    box-shadow: 0 15px 35px rgba(12, 57, 90, 0.12) !important;
    border-color: rgba(12, 57, 90, 0.22) !important;
}

[data-bs-theme="dark"] .card.dashboard-card.tau-card-ready:hover,
[data-bs-theme="dark"] .card.course-card.tau-card-ready:hover {
    box-shadow: 0 15px 35px rgba(0,0,0,0.5) !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
}

/* 3D Book Cover */
.tau-book-cover {
    width: 100% !important;
    height: 100% !important;
    background: #ffffff !important;
    border: 1px solid rgba(12, 57, 90, 0.15) !important;
    border-radius: 18px !important;
    position: relative !important;
    z-index: 5 !important;
    display: flex !important;
    flex-direction: column !important;
    transform-origin: left center !important;
    transform: rotateY(0deg) translateZ(0px) !important;
    transition: transform 0.5s cubic-bezier(0.25, 1, 0.2, 1), box-shadow 0.5s !important;
    box-shadow: 0 4px 14px rgba(0,0,0,0.06), 0 1px 3px rgba(0,0,0,0.03) !important;
    /* Spine effect on the left */
    border-left: 5px solid #0c395a !important;
    overflow: hidden !important;
    will-change: transform;
    backface-visibility: hidden;
}

[data-bs-theme="dark"] .tau-book-cover {
    background: #1e1e2e !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}

/* 3D Open Book Hover effect (subtle, scaled, shifted to prevent overflow and keep text crisp) */
.card.dashboard-card.tau-card-ready:hover .tau-book-cover,
.card.course-card.tau-card-ready:hover .tau-book-cover {
    transform: scale(0.96) translateX(10px) rotateY(-14deg) translateZ(4px) !important;
    box-shadow: -6px 8px 18px rgba(12, 57, 90, 0.16) !important;
}

/* Align pages scale/translation with the cover on hover to prevent sticking out */
.card.dashboard-card.tau-card-ready:hover .tau-book-pages,
.card.course-card.tau-card-ready:hover .tau-book-pages {
    transform: scale(0.96) translateX(10px) translateZ(-2px) !important;
}

/* Prevent book rotation when action menu / dropdown is open */
.card.dashboard-card.tau-card-ready:has(.show) .tau-book-cover,
.card.course-card.tau-card-ready:has(.show) .tau-book-cover,
.card.dashboard-card.tau-card-ready:has([aria-expanded="true"]) .tau-book-cover,
.card.course-card.tau-card-ready:has([aria-expanded="true"]) .tau-book-cover {
    transform: rotateY(0deg) translateZ(0px) !important;
    box-shadow: 0 4px 14px rgba(0,0,0,0.06) !important;
}

.card.dashboard-card.tau-card-ready:has(.show) .tau-book-pages,
.card.course-card.tau-card-ready:has(.show) .tau-book-pages,
.card.dashboard-card.tau-card-ready:has([aria-expanded="true"]) .tau-book-pages,
.card.course-card.tau-card-ready:has([aria-expanded="true"]) .tau-book-pages {
    transform: translateZ(-2px) !important;
}

/* Book Inner Pages */
.tau-book-pages {
    position: absolute !important;
    top: 2px !important;
    left: 2px !important;
    width: calc(100% - 4px) !important;
    height: calc(100% - 4px) !important;
    background: #faf8f5 !important; /* Creamy paper */
    border-radius: 16px !important;
    z-index: 2 !important; /* Sits underneath cover (z-index 5) but above card background */
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 15px !important;
    border: 1px solid rgba(0,0,0,0.06) !important;
    box-shadow: inset 12px 0 24px rgba(0,0,0,0.03), 4px 4px 12px rgba(0,0,0,0.04) !important;
    box-sizing: border-box !important;
    transform: translateZ(-2px) !important;
    overflow: hidden !important;
    transition: transform 0.5s cubic-bezier(0.25, 1, 0.2, 1) !important;
    will-change: transform;
    backface-visibility: hidden;
}

[data-bs-theme="dark"] .tau-book-pages {
    background: #252538 !important;
    border-color: rgba(255, 255, 255, 0.05) !important;
    box-shadow: inset 12px 0 24px rgba(0,0,0,0.15) !important;
}

.tau-book-pages-content {
    text-align: center !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 8px !important;
}

.tau-book-pages-text {
    font-size: 0.72rem !important;
    color: #64748b !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}

[data-bs-theme="dark"] .tau-book-pages-text {
    color: #94a3b8 !important;
}

.tau-book-pages-btn {
    background: linear-gradient(135deg, #a32230 0%, #c62b3a 60%, #d63d4d 100%) !important;
    color: #ffffff !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.8px !important;
    padding: 6px 16px !important;
    border-radius: 20px !important;
    border: none !important;
    box-shadow: 0 4px 12px rgba(198, 43, 58, 0.3) !important;
    margin-top: 5px !important;
    transition: all 0.2s ease !important;
}

.tau-book-pages-btn:hover {
    transform: scale(1.05) !important;
    box-shadow: 0 6px 16px rgba(198, 43, 58, 0.4) !important;
}

.card.dashboard-card .card-img-top,
.card.dashboard-card img.card-img-top,
.card.dashboard-card .card-img,
.card.dashboard-card .dashboard-card-img,
.card.course-card .card-img-top,
.card.course-card img.card-img-top {
    height: 115px !important;
    width: 100% !important;
    object-fit: cover !important;
    display: block !important;
    flex-shrink: 0 !important;
    transition: transform .45s ease !important;
}

.card.dashboard-card.tau-card-ready:hover .card-img-top,
.card.dashboard-card.tau-card-ready:hover img.card-img-top,
.card.dashboard-card.tau-card-ready:hover .dashboard-card-img,
.card.course-card.tau-card-ready:hover .card-img-top,
.card.course-card.tau-card-ready:hover img.card-img-top {
    transform: scale(1.07) !important;
}

.card.dashboard-card .card-body,
.card.course-card .card-body {
    padding: 1rem 1.15rem .85rem !important;
    flex: 1 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 0 !important;
}

.card.dashboard-card .card-title,
.card.course-card .card-title {
    margin-bottom: .42rem !important;
    line-height: 1.3 !important;
}

.card.dashboard-card .card-title a,
.card.dashboard-card .card-title a.aalink,
.card.dashboard-card h3.card-title a,
.card.course-card .card-title a,
.card.course-card .card-title a.aalink,
.card.course-card h3.card-title a {
    font-size: .95rem !important;
    font-weight: 700 !important;
    color: #083259 !important; /* Corporate dark blue */
    text-transform: uppercase !important; /* Uppercase like mockup */
    text-decoration: none !important;
    display: block !important;
    line-clamp: none !important;
    -webkit-line-clamp: none !important;
    overflow: visible !important;
    white-space: normal !important;
    transition: color .18s !important;
}

.card.dashboard-card.tau-card-ready:hover .card-title a,
.card.course-card.tau-card-ready:hover .card-title a {
    color: #c62b3a !important; /* Wine red hover color */
}

.card.dashboard-card .card-text,
.card.course-card .card-text {
    display: none !important;
}

.card.dashboard-card .progress,
.card.course-card .progress {
    height: 5px !important;
    border-radius: 6px !important;
    background: #f0f0f0 !important;
    margin-bottom: .4rem !important;
}

.card.dashboard-card .progress-bar,
.card.course-card .progress-bar {
    background: linear-gradient(90deg, #c62b3a 0%, #e87a84 100%) !important;
    border-radius: 6px !important;
}

.card.dashboard-card .card-footer,
.card.course-card .card-footer {
    padding: 0 !important;
    background: transparent !important;
    border: none !important;
}

/* Dark theme updates for cards */
[data-bs-theme="dark"] .card.dashboard-card,
[data-bs-theme="dark"] .card.course-card {
    background: #1e1e2e !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 3px 18px rgba(0,0,0,.32) !important;
}
[data-bs-theme="dark"] .card.dashboard-card:hover,
[data-bs-theme="dark"] .card.course-card:hover {
    box-shadow: 0 28px 60px rgba(0,0,0,.42), 0 8px 20px rgba(198,43,58,.16) !important;
}
[data-bs-theme="dark"] .card.dashboard-card .card-title a,
[data-bs-theme="dark"] .card.course-card .card-title a {
    color: #f0f0f0 !important;
}
[data-bs-theme="dark"] .card.dashboard-card:hover .card-title a,
[data-bs-theme="dark"] .card.course-card:hover .card-title a {
    color: #e8717c !important;
}
[data-bs-theme="dark"] .card.dashboard-card .card-text,
[data-bs-theme="dark"] .card.course-card .card-text {
    color: #888 !important;
}
[data-bs-theme="dark"] .card.dashboard-card .progress,
[data-bs-theme="dark"] .card.course-card .progress {
    background: #333 !important;
}


/* ── INSTITUTIONAL CARD MODEL REDESIGN (Centered Logo Header) ── */

.tau-gradient-header {
    height: 125px !important;
    background: #0c395a !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    position: relative !important;
    overflow: hidden !important;
    padding: 8px !important;
    transition: background-color 0.25s ease !important;
}

.tau-gradient-header::after {
    display: none !important; /* Hide black shadow overlay */
}

/* Institutional Logo in Header */
.tau-card-logo {
    max-height: 100px !important;
    max-width: 95% !important;
    object-fit: contain !important;
    z-index: 1 !important;
}

/* Category Badge update - light cyan/blue banner style from mockup */
.tau-cat-badge {
    font-size: .72rem !important;
    font-weight: 700 !important;
    letter-spacing: .3px !important;
    text-transform: capitalize !important;
    color: #0369a1 !important; /* Dark sky blue */
    background: #e0f2fe !important; /* Light sky blue background */
    border: none !important;
    border-radius: 4px !important; /* Rounded corners banner */
    padding: 3px 10px !important;
    display: inline-block !important;
    margin-bottom: 10px !important;
    max-width: 100% !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    align-self: flex-start !important;
}

[data-bs-theme="dark"] .tau-cat-badge {
    background: rgba(3, 105, 161, 0.15) !important;
    border: 1px solid rgba(3, 105, 161, 0.25) !important;
    color: #38bdf8 !important;
}

/* Program title overlay in the blue header */
.tau-card-program-title {
    position: absolute !important;
    bottom: 6px !important;
    left: 0 !important;
    right: 0 !important;
    text-align: center !important;
    font-size: 0.74rem !important;
    font-weight: 800 !important;
    color: #ffffff !important;
    text-transform: uppercase !important;
    letter-spacing: 0.8px !important;
    padding: 0 12px !important;
    white-space: normal !important;
    line-height: 1.25 !important;
    display: block !important;
    overflow: visible !important;
    z-index: 2 !important;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4) !important;
}

/* Hide original redundant category texts on course cards */
.card.dashboard-card .coursecat,
.card.dashboard-card .course-category,
.card.dashboard-card [data-region='coursecategory'],
.card.dashboard-card .categoryname,
.card.course-card .coursecat,
.card.course-card .course-category,
.card.course-card [data-region='coursecategory'],
.card.course-card .categoryname {
    display: none !important;
}

/* Three vertical dots dropdown position inside course cards (moved to top-right of header) */
.card.dashboard-card, .card.course-card {
    position: relative !important;
}

.card.dashboard-card .dropdown,
.card.dashboard-card .action-menu,
.card.course-card .dropdown,
.card.course-card .action-menu {
    position: absolute !important;
    top: 10px !important;
    right: 10px !important;
    margin: 0 !important;
    z-index: 100 !important;
    transform: translateZ(30px) !important;
}

/* Ensure Moodle's actual dropdown menu renders completely on top in 3D */
.card.dashboard-card .dropdown-menu,
.card.course-card .dropdown-menu {
    z-index: 1000 !important;
    transform: translateZ(40px) !important;
    background: #ffffff !important;
    border: 1px solid rgba(12, 57, 90, 0.12) !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 30px rgba(12, 57, 90, 0.15) !important;
    padding: 6px 0 !important;
    min-width: 170px !important;
    overflow: hidden !important;
}

[data-bs-theme="dark"] .card.dashboard-card .dropdown-menu,
[data-bs-theme="dark"] .card.course-card .dropdown-menu {
    background: #1e1e2e !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
}

/* Style the links inside the dropdown menu to be readable and professional */
.card.dashboard-card .dropdown-menu a.dropdown-item,
.card.dashboard-card .dropdown-menu .dropdown-item,
.card.course-card .dropdown-menu a.dropdown-item,
.card.course-card .dropdown-menu .dropdown-item {
    color: #0c395a !important; /* Corporate dark blue */
    background: transparent !important;
    border: none !important;
    border-radius: 0 !important;
    padding: 8px 16px !important;
    font-size: 0.82rem !important;
    font-weight: 600 !important;
    text-transform: none !important; /* Don't force uppercase for menu options */
    display: block !important;
    width: 100% !important;
    height: auto !important;
    box-shadow: none !important;
    text-align: left !important;
    transition: all 0.15s ease !important;
}

[data-bs-theme="dark"] .card.dashboard-card .dropdown-menu a.dropdown-item,
[data-bs-theme="dark"] .card.dashboard-card .dropdown-menu .dropdown-item,
[data-bs-theme="dark"] .card.course-card .dropdown-menu a.dropdown-item,
[data-bs-theme="dark"] .card.course-card .dropdown-menu .dropdown-item {
    color: #cbd5e1 !important;
}

/* Hover style for dropdown menu items */
.card.dashboard-card .dropdown-menu a.dropdown-item:hover,
.card.dashboard-card .dropdown-menu .dropdown-item:hover,
.card.course-card .dropdown-menu a.dropdown-item:hover,
.card.course-card .dropdown-menu .dropdown-item:hover {
    background: rgba(198, 43, 58, 0.08) !important; /* Soft wine red tint */
    color: #c62b3a !important; /* Wine red text */
}

[data-bs-theme="dark"] .card.dashboard-card .dropdown-menu a.dropdown-item:hover,
[data-bs-theme="dark"] .card.dashboard-card .dropdown-menu .dropdown-item:hover,
[data-bs-theme="dark"] .card.course-card .dropdown-menu a.dropdown-item:hover,
[data-bs-theme="dark"] .card.course-card .dropdown-menu .dropdown-item:hover {
    background: rgba(232, 122, 132, 0.12) !important;
    color: #e87a84 !important;
}

/* Target only the dropdown toggle button (the three dots icon) */
.card.dashboard-card .dropdown-toggle,
.card.course-card .dropdown-toggle {
    color: #ffffff !important;
    background: transparent !important; /* Plain transparent background as requested */
    border: none !important; /* No borders */
    border-radius: 0 !important;
    width: 32px !important;
    height: 32px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 !important;
    transition: transform 0.2s ease !important;
    box-shadow: none !important;
}

.card.dashboard-card .dropdown-toggle:hover,
.card.course-card .dropdown-toggle:hover {
    transform: scale(1.15) !important;
    color: #ffffff !important;
    background: transparent !important;
}

/* Make inner icons white and properly sized */
.card.dashboard-card .dropdown-toggle i,
.card.course-card .dropdown-toggle i,
.card.dashboard-card .dropdown-toggle svg,
.card.course-card .dropdown-toggle svg {
    color: #ffffff !important;
    fill: #ffffff !important;
    font-size: 14px !important;
    width: auto !important;
    height: auto !important;
    margin: 0 !important;
}

/* Hide caret arrow for dropdown in cards */
.card.dashboard-card .dropdown-toggle::after,
.card.course-card .dropdown-toggle::after {
    display: none !important;
}

/* ── NEW DEDICATED TEACHER SECTION IN CARD BODY ── */
.tau-card-teacher-section {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    margin-top: auto !important; /* Push to the bottom of the card body */
    padding-top: 10px !important;
    border-top: 1px solid rgba(0,0,0,.06) !important;
}

.tau-teacher-avatar-wrapper {
    width: 32px !important;
    height: 32px !important;
    border-radius: 50% !important;
    overflow: hidden !important;
    flex-shrink: 0 !important;
    background: #f1f5f9 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    border: 1.5px solid #e2e8f0 !important;
}

.tau-teacher-avatar-img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    border-radius: 50% !important;
}

.tau-teacher-avatar-placeholder {
    width: 100% !important;
    height: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #94a3b8 !important;
}

.tau-teacher-avatar-placeholder svg {
    width: 16px !important;
    height: 16px !important;
    fill: currentColor !important;
}

.tau-teacher-info {
    display: flex !important;
    flex-direction: column !important;
    gap: 1px !important;
    overflow: hidden !important;
    flex: 1 !important;
}

.tau-teacher-label {
    font-size: 0.65rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    color: #94a3b8 !important;
    font-weight: 700 !important;
}

.tau-teacher-name {
    font-size: 0.8rem !important;
    font-weight: 600 !important;
    color: #334155 !important;
    white-space: normal !important;
    word-break: break-word !important;
}

/* Dark theme updates for teacher section */
[data-bs-theme="dark"] .tau-card-teacher-section {
    border-top-color: rgba(255,255,255,.06) !important;
}
[data-bs-theme="dark"] .tau-teacher-avatar-wrapper {
    background: #1e293b !important;
    border-color: #334155 !important;
}
[data-bs-theme="dark"] .tau-teacher-name {
    color: #cbd5e1 !important;
}
[data-bs-theme="dark"] .tau-teacher-label {
    color: #64748b !important;
}


/* ── STAT BOXES ── */
#numbers .rate-box.bg-primary {
    background: linear-gradient(135deg, #4b0f18 0%, #8e1e2c 50%, #c62b3a 100%) !important;
    box-shadow: 0 10px 28px rgba(198,43,58,.22) !important;
}
#numbers .rate-box.bg-cloudburst, #numbers .rate-box-2,
#numbers .rate-box:nth-child(even) {
    background: linear-gradient(135deg, #8e1e2c 0%, #c62b3a 60%, #d63d4d 100%) !important;
    box-shadow: 0 10px 28px rgba(198,43,58,.18) !important;
}
#numbers .rate-box { border: 1px solid rgba(255,255,255,.10) !important; }

/* ── CATEGORÍAS ── */
.tau-cat-header {
    background: linear-gradient(135deg, #4b0f18 0%, #6f1520 25%, #9e1b2e 55%, #c62b3a 85%, #d63d4d 100%) !important;
}
.tau-cat-card:hover {
    background: #fff5f6 !important;
    border-color: rgba(198,43,58,.25) !important;
}
.tau-cat-card:hover .tau-cat-arr { color: #c62b3a !important; }
.tau-cat-toggle:hover { background: #fff5f6 !important; border-color: #c62b3a !important; color: #c62b3a !important; }
.tau-courses-main-btn {
    background: linear-gradient(135deg, #8e1e2c 0%, #c62b3a 100%) !important;
    box-shadow: 0 8px 24px rgba(198,43,58,.28) !important;
}
.tau-courses-main-btn:hover {
    background: linear-gradient(135deg, #6f1520 0%, #9e1b2e 100%) !important;
    box-shadow: 0 12px 30px rgba(198,43,58,.36) !important;
}

/* ── SEMESTER TABS STYLING (Professional Interface) ── */
#tau-semester-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 15px 0 25px 0;
    padding: 0;
    width: 100%;
}

.tau-tab-btn {
    background: #ffffff;
    color: #495057;
    font-size: 0.86rem;
    font-weight: 600;
    border: 1px solid #dee2e6;
    border-radius: 20px; /* Pill shapes */
    padding: 7px 18px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.03);
}

.tau-tab-btn:hover {
    background: rgba(198,43,58,0.04);
    border-color: rgba(198,43,58,0.25);
    color: #c62b3a;
}

.tau-tab-btn.active {
    background: #c62b3a !important;
    color: #ffffff !important;
    border-color: #c62b3a !important;
    box-shadow: 0 4px 10px rgba(198,43,58,0.25) !important;
}

[data-bs-theme="dark"] #tau-semester-tabs .tau-tab-btn {
    background: #1e1e2e;
    color: #c8d6e5;
    border-color: #3f3f5a;
}
[data-bs-theme="dark"] #tau-semester-tabs .tau-tab-btn:hover {
    background: rgba(198,43,58,0.08);
    color: #e87a84;
}
[data-bs-theme="dark"] #tau-semester-tabs .tau-tab-btn.active {
    background: #c62b3a !important;
    color: #ffffff !important;
    border-color: #c62b3a !important;
}


/* ── DARK MODE ── */
[data-bs-theme="dark"] a:not(.btn):not(.nav-link):not(.navbar-brand):not(.dropdown-item):not(.coursename) {
    color: #e87a84 !important;
}
[data-bs-theme="dark"] a:not(.btn):not(.nav-link):not(.navbar-brand):not(.dropdown-item):not(.coursename):hover {
    color: #f0a0a8 !important;
}
[data-bs-theme="dark"] .btn-primary {
    background: linear-gradient(135deg, #a32230 0%, #c62b3a 100%) !important;
    border: none !important;
}
[data-bs-theme="dark"] .btn-secondary, [data-bs-theme="dark"] .btn-outline-primary {
    background: rgba(198,43,58,.12) !important;
    border-color: rgba(198,43,58,.45) !important;
    color: #e87a84 !important;
}
[data-bs-theme="dark"] .btn-secondary:hover {
    background: #c62b3a !important;
    color: #fff !important;
}

/* ── FIN PALETA VINO ── */
PATCH;

// Quitar parches anteriores
$scss = get_config('theme_moove', 'scss');
foreach ([
    '/* ═══════════════════════════════════════════════════════════════════════' . "\n   TAU — PALETA UNIFICADA",
    '/* ═══════════════════════════════════════════════════════════════════' . "\n   TAU — PALETA VINO ÚNICO",
] as $marker) {
    $pos = strpos($scss, $marker);
    if ($pos !== false) $scss = rtrim(substr($scss, 0, $pos));
}

set_config('scss', $scss . "\n" . $patch, 'theme_moove');
set_config('brandcolor',       '#c62b3a', 'theme_moove');
set_config('buttonbrandcolor', '#c62b3a', 'theme_moove');
set_config('linkcolor',        '#c62b3a', 'theme_moove');

theme_reset_all_caches();
echo "Paleta vino único aplicada\n";
echo "scss: " . strlen(get_config('theme_moove','scss')) . " bytes\n";
