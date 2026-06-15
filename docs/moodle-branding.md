# Moodle Branding Guide

Guia operativa para el branding institucional de TAU Campus Virtual sobre Boost Union.

## Que queda automatizado

- Boost Union se sincroniza desde `apps/moodle/theme/boost_union`.
- El branding institucional se sincroniza desde `apps/moodle/theme/tau_branding`.
- El servicio `moodle-theme-upgrade` ejecuta upgrade, activa `boost_union`, aplica branding y purga caches.

## Activos institucionales

- Logo principal: `apps/moodle/theme/tau_branding/assets/logo-header/tau-wordmark.svg`
- Logo compacto: `apps/moodle/theme/tau_branding/assets/logo-header/tau-monogram.svg`
- Favicon: `apps/moodle/theme/tau_branding/assets/favicon/tau-favicon.svg`
- Fondo login: `apps/moodle/theme/tau_branding/assets/login-background/tau-login-grid.svg`

## SCSS institucional

- Archivo principal: `apps/moodle/theme/tau_branding/scss/custom.scss`
- La capa SCSS redefine navbar, login, footer, cards, sidebar, progreso y layouts responsivos.
- El SCSS se guarda automaticamente en la configuracion de `theme_boost_union`.

## Como cambiar logos y colores

1. Reemplaza los SVG dentro de `apps/moodle/theme/tau_branding/assets/`.
2. Ajusta paleta, espaciado y efectos en `apps/moodle/theme/tau_branding/scss/custom.scss`.
3. Ejecuta `docker compose up -d`.
4. Revisa `docker compose logs moodle-theme-upgrade --tail 100`.

## Como actualizar Boost Union

1. Descarga un tag compatible con Moodle 5 en `apps/moodle/theme/boost_union`.
2. Ejecuta `docker compose up -d`.
3. Verifica que `moodle-theme-upgrade` finalice sin errores.
4. Comprueba en Moodle que el theme activo siga siendo `boost_union`.

## Archivos clave

- `docker/moodle/sync-themes.sh`
- `docker/moodle/register-themes.sh`
- `docker/moodle/apply-boost-union-branding.php`
- `apps/moodle/theme/tau_branding/README.md`
