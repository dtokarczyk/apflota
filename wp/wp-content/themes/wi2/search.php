<?php get_header(); setup_postdata($post); $currentlang = get_bloginfo('language'); ?>

<div class="sectionSearch">
    <div id="contentEditor">
        <div class="containerBig">
            <h4><?php echo __('Wynik wyszukiwania dla:','wi'); ?> <strong><?php echo get_search_query(); ?></strong></h4>
            <br />
            <div class="row">
                <?php if (have_posts()) { ?>
                    <ul>
                        <?php while(have_posts()): the_post(); ?>
                        <li><a href="<?php the_permalink(); ?>" aria-label="<?php echo sanitize_text_field(get_the_title()); ?>">
                            <?php the_title(); ?>
                        </a></li>
                        <?php endwhile; ?>
                    </ul>
                    <br />
                <?php } else { ?>
                    <br />
                    <?php echo __('Brak wyników wyszukiwania.','wi'); ?>
                <?php } ?>
            </div>
       </div>
    </div>
    <div class="containerBig">
        <?php wpbeginner_numeric_posts_nav(); ?>
        <?php wp_reset_query(); ?>
    </div>
</div>

<?php get_footer(); ?>