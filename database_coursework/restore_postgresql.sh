#!/usr/bin/env bash
set -euo pipefail

DB_NAME="ManufacturingDB"
BACKUP_FILE="${1:-${DB_NAME}_backup_$(date +%Y%m%d).sql}"

if [ ! -f "${BACKUP_FILE}" ]; then
    echo "Backup file not found: ${BACKUP_FILE}"
    exit 1
fi

psql -d postgres -c "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = '${DB_NAME}';"
dropdb --if-exists "${DB_NAME}"
createdb "${DB_NAME}"
psql -d "${DB_NAME}" -f "${BACKUP_FILE}"

echo "Database restored from: ${BACKUP_FILE}"
