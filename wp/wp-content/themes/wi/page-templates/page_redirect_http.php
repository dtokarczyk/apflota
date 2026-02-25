<?php

declare(strict_types=1);

/*
Template Name: Przekierowanie do strony www
*/
$content_post = get_post(get_the_id());
wp_redirect($content_post->post_content);
