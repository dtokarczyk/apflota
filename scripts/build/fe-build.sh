#!/usr/bin/env sh
# One-off build: admin-calc (React) + Sass for theme wi.
# Usage: ./build.sh  (requires: cd wp/wp-content/themes/wi && npm install)
set -e
cd "$(dirname "$0")/../.."
THEME="wp/wp-content/themes/wi"
echo "Building admin-calc..."
(cd "$THEME" && npm run build:admin)
echo "Building main JS..."
(cd "$THEME" && npm run build:main)
echo "Building Sass..."
(cd "$THEME" && npm run sass:build)
echo "Done."
