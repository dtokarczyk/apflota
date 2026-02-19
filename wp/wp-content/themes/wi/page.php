<?php
get_header();
setup_postdata($post);
?>

<?php if (post_password_required()) { ?>
    <div class="containerSmall containerPassword"><?php echo get_the_password_form(); ?></div>
<?php } else { ?>
    <?php the_content(); ?>
    <?php get_template_part('content-editor/editor'); ?>
<?php } ?>

<?php get_footer(); ?>