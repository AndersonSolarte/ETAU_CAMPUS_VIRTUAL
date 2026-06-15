# TAU Branding

Espacio versionado para branding institucional aplicado sobre Boost Union.

## Estructura

- `assets/logo-header/`
- `assets/favicon/`
- `assets/login-background/`
- `scss/custom.scss`
- `tokens/colors.css`

## Uso previsto

- Cargar logo del header en Boost Union.
- Definir favicon institucional.
- Preparar fondo del login.
- Centralizar colores TAU y CESMAG para SCSS y ajustes visuales.

## Flujo de actualizacion

1. Ajusta los SVG y assets dentro de `assets/`.
2. Edita `scss/custom.scss`.
3. Ejecuta `docker compose up -d`.
4. Verifica `docker compose logs moodle-theme-upgrade --tail 100`.

Este directorio no es un theme Moodle instalable todavia. Es un workspace de branding.
