#!/usr/bin/env bash
# Prepare production dump for staging: replace apflota.pl (and www) with testy.apflota.pl (no www), using http (staging is on http).
# Run on a dump from production, then import the output on staging.
#
# Usage:
#   ./scripts/prepare-dump-for-staging.sh INPUT_DUMP OUTPUT_DUMP
# Example:
#   ./scripts/prepare-dump-for-staging.sh dump.sql dump-staging.sql

set -e

INPUT="${1:?Usage: $0 INPUT_DUMP OUTPUT_DUMP}"
OUTPUT="${2:?Usage: $0 INPUT_DUMP OUTPUT_DUMP}"

if [[ ! -f "$INPUT" ]]; then
  echo "Error: Input file not found: $INPUT" >&2
  exit 1
fi

# Order matters: full URLs first, then domain-only. Target: testy.apflota.pl (no www).
# Use placeholder so "apflota.pl" -> "testy.apflota.pl" does not match inside already-replaced "testy.apflota.pl".
PLACEHOLDER='__STAGING_DOMAIN__'
TMP=$(mktemp)
trap 'rm -f "$TMP"' EXIT
# Staging is on http, so all testy.apflota.pl URLs use http
sed -e 's|https://www\.apflota\.pl|http://testy.apflota.pl|g' \
    -e 's|https://apflota\.pl|http://testy.apflota.pl|g' \
    -e 's|http://www\.apflota\.pl|http://testy.apflota.pl|g' \
    -e 's|http://apflota\.pl|http://testy.apflota.pl|g' \
    -e 's|www\.apflota\.pl|testy.apflota.pl|g' \
    -e "s|testy\.apflota\.pl|$PLACEHOLDER|g" \
    -e 's|apflota\.pl|testy.apflota.pl|g' \
    -e "s|$PLACEHOLDER|testy.apflota.pl|g" \
    "$INPUT" > "$TMP"
mv "$TMP" "$OUTPUT"

echo "Done: $INPUT -> $OUTPUT (ready for staging testy.apflota.pl)"
