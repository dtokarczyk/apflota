#!/usr/bin/env sh
# Install Composer dependencies in wp/wp-content/themes/wi using Docker.
# No local Composer required.
set -e
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"
cd "$REPO_ROOT"
docker compose --profile tools run --rm composer install --no-interaction
