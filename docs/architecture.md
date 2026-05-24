# Arquitectura Base

## Principios

- Moodle como plataforma LMS central.
- Frontend institucional desacoplado.
- Backend Node.js como capa de integración.
- Nginx como puerta de entrada.
- Escalabilidad horizontal por servicio.
- Sin modificaciones al core de Moodle.

## Mapa de módulos

- `apps/landing-react`: sitio institucional y acceso moderno.
- `apps/api-node`: APIs, webhooks, auth futura, analytics.
- `apps/moodle`: personalizaciones seguras por theme/plugin.
- `docker/`: composición local y base de despliegue.
- `infra/`: espacio reservado para IaC, CI/CD y observabilidad.

