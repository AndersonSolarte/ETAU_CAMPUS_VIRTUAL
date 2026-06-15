#!/usr/bin/env bash
set -euo pipefail

if [[ ! -f /var/www/html/config.php ]]; then
  echo "Moodle not installed yet. Skipping demo data."
  exit 0
fi

MARKER="/var/www/moodledata/.tau_demo_seeded"

if [[ -f "${MARKER}" ]]; then
  echo "Demo data already seeded. Skipping."
  exit 0
fi

echo "Waiting for Moodle to be fully ready..."
for attempt in {1..20}; do
  if php -r "define('CLI_SCRIPT',true); require '/var/www/html/config.php'; echo 'ok';" 2>/dev/null | grep -q ok; then
    break
  fi
  echo "  Attempt ${attempt}/20..."
  sleep 5
done

echo "Running demo data seed script..."
php /scripts/seed-demo-data.php

echo "Demo data seeding complete."
