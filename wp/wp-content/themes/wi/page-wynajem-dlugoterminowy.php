<?php

/**
 * Template Name: Wynajem długoterminowy
 * Description: Long-term rental offer listing (filters + grid).
 */

get_header();
setup_postdata($post);
?>

<?php $pageID = get_the_ID(); ?>

<?php get_template_part('inc/search'); ?>

<?php
// inc/search-list.php nadpisuje globalny $wp_query (linia 103), przez co get_the_ID()
// zwraca ID ostatniej oferty zamiast ID tej strony. Reset przywraca oryginalny kontekst.
wp_reset_query();
setup_postdata($post);
?>
<?php include(get_template_directory() . '/content-editor/editor.php'); ?>

<?php get_footer(); ?>
