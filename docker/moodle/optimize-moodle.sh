#!/usr/bin/env bash
set -euo pipefail

php /scripts/optimize-moodle.php
php /var/www/html/admin/cli/purge_caches.php
