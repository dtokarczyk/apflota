#!/usr/bin/env sh
#
# Fetch full remote directory via rsync (pull), optional DB dump via REST, save to temp as zip.
# Uses same env as deploy (SERVER_HOST, USER, PASS, REMOTE_PATH, SFTP_PORT).
# If SITE_URL and MIGRATION_AUTH are set, downloads GET /wp-json/wi-calc/v1/backup/dump to dump.sql in backup.
#
# Usage: ./prepare-backup.sh .env.stg   (from repo root)
# Output: temp/apflota-rsync-backup-YYYYMMDD-HHMMSS.zip
#
set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"
ENV_NAME="${1:?Usage: prepare-backup.sh <env> (e.g. .env.stg)}"
ENV_FILE="${REPO_ROOT}/envs/${ENV_NAME}"

if [ ! -f "$ENV_FILE" ]; then
  echo "Error: $ENV_FILE not found."
  exit 1
fi

set -a
# shellcheck source=/dev/null
. "$ENV_FILE"
set +a

USER="$(printf '%s' "$USER" | tr -d '\r' | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')"
PASS="$(printf '%s' "$PASS" | tr -d '\r' | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')"

if [ -z "${SERVER_HOST}" ] && [ -n "${SERWER_HOST+x}" ]; then
  SERVER_HOST="$SERWER_HOST"
fi
if [ -n "${FTP_PATH+x}" ] && [ -n "$FTP_PATH" ]; then
  REMOTE_PATH="$FTP_PATH"
elif [ -n "${REMOTE_PATH+x}" ] && [ -n "$REMOTE_PATH" ]; then
  REMOTE_PATH="$REMOTE_PATH"
else
  echo "Error: set REMOTE_PATH or FTP_PATH in $ENV_FILE"
  exit 1
fi

for var in SERVER_HOST USER PASS; do
  eval "val=\$$var"
  if [ -z "$val" ]; then
    echo "Error: $var is required in $ENV_FILE"
    exit 1
  fi
done

if ! command -v rsync >/dev/null 2>&1; then
  echo "Error: rsync is required."
  exit 1
fi
if ! command -v sshpass >/dev/null 2>&1; then
  echo "Error: sshpass is required. Install with: brew install hudochenkov/sshpass/sshpass"
  exit 1
fi

REMOTE_PATH="${REMOTE_PATH%/}"
SSH_PORT="${SFTP_PORT:-22222}"
# Keepalive prevents SSH from hanging when transfer is slow or many files
SSH_OPTS="-o StrictHostKeyChecking=accept-new -o ConnectTimeout=10 -o ServerAliveInterval=30 -o ServerAliveCountMax=5"
TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
TEMP_DIR="${REPO_ROOT}/temp/rsync-backup-${TIMESTAMP}"
ZIP_NAME="apflota-rsync-backup-${TIMESTAMP}.zip"
ZIP_PATH="${REPO_ROOT}/temp/${ZIP_NAME}"

mkdir -p "$TEMP_DIR"
mkdir -p "${REPO_ROOT}/temp"

echo "=== Fetching full directory via rsync: $USER@$SERVER_HOST:$REMOTE_PATH/ -> $TEMP_DIR/ ==="
# No -z (compression) to avoid CPU hang on slow links. --contimeout/--timeout to avoid infinite hang.
# Exclude heavy dirs to reduce file count (uploads/cache often cause hangs on shared hosting).
# To include uploads in backup: PREPARE_BACKUP_INCLUDE_UPLOADS=1
EXCLUDE_UPLOADS=""
if [ -z "${PREPARE_BACKUP_INCLUDE_UPLOADS}" ]; then
  EXCLUDE_UPLOADS="--exclude=wp-content/uploads"
fi
rsync -av --contimeout=30 --timeout=60 \
  --exclude=node_modules \
  --exclude=.git \
  --exclude='*.log' \
  --exclude=tmp \
  $EXCLUDE_UPLOADS \
  --exclude=wp-content/cache \
  --exclude=wp-content/upgrade \
  --exclude=wp-content/upgrade-temp-backup \
  --exclude=wp-content/ai1wm-backups \
  --exclude='*.sql.gz' \
  -e "sshpass -p \"$PASS\" ssh -p $SSH_PORT $SSH_OPTS" \
  "$USER@$SERVER_HOST:${REMOTE_PATH}/" \
  "$TEMP_DIR/"

if [ -n "${SITE_URL}" ] && [ -n "${MIGRATION_AUTH}" ]; then
  DUMP_URL="${SITE_URL%/}/wp-json/wi-calc/v1/backup/dump"
  echo "=== Fetching DB dump: $DUMP_URL -> $TEMP_DIR/dump.sql ==="
  if curl -sf -u "$MIGRATION_AUTH" "$DUMP_URL" -o "$TEMP_DIR/dump.sql"; then
    echo "Dump saved to $TEMP_DIR/dump.sql"
  else
    echo "Warning: dump download failed (endpoint or auth). Continuing without dump."
    rm -f "$TEMP_DIR/dump.sql"
  fi
else
  echo "=== Skip DB dump (set SITE_URL and MIGRATION_AUTH in env to include dump) ==="
fi

echo "=== Creating zip: $ZIP_PATH ==="
(cd "${REPO_ROOT}/temp" && zip -r "$ZIP_NAME" "rsync-backup-${TIMESTAMP}" -q)
echo "Done. Backup zip: $ZIP_PATH"
echo "To remove unpacked copy: rm -rf $TEMP_DIR"
