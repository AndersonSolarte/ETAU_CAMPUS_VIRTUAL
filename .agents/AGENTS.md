## Regla de Recuperación de Moodle

Cuando el tema visual de Moodle se rompa o se ponga gris, ejecuta docker cp para mover docker/moodle/configure-moove.php dentro de tau_moodle:/var/www/html/configure-moove.php, luego ejecuta docker exec tau_moodle php /var/www/html/configure-moove.php y luego purga las cachés (admin/cli/purge_caches.php).
