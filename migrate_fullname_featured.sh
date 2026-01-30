#!/usr/bin/env bash
# Safe migration script: add `full_name` to users and `featured` to artists if missing
# Usage: ./migrate_fullname_featured.sh -u svibe_admin -d soundvibe -h localhost

set -euo pipefail
DB_USER="svibe_admin"
DB_NAME="soundvibe"
DB_HOST="localhost"
MYSQL_CMD="mysql"

while getopts "u:d:h:" opt; do
  case $opt in
    u) DB_USER="$OPTARG" ;;
    d) DB_NAME="$OPTARG" ;;
    h) DB_HOST="$OPTARG" ;;
    *) echo "Usage: $0 [-u db_user] [-d db_name] [-h db_host]"; exit 1 ;;
  esac
done

read -s -p "MySQL password for ${DB_USER}: " DB_PASS
echo

MYSQL_OPTS=( -u"${DB_USER}" -p"${DB_PASS}" -h"${DB_HOST}" -D"${DB_NAME}" -N -s -e )

echo "Checking for column 'full_name' in users..."
FULL_EXISTS=$("${MYSQL_CMD}" "${MYSQL_OPTS[@]}" "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE table_schema='${DB_NAME}' AND table_name='users' AND column_name='full_name';")
if [ "${FULL_EXISTS}" = "0" ]; then
  echo "Adding column full_name to users..."
  "${MYSQL_CMD}" "${MYSQL_OPTS[@]}" "ALTER TABLE users ADD COLUMN full_name VARCHAR(150) NULL AFTER last_name;"
  echo "Populating full_name from first_name/last_name..."
  "${MYSQL_CMD}" "${MYSQL_OPTS[@]}" "UPDATE users SET full_name = TRIM(CONCAT_WS(' ', NULLIF(first_name,''), NULLIF(last_name,''))) WHERE full_name IS NULL OR full_name = '';"
else
  echo "Column full_name already exists (skipping)."
fi

echo "Checking for column 'featured' in artists..."
FEAT_EXISTS=$("${MYSQL_CMD}" "${MYSQL_OPTS[@]}" "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE table_schema='${DB_NAME}' AND table_name='artists' AND column_name='featured';")
if [ "${FEAT_EXISTS}" = "0" ]; then
  echo "Adding column featured to artists..."
  "${MYSQL_CMD}" "${MYSQL_OPTS[@]}" "ALTER TABLE artists ADD COLUMN featured TINYINT(1) DEFAULT 0 AFTER is_featured;"
  echo "Populating featured from is_featured..."
  "${MYSQL_CMD}" "${MYSQL_OPTS[@]}" "UPDATE artists SET featured = is_featured WHERE (featured IS NULL OR featured = 0) AND (is_featured IS NOT NULL AND is_featured != 0);"
else
  echo "Column featured already exists (skipping)."
fi

echo "Migration finished."
exit 0
