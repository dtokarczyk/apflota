#!/usr/bin/env bash
#
# Seed blog categories and sample posts via WordPress REST API (remote).
# Targets http://testy.apflota.pl (or URL from args). Uses login + password (Application Password recommended).
#
# Usage:
#   ./scripts/seed-blog-posts-rest.sh LOGIN PASSWORD
#   ./scripts/seed-blog-posts-rest.sh LOGIN PASSWORD https://testy.apflota.pl
#
# Requires: curl, jq
#

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CONTENT_FILE="$SCRIPT_DIR/blog-sample-content.html"

if [[ $# -lt 2 ]]; then
  echo "Usage: $0 LOGIN PASSWORD [BASE_URL]"
  echo "  LOGIN    – WordPress username"
  echo "  PASSWORD – Application Password (or user password; Application Password recommended)"
  echo "  BASE_URL – optional, default: http://testy.apflota.pl"
  exit 1
fi

LOGIN="$1"
PASSWORD="$2"
BASE_URL="${3:-http://testy.apflota.pl}"
BASE_URL="${BASE_URL%/}"

for cmd in curl jq; do
  if ! command -v "$cmd" &>/dev/null; then
    echo "Error: $cmd is required. Install it and run again."
    exit 1
  fi
done

if [[ ! -f "$CONTENT_FILE" ]]; then
  echo "Error: Content file not found: $CONTENT_FILE"
  exit 1
fi

# Read sample content once and escape for JSON
SAMPLE_CONTENT=$(jq -Rs . < "$CONTENT_FILE")

# Basic Auth header
AUTH_HEADER="Authorization: Basic $(echo -n "${LOGIN}:${PASSWORD}" | base64)"

# REST base; adjust POSTS_Endpoint/TERMS_Endpoint if your CPT/taxonomy use different rest_base
API="${BASE_URL}/wp-json/wp/v2"
POSTS_Endpoint="${API}/blog"
TERMS_Endpoint="${API}/blog-category"

# Fail on HTTP 4xx/5xx
CURL_OPTS=(-s -S -f -H "$AUTH_HEADER")

# Preflight: check auth
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" -H "$AUTH_HEADER" "${API}/users/me")
if [[ "$HTTP_CODE" == "401" ]]; then
  echo "Error: 401 Unauthorized. Check:"
  echo "  - Application Password from WP Admin → Users → Profile → Application Passwords (not your normal password)"
  echo "  - Username is correct (login, not necessarily email)"
  echo "  - On HTTP-only sites WordPress disables Application Passwords by default; add to theme/plugin: add_filter('wp_is_application_passwords_available', '__return_true');"
  exit 1
fi

# Detect if taxonomy endpoint exists (blog-category must have show_in_rest on the site)
TAXONOMY_AVAILABLE=false
if curl -s -S -o /dev/null -w "%{http_code}" -H "$AUTH_HEADER" "${TERMS_Endpoint}?per_page=1" | grep -q '^200'; then
  TAXONOMY_AVAILABLE=true
fi

if [[ "$TAXONOMY_AVAILABLE" != "true" ]]; then
  echo "Note: Endpoint ${TERMS_Endpoint} not available (404)."
  echo "      Creating posts without categories. To assign categories via REST, enable show_in_rest for taxonomy 'blog-category' on the site."
  echo ""
fi

# Create term or get existing by slug; prints term id (only when TAXONOMY_AVAILABLE)
get_or_create_term() {
  local name="$1"
  local slug="$2"
  local existing
  existing=$(curl "${CURL_OPTS[@]}" "${TERMS_Endpoint}?slug=${slug}" | jq -r '.[0].id // empty')
  if [[ -n "$existing" ]]; then
    echo "$existing"
    return
  fi
  curl "${CURL_OPTS[@]}" -X POST -H "Content-Type: application/json" \
    -d "{\"name\":$(echo "$name" | jq -Rs .),\"slug\":$(echo "$slug" | jq -Rs .)}" \
    "$TERMS_Endpoint" | jq -r '.id'
}

# Create one post with given title and optional term id
# Taxonomy field: blog_category (WP often uses underscore in REST for blog-category)
create_post() {
  local title="$1"
  local term_id="${2:-}"
  local json
  if [[ -n "$term_id" ]] && [[ "$TAXONOMY_AVAILABLE" == "true" ]]; then
    json=$(jq -n \
      --arg title "$title" \
      --argjson content "$SAMPLE_CONTENT" \
      --argjson term_id "$term_id" \
      '{ title: $title, content: $content, status: "publish", blog_category: [$term_id] }')
  else
    json=$(jq -n \
      --arg title "$title" \
      --argjson content "$SAMPLE_CONTENT" \
      '{ title: $title, content: $content, status: "publish" }')
  fi
  curl "${CURL_OPTS[@]}" -X POST -H "Content-Type: application/json" -d "$json" "$POSTS_Endpoint" | jq -r '.id'
}

create_posts() {
  local cat_id="${1:-}"
  shift
  for title in "$@"; do
    create_post "$title" "$cat_id"
  done
}

echo "Using API: $API"

if [[ "$TAXONOMY_AVAILABLE" == "true" ]]; then
  echo "Creating blog categories (taxonomy: blog-category)..."
  ABC_WYNAJMU_ID=$(get_or_create_term "ABC Wynajmu" "abc-wynajmu")
  KIEROWCA_ID=$(get_or_create_term "Kierowca" "kierowca")
  AUTA_ID=$(get_or_create_term "Auta" "auta")
  PRAWO_ID=$(get_or_create_term "Prawo" "prawo")
  PRZYSZLOSC_ID=$(get_or_create_term "Przyszłość" "przyszlosc")
  BEZPIECZENSTWO_ID=$(get_or_create_term "Bezpieczeństwo" "bezpieczenstwo")
  EV_HYBRYDY_ID=$(get_or_create_term "EV i hybrydy" "ev-i-hybrydy")
  echo "Categories ready. Creating posts..."
else
  echo "Creating posts (no categories)..."
  ABC_WYNAJMU_ID=""
  KIEROWCA_ID=""
  AUTA_ID=""
  PRAWO_ID=""
  PRZYSZLOSC_ID=""
  BEZPIECZENSTWO_ID=""
  EV_HYBRYDY_ID=""
fi

# ABC Wynajmu (10)
create_posts "$ABC_WYNAJMU_ID" \
  "Jak działa wypożyczalnia samochodów – krok po kroku" \
  "Jakie dokumenty są potrzebne do wynajmu auta?" \
  "Kaucja przy wynajmie samochodu – co warto wiedzieć?" \
  "Wynajem krótkoterminowy vs długoterminowy – różnice" \
  "Limit kilometrów w wypożyczalni – jak to działa?" \
  "Odbiór i zwrot auta – na co zwrócić uwagę?" \
  "Ubezpieczenie w cenie wynajmu – co obejmuje?" \
  "Wynajem auta na lotnisku – jak się przygotować?" \
  "Dodatkowy kierowca – zasady i opłaty" \
  "Najczęstsze błędy przy wynajmie samochodu"

# Kierowca (10)
create_posts "$KIEROWCA_ID" \
  "Minimalny wiek kierowcy w wypożyczalni – zasady" \
  "Młody kierowca a wynajem auta – dodatkowe opłaty" \
  "Czy obcokrajowiec może wynająć auto w Polsce?" \
  "Prawo jazdy zagraniczne – czy wystarczy do wynajmu?" \
  "Punkty karne a możliwość wynajmu samochodu" \
  "Odpowiedzialność kierowcy za szkody – co obejmuje umowa?" \
  "Wynajem auta dla firm – kto może być kierowcą?" \
  "Czy można przekazać auto innej osobie?" \
  "Wynajem samochodu bez karty kredytowej – czy to możliwe?" \
  "Kierowca a ubezpieczenie – kiedy polisa nie zadziała?"

# Auta (10)
create_posts "$AUTA_ID" \
  "Jak wybrać odpowiednie auto z wypożyczalni?" \
  "Samochód miejski, SUV czy kombi – co wybrać?" \
  "Klasy aut w wypożyczalniach – co oznaczają segmenty?" \
  "Wynajem auta premium – czy warto dopłacić?" \
  "Samochód na wakacje – jaki model sprawdzi się najlepiej?" \
  "Auta 7- i 9-osobowe – kiedy są najlepszym wyborem?" \
  "Automatyczna czy manualna skrzynia – co wybrać?" \
  "Samochód zastępczy z OC sprawcy – jak to działa?" \
  "Najpopularniejsze modele w wypożyczalniach w 2026 roku" \
  "Wyposażenie dodatkowe – fotelik, GPS, łańcuchy śniegowe"

# Prawo (10)
create_posts "$PRAWO_ID" \
  "Umowa wynajmu samochodu – na co zwrócić uwagę?" \
  "Co grozi za spóźniony zwrot auta?" \
  "Mandat z fotoradaru w wynajętym aucie – kto płaci?" \
  "Kolizja wynajętym samochodem – procedura krok po kroku" \
  "Franszyza redukcyjna – co oznacza w praktyce?" \
  "Czy można wyjechać wynajętym autem za granicę?" \
  "Odpowiedzialność za uszkodzenia parkingowe" \
  "Tankowanie przed zwrotem – jakie są zasady?" \
  "Regulamin wypożyczalni a prawa konsumenta" \
  "Reklamacja w wypożyczalni samochodów – jak ją złożyć?"

# Przyszłość (10)
create_posts "$PRZYSZLOSC_ID" \
  "Carsharing vs klasyczna wypożyczalnia – co wygrywa?" \
  "Subskrypcja auta – nowy model wynajmu" \
  "Digitalizacja w wypożyczalniach samochodów" \
  "Odbiór auta bez kontaktu z obsługą – jak to działa?" \
  "Aplikacje mobilne w branży rent a car" \
  "Autonomiczne samochody a rynek wynajmu" \
  "Trendy w branży wypożyczalni na 2026 rok" \
  "Ekologiczne floty – kierunek rozwoju rynku" \
  "Wynajem auta na minuty – czy to przyszłość?" \
  "Sztuczna inteligencja w obsłudze klienta wypożyczalni"

# Bezpieczeństwo (10)
create_posts "$BEZPIECZENSTWO_ID" \
  "Jak sprawdzić auto przed wyjazdem?" \
  "Co zrobić w razie awarii wynajętego samochodu?" \
  "Assistance w wynajmie – co obejmuje?" \
  "Bezpieczna jazda wynajętym autem zimą" \
  "Jak uniknąć dopłat za szkody po zwrocie auta?" \
  "Oględziny auta przy odbiorze – lista kontrolna" \
  "Monitoring GPS w wynajmowanych samochodach" \
  "Ubezpieczenie pełne vs podstawowe – różnice" \
  "Kradzież wynajętego auta – co robić?" \
  "Najczęstsze przyczyny szkód w autach z wypożyczalni"

# EV i hybrydy (10)
create_posts "$EV_HYBRYDY_ID" \
  "Wynajem auta elektrycznego – co warto wiedzieć?" \
  "Jak ładować samochód elektryczny z wypożyczalni?" \
  "Zasięg EV w trasie – jak planować podróż?" \
  "Hybryda czy elektryk – co wybrać na wynajem?" \
  "Koszty ładowania auta elektrycznego" \
  "Stacje ładowania w Polsce – jak je znaleźć?" \
  "Czy wynajem EV się opłaca?" \
  "Dopłaty i ulgi dla aut elektrycznych – aktualne informacje" \
  "Najpopularniejsze modele elektryczne w wypożyczalniach" \
  "Ekologiczny wizerunek firmy dzięki flocie EV"

echo "Done. Categories and 70 sample posts created on $BASE_URL."
