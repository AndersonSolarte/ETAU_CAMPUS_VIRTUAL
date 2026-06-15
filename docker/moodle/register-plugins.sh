#!/usr/bin/env bash
set -euo pipefail

MARKER_FILE="/var/www/moodledata/.tau_plugins_ready"

if [[ ! -f /var/www/html/config.php ]]; then
  echo "config.php not found. Cannot register plugins."
  exit 1
fi

echo "Running Moodle upgrade to install TAU AI plugins..."
php /var/www/html/admin/cli/upgrade.php --non-interactive

php /scripts/configure-native-ollama.php

php /var/www/html/admin/cli/purge_caches.php

touch "${MARKER_FILE}"
echo "TAU AI plugins registered successfully."
