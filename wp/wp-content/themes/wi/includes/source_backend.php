<?php

// rejestracja menu
register_nav_menus([
    'menu' => __('Menu', 'wp-menu'),
]);

function wi_editor_styles(): void
{
    add_editor_style('editor-style.css');
}
add_action('init', 'wi_editor_styles');


// rejestracja custom size images - luk mod
function custom_image_sizes(): void
{
    remove_image_size('small');
    remove_image_size('large');
    remove_image_size('medium_large');
    remove_image_size('Medium_large');
    remove_image_size('1536x1536');
    remove_image_size('2048x2048');
    add_image_size('img-120x120', 120, 120, true);
    add_image_size('produkt-194x108', 194, 108, true);
    add_image_size('produkt-500x250', 500, 250, true);
    add_image_size('produkt-824x464', 824, 464, true);
    add_image_size('topbanner-712x440', 712, 440, true);
    add_image_size('img-464x464', 464, 464, true);
    add_image_size('ce_img-704xX', 704, 960, false);
    add_image_size('blog-small', 460, 280, true);
    add_image_size('blog-hero', 1920, 600, true);
}
add_action('after_setup_theme', 'custom_image_sizes');

// adminbar - icon
add_action('wp_head', 'mp6_override_toolbar_margin', 11);
function mp6_override_toolbar_margin(): void
{
    if (is_admin_bar_showing()) { ?>
        <style type="text/css" media="screen">
            html {
                margin-top: 0px !important;
            }

            * html body {
                margin-top: 0px !important;
            }

            @media (min-width: 960px) {
                body {
                    margin-top: 0px !important;
                }

                html {
                    padding-top: 0px;
                }

                #wpadminbar {
                    position: fixed !important;
                }
            }

            @media (max-width: 960px) {
                body {
                    margin-top: 0px !important;
                }

                html {
                    padding-top: 0px;
                }

                #wpadminbar {
                    position: fixed !important;
                }
            }

            @media (max-width: 782px) {
                #wpadminbar {
                    position: fixed !important;
                }

                html {
                    padding-top: 0px;
                }

                body {
                    margin-top: 0px !important;
                }
            }

            @media (max-width: 599px) {
                html {
                    margin-top: 0px !important;
                    padding-top: 0px;
                }

                body {
                    margin-top: 0px !important;
                }
            }
        </style>
<?php }
    }


add_action('admin_head', 'wi_custom_css');
function wi_custom_css(): void
{
    echo '<style>
        .post-type-post #icl_translations {
            width: 100px;
        }
        .wp-list-table .column-id {
            width: 18px;
        }
        .wp-list-table .column-cena_od {
            width: 52px;
        }
        .wp-list-table .column-w_pakiecie {
            width: 300px;
        }
        @media (max-width: 1450px) {
            .wp-list-table .column-w_pakiecie {
                width: 170px;
            }
        }
    </style>';
}


// remove white space in menu
add_filter('wp_nav_menu_items', 'prefix_remove_menu_item_whitespace');
function prefix_remove_menu_item_whitespace($items)
{
    return preg_replace('/>(\s|\n|\r)+</', '><', $items);
}


// File rename on upload - luk mod
function wi_sanitize_file_name($filename)
{
    $info = pathinfo($filename); // info o sciezce
    $ext  = empty($info['extension']) ? '' : '.' . $info['extension']; // wyciaga rozszerzenie
    $name = basename($filename, $ext); ///odcina rozszerzenie
    $number = random_int(100, 999); //generuje random
    return sanitize_title($name) . '_' . $number . $ext; // zwraca plik
}
add_filter('sanitize_file_name', 'wi_sanitize_file_name', 10);


// Remove br in content
remove_filter('the_content', 'wpautop');
$br = false;
add_filter('the_content', fn($content) => wpautop($content, $br), 10);

// Disable the emoji's
function wi_disable_emojis(): void
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('tiny_mce_plugins', 'disable_emojis_tinymce');
    add_filter('wp_resource_hints', 'disable_emojis_dns', 10, 2);
}
add_action('init', 'wi_disable_emojis');

// Filter function used to remove the tinymce emoji plugin
function disable_emojis_tinymce($plugins)
{
    if (is_array($plugins)) {
        return array_diff($plugins, ['wpemoji']);
    }
    return [];
}
// Remove emoji CDN hostname from DNS
function disable_emojis_dns($urls, $relation_type)
{
    if ('dns-prefetch' == $relation_type) {
        $emoji_svg_url_bit = 'https://s.w.org/images/core/emoji/';
        foreach ($urls as $key => $url) {
            if (str_contains($url, $emoji_svg_url_bit)) {
                unset($urls[$key]);
            }
        }
    }
    return $urls;
}

// Disable support for comments and trackbacks in post types
function df_disable_comments_post_types_support(): void
{
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'df_disable_comments_post_types_support');

// Close comments on the front-end
function df_disable_comments_status()
{
    return false;
}
add_filter('comments_open', 'df_disable_comments_status', 20, 2);
add_filter('pings_open', 'df_disable_comments_status', 20, 2);

// Hide existing comments
function df_disable_comments_hide_existing_comments($comments)
{
    $comments = [];
    return $comments;
}
add_filter('comments_array', 'df_disable_comments_hide_existing_comments', 10, 2);

// Remove comments page in menu
function df_disable_comments_admin_menu(): void
{
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'df_disable_comments_admin_menu');

// Redirect any user trying to access comments page
function df_disable_comments_admin_menu_redirect(): void
{
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'df_disable_comments_admin_menu_redirect');

// Remove comments metabox from dashboard
function df_disable_comments_dashboard(): void
{
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}

add_action('admin_init', 'df_disable_comments_dashboard');

// Remove comments links from admin bar
function df_disable_comments_admin_bar(): void
{
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
}
add_action('init', 'df_disable_comments_admin_bar');

// Kategorie drzewo bez łamania - luk mod
function taxonomyChecklistArgs($args, $post_id)
{
    $args['checked_ontop'] = false;
    return $args;
}
add_filter('wp_terms_checklist_args', 'taxonomyChecklistArgs', 1, 2);


// Zmiana nazwy postow w panelu admina
function cp_change_post_object(): void
{
    $get_post_type = get_post_type_object('post');
    $labels = $get_post_type->labels;
    $labels->name = 'Oferta';
    $labels->singular_name = 'Oferta';
    $labels->all_items = 'Wszystkie';
    $labels->menu_name = 'Oferta';
    $labels->name_admin_bar = 'Oferta';
}
add_action('init', 'cp_change_post_object');

// Usuwanie tagow w poscie
add_action('init', function (): void {
    register_taxonomy('post_tag', []);
});


// Rodzaj nadwozia
$labels = [
    'name'              => __('Rodzaj nadwozia', 'wi-post'),
    'singular_name'     => __('Rodzaj nadwozia', 'wi-post'),
    'search_items'      => __('Szukaj', 'wi-post'),
    'all_items'         => __('Wszystkie', 'wi-post'),
    'parent_item'       => __('Rodzic', 'wi-post'),
    'parent_item_colon' => __('Rodzic', 'wi-post'),
    'edit_item'         => __('Edytuj', 'wi-post'),
    'update_item'       => __('Zaktualizuj', 'wi-post'),
    'add_new_item'      => __('Dodaj', 'wi-post'),
    'new_item_name'     => __('Nowy', 'wi-post'),
    'menu_name'         => __('Rodzaj nadwozia', 'wi-post'),
];
$args = [
    'labels' => $labels,
    'hierarchical' => true,
    'show_admin_column' => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => false,
];
register_taxonomy('rodzaj-nadwozia', 'post', $args);


// Marka auta
$labels = [
    'name'              => __('Marka auta', 'wi-post'),
    'singular_name'     => __('Marka auta', 'wi-post'),
    'search_items'      => __('Szukaj', 'wi-post'),
    'all_items'         => __('Wszystkie', 'wi-post'),
    'parent_item'       => __('Rodzic', 'wi-post'),
    'parent_item_colon' => __('Rodzic', 'wi-post'),
    'edit_item'         => __('Edytuj', 'wi-post'),
    'update_item'       => __('Zaktualizuj', 'wi-post'),
    'add_new_item'      => __('Dodaj', 'wi-post'),
    'new_item_name'     => __('Nowy', 'wi-post'),
    'menu_name'         => __('Marka auta', 'wi-post'),
];
$args = [
    'labels' => $labels,
    'hierarchical' => true,
    'show_admin_column' => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => false,
];
register_taxonomy('marka-auta', 'post', $args);


// Rodzaj paliwa
$labels = [
    'name'              => __('Rodzaj paliwa', 'wi-post'),
    'singular_name'     => __('Rodzaj paliwa', 'wi-post'),
    'search_items'      => __('Szukaj', 'wi-post'),
    'all_items'         => __('Wszystkie', 'wi-post'),
    'parent_item'       => __('Rodzic', 'wi-post'),
    'parent_item_colon' => __('Rodzic', 'wi-post'),
    'edit_item'         => __('Edytuj', 'wi-post'),
    'update_item'       => __('Zaktualizuj', 'wi-post'),
    'add_new_item'      => __('Dodaj', 'wi-post'),
    'new_item_name'     => __('Nowy', 'wi-post'),
    'menu_name'         => __('Rodzaj paliwa', 'wi-post'),
];
$args = [
    'labels' => $labels,
    'hierarchical' => true,
    'show_admin_column' => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => false,
];
register_taxonomy('rodzaj-paliwa', 'post', $args);


// Rata do
/*
$labels = array(
    'name'              => __( 'Rata do', 'wi-post' ),
    'singular_name'     => __( 'Rata do', 'wi-post' ),
    'search_items'      => __( 'Szukaj', 'wi-post' ),
    'all_items'         => __( 'Wszystkie', 'wi-post' ),
    'parent_item'       => __( 'Rodzic', 'wi-post' ),
    'parent_item_colon' => __( 'Rodzic', 'wi-post' ),
    'edit_item'         => __( 'Edytuj', 'wi-post' ),
    'update_item'       => __( 'Zaktualizuj', 'wi-post' ),
    'add_new_item'      => __( 'Dodaj', 'wi-post' ),
    'new_item_name'     => __( 'Nowy', 'wi-post' ),
    'menu_name'         => __( 'Rata do', 'wi-post' ),
);
$args = array(
    'labels' => $labels,
    'hierarchical' => true,
    'show_admin_column' => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => false
);
register_taxonomy( 'rata-do', 'post', $args );
*/

// Skrzynia biegów
$labels = [
    'name'              => __('Skrzynia biegów', 'wi-post'),
    'singular_name'     => __('Skrzynia biegów', 'wi-post'),
    'search_items'      => __('Szukaj', 'wi-post'),
    'all_items'         => __('Wszystkie', 'wi-post'),
    'parent_item'       => __('Rodzic', 'wi-post'),
    'parent_item_colon' => __('Rodzic', 'wi-post'),
    'edit_item'         => __('Edytuj', 'wi-post'),
    'update_item'       => __('Zaktualizuj', 'wi-post'),
    'add_new_item'      => __('Dodaj', 'wi-post'),
    'new_item_name'     => __('Nowy', 'wi-post'),
    'menu_name'         => __('Skrzynia biegów', 'wi-post'),
];
$args = [
    'labels' => $labels,
    'hierarchical' => true,
    'show_admin_column' => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => false,
];
register_taxonomy('skrzynia-biegow', 'post', $args);


// Segment
$labels = [
    'name'              => __('Segment', 'wi-post'),
    'singular_name'     => __('Segment', 'wi-post'),
    'search_items'      => __('Szukaj', 'wi-post'),
    'all_items'         => __('Wszystkie', 'wi-post'),
    'parent_item'       => __('Rodzic', 'wi-post'),
    'parent_item_colon' => __('Rodzic', 'wi-post'),
    'edit_item'         => __('Edytuj', 'wi-post'),
    'update_item'       => __('Zaktualizuj', 'wi-post'),
    'add_new_item'      => __('Dodaj', 'wi-post'),
    'new_item_name'     => __('Nowy', 'wi-post'),
    'menu_name'         => __('Segment', 'wi-post'),
];
$args = [
    'labels' => $labels,
    'hierarchical' => true,
    'show_admin_column' => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => false,
];

register_taxonomy('segment', 'post', $args);


// W pakiecie
$labels = [
    'name'              => __('W pakiecie', 'wi-post'),
    'singular_name'     => __('W pakiecie', 'wi-post'),
    'search_items'      => __('Szukaj', 'wi-post'),
    'all_items'         => __('Wszystkie', 'wi-post'),
    'parent_item'       => __('Rodzic', 'wi-post'),
    'parent_item_colon' => __('Rodzic', 'wi-post'),
    'edit_item'         => __('Edytuj', 'wi-post'),
    'update_item'       => __('Zaktualizuj', 'wi-post'),
    'add_new_item'      => __('Dodaj', 'wi-post'),
    'new_item_name'     => __('Nowy', 'wi-post'),
    'menu_name'         => __('W pakiecie', 'wi-post'),
];
$args = [
    'labels' => $labels,
    'hierarchical' => true,
    'show_admin_column' => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => false,
];

register_taxonomy('w-pakiecie', 'post', $args);



/* wydarzenia kolumny */
$post_type = 'post';

add_filter("manage_{$post_type}_posts_columns", function ($defaults) {
    $defaults['id'] = 'ID';
    $defaults['cena_od'] = 'Cena od';
    $defaults['w_pakiecie'] = 'W pakiecie';
    return $defaults;
});

add_action("manage_{$post_type}_posts_custom_column", function ($column_name, $post_id): void {
    if ($column_name == 'id') {
        echo '<span style="font-weight:bold">' . get_field('id', $post_id) . '</span>';
    }
    if ($column_name == 'cena_od') {
        echo '<span>' . get_field('cena_od', $post_id) . ' zł</span>';
    }
    if ($column_name == 'w_pakiecie' && get_field('w_pakiecie', $post_id) != "") {
        echo '<ul style="list-style:unset;font-size:12px;line-height:1.2;margin:0;padding-left:15px;">';
        foreach (get_field('w_pakiecie', $post_id) as $cecha) {
            echo '<li>' . $cecha->name . '</li>';
        }
        echo '</ul>';
    }
}, 10, 2);



add_filter('manage_posts_columns', 'column_order');
function column_order($columns)
{
    $n_columns = [];
    $move = 'id'; // what to move
    $before = 'title'; // move before this
    foreach ($columns as $key => $value) {
        if ($key == $before) {
            $n_columns[$move] = $move;
        }
        $n_columns[$key] = $value;
    }
    return $n_columns;
}


add_filter('manage_posts_columns', 'column_order2');
function column_order2($columns)
{
    $n_columns = [];
    $move = 'cena_od'; // what to move
    $before = 'title'; // move before this
    foreach ($columns as $key => $value) {
        if ($key == $before) {
            $n_columns[$move] = $move;
        }
        $n_columns[$key] = $value;
    }
    return $n_columns;
}


function my_manage_columns($columns)
{
    unset($columns['taxonomy-w-pakiecie']);
    return $columns;
}
function my_column_init(): void
{
    add_filter('manage_posts_columns', 'my_manage_columns');
}
add_action('admin_init', 'my_column_init');


// csvToArray
function csvToArray($csvFile)
{
    $file_to_read = fopen($csvFile, 'r');
    while (!feof($file_to_read)) {
        $lines[] = fgetcsv($file_to_read, 10000, ';');
    }
    fclose($file_to_read);
    return $lines;
}

// lowest price update
add_action('save_post', 'lowest_price_update');
function lowest_price_update($post_ID): void
{
    if ($post_ID != wpmlID(2)) {
        return;
    }
    if (function_exists('wi_calc_update_cena_od_for_all_cars')) {
        wi_calc_update_cena_od_for_all_cars();
        return;
    }
    $csvFile = get_field('csv', wpmlID(2));
    if (! $csvFile) {
        return;
    }
    $csvFile = str_replace(get_site_url() . '/wp-content/uploads', wp_get_upload_dir()['basedir'], $csvFile);
    if (! is_file($csvFile)) {
        return;
    }
    $csv = csvToArray($csvFile);
    $my_query = new WP_Query(['post_type' => 'post', 'posts_per_page' => -1]);
    if (! $my_query->have_posts()) {
        return;
    }
    while ($my_query->have_posts()) {
        $my_query->the_post();
        $priceArray = [];
        foreach ($csv as $value) {
            if (get_field('id', get_the_id()) == ($value[0] ?? null)) {
                $rate = (int) preg_replace('/[^\d]/u', '', $value[7] ?? '0');
                $priceArray[$rate] = ['price' => $rate];
            }
        }
        $priceLow = 0;
        foreach ($priceArray as $key => $value) {
            if ($priceLow > $key || $priceLow == 0) {
                $priceLow = $key;
            }
        }
        if ($priceLow > 0) {
            update_field('cena_od', $priceLow, get_the_id());
        }
    }
}

// car csv api
add_action('init', 'wi_panel_router');
function wi_panel_router(): void
{
    add_rewrite_rule('^carapi', 'index.php?wi_carapi=1', 'top');
}
// add to query vars
add_filter('query_vars', 'add_router_slug');
function add_router_slug($query_vars)
{
    $query_vars[] = 'wi_carapi';
    return $query_vars;
}
// template loader user panel
add_action('parse_request', 'wi_router_load_templates');
function wi_router_load_templates(&$wp): void
{
    if (! array_key_exists('wi_carapi', $wp->query_vars) || (int) $_GET['id'] <= 0) {
        return;
    }

    $carID = (string) $_GET['id'];

    if (class_exists('CalcRate')) {
        $structures = wi_calc_build_api_structures_from_db($carID);
    } else {
        // TODO Remove in future – CSV fallback; prefer DB (CalcRate) when available.
        $structures = wi_calc_build_api_structures_from_csv($carID);
    }

    if ($structures === null) {
        return;
    }

    header('Content-Type: application/json; charset=utf-8');
    if (! empty($_GET['all'])) {
        wp_send_json($structures);
    } else {
        if (isset($_GET['lowprice']) && $_GET['lowprice'] == 1) {
            wp_send_json($structures['lowprice']);
        } elseif (isset($_GET['lowpriceall']) && $_GET['lowpriceall'] == 1) {
            wp_send_json($structures['lowpriceall']);
        } elseif (isset($_GET['price']) && $_GET['price'] == 1) {
            wp_send_json($structures['price']);
        } elseif (isset($_GET['monthkm']) && $_GET['monthkm'] == 1) {
            wp_send_json($structures['monthkm']);
        } elseif (isset($_GET['fee']) && $_GET['fee'] == 1) {
            wp_send_json($structures['fee']);
        } elseif (isset($_GET['rate']) && $_GET['rate'] == 1) {
            wp_send_json($structures['rate']);
        } else {
            wp_send_json([]);
        }
    }
    exit;
}

function wi_calc_build_api_structures_from_db(string $carID): ?array
{
    $rates = CalcRate::where('car_id', $carID)->get();
    if ($rates->isEmpty()) {
        return [
            'lowprice'    => [],
            'lowpriceall' => 0,
            'price'       => [],
            'monthkm'     => [],
            'fee'         => [],
            'rate'        => [],
        ];
    }

    $feeArray      = [];
    $rateArray     = [];
    $priceArray    = [];
    $priceArrayLow = [];
    $priceArrayLowAll = null;
    $months        = [];

    foreach ($rates as $r) {
        $month   = (int) $r->month;
        $km      = (int) $r->km;
        $percent = (int) $r->percent;
        $rate    = (int) $r->rate;
        $months[$month] = true;
        $feeArray[$month][$km][$percent] = ['fee' => (int) $r->fee];
        $rateArray[$month][$km][$percent] = ['rate' => $rate];
        $key = $rate . 'mon' . $month;
        $priceArray[$key] = [
            'idv'     => $r->idv,
            'month'   => $month,
            'km'      => $km,
            'percent' => $percent,
            'fee'     => (int) $r->fee,
        ];
        if (! isset($priceArrayLow[$month]['price']) || $priceArrayLow[$month]['price'] > $rate) {
            $priceArrayLow[$month] = ['price' => $rate];
        }
        if ($priceArrayLowAll === null || $priceArrayLowAll > $rate) {
            $priceArrayLowAll = $rate;
        }
    }

    $monthArrayKM = [];
    foreach (array_keys($months) as $month) {
        $kmDisplays = [];
        foreach ($rates as $r) {
            if ((int) $r->month === $month) {
                $display = (string) floor((int) $r->km / 1000);
                if (! in_array($display, $kmDisplays, true)) {
                    $kmDisplays[] = $display;
                }
            }
        }
        $monthArrayKM[$month] = array_values($kmDisplays);
    }

    return [
        'lowprice'    => $priceArrayLow,
        'lowpriceall' => $priceArrayLowAll,
        'price'       => $priceArray,
        'monthkm'     => $monthArrayKM,
        'fee'         => $feeArray,
        'rate'        => $rateArray,
    ];
}

function wi_calc_build_api_structures_from_csv(string $carID): ?array
{
    $csvFile = get_field('csv', wpmlID(2));
    if (! $csvFile) {
        return null;
    }
    $csvFile = str_replace(get_site_url() . '/wp-content/uploads', wp_get_upload_dir()['basedir'], $csvFile);
    if (! is_file($csvFile)) {
        return null;
    }
    $csv   = csvToArray($csvFile);
    $monthArray = [];
    $feeArray = [];
    $rateArray = [];
    $priceArray = [];
    $priceArrayLow = [];
    $priceArrayLowAll = null;
    $percentArray = ['0', '10', '20'];
    foreach ($csv as $value) {
        if (! isset($value[0]) || (string) $value[0] !== $carID) {
            continue;
        }
        if (! in_array($value[3], $monthArray, true)) {
            $monthArray[] = $value[3];
        }
        $val3 = (int) preg_replace('/\s/u', '', $value[3] ?? '0');
        $val4 = (int) preg_replace('/\s/u', '', $value[4] ?? '0');
        $val5 = (string) ($value[5] ?? '');
        $val6 = (int) preg_replace('/[^\d]/u', '', $value[6] ?? '0');
        $val7 = (int) preg_replace('/[^\d]/u', '', $value[7] ?? '0');
        if (! in_array($val5, $percentArray, true)) {
            continue;
        }
        $val5int = (int) $val5;
        $feeArray[$val3][$val4][$val5int] = ['fee' => $val6];
        $rateArray[$val3][$val4][$val5int] = ['rate' => $val7];
        $key = $val7 . 'mon' . $val3;
        $priceArray[$key] = [
            'idv'     => $value[1] ?? '',
            'month'   => $val3,
            'km'      => $val4,
            'percent' => $val5int,
            'fee'     => $val6,
        ];
        if (! isset($priceArrayLow[$val3]['price']) || $priceArrayLow[$val3]['price'] > $val7) {
            $priceArrayLow[$val3] = ['price' => $val7];
        }
        if ($priceArrayLowAll === null || $priceArrayLowAll > $val7) {
            $priceArrayLowAll = $val7;
        }
    }
    $monthArrayKM = [];
    foreach ($monthArray as $monthArrayValue) {
        $seen = [];
        foreach ($csv as $value) {
            if (! isset($value[0]) || (string) $value[0] !== $carID || ($value[3] ?? '') != $monthArrayValue) {
                continue;
            }
            $raw = preg_replace('/\s/u', '', $value[4] ?? '');
            $value4 = $raw !== '' ? substr($raw, 0, -3) : '';
            if ($value4 !== '' && ! in_array($value4, $seen, true)) {
                $seen[] = $value4;
            }
        }
        $monthArrayKM[$monthArrayValue] = array_values($seen);
    }
    return [
        'lowprice'    => $priceArrayLow,
        'lowpriceall' => $priceArrayLowAll,
        'price'       => $priceArray,
        'monthkm'     => $monthArrayKM,
        'fee'         => $feeArray,
        'rate'        => $rateArray,
    ];
}

add_filter('wp_is_application_passwords_available', '__return_true');
