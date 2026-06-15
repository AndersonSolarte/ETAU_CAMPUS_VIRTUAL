<?php
/**
 * TAU — Paleta unificada v1
 * Vino tinto #c62b3a → estructura (navbar, footer, headers de sección)
 * Azul       #0d4f8b → acciones (botones, links, progreso, íconos activos)
 * Se agrega al final del SCSS existente (no lo borra).
 */
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$patch = <<<'PATCH'

/* ═══════════════════════════════════════════════════════════════════════
   TAU — PALETA UNIFICADA: Vino #c62b3a (estructura) · Azul #0d4f8b (acción)
   ═══════════════════════════════════════════════════════════════════════ */

/* ── LINKS: azul institucional ── */
a:not(.btn):not(.nav-link):not(.navbar-brand):not(.dropdown-item):not([class*="tau-"]) {
    color: #0d4f8b !important;
}
a:not(.btn):not(.nav-link):not(.navbar-brand):not(.dropdown-item):not([class*="tau-"]):hover {
    color: #0a3d6b !important;
}

/* ── BOTONES PRIMARIOS: azul (acción) ── */
.btn-primary,
#page-login-index .btn-primary,
.btn[data-action="enrolself"],
button.btn-primary,
input[type="submit"].btn-primary {
    background: linear-gradient(135deg, #0a3d6b 0%, #0d4f8b 100%) !important;
    border-color: #0d4f8b !important;
    color: #fff !important;
    box-shadow: 0 4px 14px rgba(13,79,139,.25) !important;
    transition: all .2s !important;
}
.btn-primary:hover, .btn-primary:focus,
button.btn-primary:hover, input[type="submit"].btn-primary:hover {
    background: linear-gradient(135deg, #082f52 0%, #0a3d6b 100%) !important;
    border-color: #0a3d6b !important;
    box-shadow: 0 6px 20px rgba(13,79,139,.32) !important;
    transform: translateY(-1px) !important;
}

/* ── BOTONES SECUNDARIOS / OUTLINE: vino ── */
.btn-secondary, .btn-outline-secondary {
    border-color: #c62b3a !important;
    color: #c62b3a !important;
    background: transparent !important;
}
.btn-secondary:hover, .btn-outline-secondary:hover {
    background: #c62b3a !important;
    color: #fff !important;
}

/* ── BARRA DE PROGRESO: azul ── */
.progress-bar,
.progress-bar[role="progressbar"] {
    background: linear-gradient(90deg, #0d4f8b 0%, #1a72c4 100%) !important;
}

/* ── ÍCONOS ACTIVOS / FOCUS: azul ── */
.nav-tabs .nav-link.active,
.nav-pills .nav-link.active {
    background-color: #0d4f8b !important;
    border-color: #0d4f8b !important;
    color: #fff !important;
}
.form-check-input:checked,
input[type="checkbox"]:checked,
input[type="radio"]:checked {
    background-color: #0d4f8b !important;
    border-color: #0d4f8b !important;
}
.form-control:focus, .form-select:focus, textarea:focus, input:focus {
    border-color: #0d4f8b !important;
    box-shadow: 0 0 0 3px rgba(13,79,139,.15) !important;
    outline: none !important;
}

/* ── BADGE / ETIQUETAS: contexto ── */
.badge.bg-primary, .badge-primary { background-color: #0d4f8b !important; }
.badge.bg-danger,  .badge-danger  { background-color: #c62b3a !important; }

/* ── DASHBOARD BIENVENIDA: azul profundo (para contrastar con navbar vino) ── */
#tau-personal-dashboard .tau-pd-header,
.tau-pd-header {
    background:
        radial-gradient(circle at 85% 20%, rgba(255,255,255,.12) 0%, transparent 35%),
        linear-gradient(135deg, #071c3a 0%, #0a2547 30%, #0d4f8b 70%, #1a72c4 100%) !important;
}
#tau-personal-dashboard .tau-pd-header::after,
.tau-pd-header::after {
    content: "" !important;
    position: absolute !important;
    bottom: 0 !important; left: 0 !important;
    width: 100% !important; height: 3px !important;
    background: linear-gradient(90deg, #c62b3a 0%, rgba(198,43,58,0) 60%) !important;
}

/* ── TARJETAS CURSO — hover azul ── */
.card.dashboard-card:hover {
    border-top: 3px solid #0d4f8b !important;
    box-shadow: 0 20px 44px rgba(13,79,139,.12), 0 4px 14px rgba(0,0,0,.05) !important;
}
.card.dashboard-card .card-title a:hover,
.card.dashboard-card:hover .card-title a {
    color: #0d4f8b !important;
}

/* ── SECONDARY NAV ACTIVA: vino (mantiene coherencia con navbar) ── */
.secondary-navigation .nav-tabs .nav-link.active {
    color: #c62b3a !important;
    border-bottom-color: #c62b3a !important;
    background: transparent !important;
    font-weight: 700 !important;
}
.secondary-navigation .nav-tabs .nav-link:hover {
    color: #c62b3a !important;
}

/* ── STAT BOXES: alternar vino / azul ── */
#numbers .rate-box.bg-primary {
    background: linear-gradient(135deg, #0a2547 0%, #0d4f8b 100%) !important;
    box-shadow: 0 10px 28px rgba(13,79,139,.20) !important;
}
#numbers .rate-box.bg-cloudburst,
#numbers .rate-box-2,
#numbers .rate-box:nth-child(even) {
    background: linear-gradient(135deg, #8e1e2c 0%, #c62b3a 100%) !important;
    box-shadow: 0 10px 28px rgba(198,43,58,.20) !important;
}

/* ── CATEGORÍAS HEADER: azul ── */
.tau-cat-header {
    background: linear-gradient(135deg, #071c3a 0%, #0a2547 30%, #0d4f8b 70%, #1a72c4 100%) !important;
}
.tau-cat-header::after {
    background: linear-gradient(90deg, #c62b3a, transparent) !important;
}

/* ── TOOLTIPS Y POPOVERS ── */
.tooltip-inner { background-color: #0a2547 !important; }
.tooltip .tooltip-arrow::before { border-top-color: #0a2547 !important; }

/* ── DARK MODE AJUSTES ── */
[data-bs-theme="dark"] a:not(.btn):not(.nav-link):not(.navbar-brand):not(.dropdown-item) {
    color: #93c5fd !important;
}
[data-bs-theme="dark"] a:not(.btn):not(.nav-link):not(.navbar-brand):not(.dropdown-item):hover {
    color: #bfdbfe !important;
}
[data-bs-theme="dark"] .btn-primary {
    background: linear-gradient(135deg, #0d4f8b 0%, #1a72c4 100%) !important;
    border-color: #1a72c4 !important;
}

/* ── FIN PALETA UNIFICADA ── */
PATCH;

// Quitar parche anterior si existe
$scss = get_config('theme_moove', 'scss');
$marker = '/* ═══════════════════════════════════════════════════════════════════════' . "\n   TAU — PALETA UNIFICADA";
$pos = strpos($scss, $marker);
if ($pos !== false) {
    $scss = rtrim(substr($scss, 0, $pos));
}

set_config('scss', $scss . "\n" . $patch, 'theme_moove');

// Asegurar tokens de tema correctos
set_config('brandcolor',       '#c62b3a', 'theme_moove');
set_config('buttonbrandcolor', '#0d4f8b', 'theme_moove');
set_config('linkcolor',        '#0d4f8b', 'theme_moove');

theme_reset_all_caches();
echo "Paleta unificada aplicada\n";
echo "scss total: " . strlen(get_config('theme_moove','scss')) . " bytes\n";
echo "Vino (navbar/footer/headers): #c62b3a\n";
echo "Azul (botones/links/progreso): #0d4f8b\n";
