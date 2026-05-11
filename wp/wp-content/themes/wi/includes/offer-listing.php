<?php

declare(strict_types=1);

/**
 * Long-term rental offer listing: page-hierarchy context and helpers.
 *
 * Pages use the "Wynajem długoterminowy" template in a parent/child hierarchy:
 *  - Level 1: base listing page (slug: wynajem-dlugoterminowy)
 *  - Level 2: brand pages  (children of base; slug = marka-auta term slug)
 *  - Level 3: model pages  (children of brand; slug = model term slug)
 *
 * Deployment (WordPress admin):
 *  - Create the base page with slug "wynajem-dlugoterminowy" and template "Wynajem długoterminowy".
 *  - Use the page generator (Settings → Generuj strony ofertowe) to create brand/model pages.
 *  - Flush permalinks once (Settings > Permalinks > Save).
 */

if (! defined('ABSPATH')) {
    return;
}

/** Public page slug for the base offer listing (must match the WordPress page slug). */
function wi_offer_page_slug(): string
{
    return 'wynajem-dlugoterminowy';
}

/** Legacy category slug that posts still belong to in the database. */
function wi_offer_legacy_category_slug(): string
{
    return 'kalkulator';
}

/**
 * Base URL for the offer listing (with trailing slash).
 */
function wi_offer_base_url(): string
{
    $page = get_page_by_path(wi_offer_page_slug());
    if ($page instanceof WP_Post) {
        return trailingslashit(get_permalink($page));
    }

    return trailingslashit(home_url('/' . wi_offer_page_slug() . '/'));
}

// ---------------------------------------------------------------------------
// 1. Listing-page detection and auto-template assignment
// ---------------------------------------------------------------------------

/**
 * Whether the current request is any page in the offer-listing hierarchy
 * (base page, brand child, or model grandchild).
 */
function wi_offer_is_listing_page(): bool
{
    if (! is_page()) {
        return false;
    }

    if (is_page_template('page-wynajem-dlugoterminowy.php')) {
        return true;
    }

    $current = get_queried_object();
    if (! ($current instanceof WP_Post)) {
        return false;
    }

    $base = get_page_by_path(wi_offer_page_slug());
    if (! ($base instanceof WP_Post)) {
        return false;
    }

    if ($current->ID === $base->ID) {
        return true;
    }

    return in_array($base->ID, get_post_ancestors($current), true);
}

/**
 * Force the listing template for any descendant of the base listing page,
 * even if the template was not explicitly set in Page Attributes.
 */
function wi_offer_template_include(string $template): string
{
    if (! wi_offer_is_listing_page()) {
        return $template;
    }

    $listing_template = get_template_directory() . '/page-wynajem-dlugoterminowy.php';
    if (file_exists($listing_template)) {
        return $listing_template;
    }

    return $template;
}

add_filter('template_include', 'wi_offer_template_include');

// ---------------------------------------------------------------------------
// 2. Page-hierarchy context: resolve brand & model from parent/child structure
// ---------------------------------------------------------------------------

/**
 * Resolve brand and model terms from the current page's position in the hierarchy.
 *
 * @return array{brand: ?WP_Term, model: ?WP_Term}
 */
function wi_offer_get_resolved_terms(): array
{
    $brand = null;
    $model = null;

    if (! is_page()) {
        return ['brand' => $brand, 'model' => $model];
    }

    $current_page = get_queried_object();
    if (! ($current_page instanceof WP_Post)) {
        return ['brand' => $brand, 'model' => $model];
    }

    $ancestors = get_post_ancestors($current_page);

    if ($ancestors === []) {
        return ['brand' => $brand, 'model' => $model];
    }

    if (count($ancestors) === 1) {
        // Level 2: current page is a brand page (child of base)
        $t = get_term_by('slug', $current_page->post_name, 'marka-auta');
        if ($t instanceof WP_Term && ! is_wp_error($t)) {
            $brand = $t;
        }
    } elseif (count($ancestors) >= 2) {
        // Level 3+: parent is the brand page, current is the model page
        $brand_page = get_post($current_page->post_parent);
        if ($brand_page instanceof WP_Post) {
            $t = get_term_by('slug', $brand_page->post_name, 'marka-auta');
            if ($t instanceof WP_Term && ! is_wp_error($t)) {
                $brand = $t;
            }
        }

        $t = get_term_by('slug', $current_page->post_name, 'model');
        if ($t instanceof WP_Term && ! is_wp_error($t)) {
            $model = $t;
        }
    }

    return ['brand' => $brand, 'model' => $model];
}

/**
 * Collect model terms that appear on at least one published post in the given brand.
 *
 * @return list<WP_Term>
 */
function wi_offer_get_models_for_brand(WP_Term $brand): array
{
    $q = new WP_Query([
        'post_type'              => 'post',
        'post_status'            => 'publish',
        'posts_per_page'         => -1,
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => true,
        'tax_query'              => [
            [
                'taxonomy' => 'marka-auta',
                'field'    => 'term_id',
                'terms'    => (int) $brand->term_id,
            ],
        ],
    ]);

    $model_ids = [];
    foreach ($q->posts as $post_id) {
        $terms = get_the_terms((int) $post_id, 'model');
        if (! is_array($terms)) {
            continue;
        }
        foreach ($terms as $term) {
            $model_ids[(int) $term->term_id] = true;
        }
    }
    if ($model_ids === []) {
        return [];
    }

    $terms = get_terms([
        'taxonomy'   => 'model',
        'include'    => array_keys($model_ids),
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    if (! is_array($terms) || is_wp_error($terms)) {
        return [];
    }

    /** @var list<WP_Term> $terms */
    return wp_list_sort($terms, 'name', 'ASC');
}

// ---------------------------------------------------------------------------
// 3. Fix post permalinks: /kalkulator/post-slug/ → /wynajem-dlugoterminowy/post-slug/
// ---------------------------------------------------------------------------

/**
 * Rewrite permalink for posts in the legacy "kalkulator" category so that
 * get_permalink() returns /wynajem-dlugoterminowy/post-slug/ instead.
 */
function wi_offer_rewrite_post_permalink(string $permalink, WP_Post $post): string
{
    if ($post->post_type !== 'post') {
        return $permalink;
    }

    if (! has_category(wi_offer_legacy_category_slug(), $post)) {
        return $permalink;
    }

    $legacy = '/' . wi_offer_legacy_category_slug() . '/';
    $new    = '/' . wi_offer_page_slug() . '/';

    $path = (string) (wp_parse_url($permalink, PHP_URL_PATH) ?? '');
    if (!str_contains($path, $legacy)) {
        return $permalink;
    }

    return str_replace($legacy, $new, $permalink);
}

add_filter('post_link', 'wi_offer_rewrite_post_permalink', 10, 2);

// ---------------------------------------------------------------------------
// 4. Resolve /wynajem-dlugoterminowy/post-slug/ to the actual post
// ---------------------------------------------------------------------------

/**
 * When WP parsed /{page-slug}/{something}/ as category+post:
 *  - if the post exists in the legacy "kalkulator" category → resolve it
 *
 * @param array<string, string> $query_vars
 * @return array<string, string>
 */
function wi_offer_request_filter(array $query_vars): array
{
    $page_slug = wi_offer_page_slug();
    if (empty($query_vars['category_name']) || $query_vars['category_name'] !== $page_slug) {
        return $query_vars;
    }
    if (empty($query_vars['name'])) {
        return $query_vars;
    }

    $candidate = (string) $query_vars['name'];
    if ($candidate === '' || $candidate === 'page') {
        return $query_vars;
    }

    $in_new = get_posts([
        'name'                   => $candidate,
        'post_type'              => 'post',
        'post_status'            => 'publish',
        'posts_per_page'         => 1,
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'tax_query'              => [
            [
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $page_slug,
            ],
        ],
    ]);
    if ($in_new !== []) {
        return $query_vars;
    }

    $in_legacy = get_posts([
        'name'                   => $candidate,
        'post_type'              => 'post',
        'post_status'            => 'publish',
        'posts_per_page'         => 1,
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'tax_query'              => [
            [
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => wi_offer_legacy_category_slug(),
            ],
        ],
    ]);
    if ($in_legacy !== []) {
        $query_vars['category_name'] = wi_offer_legacy_category_slug();
        return $query_vars;
    }

    return $query_vars;
}

add_filter('request', 'wi_offer_request_filter');

// ---------------------------------------------------------------------------
// 5. template_redirect hooks
// ---------------------------------------------------------------------------

/**
 * Strip legacy ?mark= from offer URLs (301 to same path without it).
 */
function wi_offer_redirect_strip_legacy_mark_get(): void
{
    if (is_admin() || ! isset($_GET['mark'])) {
        return;
    }
    if (! wi_offer_is_listing_page()) {
        return;
    }

    $url = remove_query_arg('mark');
    wp_safe_redirect($url, 301);
    exit;
}

add_action('template_redirect', 'wi_offer_redirect_strip_legacy_mark_get', 1);

// ---------------------------------------------------------------------------
// 6. Rewrite-rule cleanup (flush stale rules from previous dynamic routing)
// ---------------------------------------------------------------------------

function wi_offer_maybe_flush_rewrite_rules(): void
{
    $version = 3;
    if ((int) get_option('wi_offer_rewrite_flushed') === $version) {
        return;
    }

    flush_rewrite_rules();
    update_option('wi_offer_rewrite_flushed', $version);
}

add_action('init', 'wi_offer_maybe_flush_rewrite_rules', 99);

// ---------------------------------------------------------------------------
// 7. Legacy redirects
// ---------------------------------------------------------------------------

/**
 * Redirect category archive (same slug as listing) to the listing page.
 */
function wi_offer_redirect_category_archive(): void
{
    $page_slug = wi_offer_page_slug();
    if (! is_category($page_slug) && ! is_category(wi_offer_legacy_category_slug())) {
        return;
    }

    $page = get_page_by_path($page_slug);
    if (! ($page instanceof WP_Post)) {
        return;
    }

    wp_safe_redirect(get_permalink($page), 301);
    exit;
}

add_action('template_redirect', 'wi_offer_redirect_category_archive', 4);

/**
 * 301 from legacy /kalkulator/... URLs to /wynajem-dlugoterminowy/...
 */
function wi_offer_redirect_legacy_kalkulator_urls(): void
{
    if (is_admin()) {
        return;
    }

    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $path = parse_url($uri, PHP_URL_PATH);
    if (! is_string($path) || $path === '') {
        return;
    }

    $legacy = '/' . wi_offer_legacy_category_slug() . '/';
    $normalized = '/' . trim($path, '/') . '/';

    if (!str_contains($normalized, $legacy)) {
        return;
    }

    $new_path = str_replace($legacy, '/' . wi_offer_page_slug() . '/', $normalized, $count);
    if ($count === 0) {
        return;
    }

    $new_path = '/' . trim($new_path, '/');
    $qs = (string) (parse_url($uri, PHP_URL_QUERY) ?? '');
    $target = home_url(user_trailingslashit($new_path));
    if ($qs !== '') {
        $target .= '?' . $qs;
    }

    wp_safe_redirect($target, 301);
    exit;
}

add_action('template_redirect', 'wi_offer_redirect_legacy_kalkulator_urls', 3);

/**
 * Prevent canonical redirect from sending /wynajem-dlugoterminowy/post-slug/
 * back to /kalkulator/post-slug/ (which would create a redirect loop).
 *
 * @param string|false $redirect_url
 * @return string|false
 */
function wi_offer_block_canonical_to_legacy($redirect_url, string $requested_url)
{
    if (! is_string($redirect_url)) {
        return $redirect_url;
    }

    $target_path = (string) (wp_parse_url($redirect_url, PHP_URL_PATH) ?? '');
    $legacy = '/' . wi_offer_legacy_category_slug() . '/';

    if (str_contains($target_path, $legacy)) {
        $request_path = (string) (wp_parse_url($requested_url, PHP_URL_PATH) ?? '');
        $expected = str_replace($legacy, '/' . wi_offer_page_slug() . '/', $target_path);
        if (rtrim($request_path, '/') === rtrim($expected, '/')) {
            return false;
        }
    }

    return $redirect_url;
}

add_filter('redirect_canonical', 'wi_offer_block_canonical_to_legacy', 10, 2);

// ---------------------------------------------------------------------------
// 8. Page generator (admin UI)
// ---------------------------------------------------------------------------

/**
 * Render the "Generate offer pages" section (embedded in the Migracje admin page).
 */
function wi_offer_render_generate_pages_section(): void
{
    if (! current_user_can('manage_options')) {
        return;
    }

    $created = 0;
    $skipped = 0;
    $errors  = [];
    $ran     = false;

    if (isset($_POST['wi_offer_generate']) && check_admin_referer('wi_offer_generate_pages')) {
        $ran = true;
        $base_page = get_page_by_path(wi_offer_page_slug());
        if (! ($base_page instanceof WP_Post)) {
            $errors[] = sprintf(
                'Base page with slug "%s" not found. Create it first.',
                wi_offer_page_slug()
            );
        } else {
            $brands = get_terms([
                'taxonomy'   => 'marka-auta',
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC',
            ]);
            if (! is_array($brands) || is_wp_error($brands)) {
                $brands = [];
            }

            foreach ($brands as $brand) {
                $brand_result = wi_offer_ensure_page(
                    $brand->name,
                    $brand->slug,
                    $base_page->ID
                );
                if ($brand_result === true) {
                    $created++;
                } else {
                    $skipped++;
                }

                $brand_page = get_page_by_path(
                    wi_offer_page_slug() . '/' . $brand->slug
                );
                if (! ($brand_page instanceof WP_Post)) {
                    continue;
                }

                $models = wi_offer_get_models_for_brand($brand);
                foreach ($models as $model_term) {
                    $model_result = wi_offer_ensure_page(
                        $model_term->name,
                        $model_term->slug,
                        $brand_page->ID
                    );
                    if ($model_result === true) {
                        $created++;
                    } else {
                        $skipped++;
                    }
                }
            }
        }
    }

    echo '<hr style="margin:32px 0 16px">';
    echo '<h2>' . esc_html__('Generuj strony ofertowe', 'wi') . '</h2>';

    if ($ran && $errors === []) {
        echo '<div class="notice notice-success inline"><p>';
        echo sprintf(
            esc_html__('Done. Created: %d, skipped (already exist): %d.', 'wi'),
            $created,
            $skipped
        );
        echo '</p></div>';
    }

    foreach ($errors as $err) {
        echo '<div class="notice notice-error inline"><p>' . esc_html($err) . '</p></div>';
    }

    echo '<p>' . esc_html__(
        'Generates pages for all marka-auta and model terms in the listing hierarchy. Existing pages are skipped.',
        'wi'
    ) . '</p>';
    echo '<form method="post">';
    wp_nonce_field('wi_offer_generate_pages');
    echo '<input type="hidden" name="wi_offer_generate" value="1">';
    submit_button(__('Generuj strony', 'wi'));
    echo '</form>';
}

/**
 * Create a page with the given title/slug under a parent, if it doesn't exist yet.
 *
 * @return bool True if created, false if already exists.
 */
function wi_offer_ensure_page(string $title, string $slug, int $parent_id): bool
{
    $existing = get_posts([
        'post_type'   => 'page',
        'post_parent' => $parent_id,
        'name'        => $slug,
        'post_status' => ['publish', 'draft', 'private', 'trash', 'pending'],
        'numberposts' => 1,
        'fields'      => 'ids',
    ]);

    if ($existing !== []) {
        $existing_id = (int) $existing[0];
        if (get_post_meta($existing_id, '_wp_page_template', true) !== 'page-wynajem-dlugoterminowy.php') {
            update_post_meta($existing_id, '_wp_page_template', 'page-wynajem-dlugoterminowy.php');
        }
        return false;
    }

    $page_id = wp_insert_post([
        'post_title'  => $title,
        'post_name'   => $slug,
        'post_parent' => $parent_id,
        'post_type'   => 'page',
        'post_status' => 'publish',
    ], true);

    if (is_wp_error($page_id)) {
        return false;
    }

    update_post_meta($page_id, '_wp_page_template', 'page-wynajem-dlugoterminowy.php');

    return true;
}
