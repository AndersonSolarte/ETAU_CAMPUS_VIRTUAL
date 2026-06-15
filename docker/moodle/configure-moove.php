<?php
declare(strict_types=1);

define('CLI_SCRIPT', true);

require('/var/www/html/config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/adminlib.php');

$systemcontext = context_system::instance();
$filestorage   = get_file_storage();
$brandingroot  = $CFG->dirroot . '/theme/tau_branding';

// ─── Extra SCSS (Moove uses "scss" key, compiled through SCSS pipeline) ──────

$extrascss = <<<'NEWSCSS'
/* ═══════════════════════════════════════════════════
   TAU Campus Virtual — Moove theme v2
   Palette: Red #c62b3a  |  TAU Blue #1e3a8a
   ═══════════════════════════════════════════════════ */

/* ── 1. ANIMATIONS ── */
@keyframes fadeSlideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes loginCardIn {
    from { opacity: 0; transform: translateY(28px) scale(.96); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes tauToastIn {
    from { opacity: 0; transform: translate(-50%, -50%) scale(.90); }
    to   { opacity: 1; transform: translate(-50%, -50%) scale(1); }
}

/* ── 2. NAVBAR: red, white text ── */
.navbar {
    background:
        radial-gradient(circle at 12% 24%, rgba(255,255,255,.12) 0%, transparent 16%),
        linear-gradient(90deg, #c62b3a 0%, #c62b3a 34%, #b42838 52%, #8e1e2c 74%, #6c1521 100%) !important;
    box-shadow: 0 10px 28px rgba(87,17,28,.22) !important;
    border-bottom: 1px solid rgba(255,255,255,.08) !important;
    transition: background .3s !important;
}
.navbar .nav-link,
.navbar-brand,
.primary-navigation .nav-link {
    color: rgba(255,255,255,.93) !important;
    transition: none !important;
    background: transparent !important;
    border-radius: 0 !important;
    box-shadow: none !important;
}
.navbar .nav-link:hover,
.navbar .nav-link:focus {
    color: #fff !important;
    background: transparent !important;
    border-radius: 0 !important;
    box-shadow: none !important;
}
.navbar .editmode-switch-form label,
.navbar .custom-switch label,
.navbar .custom-control-label {
    color: #fff !important;
}
.navbar .nav-link.active,
.primary-navigation .nav-link.active {
    color: #fff !important;
    font-weight: 700 !important;
    background: transparent !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    position: relative !important;
}
.navbar .nav-link.active::after,
.primary-navigation .nav-link.active::after,
.primary-navigation .nav-link[aria-current="page"]::after {
    content: "" !important;
    position: absolute !important;
    left: .95rem !important;
    right: .95rem !important;
    bottom: .35rem !important;
    height: 2px !important;
    border-radius: 999px !important;
    background: rgba(255,255,255,.85) !important;
}
.navbar .nav-link.show,
.navbar .nav-link[aria-current="page"],
.primary-navigation .nav-link.show,
.primary-navigation .nav-link[aria-current="page"],
.primary-navigation .nav-link[aria-selected="true"],
.moremenu.navigation .nav-link.active,
.moremenu.navigation .nav-link.show,
.moremenu.navigation .nav-link[aria-current="page"],
.moremenu.navigation .nav-link[aria-selected="true"] {
    color: #fff !important;
    font-weight: 700 !important;
    background: transparent !important;
    background-image: none !important;
    border: 0 !important;
    border-color: transparent !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    outline: 0 !important;
    transform: none !important;
    filter: none !important;
    position: relative !important;
}
.navbar .nav-link.show::before,
.navbar .nav-link.show::after,
.navbar .nav-link[aria-current="page"]::before,
.navbar .nav-link[aria-current="page"]::after,
.primary-navigation .nav-link.show::before,
.primary-navigation .nav-link.show::after,
.primary-navigation .nav-link[aria-current="page"]::before,
.primary-navigation .nav-link[aria-current="page"]::after,
.primary-navigation .nav-link[aria-selected="true"]::before,
.primary-navigation .nav-link[aria-selected="true"]::after,
.moremenu.navigation .nav-link.active::before,
.moremenu.navigation .nav-link.active::after,
.moremenu.navigation .nav-link.show::before,
.moremenu.navigation .nav-link.show::after,
.moremenu.navigation .nav-link[aria-current="page"]::before,
.moremenu.navigation .nav-link[aria-current="page"]::after,
.moremenu.navigation .nav-link[aria-selected="true"]::before,
.moremenu.navigation .nav-link[aria-selected="true"]::after {
    box-shadow: none !important;
}
.navbar .nav-link.show::after,
.navbar .nav-link[aria-current="page"]::after,
.primary-navigation .nav-link.show::after,
.primary-navigation .nav-link[aria-current="page"]::after,
.primary-navigation .nav-link[aria-selected="true"]::after,
.moremenu.navigation .nav-link.active::after,
.moremenu.navigation .nav-link.show::after,
.moremenu.navigation .nav-link[aria-current="page"]::after,
.moremenu.navigation .nav-link[aria-selected="true"]::after {
    content: "" !important;
    position: absolute !important;
    left: .95rem !important;
    right: .95rem !important;
    bottom: .35rem !important;
    height: 2px !important;
    border-radius: 999px !important;
    background: rgba(255,255,255,.85) !important;
}
.navbar .nav-item.active,
.navbar .nav-item.show,
.primary-navigation .nav-item.active,
.primary-navigation .nav-item.show,
.moremenu.navigation .nav-item.active,
.moremenu.navigation .nav-item.show,
.navbar .nav-item:focus-within,
.primary-navigation .nav-item:focus-within,
.moremenu.navigation .nav-item:focus-within {
    background: transparent !important;
    background-image: none !important;
    border: 0 !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    transform: none !important;
    filter: none !important;
}
.navbar .nav-item {
    margin-right: .2rem !important;
}
.navbar .icon,
.navbar .fa,
.navbar button.btn-icon {
    color: rgba(255,255,255,.88) !important;
}
.navbar .dropdown-toggle::after {
    border-top-color: rgba(255,255,255,.8) !important;
}

/* "Acceder" pill button for guest users */
.notloggedin .navbar .divider.border-start {
    display: none !important;
}
.notloggedin .navbar .langmenu {
    margin-right: .85rem !important;
}
.notloggedin .navbar .langmenu .dropdown-toggle {
    border: 0 !important;
    background: rgba(255,255,255,.06) !important;
    color: rgba(255,255,255,.92) !important;
    border-radius: 10px !important;
    padding: 10px 14px !important;
    box-shadow: none !important;
}
.notloggedin .navbar .langmenu .dropdown-toggle:hover,
.notloggedin .navbar .langmenu .dropdown-toggle:focus {
    background: rgba(255,255,255,.12) !important;
    color: #fff !important;
}
.notloggedin .navbar .usermenu-container {
    margin-left: 0 !important;
}
.notloggedin .navbar .usermenu {
    display: inline-flex !important;
    align-items: center !important;
    min-width: auto !important;
}
.notloggedin .navbar .usermenu .login {
    display: inline-flex !important;
    align-items: center !important;
    padding: 0 !important;
    margin: 0 !important;
}
.notloggedin .navbar .usermenu .login a {
    font-size: 0.92rem !important;
    line-height: 1 !important;
    background: #ffffff !important;
    border: 1px solid rgba(255,255,255,.32) !important;
    border-radius: 12px !important;
    padding: 11px 20px !important;
    font-weight: 800 !important;
    color: #b11f35 !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    white-space: nowrap !important;
    box-shadow: 0 8px 20px rgba(0,0,0,.12) !important;
    transition: background .2s, transform .15s, box-shadow .2s !important;
}
.notloggedin .navbar .usermenu .login a:hover,
.notloggedin .navbar .usermenu .login a:focus {
    background: #fff5f6 !important;
    color: #8d182a !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 10px 24px rgba(0,0,0,.16) !important;
}
.logininfo {
    font-size: 0 !important;
    background: transparent !important;
    line-height: 0 !important;
}
.logininfo a,
.navbar .logininfo a {
    font-size: 0.875rem !important;
    line-height: 1.4 !important;
    background: rgba(255,255,255,.2) !important;
    border: 1.5px solid rgba(255,255,255,.7) !important;
    border-radius: 20px !important;
    padding: 6px 22px !important;
    font-weight: 700 !important;
    color: #fff !important;
    text-decoration: none !important;
    display: inline-block !important;
    transition: background .2s, transform .15s, box-shadow .2s !important;
    letter-spacing: .3px !important;
    white-space: nowrap !important;
}
.logininfo a:hover {
    background: rgba(255,255,255,.36) !important;
    transform: translateY(-1px) scale(1.04) !important;
    box-shadow: 0 6px 16px rgba(0,0,0,.25) !important;
    color: #fff !important;
}

/* ── 3. DROPDOWN MENUS ── */
.dropdown-menu,
.usermenu .dropdown-menu,
.navbar .dropdown-menu,
.action-menu .dropdown-menu {
    background-color: #fff !important;
    border: 1px solid rgba(0,0,0,.08) !important;
    box-shadow: 0 16px 40px rgba(0,0,0,.16), 0 4px 12px rgba(0,0,0,.08) !important;
    border-radius: 14px !important;
    padding: 7px !important;
    min-width: 210px !important;
    animation: fadeSlideDown .18s cubic-bezier(.34,1.56,.64,1) both;
}
.dropdown-menu .dropdown-item,
.dropdown-menu a,
.usermenu .dropdown-menu a,
.action-menu .dropdown-menu a {
    color: #2a2a2a !important;
    border-radius: 8px !important;
    padding: 9px 13px !important;
    font-size: 0.88rem !important;
    transition: background .12s, color .12s !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
}
.dropdown-menu .dropdown-item:hover,
.dropdown-menu a:hover {
    background-color: #fef2f3 !important;
    color: #c62b3a !important;
}
.dropdown-menu .dropdown-item .icon,
.dropdown-menu a .icon {
    color: #999 !important;
    transition: color .12s !important;
}
.dropdown-menu .dropdown-item:hover .icon,
.dropdown-menu a:hover .icon {
    color: #c62b3a !important;
}
.dropdown-menu .dropdown-divider {
    border-color: #f2f2f2 !important;
    margin: 5px 0 !important;
}

/* ── 4. SECONDARY NAV: white with red accent (contrasts with red navbar) ── */
.secondary-navigation,
.tertiary-navigation {
    background-color: #fff !important;
    box-shadow: 0 2px 6px rgba(0,0,0,.07) !important;
    filter: none !important;
}
.secondary-navigation .nav-tabs {
    border-bottom: none !important;
}
.secondary-navigation .nav-tabs .nav-link,
.secondary-navigation .navigation .nav-link {
    color: #555 !important;
    border: none !important;
    border-bottom: 3px solid transparent !important;
    border-radius: 0 !important;
    padding: 10px 18px !important;
    font-size: .875rem !important;
    font-weight: 500 !important;
    transition: color .15s, border-color .15s, background .15s !important;
}
.secondary-navigation .nav-tabs .nav-link:hover {
    color: #c62b3a !important;
    background: rgba(198,43,58,.05) !important;
}
.secondary-navigation .nav-tabs .nav-link.active {
    color: #c62b3a !important;
    font-weight: 700 !important;
    border-bottom-color: #c62b3a !important;
    background: transparent !important;
}

/* ── 5. LOGIN PAGE ── */
/* Background: use #page-login-index (body ID) to avoid ScssPhp selector bug */
[data-bs-theme="dark"] body#page-login-index,
[data-bs-theme="dark"] #page-login-index #page {
    background-image:
        url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.025'%3E%3Ccircle cx='40' cy='40' r='1.5'/%3E%3Ccircle cx='0' cy='0' r='1.5'/%3E%3Ccircle cx='80' cy='0' r='1.5'/%3E%3Ccircle cx='0' cy='80' r='1.5'/%3E%3Ccircle cx='80' cy='80' r='1.5'/%3E%3C/g%3E%3C/svg%3E"),
        radial-gradient(ellipse at 15% 50%, rgba(198,43,58,.22) 0%, transparent 55%),
        radial-gradient(ellipse at 85% 15%, rgba(45,106,159,.25) 0%, transparent 50%),
        linear-gradient(135deg, #0d1117 0%, #161b22 45%, #0f1f38 100%) !important;
    background-color: #0d1117 !important;
    background-size: 80px 80px, cover, cover, cover !important;
}
[data-bs-theme="light"] body#page-login-index,
[data-bs-theme="light"] #page-login-index #page,
:not([data-bs-theme="dark"]) body#page-login-index,
:not([data-bs-theme="dark"]) #page-login-index #page {
    background-image:
        url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23000000' fill-opacity='0.03'%3E%3Ccircle cx='40' cy='40' r='1.5'/%3E%3Ccircle cx='0' cy='0' r='1.5'/%3E%3Ccircle cx='80' cy='0' r='1.5'/%3E%3Ccircle cx='0' cy='80' r='1.5'/%3E%3Ccircle cx='80' cy='80' r='1.5'/%3E%3C/g%3E%3C/svg%3E"),
        radial-gradient(ellipse at 15% 50%, rgba(198,43,58,.08) 0%, transparent 55%),
        radial-gradient(ellipse at 85% 15%, rgba(45,106,159,.1) 0%, transparent 50%),
        linear-gradient(135deg, #f8f9fa 0%, #e9ecef 45%, #dee2e6 100%) !important;
    background-color: #f8f9fa !important;
    background-size: 80px 80px, cover, cover, cover !important;
}
/* Layout centering is handled by the <style> tag injected via additionalhtmlhead */
/* Google-only: hide ALL local login form elements */
#page-login-index form#login,
#page-login-index .login-form,
#page-login-index .loginform,
#page-login-index .login-form-username,
#page-login-index .login-form-password,
#page-login-index .login-form-submit,
#page-login-index .login-form-forgotpassword,
#page-login-index .login-divider,
#page-login-index .login-identityproviders h2,
#page-login-index #username,
#page-login-index #password,
#page-login-index label[for="username"],
#page-login-index label[for="password"],
#page-login-index .potentialidp ~ .login-form,
#page-login-index input[name="username"],
#page-login-index input[name="password"] { display: none !important; }
#page-login-index .login-identityprovider-btn {
    font-size: 1rem;
    padding: 12px 24px;
    border-radius: 8px;
    border: 1.5px solid #ddd;
    background: #fff;
    color: #333;
    transition: box-shadow .2s, border-color .2s, transform .15s !important;
    font-weight: 500;
}
#page-login-index .login-identityprovider-btn:hover {
    box-shadow: 0 8px 28px rgba(0,0,0,.16) !important;
    border-color: #c62b3a !important;
    transform: translateY(-3px) !important;
}
/* Welcome section — typographic identity block, not a button */
.tau-login-welcome { text-align: center; margin: 10px 0 18px; }
.tau-campus-inst {
    font-size: 0.62rem;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #aaa;
    margin-bottom: 3px;
}
.tau-campus-title {
    font-size: 1.1rem;
    font-weight: 800;
    color: #1a1a2e;
    letter-spacing: -0.2px;
}
.tau-campus-divider {
    width: 36px;
    height: 3px;
    background: linear-gradient(90deg, #c62b3a 0%, #e87a84 100%);
    border-radius: 2px;
    margin: 8px auto 10px;
}
.tau-login-welcome p { font-size: .82rem; color: #777; margin: 0; }
[data-bs-theme="dark"] .tau-campus-title { color: #f0f0f0; }
[data-bs-theme="dark"] .tau-campus-inst { color: #666; }
[data-bs-theme="dark"] .tau-login-welcome p { color: #888; }

/* ── 6. NAV DRAWER ── */
#nav-drawer { background-color: #fff !important; }
[data-bs-theme="dark"] #nav-drawer { background-color: #1a1a2e !important; }

/* ── 7. GLOBAL BUTTONS & LINKS ── */
.btn-primary {
    background-color: #c62b3a;
    border-color: #c62b3a;
    transition: background .2s, transform .1s, box-shadow .2s !important;
}
.btn-primary:hover, .btn-primary:focus {
    background-color: #a32230;
    border-color: #a32230;
    box-shadow: 0 4px 14px rgba(198,43,58,.35) !important;
    transform: translateY(-1px) !important;
}
body { font-family: 'Manrope', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

/* ── 8. DARK MODE & AI FAB ── */
#tau-ai-fab {
    position: fixed; bottom: 122px; right: 16px; z-index: 1055;
    display: inline-flex; align-items: center; gap: 7px;
    background: linear-gradient(135deg, #1a1a2e 0%, #c62b3a 100%);
    color: #fff !important; text-decoration: none !important;
    border-radius: 22px; padding: 10px 16px 10px 14px;
    font-size: 0.82rem; font-weight: 700;
    box-shadow: 0 4px 18px rgba(198,43,58,.4);
    transition: transform .15s, box-shadow .15s;
    white-space: nowrap;
}
#tau-ai-fab:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(198,43,58,.5); }
.tau-ai-course-btn {
    display: inline-flex; align-items: center; gap: 8px;
    background: linear-gradient(135deg, #1a1a2e 0%, #c62b3a 100%);
    color: #fff !important; text-decoration: none !important;
    border-radius: 10px; padding: 10px 22px; font-size: .95rem;
    font-weight: 700; box-shadow: 0 4px 14px rgba(198,43,58,.35);
    transition: opacity .15s, transform .1s; margin-left: 12px;
}
.tau-ai-course-btn:hover { opacity: .88; transform: translateY(-1px); }
#tau-theme-toggle {
    position: fixed; bottom: 70px; right: 20px; z-index: 1055;
    width: 44px; height: 44px; border-radius: 50%;
    background: #c62b3a; border: none; color: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center; padding: 0;
    box-shadow: 0 4px 12px rgba(0,0,0,.25);
    transition: background .2s, transform .15s !important;
}
#tau-theme-toggle:hover { background: #a32230; transform: scale(1.1); }
[data-bs-theme="dark"] .navbar {
    background:
        radial-gradient(circle at 12% 24%, rgba(255,255,255,.08) 0%, transparent 16%),
        linear-gradient(90deg, #b73345 0%, #b73345 34%, #992737 52%, #781c2a 74%, #54131d 100%) !important;
}
[data-bs-theme="dark"] .navbar .nav-link,
[data-bs-theme="dark"] .navbar-brand { color: rgba(255,255,255,.88) !important; }

/* ── NOTIFICACIONES dark mode ── */
[data-bs-theme="dark"] .popover-region-container {
    background: #1e1e2e !important;
    border: 1px solid rgba(255,255,255,.08) !important;
    box-shadow: 0 12px 40px rgba(0,0,0,.55) !important;
}
[data-bs-theme="dark"] .popover-region-header-container {
    background: #1e1e2e !important;
    border-bottom: 1px solid rgba(255,255,255,.07) !important;
}
[data-bs-theme="dark"] .popover-region-header-text {
    color: #f0f0f0 !important;
}
[data-bs-theme="dark"] .popover-region-content-container,
[data-bs-theme="dark"] .popover-region-content {
    background: #1e1e2e !important;
}
/* Item individual de notificación */
[data-bs-theme="dark"] .content-item-container {
    background: #1e1e2e !important;
    border-bottom: 1px solid rgba(255,255,255,.05) !important;
}
[data-bs-theme="dark"] .content-item-container:hover,
[data-bs-theme="dark"] .content-item-container a:hover {
    background: rgba(255,255,255,.04) !important;
}
[data-bs-theme="dark"] .content-item-container.unread {
    background: rgba(198,43,58,.06) !important;
    border-left: 3px solid #c62b3a !important;
}
[data-bs-theme="dark"] .notification-message {
    color: #e0e0e0 !important;
}
[data-bs-theme="dark"] .content-item-footer .timestamp,
[data-bs-theme="dark"] .content-item-footer {
    color: rgba(255,255,255,.42) !important;
}
[data-bs-theme="dark"] .content-item-container a.context-link,
[data-bs-theme="dark"] .content-item-container .context-link {
    color: inherit !important;
    text-decoration: none !important;
}
/* Footer "Ver todo" */
[data-bs-theme="dark"] .popover-region-footer-container,
[data-bs-theme="dark"] .see-all-link .popover-region-footer-container {
    background: #16162a !important;
    border-top: 1px solid rgba(255,255,255,.07) !important;
}
[data-bs-theme="dark"] .popover-region-seeall-text,
[data-bs-theme="dark"] .see-all-link {
    color: #e87a84 !important;
}
[data-bs-theme="dark"] .see-all-link:hover .popover-region-footer-container {
    background: rgba(232,113,124,.08) !important;
}
/* Panel de área de notificaciones (notification_area.mustache) */
[data-bs-theme="dark"] .notification-area {
    background: #1e1e2e !important;
    color: #e0e0e0 !important;
}
[data-bs-theme="dark"] .notification-area .header,
[data-bs-theme="dark"] .notification-area .footer {
    background: #16162a !important;
    border-color: rgba(255,255,255,.07) !important;
    color: #e0e0e0 !important;
}
[data-bs-theme="dark"] .notification-area .content-area {
    background: #1e1e2e !important;
}
[data-bs-theme="dark"] .notification-area .empty-text {
    color: rgba(255,255,255,.38) !important;
}
[data-bs-theme="dark"] .notification-area .btn-link {
    color: #e87a84 !important;
}
/* Message drawer dark mode */
[data-bs-theme="dark"] [data-region="message-drawer"],
[data-bs-theme="dark"] .message-drawer {
    background: #1e1e2e !important;
    border-color: rgba(255,255,255,.08) !important;
}
/* Scrollbar del panel */
[data-bs-theme="dark"] .popover-region-content-container::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,.12) !important;
}
[data-bs-theme="dark"] .popover-region-content-container::-webkit-scrollbar-track {
    background: #1e1e2e !important;
}

/* ── Botón Acceder / Login en dark mode — fondo crimson, texto blanco ── */
[data-bs-theme="dark"] .notloggedin .navbar .usermenu .login a,
[data-bs-theme="dark"] .notloggedin .navbar .usermenu .login .btn,
[data-bs-theme="dark"] .notloggedin .navbar .usermenu .login .btn-primary,
[data-bs-theme="dark"] .navbar .logininfo a {
    background: #c62b3a !important;
    color: #ffffff !important;
    border-color: rgba(255,255,255,.20) !important;
    box-shadow: 0 4px 16px rgba(198,43,58,.35) !important;
}
[data-bs-theme="dark"] .notloggedin .navbar .usermenu .login a:hover,
[data-bs-theme="dark"] .notloggedin .navbar .usermenu .login .btn:hover,
[data-bs-theme="dark"] .navbar .logininfo a:hover {
    background: #a32230 !important;
    color: #ffffff !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 20px rgba(198,43,58,.50) !important;
}
[data-bs-theme="dark"] .dropdown-menu,
[data-bs-theme="dark"] .action-menu .dropdown-menu {
    background-color: #1e1e2e !important;
    border-color: rgba(255,255,255,.1) !important;
}
[data-bs-theme="dark"] .dropdown-menu .dropdown-item,
[data-bs-theme="dark"] .dropdown-menu a { color: #e0e0e0 !important; }
[data-bs-theme="dark"] .dropdown-menu .dropdown-item:hover,
[data-bs-theme="dark"] .dropdown-menu a:hover {
    background-color: rgba(232,113,124,.12) !important;
    color: #e8717c !important;
}
[data-bs-theme="dark"] .secondary-navigation { background-color: #1e1e2e !important; }
[data-bs-theme="dark"] .tertiary-navigation { background-color: #1e1e2e !important; }
[data-bs-theme="dark"] .secondary-navigation .nav-tabs .nav-link { color: #aaa !important; }
[data-bs-theme="dark"] .secondary-navigation .nav-tabs .nav-link.active { color: #fff !important; font-weight: 700 !important; border-bottom-color: rgba(255,255,255,.55) !important; }
[data-bs-theme="dark"] .secondary-navigation .nav-tabs .nav-link:hover { color: #fff !important; background: rgba(255,255,255,.07) !important; }
[data-bs-theme="dark"] #tau-theme-toggle { background: #2a2a3e !important; color: rgba(255,255,255,.75) !important; box-shadow: 0 4px 12px rgba(0,0,0,.4) !important; }
[data-bs-theme="dark"] #tau-theme-toggle:hover { background: #383858 !important; }
/* ════════════════════════════════════════════════
   DARK MODE — FONDO BASE GLOBAL (fix franjas blancas)
   ════════════════════════════════════════════════ */
/* color-scheme: indica al navegador usar scrollbars y UI nativos en modo oscuro */
html[data-bs-theme="dark"] { color-scheme: dark; }
/* html + body: el fondo real del viewport */
html[data-bs-theme="dark"],
[data-bs-theme="dark"] html { background-color: #111318 !important; }
[data-bs-theme="dark"] body { background-color: #111318 !important; color: #e0e0e0 !important; }
/* Wrappers de página */
[data-bs-theme="dark"] #page-wrapper,
[data-bs-theme="dark"] #page {
    background-color: #111318 !important;
}
/* Anular Bootstrap .bg-white (background: #fff !important) */
[data-bs-theme="dark"] .bg-white { background-color: #111318 !important; }
/* page con múltiples clases de Moodle */
[data-bs-theme="dark"] #page.bg-white,
[data-bs-theme="dark"] #page.drawers,
[data-bs-theme="dark"] .drawers.bg-white,
[data-bs-theme="dark"] .drag-container.bg-white { background-color: #111318 !important; }
/* Contenedores Moove */
[data-bs-theme="dark"] .moove-container,
[data-bs-theme="dark"] .moove-container-fluid { background-color: #111318 !important; }
/* Drawers — estado cerrado (not-initialized / oculto) */
[data-bs-theme="dark"] .drawer,
[data-bs-theme="dark"] .drawer.not-initialized,
[data-bs-theme="dark"] .drawer-primary,
[data-bs-theme="dark"] .drawer-left,
[data-bs-theme="dark"] .drawer-right,
[data-bs-theme="dark"] #theme_boost-drawers-primary,
[data-bs-theme="dark"] #theme_boost-drawers-secondary { background-color: #1a1a2e !important; border-color: rgba(255,255,255,.08) !important; }
/* Drawer interior */
[data-bs-theme="dark"] .drawerheader,
[data-bs-theme="dark"] .drawerheadercontent,
[data-bs-theme="dark"] .drawercontent { background-color: #1a1a2e !important; }
/* Drawer-toggler buttons (los botones hamburguesa de abrir/cerrar drawer) */
[data-bs-theme="dark"] .drawer-toggler,
[data-bs-theme="dark"] .drawer-toggles { background: transparent !important; }
[data-bs-theme="dark"] .drawer-toggler .btn,
[data-bs-theme="dark"] .drawer-toggler button { background: rgba(255,255,255,.06) !important; color: rgba(255,255,255,.7) !important; border-color: rgba(255,255,255,.1) !important; }
/* Botón "ir arriba" de Moove (#goto-top-link) */
[data-bs-theme="dark"] #goto-top-link,
[data-bs-theme="dark"] .goto-top-link {
    background: #2a2a3e !important;
    border: 1px solid rgba(255,255,255,.1) !important;
    color: rgba(255,255,255,.6) !important;
    box-shadow: 0 4px 12px rgba(0,0,0,.4) !important;
}
[data-bs-theme="dark"] #goto-top-link:hover { background: #383858 !important; color: #fff !important; }
/* Botón footer popover (accesibilidad) */
[data-bs-theme="dark"] .btn-footer-popover,
[data-bs-theme="dark"] .footer-popover { background: rgba(255,255,255,.06) !important; color: rgba(255,255,255,.6) !important; border-color: rgba(255,255,255,.1) !important; }
/* Accessibility bar (barra superior de Moove) */
[data-bs-theme="dark"] #accessibilitybar { background: #1a1a2e !important; border-bottom: 1px solid rgba(255,255,255,.06) !important; }
[data-bs-theme="dark"] #accessibilitybar .btn-default { background: #242436 !important; color: rgba(255,255,255,.7) !important; border-color: rgba(255,255,255,.08) !important; }
/* Dark mode: all links white, no red */
[data-bs-theme="dark"] { --bs-link-color: rgba(255,255,255,.78); --bs-link-color-rgb: 255,255,255; --bs-link-hover-color: #fff; }
[data-bs-theme="dark"] a:not(.btn):not(.nav-link):not(.dropdown-item) { color: rgba(255,255,255,.78) !important; }
[data-bs-theme="dark"] a:not(.btn):not(.nav-link):not(.dropdown-item):hover { color: #fff !important; }
[data-bs-theme="dark"] .card,
[data-bs-theme="dark"] .generalbox { background-color: #1e1e2e; border-color: #333; }
[data-bs-theme="dark"] .drawer { background-color: #1a1a2e !important; color: #e0e0e0 !important; border-color: rgba(255,255,255,0.1) !important; }
[data-bs-theme="dark"] .drawer a,
[data-bs-theme="dark"] .drawer .list-group-item,
[data-bs-theme="dark"] .drawer .nav-link { color: rgba(255,255,255,.78) !important; background: transparent !important; }
[data-bs-theme="dark"] .drawer a:hover,
[data-bs-theme="dark"] .drawer .list-group-item:hover,
[data-bs-theme="dark"] .drawer .nav-link:hover { color: #fff !important; background: rgba(255,255,255,.05) !important; }
[data-bs-theme="dark"] .drawer .list-group-item.active,
[data-bs-theme="dark"] .drawer .nav-link.active { background: #c62b3a !important; color: #fff !important; }
[data-bs-theme="dark"] .course-section .content, [data-bs-theme="dark"] .section.main { background-color: #1e1e2e !important; border-color: #333 !important; }
[data-bs-theme="dark"] .course-section .content .sectionname, [data-bs-theme="dark"] .course-section-header h3 { color: #fff !important; }
[data-bs-theme="dark"] .activity-item, [data-bs-theme="dark"] .activity.kalendar { background-color: #2b2b3a !important; border-color: #444 !important; }
[data-bs-theme="dark"] .activity-item .activityname, [data-bs-theme="dark"] .instancename { color: #f0f0f0 !important; }
[data-bs-theme="dark"] .moove-footer,
[data-bs-theme="dark"] #page-footer .footer-dark { background: linear-gradient(135deg, #1b0205 0%, #2a080d 50%, #380a11 100%) !important; }

/* ── 9. FOOTER ── */
.moove-footer,
#page-footer .footer-dark,
#page-footer,
footer#page-footer,
#page-footer .container-fluid { background: linear-gradient(135deg, #2d050a 0%, #4a0e17 50%, #5f121d 100%) !important; color: rgba(255,255,255,.85) !important; }
#page-footer a:not(.btn):not(.nav-link) { color: rgba(255,255,255,.7) !important; transition: color .15s !important; }
#page-footer a:not(.btn):not(.nav-link):hover { color: #fff !important; }
#page-footer h3, #page-footer h4 { color: #fff !important; }
/* Hide Moove "developed by conecti.me" attribution */
[data-region="footer-content-popover"] .footer-section:last-child,
[data-region="footer-content-popover"] .footer-section + .footer-section,
a[href*="conecti.me"],
a[href*="conecti.me"] + *,
img[alt*="Conecti"],
.footer-content-popover .footer-section:last-of-type { display: none !important; }

/* ── 10. COURSE CARDS — Professional TAU design ── */
.card.dashboard-card,
.coursebox {
    border: none !important;
    border-radius: 18px !important;
    overflow: hidden !important;
    box-shadow: 0 3px 14px rgba(0,0,0,.07), 0 1px 4px rgba(0,0,0,.04) !important;
    transition: transform .28s cubic-bezier(.34,1.56,.64,1), box-shadow .28s !important;
    cursor: pointer !important;
    background: #fff !important;
    position: relative !important;
    display: flex !important;
    flex-direction: column !important;
}
.card.dashboard-card:hover,
.coursebox:hover {
    transform: translateY(-9px) !important;
    box-shadow: 0 28px 60px rgba(0,0,0,.13), 0 8px 20px rgba(198,43,58,.11) !important;
}

/* Image header — fixed height, scale on hover */
.card.dashboard-card .card-img-top,
.card.dashboard-card img.card-img-top {
    height: 168px !important;
    width: 100% !important;
    object-fit: cover !important;
    display: block !important;
    flex-shrink: 0 !important;
    transition: transform .45s ease !important;
}
.card.dashboard-card:hover .card-img-top,
.card.dashboard-card:hover img.card-img-top {
    transform: scale(1.07) !important;
}

/* Gradient placeholder header (JS-injected when no course image) */
.tau-gradient-header {
    height: 168px;
    width: 100%;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    transition: opacity .25s ease;
}
.card.dashboard-card:hover .tau-gradient-header { opacity: .98; }
.tau-gradient-header::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 45%;
    background: linear-gradient(transparent, rgba(0,0,0,.28));
    pointer-events: none;
}
.tau-gh-icon {
    position: relative;
    z-index: 1;
    width: 54px;
    height: 54px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    filter: drop-shadow(0 4px 18px rgba(0,0,0,.38));
    line-height: 1;
}
.tau-gh-icon svg {
    width: 54px;
    height: 54px;
    stroke: rgba(255,255,255,.92);
    fill: none;
    stroke-width: 1.7;
    stroke-linecap: round;
    stroke-linejoin: round;
}

/* Geometric pattern overlay on gradient header */
.tau-gh-pattern {
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 20% 80%, rgba(255,255,255,.08) 0%, transparent 40%),
        radial-gradient(circle at 80% 20%, rgba(255,255,255,.06) 0%, transparent 40%);
    pointer-events: none;
}

/* Card body */
.card.dashboard-card .card-body {
    padding: 1rem 1.15rem .85rem !important;
    flex: 1 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 0 !important;
}

/* Category badge (JS-injected) */
.tau-cat-badge {
    font-size: .56rem;
    font-weight: 700;
    letter-spacing: .5px;
    text-transform: uppercase;
    color: #c62b3a;
    background: rgba(198,43,58,.04);
    border: 1px solid rgba(198,43,58,.1);
    border-radius: 20px;
    padding: 1px 7px;
    display: inline-block;
    margin-bottom: 7px;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    align-self: flex-start;
}

/* Course title */
.card.dashboard-card .card-title {
    margin-bottom: .42rem !important;
    line-height: 1.3 !important;
}
.card.dashboard-card .card-title a,
.card.dashboard-card .card-title a.aalink,
.card.dashboard-card h3.card-title a {
    font-size: .96rem !important;
    font-weight: 700 !important;
    color: #1a1a2e !important;
    text-decoration: none !important;
    display: -webkit-box !important;
    -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important;
    overflow: hidden !important;
    transition: color .18s !important;
}
.card.dashboard-card:hover .card-title a { color: #c62b3a !important; }

/* Description */
.card.dashboard-card .card-text {
    display: none !important;
}

/* Progress bar */
.card.dashboard-card .progress {
    height: 5px !important;
    border-radius: 6px !important;
    background: #f0f0f0 !important;
    margin-bottom: .4rem !important;
}
.card.dashboard-card .progress-bar {
    background: linear-gradient(90deg, #c62b3a 0%, #e87a84 100%) !important;
    border-radius: 6px !important;
}
.card.dashboard-card .card-footer {
    padding: 0 !important;
    background: transparent !important;
    border: none !important;
}

/* Dark mode cards */
[data-bs-theme="dark"] .card.dashboard-card {
    background: #1e1e2e !important;
    box-shadow: 0 3px 18px rgba(0,0,0,.32) !important;
}
[data-bs-theme="dark"] .card.dashboard-card:hover {
    box-shadow: 0 28px 60px rgba(0,0,0,.42), 0 8px 20px rgba(198,43,58,.16) !important;
}
[data-bs-theme="dark"] .card.dashboard-card .card-title a { color: #f0f0f0 !important; }
[data-bs-theme="dark"] .card.dashboard-card:hover .card-title a { color: #e8717c !important; }
[data-bs-theme="dark"] .card.dashboard-card .card-text { color: #888 !important; }
[data-bs-theme="dark"] .card.dashboard-card .progress { background: #333 !important; }
[data-bs-theme="dark"] .tau-cat-badge { background: rgba(232,113,124,.05) !important; border: 1px solid rgba(232,113,124,.12) !important; color: #e8717c !important; }
/* ── TEACHER ROW on course cards ── */
.tau-teacher-row {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: auto;
    padding-top: .55rem;
    border-top: 1px solid rgba(0,0,0,.055);
    font-size: .74rem;
    color: #888;
    overflow: hidden;
    white-space: nowrap;
}
.tau-tr-icon { flex-shrink: 0; font-size: .85rem; line-height: 1; }
.tau-tr-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex: 1;
    font-weight: 600;
    color: #666;
}
[data-bs-theme="dark"] .tau-teacher-row { border-top-color: rgba(255,255,255,.06); }
[data-bs-theme="dark"] .tau-tr-name { color: #777; }
/* ── FRONT PAGE CARDS ── */
/* ── 11. LOGIN TOAST NOTIFICATIONS ── */
.tau-toast {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    border-radius: 16px;
    border-top: 4px solid #c62b3a;
    padding: 24px 44px 20px 24px;
    box-shadow: 0 20px 60px rgba(0,0,0,.28), 0 4px 16px rgba(0,0,0,.12);
    max-width: 420px;
    width: calc(100vw - 40px);
    z-index: 9999;
    animation: tauToastIn .3s cubic-bezier(.34,1.56,.64,1) both;
}
.tau-toast-title {
    font-weight: 700;
    font-size: .9rem;
    color: #c62b3a;
    margin-bottom: 5px;
}
.tau-toast-body {
    font-size: .84rem;
    color: #444;
    margin-bottom: 8px;
    line-height: 1.5;
}
.tau-toast-contact {
    font-size: .78rem;
    color: #888;
}
.tau-toast-contact a { color: #c62b3a; text-decoration: none; }
.tau-toast-contact a:hover { text-decoration: underline; }
.tau-toast-close {
    position: absolute;
    top: 10px;
    right: 12px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.2rem;
    color: #bbb;
    line-height: 1;
    padding: 2px 4px;
}
.tau-toast-close:hover { color: #666; }
[data-bs-theme="dark"] .tau-toast {
    background: #1e1e2e;
    border-left-color: #e8717c;
    box-shadow: 0 8px 32px rgba(0,0,0,.5);
}
[data-bs-theme="dark"] .tau-toast-title { color: #e8717c; }
[data-bs-theme="dark"] .tau-toast-body { color: #ccc; }
[data-bs-theme="dark"] .tau-toast-contact { color: #999; }
[data-bs-theme="dark"] .tau-toast-contact a { color: #e8717c; }
#page-site-index.notloggedin .marketing-content-section .card {
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,.07);
    border-radius: 14px;
    transition: transform .2s cubic-bezier(.34,1.56,.64,1), box-shadow .2s !important;
}
#page-site-index.notloggedin .marketing-content-section .card:hover {
    transform: translateY(-6px) !important;
    box-shadow: 0 12px 32px rgba(198,43,58,.14) !important;
}

/* ── 12. PERSONAL DASHBOARD WIDGET ── */
@keyframes tauDashIn {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
}
#tau-personal-dashboard {
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 22px 44px rgba(88,16,28,.12), 0 4px 14px rgba(88,16,28,.06);
    margin-bottom: 1.9rem;
    background: #fff;
    border: 1px solid rgba(198,43,58,.08);
    animation: tauDashIn .38s ease both;
}
.tau-pd-header {
    background: linear-gradient(90deg, #5f121d 0%, #791723 24%, #9e1e2d 52%, #c62b3a 78%, #de5567 100%);
    padding: 2.2rem 2.5rem 1.75rem;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.tau-pd-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 88% 24%, rgba(255,255,255,.14) 0%, transparent 30%),
        linear-gradient(90deg, rgba(255,255,255,.03) 0%, rgba(255,255,255,0) 34%);
    pointer-events: none;
}
.tau-pd-greeting {
    font-size: 1.78rem;
    font-weight: 800;
    letter-spacing: -.04em;
    margin-bottom: .35rem;
    position: relative;
    z-index: 1;
}
.tau-pd-subtitle {
    font-size: .9rem;
    opacity: .84;
    font-weight: 500;
    position: relative;
    z-index: 1;
    margin-bottom: 1.35rem;
    max-width: 560px;
}
.tau-pd-prog-wrap { position: relative; z-index: 1; }
.tau-pd-prog-label {
    display: flex;
    justify-content: space-between;
    font-size: .74rem;
    opacity: .86;
    font-weight: 700;
    margin-bottom: .5rem;
    text-transform: uppercase;
    letter-spacing: .08em;
}
.tau-pd-prog-bar {
    height: 8px;
    background: rgba(255,255,255,.18);
    border-radius: 999px;
    overflow: hidden;
    box-shadow: inset 0 1px 2px rgba(0,0,0,.08);
}
.tau-pd-prog-fill {
    height: 100%;
    background: linear-gradient(90deg, rgba(255,255,255,.58) 0%, rgba(255,255,255,.98) 100%);
    border-radius: 999px;
    transition: width .9s cubic-bezier(.4,0,.2,1);
    width: 0;
}
.tau-pd-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    background: linear-gradient(180deg, #fff 0%, #fffafb 100%);
}
@media (max-width: 640px) {
    .tau-pd-stats { grid-template-columns: repeat(2, 1fr); }
    .tau-pd-header { padding: 1.5rem 1.5rem 1.2rem; }
    .tau-pd-greeting { font-size: 1.3rem; }
}
.tau-pd-stat {
    padding: 1.65rem 1.15rem 1.45rem;
    text-align: center;
    border-right: 1px solid rgba(129,34,47,.08);
    position: relative;
    transition: background .2s, box-shadow .2s;
}
.tau-pd-stat:last-child { border-right: none; }
.tau-pd-stat:hover {
    background: linear-gradient(180deg, rgba(198,43,58,.02), rgba(198,43,58,.055));
    box-shadow: inset 0 1px 0 rgba(255,255,255,.6);
}
.tau-pd-stat::after {
    content: '';
    position: absolute;
    bottom: 0; left: 18%; right: 18%;
    height: 3px;
    border-radius: 3px 3px 0 0;
    opacity: 0;
    transition: opacity .2s;
}
.tau-pd-stat:hover::after { opacity: 1; }
.tau-stat-enrolled::after  { background: #8e1f2d; }
.tau-stat-completed::after { background: #aa2635; }
.tau-stat-done::after      { background: #c62b3a; }
.tau-stat-pending::after   { background: #de5567; }
.tau-pd-stat-badge {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto .95rem;
    border: 1px solid rgba(198,43,58,.08);
    background: linear-gradient(180deg, #fff, #fff7f8);
    box-shadow: 0 10px 22px rgba(129,34,47,.09);
}
.tau-pd-stat-badge svg {
    width: 24px;
    height: 24px;
    stroke: currentColor;
    fill: none;
    stroke-width: 1.9;
    stroke-linecap: round;
    stroke-linejoin: round;
}
.tau-stat-enrolled  .tau-pd-stat-badge { color: #8e1f2d; }
.tau-stat-completed .tau-pd-stat-badge { color: #aa2635; }
.tau-stat-done      .tau-pd-stat-badge { color: #c62b3a; }
.tau-stat-pending   .tau-pd-stat-badge { color: #de5567; }
.tau-pd-stat-number {
    font-size: 2.35rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: .42rem;
    letter-spacing: -.06em;
    font-variant-numeric: tabular-nums;
    color: #2b2330;
}
.tau-stat-enrolled  .tau-pd-stat-number { color: #1e3a8a; }
.tau-stat-completed .tau-pd-stat-number { color: #059669; }
.tau-stat-done      .tau-pd-stat-number { color: #0f766e; }
.tau-stat-pending   .tau-pd-stat-number { color: #c62b3a; }
.tau-pd-stat-label {
    font-size: .72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #8a7a81;
}
[data-bs-theme="dark"] #tau-personal-dashboard { background: #1e1e2e; border-color: rgba(232,113,124,.12); }
[data-bs-theme="dark"] .tau-pd-header          { background: linear-gradient(90deg, #4b0f18 0%, #67141f 24%, #8f1f2d 52%, #b32939 78%, #d14d5f 100%); }
[data-bs-theme="dark"] .tau-pd-stats           { background: linear-gradient(180deg, #1e1e2e 0%, #231f2a 100%); }
[data-bs-theme="dark"] .tau-pd-stat            { border-right-color: rgba(255,255,255,.06); }
[data-bs-theme="dark"] .tau-pd-stat:hover      { background: rgba(255,255,255,.03); }
[data-bs-theme="dark"] .tau-pd-stat-badge      { background: linear-gradient(180deg, #2b2632, #221e27); border-color: rgba(232,113,124,.12); box-shadow: none; }
[data-bs-theme="dark"] .tau-pd-stat-label      { color: #94838a; }

/* ── 13. CATEGORY CARDS SECTION ── */
@keyframes tauCatIn { from { opacity: 0; transform: translateY(16px) scale(.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
.tau-cat-section { margin: 2.5rem 0; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 44px rgba(88,16,28,.08), 0 4px 14px rgba(88,16,28,.03); border: 1px solid rgba(198,43,58,.06); }
.tau-cat-header { background: linear-gradient(135deg, #5f121d 0%, #791723 22%, #9e1e2d 54%, #c62b3a 80%, #de5567 100%); padding: 2.2rem 2.5rem 1.8rem; color: #fff; position: relative; overflow: hidden; }
.tau-cat-header::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at 88% 24%, rgba(255,255,255,.14) 0%, transparent 30%); pointer-events: none; }
.tau-cat-header > * { position: relative; z-index: 1; }
.tau-cat-kicker { display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 999px; background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.16); font-size: .7rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; margin-bottom: .85rem; }
.tau-cat-kicker svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.tau-cat-htitle { font-size: 1.6rem; font-weight: 800; letter-spacing: -.03em; margin: 0 0 .35rem; }
.tau-cat-sub { font-size: .88rem; color: rgba(255,255,255,.85); max-width: 650px; margin: 0; line-height: 1.55; }
.tau-cat-body { background: linear-gradient(180deg, #fff8f9 0%, #fff 100%); padding: 2rem; }
.tau-cat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
@media (max-width: 991px) { .tau-cat-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 575px) { .tau-cat-grid { grid-template-columns: 1fr; } }
.tau-cat-card { display: flex; align-items: center; gap: 18px; padding: 24px 28px; border-radius: 24px; border: 1px solid rgba(198,43,58,.07); background: linear-gradient(135deg, #ffffff 0%, #fcfbfb 100%); text-decoration: none !important; color: inherit !important; transition: all .25s cubic-bezier(.25, .8, .25, 1); cursor: pointer; box-shadow: 0 4px 15px rgba(88,16,28,.02); animation: tauCatIn .45s cubic-bezier(.34,1.56,.64,1) both; height: 100%; }
.tau-cat-card:nth-child(2) { animation-delay: .06s; }
.tau-cat-card:nth-child(3) { animation-delay: .12s; }
.tau-cat-card:nth-child(4) { animation-delay: .18s; }
.tau-cat-card:nth-child(5) { animation-delay: .24s; }
.tau-cat-card:nth-child(6) { animation-delay: .3s; }
.tau-cat-card:nth-child(7) { animation-delay: .36s; }
.tau-cat-card:hover { transform: translateY(-5px); box-shadow: 0 12px 28px rgba(198,43,58,.1), 0 4px 10px rgba(0,0,0,.03); border-color: rgba(198,43,58,.16); background: #fff; }
.tau-cat-ico { width: 84px; height: 84px; border-radius: 24px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all .25s cubic-bezier(.25, .8, .25, 1); box-shadow: 0 4px 10px rgba(0,0,0,.03); }
.tau-cat-ico:hover { transform: scale(1.08) rotate(3deg); box-shadow: 0 6px 14px rgba(0,0,0,.06); }
.tau-cat-ico svg { width: 38px; height: 38px; stroke: currentColor; fill: none; stroke-width: 2.2; stroke-linecap: round; stroke-linejoin: round; }
.tau-cat-nm { font-size: .95rem; font-weight: 700; color: #1a1a2e; flex: 1; line-height: 1.35; }
.tau-cat-arr { font-size: 1.2rem; color: #ccc; transition: all .2s ease; }
.tau-cat-card:hover .tau-cat-arr { color: #c62b3a; transform: translateX(4px); }
.tau-cat-toggle-wrap { text-align: center; padding: 1.2rem 0 .5rem; }
.tau-cat-toggle { display: inline-flex; align-items: center; gap: 8px; padding: 10px 28px; border-radius: 999px; border: 1.5px solid rgba(198,43,58,.18); background: #fff; color: #c62b3a; font-size: .82rem; font-weight: 700; cursor: pointer; transition: all .22s; }
.tau-cat-toggle:hover { background: #fff1f3; border-color: #c62b3a; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(198,43,58,.14); }
.tau-cat-toggle svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; transition: transform .3s; }
.tau-cat-toggle.expanded svg { transform: rotate(180deg); }

/* Collapsible container for unified courses */
.tau-courses-collapsible { max-height: 0; opacity: 0; overflow: hidden; transition: max-height 0.6s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease, margin-top 0.4s ease; }
.tau-courses-collapsible.show { max-height: 4000px; opacity: 1; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px dashed rgba(198,43,58,.15); }
.tau-courses-toggle-container { text-align: center; margin: 2.2rem 0 0.5rem; }
.tau-courses-main-btn { display: inline-flex; align-items: center; justify-content: center; gap: 12px; padding: 14px 44px; border-radius: 999px; border: none; background: linear-gradient(135deg, #8e1f2d 0%, #c62b3a 100%); color: #fff !important; font-size: .95rem; font-weight: 700; letter-spacing: .5px; cursor: pointer; box-shadow: 0 10px 30px rgba(198,43,58,.25), 0 4px 10px rgba(0,0,0,.08); transition: all .25s cubic-bezier(.25, .8, .25, 1); position: relative; overflow: hidden; }
.tau-courses-main-btn::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent); transition: all 0.6s ease; }
.tau-courses-main-btn:hover::before { left: 100%; }
.tau-courses-main-btn:hover { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(198,43,58,.35), 0 6px 14px rgba(0,0,0,.1); background: linear-gradient(135deg, #9e2332 0%, #de3c4f 100%); }
.tau-courses-main-btn:active { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(198,43,58,.2); }
.tau-courses-main-btn .tau-btn-icon svg { width: 20px; height: 20px; stroke: currentColor; fill: none; stroke-width: 2.2; stroke-linecap: round; stroke-linejoin: round; vertical-align: middle; }
.tau-courses-main-btn .tau-btn-chevron svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; transition: transform .3s ease; vertical-align: middle; }
.tau-courses-main-btn.expanded .tau-btn-chevron svg { transform: rotate(180deg); }

/* Dark mode overrides */
[data-bs-theme="dark"] .tau-cat-section { border-color: rgba(232,113,124,.1); box-shadow: none; }

/* ── SECCIÓN MARKETING (#feature) — dark mode ── */
/* Fondo del contenedor de página principal */
[data-bs-theme="dark"] #page,
[data-bs-theme="dark"] #page-wrapper,
[data-bs-theme="dark"] #page-content,
[data-bs-theme="dark"] #region-main,
[data-bs-theme="dark"] #region-main-box {
    background: #111318 !important;
}
/* Sección #feature completa */
[data-bs-theme="dark"] #feature {
    background: #111318 !important;
}
/* Texto izquierdo: heading principal y descripción */
[data-bs-theme="dark"] #feature .col-lg-4 h3,
[data-bs-theme="dark"] #feature .col-lg-4 .h1,
[data-bs-theme="dark"] #feature .col-lg-4 h3.h1 {
    color: #f2eef0 !important;
}
[data-bs-theme="dark"] #feature .marketing-content,
[data-bs-theme="dark"] #feature .marketing-content p,
[data-bs-theme="dark"] #feature .col-lg-4 p {
    color: rgba(255,255,255,.62) !important;
}
/* Tarjetas derechas (marketing items 1-4) */
[data-bs-theme="dark"] #feature .card.card-body {
    background: #1e1a24 !important;
    border: 1px solid rgba(255,255,255,.06) !important;
    box-shadow: 0 4px 18px rgba(0,0,0,.28) !important;
}
[data-bs-theme="dark"] #feature .card.card-body h5 {
    color: #f0ecf2 !important;
}
[data-bs-theme="dark"] #feature .card.card-body .box-content,
[data-bs-theme="dark"] #feature .card.card-body .box-content p {
    color: rgba(255,255,255,.60) !important;
}
/* Sección de números / estadísticas del frontpage */
[data-bs-theme="dark"] .moove-container-fluid,
[data-bs-theme="dark"] .customer-area-l1 {
    background: #111318 !important;
    color: rgba(255,255,255,.70) !important;
}
[data-bs-theme="dark"] .customer-area-l1 h2 {
    color: #f2eef0 !important;
}
[data-bs-theme="dark"] .customer-area-l1 p {
    color: rgba(255,255,255,.55) !important;
}
[data-bs-theme="dark"] .tau-cat-header { background: linear-gradient(135deg, #4b0f18 0%, #67141f 22%, #8f1f2d 54%, #b32939 80%, #d14d5f 100%); }
[data-bs-theme="dark"] .tau-cat-body { background: linear-gradient(180deg, #1f1c24 0%, #1a1720 100%); }
[data-bs-theme="dark"] .tau-cat-card { background: linear-gradient(135deg, #241f28 0%, #1e1a22 100%); border-color: rgba(255,255,255,.05); box-shadow: 0 4px 15px rgba(0,0,0,.15); }
[data-bs-theme="dark"] .tau-cat-card:hover { border-color: rgba(232,113,124,.18); box-shadow: 0 12px 28px rgba(0,0,0,.3); background: #27212c; }
[data-bs-theme="dark"] .tau-cat-nm { color: #f3eff2; }
[data-bs-theme="dark"] .tau-cat-card:hover .tau-cat-arr { color: #e8717c; }
[data-bs-theme="dark"] .tau-cat-toggle { background: #241f28; border-color: rgba(232,113,124,.15); color: #e8717c; }
[data-bs-theme="dark"] .tau-courses-collapsible.show { border-top-color: rgba(232,113,124,.15); }
[data-bs-theme="dark"] .tau-courses-main-btn { background: linear-gradient(135deg, #aa2635 0%, #de5567 100%); box-shadow: 0 10px 30px rgba(232,113,124,.15); }
[data-bs-theme="dark"] .tau-courses-main-btn:hover { background: linear-gradient(135deg, #b32939 0%, #e36878 100%); box-shadow: 0 15px 35px rgba(232,113,124,.25); }

/* Frontpage dark mode: dark outer canvas without breaking hero/content/footer blocks */
[data-bs-theme="dark"] body.path-site,
[data-bs-theme="dark"] body.pagelayout-frontpage,
[data-bs-theme="dark"] body.path-site #page-wrapper,
[data-bs-theme="dark"] body.pagelayout-frontpage #page-wrapper,
[data-bs-theme="dark"] body.path-site #page.drawers.drag-container.bg-white,
[data-bs-theme="dark"] body.pagelayout-frontpage #page.drawers.drag-container.bg-white {
    background: #111318 !important;
}
[data-bs-theme="dark"] body.path-site #topofscroll,
[data-bs-theme="dark"] body.pagelayout-frontpage #topofscroll,
[data-bs-theme="dark"] body.path-site #page-content,
[data-bs-theme="dark"] body.pagelayout-frontpage #page-content,
[data-bs-theme="dark"] body.path-site #region-main,
[data-bs-theme="dark"] body.pagelayout-frontpage #region-main,
[data-bs-theme="dark"] body.path-site #region-main-box,
[data-bs-theme="dark"] body.pagelayout-frontpage #region-main-box,
[data-bs-theme="dark"] body.path-site .main-inner,
[data-bs-theme="dark"] body.pagelayout-frontpage .main-inner,
[data-bs-theme="dark"] body.path-site .moove-container-fluid,
[data-bs-theme="dark"] body.pagelayout-frontpage .moove-container-fluid,
[data-bs-theme="dark"] body.path-site .customer-area-l1,
[data-bs-theme="dark"] body.pagelayout-frontpage .customer-area-l1,
[data-bs-theme="dark"] body.path-site #frontpage-available-course-list,
[data-bs-theme="dark"] body.pagelayout-frontpage #frontpage-available-course-list {
    background: transparent !important;
}
/* ── 14. LOGIN CARD SYMMETRY ── */
body#page-login-index .login-container .loginform,
body#page-login-index .tau-login-card-presencial .loginform { margin-top: 20px !important; }
body#page-login-index .login-container,
body#page-login-index .tau-login-card-presencial {
    width: 390px !important;
    min-height: 300px !important;
}
/* Hide native footer and usertours on login page */
body#page-login-index footer,
body#page-login-index #page-footer,
body#page-login-index .moove-footer,
body#page-login-index .tau-login-global-footer,
body#page-login-index footer.footer-dark,
body#page-login-index footer.footer-light,
body#page-login-index #page-footer.footer-dark,
body#page-login-index #page-footer.footer-light,
body#page-login-index .usertour,
body#page-login-index .toolRegionActive,
body#page-login-index .popover-region,
body#page-login-index #goto-top-link,
body#page-login-index .login-languagemenu {
    display: none !important;
}
body#page-login-index .login-identityprovider-btn { width: 100% !important; padding: 13px 24px !important; border-radius: 12px !important; border: 1.5px solid #e0e0e0 !important; background: #fff !important; color: #333 !important; font-weight: 600 !important; font-size: .92rem !important; display: flex !important; align-items: center !important; justify-content: center !important; gap: 10px !important; transition: all .2s !important; box-shadow: 0 2px 8px rgba(0,0,0,.06) !important; }
body#page-login-index .login-identityprovider-btn:hover { border-color: #c62b3a !important; box-shadow: 0 8px 24px rgba(198,43,58,.15) !important; transform: translateY(-2px) !important; }
/* ── 15. FULL FOOTER DARK MODE ── */
[data-bs-theme="dark"] #page-footer, [data-bs-theme="dark"] footer#page-footer, [data-bs-theme="dark"] #page-footer .container-fluid { background: linear-gradient(135deg, #1b0205 0%, #2a080d 50%, #380a11 100%) !important; }

[data-bs-theme="dark"] .tau-stat-enrolled  .tau-pd-stat-badge { color: #f0a4af; }
[data-bs-theme="dark"] .tau-stat-completed .tau-pd-stat-badge { color: #f3b2ba; }
[data-bs-theme="dark"] .tau-stat-done      .tau-pd-stat-badge { color: #ffc3cb; }
[data-bs-theme="dark"] .tau-stat-pending   .tau-pd-stat-badge { color: #ffd3d8; }
[data-bs-theme="dark"] .tau-pd-stat-number { color: #f6edf0; }

/* ── 16. HOMEPAGE BANNER, CARD TAGS & NATIVE SEARCH OVERRIDES ── */
#mooveslideshow .carousel-caption {
    display: none !important;
}
#mooveslideshow .carousel-item {
    position: relative !important;
    overflow: hidden !important;
    border-radius: 20px !important;
}
/* ── HERO: crimson abstract background, ocultar foto ── */
#mooveslideshow .carousel-item {
    background:
        radial-gradient(ellipse 60% 85% at 16% 55%, rgba(123,25,41,.72) 0%, rgba(74,13,24,.35) 50%, transparent 70%),
        radial-gradient(ellipse 50% 55% at 86% 20%, rgba(192,37,58,.20) 0%, transparent 78%),
        radial-gradient(ellipse 42% 42% at 88% 88%, rgba(92,17,32,.42) 0%, transparent 48%),
        #0e0812 !important;
    min-height: 440px !important;
}
#mooveslideshow .carousel-item > img.d-block {
    opacity: 0 !important;
    pointer-events: none !important;
    position: absolute !important;
    inset: 0 !important;
}
/* Dot grid sutil */
#mooveslideshow .carousel-item::before {
    content: "" !important;
    position: absolute !important;
    inset: 0 !important;
    background-image: radial-gradient(circle, rgba(255,255,255,.055) 1px, transparent 1px) !important;
    background-size: 44px 44px !important;
    pointer-events: none !important;
    z-index: 1 !important;
}
/* Arcos concentricos izquierda + vignette */
#mooveslideshow .carousel-item::after {
    content: "" !important;
    position: absolute !important;
    inset: 0 !important;
    pointer-events: none !important;
    background:
        radial-gradient(circle at 0% 50%, transparent 245px, rgba(198,43,58,.15) 246px, rgba(198,43,58,.15) 249px, transparent 250px),
        radial-gradient(circle at 0% 50%, transparent 370px, rgba(198,43,58,.09) 371px, rgba(198,43,58,.09) 373px, transparent 374px),
        radial-gradient(ellipse 100% 100% at 50% 50%, transparent 52%, rgba(0,0,0,.48) 100%) !important;
    z-index: 2 !important;
}
#mooveslideshow .carousel-item img {
    max-height: 480px !important;
    object-fit: cover !important;
    border-radius: 20px !important;
    box-shadow: 0 16px 40px rgba(0,0,0,0.18) !important;
}
.tau-banner-overlay {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 0 8% !important;
    z-index: 10 !important;
    box-sizing: border-box !important;
}
/* ── DECORATIVO DERECHA ── */
.tau-banner-deco {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 20px !important;
    flex-shrink: 0 !important;
    animation: tauDecoIn 1s ease .5s both !important;
}
.tau-deco-ring-wrap {
    position: relative !important;
    width: 188px !important;
    height: 188px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}
.tau-deco-svg-outer {
    position: absolute !important;
    inset: 0 !important;
    width: 188px !important;
    height: 188px !important;
    animation: tauDecoSpin 28s linear infinite !important;
}
.tau-deco-svg-inner {
    position: absolute !important;
    inset: 0 !important;
    width: 188px !important;
    height: 188px !important;
    animation: tauDecoSpin 18s linear infinite reverse !important;
}
.tau-deco-center {
    position: relative !important;
    z-index: 1 !important;
    text-align: center !important;
}
.tau-deco-num {
    font-size: 2.4rem !important;
    font-weight: 800 !important;
    color: #fff !important;
    line-height: 1 !important;
    letter-spacing: -.04em !important;
}
.tau-deco-lbl {
    font-size: .56rem !important;
    font-weight: 800 !important;
    letter-spacing: .18em !important;
    text-transform: uppercase !important;
    color: rgba(255,255,255,.38) !important;
    margin-top: 4px !important;
}
.tau-deco-pills {
    display: flex !important;
    gap: 10px !important;
}
.tau-deco-pill {
    background: rgba(198,43,58,.12) !important;
    border: 1px solid rgba(198,43,58,.28) !important;
    border-radius: 16px !important;
    padding: 8px 16px !important;
    text-align: center !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
}
.tau-deco-pill-num {
    display: block !important;
    font-size: 1.05rem !important;
    font-weight: 800 !important;
    color: #fff !important;
    line-height: 1.1 !important;
    letter-spacing: -.02em !important;
}
.tau-deco-pill-lbl {
    display: block !important;
    font-size: .52rem !important;
    font-weight: 800 !important;
    letter-spacing: .14em !important;
    text-transform: uppercase !important;
    color: rgba(255,255,255,.38) !important;
    margin-top: 2px !important;
}
@keyframes tauDecoSpin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
@keyframes tauDecoIn { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
@media (max-width: 991px) { .tau-banner-deco { display: none !important; } }
.tau-banner-card {
    background: rgba(15, 2, 5, 0.5) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-left: 5px solid #c62b3a !important;
    border-radius: 24px !important;
    padding: 30px 36px !important;
    max-width: 480px !important;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45) !important;
    text-align: left !important;
}
.tau-banner-pretitle {
    font-size: 0.7rem !important;
    font-weight: 800 !important;
    letter-spacing: 2px !important;
    color: #e87a84 !important;
    text-transform: uppercase !important;
    margin-bottom: 6px !important;
}
.tau-banner-title {
    font-size: 2.1rem !important;
    font-weight: 800 !important;
    color: #fff !important;
    line-height: 1.15 !important;
    margin-bottom: 8px !important;
    letter-spacing: -0.6px !important;
}
.tau-accent-text {
    background: linear-gradient(90deg, #fff 0%, #ffc5cb 100%) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
}
.tau-banner-subtitle {
    font-size: 1.05rem !important;
    font-weight: 700 !important;
    color: #c62b3a !important;
    background: rgba(198, 43, 58, 0.15) !important;
    border: 1px solid rgba(198, 43, 58, 0.3) !important;
    padding: 3px 12px !important;
    border-radius: 8px !important;
    display: inline-block !important;
    margin-bottom: 15px !important;
    letter-spacing: 1px !important;
}
.tau-banner-desc {
    font-size: 0.88rem !important;
    color: rgba(255, 255, 255, 0.88) !important;
    line-height: 1.55 !important;
    margin-bottom: 20px !important;
    font-weight: 400 !important;
}
.btn-tau-banner-explore {
    background: linear-gradient(135deg, #c62b3a 0%, #8e1f2d 100%) !important;
    border: none !important;
    color: #fff !important;
    border-radius: 12px !important;
    padding: 10px 24px !important;
    font-weight: 700 !important;
    font-size: 0.88rem !important;
    letter-spacing: 0.5px !important;
    box-shadow: 0 8px 20px rgba(198,43,58,0.25) !important;
    transition: all 0.22s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    text-decoration: none !important;
}
.btn-tau-banner-explore:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 12px 28px rgba(198,43,58,0.4) !important;
    color: #fff !important;
}
@media (max-width: 767px) {
    .tau-banner-overlay {
        position: relative !important;
        padding: 20px !important;
        background: #0d1117 !important;
        display: block !important;
        height: auto !important;
    }
    .tau-banner-card {
        max-width: 100% !important;
        border-radius: 16px !important;
        padding: 20px !important;
        margin: 0 auto !important;
    }
    .tau-banner-title {
        font-size: 1.6rem !important;
    }
}
.card.dashboard-card .course-category,
.card.dashboard-card .coursecat,
.coursebox .course-category,
.coursebox .coursecat {
    display: none !important;
}

/* Hide native categories names listing, search box, and other native elements completely */
#page-site-index #region-main > div[role="main"] > h2,
#page-site-index #region-main > div[role="main"] > .skip-block,
#page-site-index #frontpage-category-names,
#page-site-index #frontpage-category-combo,
#page-site-index .frontpage-category-names,
#page-site-index .course-category-listing,
#page-site-index #region-main > div[role="main"] > br,
#page-site-index #region-main > div[role="main"] > h2.frontpage-course-list-all-title,
#page-site-index #region-main > div[role="main"] > .frontpage-course-list-all-title {
    display: none !important;
}

/* ── 17. PREMIUM MARKETING BOX OVERRIDES ── */
#feature .icon-lg {
    background: linear-gradient(135deg, #c62b3a 0%, #8e1f2d 100%) !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #fff !important;
    width: 54px !important;
    height: 54px !important;
    border-radius: 16px !important;
    box-shadow: 0 8px 20px rgba(198,43,58,0.2) !important;
    transition: transform 0.2s ease !important;
    flex-shrink: 0 !important;
}
#feature .icon-lg:hover {
    transform: scale(1.06) !important;
}
#feature .icon-lg svg {
    width: 28px !important;
    height: 28px !important;
    stroke: #fff !important;
}
#feature .card {
    border-radius: 18px !important;
    border: 1px solid rgba(0,0,0,0.04) !important;
    box-shadow: 0 8px 24px rgba(0,0,0,0.04) !important;
    transition: transform 0.22s ease, box-shadow 0.22s ease !important;
}
#feature .card:hover {
    transform: translateY(-4px) !important;
    box-shadow: 0 16px 36px rgba(198,43,58,0.08) !important;
    border-color: rgba(198,43,58,0.12) !important;
}

/* ── 18. PREMIUM MODALS & COOKIE NOTICE STYLE ── */
.modal-content {
    border: none !important;
    border-radius: 24px !important;
    box-shadow: 0 24px 60px rgba(0,0,0,0.22) !important;
    background: rgba(255,255,255,0.98) !important;
    backdrop-filter: blur(12px) !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
    overflow: hidden !important;
}
[data-bs-theme="dark"] .modal-content {
    background: rgba(30,30,46,0.96) !important;
    border-color: rgba(255,255,255,0.06) !important;
}
.modal-header {
    background: linear-gradient(135deg, #c62b3a 0%, #e87a84 100%) !important;
    color: #fff !important;
    padding: 18px 24px !important;
    border-bottom: none !important;
}
[data-bs-theme="dark"] .modal-header {
    background: linear-gradient(135deg, #8e1f2d 0%, #d14d5f 100%) !important;
}
.modal-header .modal-title, 
.modal-header h5 {
    font-weight: 800 !important;
    color: #fff !important;
    letter-spacing: -0.3px !important;
    font-size: 1.15rem !important;
}
.modal-header .btn-close,
.modal-header .close {
    color: #fff !important;
    opacity: 0.8 !important;
    text-shadow: none !important;
    transition: opacity 0.2s !important;
}
.modal-header .btn-close:hover,
.modal-header .close:hover {
    opacity: 1 !important;
}
.modal-body {
    padding: 24px 28px !important;
    font-size: 0.88rem !important;
    line-height: 1.6 !important;
    color: #444 !important;
}
[data-bs-theme="dark"] .modal-body {
    color: #ddd !important;
}
.modal-body p {
    margin-bottom: 14px !important;
}
.modal-footer {
    padding: 16px 24px !important;
    border-top: 1px dashed rgba(0,0,0,0.06) !important;
    background: rgba(0,0,0,0.01) !important;
}
[data-bs-theme="dark"] .modal-footer {
    border-top-color: rgba(255,255,255,0.06) !important;
    background: rgba(255,255,255,0.01) !important;
}
.modal-footer .btn-primary {
    background: #c62b3a !important;
    border-color: #c62b3a !important;
    border-radius: 12px !important;
    padding: 8px 20px !important;
    font-weight: 700 !important;
}
.modal-footer .btn-secondary {
    background: rgba(0,0,0,0.05) !important;
    border: 1px solid rgba(0,0,0,0.08) !important;
    color: #555 !important;
    border-radius: 12px !important;
    padding: 8px 20px !important;
    font-weight: 700 !important;
}
[data-bs-theme="dark"] .modal-footer .btn-secondary {
    background: rgba(255,255,255,0.06) !important;
    border-color: rgba(255,255,255,0.1) !important;
    color: #ccc !important;
}

/* ── 20. STUDENT VIEW - COURSE ACTIVITIES & BANNERS ── */

/* Banners Inyectados por el Constructor TAU */
.tau-banner-modulo {
    background: linear-gradient(90deg, #161b22 0%, #0d1117 100%);
    border-radius: 12px;
    padding: 16px 24px;
    margin: 30px 0 20px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-left: 6px solid #c62b3a;
    box-shadow: 0 8px 24px rgba(0,0,0,.15);
}
.tau-banner-modulo span {
    font-size: 1.35rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.02em;
}

.tau-banner-separador {
    background: #1e1e2e;
    border-radius: 8px;
    padding: 10px 20px;
    margin: 25px 0 15px 0;
    display: inline-block;
    border-bottom: 2px solid #00d2ff;
    position: relative;
    box-shadow: 0 4px 10px rgba(0,0,0,.1);
}
.tau-banner-separador::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0; width: 4px;
    background: #00d2ff;
    border-radius: 8px 0 0 8px;
}
.tau-banner-separador.tau-banner-tema::before { background: #00d2ff; }
.tau-banner-separador.tau-banner-complementario::before { background: #ff914d; }
.tau-banner-separador.tau-banner-actividad::before { background: #6c5ce7; }
.tau-banner-separador.tau-banner-evaluacion::before { background: #20bf6b; }
.tau-banner-separador span {
    font-size: 1.05rem;
    font-weight: 700;
    color: #fff;
}

/* Tarjetas de recursos/actividades horizontales adaptadas a Modo Claro/Oscuro */
[data-bs-theme="dark"] .course-content .activity {
    background: #1e1e2e !important;
    border: 1px solid rgba(255,255,255,.05) !important;
    border-radius: 12px !important;
    margin-bottom: 12px !important;
    padding: 16px !important;
    transition: transform 0.2s, box-shadow 0.2s !important;
    box-shadow: 0 4px 12px rgba(0,0,0,.1) !important;
}
[data-bs-theme="light"] .course-content .activity,
:not([data-bs-theme="dark"]) .course-content .activity {
    background: #fff !important;
    border: 1px solid rgba(0,0,0,.08) !important;
    border-radius: 12px !important;
    margin-bottom: 12px !important;
    padding: 16px !important;
    transition: transform 0.2s, box-shadow 0.2s !important;
    box-shadow: 0 4px 12px rgba(0,0,0,.03) !important;
}
[data-bs-theme="dark"] .course-content .activity:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 20px rgba(0,0,0,.15) !important;
    border-color: rgba(255,255,255,.1) !important;
}
[data-bs-theme="light"] .course-content .activity:hover,
:not([data-bs-theme="dark"]) .course-content .activity:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 20px rgba(0,0,0,.08) !important;
    border-color: rgba(0,0,0,.15) !important;
}
.course-content .activity .activityinstance {
    display: flex !important;
    align-items: center !important;
    gap: 15px !important;
}
[data-bs-theme="dark"] .course-content .activity .activityicon {
    /* Kept intentionally empty to use Moodle's native icon styling */
}
[data-bs-theme="light"] .course-content .activity .activityicon,
:not([data-bs-theme="dark"]) .course-content .activity .activityicon {
    /* Kept intentionally empty to use Moodle's native icon styling */
}
[data-bs-theme="dark"] .course-content .activity .instancename {
    color: #e0e0e0 !important;
    font-weight: 600 !important;
    font-size: 1.05rem !important;
}
[data-bs-theme="light"] .course-content .activity .instancename,
:not([data-bs-theme="dark"]) .course-content .activity .instancename {
    color: #333 !important;
    font-weight: 600 !important;
    font-size: 1.05rem !important;
}
[data-bs-theme="dark"] .course-content .activity:hover .instancename { color: #00d2ff !important; }
[data-bs-theme="light"] .course-content .activity:hover .instancename,
:not([data-bs-theme="dark"]) .course-content .activity:hover .instancename { color: #c62b3a !important; }
[data-bs-theme="dark"] .course-content .activity .contentwithoutlink { color: #aaa !important; }
[data-bs-theme="light"] .course-content .activity .contentwithoutlink,
:not([data-bs-theme="dark"]) .course-content .activity .contentwithoutlink { color: #666 !important; }

/* Modificar tabla de estado de entrega de tareas */
.path-mod-assign .generaltable {
    background: #1e1e2e !important;
    color: #e0e0e0 !important;
    border-radius: 12px;
    overflow: hidden;
}
.path-mod-assign .generaltable th, .path-mod-assign .generaltable td {
    border-color: #333 !important;
}
.path-mod-assign .submissionstatussubmitted, 
.path-mod-assign .submissiongraded {
    background-color: #1e4620 !important;
    color: #81c784 !important;
}

/* ── Assignment Redesign ── */
.path-mod-assign #region-main {
    background: #fff;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(198,43,58,0.05);
    border: 1px solid rgba(198,43,58,0.1);
}
[data-bs-theme="dark"] .path-mod-assign #region-main {
    background: #1e1e2e;
    border-color: rgba(232,113,124,0.12);
}
.path-mod-assign h2, .path-mod-assign h3 {
    color: #c62b3a;
    font-weight: 800;
}
[data-bs-theme="dark"] .path-mod-assign h2, [data-bs-theme="dark"] .path-mod-assign h3 {
    color: #e8717c;
}
.path-mod-assign .submissionstatustable {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #f0dde1;
}
[data-bs-theme="dark"] .path-mod-assign .submissionstatustable {
    border-color: rgba(255,255,255,0.08);
}
.path-mod-assign .submissionstatustable th {
    background: #fff8f9;
    color: #a32230;
    font-weight: 700;
}
[data-bs-theme="dark"] .path-mod-assign .submissionstatustable th {
    background: #2b2632;
    color: #f5edf0;
}
.path-mod-assign .submissionaction .btn {
    background: linear-gradient(135deg, #c62b3a 0%, #8b1524 100%) !important;
    color: #fff !important;
    padding: 12px 30px;
    border-radius: 50px;
    font-weight: 700;
    border: none;
    box-shadow: 0 8px 20px rgba(198,43,58,0.3);
}

NEWSCSS;

// ─── Pre-SCSS: Bootstrap variable overrides ──────────────────────────────────

$prescss = '$brand-color: #c62b3a;';

// ─── Apply theme config ──────────────────────────────────────────────────────

$themeSettings = [
    'brandcolor' => '#c62b3a',
    'scss'       => $extrascss,
    'scsspre'    => $prescss,
];

foreach ($themeSettings as $name => $value) {
    set_config($name, $value, 'theme_moove');
    fwrite(STDOUT, "  [ok] Set theme_moove/{$name}\n");
}

// ─── Front page: slider & marketing boxes ───────────────────────────────────

set_config('slidercount', '1', 'theme_moove');
set_config('slidertitle1', 'Bienvenido a TAU Campus Virtual', 'theme_moove');
set_config('slidercap1',   'Tu plataforma de aprendizaje oficial de la Universidad CESMAG en línea', 'theme_moove');

set_config('displaymarketingbox', '1', 'theme_moove');
set_config('marketingheading', 'Todo lo que necesitas para aprender', 'theme_moove');
set_config('marketingcontent',
    'TAU Campus Virtual te conecta con tus cursos, docentes y compañeros. '
    . 'Accede a todo tu material académico desde cualquier dispositivo.',
    'theme_moove'
);

$marketing = [
    1 => ['Inteligencia Artificial', 'Descubre cómo las herramientas de IA generativa y tutores inteligentes personalizan tu ritmo de estudio y mejoran tu retención de conocimientos.'],
    2 => ['Microaprendizaje Avanzado', 'Módulos de aprendizaje ágiles y enfocados en habilidades de alta demanda tecnológica, listos para potenciar tu perfil profesional.'],
    3 => ['Plataformas Colaborativas', 'Conéctate con comunidades de desarrollo, repositorios abiertos y proyectos globales de investigación directamente en el Campus Virtual.'],
    4 => ['Realidad Extendida', 'La tecnología inmersiva llega al aula. Explora laboratorios interactivos y simulaciones avanzadas que llevan la teoría a la práctica.'],
];
foreach ($marketing as $i => [$heading, $content]) {
    set_config("marketing{$i}heading", $heading, 'theme_moove');
    set_config("marketing{$i}content", $content, 'theme_moove');
    fwrite(STDOUT, "  [ok] marketing{$i}\n");
}

// Numbers section
set_config('numbersfrontpage', '1', 'theme_moove');
set_config('numbersfrontpagecontent', '<h2>Impulsando el futuro del aprendizaje profesional en la Universidad CESMAG</h2><p>TAU Campus Virtual es nuestro ecosistema digital diseñado para conectar a estudiantes y docentes con las mejores metodologías ágiles, tecnología de vanguardia y una comunidad académica comprometida con la excelencia y la innovación educativa.</p>', 'theme_moove');

// Social / footer links
set_config('mail', 'tau-ayuda@unicesmag.edu.co', 'theme_moove');

fwrite(STDOUT, "  [ok] Front page configured\n");

// ─── Custom navigation menu ─────────────────────────────────────────────────

$custommenuitems =
    "Soporte Campus Virtual|#\n" .
    "-Tutoriales|/local/tau_soporte/tutoriales.php\n" .
    "-Realizar Solicitud|/local/tau_soporte/solicitud.php";

set_config('custommenuitems', $custommenuitems);
fwrite(STDOUT, "  [ok] Custom menu (AI + Soporte)\n");

// ─── Dashboard CSS (injected inline via JS — bypasses SCSS pipeline) ────────

$dashboard_css_raw =
    '@keyframes tauDashIn{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}' .
    '#tau-personal-dashboard{border-radius:20px;overflow:hidden;box-shadow:0 10px 30px rgba(88,16,28,.08);margin-bottom:2rem;background:#fff;border:1px solid rgba(198,43,58,.08);animation:tauDashIn .5s cubic-bezier(0.16,1,0.3,1) both}' .
    '.tau-pd-header{background:linear-gradient(135deg,#8d182a 0%,#c62b3a 100%);padding:2.3rem 2.5rem 2.0rem;color:#fff;position:relative;overflow:hidden}' .
    '.tau-pd-header::before{content:"";position:absolute;inset:0;background-image:radial-gradient(circle at 90% 10%,rgba(255,255,255,.12) 0%,transparent 40%),linear-gradient(90deg,rgba(255,255,255,.03) 0%,rgba(255,255,255,0) 50%);pointer-events:none}' .
    '.tau-pd-greeting{font-size:1.85rem;font-weight:800;letter-spacing:-.03em;margin-bottom:.35rem;position:relative;z-index:1;display:flex;align-items:center;gap:10px}' .
    '.tau-pd-subtitle{font-size:.95rem;opacity:.9;font-weight:500;position:relative;z-index:1;margin-bottom:0;max-width:600px}' .
    '.tau-pd-prog-wrap{margin-top:1.4rem;position:relative;z-index:1}' .
    '.tau-pd-prog-label{display:flex;justify-content:space-between;font-size:.78rem;opacity:.9;font-weight:700;margin-bottom:.55rem;text-transform:uppercase;letter-spacing:.06em}' .
    '.tau-pd-prog-bar{height:10px;background:rgba(255,255,255,.2);border-radius:999px;overflow:hidden;box-shadow:inset 0 1px 2px rgba(0,0,0,.1)}' .
    '.tau-pd-prog-fill{height:100%;background:linear-gradient(90deg,#fff 0%,#fff 100%);border-radius:999px;transition:width 1.2s cubic-bezier(.34,1.56,.64,1);width:0}' .
    '.tau-pd-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;padding:24px;background:#fdfdfd}' .
    '@media(max-width:992px){.tau-pd-stats{grid-template-columns:repeat(2,1fr)}}' .
    '@media(max-width:576px){.tau-pd-stats{grid-template-columns:1fr}}' .
    '.tau-pd-stat{background:#fff;border:1px solid rgba(198,43,58,.08);border-radius:16px;padding:1.8rem 1.4rem;text-align:center;position:relative;transition:transform .3s cubic-bezier(.34,1.56,.64,1),box-shadow .3s,border-color .3s;box-shadow:0 4px 12px rgba(0,0,0,.02);display:flex;flex-direction:column;align-items:center;justify-content:center;overflow:hidden}' .
    '.tau-pd-stat:hover{transform:translateY(-6px);box-shadow:0 12px 28px rgba(198,43,58,.12);border-color:rgba(198,43,58,.25)}' .
    '.tau-pd-stat::after{content:"";position:absolute;bottom:0;left:0;right:0;height:4px;background:transparent;transition:background .3s}' .
    '.tau-stat-card-1::after{background:linear-gradient(90deg,#8d182a,#c62b3a)}' .
    '.tau-stat-card-2::after{background:linear-gradient(90deg,#1e3a8a,#3b82f6)}' .
    '.tau-stat-card-3::after{background:linear-gradient(90deg,#0f766e,#14b8a6)}' .
    '.tau-stat-card-4::after{background:linear-gradient(90deg,#b02230,#e84393)}' .
    '.tau-pd-stat-badge{width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;transition:transform .4s cubic-bezier(.34,1.56,.64,1);box-shadow:0 8px 20px rgba(0,0,0,.04)}' .
    '.tau-pd-stat:hover .tau-pd-stat-badge{transform:scale(1.1) rotate(5deg)}' .
    '.tau-stat-card-1 .tau-pd-stat-badge{color:#c62b3a;background:rgba(198,43,58,.08)}' .
    '.tau-stat-card-2 .tau-pd-stat-badge{color:#1e3a8a;background:rgba(30,58,138,.08)}' .
    '.tau-stat-card-3 .tau-pd-stat-badge{color:#0f766e;background:rgba(15,118,110,.08)}' .
    '.tau-stat-card-4 .tau-pd-stat-badge{color:#d63031;background:rgba(214,48,49,.08)}' .
    '.tau-pd-stat-badge svg{width:26px;height:26px;stroke:currentColor;fill:none;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round}' .
    '.tau-pd-stat-number{font-size:2.5rem;font-weight:800;line-height:1.1;margin-bottom:.5rem;letter-spacing:-.05em;font-variant-numeric:tabular-nums;transition:color .3s}' .
    '.tau-stat-card-1 .tau-pd-stat-number{color:#8d182a}' .
    '.tau-stat-card-2 .tau-pd-stat-number{color:#1e3a8a}' .
    '.tau-stat-card-3 .tau-pd-stat-number{color:#0f766e}' .
    '.tau-stat-card-4 .tau-pd-stat-number{color:#b02230}' .
    '.tau-pd-stat-label{font-size:.78rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#555;margin:0}' .
    '[data-bs-theme=dark] #tau-personal-dashboard{background:#1e1e2e;border-color:rgba(232,113,124,.15);box-shadow:0 10px 30px rgba(0,0,0,.3)}' .
    '[data-bs-theme=dark] .tau-pd-stats{background:#151522}' .
    '[data-bs-theme=dark] .tau-pd-stat{background:#1e1e2e;border-color:rgba(255,255,255,.06)}' .
    '[data-bs-theme=dark] .tau-pd-stat:hover{border-color:rgba(232,113,124,.3);box-shadow:0 12px 28px rgba(232,113,124,.15)}' .
    '[data-bs-theme=dark] .tau-stat-card-1 .tau-pd-stat-badge{color:#ff6b81;background:rgba(232,113,124,.15)}' .
    '[data-bs-theme=dark] .tau-stat-card-2 .tau-pd-stat-badge{color:#70a1ff;background:rgba(112,161,255,.15)}' .
    '[data-bs-theme=dark] .tau-stat-card-3 .tau-pd-stat-badge{color:#2ed573;background:rgba(46,213,115,.15)}' .
    '[data-bs-theme=dark] .tau-stat-card-4 .tau-pd-stat-badge{color:#ffa502;background:rgba(255,165,2,.15)}' .
    '[data-bs-theme=dark] .tau-stat-card-1 .tau-pd-stat-number{color:#ff6b81}' .
    '[data-bs-theme=dark] .tau-stat-card-2 .tau-pd-stat-number{color:#70a1ff}' .
    '[data-bs-theme=dark] .tau-stat-card-3 .tau-pd-stat-number{color:#2ed573}' .
    '[data-bs-theme=dark] .tau-stat-card-4 .tau-pd-stat-number{color:#ffa502}' .
    '[data-bs-theme=dark] .tau-pd-stat-label{color:#aaa}' .
    '.tau-teacher-row{display:flex;align-items:center;gap:5px;margin-top:auto;padding-top:.55rem;border-top:1px solid rgba(0,0,0,.055);font-size:.74rem;overflow:hidden;white-space:nowrap}' .
    '.tau-tr-icon{flex-shrink:0;font-size:.85rem;line-height:1}' .
    '.tau-tr-name{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;font-weight:600;color:#666}' .
    '[data-bs-theme=dark] .tau-teacher-row{border-top-color:rgba(255,255,255,.06)}' .
    '[data-bs-theme=dark] .tau-tr-name{color:#777}' .
    '.tau-header-buttons-container{display:flex!important;align-items:center!important;justify-content:flex-end!important;gap:12px!important;flex-wrap:nowrap!important;flex:1 1 auto!important;width:100%!important;max-width:1140px!important;margin-left:auto!important}' .
    '.tau-header-buttons-container > a,.tau-header-buttons-container > form,.tau-header-buttons-container > button{flex:1 1 0%!important;max-width:380px!important;width:100%!important;height:42px!important;border-radius:8px!important;font-size:0.88rem!important;font-weight:700!important;display:inline-flex!important;align-items:center!important;justify-content:center!important;white-space:nowrap!important;text-decoration:none!important;box-sizing:border-box!important;margin:0!important;transition:all 0.2s cubic-bezier(0.4,0,0.2,1)!important;text-align:center!important}' .
    '.tau-header-buttons-container > form{padding:0!important;border:none!important;background:transparent!important;box-shadow:none!important}' .
    '.tau-header-buttons-container > form > div{width:100%!important;height:100%!important;display:inline-flex!important;align-items:center!important;justify-content:center!important;margin:0!important;padding:0!important;box-sizing:border-box!important}' .
    '.tau-header-buttons-container > form button,.tau-header-buttons-container > form input[type="submit"]{width:100%!important;height:100%!important;margin:0!important;display:inline-flex!important;align-items:center!important;justify-content:center!important;border-radius:8px!important;font-size:0.88rem!important;font-weight:700!important;box-sizing:border-box!important;transition:all 0.2s cubic-bezier(0.4,0,0.2,1)!important}' .
    '.tau-btn-manage{background:#ffffff!important;color:#c62b3a!important;border:2px solid #c62b3a!important;box-shadow:0 2px 6px rgba(198,43,58,0.08)!important}' .
    '.tau-btn-manage:hover{background:#fff2f3!important;transform:translateY(-1.5px)!important;box-shadow:0 6px 14px rgba(198,43,58,0.16)!important}' .
    '.tau-btn-create,.tau-header-buttons-container > form button,.tau-header-buttons-container > form input[type="submit"]{background:#c62b3a!important;color:#ffffff!important;border:2px solid #c62b3a!important;box-shadow:0 4px 10px rgba(198,43,58,0.18)!important}' .
    '.tau-btn-create:hover,.tau-header-buttons-container > form button:hover,.tau-header-buttons-container > form input[type="submit"]:hover{background:#b02230!important;border-color:#b02230!important;transform:translateY(-1.5px)!important;box-shadow:0 8px 18px rgba(198,43,58,0.28)!important}' .
    '.tau-btn-ai{background:linear-gradient(135deg,#c62b3a 0%,#8d182a 100%)!important;color:#ffffff!important;border:none!important;box-shadow:0 4px 12px rgba(198,43,58,0.25)!important}' .
    '.tau-btn-ai:hover{transform:translateY(-1.5px)!important;box-shadow:0 8px 20px rgba(198,43,58,0.4)!important}'
;
$dashboard_css = json_encode($dashboard_css_raw);

// ─── SVG icons for dashboard stat cards ─────────────────────────────────────

$ico_enrolled  = json_encode('<svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>');
$ico_completed = json_encode('<svg viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>');
$ico_done      = json_encode('<svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>');
$ico_pending   = json_encode('<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>');
$ico_instructor = json_encode('<svg viewBox="0 0 24 24"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Z"/><path d="M4 20a8 8 0 0 1 16 0"/></svg>');

$native_course_css = json_encode(
    '#tau-native-course-designer{margin:0 0 28px}' .
    '.tau-native-shell{border:1px solid rgba(198,43,58,.1);border-radius:24px;overflow:hidden;background:linear-gradient(110deg,#5f121d 0%,#7d1825 22%,#a32230 54%,#c62b3a 80%,#de5567 100%);box-shadow:0 24px 54px rgba(95,18,29,.14)}' .
    '.tau-native-top{padding:26px 28px 24px;color:#fff;position:relative;overflow:hidden}' .
    '.tau-native-top:before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 88% 22%,rgba(255,255,255,.16) 0%,transparent 26%),linear-gradient(90deg,rgba(255,255,255,.02),rgba(255,255,255,0) 34%);pointer-events:none}' .
    '.tau-native-top>*{position:relative;z-index:1}' .
    '.tau-native-kicker{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:999px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.16);font-size:.72rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;margin-bottom:1rem}' .
    '.tau-native-title{margin:0 0 .45rem;font-size:1.52rem;font-weight:800;letter-spacing:-.04em}' .
    '.tau-native-copy{margin:0;max-width:820px;font-size:.92rem;line-height:1.6;color:rgba(255,255,255,.88)}' .
    '.tau-native-body{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(320px,.85fr);gap:18px;padding:18px;background:linear-gradient(180deg,#fff8f9 0%,#ffffff 100%)}' .
    '@media(max-width:980px){.tau-native-body{grid-template-columns:1fr}}' .
    '.tau-native-panel{border:1px solid #f0dde1;border-radius:20px;background:#fff;padding:20px;box-shadow:0 12px 26px rgba(95,18,29,.06)}' .
    '.tau-native-label{font-size:.72rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#a32230;margin:0 0 .85rem}' .
    '.tau-native-presets{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}' .
    '@media(max-width:760px){.tau-native-presets{grid-template-columns:1fr}}' .
    '.tau-native-preset{border:1px solid #ead8dc;border-radius:18px;background:#fff;padding:16px;cursor:pointer;transition:border-color .18s,box-shadow .18s,transform .18s}' .
    '.tau-native-preset:hover,.tau-native-preset.active{border-color:#c62b3a;box-shadow:0 12px 28px rgba(198,43,58,.12);transform:translateY(-1px)}' .
    '.tau-native-preset strong{display:block;color:#231a21;font-size:.95rem;margin-bottom:6px}' .
    '.tau-native-preset span{display:block;color:#6d6067;font-size:.81rem;line-height:1.5}' .
    '.tau-native-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:14px}' .
    '@media(max-width:760px){.tau-native-fields{grid-template-columns:1fr}}' .
    '.tau-native-field label{display:block;font-size:.73rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#77666d;margin-bottom:7px}' .
    '.tau-native-field select,.tau-native-field textarea{width:100%;border:1px solid #e6d7da;border-radius:14px;background:#fff;padding:12px 13px;font:inherit;box-shadow:none}' .
    '.tau-native-field textarea{min-height:104px;resize:vertical}' .
    '.tau-native-chipset{display:flex;flex-wrap:wrap;gap:8px}' .
    '.tau-native-chip{display:inline-flex;align-items:center;gap:8px;padding:9px 12px;border-radius:999px;border:1px solid #ead8dc;background:#fff;color:#6f5d64;font-size:.79rem;font-weight:700;cursor:pointer;transition:border-color .18s,background .18s,color .18s}' .
    '.tau-native-chip:hover,.tau-native-chip.active{border-color:#c62b3a;background:#fff1f3;color:#b12434}' .
    '.tau-native-chip svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}' .
    '.tau-native-meta{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px}' .
    '.tau-native-pill{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:999px;background:#fff7f8;border:1px solid #f0dde1;color:#705e66;font-size:.78rem;font-weight:700}' .
    '.tau-native-pill svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}' .
    '.tau-native-modules{display:flex;flex-direction:column;gap:10px}' .
    '.tau-native-module{border:1px solid #f1e2e5;border-radius:16px;background:linear-gradient(180deg,#fff 0%,#fffafb 100%);padding:14px 15px}' .
    '.tau-native-module h4{margin:0 0 6px;font-size:.94rem;font-weight:800;color:#261d24}' .
    '.tau-native-module p{margin:0 0 10px;font-size:.8rem;line-height:1.55;color:#6f6269}' .
    '.tau-native-module ul{margin:0;padding-left:18px;color:#40373d;font-size:.8rem}' .
    '.tau-native-module li+li{margin-top:4px}' .
    '.tau-native-footer{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-top:16px;padding-top:16px;border-top:1px solid #f0e1e4}' .
    '@media(max-width:760px){.tau-native-footer{flex-direction:column;align-items:stretch}}' .
    '.tau-native-toggle{display:flex;align-items:center;gap:10px;font-size:.9rem;font-weight:700;color:#2f2830}' .
    '.tau-native-toggle input{width:18px;height:18px}' .
    '.tau-native-note{font-size:.79rem;color:#7b6e75}' .
    '[data-bs-theme=dark] .tau-native-shell{box-shadow:none}' .
    '[data-bs-theme=dark] .tau-native-body{background:linear-gradient(180deg,#1f1c24 0%,#1a1720 100%)}' .
    '[data-bs-theme=dark] .tau-native-panel{background:#241f28;border-color:rgba(255,255,255,.08);box-shadow:none}' .
    '[data-bs-theme=dark] .tau-native-preset{background:#241f28;border-color:rgba(255,255,255,.08)}' .
    '[data-bs-theme=dark] .tau-native-preset strong{color:#f5edf0}' .
    '[data-bs-theme=dark] .tau-native-preset span,[data-bs-theme=dark] .tau-native-note,[data-bs-theme=dark] .tau-native-field label,[data-bs-theme=dark] .tau-native-module p{color:#b5a7ae}' .
    '[data-bs-theme=dark] .tau-native-field select,[data-bs-theme=dark] .tau-native-field textarea{background:#1c1820;border-color:rgba(255,255,255,.08);color:#f6eef1}' .
    '[data-bs-theme=dark] .tau-native-chip,[data-bs-theme=dark] .tau-native-pill,[data-bs-theme=dark] .tau-native-module{background:#1d1921;border-color:rgba(255,255,255,.08);color:#e7dfe2}' .
    '[data-bs-theme=dark] .tau-native-module h4,[data-bs-theme=dark] .tau-native-toggle{color:#f5edf0}'
);

// Old builder removed
$native_course_markup = "''";

// ─── JavaScript: dark mode toggle + login welcome text ──────────────────────

$topofbody = '';
if (false) {
$topofbody_disabled = '<style>' . $dashboard_css_raw . '</style><script>(function(){' .
'var _isLogin=window.location.pathname.indexOf("/login")!==-1;' .
'var _isFirst=!sessionStorage.getItem("tau-s");' .
'var _fromGoogle=sessionStorage.getItem("tau-google-auth")==="1";' .
'if(_isLogin||_isFirst||_fromGoogle){' .
  'sessionStorage.setItem("tau-s","1");' .
  'sessionStorage.removeItem("tau-google-auth");' .
  'var _plT=Date.now();' .
  'var _pl=document.createElement("div");_pl.id="tau-preloader";' .
  '_pl.innerHTML=\'<div class="tau-pl-inner"><div class="tau-pl-ring-wrap"><svg class="tau-pl-svg" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="44" stroke="rgba(198,43,58,0.12)" stroke-width="2"/><circle class="tau-pl-arc" cx="50" cy="50" r="44" stroke="#c62b3a" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="188 88"/></svg><img class="tau-pl-icon" src="/pluginfile.php/1/theme_moove/favicon/0/tau-official-icon.png" alt="TAU"></div><div class="tau-pl-label">TAU CAMPUS VIRTUAL</div></div>\';' .
  'document.documentElement.appendChild(_pl);' .
  'var hideLoader=function(){var el=document.getElementById("tau-preloader");if(!el)return;el.classList.add("tau-pl-out");setTimeout(function(){if(el.parentNode)el.parentNode.removeChild(el);},320);};' .
  'window.addEventListener("load",function(){var e=Date.now()-_plT;setTimeout(hideLoader,Math.max(0,1650-e));});' .
  'setTimeout(hideLoader,3500);' .
'}' .
'if(window.location.pathname.indexOf("/course/index.php")!==-1&&window.location.search.indexOf("categoryid=")===-1){window.location.href="/#apoyo-academico";}' .
'var t=localStorage.getItem("tau-theme")||"dark";' .
'if(t==="dark")document.documentElement.setAttribute("data-bs-theme","dark");' .
'var MOON=\'<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-2.98 0-5.4-2.42-5.4-5.4 0-1.81.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z"/></svg>\';' .
'var SUN=\'<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1z"/></svg>\';' .
'var TAU_CARD_ICONS=[' . $ico_enrolled . ',' . $ico_completed . ',' . $ico_done . ',' . $ico_pending . '];' .
'var TAU_INSTRUCTOR_ICON=' . $ico_instructor . ';' .
'var TAU_NATIVE_COURSE_CSS=' . $native_course_css . ';' .
'var TAU_NATIVE_COURSE_MARKUP=' . $native_course_markup . ';' .
'document.addEventListener("DOMContentLoaded",function(){' .
  'document.querySelectorAll("a").forEach(function(a){' .
    'if(a.href&&a.href.indexOf("/course/index.php")!==-1&&a.href.indexOf("categoryid=")===-1){a.href="/#apoyo-academico";}' .
  '});' .
  /* Marcar sesión antes de salir al flujo OAuth de Google */
  'document.addEventListener("click",function(e){' .
    'var gBtn=e.target.closest(".login-identityprovider-btn,[href*=\'oauth2\'],[data-provider],.tau-login-google-btn");' .
    'if(gBtn){sessionStorage.setItem("tau-google-auth","1");}' .
  '});' .
  'document.addEventListener("click",function(e){' .
    'var btn=e.target.closest(".tau-cookie-btn");' .
    'if(btn){' .
      'e.preventDefault();' .
      'var modal=document.getElementById("tau-cookie-modal");' .
      'if(!modal){' .
        'modal=document.createElement("div");' .
        'modal.id="tau-cookie-modal";' .
        'modal.className="tau-modal-backdrop";' .
        'modal.innerHTML=' .
          '\'<div class="tau-cookie-modal-card">\' +' .
            '\'<div class="tau-cookie-modal-header">\' +' .
              '\'<h5 class="tau-cookie-modal-title">Aviso de Cookies<\/h5>\' +' .
              '\'<button type="button" class="tau-cookie-modal-close">&times;<\/button>\' +' .
            '\'<\/div>\' +' .
            '\'<div class="tau-cookie-modal-body">\' +' .
              '\'<p><strong>Este sitio utiliza dos "cookies":<\/strong><\/p>\' +' .
              '\'<p>La cookie esencial es la de sesión, normalmente llamada <strong>MoodleSession<\/strong>. Debe permitir que su navegador la acepte para poder mantener el servicio funcionando mientras navega el sitio. Cuando sale de la plataforma o cierra su navegador la \\\'cookie\\\' se destruye (en su navegador y en el servidor).<\/p>\' +' .
              '\'<p>La otra \\\'cookie\\\', normalmente llamada <strong>MOODLEID<\/strong> o similar, es para su comodidad. Se limita a recordar su nombre de usuario dentro del navegador. Esto significa que cuando regresa al sitio, se escribirá automáticamente su nombre en el campo nombre de usuario. Si desea mayor seguridad no utilice esta opción: sólo tendrá que escribir su nombre manualmente cada vez que quiera iniciar sesión.<\/p>\' +' .
            '\'<\/div>\' +' .
            '\'<div class="tau-cookie-modal-footer">\' +' .
              '\'<button type="button" class="tau-cookie-modal-btn">Aceptar<\/button>\' +' .
            '\'<\/div>\' +' .
          '\'<\/div>\';' .
        'document.body.appendChild(modal);' .
        'var closeFn=function(){modal.classList.remove("show");};' .
        'modal.querySelector(".tau-cookie-modal-close").addEventListener("click",closeFn);' .
        'modal.querySelector(".tau-cookie-modal-btn").addEventListener("click",closeFn);' .
        'modal.addEventListener("click",function(evt){if(evt.target===modal)closeFn();});' .
      '}' .
      'setTimeout(function(){modal.classList.add("show");},50);' .
    '}' .
  '});' .
  'if(window.location.hash==="#apoyo-academico"){' .
    'setTimeout(function(){' .
      'var dest=document.getElementById("apoyo-academico");' .
      'if(dest)dest.scrollIntoView({behavior:"smooth",block:"start"});' .
    '},700);' .
  '}' .
  'window.addEventListener("hashchange",function(){' .
    'if(window.location.hash==="#apoyo-academico"){' .
      'var dest=document.getElementById("apoyo-academico");' .
      'if(dest)dest.scrollIntoView({behavior:"smooth",block:"start"});' .
    '}' .
  '});' .
  'var item=document.querySelector("#mooveslideshow .carousel-item");' .
  'if(item&&!item.querySelector(".tau-banner-overlay")){' .
    'var overlay=document.createElement("div");' .
    'overlay.className="tau-banner-overlay";' .
    'overlay.innerHTML=\'<div class="tau-banner-card">\' +' .
      '\'<div class="tau-banner-pretitle tau-banner-animate">Universidad CESMAG<\/div>\' +' .
      '\'<h1 class="tau-banner-title tau-banner-animate">TAU <span class="tau-accent-text">Campus Virtual<\/span><\/h1>\' +' .
      '\'<div class="tau-banner-subtitle tau-banner-animate">UNICESMAG<\/div>\' +' .
      '\'<p class="tau-banner-desc tau-banner-animate">Tu plataforma de educación y aprendizaje en línea de vanguardia, diseñada para conectar tu talento con el futuro profesional.<\/p>\' +' .
      '\'<div class="tau-banner-btn-wrap tau-banner-animate">\' +' .
        '\'<a href="#apoyo-academico" class="btn btn-tau-banner-explore">Explorar Cursos<\/a>\' +' .
      '\'<\/div>\' +' .
    '\'<\/div>\' +' .
    /* ── Decorativo lado derecho ── */
    '\'<div class="tau-banner-deco">\' +' .
      '\'<div class="tau-deco-ring-wrap">\' +' .
        /* Anillo exterior girando lento */
        '\'<svg class="tau-deco-svg-outer" viewBox="0 0 188 188" fill="none" xmlns="http://www.w3.org/2000/svg">\' +' .
          '\'<circle cx="94" cy="94" r="88" stroke="rgba(198,43,58,0.18)" stroke-width="1.5"\/>\' +' .
          '\'<circle cx="94" cy="94" r="88" stroke="rgba(198,43,58,0.55)" stroke-width="1.5" stroke-dasharray="12 180" stroke-linecap="round"\/>\' +' .
          '\'<circle cx="94" cy="6" r="4.5" fill="#c62b3a" opacity=".75"\/>\' +' .
          '\'<circle cx="182" cy="94" r="3.5" fill="#c62b3a" opacity=".55"\/>\' +' .
          '\'<circle cx="94" cy="182" r="4" fill="#c62b3a" opacity=".65"\/>\' +' .
          '\'<circle cx="6" cy="94" r="3" fill="#c62b3a" opacity=".45"\/>\' +' .
        '\'<\/svg>\' +' .
        /* Anillo interior girando inverso */
        '\'<svg class="tau-deco-svg-inner" viewBox="0 0 188 188" fill="none" xmlns="http://www.w3.org/2000/svg">\' +' .
          '\'<circle cx="94" cy="94" r="65" stroke="rgba(198,43,58,0.12)" stroke-width="1"\/>\' +' .
          '\'<circle cx="94" cy="94" r="65" stroke="rgba(198,43,58,0.40)" stroke-width="1" stroke-dasharray="6 90" stroke-linecap="round"\/>\' +' .
          '\'<line x1="94" y1="29" x2="94" y2="159" stroke="rgba(198,43,58,0.07)" stroke-width="1"\/>\' +' .
          '\'<line x1="29" y1="94" x2="159" y2="94" stroke="rgba(198,43,58,0.07)" stroke-width="1"\/>\' +' .
        '\'<\/svg>\' +' .
        '\'<div class="tau-deco-center"><div class="tau-deco-num">+180<\/div><div class="tau-deco-lbl">Cursos activos<\/div><\/div>\' +' .
      '\'<\/div>\' +' .
      '\'<div class="tau-deco-pills">\' +' .
        '\'<div class="tau-deco-pill"><span class="tau-deco-pill-num">+2.4K<\/span><span class="tau-deco-pill-lbl">Estudiantes<\/span><\/div>\' +' .
        '\'<div class="tau-deco-pill"><span class="tau-deco-pill-num">98%<\/span><span class="tau-deco-pill-lbl">Satisfacción<\/span><\/div>\' +' .
      '\'<\/div>\' +' .
    '\'<\/div>\';' .
    'item.appendChild(overlay);' .
    'var btn=overlay.querySelector(".btn-tau-banner-explore");' .
    'if(btn){' .
      'btn.addEventListener("click",function(e){' .
        'e.preventDefault();' .
        'var dest=document.getElementById("apoyo-academico");' .
        'if(dest)dest.scrollIntoView({behavior:"smooth",block:"start"});' .
      '});' .
    '}' .
    'var checkGsap=setInterval(function(){' .
      'if(window.gsap){' .
        'clearInterval(checkGsap);' .
        'gsap.from(overlay.querySelector(".tau-banner-card"),{opacity:0,x:-60,duration:1.2,ease:"power3.out"});' .
        'gsap.from(overlay.querySelectorAll(".tau-banner-animate"),{opacity:0,y:24,duration:0.8,stagger:0.12,ease:"power2.out",delay:0.2});' .
      '}' .
    '},100);' .
  '}' .
  /* ── Tarjetas de curso completamente clickeables ── */
  /* ── Palette for gradient card headers ── */
  'var TAU_G=[' .
    '["#c62b3a","#7f0d1c"],' .
    '["#1e3a8a","#0a1854"],' .
    '["#0f766e","#064e3b"],' .
    '["#7c3aed","#3b0d8f"],' .
    '["#c2410c","#7c2d12"],' .
    '["#047857","#022c22"],' .
    '["#1d4ed8","#1e3a8a"],' .
    '["#be185d","#6b0c37"]' .
  '];' .
  'var TAU_ICONS=[TAU_CARD_ICONS[0],TAU_CARD_ICONS[1],TAU_CARD_ICONS[2],TAU_CARD_ICONS[3],TAU_CARD_ICONS[0],TAU_CARD_ICONS[1],TAU_CARD_ICONS[2],TAU_CARD_ICONS[3]];' .

  /* ── Make all course cards fully clickable ── */
  'function tauMakeCardsClickable(){' .
    'document.querySelectorAll(".card.dashboard-card,.coursebox,.course-card,.card[data-course-id]").forEach(function(card){' .
      'if(card.dataset.tauClick)return;' .
      'card.dataset.tauClick="1";' .
      'var link=card.querySelector("a.aalink[href*=\"/course/view.php\"],a[href*=\"/course/view.php\"],h3 a,h4 a,.coursename a");' .
      'if(!link)return;' .
      'card.style.cursor="pointer";' .
      'card.addEventListener("click",function(e){' .
        'if(e.target.closest("a,button,[role=button],.dropdown,.action-menu"))return;' .
        'window.location.href=link.href;' .
      '});' .
    '});' .
  '}' .

  /* ── Enhance cards: gradient headers + category badges + teacher row ── */
  'function tauEnhanceCards(){' .
    'document.querySelectorAll(".card.dashboard-card").forEach(function(card){' .
      /* Course ID first — needed for teacher lookup AND palette */
      'var link=card.querySelector("a[href*=\"/course/view.php\"]");' .
      'var cid=0;' .
      'if(link){var m=link.href.match(/[?&]id=(\\d+)/);if(m)cid=parseInt(m[1]);}' .
      /* Teacher row — runs on every card, even already-enhanced ones */
      'var body=card.querySelector(".card-body");' .
      'if(body&&!body.querySelector(".tau-teacher-row")&&window.tauCourseTeachers&&cid&&window.tauCourseTeachers[cid]){' .
        'var tchs=window.tauCourseTeachers[cid];' .
        'if(tchs&&tchs.length){' .
          'var tr=document.createElement("div");' .
          'tr.className="tau-teacher-row";' .
          'tr.innerHTML=\'<span class="tau-tr-icon">\'+TAU_INSTRUCTOR_ICON+\'</span><span class="tau-tr-name">\'+tchs.slice(0,2).join(\' · \')+\'</span>\';' .
          'body.appendChild(tr);' .
        '}' .
      '}' .
      /* Full enhancement (gradient + badge) only once per card */
      'if(card.dataset.tauEnh)return;' .
      'card.dataset.tauEnh="1";' .
      'var idx=cid?cid%TAU_G.length:Array.from(document.querySelectorAll(".card.dashboard-card")).indexOf(card)%TAU_G.length;' .
      /* Detect real course image (pluginfile.php = user-uploaded) */
      'var imgDiv=card.querySelector(".card-img-top,.dashboard-card-img,.card-img");' .
      'var imgEl=card.querySelector(".card-img-top img,img.card-img-top,.card-img img");' .
      'var realImg=false;' .
      'if(imgEl&&imgEl.src&&imgEl.src.indexOf("pluginfile.php")!==-1){realImg=true;}' .
      'else if(imgDiv&&imgDiv.style.backgroundImage&&imgDiv.style.backgroundImage.indexOf("pluginfile.php")!==-1){realImg=true;}' .
      'if(!realImg){' .
        'var gh=document.createElement("div");' .
        'gh.className="tau-gradient-header";' .
        'gh.style.background="linear-gradient(135deg,"+TAU_G[idx][0]+" 0%,"+TAU_G[idx][1]+" 100%)";' .
        'gh.innerHTML=\'<div class="tau-gh-pattern"></div><span class="tau-gh-icon">\'+TAU_ICONS[idx]+\'</span>\';' .
        'if(imgDiv){' .
          'var parent=imgDiv.parentNode;' .
          'if(parent){parent.replaceChild(gh,imgDiv);}else{card.insertBefore(gh,card.firstChild);}' .
        '}else{card.insertBefore(gh,card.firstChild);}' .
      '}' .
      /* Add category badge */
      'var body=card.querySelector(".card-body");' .
      'if(body&&!body.querySelector(".tau-cat-badge")){' .
        'var catEl=card.querySelector(".coursecat,.course-category,[data-region=\"coursecategory\"]");' .
        'var catTxt=catEl?catEl.textContent.trim():null;' .
        'if(catTxt){' .
          'var badge=document.createElement("div");' .
          'badge.className="tau-cat-badge";' .
          'badge.textContent=catTxt;' .
          'body.insertBefore(badge,body.firstChild);' .
        '}' .
      '}' .
    '});' .
  '}' .

  'function tauHideConectiMe(){' .
    'document.querySelectorAll("[data-region=\'footer-content-popover\'] .footer-section,[data-region=\'footer-content-popover\'] .footer-section + *").forEach(function(el){' .
      'if(el.querySelector("a[href*=\'conecti.me\'],img[alt*=\'Conecti\']")||el.textContent.indexOf("conecti")!==-1){' .
        'el.style.setProperty("display","none","important");' .
      '}' .
    '});' .
  '}' .
  'try { tauMakeCardsClickable(); tauEnhanceCards(); tauHideConectiMe(); } catch(e) { console.error("TAU Error:", e); }' .
  'var tauCardObs=new MutationObserver(function(){' .
    'try { tauMakeCardsClickable(); tauEnhanceCards(); tauHideConectiMe(); } catch(e) {}' .
  '});' .
  'tauCardObs.observe(document.body,{childList:true,subtree:true});' .
  /* Old builder JS removed */
  'var btn=document.createElement("button");' .
  'btn.id="tau-theme-toggle";' .
  'btn.title="Modo oscuro";' .
  'btn.setAttribute("aria-label","Cambiar modo oscuro/claro");' .
  'btn.innerHTML=t==="dark"?SUN:MOON;' .
  'document.body.appendChild(btn);' .
  'btn.addEventListener("click",function(){' .
    'var cur=document.documentElement.getAttribute("data-bs-theme")||"light";' .
    'var next=cur==="dark"?"light":"dark";' .
    'document.documentElement.setAttribute("data-bs-theme",next);' .
    'localStorage.setItem("tau-theme",next);' .
    'btn.innerHTML=next==="dark"?SUN:MOON;' .
    'btn.title=next==="dark"?"Modo claro":"Modo oscuro";' .
  '});' .

  'if(document.body.id==="page-login-index"){' .
    'var container=document.querySelector("body#page-login-index .login-container");' .
    'var wrapper=document.querySelector("body#page-login-index .login-wrapper");' .
    'if(container&&wrapper){' .
      'var originalLogo=container.querySelector("#loginlogo");' .
      'var logoHtml="";' .
      'if(originalLogo){logoHtml=originalLogo.outerHTML;}' .
      'var googleBtn=container.querySelector(".login-identityprovider-btn");' .
      'var googleBtnHtml="";' .
      'if(googleBtn){googleBtnHtml=googleBtn.outerHTML;}' .
      'var originalDflex=container.querySelector(".loginform .d-flex");' .
      'var languages=[];' .
      'var nativeLangMenu=container.querySelector(".login-languagemenu")||document.querySelector(".login-languagemenu")||document.querySelector(".langmenu");' .
      'if(nativeLangMenu){' .
        'nativeLangMenu.querySelectorAll("a.dropdown-item, .dropdown-menu a").forEach(function(a){' .
          'var txt=a.textContent.trim();' .
          'var href=a.href;' .
          'if(txt&&href){' .
            'languages.push("<a href=\'" + href + "\'>" + txt + "</a>");' .
          '}' .
        '});' .
      '}' .
      'if(languages.length===0){' .
        'languages.push("<a href=\'" + window.location.pathname + "?lang=es_co\'>Español (Internacional)</a>");' .
        'languages.push("<a href=\'" + window.location.pathname + "?lang=en\'>English</a>");' .
      '}' .
      'var cookieUrl="";' .
      'var cookieText="Aviso de Cookies";' .
      'var nativeCookieLink=document.querySelector("a[href*=\'cookie\']")||document.querySelector("a[href*=\'dataprivacy\']")||document.querySelector("footer a[href*=\'summary\']");' .
      'if(nativeCookieLink){' .
        'cookieUrl=nativeCookieLink.href;' .
        'cookieText=nativeCookieLink.textContent.trim();' .
      '}else{' .
        'cookieUrl="/admin/tool/dataprivacy/summary.php";' .
      '}' .
      'var cardFooterHtml="<div class=\'tau-login-card-foot-controls\'>" +' .
        '"<div class=\'tau-lang-selector-custom\'>" +' .
          '"<button type=\'button\' class=\'tau-lang-btn\'>" +' .
            '"<svg width=\'14\' height=\'14\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><circle cx=\'12\' cy=\'12\' r=\'10\'></circle><line x1=\'2\' y1=\'12\' x2=\'22\' y2=\'12\'></line><path d=\'M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z\'></path></svg>" +' .
            '"<span>Idioma</span>" +' .
          '"</button>" +' .
          '"<div class=\'tau-lang-dropdown-menu\'>" +' .
            'languages.join("") +' .
          '"</div>" +' .
        '"</div>" +' .
        '"<a href=\'" + cookieUrl + "\' class=\'tau-cookie-btn\' target=\'_blank\'>" +' .
          '"<svg width=\'14\' height=\'14\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><path d=\'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z\'></path></svg>" +' .
          '"<span>" + cookieText + "</span>" +' .
        '"</a>" +' .
      '"</div>";' .
      'container.innerHTML=' .
        'logoHtml+' .
        '"<div class=\'tau-login-welcome\'>" +' .
          '"<div class=\'tau-campus-inst\'>Universidad CESMAG</div>" +' .
          '"<div class=\'tau-campus-title\'>Campus Virtual</div>" +' .
          '"<div class=\'tau-campus-divider\'></div>" +' .
          '"<p>Inicia sesión con tu cuenta Google institucional</p>" +' .
        '"</div>" +' .
        '"<div class=\'tau-login-btnwrap\'><div class=\'tau-login-btnbox\'>" + googleBtnHtml + "</div></div>" +' .
        'cardFooterHtml;' .
      'if(!document.getElementById("tau-presencial-card")){' .
        'var logoCloneHtml="";' .
        'var logoEl=container.querySelector("#loginlogo");' .
        'if(logoEl){var lc=logoEl.cloneNode(true);lc.id="tau-presencial-loginlogo";logoCloneHtml=lc.outerHTML;}' .
        'var presencialCard=document.createElement("div");' .
        'presencialCard.id="tau-presencial-card";' .
        'presencialCard.className="login-container tau-login-card-presencial";' .
        'presencialCard.innerHTML=' .
          'logoCloneHtml+' .
          '"<div class=\'tau-login-welcome\'>" +' .
            '"<div class=\'tau-campus-inst\'>Universidad CESMAG</div>" +' .
            '"<div class=\'tau-campus-title\'>Campus Presencial</div>" +' .
            '"<div class=\'tau-campus-divider\'></div>" +' .
            '"<p>Accede a la plataforma académica presencial</p>" +' .
          '"</div>" +' .
          '"<div class=\'tau-login-btnwrap\'><div class=\'tau-login-btnbox\'>" +' .
            '"<a href=\'https://uv4.unicesmag.edu.co/login/index.php\' class=\'login-identityprovider-btn btn\'>" +' .
              '"<svg width=\'20\' height=\'20\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'#c62b3a\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'>" +' .
                '"<path d=\'M22 10v6M2 10l10-5 10 5-10 5z\'></path>" +' .
                '"<path d=\'M6 12v5c3 3 9 3 12 0v-5\'></path>" +' .
              '"</svg>" +' .
              '"Plataforma Presencial" +' .
            '"</a>" +' .
          '"</div></div>" +' .
          'cardFooterHtml;' .
        'wrapper.insertBefore(presencialCard,container);' .
      '}' .
      '/* Add language button listeners dynamically */' .
      'document.querySelectorAll(".tau-lang-btn").forEach(function(btn){' .
        'if(btn.dataset.tauLangEvent)return;' .
        'btn.dataset.tauLangEvent="1";' .
        'btn.addEventListener("click",function(e){' .
          'e.stopPropagation();' .
          'btn.parentNode.classList.toggle("show");' .
        '});' .
      '});' .
      'document.addEventListener("click",function(){' .
        'document.querySelectorAll(".tau-lang-selector-custom").forEach(function(el){' .
          'el.classList.remove("show");' .
        '});' .
      '});' .
    '}' .
    'var TAU_ERR={' .
      '"no-account":{t:"Cuenta no encontrada",m:"No encontramos ninguna cuenta TAU con este correo. Usa tu correo <b>@unicesmag.edu.co</b>."},' .
      '"wrong-pass":{t:"Credenciales incorrectas",m:"Verifica que uses tu <b>cuenta Google institucional @unicesmag.edu.co</b>, no una personal."},' .
      '"blocked":{t:"Cuenta bloqueada",m:"Tu cuenta está bloqueada temporalmente. Espera unos minutos o contacta soporte."},' .
      '"no-auth":{t:"Método no permitido",m:"Solo se permite inicio de sesión con <b>Google institucional</b>. El acceso directo está deshabilitado."},' .
      '"generic":{t:"Error de acceso",m:"No fue posible iniciar sesión. Contacta a soporte si el problema persiste."}' .
    '};' .
    'function tauShowToast(key){' .
      'var d=TAU_ERR[key]||TAU_ERR["generic"];' .
      'var el=document.createElement("div");' .
      'el.className="tau-toast";' .
      'el.innerHTML=' .
        '\'<button class="tau-toast-close" onclick="this.parentNode.remove()" aria-label="Cerrar">&times;<\/button>\'' .
        '+\'<div class="tau-toast-title">\'+d.t+\'<\/div>\'' .
        '+\'<div class="tau-toast-body">\'+d.m+\'<\/div>\'' .
        '+\'<div class="tau-toast-contact">\'' .
        '+\'Soporte: <a href="mailto:tau-ayuda@unicesmag.edu.co">tau-ayuda@unicesmag.edu.co<\/a>\'' .
        '+\'<\/div>\';' .
      'document.body.appendChild(el);' .
      'setTimeout(function(){' .
        'el.style.transition="opacity .4s";' .
        'el.style.opacity="0";' .
        'setTimeout(function(){if(el.parentNode)el.remove();},420);' .
      '},9000);' .
    '}' .
    'document.querySelectorAll(".loginerrors,.alert-danger").forEach(function(e){' .
      'var m=(e.textContent||"").toLowerCase().trim();' .
      'if(!m)return;' .
      'e.style.setProperty("display","none","important");' .
      'var k="generic";' .
      'if(m.includes("no se pudo encontrar")||m.includes("correo electr"))k="no-account";' .
      'else if(m.includes("contrase")||m.includes("incorrecta")||m.includes("incorrectos"))k="wrong-pass";' .
      'else if(m.includes("bloqueado")||m.includes("bloqueada")||m.includes("suspendid"))k="blocked";' .
      'else if(m.includes("plugin")||m.includes("no est"))k="no-auth";' .
      'tauShowToast(k);' .
    '});' .
  '}' .

  /* ── Personal dashboard widget: stats for all users on dashboard pages ── */
  'var tauDashFetched=false;' .
  'if(document.body.id.indexOf("page-my-")===0){' .
  'if(!tauDashFetched&&!document.getElementById("tau-personal-dashboard")){' .
  'tauDashFetched=true;' .
  'if(!document.getElementById("tau-pd-css")){var tauSt=document.createElement("style");tauSt.id="tau-pd-css";tauSt.textContent=' . $dashboard_css . ';document.head.appendChild(tauSt);}' .
  'fetch("/local/tau_course_creator_ai/user_stats.php?_="+Date.now())' .
  '.then(function(r){return r.ok?r.json():null;})' .
  '.then(function(d){' .
    'if(!d)return;' .
    'if(d.course_teachers){window.tauCourseTeachers=d.course_teachers;tauEnhanceCards();}' .
    'var p=d.total_activities>0?Math.round(d.completed_activities*100/d.total_activities):0;' .
    'function nv(v){return v||0;}' .
    'var getStatIcon=function(lbl,idx){' .
      'var l=(lbl||"").toLowerCase();' .
      'if(l.includes("ia"))return \'<svg viewBox="0 0 24 24"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/><circle cx="12" cy="12" r="4"/><\\/svg>\';' .
      'if(l.includes("curso"))return TAU_CARD_ICONS[0];' .
      'if(l.includes("complet")||l.includes("alumn")||l.includes("estudiant"))return TAU_CARD_ICONS[1];' .
      'if(l.includes("actividad")||l.includes("ok")||l.includes("hech"))return TAU_CARD_ICONS[2];' .
      'if(l.includes("pendiente")||l.includes("calific")||l.includes("espera"))return TAU_CARD_ICONS[3];' .
      'if(l.includes("docent")||l.includes("profe")||l.includes("foro")||l.includes("debate"))return TAU_INSTRUCTOR_ICON;' .
      'return TAU_CARD_ICONS[idx%4];' .
    '};' .
    'var el=document.createElement("div");' .
    'el.id="tau-personal-dashboard";' .
    'var h="";' .
    'h+=\'<div class="tau-pd-header">\';' .
    'var title="¡Hola, "+d.firstname+"! <span class=\'wave\'>👋<\/span>";' .
    'var subtitle="Tu progreso académico en TAU Campus Virtual";' .
    'if(d.role==="teacher"){subtitle="Panel de Labor Docente — Resumen de tus clases";}' .
    'else if(d.role==="admin"){subtitle="Panel de Control de la Plataforma — Estado y salud general de Moodle";}' .
    'h+=\'<div class="tau-pd-greeting">\'+title+\'<\/div>\';' .
    'h+=\'<div class="tau-pd-subtitle">\'+subtitle+\'<\/div>\';' .
    'if(d.role==="student"){' .
      'h+=\'<div class="tau-pd-prog-wrap">\';' .
      'h+=\'<div class="tau-pd-prog-label"><span>Progreso general<\/span><span>\'+p+\'%<\/span><\/div>\';' .
      'h+=\'<div class="tau-pd-prog-bar"><div class="tau-pd-prog-fill" id="tau-pd-fill"><\/div><\/div>\';' .
      'h+=\'<\/div>\';' .
    '}' .
    'h+=\'<\/div>\';' .
    'h+=\'<div class="tau-pd-stats">\';' .
    'h+=\'<div class="tau-pd-stat tau-stat-card-1">\';' .
    'h+=\'<div class="tau-pd-stat-badge">\'+getStatIcon(d.stat_1_lbl,0)+\'<\/div>\';' .
    'h+=\'<div class="tau-pd-stat-number" data-n="\'+nv(d.stat_1_val)+\'">0<\/div>\';' .
    'h+=\'<div class="tau-pd-stat-label">\'+(d.stat_1_lbl||"Cursos")+\'<\/div><\/div>\';' .
    'h+=\'<div class="tau-pd-stat tau-stat-card-2">\';' .
    'h+=\'<div class="tau-pd-stat-badge">\'+getStatIcon(d.stat_2_lbl,1)+\'<\/div>\';' .
    'h+=\'<div class="tau-pd-stat-number" data-n="\'+nv(d.stat_2_val)+\'">0<\/div>\';' .
    'h+=\'<div class="tau-pd-stat-label">\'+(d.stat_2_lbl||"Completados")+\'<\/div><\/div>\';' .
    'h+=\'<div class="tau-pd-stat tau-stat-card-3">\';' .
    'h+=\'<div class="tau-pd-stat-badge">\'+getStatIcon(d.stat_3_lbl,2)+\'<\/div>\';' .
    'h+=\'<div class="tau-pd-stat-number" data-n="\'+nv(d.stat_3_val)+\'">0<\/div>\';' .
    'h+=\'<div class="tau-pd-stat-label">\'+(d.stat_3_lbl||"Actividades")+\'<\/div><\/div>\';' .
    'h+=\'<div class="tau-pd-stat tau-stat-card-4">\';' .
    'h+=\'<div class="tau-pd-stat-badge">\'+getStatIcon(d.stat_4_lbl,3)+\'<\/div>\';' .
    'h+=\'<div class="tau-pd-stat-number" data-n="\'+nv(d.stat_4_val)+\'">0<\/div>\';' .
    'h+=\'<div class="tau-pd-stat-label">\'+(d.stat_4_lbl||"Pendientes")+\'<\/div><\/div>\';' .
    'h+=\'<\/div>\';' .
    'el.innerHTML=h;' .
    'var reg=document.querySelector("#region-main")||document.querySelector(\'[role="main"]\');' .
    'if(reg){' .
      'var skip=["region-main-settings-menu","tertiary-navigation"];' .
      'var anc=null;' .
      'for(var ci=0;ci<reg.children.length;ci++){' .
        'var ch=reg.children[ci];' .
        'if(!skip.some(function(s){return ch.id===s||ch.classList.contains(s);})){anc=ch;break;}' .
      '}' .
      'reg.insertBefore(el,anc||reg.firstElementChild);' .
      'var candidates=document.querySelectorAll("h1, h2, h3, h4, h5, h6, .welcome-message, [class*=\'welcome\'], [class*=\'greeting\'], #page-header h1, #page-header h2, .page-header h1, .page-header h2");' .
      'candidates.forEach(function(c){' .
        'if(c.closest("#tau-personal-dashboard"))return;' .
        'var txt=c.textContent||"";' .
        'if(txt.toLowerCase().includes("hola")&&txt.toLowerCase().includes(d.firstname.toLowerCase())){' .
          'c.style.setProperty("display","none","important");' .
        '}' .
      '});' .
      'setTimeout(function(){' .
        'var fill=document.getElementById("tau-pd-fill");' .
        'if(fill)fill.style.width=p+"%";' .
        'el.querySelectorAll(".tau-pd-stat-number").forEach(function(nm){' .
          'var target=parseInt(nm.dataset.n)||0;' .
          'if(!target){nm.textContent="0";return;}' .
          'var t0=performance.now();' .
          '(function tick(now){' .
            'var frac=Math.min((now-t0)/900,1);' .
            'var ease=1-Math.pow(1-frac,3);' .
            'nm.textContent=Math.round(ease*target);' .
            'if(frac<1)requestAnimationFrame(tick);' .
          '})(performance.now());' .
        '});' .
      '},150);' .
    '}' .
  '}).catch(function(){});' .
  '}}' .

  '/* ── Categories Professional Redesign ── */' .
  'if(document.body.id==="page-site-index"){' .
    'var cs=document.getElementById("frontpage-category-names")||document.getElementById("frontpage-category-combo")||document.querySelector(".frontpage-category-names,.category_subcategories,.course-category-listing");' .
    'if(!cs){var hh2s=document.querySelectorAll("h2");hh2s.forEach(function(hx){if(hx.textContent.trim()==="Categorías"||hx.textContent.trim()==="Categorias")cs=hx.closest("div")||hx.parentNode;});}' .
    'if(cs&&!cs.dataset.tauCat){' .
      'cs.dataset.tauCat="1";' .
      'var cl=cs.querySelectorAll("a");var cats=[];' .
      'cl.forEach(function(a){var n=a.textContent.trim();if(n&&n!=="Expandir todo"&&n!=="Colapsar todo"&&n.length>1&&a.href)cats.push({name:n,href:a.href});});' .
      'if(cats.length){' .
        'var getCatIcon=function(name){' .
          'var n=name.toLowerCase();' .
          'var award=\'<svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="7"><\\/circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"><\\/polyline><\\/svg>\';' .
          'var laptop=\'<svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"><\\/rect><line x1="8" y1="21" x2="16" y2="21"><\\/line><line x1="12" y1="17" x2="12" y2="21"><\\/line><\\/svg>\';' .
          'var activity=\'<svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"><\\/path><\\/svg>\';' .
          'var users=\'<svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"><\\/path><circle cx="9" cy="7" r="4"><\\/circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"><\\/path><path d="M16 3.13a4 4 0 0 1 0 7.75"><\\/path><\\/svg>\';' .
          'var cpu=\'<svg viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"><\\/rect><rect x="9" y="9" width="6" height="6"><\\/rect><line x1="9" y1="1" x2="9" y2="4"><\\/line><line x1="15" y1="1" x2="15" y2="4"><\\/line><line x1="9" y1="20" x2="9" y2="23"><\\/line><line x1="15" y1="20" x2="15" y2="23"><\\/line><line x1="20" y1="9" x2="23" y2="9"><\\/line><line x1="20" y1="15" x2="23" y2="15"><\\/line><line x1="1" y1="9" x2="4" y2="9"><\\/line><line x1="1" y1="15" x2="4" y2="15"><\\/line><\\/svg>\';' .
          'var briefcase=\'<svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"><\\/rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"><\\/path><\\/svg>\';' .
          'var compass=\'<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"><\\/circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"><\\/polygon><\\/svg>\';' .
          'var graduation=\'<svg viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"><\\/path><path d="M6 12v5c3 3 9 3 12 0v-5"><\\/path><\\/svg>\';' .
          'var bookOpen=\'<svg viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2"><\\/path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7"><\\/path><\\/svg>\';' .
          'if(n.indexOf("virtual")!==-1||n.indexOf("tecnolog")!==-1||n.indexOf("sistemas")!==-1||n.indexOf("computac")!==-1)return laptop;' .
          'if(n.indexOf("salud")!==-1||n.indexOf("medicin")!==-1||n.indexOf("enfermer")!==-1||n.indexOf("deport")!==-1)return activity;' .
          'if(n.indexOf("humana")!==-1||n.indexOf("social")!==-1||n.indexOf("psicolog")!==-1||n.indexOf("educac")!==-1)return users;' .
          'if(n.indexOf("ingenier")!==-1||n.indexOf("exacta")!==-1||n.indexOf("ciencia")!==-1||n.indexOf("fisic")!==-1||n.indexOf("matemat")!==-1)return cpu;' .
          'if(n.indexOf("empresa")!==-1||n.indexOf("administr")!==-1||n.indexOf("econom")!==-1||n.indexOf("negocio")!==-1||n.indexOf("contad")!==-1)return briefcase;' .
          'if(n.indexOf("apoyo")!==-1||n.indexOf("tutor")!==-1||n.indexOf("induc")!==-1||n.indexOf("orientac")!==-1)return award;' .
          'if(n.indexOf("idioma")!==-1||n.indexOf("ingles")!==-1||n.indexOf("lengua")!==-1)return compass;' .
          'if(n.indexOf("lectura")!==-1||n.indexOf("escritura")!==-1||n.indexOf("bibliotec")!==-1)return bookOpen;' .
          'return graduation;' .
        '};' .
        'var CC=[["#c62b3a","rgba(198,43,58,.07)"],["#1e3a8a","rgba(30,58,138,.07)"],["#0f766e","rgba(15,118,110,.07)"],["#7c3aed","rgba(124,58,237,.07)"],["#c2410c","rgba(194,65,12,.07)"],["#be185d","rgba(190,24,93,.07)"],["#047857","rgba(4,120,87,.07)"]];' .
        'var wd=document.createElement("div");wd.className="tau-cat-section";' .
        'var hh=\'<div class="tau-cat-header"><div class="tau-cat-kicker"><svg viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"><\\/path><path d="M6 12v5c3 3 9 3 12 0v-5"><\\/path><\\/svg>Formación Complementaria<\\/div><h2 class="tau-cat-htitle">Cursos de Apoyo Académico<\\/h2><p class="tau-cat-sub">Explora nuestra oferta académica organizada por áreas de conocimiento para fortalecer tus competencias profesionales.<\\/p><\\/div><div class="tau-cat-body"><div class="tau-cat-grid">\';' .
        'cats.forEach(function(cat,i){var c=CC[i%CC.length];hh+=\'<a href="\'+cat.href+\'" class="tau-cat-card"><div class="tau-cat-ico" style="color:\'+c[0]+\';background:\'+c[1]+\'">\'+getCatIcon(cat.name)+\'</div><span class="tau-cat-nm">\'+cat.name+\'<\\/span><span class="tau-cat-arr">→<\\/span><\\/a>\';});' .
        'hh+=\'<\\/div>\';' .
        'if(cats.length>6){hh+=\'<div class="tau-cat-toggle-wrap"><button type="button" class="tau-cat-toggle" id="tauCatTgl"><svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"><\\/polyline><\\/svg> Expandir todo<\\/button><\\/div>\';}' .
        'hh+=\'<\\/div>\';' .
        'wd.innerHTML=hh;' .
        'if(cats.length>6){var cds=wd.querySelectorAll(".tau-cat-card");cds.forEach(function(c,i){if(i>=6)c.style.display="none";});var tgl=wd.querySelector("#tauCatTgl");var ex=false;if(tgl)tgl.addEventListener("click",function(){ex=!ex;cds.forEach(function(c,i){if(i>=6)c.style.display=ex?"":"none";});tgl.classList.toggle("expanded",ex);tgl.innerHTML=ex?\'<svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"><\\/polyline><\\/svg> Colapsar\':\'<svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"><\\/polyline><\\/svg> Expandir todo\';});}' .
        'cs.style.display="none";cs.parentNode.insertBefore(wd,cs);' .
        /* ── Unified Course Drawer Logic ── */
        'var courseList=document.querySelector(".frontpage-course-list-all")||document.querySelector(".courses");' .
        'if(courseList){' .
          'var courseHeading=courseList.previousElementSibling;' .
          'if(courseHeading&&(courseHeading.tagName==="H2"||courseHeading.tagName==="H3"||courseHeading.classList.contains("frontpage-course-list-all-title"))){courseHeading.style.display="none";}' .
          'var collapseWrapper=document.createElement("div");' .
          'collapseWrapper.className="tau-courses-collapsible";' .
          'collapseWrapper.id="tauCoursesCollapsible";' .
          'courseList.parentNode.insertBefore(collapseWrapper,courseList);' .
          'collapseWrapper.appendChild(courseList);' .
          'var catBody=wd.querySelector(".tau-cat-body");' .
          'if(catBody){catBody.appendChild(collapseWrapper);}' .
          'var toggleContainer=document.createElement("div");' .
          'toggleContainer.className="tau-courses-toggle-container";' .
          'toggleContainer.innerHTML=\'<button type="button" class="tau-courses-main-btn" id="tauCoursesTglBtn"><span class="tau-btn-icon"><svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20M4 19.5V3a2.5 2.5 0 0 1 2.5-2.5H20v14H6.5"><\\/path><\\/svg><\\/span><span class="tau-btn-text">Ver Cursos Disponibles<\\/span><span class="tau-btn-chevron"><svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"><\\/polyline><\\/svg><\\/span><\\/button>\';' .
          'var grid=wd.querySelector(".tau-cat-grid");' .
          'var tglWrap=wd.querySelector(".tau-cat-toggle-wrap");' .
          'if(tglWrap)tglWrap.parentNode.insertBefore(toggleContainer,tglWrap.nextSibling);' .
          'else if(grid)grid.parentNode.insertBefore(toggleContainer,collapseWrapper);' .
          'var tglBtn=toggleContainer.querySelector("#tauCoursesTglBtn");' .
          'tglBtn.addEventListener("click",function(){' .
            'var isShown=collapseWrapper.classList.toggle("show");' .
            'tglBtn.classList.toggle("expanded",isShown);' .
            'tglBtn.querySelector(".tau-btn-text").textContent=isShown?"Ocultar Cursos Disponibles":"Ver Cursos Disponibles";' .
            'if(isShown){setTimeout(function(){collapseWrapper.scrollIntoView({behavior:"smooth",block:"nearest"});},150);}' .
          '});' .
        '}' .
      '}' .
    '}' .
  '}' .
  '  /* ── GSAP ScrollTrigger Animations ── */' .
  'var gsapCheck=setInterval(function(){' .
    'if(window.gsap&&window.ScrollTrigger){' .
      'clearInterval(gsapCheck);' .
      'gsap.registerPlugin(ScrollTrigger);' .
      'var carouselImg=document.querySelector("#mooveslideshow img,.carousel-item img");' .
      'if(carouselImg){' .
        'gsap.to(carouselImg,{scale:1.12,ease:"none",scrollTrigger:{trigger:"#mooveslideshow",start:"top top",end:"bottom top",scrub:true}});' .
      '}' .
      'var marketingCards=document.querySelectorAll("#feature .card");' .
      'if(marketingCards.length>0){' .
        'marketingCards.forEach(function(card){' .
          'gsap.from(card,{opacity:0,scale:0.88,y:30,duration:0.7,ease:"power2.out",scrollTrigger:{trigger:card,start:"top 88%",toggleActions:"play none none none"}});' .
        '});' .
      '}' .
      'var courseCards=document.querySelectorAll(".frontpage-course-list-all .card,.courses .card,.dashboard-card");' .
      'if(courseCards.length>0){' .
        'courseCards.forEach(function(card){' .
          'gsap.from(card,{opacity:0,scale:0.92,y:25,duration:0.6,ease:"back.out(1.1)",scrollTrigger:{trigger:card,start:"top 92%",toggleActions:"play none none none"}});' .
        '});' .
      '}' .
      'var rateBoxes=document.querySelectorAll(".rate-box");' .
      'if(rateBoxes.length>0){' .
        'rateBoxes.forEach(function(box,idx){' .
          'gsap.from(box,{opacity:0,x:idx%2===0?-30:30,duration:0.7,ease:"power2.out",scrollTrigger:{trigger:box,start:"top 85%",toggleActions:"play none none none"}});' .
        '});' .
      '}' .
    '}' .
  '},100);' .
  '  /* ── Replace marketing icons with professional non-animated SVGs ── */' .
  'if(document.body.id==="page-site-index"){' .
    'var mCards=document.querySelectorAll("#feature .card");' .
    'var mIcons=[' .
      '\'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1m-1.636 6.364l-.707-.707M12 21v-1m-6.364-1.636l.707.707M3 12h1m1.636-6.364l.707.707M12 7a5 5 0 0 0-5 5c0 1.25.46 2.39 1.21 3.26L9.5 17h5l1.29-1.74A4.98 4.98 0 0 0 17 12a5 5 0 0 0-5-5z"></path></svg>\',' .
      '\'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path></svg>\',' .
      '\'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>\',' .
      '\'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>\'' .
    '];' .
    'mCards.forEach(function(card,i){' .
      'var iconBox=card.querySelector(".icon-lg");' .
      'if(iconBox&&mIcons[i]){' .
        'iconBox.innerHTML=mIcons[i];' .
      '}' .
    '});' .
    'var banner=document.querySelector("#mooveslideshow .carousel-inner");' .
    'if(banner){banner.insertAdjacentHTML("beforeend",\'<div class="tau-banner-overlay"><div class="tau-banner-card"><span class="tau-banner-pretitle">TAU Campus Virtual</span><h1 class="tau-banner-title">Conectando <span class="tau-accent-text">saberes</span></h1><span class="tau-banner-subtitle">Educación con Propósito</span><p class="tau-banner-desc">Accede a tus cursos, recursos académicos y herramientas de aprendizaje en un entorno diseñado para tu crecimiento profesional.</p><a href="/login" class="btn-tau-banner-explore">Ingresar a mi espacio</a></div></div>\');}' .
  '}' .
  /* ── Inyectar el código de diseño de botones del constructor en la cabecera ── */
  'var checkBtnT=setInterval(function(){' .
    'var path=window.location.pathname;' .
    'if(path.indexOf("/course/edit.php")!==-1) { clearInterval(checkBtnT); return; }' .
    'var createElements=document.querySelectorAll("a[href*=\'/course/edit.php\'], form[action*=\'/course/edit.php\']");' .
    'if(createElements.length===0)return;' .
    'clearInterval(checkBtnT);' .
    'function styleBtn(btn, type){' .
      'if(!btn)return;' .
      'btn.style.setProperty("height","42px","important");' .
      'btn.style.setProperty("padding","0 22px","important");' .
      'btn.style.setProperty("border-radius","8px","important");' .
      'btn.style.setProperty("font-size","0.88rem","important");' .
      'btn.style.setProperty("font-weight","700","important");' .
      'btn.style.setProperty("display","inline-flex","important");' .
      'btn.style.setProperty("align-items","center","important");' .
      'btn.style.setProperty("justify-content","center","important");' .
      'btn.style.setProperty("white-space","nowrap","important");' .
      'btn.style.setProperty("text-decoration","none","important");' .
      'btn.style.setProperty("box-sizing","border-box","important");' .
      'btn.style.setProperty("margin","0","important");' .
      'btn.style.setProperty("transition","all 0.2s cubic-bezier(0.4, 0, 0.2, 1)","important");' .
      'btn.style.setProperty("flex","1 1 0%","important");' .
      'btn.style.setProperty("max-width","380px","important");' .
      'btn.style.setProperty("width","100%","important");' .
      'btn.style.setProperty("text-align","center","important");' .
      'if(type==="manage"){' .
        'btn.style.setProperty("background","#ffffff","important");' .
        'btn.style.setProperty("color","#c62b3a","important");' .
        'btn.style.setProperty("border","2px solid #c62b3a","important");' .
        'btn.style.setProperty("box-shadow","0 2px 6px rgba(198, 43, 58, 0.08)","important");' .
        'btn.onmouseenter=function(){' .
          'btn.style.setProperty("background","#fff2f3","important");' .
          'btn.style.setProperty("transform","translateY(-1.5px)","important");' .
          'btn.style.setProperty("box-shadow","0 6px 14px rgba(198, 43, 58, 0.16)","important");' .
        '};' .
        'btn.onmouseleave=function(){' .
          'btn.style.setProperty("background","#ffffff","important");' .
          'btn.style.setProperty("transform","none","important");' .
          'btn.style.setProperty("box-shadow","0 2px 6px rgba(198, 43, 58, 0.08)","important");' .
        '};' .
      '}else if(type==="create"){' .
        'btn.style.setProperty("background","#c62b3a","important");' .
        'btn.style.setProperty("color","#ffffff","important");' .
        'btn.style.setProperty("border","2px solid #c62b3a","important");' .
        'btn.style.setProperty("box-shadow","0 4px 10px rgba(198, 43, 58, 0.18)","important");' .
        'btn.onmouseenter=function(){' .
          'btn.style.setProperty("background","#b02230","important");' .
          'btn.style.setProperty("border-color","#b02230","important");' .
          'btn.style.setProperty("transform","translateY(-1.5px)","important");' .
          'btn.style.setProperty("box-shadow","0 8px 18px rgba(198, 43, 58, 0.28)","important");' .
        '};' .
        'btn.onmouseleave=function(){' .
          'btn.style.setProperty("background","#c62b3a","important");' .
          'btn.style.setProperty("border-color","#c62b3a","important");' .
          'btn.style.setProperty("transform","none","important");' .
          'btn.style.setProperty("box-shadow","0 4px 10px rgba(198, 43, 58, 0.18)","important");' .
        '};' .
      '}else if(type==="ai"){' .
        'btn.style.setProperty("background","linear-gradient(135deg, #c62b3a 0%, #8d182a 100%)","important");' .
        'btn.style.setProperty("color","#ffffff","important");' .
        'btn.style.setProperty("border","none","important");' .
        'btn.style.setProperty("box-shadow","0 4px 12px rgba(198, 43, 58, 0.25)","important");' .
        'btn.onmouseenter=function(){' .
          'btn.style.setProperty("transform","translateY(-1.5px)","important");' .
          'btn.style.setProperty("box-shadow","0 8px 20px rgba(198, 43, 58, 0.4)","important");' .
        '};' .
        'btn.onmouseleave=function(){' .
          'btn.style.setProperty("transform","none","important");' .
          'btn.style.setProperty("box-shadow","0 4px 12px rgba(198, 43, 58, 0.25)","important");' .
        '};' .
      '}' .
    '}' .
    'var el=null;' .
    'for(var i=0;i<createElements.length;i++){' .
      'var candidate=createElements[i];' .
      'if(candidate.closest(\'.drawer\')||candidate.closest(\'#nav-drawer\')||candidate.closest(\'[data-region="drawer"]\')||candidate.closest(\'.flat-navigation\')||candidate.closest(\'.nav\')||candidate.closest(\'#theme_moove_drawer\')){continue;}' .
      'if(candidate.closest(\'#page-header\')||candidate.closest(\'.header-actions\')||candidate.closest(\'.tertiary-navigation\')||candidate.closest(\'#region-main\')||candidate.closest(\'[role="main"]\')||candidate.closest(\'.header-extra-actions\')){' .
        'el=candidate;break;' .
      '}' .
    '}' .
    'if(!el&&createElements.length>0){' .
      'for(var i=0;i<createElements.length;i++){' .
        'var candidate=createElements[i];' .
        'if(!candidate.closest(\'.drawer\')&&!candidate.closest(\'#nav-drawer\')&&!candidate.closest(\'[data-region="drawer"]\')&&!candidate.closest(\'.flat-navigation\')&&!candidate.closest(\'#theme_moove_drawer\')){' .
          'el=candidate;break;' .
        '}' .
      '}' .
    '}' .
    'if(!el)el=createElements[0];' .
    'if(!el||el.dataset.tauAiAdded)return;' .
    'el.dataset.tauAiAdded="1";' .
    'var aiBtn=document.createElement("a");' .
    'aiBtn.id="tau-ai-category-btn";' .
    'aiBtn.className="tau-btn-ai";' .
    'aiBtn.innerHTML=\'<svg width=\"14\" height=\"14\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" style=\"margin-right: 6px; vertical-align: middle;\"><path d=\"M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83\"/></svg> Crear curso con IA\';' .
    'var categoryId="";' .
    'var targetUrl=el.tagName==="FORM"?el.action:el.href;' .
    'if(targetUrl){' .
      'var match=targetUrl.match(/[?&]category=(\\d+)/);' .
      'if(match)categoryId=match[1];' .
    '}' .
    'if(!categoryId){' .
      'var urlMatch=window.location.search.match(/[?&]categoryid=(\\d+)/);' .
      'if(urlMatch)categoryId=urlMatch[1];' .
    '}' .
    'aiBtn.href="/local/tau_course_creator_ai/index.php"+(categoryId?"?category="+categoryId:"");' .
    'el.parentNode.insertBefore(aiBtn,el.nextSibling);' .
    'var parent=el.parentNode;' .
    'if(parent){' .
      'parent.classList.add("tau-header-buttons-container");' .
      'parent.style.setProperty("display","flex","important");' .
      'parent.style.setProperty("align-items","center","important");' .
      'parent.style.setProperty("justify-content","flex-end","important");' .
      'parent.style.setProperty("gap","12px","important");' .
      'parent.style.setProperty("flex-wrap","nowrap","important");' .
      'parent.style.setProperty("flex","1 1 auto","important");' .
      'parent.style.setProperty("width","100%","important");' .
      'parent.style.setProperty("max-width","1140px","important");' .
      'parent.style.setProperty("margin-left","auto","important");' .
    '}' .
    'var manageBtn=null;' .
    'var candidates=document.querySelectorAll("a, button, input[type=\'submit\'], input[type=\'button\']");' .
    'candidates.forEach(function(c){' .
      'var text=(c.textContent||c.value||"").toLowerCase();' .
      'if(text.indexOf("gestionar")!==-1&&((c.href&&c.href.indexOf("course")!==-1)||c.closest(".header-actions")||c.parentNode===parent)){' .
        'manageBtn=c;' .
      '}' .
    '});' .
    'var manageEl=manageBtn;' .
    'if(manageBtn){' .
      'if(manageBtn.tagName==="INPUT"||manageBtn.tagName==="BUTTON"){' .
        'var formAncestor=manageBtn.closest("form");' .
        'if(formAncestor)manageEl=formAncestor;' .
      '}' .
    '}' .
    'if(manageEl&&manageEl.parentNode!==parent&&parent){' .
      'parent.insertBefore(manageEl,el);' .
    '}' .
    'if(manageBtn){' .
      'manageBtn.classList.add("tau-btn-manage");' .
    '}' .
    'var manageToStyle=manageBtn;' .
    'if(manageEl&&manageEl.tagName==="FORM"){' .
      'manageEl.style.setProperty("display","inline-flex","important");' .
      'manageEl.style.setProperty("margin","0","important");' .
      'manageEl.style.setProperty("flex","1 1 0%","important");' .
      'manageEl.style.setProperty("max-width","380px","important");' .
      'manageEl.style.setProperty("width","100%","important");' .
      'var mFormDiv=manageEl.querySelector("div");' .
      'if(mFormDiv){' .
        'mFormDiv.style.setProperty("width","100%","important");' .
        'mFormDiv.style.setProperty("height","100%","important");' .
        'mFormDiv.style.setProperty("display","inline-flex","important");' .
        'mFormDiv.style.setProperty("align-items","center","important");' .
        'mFormDiv.style.setProperty("justify-content","center","important");' .
        'mFormDiv.style.setProperty("margin","0","important");' .
        'mFormDiv.style.setProperty("padding","0","important");' .
        'mFormDiv.style.setProperty("box-sizing","border-box","important");' .
      '}' .
      'manageToStyle=manageEl.querySelector("button, input[type=\'submit\']");' .
      'if(manageToStyle){' .
        'manageToStyle.style.setProperty("width","100%","important");' .
        'manageToStyle.style.setProperty("height","100%","important");' .
      '}' .
    '}' .
    'var buttonToStyle=el;' .
    'if(el.tagName==="FORM"){' .
      'el.style.setProperty("display","inline-flex","important");' .
      'el.style.setProperty("margin","0","important");' .
      'el.style.setProperty("flex","1 1 0%","important");' .
      'el.style.setProperty("max-width","380px","important");' .
      'el.style.setProperty("width","100%","important");' .
      'var formDiv=el.querySelector("div");' .
      'if(formDiv){' .
        'formDiv.style.setProperty("width","100%","important");' .
        'formDiv.style.setProperty("height","100%","important");' .
        'formDiv.style.setProperty("display","inline-flex","important");' .
        'formDiv.style.setProperty("align-items","center","important");' .
        'formDiv.style.setProperty("justify-content","center","important");' .
        'formDiv.style.setProperty("margin","0","important");' .
        'formDiv.style.setProperty("padding","0","important");' .
        'formDiv.style.setProperty("box-sizing","border-box","important");' .
      '}' .
      'buttonToStyle=el.querySelector("button, input[type=\'submit\']");' .
      'if(buttonToStyle){' .
        'buttonToStyle.classList.add("tau-btn-create");' .
        'buttonToStyle.style.setProperty("width","100%","important");' .
        'buttonToStyle.style.setProperty("height","100%","important");' .
      '}' .
    '}' .
    'styleBtn(manageToStyle,"manage");' .
    'styleBtn(buttonToStyle,"create");' .
    'styleBtn(aiBtn,"ai");' .
  '},100);' .
'});' .
'})();</script>';
}
set_config('additionalhtmlfooter', $topofbody);
fwrite(STDOUT, "  [ok] JS: dark mode + login welcome\n");

// ─── Manrope font in <head> ─────────────────────────────────────────────────

$loginStyle = '';
if (false) {
$loginStyle_disabled = '<style>'
    . 'body#page-login-index footer,body#page-login-index #page-footer,body#page-login-index .moove-footer,body#page-login-index .tau-login-global-footer,body#page-login-index footer.footer-dark,body#page-login-index footer.footer-light,body#page-login-index #page-footer.footer-dark,body#page-login-index #page-footer.footer-light,body#page-login-index .usertour,body#page-login-index .toolRegionActive,body#page-login-index .popover-region,body#page-login-index #goto-top-link,body#page-login-index .login-languagemenu,body#page-login-index .cc-window,body#page-login-index .cc-banner,body#page-login-index .cookieconsent,body#page-login-index #cookie-policy-banner,body#page-login-index #moove-footer{display:none!important;}'
    . '.tau-modal-backdrop{position:fixed!important;top:0!important;left:0!important;width:100%!important;height:100%!important;background:rgba(10,2,5,0.55)!important;backdrop-filter:blur(8px)!important;-webkit-backdrop-filter:blur(8px)!important;display:flex!important;align-items:center!important;justify-content:center!important;z-index:10000!important;opacity:0!important;pointer-events:none!important;transition:opacity 0.3s ease!important;}'
    . '.tau-modal-backdrop.show{opacity:1!important;pointer-events:auto!important;}'
    . '.tau-cookie-modal-card{background:rgba(255,255,255,0.98)!important;border:1px solid rgba(255,255,255,0.2)!important;border-radius:24px!important;width:90%!important;max-width:520px!important;box-shadow:0 30px 70px rgba(0,0,0,0.35)!important;transform:scale(0.92)!important;transition:transform 0.3s cubic-bezier(0.34,1.56,0.64,1)!important;overflow:hidden!important;}'
    . '[data-bs-theme="dark"] .tau-cookie-modal-card{background:rgba(30,30,46,0.96)!important;border-color:rgba(255,255,255,0.08)!important;}'
    . '.tau-modal-backdrop.show .tau-cookie-modal-card{transform:scale(1)!important;}'
    . '.tau-cookie-modal-header{background:linear-gradient(135deg,#c62b3a 0%,#e87a84 100%)!important;color:#fff!important;padding:20px 24px!important;display:flex!important;align-items:center!important;justify-content:space-between!important;}'
    . '[data-bs-theme="dark"] .tau-cookie-modal-header{background:linear-gradient(135deg,#8e1f2d 0%,#d14d5f 100%)!important;}'
    . '.tau-cookie-modal-title{font-size:1.15rem!important;font-weight:800!important;margin:0!important;letter-spacing:-0.3px!important;color:#fff!important;}'
    . '.tau-cookie-modal-close{background:none!important;border:none!important;color:#fff!important;font-size:1.4rem!important;cursor:pointer!important;line-height:1!important;opacity:0.8!important;transition:opacity 0.2s!important;}'
    . '.tau-cookie-modal-close:hover{opacity:1!important;}'
    . '.tau-cookie-modal-body{padding:28px!important;font-size:0.88rem!important;line-height:1.65!important;color:#444!important;text-align:left!important;}'
    . '[data-bs-theme="dark"] .tau-cookie-modal-body{color:#ddd!important;}'
    . '.tau-cookie-modal-body p{margin-bottom:16px!important;}'
    . '.tau-cookie-modal-body p:last-child{margin-bottom:0!important;}'
    . '.tau-cookie-modal-footer{padding:16px 28px!important;border-top:1px dashed rgba(0,0,0,0.06)!important;background:rgba(0,0,0,0.01)!important;text-align:right!important;}'
    . '[data-bs-theme="dark"] .tau-cookie-modal-footer{border-top-color:rgba(255,255,255,0.06)!important;background:rgba(255,255,255,0.01)!important;}'
    . '.tau-cookie-modal-btn{background:#c62b3a!important;border:none!important;color:#fff!important;border-radius:12px!important;padding:8px 24px!important;font-weight:700!important;cursor:pointer!important;transition:all 0.2s ease!important;font-size:0.85rem!important;}'
    . '.tau-cookie-modal-btn:hover{background:#a32230!important;transform:translateY(-1px)!important;box-shadow:0 4px 12px rgba(198, 43, 58, 0.25)!important;}'
    . '@keyframes loginCardIn{from{opacity:0;transform:translateY(28px) scale(.96)}to{opacity:1;transform:translateY(0) scale(1)}}'
    . 'body#page-login-index .login-wrapper{display:flex!important;flex-direction:row!important;flex-wrap:wrap!important;justify-content:center!important;align-items:stretch!important;gap:30px!important;max-width:1000px!important;margin:0 auto!important;padding:40px 20px!important;min-height:auto!important;background:transparent!important;box-shadow:none!important;}'
    . 'body#page-login-index .login-container{width:390px!important;max-width:94vw!important;margin:0!important;border-radius:24px!important;box-sizing:border-box!important;padding:24px 30px!important;box-shadow:0 12px 40px rgba(0,0,0,.22),0 2px 10px rgba(0,0,0,.1)!important;background:rgba(255,255,255,0.96)!important;backdrop-filter:blur(8px)!important;border:1px solid rgba(255,255,255,0.1)!important;display:flex!important;flex-direction:column!important;justify-content:flex-start!important;animation:loginCardIn .45s cubic-bezier(.34,1.56,.64,1) both!important;}'
    . 'body#page-login-index .tau-login-btnwrap{margin-top:20px!important;width:100%!important;padding-top:16px!important;}'
    . 'body#page-login-index .tau-login-btnbox{width:100%!important;text-align:center!important;padding:15px 0!important;}'
    . '[data-bs-theme="dark"] body#page-login-index .login-container{background:rgba(30,30,46,0.95)!important;border-color:rgba(255,255,255,0.05)!important;}'
    . '@media (max-width:991px){body#page-login-index .login-wrapper{flex-direction:column!important;align-items:center!important;gap:24px!important;}}'
    . 'body#page-login-index .loginform{display:block!important;margin:0!important;width:100%!important;}'
    . 'body#page-login-index .loginform>.col{width:100%!important;max-width:100%!important;flex:none!important;padding:0!important;}'
    . 'body#page-login-index .login-identityprovider-btn{width:100%!important;min-width:0!important;box-sizing:border-box!important;display:flex!important;align-items:center!important;justify-content:center!important;gap:10px!important;}'
    . 'body#page-login-index #loginlogo,body#page-login-index #tau-presencial-loginlogo{display:block!important;text-align:center!important;margin-bottom:.75rem!important;}'
    . 'body#page-login-index #loginlogo img,body#page-login-index #tau-presencial-loginlogo img{max-width:260px!important;height:auto!important;}'
    . 'body#page-login-index .login-identityproviders h2{display:none!important;}'
    . 'body#page-login-index .tau-login-global-footer{width:100%!important;text-align:center!important;margin-top:20px!important;display:flex!important;justify-content:center!important;align-items:center!important;}'
    . 'body#page-login-index .tau-login-global-footer .d-flex{display:flex!important;justify-content:center!important;align-items:center!important;gap:10px!important;flex-wrap:wrap!important;padding:8px 0 4px!important;width:100%!important;}'
    . 'body#page-login-index .divider.border-start{display:none!important;}'
    . 'body#page-login-index .login-languagemenu a,body#page-login-index .login-languagemenu .dropdown-toggle{color:#888!important;text-decoration:none!important;font-size:.82rem!important;}'
    . 'body#page-login-index .login-languagemenu a:hover{color:#c62b3a!important;}'
    . 'body#page-login-index .btn-secondary{font-size:.78rem!important;padding:2px 12px!important;background:transparent!important;border:1px solid #ddd!important;color:#888!important;border-radius:20px!important;box-shadow:none!important;}'
    . 'body#page-login-index .btn-secondary:hover{border-color:#c62b3a!important;color:#c62b3a!important;}'
    . 'body#page-login-index .loginerrors,body#page-login-index .col>.alert{display:none!important;}'
    . 'body#page-login-index .tau-login-card-warnings{margin:12px 0 0!important;padding:10px 12px!important;border-radius:12px!important;background:rgba(198,43,58,0.03)!important;border:1px solid rgba(198,43,58,0.08)!important;font-size:0.73rem!important;color:#555!important;line-height:1.45!important;text-align:center!important;}'
    . 'body#page-login-index .tau-login-card-warnings a{color:#c62b3a!important;font-weight:600!important;text-decoration:underline!important;}'
    . 'body#page-login-index .tau-login-card-links{margin-top:15px!important;padding-top:12px!important;border-top:1px dashed rgba(0,0,0,0.07)!important;display:flex!important;justify-content:center!important;align-items:center!important;flex-wrap:wrap!important;gap:10px!important;font-size:0.72rem!important;width:100%!important;}'
    . 'body#page-login-index .tau-login-card-links a{color:#777!important;text-decoration:none!important;transition:color 0.18s ease!important;}'
    . 'body#page-login-index .tau-login-card-links a:hover{color:#c62b3a!important;}'
    . 'body#page-login-index .tau-login-card-links .tau-sep{color:#ccc!important;font-size:0.65rem!important;}'
    . '[data-bs-theme="dark"] body#page-login-index .tau-login-card-warnings{background:rgba(232,113,124,0.04)!important;border-color:rgba(232,113,124,0.12)!important;color:#bbb!important;}'
    . '[data-bs-theme="dark"] body#page-login-index .tau-login-card-links{border-top-color:rgba(255,255,255,0.05)!important;}'
    . '[data-bs-theme="dark"] body#page-login-index .tau-login-card-links a{color:#aaa!important;}'
    . '[data-bs-theme="dark"] body#page-login-index .tau-login-card-links a:hover{color:#e8717c!important;}'
    . '.course-card .card-text{display:none!important;}'
    . '#mooveslideshow .carousel-caption{display:none!important;}'
    . '#mooveslideshow .carousel-item{position:relative!important;overflow:hidden!important;border-radius:20px!important;}'
    . '#mooveslideshow .carousel-item img{max-height:480px!important;object-fit:cover!important;border-radius:20px!important;box-shadow:0 16px 40px rgba(0,0,0,0.18)!important;}'
    . '.tau-banner-overlay{position:absolute!important;top:0!important;left:0!important;width:100%!important;height:100%!important;display:flex!important;align-items:center!important;padding-left:6%!important;z-index:10!important;box-sizing:border-box!important;}'
    . '.tau-banner-card{background:rgba(15,2,5,0.65)!important;backdrop-filter:blur(24px)!important;-webkit-backdrop-filter:blur(24px)!important;border:1px solid rgba(255,255,255,0.15)!important;border-left:6px solid #c62b3a!important;border-radius:28px!important;padding:36px 40px!important;max-width:480px!important;box-shadow:0 30px 70px rgba(0,0,0,0.5)!important;text-align:left!important;}'
    . '.tau-banner-pretitle{font-size:0.78rem!important;font-weight:700!important;letter-spacing:3px!important;color:#e87a84!important;text-transform:uppercase!important;margin-bottom:6px!important;display:block!important;}'
    . '.tau-banner-title{font-size:2.4rem!important;font-weight:800!important;color:#fff!important;line-height:1.15!important;margin-bottom:8px!important;letter-spacing:-0.8px!important;}'
    . '.tau-accent-text{background:linear-gradient(90deg,#fff 0%,#ffc5cb 100%)!important;-webkit-background-clip:text!important;-webkit-text-fill-color:transparent!important;}'
    . '.tau-banner-subtitle{font-size:0.85rem!important;font-weight:700!important;color:#fff!important;background:linear-gradient(135deg,#c62b3a 0%,#a32230 100%)!important;padding:4px 12px!important;border-radius:6px!important;display:inline-block!important;margin-bottom:16px!important;letter-spacing:1px!important;}'
    . '.tau-banner-desc{font-size:0.92rem!important;color:rgba(255,255,255,0.9)!important;line-height:1.6!important;margin-bottom:24px!important;font-weight:400!important;}'
    . '.btn-tau-banner-explore{background:linear-gradient(135deg,#c62b3a 0%,#8e1f2d 100%)!important;border:none!important;color:#fff!important;border-radius:14px!important;padding:12px 28px!important;font-weight:700!important;font-size:0.88rem!important;letter-spacing:0.5px!important;box-shadow:0 8px 24px rgba(198,43,58,0.3)!important;transition:all 0.22s ease!important;display:inline-flex!important;align-items:center!important;justify-content:center!important;text-decoration:none!important;}'
    . '.btn-tau-banner-explore:hover{transform:translateY(-2px)!important;box-shadow:0 12px 28px rgba(198,43,58,0.4)!important;color:#fff!important;}'
    . '@media(max-width:767px){.tau-banner-overlay{position:relative!important;padding:20px!important;background:#0d1117!important;display:block!important;height:auto!important;}.tau-banner-card{max-width:100%!important;border-radius:16px!important;padding:20px!important;margin:0 auto!important;}.tau-banner-title{font-size:1.6rem!important;}}'
    . '#numbers{background:#ffffff!important;padding:5.625rem 0!important;}'
    . '[data-bs-theme="dark"] #numbers{background:#12121a!important;}'
    . '#numbers .sectionheading h2{font-family:\'Manrope\',sans-serif!important;font-size:2.2rem!important;font-weight:800!important;color:#1e1e2f!important;line-height:1.25!important;letter-spacing:-0.8px!important;margin-bottom:20px!important;}'
    . '[data-bs-theme="dark"] #numbers .sectionheading h2{color:#ffffff!important;}'
    . '#numbers .sectionheading p{font-family:\'Manrope\',sans-serif!important;font-size:0.95rem!important;color:#5d5d70!important;line-height:1.65!important;}'
    . '[data-bs-theme="dark"] #numbers .sectionheading p{color:rgba(255,255,255,0.7)!important;}'
    . '#numbers .rate-box{border-radius:24px!important;padding:36px 40px!important;transition:transform 0.25s cubic-bezier(0.25,0.8,0.25,1),box-shadow 0.25s cubic-bezier(0.25,0.8,0.25,1)!important;border:1px solid rgba(255,255,255,0.12)!important;box-sizing:border-box!important;}'
    . '#numbers .rate-box.bg-primary{background:linear-gradient(135deg,#c62b3a 0%,#7f0d1c 100%)!important;box-shadow:0 10px 30px rgba(198, 43, 58, 0.18)!important;}'
    . '#numbers .rate-box.bg-cloudburst,#numbers .rate-box-2{background:linear-gradient(135deg,#242436 0%,#171724 100%)!important;box-shadow:0 10px 30px rgba(0, 0, 0, 0.2)!important;border-color:rgba(255,255,255,0.08)!important;}'
    . '#numbers .rate-box:hover{transform:translateY(-4px)!important;}'
    . '#numbers .rate-box.bg-primary:hover{box-shadow:0 16px 40px rgba(198, 43, 58, 0.3)!important;}'
    . '#numbers .rate-box.bg-cloudburst:hover,#numbers .rate-box-2:hover{box-shadow:0 16px 40px rgba(0, 0, 0, 0.35)!important;}'
    . '#numbers .rate-box h3{font-family:\'Manrope\',sans-serif!important;color:#ffffff!important;font-size:56px!important;font-weight:800!important;line-height:1.1!important;margin-bottom:18px!important;letter-spacing:-1.5px!important;text-shadow:0 2px 10px rgba(0, 0, 0, 0.15)!important;}'
    . '#numbers .rate-box p{font-family:\'Manrope\',sans-serif!important;color:rgba(255,255,255,0.9)!important;font-size:0.85rem!important;font-weight:700!important;letter-spacing:0.8px!important;text-transform:uppercase!important;line-height:1.4!important;}'
    . 'body#page-login-index .tau-login-card-foot-controls{display:flex!important;justify-content:space-between!important;align-items:center!important;gap:12px!important;margin-top:12px!important;padding-top:16px!important;border-top:1px dashed rgba(0,0,0,0.08)!important;width:100%!important;box-sizing:border-box!important;}'
    . '[data-bs-theme="dark"] body#page-login-index .tau-login-card-foot-controls{border-top-color:rgba(255,255,255,0.08)!important;}'
    . 'body#page-login-index .tau-lang-selector-custom{position:relative!important;}'
    . 'body#page-login-index .tau-lang-btn,body#page-login-index .tau-cookie-btn{display:inline-flex!important;align-items:center!important;gap:6px!important;padding:6px 14px!important;border-radius:20px!important;background:rgba(0,0,0,0.03)!important;border:1px solid rgba(0,0,0,0.06)!important;color:#555!important;font-size:0.76rem!important;font-weight:600!important;text-decoration:none!important;cursor:pointer!important;transition:all 0.2s ease!important;line-height:1.2!important;}'
    . '[data-bs-theme="dark"] body#page-login-index .tau-lang-btn,[data-bs-theme="dark"] body#page-login-index .tau-cookie-btn{background:rgba(255,255,255,0.04)!important;border-color:rgba(255,255,255,0.08)!important;color:#ccc!important;}'
    . 'body#page-login-index .tau-lang-btn:hover,body#page-login-index .tau-cookie-btn:hover{background:rgba(198,43,58,0.08)!important;border-color:rgba(198,43,58,0.2)!important;color:#c62b3a!important;}'
    . '[data-bs-theme="dark"] body#page-login-index .tau-lang-btn:hover,[data-bs-theme="dark"] body#page-login-index .tau-cookie-btn:hover{background:rgba(232,113,124,0.12)!important;border-color:rgba(232,113,124,0.25)!important;color:#e8717c!important;}'
    . 'body#page-login-index .tau-lang-dropdown-menu{position:absolute!important;bottom:calc(100% + 8px)!important;left:0!important;background:#fff!important;border:1px solid rgba(0,0,0,0.1)!important;border-radius:12px!important;box-shadow:0 10px 25px rgba(0,0,0,0.15)!important;min-width:180px!important;padding:6px!important;display:none!important;z-index:1000!important;}'
    . '[data-bs-theme="dark"] body#page-login-index .tau-lang-dropdown-menu{background:#242436!important;border-color:rgba(255,255,255,0.08)!important;box-shadow:0 10px 25px rgba(0,0,0,0.3)!important;}'
    . 'body#page-login-index .tau-lang-selector-custom.show .tau-lang-dropdown-menu{display:block!important;}'
    . 'body#page-login-index .tau-lang-dropdown-menu a{display:block!important;padding:8px 12px!important;border-radius:8px!important;color:#444!important;font-size:0.78rem!important;font-weight:600!important;text-decoration:none!important;text-align:left!important;transition:background 0.15s ease,color 0.15s ease!important;}'
    . '[data-bs-theme="dark"] body#page-login-index .tau-lang-dropdown-menu a{color:#ddd!important;}'
    . 'body#page-login-index .tau-lang-dropdown-menu a:hover{background:rgba(198,43,58,0.08)!important;color:#c62b3a!important;}'
    . '[data-bs-theme="dark"] body#page-login-index .tau-lang-dropdown-menu a:hover{background:rgba(232,113,124,0.12)!important;color:#e8717c!important;}'
    . '#tau-preloader{position:fixed!important;inset:0!important;width:100vw!important;height:100vh!important;background:radial-gradient(circle at 18% 20%, rgba(255,255,255,.7) 0%, rgba(255,255,255,0) 22%),radial-gradient(circle at 82% 16%, rgba(111,168,255,.18) 0%, rgba(111,168,255,0) 20%),linear-gradient(180deg, #eef6ff 0%, #e6f1ff 52%, #ddeafe 100%)!important;display:flex!important;align-items:center!important;justify-content:center!important;z-index:999999!important;animation:tauPlIn .18s ease both!important;}'
    . '#tau-preloader.tau-pl-out{animation:tauPlOut .28s ease both!important;pointer-events:none!important;}'
    . '.tau-pl-inner{display:flex!important;flex-direction:column!important;align-items:center!important;gap:18px!important;padding:28px 34px!important;border-radius:28px!important;background:rgba(255,255,255,.52)!important;border:1px solid rgba(140,181,235,.34)!important;box-shadow:0 20px 50px rgba(62,109,173,.12)!important;backdrop-filter:blur(16px)!important;-webkit-backdrop-filter:blur(16px)!important;}'
    . '.tau-pl-ring-wrap{position:relative!important;width:90px!important;height:90px!important;display:flex!important;align-items:center!important;justify-content:center!important;}'
    . '.tau-pl-svg{position:absolute!important;inset:0!important;width:90px!important;height:90px!important;}'
    . '.tau-pl-arc{transform-box:fill-box!important;transform-origin:center!important;animation:tauSpinLoop 1.05s linear infinite!important;}'
    . '.tau-pl-icon{width:52px!important;height:52px!important;object-fit:contain!important;position:relative!important;z-index:1!important;animation:tauIconIn .4s cubic-bezier(.34,1.56,.64,1) .1s both!important;}'
    . '.tau-pl-label{font-family:\'Manrope\',system-ui,sans-serif!important;font-size:.6rem!important;font-weight:800!important;letter-spacing:.22em!important;text-transform:uppercase!important;color:rgba(22,58,110,.56)!important;animation:tauLabelIn .35s ease .35s both!important;}'
    . '@keyframes tauPlIn{from{opacity:0}to{opacity:1}}'
    . '@keyframes tauPlOut{from{opacity:1}to{opacity:0}}'
    . '@keyframes tauSpinLoop{0%{transform:rotate(-90deg)}100%{transform:rotate(270deg)}}'
    . '@keyframes tauIconIn{from{opacity:0;transform:scale(.65)}to{opacity:1;transform:scale(1)}}'
    . '@keyframes tauLabelIn{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:translateY(0)}}'
    . '</style>';
}

$conectimeHide = '<style>'
    . 'footer .copyright,footer .madeby{display:none!important;}'
    . '</style>';

$darkModeScript = '<script>
(function() {
    var t=localStorage.getItem("tau-theme")||"light";
    document.documentElement.setAttribute("data-bs-theme", t);
    window.addEventListener("DOMContentLoaded", function() {
        document.documentElement.setAttribute("data-bs-theme", t);
    });
})();
</script>';

$fontHead = '<link rel="preconnect" href="https://fonts.googleapis.com">'
    . '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
    . '<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">'
    . '<link rel="stylesheet" href="/local/tau_course_creator_ai/assets/css/tau_global.css?v=20260614c">'
    . '<style>#page-my-index #page-header .mb-2, #page-my-index .mb-2 span.h2, #page-my-index .welcome-message { display: none !important; }</style>'
    . '<script>(function(){var t=localStorage.getItem("tau-theme")||"light";document.documentElement.setAttribute("data-bs-theme",t);})();</script>';
set_config('additionalhtmlhead', $fontHead);
fwrite(STDOUT, "  [ok] Manrope font\n");

// ─── Core auth & UX settings ────────────────────────────────────────────────

set_config('rememberusername', 0);
set_config('guestloginbutton', 0);
set_config('registerauth', '');
set_config('authloginviaemail', 0);
set_config('frontpage', '2,1');
set_config('frontpageloggedin', '2,1');

// ─── Upload assets ───────────────────────────────────────────────────────────

$assets = [
    ['source' => $brandingroot . '/assets/official/cesmag-tau-wordmark.png', 'filearea' => 'logo',         'config' => 'logo'],
    ['source' => $brandingroot . '/assets/official/tau-official-icon.png',   'filearea' => 'favicon',      'config' => 'favicon'],
    ['source' => $brandingroot . '/assets/official/tau-hero-bg.jpg',        'filearea' => 'sliderimage1', 'config' => 'sliderimage1'],
];
// Clear any previously-uploaded loginbgimg so our CSS gradient takes over
$filestorage->delete_area_files($systemcontext->id, 'theme_moove', 'loginbgimg', 0);
set_config('loginbgimg', '', 'theme_moove');

foreach ($assets as $asset) {
    if (!is_readable($asset['source'])) {
        fwrite(STDOUT, "  [skip] Asset not found: {$asset['source']}\n");
        continue;
    }

    $filename = basename($asset['source']);

    $filestorage->delete_area_files($systemcontext->id, 'theme_moove', $asset['filearea'], 0);
    $filestorage->create_file_from_pathname([
        'contextid' => $systemcontext->id,
        'component' => 'theme_moove',
        'filearea'  => $asset['filearea'],
        'itemid'    => 0,
        'filepath'  => '/',
        'filename'  => $filename,
    ], $asset['source']);

    set_config($asset['config'], '/' . $filename, 'theme_moove');
    fwrite(STDOUT, "  [ok] Uploaded: {$filename} -> {$asset['filearea']}\n");
}

// Upload Billy the mascot to the logo filearea so it can be served publicly
$mascot_source = $brandingroot . '/assets/official/tau-mascot-run.png';
if (is_readable($mascot_source)) {
    $fileexists = $filestorage->file_exists($systemcontext->id, 'theme_moove', 'logo', 0, '/', 'tau-mascot-run.png');
    if (!$fileexists) {
        $filestorage->create_file_from_pathname([
            'contextid' => $systemcontext->id,
            'component' => 'theme_moove',
            'filearea'  => 'logo',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => 'tau-mascot-run.png',
        ], $mascot_source);
        fwrite(STDOUT, "  [ok] Uploaded: tau-mascot-run.png -> logo filearea\n");
    } else {
        fwrite(STDOUT, "  [skip] Billy the mascot already uploaded\n");
    }
}

theme_reset_all_caches();

fwrite(STDOUT, "Moove TAU branding applied successfully.\n");
