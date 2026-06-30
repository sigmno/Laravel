#!/usr/bin/env bash
set -euo pipefail

DB_NAME="ManufacturingDB"
BACKUP_DATE="$(date +%Y%m%d)"
BACKUP_FILE="${DB_NAME}_backup_${BACKUP_DATE}.sql"

pg_dump -d "${DB_NAME}" -F p -f "${BACKUP_FILE}"

echo "Backup created: ${BACKUP_FILE}"
