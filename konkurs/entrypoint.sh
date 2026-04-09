#!/bin/sh
set -e

nginx &

cd /app
exec node dist/main.js
