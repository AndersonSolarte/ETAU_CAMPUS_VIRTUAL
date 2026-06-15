#!/bin/sh
set -eu

MOOVE_REPO="https://github.com/willianmano/moodle-theme_moove.git"
MOOVE_BRANCH="${MOOVE_BRANCH:-MOODLE_500_STABLE}"
TARGET_THEME="${MOODLE_DEFAULT_THEME:-boost_union}"
TARGET="/var/www/html/theme/moove"

if [ "${TARGET_THEME}" != "moove" ]; then
  echo "Skipping Moove fetch because target theme is ${TARGET_THEME}."
  exit 0
fi

if [ -f "${TARGET}/version.php" ]; then
  echo "Moove theme already present in volume."
  exit 0
fi

echo "Cloning Moove theme (branch: ${MOOVE_BRANCH})..."
rm -rf "${TARGET}"
git clone --depth 1 --branch "${MOOVE_BRANCH}" "${MOOVE_REPO}" "${TARGET}"

echo "Moove theme cloned successfully."
