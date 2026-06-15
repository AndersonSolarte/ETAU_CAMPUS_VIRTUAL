#!/usr/bin/env bash
set -euo pipefail

SRC_LOCAL="/plugins-src/local"
SRC_BLOCKS="/plugins-src/blocks"
SRC_ENROL="/plugins-src/enrol"
DST_LOCAL="/var/www/html/local"
DST_BLOCKS="/var/www/html/blocks"
DST_ENROL="/var/www/html/enrol"

mkdir -p "${DST_LOCAL}" "${DST_BLOCKS}"

TAU_LOCAL_PLUGINS=(
  tau_ai_provider
  tau_course_creator_ai
  tau_assign_ai
  tau_forum_ai
  tau_student_life_story
  tau_smart_rules_ai
  tau_ranking_activities_ai
  tau_share_certificate_ai
  tau_soporte
  tau_useradmin
  tau_certificate
)

TAU_ENROL_PLUGINS=(
  cesmag
)

TAU_BLOCK_PLUGINS=(
  tau_tutor_ai
  tau_recommended_courses
)

echo "Syncing TAU local plugins..."
for plugin in "${TAU_LOCAL_PLUGINS[@]}"; do
  if [[ ! -d "${SRC_LOCAL}/${plugin}" ]]; then
    echo "  SKIP: ${plugin} not found in source."
    continue
  fi
  rm -rf "${DST_LOCAL:?}/${plugin}"
  cp -a "${SRC_LOCAL}/${plugin}" "${DST_LOCAL}/${plugin}"
  echo "  OK: local/${plugin}"
done

echo "Syncing TAU block plugins..."
for plugin in "${TAU_BLOCK_PLUGINS[@]}"; do
  if [[ ! -d "${SRC_BLOCKS}/${plugin}" ]]; then
    echo "  SKIP: ${plugin} not found in source."
    continue
  fi
  rm -rf "${DST_BLOCKS:?}/${plugin}"
  cp -a "${SRC_BLOCKS}/${plugin}" "${DST_BLOCKS}/${plugin}"
  echo "  OK: blocks/${plugin}"
done

echo "Syncing TAU enrol plugins..."
for plugin in "${TAU_ENROL_PLUGINS[@]}"; do
  if [[ ! -d "${SRC_ENROL}/${plugin}" ]]; then
    echo "  SKIP: enrol/${plugin} not found in source."
    continue
  fi
  rm -rf "${DST_ENROL:?}/${plugin}"
  cp -a "${SRC_ENROL}/${plugin}" "${DST_ENROL}/${plugin}"
  echo "  OK: enrol/${plugin}"
done

echo "TAU plugins synced successfully."
