# TAU_CAMPUS_VIRTUAL

Arquitectura enterprise para un campus virtual moderno con Moodle 5.x como motor LMS, React como experiencia institucional y Node.js como capa de integraciones.

## Objetivo

- Mantener Moodle como LMS administrable y actualizable.
- Evitar modificaciones al core de Moodle.
- Construir una landing moderna e independiente.
- Preparar una capa API modular para integraciones, analítica, tiempo real e IA futura.
- Dejar una base lista para entornos de desarrollo y despliegue.

## Stack

- Moodle 5.x oficial sobre imagen `bitnami/moodle`
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
│   ├── landing-react/
│   ├── api-node/
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

## Servicios

- `landing`: app React/Vite en `localhost:3000`
- `api`: backend Express en `localhost:4000`
- `moodle`: LMS Moodle en `localhost:8080` vía Nginx
- `postgres`: base de datos principal en `localhost:5432`
- `redis`: caché y base para realtime en `localhost:6379`
- `nginx`: reverse proxy frontal en `localhost`

## Arranque rápido

1. Copiar `.env.example` como `.env`.
2. Ajustar secretos y credenciales.
3. Levantar servicios con `docker compose up -d`.
4. Acceder a:
   - Landing: `http://localhost`
   - API: `http://localhost/api/health`
   - Moodle: `http://localhost:8080`

## Scripts

- `npm run dev`: levanta `landing-react` y `api-node` en local
- `npm run build`: compila apps Node/React
- `npm run docker:up`: inicia la plataforma completa
- `npm run docker:down`: detiene contenedores
- `npm run docker:logs`: sigue logs

## Moodle

- El core de Moodle no se modifica.
- La personalización debe hacerse con themes y plugins.
- Se prepara el theme hijo `tau_enterprise` para branding institucional.
- RemUI y Edwiser Course Format quedan previstos como dependencias funcionales del LMS, no embebidos en el core.

## Git y flujo de ramas

- Rama principal: `main`
- Rama de integración: `develop`
- Convención de commits: Conventional Commits
- Usuario GitHub objetivo: `@ftandersonsolarte`

## Desarrollo futuro

- Branding TAU y design tokens
- Theme hijo completo para Moodle
- Landing institucional avanzada
- Integraciones con servicios externos
- SSO
- Analítica y dashboards
- Módulos IA

