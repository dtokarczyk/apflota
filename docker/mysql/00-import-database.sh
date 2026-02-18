#!/bin/bash
# Replace production URLs with localhost in dump and import into database.
# Order matters: replace full URLs first, then domain-only.
set -e

sed -e 's|https://www\.apflota\.pl|http://localhost|g' \
    -e 's|https://apflota\.pl|http://localhost|g' \
    -e 's|http://www\.apflota\.pl|http://localhost|g' \
    -e 's|http://apflota\.pl|http://localhost|g' \
    -e 's|www\.apflota\.pl|localhost|g' \
    -e 's|apflota\.pl|localhost|g' \
    /docker-entrypoint-initdb.d/dump.sql.orig \
| mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"

echo "Database imported with URL replacements applied."
