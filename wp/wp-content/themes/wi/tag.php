<?php get_header();
setup_postdata($post); ?>

<section id="sectionBlog" class="col-lg-16 col-md-16 col-sm-16 col-xs-16 globalPaddingDefaultPage">
    <div class="container">
        <section class="col-lg-16 col-md-16 col-sm-16 col-xs-16">
            <div class="sectionBlog">
                <?php
                    if (have_posts()) {
                        $i = 0;
                        while (have_posts()) {
                            the_post();
                            ?>
                        <a href="<?php echo get_permalink(get_the_ID()); ?>" class="itemBlog">
                            <span class="col-lg-8 col-md-8 col-sm-8 col-xs-16 sectionBlogItemPadding sectionBlogItemPadding<?php if ($i % 2 == 0) {
                                echo "Left";
                            } else {
                                echo "Right";
                            } ?>">
                                <span class="col-lg-16 col-md-16 col-sm-16 col-xs-16 sectionBlogItem">
                                    <span class="sectionBlogItemContent">
                                        <span class="sectionBlogItemContentTitle"><h3><?php echo get_the_title(); ?></h3></span>
                                        <span class="sectionBlogItemContentDate"><?php echo get_the_date(); ?></span>
                                    </span>
                                    <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'blog'); ?>" class="img-responsive"/>
                                </span>
                            </span>
                        </a>
                <?php
                        $i++;
                        }
                    }
?>
            </div>
        </section>
        <?php wp_reset_query(); ?>
    </div>
</section>

<?php get_footer(); ?>