<?php

// rejestracja menu
register_nav_menus(array(
    'menu' => __('Menu', 'wp-menu')
));

function wi_editor_styles()
{
    add_editor_style('editor-style.css');
}
add_action('init', 'wi_editor_styles');


// rejestracja custom size images - luk mod
function custom_image_sizes()
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
function mp6_override_toolbar_margin()
{
    if (is_admin_bar_showing()) { ?>
    <style type="text/css" media="screen">
        html { margin-top: 0px !important; }
        * html body { margin-top: 0px !important; }
        @media (min-width: 960px) {
            body { margin-top: 0px !important; }
            html { padding-top: 0px; }
            #wpadminbar { position: fixed !important; }
        }
        @media (max-width: 960px) {
            body { margin-top: 0px !important; }
            html { padding-top: 0px; }
            #wpadminbar{ position: fixed !important; }
        }
        @media (max-width: 782px) {
            #wpadminbar { position:fixed !important; }
            html { padding-top:0px; }
            body { margin-top: 0px !important; } 
        }
        @media (max-width: 599px){
            html { margin-top: 0px !important; padding-top:0px; }
            body { margin-top: 0px !important; } 
        }
    </style>
    <?php }
    }


add_action('admin_head', 'wi_custom_css');
function wi_custom_css()
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
    $number = rand(100, 999); //generuje random
    return sanitize_title($name).'_'.$number.$ext; // zwraca plik
}
add_filter('sanitize_file_name', 'wi_sanitize_file_name', 10);


// Remove br in content
remove_filter('the_content', 'wpautop');
$br = false;
add_filter('the_content', function ($content) use ($br) {
    return wpautop($content, $br);
}, 10);

// Disable the emoji's
function wi_disable_emojis()
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
        return array_diff($plugins, array( 'wpemoji' ));
    }
    return array();
}
// Remove emoji CDN hostname from DNS
function disable_emojis_dns($urls, $relation_type)
{
    if ('dns-prefetch' == $relation_type) {
        $emoji_svg_url_bit = 'https://s.w.org/images/core/emoji/';
        foreach ($urls as $key => $url) {
            if (strpos($url, $emoji_svg_url_bit) !== false) {
                unset($urls[$key]);
            }
        }
    }
    return $urls;
}

// Disable support for comments and trackbacks in post types
function df_disable_comments_post_types_support()
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
    $comments = array();
    return $comments;
}
add_filter('comments_array', 'df_disable_comments_hide_existing_comments', 10, 2);

// Remove comments page in menu
function df_disable_comments_admin_menu()
{
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'df_disable_comments_admin_menu');

// Redirect any user trying to access comments page
function df_disable_comments_admin_menu_redirect()
{
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'df_disable_comments_admin_menu_redirect');

// Remove comments metabox from dashboard
function df_disable_comments_dashboard()
{
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}

add_action('admin_init', 'df_disable_comments_dashboard');

// Remove comments links from admin bar
function df_disable_comments_admin_bar()
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
function cp_change_post_object()
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
add_action('init', function () {
    register_taxonomy('post_tag', []);

});


// Rodzaj nadwozia
$labels = array(
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
);
$args = array(
    'labels' => $labels,
    'hierarchical' => true,
    'show_admin_column' => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => false
);
register_taxonomy('rodzaj-nadwozia', 'post', $args);


// Marka auta
$labels = array(
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
);
$args = array(
    'labels' => $labels,
    'hierarchical' => true,
    'show_admin_column' => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => false
);
register_taxonomy('marka-auta', 'post', $args);


// Rodzaj paliwa
$labels = array(
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
);
$args = array(
    'labels' => $labels,
    'hierarchical' => true,
    'show_admin_column' => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => false
);
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
$labels = array(
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
);
$args = array(
    'labels' => $labels,
    'hierarchical' => true,
    'show_admin_column' => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => false
);
register_taxonomy('skrzynia-biegow', 'post', $args);


// Segment
$labels = array(
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
);
$args = array(
    'labels' => $labels,
    'hierarchical' => true,
    'show_admin_column' => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => false
);

register_taxonomy('segment', 'post', $args);


// W pakiecie
$labels = array(
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
);
$args = array(
    'labels' => $labels,
    'hierarchical' => true,
    'show_admin_column' => true,
    'show_ui'           => true,
    'query_var'         => true,
    'rewrite'           => false
);

register_taxonomy('w-pakiecie', 'post', $args);



/* wydarzenia kolumny */
$post_type = 'post';

add_filter("manage_{$post_type}_posts_columns", function ($defaults) {
    $defaults['id'] = 'ID';
    $defaults['cena_od'] = 'Cena od';
    $defaults['w_pakiecie'] = 'W pakiecie';
    return $defaults;
});

add_action("manage_{$post_type}_posts_custom_column", function ($column_name, $post_id) {
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
    $n_columns = array();
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
    $n_columns = array();
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
function my_column_init()
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
function lowest_price_update($post_ID)
{
    if ($post_ID == wpmlID(2)) {
        $csvFile = get_field('csv', wpmlID(2));
        $csvFile = str_replace(get_site_url() . "/wp-content/uploads", wp_get_upload_dir()['basedir'], $csvFile);
        $csv = csvToArray($csvFile);

        $my_query = new WP_Query(array('post_type' => 'post', 'posts_per_page' => -1));

        if ($my_query->have_posts()) {
            while ($my_query->have_posts()) {
                $my_query->the_post();
                $priceArray = array();
                foreach ($csv as $key => $value) {
                    if (get_field('id', get_the_id()) == $value[0]) {
                        $priceArray[intval(str_replace(' ', '', str_replace(' ', '', $value[7])))]['price'] = intval(str_replace(' ', '', str_replace(' ', '', $value[7])));
                    }
                }
                $priceLow = 0;
                foreach ($priceArray as $key => $value) {
                    if ($priceLow > $key || $priceLow == 0) {
                        $priceLow = $key;
                    }
                }
                update_field('cena_od', strip_tags($priceLow), get_the_id());
            }
        }
    }
}

// car csv api
add_action('init', 'wi_panel_router');
function wi_panel_router()
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
function wi_router_load_templates(&$wp)
{
    if (array_key_exists('wi_carapi', $wp->query_vars)) {
        if (intval($_GET['id']) > 0) {
            $csvFile = get_field('csv', wpmlID(2));
            $csvFile = str_replace(get_site_url() . "/wp-content/uploads", wp_get_upload_dir()['basedir'], $csvFile);
            $csv = csvToArray($csvFile);

            $carID = intval($_GET['id']);
            $monthArray = array();
            $feeArray = array();
            $rateArray = array();
            $priceArrayLow = array();
            $priceArrayLowAll = array();
            $percentArray = array("0", "10", "20");
            foreach ($csv as $key => $value) {
                if ($carID == $value[0]) {
                    if (in_array($value[3], $monthArray)) {
                    } else {
                        array_push($monthArray, $value[3]);
                    }
                    foreach ($percentArray as $percentArrayValue) {
                        if ($percentArrayValue == $value[5]) {
                            $val2 = $value[1];
                            $val3 = intval(str_replace(' ', '', str_replace(' ', '', $value[3])));
                            $val4 = intval(str_replace(' ', '', str_replace(' ', '', $value[4])));
                            $val5 = intval(str_replace(' ', '', str_replace(' ', '', $value[5])));
                            $val6 = intval(str_replace(' ', '', str_replace(' ', '', $value[6])));
                            $val7 = intval(str_replace(' ', '', str_replace(' ', '', $value[7])));
                            $feeArray[$val3][$val4][$val5]['fee'] = $val6;
                            $rateArray[$val3][$val4][$val5]['rate'] = $val7;
                            $rateArray[$val7] = [$val3][$val4][$val5]['rate'];
                        }
                    }
                    $priceArray[$val7 . "mon" . $val3]['idv'] = $val2;
                    $priceArray[$val7 . "mon" . $val3]['month'] = $val3;
                    $priceArray[$val7 . "mon" . $val3]['km'] = $val4;
                    $priceArray[$val7 . "mon" . $val3]['percent'] = $val5;
                    $priceArray[$val7 . "mon" . $val3]['fee'] = $val6;

                    if (!isset($priceArrayLow[$val3]['price']) || $priceArrayLow[$val3]['price'] > $val7) {
                        $priceArrayLow[$val3]['price'] = $val7;
                    }
                    if (!isset($priceArrayLowAll) || $priceArrayLowAll > $val7) {
                        $priceArrayLowAll = $val7;
                    }
                }
            }
            $monthArrayKM = array();
            foreach ($monthArray as $monthArrayValue) {
                $i = 0;
                foreach ($csv as $key => $value) {
                    if ($carID == $value[0]) {
                        if ($value[3] == $monthArrayValue) {
                            $value4 = substr(str_replace(" ", "", $value[4]), 0, -3);
                            if ($monthArrayKM[$monthArrayValue] != "") {
                                if (in_array($value4, $monthArrayKM[$monthArrayValue])) {
                                } else {
                                    $monthArrayKM[$monthArrayValue][$i] = $value4;
                                    $i++;
                                }
                            } else {
                                $monthArrayKM[$monthArrayValue][$i] = $value4;
                                $i++;
                            }
                        }
                    }
                }
            }

            header('Content-Type: application/json; charset=utf-8');
            $data = array();
            if ($_GET['lowprice'] == 1) {
                $data = $priceArrayLow;
            } elseif ($_GET['lowpriceall'] == 1) {
                $data = $priceArrayLowAll;
            } elseif ($_GET['price'] == 1) {
                $data = $priceArray;
            } elseif ($_GET['monthkm'] == 1) {
                $data = $monthArrayKM;
            } elseif ($_GET['fee'] == 1) {
                $data = $feeArray;
            } elseif ($_GET['rate'] == 1) {
                $data = $rateArray;
            }
            wp_send_json($data);
            exit;
        }
    }
}
