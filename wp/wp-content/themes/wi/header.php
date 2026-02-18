<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_template_directory_uri(); ?>/images/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_template_directory_uri(); ?>/images/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_template_directory_uri(); ?>/images/favicons/favicon-16x16.png">
    <link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/images/favicons/site.webmanifest">
    <link rel="mask-icon" href="<?php echo get_template_directory_uri(); ?>/images/favicons/safari-pinned-tab.svg" color="#333333">
    <meta name="apple-mobile-web-app-title" content="AP FLota">
    <meta name="application-name" content="AP FLota">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <?php wp_head(); ?>

    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-MNW9RQCM');
    </script>
    <!-- End Google Tag Manager -->
</head>

<body <?php body_class(); ?> lang="<?php if (function_exists('icl_object_id')) {
                                        echo ICL_LANGUAGE_CODE;
                                    } ?>" path="<?php echo home_url(); ?>">

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MNW9RQCM"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <div id="sectionLoader"></div>
    hello world
    <header>
        <div class="sectionHeaderBox displayFlex flexXbeetwen flexYcenter">
            <div class="sectionHeaderLogo displayFlex flexXstart flexYcenter">
                <a href="<?php echo home_url(); ?>">
                    <img src="<?php echo get_field('logo', wpmlID(2)); ?>" class="svg img-responsive" />
                </a>
            </div>
            <div class="sectionHeaderMenu">
                <div class="sectionHeaderMenuBox sectionHeaderMenuBoxCenter displayFlex flexXend flexYcenter">
                    <ul class="sectionHeaderMenuUl sectionHeaderMenuUl1">
                        <?php wp_nav_menu(array(
                            'container' => '',
                            'theme_location' => 'menu',
                            'items_wrap' => '%3$s',
                            'depth' => 2,
                            'walker' => new BS3_Walker_Nav_Menu
                        )); ?>
                    </ul>
                    <ul class="sectionHeaderMenuUl sectionHeaderMenuUl2">
                        <?php if (get_field('telefon', wpmlID(2)) != "") { ?>
                            <a href="tel:<?php echo str_replace("-", "", str_replace(" ", "", str_replace("-", "", str_replace("(", "", str_replace(")", "", get_field('telefon', wpmlID(2))))))); ?>" class="buttonPhone displayFlex flexXcenter flexYcenter">
                                <strong>24H</strong>
                                <img src="<?php echo get_template_directory_uri(); ?>/images/phone.svg" class="svg img-responsive" alt="<?php echo __('telefon', 'wi'); ?>" />
                                <?php echo get_field('telefon', wpmlID(2)); ?>
                            </a>
                        <?php } ?>
                        <?php if (have_rows("socialmedia", wpmlID(2))) { ?>
                            <div class="sectionSocialMedia">
                                <?php while (have_rows("socialmedia", wpmlID(2))) {
                                    the_row(); ?>
                                    <a href="<?php echo get_sub_field('link'); ?>" target="_blank">
                                        <img class="svg" src="<?php echo get_sub_field('ikona'); ?>" />
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if (get_field('panel_klienta_-_odnosnik', wpmlID(2)) != "") { ?>
                            <a href="<?php echo get_field('panel_klienta_-_odnosnik', wpmlID(2)); ?>" class="button buttonTransparent buttonIconLeft displayFlex flexXcenter flexYcenter">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/panel.svg" class="svg img-responsive" alt="<?php echo get_field('panel_klienta_-_nazwa', wpmlID(2)); ?>" />
                                <?php echo get_field('panel_klienta_-_nazwa', wpmlID(2)); ?>
                            </a>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="sectionHeaderMenuButton displayFlex flexXstart flexYcenter">
                <div class="sectionHeaderMenuHamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>

    <?php wp_reset_query(); ?>

    <div class="topBannerNone"></div>

    <?php if (have_rows("submenu_anchor")) { ?>
        <div class="sectionTopbannerSubMenu">
            <div class="containerBig">
                <div class="sectionTopbannerSubMenuBox displaFlex flexWrap flexXbetween flexYcenter">
                    <?php while (have_rows("submenu_anchor")) {
                        the_row(); ?>
                        <a class="button buttonTransparent displaFlex flexXcenter flexYcenter" href="#<?php echo sanitize_title(get_sub_field('anchor')); ?>" aria-label="<?php echo __('przycisk', 'wi'); ?> - <?php echo get_sub_field('anchor'); ?>">
                            <?php echo get_sub_field('anchor'); ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>


    <?php if (is_front_page() || is_home() || get_field('topbanner_-_typ') == 1) {
    } else { ?>
        <div id="topBanner" class="<?php if (get_post_type() == 'post') { ?>topBannerOffer<?php } ?><?php if (get_post_type() == 'post' && is_single()) { ?>Single<?php } ?>">
            <div class="topBanner">
                <div class="containerBig">
                    <div class="topBannerContainer displayFlex flexWrap flexXstart flexYstretch">
                        <div class="topBannerTitle displayFlex flexXstart flexYcenter">
                            <div>
                                <?php if (get_field('topbanner_-_tresc') != "") { ?>
                                    <?php echo get_field('topbanner_-_tresc'); ?>
                                <?php } else { ?>
                                    <?php
                                    if (is_404()) {
                                        echo "<h1><p>404</p></h1>";
                                    } elseif (is_search()) {
                                        echo "<h1><p>" . __('Szukaj', 'wi') . "</p></h1>";
                                    } elseif (get_post_type() == 'post') {
                                        if (is_single()) {
                                            echo "<h1><p>" . get_the_title() . "</p></h1>";
                                        } else {
                                            if (get_field('wyszukiwarka_naglowek', wpmlID(2)) != "") {
                                                echo "<h1><p>" . get_field('wyszukiwarka_naglowek', wpmlID(2)) . "</p></h1>";
                                            } else {
                                                echo "<h1><p>" . __('Oferta <strong>AP Flota</strong>', 'wi') . "</p></h1>";
                                            }
                                        }
                                    } else {
                                        echo "<h1>" . get_the_title() . "</h1>";
                                    }
                                    ?>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if (get_post_type() == 'post') {
                        } else { ?>
                            <div class="topBannerImg displayFlex flexXstart flexYcenter">
                                <?php $topbanner = get_template_directory_uri() . '/images/topBanner.jpg' ?>
                                <?php if (get_field('topbanner') != "") { ?>
                                    <?php $topbanner = get_field('topbanner');
                                    $topbanner = $topbanner["sizes"]["topbanner-712x440"]; ?>
                                <?php } ?>
                                <img src="<?php echo $topbanner; ?>" class="img-full topBannerImg" alt="topbanner">
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php if (get_post_type() == 'post') {
                } else { ?>
                    <img src="<?php echo get_template_directory_uri() . '/images/topbanner_bg.png'; ?>" class="img-full" alt="topbanner">
                <?php } ?>
            </div>
        </div>
    <?php } ?>