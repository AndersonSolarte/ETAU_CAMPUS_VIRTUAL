<?php
/**
 * TAU — Paleta vino tinto pura
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

/* ── LINKS ── */
a:not(.btn):not(.nav-link):not(.navbar-brand):not(.dropdown-item):not([class*="tau-"]) {
    color: #c62b3a !important;
}
a:not(.btn):not(.nav-link):not(.navbar-brand):not(.dropdown-item):not([class*="tau-"]):hover {
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

/* ── TARJETAS CURSO ── */
.card.dashboard-card:hover {
    border-top: 3px solid #c62b3a !important;
    box-shadow: 0 20px 44px rgba(198,43,58,.12), 0 4px 14px rgba(0,0,0,.06) !important;
}
.card.dashboard-card .card-title a:hover,
.card.dashboard-card:hover .card-title a {
    color: #c62b3a !important;
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

/* ── DARK MODE ── */
[data-bs-theme="dark"] a:not(.btn):not(.nav-link):not(.navbar-brand):not(.dropdown-item) {
    color: #e87a84 !important;
}
[data-bs-theme="dark"] a:not(.btn):not(.nav-link):not(.navbar-brand):not(.dropdown-item):hover {
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
