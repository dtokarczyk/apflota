#!/usr/bin/env sh
# Run Sass in watch mode for theme wi (local, no Docker).
# Compiles wp/wp-content/themes/wi/scss -> wp/wp-content/themes/wi (style.css).
# Usage: ./watch.sh  (requires: npm install)
set -e
cd "$(dirname "$0")"
THEME="wp/wp-content/themes/wi"
npx sass --watch "$THEME/scss:$THEME" --style compressed --no-source-map
