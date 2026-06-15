#!/bin/sh
set -eu

if [ -f /var/www/html/version.php ]; then
  echo "Moodle code already present in persistent volume."
  exit 0
fi

rm -rf /tmp/moodle-src
git clone --depth 1 --branch "${MOODLE_GIT_BRANCH}" "${MOODLE_GIT_REPOSITORY}" /tmp/moodle-src
cp -a /tmp/moodle-src/. /var/www/html/
rm -rf /tmp/moodle-src

echo "Moodle code bootstrapped successfully."

