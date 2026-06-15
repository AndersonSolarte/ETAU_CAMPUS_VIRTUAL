#!/usr/bin/env bash
set -euo pipefail

MARKER_FILE="/var/www/moodledata/.tau_theme_ready"
TARGET_THEME="${MOODLE_DEFAULT_THEME:-moove}"

if [[ ! -f /var/www/html/config.php ]]; then
  echo "config.php not found. Can not register theme."
  exit 1
fi

# Remove non-Moove third-party themes if they exist (permanent cleanup)
for unwanted in boost_union tau_enterprise; do
  if [[ -d /var/www/html/theme/${unwanted} ]]; then
    rm -rf "/var/www/html/theme/${unwanted}"
    echo "Removed unwanted theme: ${unwanted}"
  fi
done

# Verify the target theme exists.
if [[ ! -f /var/www/html/theme/${TARGET_THEME}/version.php ]]; then
  echo "Theme '${TARGET_THEME}' is missing from Moodle theme directory."
  exit 1
fi

CURRENT_THEME="$(php /var/www/html/admin/cli/cfg.php --name=theme --no-eol 2>/dev/null || true)"

php /var/www/html/admin/cli/upgrade.php --non-interactive

if [[ "${CURRENT_THEME}" != "${TARGET_THEME}" ]]; then
  php /var/www/html/admin/cli/cfg.php --name=theme --set="${TARGET_THEME}"
fi

# Apply Moove TAU branding.
echo "Applying Moove TAU branding..."
php /scripts/configure-moove.php

php /scripts/configure-google-oauth.php
php /var/www/html/admin/cli/purge_caches.php

touch "${MARKER_FILE}"
echo "Theme '${TARGET_THEME}' registered and TAU branding applied."
