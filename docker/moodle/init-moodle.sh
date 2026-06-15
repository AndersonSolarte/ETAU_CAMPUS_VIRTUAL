#!/usr/bin/env bash
set -euo pipefail

if [[ -f /var/www/html/config.php ]]; then
  echo "config.php already exists. Moodle is already installed."
  exit 0
fi

if [[ -f /var/www/moodledata/.tau_moodle_installed ]]; then
  echo "Moodle already installed."
  exit 0
fi

mkdir -p /var/www/moodledata

for attempt in {1..30}; do
  if [[ -f /var/www/html/admin/cli/install.php ]]; then
    break
  fi

  echo "Waiting for Moodle codebase to be available... (${attempt}/30)"
  sleep 2
done

if [[ ! -f /var/www/html/admin/cli/install.php ]]; then
  echo "Moodle codebase is missing. Aborting installation."
  exit 1
fi

php /var/www/html/admin/cli/install.php \
  --agree-license \
  --lang=en \
  --wwwroot="${MOODLE_URL}" \
  --dataroot="/var/www/moodledata" \
  --dbtype=pgsql \
  --dbhost="${POSTGRES_HOST}" \
  --dbport=5432 \
  --dbname="${POSTGRES_DB}" \
  --dbuser="${POSTGRES_USER}" \
  --dbpass="${POSTGRES_PASSWORD}" \
  --fullname="${MOODLE_SITE_NAME}" \
  --shortname="${MOODLE_SHORT_NAME}" \
  --adminuser="${MOODLE_USERNAME}" \
  --adminpass="${MOODLE_PASSWORD}" \
  --adminemail="${MOODLE_EMAIL}" \
  --non-interactive

if [[ ! -f /var/www/html/config.php ]]; then
  echo "Moodle installation did not generate config.php."
  exit 1
fi

touch /var/www/moodledata/.tau_moodle_installed

echo "Moodle installation completed."
