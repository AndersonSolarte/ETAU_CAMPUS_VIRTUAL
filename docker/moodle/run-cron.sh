#!/usr/bin/env bash
set -euo pipefail

while true; do
  php /var/www/html/admin/cli/cron.php
  sleep 60
done
