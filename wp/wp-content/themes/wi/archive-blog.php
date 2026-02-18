<?php
/**
 * Archive template for blog CPT. Hero, category bar, grid 3/2/1, pagination.
 */
get_header();

$blog_page_id = function_exists('wpmlID') ? wpmlID(2) : 2;
$hero_title   = get_field('blog_hero_title', $blog_page_id);
$hero_subtitle = get_field('blog_hero_subtitle', $blog_page_id);
$hero_image   = get_field('blog_hero_image', $blog_page_id);
$current_slug  = get_query_var('kategoria');

$categories = get_terms(array(
    'taxonomy'   => 'blog-category',
    'hide_empty' => true,
));
?>

<section id="blogArchive" class="blog-archive">
	<!-- Hero -->
	<div class="blog-hero">
		<?php if (! empty($hero_image['sizes']) && isset($hero_image['sizes']['blog-hero'])) : ?>
			<div class="blog-hero-bg" style="background-image: url('<?php echo esc_url($hero_image['sizes']['blog-hero']); ?>');"></div>
		<?php elseif (! empty($hero_image['url'])) : ?>
			<div class="blog-hero-bg" style="background-image: url('<?php echo esc_url($hero_image['url']); ?>');"></div>
		<?php endif; ?>
		<div class="blog-hero-overlay"></div>
		<div class="containerBig">
			<div class="blog-hero-inner displayFlex flexWrap flexXstart flexYcenter">
				<div class="blog-hero-content">
					<?php if ($hero_title) : ?>
						<h1 class="blog-hero-title"><?php echo esc_html($hero_title); ?></h1>
					<?php else : ?>
						<h1 class="blog-hero-title"><?php esc_html_e('Blog', 'wi'); ?></h1>
					<?php endif; ?>
					<?php if ($hero_subtitle) : ?>
						<div class="blog-hero-subtitle"><?php echo wp_kses_post(nl2br($hero_subtitle)); ?></div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="containerBig">
		<!-- Category bar -->
		<nav class="blog-category-bar displayFlex flexWrap flexXstart flexYcenter">
			<a href="<?php echo esc_url(get_post_type_archive_link('blog')); ?>" class="blog-category-link <?php echo empty($current_slug) ? 'blog-category-link-active' : ''; ?>"><?php esc_html_e('Wszystkie wpisy', 'wi'); ?></a>
			<?php if ($categories && ! is_wp_error($categories)) : ?>
				<?php foreach ($categories as $term) : ?>
					<a href="<?php echo esc_url(add_query_arg('kategoria', $term->slug, get_post_type_archive_link('blog'))); ?>" class="blog-category-link <?php echo $current_slug === $term->slug ? 'blog-category-link-active' : ''; ?>"><?php echo esc_html($term->name); ?></a>
				<?php endforeach; ?>
			<?php endif; ?>
		</nav>

		<!-- Posts grid -->
		<div class="blog-grid displayFlex flexWrap flexXstart flexYstretch">
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<article class="blog-card">
						<a href="<?php the_permalink(); ?>" class="blog-card-link displayFlex flexWrap flexXstart flexYstretch">
							<div class="blog-card-image">
								<?php
                                $small_img = get_field('blog_small_image');
				    if (! empty($small_img['sizes']['blog-small'])) : ?>
									<img src="<?php echo esc_url($small_img['sizes']['blog-small']); ?>" alt="<?php the_title_attribute(); ?>" class="img-full">
								<?php elseif (has_post_thumbnail()) : ?>
									<?php the_post_thumbnail('blog-small', array( 'class' => 'img-full' )); ?>
								<?php else : ?>
									<div class="blog-card-image-placeholder"></div>
								<?php endif; ?>
							</div>
							<div class="blog-card-body">
								<?php
				    $terms = get_the_terms(get_the_ID(), 'blog-category');
				    if ($terms && ! is_wp_error($terms)) : ?>
									<span class="blog-card-category"><?php echo esc_html($terms[0]->name); ?></span>
								<?php endif; ?>
								<h2 class="blog-card-title"><?php the_title(); ?></h2>
								<span class="blog-card-meta">
									<?php echo get_the_date(); ?>
									<?php
				        $reading_time = get_field('blog_reading_time');
				    if ($reading_time) : ?>
										<span class="blog-card-reading-time"> – <?php echo esc_html($reading_time); ?> <?php esc_html_e('do czytania', 'wi'); ?></span>
									<?php endif; ?>
								</span>
							</div>
						</a>
					</article>
				<?php endwhile; ?>
			<?php else : ?>
				<p class="blog-no-posts"><?php esc_html_e('Brak wpisów.', 'wi'); ?></p>
			<?php endif; ?>
		</div>

		<?php wpbeginner_numeric_posts_nav(); ?>
	</div>
</section>

<?php get_footer(); ?>
