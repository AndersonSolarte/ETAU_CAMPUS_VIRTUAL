<?php
/**
 * TAU Campus Virtual — Rediseño profesional v11
 * Filosofía: navbar ROJO (para que el logo azul/blanco resalte)
 *            contenido AZUL institucional + BLANCO (como el sitio oficial CESMAG)
 *            rojo solo en navbar, botones CTA y acentos
 */
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$css = <<<'CSS'

/* ═══════════════════════════════════════════════════════════════════════
   TAU — PALETA PROFESIONAL v11
   Navbar: Carmesí  #b8222f → #d42c3b
   Contenido: Azul #0d4f8b  |  Blanco  |  Gris suave #f8fafc
   ═══════════════════════════════════════════════════════════════════════ */

@keyframes fadeSlideDown { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)} }
@keyframes loginCardIn   { from{opacity:0;transform:translateY(28px) scale(.96)} to{opacity:1;transform:translateY(0) scale(1)} }
@keyframes tauDashIn     { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }

/* ── TIPOGRAFÍA ── */
body { font-family:'Manrope',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; }
a { color:#0d4f8b; }
a:hover { color:#0a3d6b; }

/* ══════════════════════════════════════════════════════════
   NAVBAR — ROJO CARMESÍ PARA QUE EL LOGO RESALTE
   ══════════════════════════════════════════════════════════ */
.navbar {
    background:
        radial-gradient(circle at 5% 50%, rgba(255,255,255,.12) 0%, transparent 35%),
        linear-gradient(135deg, #7a0e1a 0%, #9e1b2e 25%, #c0253a 55%, #d42c3b 80%, #c0253a 100%) !important;
    box-shadow: 0 4px 20px rgba(120,14,26,.40) !important;
    border-bottom: 1px solid rgba(255,255,255,.10) !important;
    position: relative !important;
}
/* Línea azul muy fina al borde inferior — detalle institucional */
.navbar::after {
    content:"" !important; position:absolute !important;
    left:0 !important; bottom:0 !important;
    width:100% !important; height:2px !important;
    background: linear-gradient(90deg, #0d4f8b 0%, rgba(13,79,139,.4) 60%, transparent 100%) !important;
    pointer-events:none !important;
}
.navbar .nav-link, .navbar-brand, .primary-navigation .nav-link {
    color: rgba(255,255,255,.92) !important;
    transition: color .18s, background .18s !important;
    background: transparent !important;
}
.navbar .nav-link:hover {
    color: #fff !important;
    background: rgba(255,255,255,.12) !important;
    border-radius: 8px !important;
}
/* Nav activo: subrayado blanco */
.navbar .nav-link.active, .navbar .nav-link.show,
.primary-navigation .nav-link.active,
.primary-navigation .nav-link[aria-current="page"],
.moremenu.navigation .nav-link.active {
    color: #fff !important; font-weight: 700 !important;
    background: transparent !important; border-radius: 0 !important;
    position: relative !important; box-shadow: none !important;
}
.navbar .nav-link.active::after,
.primary-navigation .nav-link.active::after,
.primary-navigation .nav-link[aria-current="page"]::after,
.moremenu.navigation .nav-link.active::after {
    content:"" !important; position:absolute !important;
    left:.9rem !important; right:.9rem !important; bottom:.25rem !important;
    height:2px !important; border-radius:999px !important;
    background:rgba(255,255,255,.85) !important;
}
.navbar .icon, .navbar .fa, .navbar button.btn-icon { color:rgba(255,255,255,.88) !important; }
.navbar .dropdown-toggle::after { border-top-color:rgba(255,255,255,.80) !important; }

/* Botón "Acceder" — azul sobre rojo para contraste y distinción */
.notloggedin .navbar .usermenu .login a,
.logininfo a, .navbar .logininfo a {
    background: #0d4f8b !important;
    border: none !important;
    border-radius: 10px !important;
    padding: 9px 22px !important;
    font-weight: 800 !important; font-size: .875rem !important;
    color: #fff !important;
    text-decoration: none !important; white-space: nowrap !important;
    box-shadow: 0 4px 14px rgba(0,0,0,.22) !important;
    transition: all .2s !important; display: inline-block !important;
}
.notloggedin .navbar .usermenu .login a:hover,
.logininfo a:hover { background: #0a3d6b !important; transform: translateY(-1px) !important; }

/* ── DROPDOWN ── */
.dropdown-menu, .usermenu .dropdown-menu, .navbar .dropdown-menu, .action-menu .dropdown-menu {
    background: #fff !important;
    border: 1px solid rgba(0,0,0,.08) !important;
    box-shadow: 0 16px 40px rgba(0,0,0,.12), 0 4px 12px rgba(0,0,0,.06) !important;
    border-radius: 14px !important; padding: 7px !important;
    animation: fadeSlideDown .18s cubic-bezier(.34,1.56,.64,1) both;
}
.dropdown-menu .dropdown-item, .dropdown-menu a {
    color: #1e293b !important; border-radius: 8px !important;
    padding: 9px 13px !important; font-size: .88rem !important;
    transition: background .12s !important;
    display: flex !important; align-items: center !important; gap: 8px !important;
}
.dropdown-menu .dropdown-item:hover { background: #f0f5ff !important; color: #0d4f8b !important; }

/* ── NAV SECUNDARIA ── */
.secondary-navigation { background: #fff !important; box-shadow: 0 2px 6px rgba(0,0,0,.06) !important; }
.secondary-navigation .nav-tabs .nav-link { color: #64748b !important; border: none !important; border-bottom: 3px solid transparent !important; border-radius: 0 !important; padding: 10px 18px !important; font-weight: 500 !important; }
.secondary-navigation .nav-tabs .nav-link:hover { color: #0d4f8b !important; background: rgba(13,79,139,.04) !important; }
.secondary-navigation .nav-tabs .nav-link.active { color: #0d4f8b !important; font-weight: 700 !important; border-bottom-color: #c0253a !important; }

/* ══════════════════════════════════════════════════════════
   BOTONES GLOBALES
   ══════════════════════════════════════════════════════════ */
/* Azul = acción estándar */
.btn-primary {
    background: linear-gradient(135deg, #0a3d6b 0%, #0d4f8b 100%) !important;
    border-color: #0d4f8b !important; color: #fff !important;
    box-shadow: 0 4px 14px rgba(13,79,139,.25) !important;
    transition: all .2s !important;
}
.btn-primary:hover {
    background: linear-gradient(135deg, #082f52 0%, #0a3d6b 100%) !important;
    border-color: #0a3d6b !important; transform: translateY(-1px) !important;
}
/* Rojo = CTA hero / inscripción */
.btn-tau-cta, .btn-tau-banner-explore {
    background: linear-gradient(135deg, #9e1b2e 0%, #c0253a 100%) !important;
    border: none !important; color: #fff !important; border-radius: 12px !important;
    padding: 12px 28px !important; font-weight: 700 !important;
    box-shadow: 0 6px 20px rgba(192,37,58,.28) !important;
    transition: all .22s !important; display: inline-flex !important;
    align-items: center !important; justify-content: center !important;
    text-decoration: none !important;
}
.btn-tau-cta:hover { transform: translateY(-2px) !important; color: #fff !important; background: linear-gradient(135deg, #7d1524 0%, #a81f31 100%) !important; }

/* ── LOGIN ── */
body#page-login-index #page {
    background:
        radial-gradient(ellipse at 10% 55%, rgba(192,37,58,.06) 0%, transparent 45%),
        radial-gradient(ellipse at 90% 15%, rgba(13,79,139,.07) 0%, transparent 45%),
        linear-gradient(160deg, #f8fafc 0%, #f0f5ff 50%, #fef2f2 100%) !important;
    background-color: #f8fafc !important;
}
[data-bs-theme="dark"] body#page-login-index #page {
    background: linear-gradient(160deg, #060f1e 0%, #0a1835 50%, #150208 100%) !important;
    background-color: #060f1e !important;
}

/* ── FOOTER ── */
.moove-footer, #page-footer .footer-dark, #page-footer, footer#page-footer, #page-footer .container-fluid {
    background: linear-gradient(135deg, #06090f 0%, #0a1835 40%, #0d2348 100%) !important;
    color: rgba(255,255,255,.78) !important;
    border-top: 3px solid #c0253a !important;
}
#page-footer a:not(.btn) { color: rgba(255,255,255,.60) !important; }
#page-footer a:not(.btn):hover { color: #fff !important; }

/* ══════════════════════════════════════════════════════════
   DASHBOARD BIENVENIDA — AZUL (contrasta con navbar rojo)
   ══════════════════════════════════════════════════════════ */
#tau-personal-dashboard { border-radius: 24px; overflow: hidden; box-shadow: 0 18px 40px rgba(10,37,71,.12); margin-bottom: 1.9rem; background: #fff; animation: tauDashIn .38s ease both; }
.tau-pd-header {
    background:
        radial-gradient(circle at 85% 20%, rgba(255,255,255,.13) 0%, transparent 35%),
        radial-gradient(circle at 10% 80%, rgba(192,37,58,.10) 0%, transparent 35%),
        linear-gradient(135deg, #071c3a 0%, #0a2547 30%, #0d4f8b 70%, #1a72c4 100%) !important;
    padding: 2.2rem 2.5rem 1.75rem; color: #fff; position: relative; overflow: hidden;
}
.tau-pd-header::after { content:""; position:absolute; bottom:0; left:0; width:100%; height:3px; background:linear-gradient(90deg, #c0253a 0%, rgba(192,37,58,0) 60%); }
.tau-pd-header > * { position: relative; z-index: 1; }

/* ══════════════════════════════════════════════════════════
   TARJETAS DE CURSO — BLANCO LIMPIO
   ══════════════════════════════════════════════════════════ */
.card.dashboard-card, .coursebox {
    border: none !important; border-radius: 16px !important; overflow: hidden !important;
    box-shadow: 0 2px 12px rgba(0,0,0,.07), 0 1px 4px rgba(0,0,0,.04) !important;
    transition: transform .28s cubic-bezier(.34,1.56,.64,1), box-shadow .28s !important;
    background: #fff !important;
    border-top: 3px solid transparent !important;
}
.card.dashboard-card:hover {
    transform: translateY(-7px) !important;
    box-shadow: 0 20px 44px rgba(13,79,139,.12), 0 4px 14px rgba(0,0,0,.05) !important;
    border-top-color: #0d4f8b !important;
}
.card.dashboard-card .card-img-top { height: 160px !important; width: 100% !important; object-fit: cover !important; display: block !important; transition: transform .4s !important; }
.card.dashboard-card:hover .card-img-top { transform: scale(1.05) !important; }
.card.dashboard-card .card-body { padding: 1rem 1.1rem .8rem !important; flex: 1 !important; display: flex !important; flex-direction: column !important; }
.card.dashboard-card .card-title a {
    font-size: .94rem !important; font-weight: 700 !important; color: #0f172a !important;
    text-decoration: none !important;
    display: -webkit-box !important; -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important; overflow: hidden !important;
    transition: color .18s !important;
}
.card.dashboard-card:hover .card-title a { color: #0d4f8b !important; }
.card.dashboard-card .progress { height: 4px !important; border-radius: 4px !important; background: #e2e8f0 !important; }
.card.dashboard-card .progress-bar { background: linear-gradient(90deg, #0d4f8b, #1a72c4) !important; border-radius: 4px !important; }

/* ══════════════════════════════════════════════════════════
   SECCIÓN HERO — ESCUDO Y LEMA
   ══════════════════════════════════════════════════════════ */
#mooveslideshow { position: relative !important; overflow: visible !important; }
#mooveslideshow .carousel-inner { overflow: hidden !important; }
#mooveslideshow .carousel-item { min-height: clamp(300px,44vh,500px) !important; display: flex !important; align-items: center !important; }
#mooveslideshow .carousel-caption { position: relative !important; bottom: auto !important; left: auto !important; right: auto !important; max-width: 54% !important; z-index: 5 !important; }

/* TAU-ESCUDO-HERO */
#mooveslideshow::after {
    content: "" !important; position: absolute !important;
    right: 6% !important; top: 50% !important; transform: translateY(-50%) !important;
    width: clamp(200px,28%,300px) !important; aspect-ratio: 1 !important;
    background-image: url("/theme/tau_branding/assets/official/cesmag-escudo-hero.png") !important;
    background-size: 82% !important; background-repeat: no-repeat !important;
    background-position: center !important; background-color: transparent !important;
    z-index: 9 !important; pointer-events: none !important;
    filter: drop-shadow(0 6px 34px rgba(0,0,0,.65)) brightness(1.05) !important;
    animation: tau-escudo-in .9s cubic-bezier(.22,1,.36,1) both !important;
}
@keyframes tau-escudo-in {
    from { opacity:0; transform:translateY(-47%) scale(.88); }
    to   { opacity:1; transform:translateY(-50%) scale(1); }
}
@media(max-width:900px){ #mooveslideshow::after{width:clamp(150px,22vw,200px);right:2%;} }
@media(max-width:768px){ #mooveslideshow::after{display:none !important;} }

/* ══════════════════════════════════════════════════════════
   SECCIÓN NÚMEROS / STATS
   ══════════════════════════════════════════════════════════ */
#numbers { background: #fff !important; padding: 5rem 0 !important; }
#numbers .rate-box { border-radius: 20px !important; padding: 32px 36px !important; transition: transform .25s, box-shadow .25s !important; }
#numbers .rate-box.bg-primary { background: linear-gradient(135deg, #0a2547 0%, #0d4f8b 100%) !important; box-shadow: 0 10px 28px rgba(13,79,139,.22) !important; }
#numbers .rate-box.bg-cloudburst, #numbers .rate-box-2 { background: linear-gradient(135deg, #9e1b2e 0%, #c0253a 100%) !important; box-shadow: 0 10px 28px rgba(192,37,58,.22) !important; }
#numbers .rate-box:hover { transform: translateY(-4px) !important; }
#numbers .rate-box h3 { color: #fff !important; font-size: 52px !important; font-weight: 800 !important; margin-bottom: 14px !important; letter-spacing: -1.5px !important; }
#numbers .rate-box p  { color: rgba(255,255,255,.88) !important; font-size: .82rem !important; font-weight: 700 !important; letter-spacing: .8px !important; text-transform: uppercase !important; }
#numbers .sectionheading h2 { font-size: 2.1rem !important; font-weight: 800 !important; color: #0a2547 !important; }

/* ══════════════════════════════════════════════════════════
   SECCIÓN CATEGORÍAS
   ══════════════════════════════════════════════════════════ */
.tau-cat-section { margin: 2.5rem 0; border-radius: 22px; overflow: hidden; box-shadow: 0 16px 40px rgba(10,37,71,.07); border: 1px solid rgba(13,79,139,.06); }
.tau-cat-header {
    background: linear-gradient(135deg, #071c3a 0%, #0a2547 30%, #0d4f8b 70%, #1a72c4 100%);
    padding: 2rem 2.2rem 1.6rem; color: #fff; position: relative; overflow: hidden;
}
.tau-cat-header::after { content:""; position:absolute; bottom:0; left:0; width:100%; height:3px; background:linear-gradient(90deg,#c0253a,transparent); }
.tau-cat-kicker { display:inline-flex; align-items:center; gap:8px; padding:5px 14px; border-radius:999px; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.18); font-size:.68rem; font-weight:800; letter-spacing:.09em; text-transform:uppercase; margin-bottom:.8rem; }
.tau-cat-htitle { font-size:1.5rem; font-weight:800; letter-spacing:-.03em; margin:0 0 .3rem; }
.tau-cat-sub { font-size:.86rem; color:rgba(255,255,255,.80); margin:0; line-height:1.55; }
.tau-cat-body { background: #fff; padding: 1.8rem; }
.tau-cat-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
@media(max-width:991px){ .tau-cat-grid{grid-template-columns:repeat(2,1fr);} }
@media(max-width:575px){ .tau-cat-grid{grid-template-columns:1fr;} }
.tau-cat-card { display:flex; align-items:center; gap:16px; padding:20px 24px; border-radius:16px; border:1px solid rgba(13,79,139,.08); background:#f8faff; text-decoration:none !important; color:inherit !important; transition:all .22s; box-shadow:0 2px 8px rgba(10,37,71,.03); }
.tau-cat-card:hover { transform:translateY(-4px); box-shadow:0 10px 24px rgba(13,79,139,.12); border-color:rgba(13,79,139,.20); background:#eef4ff; }
.tau-cat-nm { font-size:.92rem; font-weight:700; color:#0f172a; flex:1; }
.tau-cat-arr { color:#cbd5e1; transition:all .2s; }
.tau-cat-card:hover .tau-cat-arr { color:#0d4f8b; transform:translateX(4px); }
.tau-courses-main-btn { display:inline-flex; align-items:center; justify-content:center; gap:10px; padding:13px 40px; border-radius:999px; border:none; background:linear-gradient(135deg,#0a2547 0%,#0d4f8b 100%); color:#fff !important; font-size:.9rem; font-weight:700; cursor:pointer; box-shadow:0 8px 24px rgba(13,79,139,.22); transition:all .22s; }
.tau-courses-main-btn:hover { background:linear-gradient(135deg,#071c3a 0%,#0a3d6b 100%); box-shadow:0 12px 30px rgba(13,79,139,.30); transform:translateY(-2px); }

/* ══════════════════════════════════════════════════════════
   BANNER HERO
   ══════════════════════════════════════════════════════════ */
.tau-banner-card {
    background: rgba(7,28,58,.70) !important;
    backdrop-filter: blur(22px) !important; -webkit-backdrop-filter: blur(22px) !important;
    border: 1px solid rgba(255,255,255,.14) !important;
    border-left: 5px solid #c0253a !important;
    border-radius: 24px !important; padding: 32px 36px !important;
    max-width: 470px !important; box-shadow: 0 24px 60px rgba(7,28,58,.50) !important;
}
.tau-banner-pretitle { font-size:.75rem !important; font-weight:700 !important; letter-spacing:3px !important; color:rgba(255,255,255,.60) !important; text-transform:uppercase !important; margin-bottom:5px !important; display:block !important; }
.tau-banner-title { font-size:2.2rem !important; font-weight:800 !important; color:#fff !important; line-height:1.15 !important; margin-bottom:7px !important; letter-spacing:-.5px !important; }
.tau-accent-text { background:linear-gradient(90deg,#fff 0%,#93c5fd 100%) !important; -webkit-background-clip:text !important; -webkit-text-fill-color:transparent !important; }
.tau-banner-subtitle { font-size:.83rem !important; font-weight:700 !important; color:#fff !important; background:linear-gradient(135deg,#c0253a 0%,#9e1b2e 100%) !important; padding:4px 12px !important; border-radius:6px !important; display:inline-block !important; margin-bottom:14px !important; }
.tau-banner-desc { font-size:.90rem !important; color:rgba(255,255,255,.88) !important; line-height:1.6 !important; margin-bottom:22px !important; }

/* ══════════════════════════════════════════════════════════
   DARK MODE
   ══════════════════════════════════════════════════════════ */
html[data-bs-theme="dark"] { color-scheme: dark; }
[data-bs-theme="dark"] body { background-color: #060f1e !important; color: #e2e8f0 !important; }
[data-bs-theme="dark"] #page-wrapper, [data-bs-theme="dark"] #page { background-color: #060f1e !important; }
[data-bs-theme="dark"] .bg-white, [data-bs-theme="dark"] #page.drawers { background-color: #060f1e !important; }
[data-bs-theme="dark"] .navbar {
    background:
        radial-gradient(circle at 5% 50%, rgba(255,255,255,.10) 0%, transparent 35%),
        linear-gradient(135deg, #5a0910 0%, #7a0e1a 25%, #9e1b2e 55%, #b82638 80%, #9e1b2e 100%) !important;
}
[data-bs-theme="dark"] .card, [data-bs-theme="dark"] .generalbox { background: #0d1f3a !important; border-color: #1e3a5f !important; }
[data-bs-theme="dark"] .dropdown-menu { background: #0d1f3a !important; border-color: rgba(255,255,255,.08) !important; }
[data-bs-theme="dark"] .dropdown-menu .dropdown-item { color: #cbd5e1 !important; }
[data-bs-theme="dark"] .dropdown-menu .dropdown-item:hover { background: rgba(13,79,139,.2) !important; color: #93c5fd !important; }
[data-bs-theme="dark"] .secondary-navigation { background: #0d1f3a !important; }
[data-bs-theme="dark"] a:not(.btn):not(.nav-link):not(.dropdown-item) { color: #93c5fd !important; }
[data-bs-theme="dark"] .card.dashboard-card { background: #0d1f3a !important; }
[data-bs-theme="dark"] .card.dashboard-card .card-title a { color: #e2e8f0 !important; }
[data-bs-theme="dark"] .card.dashboard-card:hover .card-title a { color: #93c5fd !important; }
[data-bs-theme="dark"] #numbers { background: #060f1e !important; }
[data-bs-theme="dark"] #numbers .sectionheading h2 { color: #e2e8f0 !important; }
[data-bs-theme="dark"] .tau-cat-body { background: #060f1e !important; }
[data-bs-theme="dark"] .tau-cat-card { background: #0d1f3a; border-color: rgba(255,255,255,.06); }
[data-bs-theme="dark"] .tau-cat-card:hover { background: #122a50; border-color: rgba(13,79,139,.3); }
[data-bs-theme="dark"] .tau-cat-nm { color: #e2e8f0; }
[data-bs-theme="dark"] .moove-footer, [data-bs-theme="dark"] #page-footer { background: linear-gradient(135deg, #030710 0%, #060f1e 100%) !important; }

/* ── FAB ── */
#tau-ai-fab { position:fixed; bottom:122px; right:16px; z-index:1055; display:inline-flex; align-items:center; gap:7px; background:linear-gradient(135deg,#9e1b2e 0%,#0d4f8b 100%); color:#fff !important; text-decoration:none !important; border-radius:22px; padding:10px 16px 10px 14px; font-size:.82rem; font-weight:700; box-shadow:0 4px 18px rgba(10,37,71,.35); transition:transform .15s, box-shadow .15s; }
#tau-ai-fab:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(10,37,71,.45); }
#tau-theme-toggle { position:fixed; bottom:70px; right:20px; z-index:1055; width:44px; height:44px; border-radius:50%; background:linear-gradient(135deg,#9e1b2e,#c0253a); border:none; color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 12px rgba(120,14,26,.35); transition:all .2s !important; }
#tau-theme-toggle:hover { transform:scale(1.08); }
[data-bs-theme="dark"] #tau-theme-toggle { background:linear-gradient(135deg,#0a2547,#0d4f8b); }

CSS;

// ── FOOTER: aros + LEMA ──────────────────────────────────────────────────────
$footer_inject = <<<'FOOTER'
<style id="tau-v11-style">
/* Aros giratorios — carmesí + blanco sobre el escudo */
.tau-rings-wrap{position:absolute;right:6%;top:50%;transform:translateY(-50%);width:clamp(200px,28%,300px);aspect-ratio:1;pointer-events:none;z-index:12;overflow:visible;}
.tau-rings-wrap svg{width:100%;height:100%;overflow:visible;}
@keyframes tau-cw  {to{transform:rotate(360deg)}}
@keyframes tau-ccw {to{transform:rotate(-360deg)}}
@keyframes tau-pa  {0%,100%{opacity:.14}50%{opacity:.40}}
@keyframes tau-nd  {from{r:4;opacity:.40}to{r:6.5;opacity:1}}
@keyframes tau-nw  {from{r:3;opacity:.25}to{r:5;opacity:.80}}
.tau-gc {animation:tau-cw  22s linear infinite;transform-origin:100px 100px;transform-box:fill-box;}
.tau-gcc{animation:tau-ccw 32s linear infinite;transform-origin:100px 100px;transform-box:fill-box;}
.tau-pa {animation:tau-pa 4.5s ease-in-out infinite;}
.tau-nd {animation:tau-nd 3s ease-in-out infinite alternate;}
.tau-nd:nth-child(2){animation-delay:-.75s}.tau-nd:nth-child(3){animation-delay:-1.5s}.tau-nd:nth-child(4){animation-delay:-2.25s}
.tau-nw {animation:tau-nw 3.8s ease-in-out infinite alternate;}
.tau-nw:nth-child(2){animation-delay:-.95s}.tau-nw:nth-child(3){animation-delay:-1.9s}.tau-nw:nth-child(4){animation-delay:-2.85s}

/* LEMA — entre hero y lo que sigue */
.tau-lema {
    position:relative; text-align:center; overflow:hidden;
    background: linear-gradient(180deg, #06090f 0%, #0a1835 55%, #06090f 100%);
    border-top: 2px solid #c0253a;
    border-bottom: 1px solid rgba(13,79,139,.20);
    padding: 14px 1rem 12px;
}
.tau-lema::before {
    content:""; position:absolute; inset:0; pointer-events:none;
    background-image:radial-gradient(circle, rgba(13,79,139,.07) 1px, transparent 1px);
    background-size: 28px 28px;
}
.tau-lema-row {
    display:inline-flex; align-items:center; gap:14px; position:relative;
}
.tau-lema-row::before {
    content:""; display:block; width:50px; height:1.5px;
    background:linear-gradient(90deg,transparent,#c0253a);
}
.tau-lema-row::after {
    content:""; display:block; width:50px; height:1.5px;
    background:linear-gradient(90deg,#c0253a,transparent);
}
.tau-lema-q {
    font-family:Georgia,"Times New Roman",serif;
    font-size:clamp(.80rem,1.1vw,.94rem); font-style:italic;
    color:rgba(255,255,255,.92); letter-spacing:.06em; margin:0;
    text-shadow:0 1px 8px rgba(0,0,0,.5);
}
.tau-lema-q strong { color:#fff; font-style:normal; font-weight:700; letter-spacing:.12em; text-transform:uppercase; }
.tau-lema-acc {
    display:inline-block; width:7px; height:7px; border-radius:50%;
    background:linear-gradient(135deg,#c0253a,#0d4f8b);
    margin:0 5px; vertical-align:middle;
}
.tau-lema-author {
    font-family:system-ui,Arial,sans-serif;
    font-size:clamp(.62rem,.85vw,.72rem); color:rgba(255,255,255,.40);
    letter-spacing:.10em; text-transform:uppercase; margin:4px 0 0;
}
@media(max-width:768px){.tau-rings-wrap{display:none!important}}
</style>
<script id="tau-v11-script">
(function(){
    function go(){
        var ss=document.getElementById("mooveslideshow");
        if(!ss){setTimeout(go,300);return;}

        /* --- AROS --- */
        if(!document.querySelector(".tau-rings-wrap")){
            var w=document.createElement("div");
            w.className="tau-rings-wrap";
            w.innerHTML='<svg viewBox="-34 -34 268 268" xmlns="http://www.w3.org/2000/svg">'+
              '<circle class="tau-pa" cx="100" cy="100" r="95" fill="rgba(7,28,58,.18)" stroke="none"/>'+
              '<circle class="tau-pa" cx="100" cy="100" r="120" fill="none" stroke="rgba(192,37,58,.30)" stroke-width="1.4" stroke-dasharray="8 14"/>'+
              '<g class="tau-gcc">'+
                '<circle cx="100" cy="100" r="110" fill="none" stroke="rgba(255,255,255,.20)" stroke-width="1" stroke-dasharray="5 18"/>'+
                '<circle class="tau-nw" cx="100" cy="-10" r="4" fill="rgba(255,255,255,.80)"/>'+
                '<circle class="tau-nw" cx="210" cy="100" r="4" fill="rgba(255,255,255,.80)"/>'+
                '<circle class="tau-nw" cx="100" cy="210" r="4" fill="rgba(255,255,255,.80)"/>'+
                '<circle class="tau-nw" cx="-10" cy="100" r="4" fill="rgba(255,255,255,.80)"/>'+
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

        /* --- LEMA --- */
        if(!document.querySelector(".tau-lema")){
            var lema=document.createElement("div");
            lema.className="tau-lema";
            lema.innerHTML=
              '<div class="tau-lema-row">'+
                '<p class="tau-lema-q">'+
                  '<span class="tau-lema-acc"></span>'+
                  '“<strong>Hombres nuevos</strong> para <strong>tiempos nuevos</strong>”'+
                  '<span class="tau-lema-acc"></span>'+
                '</p>'+
              '</div>'+
              '<p class="tau-lema-author">Fray Guillermo de Castellana, OFMCap.</p>';
            /* Insertarlo justo después del slideshow, saltando nodos de texto */
            var ref=ss.nextSibling;
            while(ref&&ref.nodeType===3)ref=ref.nextSibling;
            if(ref)ss.parentNode.insertBefore(lema,ref);
            else ss.parentNode.appendChild(lema);
        }
    }
    if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",go);
    else go();
    /* Fallback por si el hero carga tarde (slideshow con fade) */
    setTimeout(go,800);
})();
</script>
FOOTER;

// ── Guardar en BD ─────────────────────────────────────────────────────────────
set_config('scss', $css, 'theme_moove');
set_config('brandcolor',       '#c0253a', 'theme_moove'); // rojo como brand para inputs, checkboxes
set_config('buttonbrandcolor', '#0d4f8b', 'theme_moove');
set_config('linkcolor',        '#0d4f8b', 'theme_moove');

// Limpiar footers anteriores (tau-v9, tau-v10, tau-v11)
$footer = get_config('theme_moove', 'additionalhtmlfooter');
$clean_kw = ['tau-v9','tau-v10','tau-v11'];
foreach (['<style','<script'] as $tag) {
    $close = ($tag==='<style') ? '</style>' : '</script>';
    $changed = true;
    while ($changed) {
        $changed = false; $pos = 0;
        while (($s = strpos($footer,$tag,$pos)) !== false) {
            $e = strpos($footer,$close,$s);
            if ($e===false) break;
            $block = substr($footer,$s,$e-$s+strlen($close));
            $hit = false;
            foreach ($clean_kw as $kw) { if (stripos($block,$kw)!==false){$hit=true;break;} }
            if ($hit) { $footer = substr($footer,0,$s).substr($footer,$e+strlen($close)); $changed=true; break; }
            $pos = $e + strlen($close);
        }
    }
}
set_config('additionalhtmlfooter', trim($footer)."\n".$footer_inject, 'theme_moove');

theme_reset_all_caches();
echo "Rediseño profesional v11 aplicado\n";
echo "scss: ".strlen(get_config('theme_moove','scss'))." bytes\n";
echo "footer: ".strlen(get_config('theme_moove','additionalhtmlfooter'))." bytes\n";
echo "brandcolor: ".get_config('theme_moove','brandcolor')."\n";
