<?php

declare(strict_types=1);


/*******************
JS + CSS + FONTS include luk mod
********************/
function ac_main_config()
{
    $domain = $_SERVER['SERVER_NAME'];
    $debug_domain = ['192.168.1.5', 'localhost', 'webidea-dev.pl', 'webidea-dev2.pl'];
    $debug = false;
    if (in_array($domain, $debug_domain)) {
        $debug = true;
    }
    $settings = [
        'debug' => $debug,
    ];
    return $settings;
}

add_action('init', 'ac_modify_jquery');
function ac_modify_jquery(): void
{
    if (!is_admin()) {
        wp_deregister_script('jquery');
        wp_register_script('jquery', get_template_directory_uri() . '/src/js/vendor/jquery-1.12.4.min.js', false, '1.12.4');
        wp_enqueue_script('jquery');
    }
}

function wi_wp_enqueue_scripts(): void
{

    //wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Albert+Sans:wght@100..900&display=swap');
    //wp_enqueue_style( 'google-fonts-2', 'https://fonts.googleapis.com/css2?family=Inter&display=swap');

    $ext = '.js?v=7';
    if (ac_main_config()['debug'] === true) {
        $ext = '-debug.js?v=' . time();
        wp_enqueue_style('styles', get_template_directory_uri() . '/style.css?v=' . time());
    } else {
        wp_enqueue_style('styles', get_template_directory_uri() . '/style.css?v=7');
    }
    wp_register_script('vendor', get_stylesheet_directory_uri() . '/js/vendor.js', ['jquery']);
    //wp_register_script( 'isotope', get_stylesheet_directory_uri() . '/js/isotope.pkgd.min.js', array('jquery'));
    wp_register_script('lazyload', get_stylesheet_directory_uri() . '/js/jquery.lazy.min.js', ['jquery']);
    wp_register_script('lazyload-plugins', get_stylesheet_directory_uri() . '/js/jquery.lazy.plugins.min.js', ['jquery','lazyload']);
    wp_register_script('addIndicators', get_template_directory_uri() . '/js/debug.addIndicators.min.js', ['jquery']);
    wp_register_script('TweenMax', get_template_directory_uri() . '/js/TweenMax.min.js', ['jquery']);
    wp_register_script('ScrollMagic', get_template_directory_uri() . '/js/ScrollMagic.min.js', ['jquery']);
    wp_register_script('animation.gsap', get_template_directory_uri() . '/js/animation.gsap.min.js', ['jquery', 'ScrollMagic']);

    wp_register_script('jquery3', get_template_directory_uri() . '/js/jquery-3.3.1.min.js');
    wp_enqueue_script('fancybox', get_template_directory_uri() . '/js/jquery.fancybox.min.js', ['jquery3']);

    wp_register_script('main', get_template_directory_uri() . '/js/main' . $ext, [
        'jquery',
        'vendor',
        //'isotope',
        'lazyload',
        'lazyload-plugins',
        'TweenMax',
        'ScrollMagic',
        'animation.gsap',
        'addIndicators',
    ]);
    wp_enqueue_script('main');

    // Blog: conditional script and nonce for like/dislike
    if (is_post_type_archive('blog') || is_singular('blog') || is_tax('blog-category')) {
        wp_enqueue_script(
            'blog-js',
            get_template_directory_uri() . '/js/blog.js',
            [ 'jquery' ],
            '1.0',
            true,
        );
        wp_localize_script('blog-js', 'blogAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('blog_like_dislike'),
        ]);
    }
}
add_action('wp_enqueue_scripts', 'wi_wp_enqueue_scripts');
