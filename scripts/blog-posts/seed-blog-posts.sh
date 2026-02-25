#!/usr/bin/env bash
#
# Seed blog categories and sample posts via WP-CLI.
# Run from repository root.
#
# Usage:
#   ./scripts/seed-blog-posts.sh           # requires wp in PATH
#   ./scripts/seed-blog-posts.sh --docker  # run WP-CLI via Docker (docker compose)
#

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
WP_PATH="$REPO_ROOT/wp"

USE_DOCKER=false
if [[ "${1:-}" == "--docker" ]] || [[ -n "${WP_CLI_DOCKER:-}" ]]; then
  USE_DOCKER=true
elif ! command -v wp &>/dev/null && command -v docker &>/dev/null; then
  # No wp in PATH but Docker available – use Docker
  USE_DOCKER=true
  echo "WP-CLI not in PATH, using Docker (profile: tools)..."
fi

if [[ "$USE_DOCKER" == true ]]; then
  CONTENT_FILE="/app/scripts/blog-sample-content.html"
  if ! command -v docker &>/dev/null; then
    echo "Error: Docker not found. Install Docker or run without --docker (wp in PATH)."
    exit 1
  fi
else
  CONTENT_FILE="$SCRIPT_DIR/blog-sample-content.html"
  if ! command -v wp &>/dev/null; then
    echo "Error: WP-CLI (wp) not found. Install it or run with: ./scripts/seed-blog-posts.sh --docker"
    exit 1
  fi
fi

if [[ "$USE_DOCKER" == false ]] && [[ ! -f "$CONTENT_FILE" ]]; then
  echo "Error: Content file not found: $CONTENT_FILE"
  exit 1
fi

if [[ ! -f "$WP_PATH/wp-config.php" ]]; then
  echo "Error: WordPress not found at $WP_PATH"
  exit 1
fi

cd "$REPO_ROOT"

if [[ "$USE_DOCKER" == true ]]; then
  # wpcli image has entrypoint "wp", so we only pass wp arguments
  WP_CMD=(docker compose --profile tools run --rm wpcli --path=/var/www/html)
else
  WP_CMD=(wp --path="$WP_PATH")
fi

echo "Creating blog categories (taxonomy: blog-category)..."

get_or_create_cat() {
  local name=$1
  local slug=$2
  local id
  id=$("${WP_CMD[@]}" term create blog-category "$name" --slug="$slug" --porcelain 2>/dev/null) || true
  if [[ -z "${id:-}" ]]; then
    id=$("${WP_CMD[@]}" term list blog-category --slug="$slug" --field=term_id --format=csv 2>/dev/null | head -n1)
  fi
  echo "$id"
}

ABC_WYNAJMU_ID=$(get_or_create_cat "ABC Wynajmu" "abc-wynajmu")
KIEROWCA_ID=$(get_or_create_cat "Kierowca" "kierowca")
AUTA_ID=$(get_or_create_cat "Auta" "auta")
PRAWO_ID=$(get_or_create_cat "Prawo" "prawo")
PRZYSZLOSC_ID=$(get_or_create_cat "Przyszłość" "przyszlosc")
BEZPIECZENSTWO_ID=$(get_or_create_cat "Bezpieczeństwo" "bezpieczenstwo")
EV_HYBRYDY_ID=$(get_or_create_cat "EV i hybrydy" "ev-i-hybrydy")

echo "Categories ready. Creating posts..."

create_posts() {
  local cat_id=$1
  shift
  local title
  for title in "$@"; do
    post_id=$("${WP_CMD[@]}" post create "$CONTENT_FILE" \
      --post_type=blog \
      --post_status=publish \
      --post_title="$title" \
      --porcelain)
    "${WP_CMD[@]}" post term add "$post_id" blog-category "$cat_id" --by=id
    # Set Yoast primary category for permalink: /blog/{category-slug}/{post-slug}/
    "${WP_CMD[@]}" post meta update "$post_id" _yoast_wpseo_primary_blog-category "$cat_id"
  done
}

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

echo "Done. Categories and 70 sample posts created."
