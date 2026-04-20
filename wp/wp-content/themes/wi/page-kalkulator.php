<?php

/**
 * Template Name: Kalkulator Page
 * Description: Calculator landing page rendered as a regular WordPress page.
 */

get_header();
setup_postdata($post);
?>

<?php get_template_part('template-parts/kalkulator', 'title-banner'); ?>

<?php get_template_part('inc/search'); ?>

<?php get_footer(); ?>
