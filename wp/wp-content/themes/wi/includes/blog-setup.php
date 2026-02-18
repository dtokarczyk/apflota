<?php

/**
 * Blog CPT, taxonomy, ACF fields (as code), and AJAX like/dislike.
 */

// Register Custom Post Type: blog
function wi_register_blog_cpt()
{
    $labels = array(
        'name'                  => _x('Wpisy', 'Post Type General Name', 'wi'),
        'singular_name'         => _x('Wpis', 'Post Type Singular Name', 'wi'),
        'menu_name'             => __('Blog', 'wi'),
        'name_admin_bar'        => __('Wpis', 'wi'),
        'archives'              => __('Archiwum wpisów', 'wi'),
        'attributes'            => __('Atrybuty wpisu', 'wi'),
        'parent_item_colon'     => __('Nadrzędny wpis:', 'wi'),
        'all_items'             => __('Wszystkie wpisy', 'wi'),
        'add_new_item'          => __('Dodaj nowy wpis', 'wi'),
        'add_new'               => __('Dodaj nowy', 'wi'),
        'new_item'              => __('Nowy wpis', 'wi'),
        'edit_item'             => __('Edytuj wpis', 'wi'),
        'update_item'           => __('Aktualizuj wpis', 'wi'),
        'view_item'             => __('Zobacz wpis', 'wi'),
        'view_items'            => __('Zobacz wpisy', 'wi'),
        'search_items'          => __('Szukaj wpisów', 'wi'),
        'not_found'             => __('Nie znaleziono', 'wi'),
        'not_found_in_trash'    => __('Brak w koszu', 'wi'),
        'featured_image'        => __('Obrazek wyróżniający', 'wi'),
        'set_featured_image'    => __('Ustaw obrazek wyróżniający', 'wi'),
        'remove_featured_image' => __('Usuń obrazek wyróżniający', 'wi'),
        'use_featured_image'    => __('Jako obrazek wyróżniający', 'wi'),
        'insert_into_item'      => __('Wstaw do wpisu', 'wi'),
        'uploaded_to_this_item' => __('Wgrano do tego wpisu', 'wi'),
        'items_list'            => __('Lista wpisów', 'wi'),
        'items_list_navigation' => __('Nawigacja listy wpisów', 'wi'),
        'filter_items_list'     => __('Filtruj listę wpisów', 'wi'),
    );
    $args = array(
        'label'               => __('Wpis', 'wi'),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
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
        'rewrite'             => array( 'slug' => 'blog', 'with_front' => false ),
        'capability_type'     => 'post',
        'show_in_rest'        => true,
    );
    register_post_type('blog', $args);
}
add_action('init', 'wi_register_blog_cpt', 0);

// Register Taxonomy: blog-category
function wi_register_blog_category_taxonomy()
{
    $labels = array(
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
    );
    $args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud'     => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'blog/kategoria', 'with_front' => false ),
    );
    register_taxonomy('blog-category', array( 'blog' ), $args);
}
add_action('init', 'wi_register_blog_category_taxonomy', 0);

// ACF field groups (as code)
function wi_register_blog_acf_field_groups()
{
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    // Blog post fields (CPT blog)
    acf_add_local_field_group(array(
        'key'                   => 'group_blog_post',
        'title'                 => __('Ustawienia wpisu bloga', 'wi'),
        'fields'                => array(
            array(
                'key'   => 'field_blog_small_image',
                'label' => __('Mały obrazek (karta listy)', 'wi'),
                'name'  => 'blog_small_image',
                'type'  => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ),
            array(
                'key'   => 'field_blog_big_image',
                'label' => __('Duży obrazek (hero wpisu)', 'wi'),
                'name'  => 'blog_big_image',
                'type'  => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ),
            array(
                'key'           => 'field_blog_reading_time',
                'label'         => __('Czas czytania', 'wi'),
                'name'          => 'blog_reading_time',
                'type'          => 'text',
                'placeholder'   => 'np. 3 min',
            ),
            array(
                'key'   => 'field_blog_summary',
                'label' => __('Podsumowanie', 'wi'),
                'name'  => 'blog_summary',
                'type'  => 'textarea',
                'rows'  => 4,
            ),
            array(
                'key'     => 'field_blog_likes',
                'label'   => __('Polubienia', 'wi'),
                'name'    => 'blog_likes',
                'type'    => 'number',
                'default_value' => 0,
                'min'     => 0,
                'readonly' => 1,
            ),
            array(
                'key'     => 'field_blog_dislikes',
                'label'   => __('Niepolubienia', 'wi'),
                'name'    => 'blog_dislikes',
                'type'    => 'number',
                'default_value' => 0,
                'min'     => 0,
                'readonly' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'blog',
                ),
            ),
        ),
    ));

    // Blog settings (page ID 2 - global options page)
    acf_add_local_field_group(array(
        'key'                   => 'group_blog_settings',
        'title'                 => __('Ustawienia bloga (strona główna bloga)', 'wi'),
        'fields'                => array(
            array(
                'key'   => 'field_blog_hero_title',
                'label' => __('Blog Hero – tytuł', 'wi'),
                'name'  => 'blog_hero_title',
                'type'  => 'text',
            ),
            array(
                'key'   => 'field_blog_hero_subtitle',
                'label' => __('Blog Hero – podtytul', 'wi'),
                'name'  => 'blog_hero_subtitle',
                'type'  => 'textarea',
                'rows'  => 3,
            ),
            array(
                'key'   => 'field_blog_hero_image',
                'label' => __('Blog Hero – obrazek tła', 'wi'),
                'name'  => 'blog_hero_image',
                'type'  => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ),
            array(
                'key'   => 'field_blog_sidebar_banner',
                'label' => __('Blog Sidebar – banner', 'wi'),
                'name'  => 'blog_sidebar_banner',
                'type'  => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ),
            array(
                'key'   => 'field_blog_sidebar_banner_link',
                'label' => __('Blog Sidebar – link bannera', 'wi'),
                'name'  => 'blog_sidebar_banner_link',
                'type'  => 'url',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'page',
                    'operator' => '==',
                    'value'    => '2',
                ),
            ),
        ),
    ));
}
add_action('acf/init', 'wi_register_blog_acf_field_groups');

// AJAX: blog like/dislike
function wi_blog_like_dislike_ajax()
{
    $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
    if (! wp_verify_nonce($nonce, 'blog_like_dislike')) {
        wp_send_json_error(array( 'message' => 'Invalid nonce' ));
    }
    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $type    = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
    if (! $post_id || get_post_type($post_id) !== 'blog') {
        wp_send_json_error(array( 'message' => 'Invalid post' ));
    }
    if (! in_array($type, array( 'like', 'dislike' ), true)) {
        wp_send_json_error(array( 'message' => 'Invalid type' ));
    }
    $field = $type === 'like' ? 'blog_likes' : 'blog_dislikes';
    $current = (int) get_field($field, $post_id);
    $new_value = $current + 1;
    update_field($field, $new_value, $post_id);
    wp_send_json_success(array(
        'value' => $new_value,
        'type'  => $type,
    ));
}
add_action('wp_ajax_blog_like', 'wi_blog_like_dislike_ajax');
add_action('wp_ajax_nopriv_blog_like', 'wi_blog_like_dislike_ajax');

// Register query var for category filter on blog archive
function wi_blog_query_vars($vars)
{
    $vars[] = 'kategoria';
    return $vars;
}
add_filter('query_vars', 'wi_blog_query_vars');

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
    $query->set('tax_query', array(
        array(
            'taxonomy' => 'blog-category',
            'field'    => 'term_id',
            'terms'    => $term->term_id,
        ),
    ));
}
add_action('pre_get_posts', 'wi_blog_archive_tax_query');
