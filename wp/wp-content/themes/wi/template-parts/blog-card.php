<?php
/**
 * Shared blog card partial. Use inside the loop (global $post set).
 * Used on archive (blog list) and single (related posts).
 * Optional: set_query_var('blog_card_heading', 'h3') for related (default h2).
 */
if (! get_the_ID()) {
    return;
}
$card_heading = get_query_var('blog_card_heading', 'h2');
$card_heading = in_array($card_heading, [ 'h2', 'h3', 'h4' ], true) ? $card_heading : 'h2';
?>
<article class="blog-card">
	<?php
    $terms = get_the_terms(get_the_ID(), 'blog-category');
$category_term = ($terms && ! is_wp_error($terms)) ? $terms[0] : null;
$category_url = $category_term ? get_term_link($category_term, 'blog-category') : '';
if ($category_term && is_wp_error($category_url)) {
    $category_url = add_query_arg('kategoria', $category_term->slug, get_post_type_archive_link('blog'));
}
?>
	<a href="<?php the_permalink(); ?>" class="blog-card-link blog-card-link-image">
		<div class="blog-card-image">
			<?php
        $small_img = get_field('blog_small_image');
if (! empty($small_img['sizes']['blog-small'])) :
    ?>
				<img src="<?php echo esc_url($small_img['sizes']['blog-small']); ?>" alt="<?php the_title_attribute(); ?>" class="img-full">
			<?php elseif (has_post_thumbnail()) : ?>
				<?php the_post_thumbnail('blog-small', [ 'class' => 'img-full' ]); ?>
			<?php else : ?>
				<div class="blog-card-image-placeholder"></div>
			<?php endif; ?>
		</div>
	</a>
	<div class="blog-card-body">
		<?php if ($category_term && $category_url && ! is_wp_error($category_url)) : ?>
			<a href="<?php echo esc_url($category_url); ?>" class="blog-card-category-link"><?php echo esc_html($category_term->name); ?></a>
		<?php elseif ($category_term) : ?>
			<span class="blog-card-category"><?php echo esc_html($category_term->name); ?></span>
		<?php endif; ?>
		<a href="<?php the_permalink(); ?>" class="blog-card-link blog-card-link-content">
			<<?php echo $card_heading; ?> class="blog-card-title"><?php the_title(); ?></<?php echo $card_heading; ?>>
			<?php if (get_the_excerpt()) : ?>
				<p class="blog-card-excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 20)); ?></p>
			<?php endif; ?>
			<span class="blog-card-meta">
				<?php echo get_the_date(); ?>
				<?php
    $reading_time = get_field('blog_reading_time');
if ($reading_time) :
    ?>
					<span class="blog-card-reading-time"> – <?php echo esc_html($reading_time); ?> <?php esc_html_e('do czytania', 'wi'); ?></span>
				<?php endif; ?>
			</span>
		</a>
	</div>
</article>
