#!/usr/bin/env sh
# Watch both: Sass + admin-calc. Rebuilds on file changes.
# Usage: ./watch.sh  (requires: cd wp/wp-content/themes/wi && npm install)
# Ctrl+C stops both processes.
set -e
cd "$(dirname "$0")"
THEME="wp/wp-content/themes/wi"
SASS_PID=""
cleanup() {
  [ -n "$SASS_PID" ] && kill "$SASS_PID" 2>/dev/null || true
  exit 0
}
trap cleanup INT TERM
echo "Starting Sass watch..."
(cd "$THEME" && npm run sass:watch) &
SASS_PID=$!
echo "Starting admin-calc watch..."
(cd "$THEME" && npm run start:admin)
cleanup
