# Moodle Workspace

Estructura base para personalizaciones y extensiones seguras del LMS.

- `theme/boost_union`: theme oficial instalado desde repositorio y sincronizado al volumen de Moodle.
- `theme/tau_branding`: assets y SCSS institucional aplicado sobre Boost Union para TAU.
- `theme/tau_enterprise`: prototipo histórico interno, no usado como theme activo.
- `local/`: plugins locales de integración.
- `mod/`: módulos de actividad futuros.
- `blocks/`: bloques personalizados futuros.
- `moodledata/`: almacenamiento persistente para entornos Docker locales.

No instalar ni modificar el core manualmente aquí.
El código fuente versionado del theme vive en el repositorio y se copia al volumen Linux del contenedor para evitar lentitud extra por bind mounts en Docker Desktop Windows.
