#!/usr/bin/env sh
# Install Composer dependencies in wp/wp-content/themes/wi using Docker.
# No local Composer required.
set -e
cd "$(dirname "$0")"
docker compose --profile tools run --rm composer install --no-interaction
