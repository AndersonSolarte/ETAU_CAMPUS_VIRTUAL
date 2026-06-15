#!/usr/bin/env bash
set -euo pipefail

TARGET_DIR="/var/www/html/theme"
SOURCE_DIR="/theme-src"

mkdir -p "${TARGET_DIR}"

for theme in tau_branding; do
  if [[ -d "${SOURCE_DIR}/${theme}" ]]; then
    rm -rf "${TARGET_DIR:?}/${theme}"
    cp -a "${SOURCE_DIR}/${theme}" "${TARGET_DIR}/${theme}"
    echo "${theme} synced."
  else
    echo "Warning: ${theme} not found in source theme directory."
  fi
done

echo "Moodle themes synced successfully."
