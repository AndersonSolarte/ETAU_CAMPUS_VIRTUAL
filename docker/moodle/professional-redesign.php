<?php
/**
 * TAU Campus Virtual — Rediseño profesional
 * Paleta: Azul profundo #0a2547 (base) + Carmesí #c0253a (acento) + Blanco
 * Filosofía: azul = institución/confianza, rojo = acción/energía, blanco = limpieza
 */
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// ═══════════════════════════════════════════════════════════════════════════════
// PALETA PROFESIONAL
// ═══════════════════════════════════════════════════════════════════════════════
// Azules: #0a2547 (más oscuro) › #0d4f8b (institucional) › #1a72c4 (medio) › #e8f1fb (tint)
// Rojos:  #9e1b2e (oscuro)    › #c0253a (institucional)  › #e05c6e (claro)
// Neutros: #0f172a (texto)    › #f8fafc (fondo) › #ffffff (blanco)

$css = <<<'NEWCSS'

/* ═══════════════════════════════════════════════════════════════════════
   TAU — PALETA PROFESIONAL v10
   Azul profundo #0a2547 · Carmesí #c0253a · Blanco
   ═══════════════════════════════════════════════════════════════════════ */

/* ── ANIMACIONES ── */
@keyframes fadeSlideDown { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)} }
@keyframes loginCardIn   { from{opacity:0;transform:translateY(28px) scale(.96)} to{opacity:1;transform:translateY(0) scale(1)} }
@keyframes tauToastIn    { from{opacity:0;transform:translate(-50%,-50%) scale(.90)} to{opacity:1;transform:translate(-50%,-50%) scale(1)} }

/* ── TIPOGRAFÍA ── */
body { font-family:'Manrope',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; }

/* ── NAVBAR: azul profundo con sutileza de luz ── */
.navbar {
    background:
        radial-gradient(circle at 8% 50%, rgba(255,255,255,.10) 0%, transparent 40%),
        linear-gradient(135deg, #071c3a 0%, #0a2547 35%, #0d3868 65%, #0d4f8b 100%) !important;
    box-shadow: 0 4px 24px rgba(7,28,58,.35) !important;
    border-bottom: 1px solid rgba(255,255,255,.07) !important;
}
.navbar .nav-link, .navbar-brand, .primary-navigation .nav-link {
    color: rgba(255,255,255,.90) !important;
    transition: color .2s !important;
    background: transparent !important;
    border-radius: 0 !important;
    box-shadow: none !important;
}
.navbar .nav-link:hover { color: #fff !important; background: rgba(255,255,255,.08) !important; border-radius: 8px !important; }
.navbar .nav-link.active,  .navbar .nav-link.show,
.primary-navigation .nav-link.active, .primary-navigation .nav-link[aria-current="page"],
.primary-navigation .nav-link[aria-selected="true"],
.moremenu.navigation .nav-link.active, .moremenu.navigation .nav-link.show,
.moremenu.navigation .nav-link[aria-current="page"] {
    color: #fff !important; font-weight: 700 !important;
    background: transparent !important; position: relative !important;
    border-radius: 0 !important; box-shadow: none !important;
    border-bottom: 0 !important;
}
.navbar .nav-link.active::after,
.primary-navigation .nav-link.active::after,
.primary-navigation .nav-link[aria-current="page"]::after,
.moremenu.navigation .nav-link.active::after {
    content:"" !important; position:absolute !important;
    left:.9rem !important; right:.9rem !important; bottom:.3rem !important;
    height:2px !important; border-radius:999px !important;
    background: rgba(255,255,255,.80) !important;
}
.navbar .nav-item { margin-right:.2rem !important; }
.navbar .icon, .navbar .fa, .navbar button.btn-icon { color:rgba(255,255,255,.85) !important; }
.navbar .dropdown-toggle::after { border-top-color:rgba(255,255,255,.75) !important; }
/* Barra roja carmesí al borde inferior del navbar */
.navbar::after {
    content:"" !important; position:absolute !important;
    left:0 !important; bottom:0 !important; width:100% !important; height:3px !important;
    background: linear-gradient(90deg, #c0253a 0%, #c0253a 50%, transparent 50%) !important;
    opacity:.7 !important; pointer-events:none !important;
}

/* Botón Acceder — blanco con texto azul */
.notloggedin .navbar .usermenu .login a {
    font-size:.92rem !important; line-height:1 !important;
    background:#ffffff !important; border:1px solid rgba(255,255,255,.3) !important;
    border-radius:10px !important; padding:10px 20px !important;
    font-weight:800 !important; color:#0a2547 !important;
    text-decoration:none !important; white-space:nowrap !important;
    box-shadow:0 4px 14px rgba(0,0,0,.18) !important;
    transition:all .2s !important;
}
.notloggedin .navbar .usermenu .login a:hover {
    background:#f0f5ff !important; color:#071c3a !important;
    transform:translateY(-1px) !important; box-shadow:0 6px 20px rgba(0,0,0,.22) !important;
}
.logininfo a, .navbar .logininfo a {
    font-size:.875rem !important; background:rgba(255,255,255,.15) !important;
    border:1.5px solid rgba(255,255,255,.55) !important; border-radius:20px !important;
    padding:6px 20px !important; font-weight:700 !important; color:#fff !important;
    text-decoration:none !important; display:inline-block !important;
    transition:all .2s !important; white-space:nowrap !important;
}
.logininfo a:hover {
    background:rgba(255,255,255,.28) !important; transform:translateY(-1px) !important;
    box-shadow:0 4px 14px rgba(0,0,0,.2) !important; color:#fff !important;
}

/* ── DROPDOWN ── */
.dropdown-menu, .usermenu .dropdown-menu, .navbar .dropdown-menu, .action-menu .dropdown-menu {
    background:#fff !important; border:1px solid rgba(0,0,0,.07) !important;
    box-shadow:0 16px 40px rgba(10,37,71,.14), 0 4px 12px rgba(0,0,0,.06) !important;
    border-radius:14px !important; padding:7px !important; min-width:210px !important;
    animation:fadeSlideDown .18s cubic-bezier(.34,1.56,.64,1) both;
}
.dropdown-menu .dropdown-item, .dropdown-menu a {
    color:#1e293b !important; border-radius:8px !important;
    padding:9px 13px !important; font-size:.88rem !important;
    transition:background .12s, color .12s !important;
    display:flex !important; align-items:center !important; gap:8px !important;
}
.dropdown-menu .dropdown-item:hover, .dropdown-menu a:hover {
    background:#f0f5ff !important; color:#0d4f8b !important;
}
.dropdown-menu .dropdown-item .icon:hover, .dropdown-menu a:hover .icon { color:#0d4f8b !important; }

/* ── NAV SECUNDARIA ── */
.secondary-navigation, .tertiary-navigation {
    background:#fff !important; box-shadow:0 2px 6px rgba(0,0,0,.06) !important;
}
.secondary-navigation .nav-tabs .nav-link { color:#64748b !important; border:none !important; border-bottom:3px solid transparent !important; border-radius:0 !important; padding:10px 18px !important; font-size:.875rem !important; font-weight:500 !important; }
.secondary-navigation .nav-tabs .nav-link:hover { color:#0d4f8b !important; background:rgba(13,79,139,.05) !important; }
.secondary-navigation .nav-tabs .nav-link.active { color:#0d4f8b !important; font-weight:700 !important; border-bottom-color:#0d4f8b !important; background:transparent !important; }

/* ── BOTONES GLOBALES ── */
/* Azul = acción estándar */
.btn-primary {
    background: linear-gradient(135deg, #0a2547 0%, #0d4f8b 100%) !important;
    border-color:#0d4f8b !important; color:#fff !important;
    box-shadow:0 4px 14px rgba(13,79,139,.28) !important;
    transition:all .22s !important;
}
.btn-primary:hover, .btn-primary:focus {
    background: linear-gradient(135deg, #071c3a 0%, #0a3d6b 100%) !important;
    border-color:#0a3d6b !important;
    box-shadow:0 6px 20px rgba(13,79,139,.38) !important;
    transform:translateY(-1px) !important;
}
/* Rojo = llamada a la acción principal (hero, inscripción) */
.btn-tau-cta, .btn-tau-banner-explore {
    background: linear-gradient(135deg, #9e1b2e 0%, #c0253a 100%) !important;
    border:none !important; color:#fff !important; border-radius:12px !important;
    padding:12px 28px !important; font-weight:700 !important; font-size:.9rem !important;
    box-shadow:0 6px 20px rgba(192,37,58,.28) !important;
    transition:all .22s !important; display:inline-flex !important; align-items:center !important;
    justify-content:center !important; text-decoration:none !important;
}
.btn-tau-cta:hover, .btn-tau-banner-explore:hover {
    transform:translateY(-2px) !important;
    box-shadow:0 10px 28px rgba(192,37,58,.38) !important; color:#fff !important;
    background: linear-gradient(135deg, #7d1524 0%, #a81f31 100%) !important;
}
a { color:#0d4f8b; }
a:hover { color:#0a2547; }

/* ── LOGIN ── */
[data-bs-theme="light"] body#page-login-index,
:not([data-bs-theme="dark"]) body#page-login-index,
body#page-login-index #page {
    background:
        url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%230d4f8b' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='1.5'/%3E%3C/g%3E%3C/svg%3E"),
        radial-gradient(ellipse at 12% 50%, rgba(192,37,58,.07) 0%, transparent 55%),
        radial-gradient(ellipse at 88% 15%, rgba(13,79,139,.10) 0%, transparent 50%),
        linear-gradient(135deg, #f0f5ff 0%, #f8fafc 45%, #edf2ff 100%) !important;
    background-color:#f8fafc !important;
}
[data-bs-theme="dark"] body#page-login-index,
[data-bs-theme="dark"] #page-login-index #page {
    background:
        radial-gradient(ellipse at 12% 50%, rgba(192,37,58,.18) 0%, transparent 55%),
        radial-gradient(ellipse at 88% 15%, rgba(13,79,139,.22) 0%, transparent 50%),
        linear-gradient(135deg, #060f1e 0%, #0a1835 45%, #071c3a 100%) !important;
    background-color:#060f1e !important;
}
#page-login-index form#login, #page-login-index .login-form,
#page-login-index .loginform, #page-login-index .login-form-username,
#page-login-index .login-form-password, #page-login-index .login-form-submit,
#page-login-index .login-form-forgotpassword, #page-login-index .login-divider,
#page-login-index .login-identityproviders h2, #page-login-index #username,
#page-login-index #password, #page-login-index label[for="username"],
#page-login-index label[for="password"], #page-login-index input[name="username"],
#page-login-index input[name="password"] { display:none !important; }
#page-login-index .login-identityprovider-btn {
    font-size:1rem; padding:12px 24px; border-radius:10px;
    border:1.5px solid #dee2e6; background:#fff; color:#1e293b;
    transition:box-shadow .2s, border-color .2s, transform .15s !important; font-weight:500;
}
#page-login-index .login-identityprovider-btn:hover {
    box-shadow:0 8px 28px rgba(13,79,139,.18) !important;
    border-color:#0d4f8b !important; transform:translateY(-2px) !important;
}
.tau-login-welcome { text-align:center; margin:10px 0 18px; }
.tau-campus-inst { font-size:.62rem; font-weight:600; letter-spacing:2px; text-transform:uppercase; color:#94a3b8; margin-bottom:3px; }
.tau-campus-title { font-size:1.1rem; font-weight:800; color:#0a2547; letter-spacing:-.2px; }
.tau-campus-divider { width:36px; height:3px; background:linear-gradient(90deg,#0d4f8b 0%,#c0253a 100%); border-radius:2px; margin:8px auto 10px; }
.tau-login-welcome p { font-size:.82rem; color:#94a3b8; margin:0; }
[data-bs-theme="dark"] .tau-campus-title { color:#f0f0f0; }

/* ── NAV DRAWER ── */
#nav-drawer { background:#fff !important; }
[data-bs-theme="dark"] #nav-drawer { background:#0a1835 !important; }

/* ── DARK MODE ── */
html[data-bs-theme="dark"] { color-scheme:dark; }
[data-bs-theme="dark"] body, [data-bs-theme="dark"] html { background-color:#060f1e !important; color:#e2e8f0 !important; }
[data-bs-theme="dark"] #page-wrapper, [data-bs-theme="dark"] #page { background-color:#060f1e !important; }
[data-bs-theme="dark"] .bg-white, [data-bs-theme="dark"] #page.bg-white,
[data-bs-theme="dark"] #page.drawers, [data-bs-theme="dark"] .drawers.bg-white { background-color:#060f1e !important; }
[data-bs-theme="dark"] .moove-container, [data-bs-theme="dark"] .moove-container-fluid { background:#060f1e !important; }
[data-bs-theme="dark"] .navbar {
    background: radial-gradient(circle at 8% 50%, rgba(255,255,255,.07) 0%, transparent 40%),
        linear-gradient(135deg, #04111f 0%, #071c3a 35%, #0a2547 65%, #0d3868 100%) !important;
}
[data-bs-theme="dark"] .card, [data-bs-theme="dark"] .generalbox { background:#0d1f3a !important; border-color:#1e3a5f !important; }
[data-bs-theme="dark"] .dropdown-menu { background:#0d1f3a !important; border-color:rgba(255,255,255,.08) !important; }
[data-bs-theme="dark"] .dropdown-menu .dropdown-item { color:#cbd5e1 !important; }
[data-bs-theme="dark"] .dropdown-menu .dropdown-item:hover { background:rgba(13,79,139,.2) !important; color:#93c5fd !important; }
[data-bs-theme="dark"] .secondary-navigation { background:#0d1f3a !important; }
[data-bs-theme="dark"] .secondary-navigation .nav-tabs .nav-link { color:#94a3b8 !important; }
[data-bs-theme="dark"] .secondary-navigation .nav-tabs .nav-link.active { color:#93c5fd !important; border-bottom-color:#93c5fd !important; }
[data-bs-theme="dark"] a:not(.btn):not(.nav-link):not(.dropdown-item) { color:#93c5fd !important; }
[data-bs-theme="dark"] a:not(.btn):not(.nav-link):not(.dropdown-item):hover { color:#bfdbfe !important; }
[data-bs-theme="dark"] #tau-theme-toggle { background:#0d1f3a !important; color:rgba(255,255,255,.7) !important; }
[data-bs-theme="dark"] #numbers { background:#060f1e !important; }
[data-bs-theme="dark"] #feature { background:#060f1e !important; }
[data-bs-theme="dark"] .tau-cat-body { background:linear-gradient(180deg, #0d1f3a 0%, #0a1835 100%); }
[data-bs-theme="dark"] .tau-cat-card { background:linear-gradient(135deg, #0f2342 0%, #0a1a34 100%); border-color:rgba(255,255,255,.06); }
[data-bs-theme="dark"] .tau-cat-card:hover { border-color:rgba(13,79,139,.3); background:#122a50; }
[data-bs-theme="dark"] .tau-cat-nm { color:#e2e8f0; }
[data-bs-theme="dark"] .tau-cat-toggle { background:#0d1f3a; border-color:rgba(13,79,139,.3); color:#93c5fd; }

/* ── FOOTER ── */
.moove-footer, #page-footer .footer-dark, #page-footer, footer#page-footer, #page-footer .container-fluid {
    background: linear-gradient(135deg, #04111f 0%, #071c3a 40%, #0a2547 100%) !important;
    color:rgba(255,255,255,.80) !important;
    border-top: 3px solid #c0253a !important;
}
#page-footer a:not(.btn):not(.nav-link) { color:rgba(255,255,255,.65) !important; }
#page-footer a:not(.btn):not(.nav-link):hover { color:#fff !important; }
#page-footer h3, #page-footer h4 { color:#fff !important; }
a[href*="conecti.me"], img[alt*="Conecti"],
.footer-content-popover .footer-section:last-of-type { display:none !important; }
[data-bs-theme="dark"] .moove-footer, [data-bs-theme="dark"] #page-footer .footer-dark {
    background: linear-gradient(135deg, #030a14 0%, #04111f 50%, #071c3a 100%) !important;
}

/* ── FAB (Tutor IA) ── */
#tau-ai-fab {
    position:fixed; bottom:122px; right:16px; z-index:1055;
    display:inline-flex; align-items:center; gap:7px;
    background: linear-gradient(135deg, #0a2547 0%, #c0253a 100%);
    color:#fff !important; text-decoration:none !important;
    border-radius:22px; padding:10px 16px 10px 14px;
    font-size:.82rem; font-weight:700;
    box-shadow:0 4px 18px rgba(10,37,71,.4);
    transition:transform .15s, box-shadow .15s;
}
#tau-ai-fab:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(10,37,71,.5); }
#tau-theme-toggle {
    position:fixed; bottom:70px; right:20px; z-index:1055;
    width:44px; height:44px; border-radius:50%;
    background: linear-gradient(135deg, #0a2547, #0d4f8b);
    border:none; color:#fff; cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    box-shadow:0 4px 12px rgba(10,37,71,.35);
    transition:all .2s !important;
}
#tau-theme-toggle:hover { background: linear-gradient(135deg, #071c3a, #0a3d6b); transform:scale(1.08); }

/* ── TARJETAS DE CURSO ── */
.card.dashboard-card, .coursebox {
    border:none !important; border-radius:18px !important; overflow:hidden !important;
    box-shadow:0 3px 14px rgba(10,37,71,.08), 0 1px 4px rgba(0,0,0,.04) !important;
    transition:transform .28s cubic-bezier(.34,1.56,.64,1), box-shadow .28s !important;
    background:#fff !important; position:relative !important;
    display:flex !important; flex-direction:column !important;
    border-top:3px solid transparent !important;
}
.card.dashboard-card:hover, .coursebox:hover {
    transform:translateY(-8px) !important;
    box-shadow:0 24px 50px rgba(10,37,71,.12), 0 6px 18px rgba(13,79,139,.10) !important;
    border-top-color:#0d4f8b !important;
}
.card.dashboard-card .card-img-top, .card.dashboard-card img.card-img-top {
    height:168px !important; width:100% !important; object-fit:cover !important;
    display:block !important; transition:transform .45s ease !important;
}
.card.dashboard-card:hover .card-img-top { transform:scale(1.06) !important; }
.card.dashboard-card .card-body {
    padding:1rem 1.15rem .85rem !important; flex:1 !important;
    display:flex !important; flex-direction:column !important;
}
.tau-cat-badge {
    font-size:.56rem; font-weight:700; letter-spacing:.5px;
    text-transform:uppercase; color:#0d4f8b;
    background:rgba(13,79,139,.06); border:1px solid rgba(13,79,139,.12);
    border-radius:20px; padding:2px 8px; display:inline-block;
    margin-bottom:7px; align-self:flex-start;
}
.card.dashboard-card .card-title a, .card.dashboard-card .card-title a.aalink {
    font-size:.96rem !important; font-weight:700 !important;
    color:#0f172a !important; text-decoration:none !important;
    display:-webkit-box !important; -webkit-line-clamp:2 !important;
    -webkit-box-orient:vertical !important; overflow:hidden !important;
    transition:color .18s !important;
}
.card.dashboard-card:hover .card-title a { color:#0d4f8b !important; }
.card.dashboard-card .card-text { display:none !important; }
.card.dashboard-card .progress {
    height:5px !important; border-radius:6px !important;
    background:#e2e8f0 !important; margin-bottom:.4rem !important;
}
.card.dashboard-card .progress-bar {
    background: linear-gradient(90deg, #0d4f8b 0%, #1a72c4 100%) !important;
    border-radius:6px !important;
}
.card.dashboard-card .card-footer { padding:0 !important; background:transparent !important; border:none !important; }
.tau-teacher-row {
    display:flex; align-items:center; gap:5px;
    margin-top:auto; padding-top:.55rem;
    border-top:1px solid rgba(0,0,0,.05);
    font-size:.74rem; color:#94a3b8; overflow:hidden;
}
.tau-tr-name { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; flex:1; font-weight:600; color:#64748b; }
[data-bs-theme="dark"] .card.dashboard-card { background:#0d1f3a !important; box-shadow:0 3px 18px rgba(0,0,0,.32) !important; }
[data-bs-theme="dark"] .card.dashboard-card:hover { box-shadow:0 24px 50px rgba(0,0,0,.42), 0 6px 18px rgba(13,79,139,.2) !important; border-top-color:#1a72c4 !important; }
[data-bs-theme="dark"] .card.dashboard-card .card-title a { color:#e2e8f0 !important; }
[data-bs-theme="dark"] .card.dashboard-card:hover .card-title a { color:#93c5fd !important; }
[data-bs-theme="dark"] .tau-cat-badge { background:rgba(13,79,139,.15) !important; border-color:rgba(13,79,139,.25) !important; color:#93c5fd !important; }
[data-bs-theme="dark"] .tau-teacher-row { border-top-color:rgba(255,255,255,.06); }

/* ── SECCIÓN NÚMEROS / STATS ── */
#numbers { background:#fff !important; padding:5.625rem 0 !important; }
[data-bs-theme="dark"] #numbers { background:#060f1e !important; }
#numbers .rate-box {
    border-radius:22px !important; padding:36px 40px !important;
    transition:transform .25s, box-shadow .25s !important;
    border:1px solid rgba(255,255,255,.10) !important;
}
#numbers .rate-box.bg-primary {
    background: linear-gradient(135deg, #0a2547 0%, #0d4f8b 100%) !important;
    box-shadow:0 10px 30px rgba(10,37,71,.22) !important;
}
#numbers .rate-box.bg-cloudburst, #numbers .rate-box-2 {
    background: linear-gradient(135deg, #c0253a 0%, #9e1b2e 100%) !important;
    box-shadow:0 10px 30px rgba(192,37,58,.22) !important;
}
#numbers .rate-box:hover { transform:translateY(-4px) !important; }
#numbers .rate-box h3 { font-family:'Manrope',sans-serif !important; color:#fff !important; font-size:56px !important; font-weight:800 !important; line-height:1.1 !important; margin-bottom:18px !important; letter-spacing:-1.5px !important; }
#numbers .rate-box p { font-family:'Manrope',sans-serif !important; color:rgba(255,255,255,.88) !important; font-size:.85rem !important; font-weight:700 !important; letter-spacing:.8px !important; text-transform:uppercase !important; }
#numbers .sectionheading h2 { font-family:'Manrope',sans-serif !important; font-size:2.2rem !important; font-weight:800 !important; color:#0a2547 !important; }
[data-bs-theme="dark"] #numbers .sectionheading h2 { color:#e2e8f0 !important; }

/* ── SECCIÓN CATEGORÍAS ── */
.tau-cat-section { margin:2.5rem 0; border-radius:24px; overflow:hidden; box-shadow:0 20px 44px rgba(10,37,71,.07), 0 4px 14px rgba(10,37,71,.03); border:1px solid rgba(13,79,139,.06); }
.tau-cat-header {
    background: linear-gradient(135deg, #071c3a 0%, #0a2547 30%, #0d4f8b 70%, #1a72c4 100%);
    padding:2.2rem 2.5rem 1.8rem; color:#fff; position:relative; overflow:hidden;
}
.tau-cat-header::before { content:''; position:absolute; inset:0; background:radial-gradient(circle at 88% 24%, rgba(255,255,255,.12) 0%, transparent 30%); pointer-events:none; }
.tau-cat-header > * { position:relative; z-index:1; }
.tau-cat-header::after { content:''; position:absolute; bottom:0; left:0; width:100%; height:3px; background:linear-gradient(90deg, #c0253a, transparent); }
.tau-cat-kicker { display:inline-flex; align-items:center; gap:8px; padding:6px 14px; border-radius:999px; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.18); font-size:.7rem; font-weight:800; letter-spacing:.08em; text-transform:uppercase; margin-bottom:.85rem; }
.tau-cat-htitle { font-size:1.6rem; font-weight:800; letter-spacing:-.03em; margin:0 0 .35rem; }
.tau-cat-sub { font-size:.88rem; color:rgba(255,255,255,.82); max-width:650px; margin:0; line-height:1.55; }
.tau-cat-body { background:linear-gradient(180deg, #f8faff 0%, #fff 100%); padding:2rem; }
.tau-cat-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; }
@media(max-width:991px){ .tau-cat-grid{grid-template-columns:repeat(2,1fr);} }
@media(max-width:575px){ .tau-cat-grid{grid-template-columns:1fr;} }
.tau-cat-card { display:flex; align-items:center; gap:18px; padding:24px 28px; border-radius:20px; border:1px solid rgba(13,79,139,.07); background:#fff; text-decoration:none !important; color:inherit !important; transition:all .25s; cursor:pointer; box-shadow:0 4px 15px rgba(10,37,71,.03); }
.tau-cat-card:hover { transform:translateY(-5px); box-shadow:0 12px 28px rgba(13,79,139,.12), 0 4px 10px rgba(0,0,0,.03); border-color:rgba(13,79,139,.18); background:#f0f7ff; }
.tau-cat-nm { font-size:.95rem; font-weight:700; color:#0f172a; flex:1; line-height:1.35; }
.tau-cat-arr { font-size:1.2rem; color:#cbd5e1; transition:all .2s; }
.tau-cat-card:hover .tau-cat-arr { color:#0d4f8b; transform:translateX(4px); }
.tau-cat-toggle { display:inline-flex; align-items:center; gap:8px; padding:10px 28px; border-radius:999px; border:1.5px solid rgba(13,79,139,.18); background:#fff; color:#0d4f8b; font-size:.82rem; font-weight:700; cursor:pointer; transition:all .22s; }
.tau-cat-toggle:hover { background:#f0f7ff; border-color:#0d4f8b; transform:translateY(-2px); box-shadow:0 8px 20px rgba(13,79,139,.14); }
.tau-courses-main-btn { display:inline-flex; align-items:center; justify-content:center; gap:12px; padding:14px 44px; border-radius:999px; border:none; background: linear-gradient(135deg, #0a2547 0%, #0d4f8b 100%); color:#fff !important; font-size:.95rem; font-weight:700; cursor:pointer; box-shadow:0 10px 30px rgba(13,79,139,.25); transition:all .25s; }
.tau-courses-main-btn:hover { background: linear-gradient(135deg, #071c3a 0%, #0a3d6b 100%); box-shadow:0 15px 35px rgba(13,79,139,.35); transform:translateY(-3px); }
.tau-cat-details-title { font-size:1.5rem !important; font-weight:800 !important; color:#0d4f8b !important; margin:0 !important; }
.tau-course-header-color { height:8px; background: linear-gradient(90deg, #0d4f8b 0%, #c0253a 100%); }
.tau-course-action-btn { display:inline-block; padding:8px 20px; border-radius:12px; background:#0d4f8b; color:#fff !important; font-size:.82rem; font-weight:700; text-decoration:none !important; transition:all .2s; }
.tau-course-action-btn:hover { background:#0a3d6b; transform:translateY(-1px); }

/* ── DASHBOARD PERSONAL ── */
@keyframes tauDashIn { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
#tau-personal-dashboard { border-radius:24px; overflow:hidden; box-shadow:0 22px 44px rgba(10,37,71,.10); margin-bottom:1.9rem; background:#fff; border:1px solid rgba(13,79,139,.08); animation:tauDashIn .38s ease both; }
.tau-pd-header {
    background: linear-gradient(135deg, #071c3a 0%, #0a2547 30%, #0d4f8b 70%, #1a72c4 100%);
    padding:2.2rem 2.5rem 1.75rem; color:#fff; position:relative; overflow:hidden;
}
.tau-pd-header::before { content:''; position:absolute; inset:0; background:radial-gradient(circle at 88% 24%, rgba(255,255,255,.12) 0%, transparent 30%), radial-gradient(circle at 12% 78%, rgba(192,37,58,.14) 0%, transparent 40%); pointer-events:none; }
.tau-pd-header > * { position:relative; z-index:1; }
/* Acento rojo en la base del header del dashboard */
.tau-pd-header::after { content:''; position:absolute; bottom:0; left:0; width:100%; height:3px; background:linear-gradient(90deg, #c0253a, #0d4f8b, transparent); }

/* ── FRONTPAGE FEATURE CARDS ── */
#page-site-index.notloggedin .marketing-content-section .card {
    border:none; box-shadow:0 4px 20px rgba(10,37,71,.07);
    border-radius:14px; transition:transform .2s, box-shadow .2s !important;
    border-top:3px solid transparent !important;
}
#page-site-index.notloggedin .marketing-content-section .card:hover {
    transform:translateY(-6px) !important; border-top-color:#0d4f8b !important;
    box-shadow:0 12px 32px rgba(13,79,139,.14) !important;
}

/* ── BANNER HERO ── */
.tau-banner-card {
    background:rgba(7,28,58,.72) !important;
    backdrop-filter:blur(24px) !important; -webkit-backdrop-filter:blur(24px) !important;
    border:1px solid rgba(255,255,255,.14) !important;
    border-left:6px solid #c0253a !important;
    border-radius:28px !important; padding:36px 40px !important;
    max-width:480px !important; box-shadow:0 30px 70px rgba(7,28,58,.55) !important;
}
.tau-banner-pretitle { font-size:.78rem !important; font-weight:700 !important; letter-spacing:3px !important; color:rgba(255,255,255,.65) !important; text-transform:uppercase !important; margin-bottom:6px !important; display:block !important; }
.tau-banner-title { font-size:2.4rem !important; font-weight:800 !important; color:#fff !important; line-height:1.15 !important; margin-bottom:8px !important; letter-spacing:-.8px !important; }
.tau-accent-text { background:linear-gradient(90deg,#fff 0%,#93c5fd 100%) !important; -webkit-background-clip:text !important; -webkit-text-fill-color:transparent !important; }
.tau-banner-subtitle { font-size:.85rem !important; font-weight:700 !important; color:#fff !important; background: linear-gradient(135deg, #c0253a 0%, #9e1b2e 100%) !important; padding:4px 12px !important; border-radius:6px !important; display:inline-block !important; margin-bottom:16px !important; }
.tau-banner-desc { font-size:.92rem !important; color:rgba(255,255,255,.88) !important; line-height:1.6 !important; margin-bottom:24px !important; }

/* ── NOTIFICACIONES dark mode ── */
[data-bs-theme="dark"] .popover-region-container { background:#0d1f3a !important; border:1px solid rgba(255,255,255,.08) !important; }
[data-bs-theme="dark"] .content-item-container { background:#0d1f3a !important; border-bottom:1px solid rgba(255,255,255,.05) !important; }
[data-bs-theme="dark"] .content-item-container.unread { background:rgba(13,79,139,.10) !important; border-left:3px solid #0d4f8b !important; }
[data-bs-theme="dark"] .popover-region-seeall-text, [data-bs-theme="dark"] .see-all-link { color:#93c5fd !important; }

/* ── TOAST ── */
.tau-toast { position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; border-radius:16px; border-top:4px solid #0d4f8b; padding:24px 44px 20px 24px; box-shadow:0 20px 60px rgba(10,37,71,.2); max-width:420px; width:calc(100vw - 40px); z-index:9999; animation:tauToastIn .3s cubic-bezier(.34,1.56,.64,1) both; }
.tau-toast-title { font-weight:700; font-size:.9rem; color:#0d4f8b; margin-bottom:5px; }
.tau-toast-body { font-size:.84rem; color:#475569; margin-bottom:8px; line-height:1.5; }
.tau-toast-contact { font-size:.78rem; color:#94a3b8; }
.tau-toast-contact a { color:#0d4f8b; text-decoration:none; }

/* ── COLAPSIBLES CURSOS ── */
.tau-courses-collapsible { max-height:0; opacity:0; overflow:hidden; transition:max-height .6s, opacity .4s, margin-top .4s; }
.tau-courses-collapsible.show { max-height:4000px; opacity:1; margin-top:2rem; padding-top:1.5rem; border-top:1px dashed rgba(13,79,139,.15); }

/* ── ESCUDO hero ── */
#mooveslideshow { position:relative !important; overflow:visible !important; }
#mooveslideshow .carousel-inner { overflow:hidden !important; }
#mooveslideshow .carousel-item { min-height:clamp(300px,44vh,500px) !important; display:flex !important; align-items:center !important; }
#mooveslideshow .carousel-caption { position:relative !important; bottom:auto !important; left:auto !important; right:auto !important; max-width:54% !important; z-index:5 !important; }

/* TAU-ESCUDO-HERO ════════════════ */
#mooveslideshow::after {
    content:"" !important; position:absolute !important;
    right:6% !important; top:50% !important; transform:translateY(-50%) !important;
    width:clamp(200px,28%,300px) !important; aspect-ratio:1 !important;
    background-image:url("/theme/tau_branding/assets/official/cesmag-escudo-hero.png") !important;
    background-size:82% !important; background-repeat:no-repeat !important;
    background-position:center !important; background-color:transparent !important;
    border:none !important; outline:none !important;
    z-index:9 !important; pointer-events:none !important;
    filter:drop-shadow(0 6px 34px rgba(10,37,71,.65)) brightness(1.05) !important;
    animation:tau-escudo-in .9s cubic-bezier(.22,1,.36,1) both !important;
}
@keyframes tau-escudo-in {
    from{opacity:0;transform:translateY(-47%) scale(.88);}
    to{opacity:1;transform:translateY(-50%) scale(1);}
}
@media(max-width:900px){ #mooveslideshow::after{width:clamp(160px,22vw,210px);right:2%;} }
@media(max-width:768px){ #mooveslideshow::after{display:none !important;} }
/* TAU-ESCUDO-HERO-FIN ════════════ */
NEWCSS;

// ── FOOTER: aros + LEMA ───────────────────────────────────────────────────────
$footer_inject = <<<'FOOTER'
<style id="tau-v10-style">
/* Aros giratorios alrededor del escudo */
.tau-rings-wrap{position:absolute;right:6%;top:50%;transform:translateY(-50%);width:clamp(200px,28%,300px);aspect-ratio:1;pointer-events:none;z-index:12;overflow:visible;}
.tau-rings-wrap svg{width:100%;height:100%;overflow:visible;}
@keyframes tau-cw  {to{transform:rotate(360deg)}}
@keyframes tau-ccw {to{transform:rotate(-360deg)}}
@keyframes tau-pa  {0%,100%{opacity:.15}50%{opacity:.45}}
@keyframes tau-nd  {from{r:4;opacity:.40}to{r:6.5;opacity:1}}
@keyframes tau-nw  {from{r:3;opacity:.25}to{r:5;opacity:.80}}
.tau-gc {animation:tau-cw  22s linear infinite;transform-origin:100px 100px;transform-box:fill-box;}
.tau-gcc{animation:tau-ccw 32s linear infinite;transform-origin:100px 100px;transform-box:fill-box;}
.tau-pa {animation:tau-pa 4.5s ease-in-out infinite;}
.tau-nd {animation:tau-nd 3s ease-in-out infinite alternate;}
.tau-nd:nth-child(2){animation-delay:-.75s}.tau-nd:nth-child(3){animation-delay:-1.5s}.tau-nd:nth-child(4){animation-delay:-2.25s}
.tau-nw {animation:tau-nw 3.8s ease-in-out infinite alternate;}
.tau-nw:nth-child(2){animation-delay:-.95s}.tau-nw:nth-child(3){animation-delay:-1.9s}.tau-nw:nth-child(4){animation-delay:-2.85s}
/* LEMA */
.tau-lema{position:relative;text-align:center;overflow:hidden;background:linear-gradient(180deg,#060f1e 0%,#071c3a 60%,#060f1e 100%);border-top:2px solid #0d4f8b;border-bottom:1px solid rgba(13,79,139,.25);padding:16px 1rem 14px;}
.tau-lema::before{content:"";position:absolute;inset:0;pointer-events:none;background-image:radial-gradient(circle,rgba(13,79,139,.08) 1px,transparent 1px);background-size:28px 28px;}
.tau-lema-row{display:inline-flex;align-items:center;gap:16px;position:relative;}
.tau-lema-row::before{content:"";display:block;width:55px;height:1.5px;background:linear-gradient(90deg,transparent,#0d4f8b);}
.tau-lema-row::after{content:"";display:block;width:55px;height:1.5px;background:linear-gradient(90deg,#0d4f8b,transparent);}
.tau-lema-q{font-family:Georgia,"Times New Roman",serif;font-size:clamp(.80rem,1.1vw,.95rem);font-style:italic;color:rgba(255,255,255,.90);letter-spacing:.06em;margin:0;text-shadow:0 1px 8px rgba(0,0,0,.5);}
.tau-lema-q strong{color:#fff;font-style:normal;font-weight:700;letter-spacing:.13em;text-transform:uppercase;}
.tau-lema-acc{display:inline-block;width:8px;height:8px;border-radius:50%;background:linear-gradient(135deg,#c0253a,#0d4f8b);margin:0 4px;vertical-align:middle;}
.tau-lema-author{font-family:system-ui,Arial,sans-serif;font-size:clamp(.63rem,.88vw,.74rem);color:rgba(255,255,255,.40);letter-spacing:.10em;text-transform:uppercase;margin:5px 0 0;}
@media(max-width:768px){.tau-rings-wrap{display:none!important}}
</style>
<script id="tau-v10-script">
(function(){
    function go(){
        var ss=document.getElementById("mooveslideshow");
        if(!ss){setTimeout(go,300);return;}
        // Aros
        if(!document.querySelector(".tau-rings-wrap")){
            var w=document.createElement("div");
            w.className="tau-rings-wrap";
            w.innerHTML='<svg viewBox="-34 -34 268 268" xmlns="http://www.w3.org/2000/svg">'+
              '<circle class="tau-pa" cx="100" cy="100" r="95" fill="rgba(10,37,71,.14)" stroke="none"/>'+
              '<circle class="tau-pa" cx="100" cy="100" r="120" fill="none" stroke="rgba(13,79,139,.30)" stroke-width="1.4" stroke-dasharray="8 14"/>'+
              '<g class="tau-gcc">'+
                '<circle cx="100" cy="100" r="110" fill="none" stroke="rgba(255,255,255,.18)" stroke-width="1" stroke-dasharray="5 18"/>'+
                '<circle class="tau-nw" cx="100" cy="-10" r="4" fill="rgba(255,255,255,.75)"/>'+
                '<circle class="tau-nw" cx="210" cy="100" r="4" fill="rgba(255,255,255,.75)"/>'+
                '<circle class="tau-nw" cx="100" cy="210" r="4" fill="rgba(255,255,255,.75)"/>'+
                '<circle class="tau-nw" cx="-10" cy="100" r="4" fill="rgba(255,255,255,.75)"/>'+
              '</g>'+
              '<g class="tau-gc">'+
                '<circle cx="100" cy="100" r="98" fill="none" stroke="rgba(192,37,58,.55)" stroke-width="1.5" stroke-dasharray="16 8"/>'+
                '<circle class="tau-nd" cx="100" cy="2"   r="6" fill="#c0253a"/>'+
                '<circle class="tau-nd" cx="198" cy="100" r="6" fill="#c0253a"/>'+
                '<circle class="tau-nd" cx="100" cy="198" r="6" fill="#c0253a"/>'+
                '<circle class="tau-nd" cx="2"   cy="100" r="6" fill="#c0253a"/>'+
              '</g>'+
            '</svg>';
            ss.appendChild(w);
        }
        // Lema
        if(!document.querySelector(".tau-lema")){
            var lema=document.createElement("div");
            lema.className="tau-lema";
            lema.innerHTML='<div class="tau-lema-row">'+
              '<p class="tau-lema-q"><span class="tau-lema-acc"></span>&ldquo;<strong>Hombres nuevos</strong> para <strong>tiempos nuevos</strong>&rdquo;<span class="tau-lema-acc"></span></p>'+
            '</div>'+
            '<p class="tau-lema-author">Fray Guillermo de Castellana, OFMCap.</p>';
            var ref=ss.nextSibling;
            while(ref&&ref.nodeType===3)ref=ref.nextSibling;
            if(ref)ss.parentNode.insertBefore(lema,ref);
            else ss.parentNode.appendChild(lema);
        }
    }
    if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",go);
    else go();
})();
</script>
FOOTER;

// ── Guardar en BD ─────────────────────────────────────────────────────────────
// Reemplazar TODO el scss con el nuevo diseño
set_config('scss', $css, 'theme_moove');

// Colores base
set_config('brandcolor',       '#0d4f8b', 'theme_moove');
set_config('buttonbrandcolor', '#0d4f8b', 'theme_moove');
set_config('linkcolor',        '#0d4f8b', 'theme_moove');

// Footer: limpiar todo lo anterior (tau-v9, tau-escudo, etc.)
$footer = get_config('theme_moove', 'additionalhtmlfooter');
$clean_tags = [['<style','</style>'],['<script','</script>']];
$clean_kw   = ['tau-v9','tau-v10','tau-escudo','tau-rings','tau-lema','tau_init','tau_run','tau_hero'];
foreach ($clean_tags as [$open,$close]) {
    $changed = true;
    while ($changed) {
        $changed = false; $pos = 0;
        while (($s = strpos($footer,$open,$pos)) !== false) {
            $e = strpos($footer,$close,$s);
            if ($e === false) break;
            $block = substr($footer,$s,$e-$s+strlen($close));
            $hit = false;
            foreach ($clean_kw as $kw) { if (stripos($block,$kw)!==false){$hit=true;break;} }
            if ($hit) { $footer = substr($footer,0,$s).substr($footer,$e+strlen($close)); $changed=true; break; }
            $pos = $e+strlen($close);
        }
    }
}
set_config('additionalhtmlfooter', trim($footer)."\n".$footer_inject, 'theme_moove');

theme_reset_all_caches();
echo "Rediseño profesional v10 aplicado\n";
echo "scss: ".strlen(get_config('theme_moove','scss'))." bytes\n";
echo "footer: ".strlen(get_config('theme_moove','additionalhtmlfooter'))." bytes\n";
