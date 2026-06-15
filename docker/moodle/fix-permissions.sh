#!/usr/bin/env bash
set -euo pipefail

if [[ ! -d /var/www/html ]]; then
  echo "/var/www/html does not exist."
  exit 1
fi

mkdir -p /var/www/moodledata

chown -R www-data:www-data /var/www/html
find /var/www/html -type d -exec chmod 755 {} + || chmod -R 755 /var/www/html
find /var/www/html -type f -exec chmod 644 {} + || true

if [[ -f /var/www/html/config.php ]]; then
  chmod 644 /var/www/html/config.php
fi

chown www-data:www-data /var/www/moodledata || true
chmod 777 /var/www/moodledata || true

echo "Moodle permissions normalized successfully."
