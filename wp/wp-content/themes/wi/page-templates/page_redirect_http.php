<?php
/*
Template Name: Przekierowanie do strony www
*/
$content_post = get_post(get_the_id());
wp_redirect($content_post->post_content);
?>