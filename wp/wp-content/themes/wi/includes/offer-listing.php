<?php

declare(strict_types=1);

/**
 * Long-term rental offer listing: URL context (marka / model path segments) and helpers.
 *
 * Deployment (WordPress admin, after theme deploy):
 * - Create or set the listing page slug to "wynajem-dlugoterminowy" and assign template "Wynajem długoterminowy".
 * - Flush permalinks once (Settings > Permalinks > Save) if rewrite rules do not apply automatically.
 */

if (! defined('ABSPATH')) {
    return;
}

/** Public page slug for the offer listing (must match the WordPress page slug). */
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
// 1. Fix post permalinks: /kalkulator/post-slug/ → /wynajem-dlugoterminowy/post-slug/
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
// 2. Resolve /wynajem-dlugoterminowy/post-slug/ to the actual post
// ---------------------------------------------------------------------------

/**
 * Resolve marka-auta / model terms from current main query vars.
 *
 * @return array{brand: ?WP_Term, model: ?WP_Term}
 */
function wi_offer_get_resolved_terms(): array
{
    $brand = null;
    $model = null;
    $marka_slug = (string) get_query_var('wi_calc_marka');
    $model_slug = (string) get_query_var('wi_calc_model');

    if ($marka_slug !== '') {
        $t = get_term_by('slug', $marka_slug, 'marka-auta');
        if ($t instanceof WP_Term && ! is_wp_error($t)) {
            $brand = $t;
        }
    }
    if ($model_slug !== '' && $brand instanceof WP_Term) {
        $t = get_term_by('slug', $model_slug, 'model');
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
    return $terms;
}

/**
 * When WP parsed /{page-slug}/{something}/ as category+post:
 *  - if the post exists in the legacy "kalkulator" category → resolve it
 *  - if it's a marka-auta term → load the listing page with brand filter
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

    // Post in new category slug?
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

    // Post in legacy "kalkulator" category? → rewrite category so WP resolves it.
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

    // marka-auta term? → load listing page with brand filter.
    $term = get_term_by('slug', $candidate, 'marka-auta');
    if ($term instanceof WP_Term && ! is_wp_error($term)) {
        return [
            'pagename'      => $page_slug,
            'wi_calc_marka' => $term->slug,
        ];
    }

    return $query_vars;
}

add_filter('request', 'wi_offer_request_filter');

// ---------------------------------------------------------------------------
// 3. template_redirect hooks
// ---------------------------------------------------------------------------

/**
 * Validate path taxonomies: unknown brand → 404; unknown model → redirect to brand URL.
 */
function wi_offer_template_redirect_validate_terms(): void
{
    if (is_admin()) {
        return;
    }

    $marka_raw = (string) get_query_var('wi_calc_marka');
    $model_raw = (string) get_query_var('wi_calc_model');

    if ($marka_raw === '' && $model_raw === '') {
        return;
    }

    if (! is_page(wi_offer_page_slug())) {
        return;
    }

    $resolved = wi_offer_get_resolved_terms();

    if ($marka_raw !== '' && ! ($resolved['brand'] instanceof WP_Term)) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        include get_template_directory() . '/404.php';
        exit;
    }

    if ($model_raw !== '' && ! ($resolved['model'] instanceof WP_Term)) {
        $base = wi_offer_base_url();
        $brand_slug = $resolved['brand'] instanceof WP_Term ? $resolved['brand']->slug : $marka_raw;
        $target = trailingslashit($base . $brand_slug);
        $qs = $_SERVER['QUERY_STRING'] ?? '';
        if ($qs !== '') {
            $target .= '?' . $qs;
        }
        wp_safe_redirect($target, 301);
        exit;
    }
}

add_action('template_redirect', 'wi_offer_template_redirect_validate_terms', 2);

/**
 * Strip legacy ?mark= from offer URLs (301 to same path without it).
 */
function wi_offer_redirect_strip_legacy_mark_get(): void
{
    if (is_admin() || ! isset($_GET['mark'])) {
        return;
    }
    if (! is_page(wi_offer_page_slug())) {
        return;
    }

    $url = remove_query_arg('mark');
    wp_safe_redirect($url, 301);
    exit;
}

add_action('template_redirect', 'wi_offer_redirect_strip_legacy_mark_get', 1);

// ---------------------------------------------------------------------------
// 4. Rewrite rules
// ---------------------------------------------------------------------------

/**
 * Register rewrite rules for the offer listing page and marka/model path segments.
 */
function wi_offer_register_rewrite_rules(): void
{
    $slug = wi_offer_page_slug();
    add_rewrite_rule('^' . $slug . '/?$', 'index.php?pagename=' . $slug, 'top');
    add_rewrite_rule(
        '^' . $slug . '/(?!page/)([^/]+)/([^/]+)/?$',
        'index.php?pagename=' . $slug . '&wi_calc_marka=$matches[1]&wi_calc_model=$matches[2]',
        'top'
    );
}

add_action('init', 'wi_offer_register_rewrite_rules', 5);

/**
 * Flush rewrite rules once after offer URL rules change.
 */
function wi_offer_maybe_flush_rewrite_rules(): void
{
    $version = 2;
    if ((int) get_option('wi_offer_rewrite_flushed') === $version) {
        return;
    }

    flush_rewrite_rules();
    update_option('wi_offer_rewrite_flushed', $version);
}

add_action('init', 'wi_offer_maybe_flush_rewrite_rules', 99);

// ---------------------------------------------------------------------------
// 5. Legacy redirects
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
