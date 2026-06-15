# TAU_CAMPUS_VIRTUAL

Arquitectura enterprise para un campus virtual moderno con Moodle 5.x como motor LMS, React como experiencia institucional y Node.js como capa de integraciones.

## Objetivo

- Mantener Moodle como LMS administrable y actualizable.
- Evitar modificaciones al core de Moodle.
- Construir una landing moderna e independiente.
- Preparar una capa API modular para integraciones, analítica, tiempo real e IA futura.
- Dejar una base lista para entornos de desarrollo y despliegue.

## Stack

- Moodle 5.x oficial sobre imagen `moodlehq/moodle-php-apache`
- PostgreSQL 16
- Redis 7
- Node.js 22 LTS style runtime
- Express
- React + Vite
- TailwindCSS
- Framer Motion
- Nginx reverse proxy
- Docker Compose

## Estructura

```txt
TAU_CAMPUS_VIRTUAL/
├── apps/
│   ├── frontend/
│   ├── backend/
│   └── moodle/
├── docker/
│   ├── nginx/
│   ├── postgres/
│   ├── redis/
│   └── moodledata/
├── infra/
├── docs/
├── docker-compose.yml
├── .env.example
├── .gitignore
├── README.md
└── package.json
```

## Servicios Docker

- `moodle`: LMS Moodle servido detrás de Nginx en `http://localhost:8080`
- `postgres`: base de datos principal de Moodle
- `redis`: servicio preparado para caché/sesiones
- `nginx`: reverse proxy frontal para Moodle
- `moodle-cron`: ejecuta `admin/cli/cron.php` cada minuto

## Arranque rápido

1. Copiar `.env.example` como `.env`.
2. Ajustar secretos y credenciales.
3. Levantar servicios con `docker compose up -d`.
4. Acceder a:
   - Moodle: `http://localhost:8080`
5. Primer acceso administrador:
   - Usuario: `admin`
   - Password inicial: `admin123*`

## Scripts

- `npm run dev`: levanta `frontend` y `backend` en local
- `npm run build`: compila apps Node/React
- `npm run docker:up`: inicia la plataforma completa
- `npm run docker:down`: detiene contenedores
- `npm run docker:logs`: sigue logs

## Moodle

- El core de Moodle no se modifica.
- La personalización debe hacerse con themes y plugins.
- Boost Union se instala desde código versionado en `apps/moodle/theme/boost_union` y se sincroniza automáticamente al volumen de Moodle.
- `apps/moodle/theme/tau_branding` queda listo para logos, favicon, fondo de login y SCSS institucional.
- Para que Docker Desktop en Windows no degrade tanto el rendimiento, los themes no se sirven desde bind mount directo: se copian al volumen Linux interno del contenedor.

## Boost Union

- Versión instalada en el repositorio: `v5.0-r26`
- Compatibilidad declarada por el plugin: Moodle `5.0`
- Activación automática en Docker:
  - `docker compose up -d`
  - el servicio `moodle-theme-upgrade` ejecuta `upgrade.php`, detecta el plugin y configura `boost_union` como theme por defecto

## Branding TAU

- Header logo: `apps/moodle/theme/tau_branding/assets/logo-header/`
- Favicon: `apps/moodle/theme/tau_branding/assets/favicon/`
- Login background: `apps/moodle/theme/tau_branding/assets/login-background/`
- SCSS base: `apps/moodle/theme/tau_branding/scss/custom.scss`
- Tokens de color: `apps/moodle/theme/tau_branding/tokens/colors.css`

## Actualizar Boost Union

1. Descargar un nuevo tag compatible desde `moodle-theme_boost_union`.
2. Reemplazar el contenido de `apps/moodle/theme/boost_union`.
3. Ejecutar `docker compose up -d`.
4. Verificar logs de `moodle-theme-upgrade`.

## Rendimiento

- PHP 8.3:
  - `memory_limit=512M`
  - `max_execution_time=180`
  - `upload_max_filesize=256M`
  - `post_max_size=256M`
  - OPcache activo con memoria ampliada
- Moodle:
  - sesiones en Redis
  - caché de aplicación y caché de sesión MUC mapeadas a Redis
  - `localcachedir` en `moodledata/localcache`
  - reverse proxy validado para `localhost:8080` con `Host` interno hacia Moodle
  - cron automático cada minuto con `moodle-cron`
- Nginx:
  - gzip
  - proxy buffering
  - keepalive
  - headers de caché para estáticos
- PostgreSQL:
  - `shared_buffers=256MB`
  - `work_mem=8MB`
  - `effective_cache_size=768MB`
  - `max_connections=200`

## Ajustar branding y rendimiento

- PHP tuning: `docker/php/conf.d/zz-tau-performance.ini`
- PostgreSQL tuning: `docker/postgres/postgresql.conf`
- Nginx tuning: `docker/nginx/default.conf`
- Optimización Moodle/Redis: `docker/moodle/optimize-moodle.php`
- Cron Moodle: `docker/moodle/run-cron.sh`
- Direcciones de proxy confiables: `MOODLE_REVERSEPROXY_ADDRESSES` en `.env`

## Validación operativa

- Reiniciar stack: `docker compose up -d`
- Ver estado: `docker compose ps`
- Ver cron: `docker compose logs moodle-cron --tail 50`

## Git y flujo de ramas

- Rama principal: `main`
- Rama de integración: `develop`
- Convención de commits: Conventional Commits
- Usuario GitHub objetivo: `@ftandersonsolarte`

## Desarrollo futuro

## Branding operativo

- El branding enterprise de Boost Union se aplica automÃ¡ticamente en cada `docker compose up -d`.
- Logos: `apps/moodle/theme/tau_branding/assets/logo-header/`
- Favicon: `apps/moodle/theme/tau_branding/assets/favicon/`
- Fondo del login: `apps/moodle/theme/tau_branding/assets/login-background/`
- SCSS institucional: `apps/moodle/theme/tau_branding/scss/custom.scss`
- AutomatizaciÃ³n del branding: `docker/moodle/apply-boost-union-branding.php`

## CÃ³mo cambiar branding

1. Reemplazar logos SVG en `apps/moodle/theme/tau_branding/assets/logo-header/`.
2. Reemplazar favicon en `apps/moodle/theme/tau_branding/assets/favicon/`.
3. Ajustar el fondo del login en `apps/moodle/theme/tau_branding/assets/login-background/`.
4. Editar colores, layout y microinteracciones en `apps/moodle/theme/tau_branding/scss/custom.scss`.
5. Ejecutar `docker compose up -d`.
6. Revisar `docker compose logs moodle-theme-upgrade --tail 100`.

- Branding TAU y design tokens
- Theme hijo completo para Moodle
- Landing institucional avanzada
- Integraciones con servicios externos
- SSO
- Analítica y dashboards
- Módulos IA
