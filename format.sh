#!/usr/bin/env sh
# Format PHP code in wp/wp-content/themes/wi using Composer (PHP-CS-Fixer) inside Docker.
# No local Composer required.
set -e
cd "$(dirname "$0")"
docker compose --profile tools run --rm composer
