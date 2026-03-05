#!/usr/bin/env sh
# One-off build: admin-calc (React) + main JS + Sass for theme wi.
# Usage: ./fe-build.sh [prod]
#   prod = minified CSS (compressed), NODE_ENV=production for wp-scripts.
#   (requires: cd wp/wp-content/themes/wi && npm install)
set -e
cd "$(dirname "$0")/../.."
THEME="wp/wp-content/themes/wi"
PROD="${1:-}"

if [ "$PROD" = "prod" ]; then
  export NODE_ENV=production
  SASS_SCRIPT="sass:build:prod"
  echo "Building (production, minified)..."
else
  SASS_SCRIPT="sass:build"
  echo "Building (dev)..."
fi

echo "Building admin-calc..."
(cd "$THEME" && npm run build:admin)
echo "Building main JS..."
(cd "$THEME" && npm run build:main)
echo "Building Sass..."
(cd "$THEME" && npm run $SASS_SCRIPT)
echo "Done."
