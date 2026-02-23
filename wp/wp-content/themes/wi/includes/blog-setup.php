<?php

/**
 * Blog CPT, taxonomy, ACF fields (as code), and AJAX like/dislike.
 */

// Register Custom Post Type: blog
function wi_register_blog_cpt()
{
    $labels = [
        'name'                  => _x('Artykuły', 'Post Type General Name', 'wi'),
        'singular_name'         => _x('Artykuł', 'Post Type Singular Name', 'wi'),
        'menu_name'             => __('Blog', 'wi'),
        'name_admin_bar'        => __('Artykuł', 'wi'),
        'archives'              => __('Archiwum artykułów', 'wi'),
        'attributes'            => __('Atrybuty artykułu', 'wi'),
        'parent_item_colon'     => __('Nadrzędny artykuł:', 'wi'),
        'all_items'             => __('Wszystkie artykuły', 'wi'),
        'add_new_item'          => __('Dodaj nowy artykuł', 'wi'),
        'add_new'               => __('Dodaj nowy', 'wi'),
        'new_item'              => __('Nowy artykuł', 'wi'),
        'edit_item'             => __('Edytuj artykuł', 'wi'),
        'update_item'           => __('Aktualizuj artykuł', 'wi'),
        'view_item'             => __('Zobacz artykuł', 'wi'),
        'view_items'            => __('Zobacz artykuły', 'wi'),
        'search_items'          => __('Szukaj artykułów', 'wi'),
        'not_found'             => __('Nie znaleziono', 'wi'),
        'not_found_in_trash'    => __('Brak w koszu', 'wi'),
        'featured_image'        => __('Obrazek wyróżniający', 'wi'),
        'set_featured_image'    => __('Ustaw obrazek wyróżniający', 'wi'),
        'remove_featured_image' => __('Usuń obrazek wyróżniający', 'wi'),
        'use_featured_image'    => __('Jako obrazek wyróżniający', 'wi'),
        'insert_into_item'      => __('Wstaw do artykułu', 'wi'),
        'uploaded_to_this_item' => __('Wgrano do tego artykułu', 'wi'),
        'items_list'            => __('Lista artykułów', 'wi'),
        'items_list_navigation' => __('Nawigacja listy artykułów', 'wi'),
        'filter_items_list'     => __('Filtruj listę artykułów', 'wi'),
    ];
    $args = [
        'label'               => __('Artykuł', 'wi'),
        'labels'              => $labels,
        'supports'            => ['title', 'editor', 'thumbnail', 'excerpt'],
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-welcome-write-blog',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'rewrite'             => ['slug' => 'blog', 'with_front' => false],
        'capability_type'     => 'post',
        'show_in_rest'        => true,
    ];
    register_post_type('blog', $args);
}
add_action('init', 'wi_register_blog_cpt', 0);

// Register Taxonomy: blog-category
function wi_register_blog_category_taxonomy()
{
    $labels = [
        'name'              => _x('Kategorie bloga', 'taxonomy general name', 'wi'),
        'singular_name'     => _x('Kategoria bloga', 'taxonomy singular name', 'wi'),
        'search_items'      => __('Szukaj kategorii', 'wi'),
        'all_items'         => __('Wszystkie kategorie', 'wi'),
        'parent_item'       => __('Kategoria nadrzędna', 'wi'),
        'parent_item_colon' => __('Kategoria nadrzędna:', 'wi'),
        'edit_item'         => __('Edytuj kategorię', 'wi'),
        'update_item'       => __('Aktualizuj kategorię', 'wi'),
        'add_new_item'      => __('Dodaj nową kategorię', 'wi'),
        'new_item_name'     => __('Nazwa nowej kategorii', 'wi'),
        'menu_name'         => __('Kategorie bloga', 'wi'),
    ];
    $args = [
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud'     => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'blog', 'with_front' => false],
    ];
    register_taxonomy('blog-category', ['blog'], $args);
}
add_action('init', 'wi_register_blog_category_taxonomy', 0);

// Custom rewrite rules: single post blog/{category-slug}/{post-slug}/, category archive blog/slug/
function wi_blog_add_rewrite_rules()
{
    // Single post: blog/{category-slug}/{post-slug}/ (two segments; must be before one-segment rule)
    add_rewrite_rule('^blog/([^/]+)/([^/]+)/?$', 'index.php?post_type=blog&name=$matches[2]', 'top');
    // Category archive: blog/przyszlosc/
    add_rewrite_rule('^blog/([^/]+)/?$', 'index.php?blog-category=$matches[1]', 'top');
}
add_action('init', 'wi_blog_add_rewrite_rules', 1);

// Flush rewrite rules once when version changes
function wi_blog_maybe_flush_rewrite_rules()
{
    $version = 4;
    if ((int) get_option('wi_blog_rewrite_flushed') === $version) {
        return;
    }
    flush_rewrite_rules();
    update_option('wi_blog_rewrite_flushed', $version);
}
add_action('init', 'wi_blog_maybe_flush_rewrite_rules', 99);

// ACF Options page for global blog settings (sidebar banner)
function wi_register_blog_options_page()
{
    if (! function_exists('acf_add_options_page')) {
        return;
    }
    acf_add_options_page([
        'page_title' => __('Ustawienia bloga', 'wi'),
        'menu_title' => __('Ustawienia bloga', 'wi'),
        'menu_slug'  => 'ustawienia-bloga',
        'capability' => 'edit_posts',
        'redirect'   => false,
        'parent_slug' => 'edit.php?post_type=blog',
    ]);
}
add_action('acf/init', 'wi_register_blog_options_page');

// ACF field groups (as code)
function wi_register_blog_acf_field_groups()
{
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    // Blog post fields (CPT blog)
    acf_add_local_field_group([
        'key'                   => 'group_blog_post',
        'title'                 => __('Ustawienia artykułu', 'wi'),
        'fields'                => [
            [
                'key'   => 'field_blog_small_image',
                'label' => __('Mały obrazek (karta listy)', 'wi'),
                'name'  => 'blog_small_image',
                'type'  => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ],
            [
                'key'   => 'field_blog_big_image',
                'label' => __('Duży obrazek (hero artykułu)', 'wi'),
                'name'  => 'blog_big_image',
                'type'  => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ],
            [
                'key'           => 'field_blog_reading_time',
                'label'         => __('Czas czytania', 'wi'),
                'name'          => 'blog_reading_time',
                'type'          => 'text',
                'placeholder'   => 'np. 3 min',
            ],
            [
                'key'   => 'field_blog_summary',
                'label' => __('Podsumowanie', 'wi'),
                'name'  => 'blog_summary',
                'type'  => 'textarea',
                'rows'  => 4,
            ],
            [
                'key'   => 'field_blog_sidebar_banner_override',
                'label' => __('Banner boczny (nadpisanie)', 'wi'),
                'name'  => 'blog_sidebar_banner_override',
                'type'  => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'instructions' => __('Opcjonalnie: ustaw inny banner niż globalny dla tego artykułu.', 'wi'),
            ],
            [
                'key'   => 'field_blog_sidebar_banner_link_override',
                'label' => __('Link bannera (nadpisanie)', 'wi'),
                'name'  => 'blog_sidebar_banner_link_override',
                'type'  => 'url',
                'instructions' => __('Link dla bannera nadpisanego w tym poście blogowym.', 'wi'),
            ],
            [
                'key'     => 'field_blog_likes',
                'label'   => __('Polubienia', 'wi'),
                'name'    => 'blog_likes',
                'type'    => 'number',
                'default_value' => 0,
                'min'     => 0,
                'readonly' => 1,
            ],
            [
                'key'     => 'field_blog_dislikes',
                'label'   => __('Niepolubienia', 'wi'),
                'name'    => 'blog_dislikes',
                'type'    => 'number',
                'default_value' => 0,
                'min'     => 0,
                'readonly' => 1,
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'blog',
                ],
            ],
        ],
    ]);

    // Blog options (Ustawienia bloga: hero header + sidebar banner)
    acf_add_local_field_group([
        'key'                   => 'group_blog_options',
        'title'                 => __('Ustawienia bloga', 'wi'),
        'fields'                => [
            // Hero header (shown only on main blog archive – "Wszystkie artykuły")
            [
                'key'   => 'field_blog_hero_title',
                'label' => __('Hero – tytuł', 'wi'),
                'name'  => 'blog_hero_title',
                'type'  => 'text',
                'instructions' => __('Wyświetlany na stronie „Wszystkie artykuły”. W widoku kategorii hero jest bez tła i overlay.', 'wi'),
            ],
            [
                'key'   => 'field_blog_hero_subtitle',
                'label' => __('Hero – podtytuł', 'wi'),
                'name'  => 'blog_hero_subtitle',
                'type'  => 'textarea',
                'rows'  => 3,
            ],
            [
                'key'   => 'field_blog_hero_image',
                'label' => __('Hero – zdjęcie w tle headera', 'wi'),
                'name'  => 'blog_hero_image',
                'type'  => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'instructions' => __('Obrazek w tle nagłówka („Wszystkie artykuły”). Zalecana szerokość ok. 1920 px.', 'wi'),
            ],
            // Sidebar banner
            [
                'key'   => 'field_blog_sidebar_banner_option',
                'label' => __('Banner boczny', 'wi'),
                'name'  => 'blog_sidebar_banner',
                'type'  => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ],
            [
                'key'   => 'field_blog_sidebar_banner_link_option',
                'label' => __('Link bannera', 'wi'),
                'name'  => 'blog_sidebar_banner_link',
                'type'  => 'url',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'options_page',
                    'operator' => '==',
                    'value'    => 'ustawienia-bloga',
                ],
            ],
        ],
    ]);
}
add_action('acf/init', 'wi_register_blog_acf_field_groups');

// AJAX: blog like/dislike
function wi_blog_like_dislike_ajax()
{
    $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
    if (! wp_verify_nonce($nonce, 'blog_like_dislike')) {
        wp_send_json_error(['message' => 'Invalid nonce']);
    }
    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $type    = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
    if (! $post_id || get_post_type($post_id) !== 'blog') {
        wp_send_json_error(['message' => 'Invalid post']);
    }
    if (! in_array($type, ['like', 'dislike'], true)) {
        wp_send_json_error(['message' => 'Invalid type']);
    }
    $field = $type === 'like' ? 'blog_likes' : 'blog_dislikes';
    $current = (int) get_field($field, $post_id);
    $new_value = $current + 1;
    update_field($field, $new_value, $post_id);
    wp_send_json_success([
        'value' => $new_value,
        'type'  => $type,
    ]);
}
add_action('wp_ajax_blog_like', 'wi_blog_like_dislike_ajax');
add_action('wp_ajax_nopriv_blog_like', 'wi_blog_like_dislike_ajax');

// Register query vars for blog URLs
function wi_blog_query_vars($vars)
{
    $vars[] = 'kategoria';
    return $vars;
}
add_filter('query_vars', 'wi_blog_query_vars');

/**
 * Get primary blog-category term for post (Yoast "Podstawowy" or first assigned term).
 *
 * @param int $post_id Post ID.
 * @return WP_Term|null Term object or null.
 */
function wi_blog_get_primary_category_term($post_id)
{
    $taxonomy = 'blog-category';
    $term = null;

    // Yoast primary term (metabox "Ustaw jako główną")
    if (class_exists('WPSEO_Primary_Term')) {
        $primary = new WPSEO_Primary_Term($taxonomy, $post_id);
        $term_id = $primary->get_primary_term();
        if ($term_id) {
            $term = get_term($term_id, $taxonomy);
        }
    }
    if (! $term && function_exists('yoast_get_primary_term_id')) {
        $term_id = yoast_get_primary_term_id($taxonomy, $post_id);
        if ($term_id) {
            $term = get_term($term_id, $taxonomy);
        }
    }
    if (! $term) {
        $primary_id = get_post_meta($post_id, '_yoast_wpseo_primary_' . $taxonomy, true);
        if ($primary_id) {
            $term = get_term((int) $primary_id, $taxonomy);
        }
    }

    // Fallback: first assigned term
    if (! $term || is_wp_error($term)) {
        $terms = get_the_terms($post_id, $taxonomy);
        if ($terms && ! is_wp_error($terms) && ! empty($terms)) {
            $term = $terms[0];
        }
    }

    return ($term && ! is_wp_error($term)) ? $term : null;
}

// Permalink for blog posts: blog/{primary-category-slug}/{post-slug}/ or blog/i/{post-slug}/ fallback
function wi_blog_post_permalink($post_url, $post)
{
    if (! $post || $post->post_type !== 'blog' || $post->post_status !== 'publish') {
        return $post_url;
    }
    $segment = 'i';
    $term = wi_blog_get_primary_category_term($post->ID);
    if ($term && ! empty($term->slug)) {
        $segment = $term->slug;
    }
    $home = trailingslashit(home_url());
    return $home . 'blog/' . $segment . '/' . $post->post_name . '/';
}
add_filter('post_type_link', 'wi_blog_post_permalink', 10, 2);

// Filter blog archive by category when ?kategoria=slug
function wi_blog_archive_tax_query($query)
{
    if (is_admin() || ! $query->is_main_query() || ! is_post_type_archive('blog')) {
        return;
    }
    $slug = get_query_var('kategoria');
    if (empty($slug)) {
        return;
    }
    $term = get_term_by('slug', $slug, 'blog-category');
    if (! $term || is_wp_error($term)) {
        return;
    }
    $query->set('tax_query', [
        [
            'taxonomy' => 'blog-category',
            'field'    => 'term_id',
            'terms'    => $term->term_id,
        ],
    ]);
}
add_action('pre_get_posts', 'wi_blog_archive_tax_query');

// --- Yoast SEO integration for blog CPT ---

// Ensure blog CPT is accessible to Yoast (metabox, meta output, indexables).
add_filter('wpseo_accessible_post_types', 'wi_blog_yoast_accessible_post_types');
function wi_blog_yoast_accessible_post_types($post_types)
{
    if (! is_array($post_types)) {
        $post_types = [];
    }
    if (! in_array('blog', $post_types, true)) {
        $post_types[] = 'blog';
    }
    return $post_types;
}

// Ensure Yoast creates indexables for blog posts (canonical, meta description, OG).
add_filter('wpseo_indexable_excluded_post_types', 'wi_blog_yoast_indexable_post_types');
function wi_blog_yoast_indexable_post_types($excluded)
{
    if (! is_array($excluded)) {
        return $excluded;
    }
    $key = array_search('blog', $excluded, true);
    if ($key !== false) {
        unset($excluded[$key]);
        $excluded = array_values($excluded);
    }
    return $excluded;
}
