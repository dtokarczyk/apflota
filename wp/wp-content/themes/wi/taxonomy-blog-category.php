<?php

/**
 * Taxonomy archive for blog-category. Same layout as blog archive (category view).
 * Used for pretty URLs: blog/term-slug/
 */
get_header();

$current_term = get_queried_object();
if (! $current_term || ! isset($current_term->slug)) {
	// Fallback: redirect to blog archive
	wp_safe_redirect(get_post_type_archive_link('blog'), 302);
	exit;
}

$hero_title   = get_field('blog_hero_title', 'option');
$hero_subtitle = get_field('blog_hero_subtitle', 'option');
$hero_image   = get_field('blog_hero_image', 'option');
$current_slug  = $current_term->slug;
$is_blog_main  = false; // Category view – no hero

$categories = get_terms([
	'taxonomy'   => 'blog-category',
	'hide_empty' => true,
]);
?>

<section id="blogArchive" class="blog-archive">
	<div class="blog-archive-container">
		<ol id="breadcrumbs" class="breadcrumb displaFlex flexWrap flexXstart flexYcenter" itemscope itemtype="http://schema.org/BreadcrumbList">
			<li class="item-home" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" class="bread-link bread-home" href="<?php echo esc_url(get_home_url()); ?>" aria-label="<?php esc_attr_e('Strona główna', 'wi'); ?>"><span itemprop="name"><?php esc_html_e('Strona główna', 'wi'); ?></span></a>
				<meta itemprop="position" content="1" />
			</li>
			<li itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="<?php echo esc_url(get_post_type_archive_link('blog')); ?>" aria-label="<?php esc_attr_e('Blog', 'wi'); ?>"><span itemprop="name"><?php esc_html_e('Blog', 'wi'); ?></span></a>
				<meta itemprop="position" content="2" />
			</li>
			<li class="active item-current item-cat" itemscope itemtype="http://schema.org/ListItem"><strong class="bread-current bread-cat"><?php echo esc_html($current_term->name); ?></strong>
				<meta itemprop="position" content="3" />
			</li>
		</ol>

		<!-- Category bar -->
		<nav class="blog-category-bar displayFlex flexWrap flexXstart flexYcenter">
			<a href="<?php echo esc_url(get_post_type_archive_link('blog')); ?>" class="blog-category-link"><?php esc_html_e('Wszystkie posty blogowe', 'wi'); ?></a>
			<?php if ($categories && ! is_wp_error($categories)) : ?>
				<?php foreach ($categories as $term) : ?>
					<?php
					$term_link = get_term_link($term, 'blog-category');
					if (is_wp_error($term_link)) {
						$term_link = get_post_type_archive_link('blog');
					}
					?>
					<a href="<?php echo esc_url($term_link); ?>" class="blog-category-link <?php echo $current_slug === $term->slug ? 'blog-category-link-active' : ''; ?>"><?php echo esc_html($term->name); ?></a>
				<?php endforeach; ?>
			<?php endif; ?>
		</nav>

		<!-- Posts grid -->
		<div class="blog-grid">
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<?php get_template_part('template-parts/blog', 'card'); ?>
				<?php endwhile; ?>
			<?php else : ?>
				<p class="blog-no-posts"><?php esc_html_e('Brak postów blogowych.', 'wi'); ?></p>
			<?php endif; ?>
		</div>

		<?php wpbeginner_numeric_posts_nav(); ?>
	</div>
</section>

<?php get_footer(); ?>