<?php
/*
Template Name: Przkierowanie do 1 dziecka
*/
if (have_posts()) {
  while (have_posts()) {
    the_post();
    $pagekids =
    get_pages("child_of=".$post->ID."&sort_column=menu_order");
    $firstchild = $pagekids[0];
    wp_redirect(get_permalink($firstchild->ID));
  }
}
?>