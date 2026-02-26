#!/usr/bin/env sh
#
# Deploy wp/ to target: load env from envs/<env>, build, patch wp-config (DB_*),
# upload via rsync over SSH (password via sshpass), then trigger migrations endpoint.
#
# Required in env file (e.g. envs/.env.stg):
#   SERVER_HOST (or SERWER_HOST), USER, PASS
#   REMOTE_PATH or FTP_PATH (path on server where theme is uploaded)
#   Optional: SFTP_PORT (default 22; use 22222 if your host uses custom SSH port)
# Requires: rsync, sshpass (brew install hudochenkov/sshpass/sshpass)
#   DB_NAME, DB_USER, DB_PASSWORD (for wp-config.php on server)
#   SITE_URL (base URL of target site, for migration endpoint)
# Optional: MIGRATION_AUTH="admin_user:application_password" for Basic auth on migrations.
#
# Usage: ./deploy .env.stg   (from repo root; env file is envs/.env.stg)
#
set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"
ENV_NAME="${1:?Usage: deploy.sh <env> (e.g. .env.stg)}"
ENV_FILE="${REPO_ROOT}/envs/${ENV_NAME}"
WP_DIR="${REPO_ROOT}/wp"

# Resolve REMOTE_PATH from .env
export REMOTE_PATH=""

if [ ! -f "$ENV_FILE" ]; then
  echo "Error: $ENV_FILE not found. Copy envs/.env.stg.example to envs/$ENV_NAME and fill in values."
  exit 1
fi

# Load env (simple key=value, no spaces around =)
set -a
# shellcheck source=/dev/null
. "$ENV_FILE"
set +a

# Trim USER and PASS (strip CR and leading/trailing spaces – .env from Windows or copy-paste)
USER="$(printf '%s' "$USER" | tr -d '\r' | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')"
PASS="$(printf '%s' "$PASS" | tr -d '\r' | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')"

# SERVER_HOST: allow SERWER_HOST (Polish) or SERVER_HOST
if [ -z "${SERVER_HOST}" ] && [ -n "${SERWER_HOST+x}" ]; then
  SERVER_HOST="$SERWER_HOST"
fi
# REMOTE_PATH: use FTP_PATH or REMOTE_PATH in .env (do not use PATH – it would override system PATH)
if [ -n "${FTP_PATH+x}" ] && [ -n "$FTP_PATH" ]; then
  REMOTE_PATH="$FTP_PATH"
elif [ -n "${REMOTE_PATH+x}" ] && [ -n "$REMOTE_PATH" ]; then
  REMOTE_PATH="$REMOTE_PATH"
else
  echo "Error: set REMOTE_PATH or FTP_PATH in $ENV_FILE (server path where wp/ should be uploaded)."
  exit 1
fi

for var in SERVER_HOST USER PASS DB_NAME DB_USER DB_PASSWORD SITE_URL; do
  eval "val=\$$var"
  if [ -z "$val" ]; then
    echo "Error: $var is required in $ENV_FILE"
    exit 1
  fi
done

echo "=== 1/5 Pre-deploy: format, composer install, frontend build ==="
cd "$REPO_ROOT"
BUILD_DIR="${REPO_ROOT}/scripts/build"
"${BUILD_DIR}/format.sh"
"${BUILD_DIR}/composer-install.sh"
"${BUILD_DIR}/fe-build.sh"
echo "Pre-deploy build done."

THEME_DIR="${WP_DIR}/wp-content/themes/wi"

echo "=== 2/5 Patch wp-config.php (DB_* from .env) ==="
WP_CONFIG="${WP_DIR}/wp-config.php"
WP_CONFIG_BAK="${WP_DIR}/wp-config.php.bak.deploy"
if [ ! -f "$WP_CONFIG" ]; then
  echo "Error: $WP_CONFIG not found."
  exit 1
fi
cp "$WP_CONFIG" "$WP_CONFIG_BAK"
# Replace define('DB_NAME', '...'); and same for DB_USER, DB_PASSWORD (single-quoted values)
# Use # as sed delimiter so values containing / are safe. macOS sed: -i ''; GNU: -i.
for def in "DB_NAME:${DB_NAME}" "DB_USER:${DB_USER}" "DB_PASSWORD:${DB_PASSWORD}"; do
  key="${def%%:*}"
  val="${def#*:}"
  val_escaped="$(echo "$val" | sed "s/'/'\\\\''/g")"
  if sed --version >/dev/null 2>&1; then
    sed -i "s#define('$key', *'[^']*');#define('$key', '$val_escaped');#" "$WP_CONFIG"
  else
    sed -i '' "s#define('$key', *'[^']*');#define('$key', '$val_escaped');#" "$WP_CONFIG"
  fi
done

echo "=== 3/5 Upload wp-content/themes/wi via rsync (SSH + password) ==="
if [ ! -d "$THEME_DIR" ]; then
  echo "Error: Theme dir $THEME_DIR not found."
  exit 1
fi
if ! command -v rsync >/dev/null 2>&1; then
  echo "Error: rsync is required (usually preinstalled on macOS)."
  exit 1
fi
if ! command -v sshpass >/dev/null 2>&1; then
  echo "Error: sshpass is required to pass SSH password. Install with: brew install hudochenkov/sshpass/sshpass"
  exit 1
fi
REMOTE_PATH="${REMOTE_PATH%/}"
REMOTE_THEME_DIR="${REMOTE_PATH}/wp-content/themes/wi"
SSH_PORT="${SFTP_PORT:-22222}"
SSH_OPTS="-o StrictHostKeyChecking=accept-new -o ConnectTimeout=10"
# On shared hosting use REMOTE_PATH relative to home (e.g. public_html/testy), not /public_html/...
echo "Creating remote dir (if needed)..."
sshpass -p "$PASS" ssh -p "$SSH_PORT" $SSH_OPTS "$USER@$SERVER_HOST" "mkdir -p $REMOTE_THEME_DIR"
echo "Syncing theme..."
rsync -avz --delete --exclude='node_modules' -e "sshpass -p \"$PASS\" ssh -p $SSH_PORT $SSH_OPTS" "$THEME_DIR/" "$USER@$SERVER_HOST:$REMOTE_THEME_DIR/"
echo "Upload done."

echo "=== 4/5 Restore wp-config.php ==="
mv "$WP_CONFIG_BAK" "$WP_CONFIG"
echo "Restored local wp-config.php."

echo "=== 5/5 Run migrations endpoint ==="
MIGRATION_URL="${SITE_URL%/}/wp-json/wi-calc/v1/migrations/run"
if [ -n "${MIGRATION_AUTH+x}" ] && [ -n "$MIGRATION_AUTH" ]; then
  if curl -sf -X POST -u "$MIGRATION_AUTH" "$MIGRATION_URL" -H "Content-Type: application/json" -d "{}"; then
    echo ""
    echo "Migrations triggered successfully."
  else
    echo "Warning: Migration request failed. Check $MIGRATION_URL and MIGRATION_AUTH (e.g. admin_user:application_password)."
  fi
else
  if curl -sf -X POST "$MIGRATION_URL" -H "Content-Type: application/json" -d "{}"; then
    echo ""
    echo "Migrations triggered successfully."
  else
    echo "Warning: Migration request failed. Endpoint may require auth – set MIGRATION_AUTH=user:application_password in env."
  fi
fi

echo "=== Deploy finished ==="
