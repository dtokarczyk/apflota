<?php

declare(strict_types=1);


// Add dropdawn to menu
add_filter('nav_menu_link_attributes', 'nav_link_att', 10, 3);
function nav_link_att($atts, $item, $args)
{
    if ($args->has_children) {
        //$atts['data-toggle'] = 'dropdown';
        //$atts['class'] = 'dropdown-toggle';
    }
    return $atts;
}


// Add class active to menu
add_filter('nav_menu_css_class', 'special_nav_class', 10, 2);
function special_nav_class($classes, $item)
{
    if (in_array('current-menu-item', $classes)) {
        $classes[] = 'active ';
    }
    return $classes;
}


// BS3_Walker_Nav_Menu
class BS3_Walker_Nav_Menu extends Walker_Nav_Menu
{
    public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output)
    {
        $id_field = $this->db_fields['id'];

        if (isset($args[0]) && is_object($args[0])) {
            $args[0]->has_children = !empty($children_elements[$element->$id_field]);
        }

        return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }
    public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0): void
    {
        if (is_object($args) && !empty($args->has_children)) {
            $link_after = $args->link_after;
            //$args->link_after = ' <b class="caret"></b>';
        }

        parent::start_el($output, $item, $depth, $args, $id);

        if (is_object($args) && !empty($args->has_children)) {
            $args->link_after = $link_after;
        }
    }
    public function start_lvl(&$output, $depth = 0, $args = []): void
    {
        $indent = '';
        $output .= "$indent<span class=\"caret_arrow\"><b class=\"caret\"></span></b><ul class=\"dropdown-menu list-unstyled\">";
    }
}


// Paginacja strony kategorii
function wpbeginner_numeric_posts_nav(): void
{

    if (is_singular()) {
        return;
    }

    global $wp_query;

    /* Stop execution if there's only 1 page */
    if ($wp_query->max_num_pages <= 1) {
        return;
    }

    $paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
    $max = intval($wp_query->max_num_pages);

    /* Add current page to the array */
    if ($paged >= 1) {
        $links[] = $paged;
    }

    /* Add the pages around the current page to the array */
    if ($paged >= 3) {
        $links[] = $paged - 1;
        $links[] = $paged - 2;
    }

    if (($paged + 2) <= $max) {
        $links[] = $paged + 2;
        $links[] = $paged + 1;
    }

    echo '<div class="paginationNewBox"><ul class="paginationNew">' . "";

    /* Previous Post Link */
    if (get_previous_posts_link()) {
        $npl_url = explode('"', get_previous_posts_link());
        echo '<li><a class="paginationNewButton arrow-prev-pagi" href="' . $npl_url[1] . '"><span></span></a></li>';
    }
    /* Link to first page, plus ellipses if necessary */
    if (!in_array(1, $links)) {
        $class = 1 == $paged ? ' class="active"' : '';

        printf('<li%s><a class="paginationNewButton" href="%s"><span>%s</span></a></li>' . "", $class, esc_url(get_pagenum_link(1)), '1');

        if (!in_array(2, $links)) {
            echo '<li><span class="paginationNewButton"><span>...</span></span></li>';
        }
    }

    /* Link to current page, plus 2 pages in either direction if necessary */
    sort($links);
    foreach ((array) $links as $link) {
        $class = $paged == $link ? ' class="active"' : '';
        printf('<li%s><a class="paginationNewButton" href="%s"><span>%s</span></a></li>' . "", $class, esc_url(get_pagenum_link($link)), $link);
    }

    /* Link to last page, plus ellipses if necessary */
    if (!in_array($max, $links)) {
        if (!in_array($max - 1, $links)) {
            echo '<li><span class="paginationNewButton"><span>...</span></span></li>' . "";
        }

        $class = $paged == $max ? ' class="active"' : '';
        printf('<li%s><a class="paginationNewButton" href="%s"><span>%s</span></a></li>' . "", $class, esc_url(get_pagenum_link($max)), $max);
    }

    /* Next Post Link */
    if (get_next_posts_link()) {
        $npl_url = explode('"', get_next_posts_link());
        echo '<li><a class="paginationNewButton arrow-next-pagi" href="' . $npl_url[1] . '"><span></span></a></li>';
    }
    echo '</ul></div>' . "\n";
}


// Excerpt kropki - luk mod
function wpdocs_excerpt_more($more)
{
    return '...';
}
add_filter('excerpt_more', 'wpdocs_excerpt_more');


// add list-group-itemclass to menu link - luk mod
function add_menuclass($ulclass)
{
    return preg_replace('/<a /', '<a class="list-group-item"', $ulclass);
}
add_filter('wp_nav_menu', 'add_menuclass');


// Menu add span to a - hover underline - luk mod
add_filter('wp_nav_menu_objects', function ($items) {
    foreach ($items as $item) {
        $item->title = '<span>' . $item->title . '</span>';
    }
    return $items;
});


// wpmlID
function wpmlID($page_id)
{
    if (function_exists('icl_object_id')) {
        return icl_object_id($page_id, 'page', false, ICL_LANGUAGE_CODE);
    } else {
        return $page_id;
    }
}
// wpmlIDTax
function wpmlIDTax($page_id, $taxonomy_name)
{
    if (function_exists('icl_object_id')) {
        return icl_object_id($page_id, $taxonomy_name, false, ICL_LANGUAGE_CODE);
    } else {
        return $page_id;
    }
}

// Usuwanie pojedynczych znaku na koncu zdan - luk mod
function koncowki_luk_mod($value)
{
    $value = preg_replace('/ ([a-zA-Z0-9]{1}) /', " $1&nbsp;", $value);
    return $value;
}

function acf_koncowki($value, $post_id, $field)
{
    $value = koncowki_luk_mod($value);
    return $value;
}

function the_content_koncowki($value)
{
    $value = koncowki_luk_mod($value);
    return $value;
}
add_filter('the_content', 'the_content_koncowki', 10, 3);
add_filter('acf/format_value/type=text', 'acf_koncowki', 10, 3);
add_filter('acf/format_value/type=textarea', 'acf_koncowki', 10, 3);
add_filter('acf/format_value/type=wysiwyg', 'acf_koncowki', 10, 3);


function qt_custom_breadcrumbs()
{
    // Settings
    $page_id_gt = get_the_ID();
    $separator = '';
    $breadcrums_id = 'breadcrumbs';
    $breadcrums_class = 'breadcrumb';
    $home_title = 'Home';
    $home_img = '<img src="' . get_template_directory_uri() . '/images/home.svg" class="img-responsive svg"/>';

    // If you have any custom post types with custom taxonomies, put the taxonomy name below (e.g. product_cat)
    $custom_taxonomy = 'wydarzenia';

    // Get the query & post information
    global $post, $wp_query;

    function smart_category_top_parent_id($catid)
    {
        while ($catid) {
            $cat = get_category($catid); // get the object for the catid
            $catid = $cat->category_parent; // assign parent ID (if exists) to $catid
            // the while loop will continue whilst there is a $catid
            // when there is no longer a parent $catid will be NULL so we can assign our $catParent
            $catParent = $cat->cat_ID;
        }
        return $catParent;
    }

    // Do not display on the homepage
    if (!is_front_page() || is_front_page()) {

        $schemaPosition = 2;

        // Build the breadcrums
        echo '<ol id="' . $breadcrums_id . '" class="' . $breadcrums_class . ' displaFlex flexWrap flexXstart flexYcenter" itemscope itemtype="http://schema.org/BreadcrumbList">';

        // Home page
        echo '<li class="item-home" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" class="bread-link bread-home" href="' . get_home_url() . '" aria-label="' . __('Strona główna', 'wi') . '"><span itemprop="name">' . __('Strona główna', 'wi') . '</span></a><meta itemprop="position" content="1" /></li>';

        // If post is a custom post type
        $post_type = get_post_type();

        // POST TYPE ===========================================================

        // POST TYPE - Blog
        if (is_post_type_archive('blog')) {
            echo '<li class="active item-current item-cat" itemscope itemtype="http://schema.org/ListItem"><strong class="bread-current bread-cat">' . __("Blog", "wi") . '</strong></li>';
        }
        if (is_tax('blog-category') && get_queried_object() && get_queried_object()->taxonomy === 'blog-category') {
            echo '<li itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url(get_post_type_archive_link('blog')) . '" aria-label="' . esc_attr__('Blog', 'wi') . '"><span itemprop="name">' . __('Blog', 'wi') . '</span></a><meta itemprop="position" content="' . $schemaPosition++ . '" /></li>';
        }

        if (is_archive() && !is_tax() && !is_category() && !is_tag()) {

        } elseif (is_archive() && is_tax() && !is_category() && !is_tag()) {

            // If it is a custom post type display name and link
            if ($post_type != 'post') {

                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);

                //echo '<li class="item-cat item-custom-post-type-' . $post_type . '" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '"><span itemprop="name">' . $post_type_object->labels->name . '</span></a><meta itemprop="position" content="'.$schemaPosition++.'" /></li>';
            }

            $custom_tax_name = get_queried_object()->name;
            echo '<li class="active item-current item-archive" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><strong class="bread-current bread-archive"><h1>' . $custom_tax_name . '</h1></strong></li>';
        } elseif (is_single()) {

            // If post is a custom post type
            $post_type = get_post_type();

            // If it is a custom post type display name and link
            if ($post_type != 'post') {

                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);

                echo '<li class="item-cat item-custom-post-type-' . $post_type . '"  aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '" aria-label="' . $post_type_object->labels->name . '"><span itemprop="name">' . $post_type_object->labels->name . '</span></a><meta itemprop="position" content="' . $schemaPosition++ . '" /></li>';
            }
            if ($post_type == 'post') {
                $category = get_the_category($page_id_gt);
                $catid = $category[0]->cat_ID;
                $top_level_cat = smart_category_top_parent_id($catid);
                $top_level_cat_array = get_category($top_level_cat);
                if ($top_level_cat_array->name != "" && (single_cat_title('', false) != $top_level_cat_array->name)) {
                    echo '<li class="item-cat" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" class="bread-cat" href="' . get_home_url() . '/' . $top_level_cat_array->slug . '" title="' . $top_level_cat_array->name . '" aria-label="' . $top_level_cat_array->name . '"><span itemprop="name">' . $top_level_cat_array->name . '</span></a><meta itemprop="position" content="' . $schemaPosition++ . '" /></li>';
                }
                $categories = get_the_category();
                echo '<li aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url(get_category_link($categories[0]->term_id)) . '" aria-label="' . $categories[0]->name . '"><span itemprop="name">' . $categories[0]->name . '</span></a><meta itemprop="position" content="' . $schemaPosition++ . '" /></li>';
            }
            if ($post_type == 'blog') {
                $blog_terms = get_the_terms($post->ID, 'blog-category');
                if ($blog_terms && ! is_wp_error($blog_terms)) {
                    $cat_id = $blog_terms[0]->term_id;
                    $cat_nicename = $blog_terms[0]->slug;
                    $cat_link = get_term_link($blog_terms[0]->term_id, 'blog-category');
                    $cat_name = $blog_terms[0]->name;
                }
            }

            // Get post category info
            $category = get_the_category();

            if (!empty($category)) {

                // Get last category post is in
                $last_category = end(array_values($category));

                // Get parent any categories and create array
                $get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','), ',');
                $cat_parents = explode(',', $get_cat_parents);

                // Loop through parent categories and store in variable $cat_display
                $cat_display = '';
                foreach ($cat_parents as $parents) {
                    //$cat_display .= '<li class="item-cat" itemscope itemtype="http://schema.org/ListItem">' . $parents . '</li>';
                }
            }

            // If it's a custom post type within a custom taxonomy
            $taxonomy_exists = taxonomy_exists($custom_taxonomy);
            if (empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {

                $taxonomy_terms = get_the_terms($post->ID, $custom_taxonomy);
                $cat_id = $taxonomy_terms[0]->term_id;
                $cat_nicename = $taxonomy_terms[0]->slug;
                $cat_link = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
                $cat_name = $taxonomy_terms[0]->name;
            }

            // Check if the post is in a category
            if (!empty($last_category)) {
                echo $cat_display;
                //echo '<li class="active item-current item-' . $post->ID . '" itemscope itemtype="http://schema.org/ListItem"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';
                echo '<li class="active item-current item-' . $post->ID . '" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '" aria-hidden="true">...</strong></li>';

                // Else if post is in a custom taxonomy
            } elseif (!empty($cat_id)) {

                echo '<li class="item-cat item-cat-' . $cat_id . ' item-cat-' . $cat_nicename . '" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" class="bread-cat bread-cat-' . $cat_id . ' bread-cat-' . $cat_nicename . '" href="' . $cat_link . '" title="' . $cat_name . '" aria-label="' . $cat_name . '"><span itemprop="name">' . $cat_name . '</span></a><meta itemprop="position" content="' . $schemaPosition++ . '" /></li>';

                echo '<li class="active item-current item-' . $post->ID . '" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '" aria-hidden="true">' . get_the_title() . '</strong></li>';
            }
        } elseif (is_category()) {
            // POST TYPE - Referencje
            if (get_queried_object()->taxonomy == 'category' && get_queried_object()->term_id != wpmlIDTax(1, 'category') && get_queried_object()->term_id != wpmlIDTax(10, 'category')) {
                $category = get_term(wpmlIDTax(1, 'category'), 'category');
                echo '<li class="item-cat" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" class="bread-cat" href="' . get_term_link(wpmlIDTax(1, 'category'), 'category') . '" title="' . koncowki_luk_mod($category->name) . '"><span itemprop="name">' . koncowki_luk_mod($category->name) . '</span></a><meta itemprop="position" content="' . $schemaPosition++ . '" /></li>';
                $category = get_term(wpmlIDTax(10, 'category'), 'category');
                echo '<li class="item-cat" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" class="bread-cat" href="' . get_term_link(wpmlIDTax(10, 'category'), 'category') . '" title="' . koncowki_luk_mod($category->name) . '"><span itemprop="name">' . koncowki_luk_mod($category->name) . '</span></a><meta itemprop="position" content="' . $schemaPosition++ . '" /></li>';
            } else {
                $category = get_the_category($page_id_gt);
                $catid = $category[0]->cat_ID;
                $top_level_cat = smart_category_top_parent_id($catid);
                $top_level_cat_array = get_category($top_level_cat);
                if ($top_level_cat_array->name != "" && (single_cat_title('', false) != $top_level_cat_array->name)) {
                    echo '<li class="item-cat" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" class="bread-cat" href="' . get_home_url() . '/' . $top_level_cat_array->slug . '" title="' . $top_level_cat_array->name . '" aria-label="' . $top_level_cat_array->name . '"><span itemprop="name">' . $top_level_cat_array->name . '</span></a><meta itemprop="position" content="' . $schemaPosition++ . '" /></li>';
                }
            }
            // Category page
            echo '<li class="active item-current item-cat" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><strong class="bread-current bread-cat" aria-hidden="true">' . single_cat_title('', false) . '</strong></li>';

        } elseif (is_page()) {

            // Standard page
            if ($post->post_parent) {

                // If child page, get parents
                $anc = get_post_ancestors($post->ID);

                // Get parents in the right order
                $anc = array_reverse($anc);

                // Parent page loop
                foreach ($anc as $ancestor) {
                    $parents .= '<li class="item-parent item-parent-' . $ancestor . '" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" class="bread-parent bread-parent-' . $ancestor . '" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '" aria-label="' . get_the_title($ancestor) . '"><span itemprop="name">' . get_the_title($ancestor) . '</span></a><meta itemprop="position" content="' . $schemaPosition++ . '" /></li>';
                }

                // Display parent pages
                echo $parents;

                // Current page
                echo '<li class="active item-current item-' . $post->ID . '" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><strong title="' . get_the_title() . '"> ' . get_the_title() . '</strong></li>';
            } else {

                // Just display current page if not parents
                echo '<li class="active item-current item-' . $post->ID . '" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><strong class="bread-current bread-' . $post->ID . '"> ' . get_the_title() . '</strong></li>';
            }
        } elseif (is_tag()) {

            // Tag page
            // Get tag information
            $term_id = get_query_var('tag_id');
            $taxonomy = 'post_tag';
            $args = 'include=' . $term_id;
            $terms = get_terms($taxonomy, $args);
            $get_term_id = $terms[0]->term_id;
            $get_term_slug = $terms[0]->slug;
            $get_term_name = $terms[0]->name;

            // Display the tag name
            echo '<li class="active item-current item-tag-' . $get_term_id . ' item-tag-' . $get_term_slug . '" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><strong class="bread-current bread-tag-' . $get_term_id . ' bread-tag-' . $get_term_slug . '" aria-hidden="true" aria-label="' . $get_term_name . '">' . $get_term_name . '</strong></li>';
        } elseif (is_day()) {

            // Day archive
            // Year link
            echo '<li class="item-year item-year-' . get_the_time('Y') . '" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link(get_the_time('Y')) . '" title="' . get_the_time('Y') . '" aria-label="' . get_the_time('Y') . ' Archives"><span itemprop="name">' . get_the_time('Y') . ' Archives</span></a><meta itemprop="position" content="' . $schemaPosition++ . '" /></li>';

            // Month link
            echo '<li class="item-month item-month-' . get_the_time('m') . '" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" class="bread-month bread-month-' . get_the_time('m') . '" href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '" title="' . get_the_time('M') . '" aria-label="' . get_the_time('M') . ' Archives"><span itemprop="name">' . get_the_time('M') . ' Archives</span></a><meta itemprop="position" content="' . $schemaPosition++ . '" /></li>';

            // Day display
            echo '<li class="active item-current item-' . get_the_time('j') . '" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><strong class="bread-current bread-' . get_the_time('j') . '"> ' . get_the_time('jS') . ' ' . get_the_time('M') . ' Archives</strong></li>';
        } elseif (is_month()) {

            // Month Archive
            // Year link
            echo '<li class="item-year item-year-' . get_the_time('Y') . '" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link(get_the_time('Y')) . '" title="' . get_the_time('Y') . '" aria-label="' . get_the_time('Y') . ' Archives"><span itemprop="name">' . get_the_time('Y') . ' Archives</span></a><meta itemprop="position" content="' . $schemaPosition++ . '" /></li>';

            // Month display
            echo '<li class="active item-month item-month-' . get_the_time('m') . '" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><strong class="bread-month bread-month-' . get_the_time('m') . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</strong></li>';
        } elseif (is_year()) {

            // Display year archive
            echo '<li class="active item-current item-current-' . get_the_time('Y') . '" aria-hidden="true"><strong class="bread-current bread-current-' . get_the_time('Y') . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</strong></li>';
        } elseif (is_author()) {

            // Auhor archive
            // Get the author information
            global $author;
            $userdata = get_userdata($author);

            // Display author name
            echo '<li class="active item-current item-current-' . $userdata->user_nicename . '" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><strong class="bread-current bread-current-' . $userdata->user_nicename . '" title="' . $userdata->display_name . '">' . 'Author: ' . $userdata->display_name . '</strong></li>';
        } elseif (get_query_var('paged')) {

            // Paginated archives
            echo '<li class="active item-current item-current-' . get_query_var('paged') . '" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><strong class="bread-current bread-current-' . get_query_var('paged') . '" title="Page ' . get_query_var('paged') . '">' . __('Wyszukiwanie strona', 'wi') . ' ' . get_query_var('paged') . '</strong></li>';
        } elseif (is_search()) {

            // Search results page
            echo '<li class="active item-current item-current-' . get_search_query() . '" aria-hidden="true" itemscope itemtype="http://schema.org/ListItem"><strong class="bread-current bread-current-' . get_search_query() . '" title="' . __("Wyszukiwarka", "wi") . '">' . __("Wyszukiwarka", "wi") . '</strong></li>';
        } elseif (is_404()) {

            // 404 page
            echo '<li class="active item-current item-cat"><strong class="bread-current bread-cat" itemscope itemtype="http://schema.org/ListItem">' . 'Error 404' . '</strong></li>';
        }

        // POST TYPE ===========================================================

        // POST TYPE - baza-wiedzy
        if ($post_type == 'baza-wiedzy' && is_single()) {
            $cat_id_slug = get_query_var('blog-category');
            $cat_id_array = get_term_by('slug', $cat_id_slug, 'blog-category');
            echo '<li itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . get_term_link($cat_id_array->slug, 'blog-category') . '"><span itemprop="name">' . $cat_id_array->name . '</span></a><meta itemprop="position" content="' . $schemaPosition++ . '" /></li>';

            //echo '<li class="active item-current" aria-hidden="true"><strong class="bread-current" itemscope itemtype="http://schema.org/ListItem">' . wp_trim_words(get_the_title(), 3, '...' ) . '</strong></li>';
        }

        echo '</ul>';
    }
}

function slashAdd($url)
{
    $url = str_replace("//", "@", $url);
    $url = $url . "/";
    $url = str_replace("//", "/", $url);
    return str_replace("@", "//", $url);
}


// Phone - luk mod
function phoneUrl($value)
{
    return str_replace(
        " ",
        "",
        str_replace(
            "-",
            "",
            str_replace(
                "(",
                "",
                str_replace(")", "", $value),
            ),
        ),
    );
}


function set_posts_per_page_for_custom($query): void
{
    // Blog archive and blog category (e.g. /blog/kierowca/) – 9 posts per page
    if (!is_admin() && $query->is_main_query() && (is_post_type_archive('blog') || is_tax('blog-category'))) {
        $query->set('posts_per_page', '9');
    }
    if (!is_admin() && $query->is_main_query() && is_search()) {
        $query->set('posts_per_page', '10');
    }
}
add_action('pre_get_posts', 'set_posts_per_page_for_custom');


// Deregister Contact Form 7
add_action('wp_print_scripts', 'deregister_cf7_javascript', 100);
function deregister_cf7_javascript(): void
{
    if (get_the_ID() == wpmlID(2)) {
        wp_deregister_script('contact-form-7');
        wp_deregister_script('google-recaptcha');
    }
}
add_action('wp_print_styles', 'deregister_cf7_styles', 100);
function deregister_cf7_styles(): void
{
    if (get_the_ID() == wpmlID(2)) {
        wp_deregister_style('contact-form-7');
    }
}

// luk mod - alt img
function altIMG($item): void
{
    $itemALT = $item['name'];
    if ($item['alt'] != "") {
        $itemPieces = explode("|", $item['alt']);
        if (ICL_LANGUAGE_CODE == 'pl') {
            if ($itemPieces[0] != "") {
                $itemALT = $itemPieces[0];
            }
        } elseif (ICL_LANGUAGE_CODE == 'en') {
            if ($itemPieces[1] != "") {
                $itemALT = $itemPieces[1];
            } else {
                $itemALT = $itemPieces[0];
            }
        }
    }
    echo $itemALT;
}

function my_wpcf7_form_elements($html)
{
    $html = str_replace('&#8212;Please choose an option&#8212;', 'Choose', $html);
    $html = str_replace('&#8212;Proszę wybrać opcję&#8212;', 'Wybierz', $html);
    return $html;
}
add_filter('wpcf7_form_elements', 'my_wpcf7_form_elements');
