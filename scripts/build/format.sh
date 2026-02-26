#!/usr/bin/env sh
# Format PHP code in wp/wp-content/themes/wi using Composer (PHP-CS-Fixer) inside Docker.
# No local Composer required.
set -e
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"
cd "$REPO_ROOT"
docker compose --profile tools run --rm composer
