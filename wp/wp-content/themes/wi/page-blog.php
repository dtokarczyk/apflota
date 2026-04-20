<?php

/**
 * Template Name: Blog Page
 * Description: Blog landing page rendered as a regular WordPress page.
 */
get_header();

$hero_title    = get_field('blog_hero_title', 'option');
$hero_subtitle = get_field('blog_hero_subtitle', 'option');
$hero_image    = get_field('blog_hero_image', 'option');
$current_slug  = get_query_var('kategoria');
$is_blog_main  = empty($current_slug);

$categories = get_terms([
    'taxonomy'   => 'blog-category',
    'hide_empty' => true,
]);

$paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
$query_args = [
    'post_type'           => 'blog',
    'post_status'         => 'publish',
    'posts_per_page'      => 9,
    'paged'               => $paged,
    'ignore_sticky_posts' => true,
];

if (! empty($current_slug)) {
    $query_args['tax_query'] = [
        [
            'taxonomy' => 'blog-category',
            'field'    => 'slug',
            'terms'    => $current_slug,
        ],
    ];
}

$blog_query = new WP_Query($query_args);
?>

<section id="blogListing" class="blog-listing">
	<?php if ($is_blog_main) : ?>
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
	<?php endif; ?>

	<div class="blog-listing-container">
		<?php if (! $is_blog_main && $current_slug) : ?>
			<?php
            $current_term = get_term_by('slug', $current_slug, 'blog-category');
		    if ($current_term && ! is_wp_error($current_term)) :
		        ?>
				<ol id="breadcrumbs" class="breadcrumb displaFlex flexWrap flexXstart flexYcenter" itemscope itemtype="http://schema.org/BreadcrumbList">
					<li class="item-home" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" class="bread-link bread-home" href="<?php echo esc_url(get_home_url()); ?>" aria-label="<?php esc_attr_e('Strona główna', 'wi'); ?>"><span itemprop="name"><?php esc_html_e('Strona główna', 'wi'); ?></span></a>
						<meta itemprop="position" content="1" />
					</li>
					<li itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="<?php echo esc_url(wi_get_blog_home_url()); ?>" aria-label="<?php esc_attr_e('Blog', 'wi'); ?>"><span itemprop="name"><?php esc_html_e('Blog', 'wi'); ?></span></a>
						<meta itemprop="position" content="2" />
					</li>
					<li class="active item-current item-cat" itemscope itemtype="http://schema.org/ListItem"><strong class="bread-current bread-cat"><?php echo esc_html($current_term->name); ?></strong>
						<meta itemprop="position" content="3" />
					</li>
				</ol>
			<?php endif; ?>
		<?php endif; ?>

		<nav class="blog-category-bar displayFlex flexWrap flexXstart flexYcenter">
			<a href="<?php echo esc_url(wi_get_blog_home_url()); ?>" class="blog-category-link <?php echo empty($current_slug) ? 'blog-category-link-active' : ''; ?>"><?php esc_html_e('Wszystkie artykuły', 'wi'); ?></a>
			<?php if ($categories && ! is_wp_error($categories)) : ?>
				<?php foreach ($categories as $term) : ?>
					<?php
		                $term_link = get_term_link($term, 'blog-category');
				    if (is_wp_error($term_link)) {
				        $term_link = add_query_arg('kategoria', $term->slug, wi_get_blog_home_url());
				    }
				    ?>
					<a href="<?php echo esc_url($term_link); ?>" class="blog-category-link <?php echo $current_slug === $term->slug ? 'blog-category-link-active' : ''; ?>"><?php echo esc_html($term->name); ?></a>
				<?php endforeach; ?>
			<?php endif; ?>
		</nav>

		<div class="blog-grid">
			<?php if ($blog_query->have_posts()) : ?>
				<?php while ($blog_query->have_posts()) : $blog_query->the_post(); ?>
					<?php get_template_part('template-parts/blog', 'card'); ?>
				<?php endwhile; ?>
			<?php else : ?>
				<p class="blog-no-posts"><?php esc_html_e('Brak artykułów.', 'wi'); ?></p>
			<?php endif; ?>
		</div>

		<?php
        $original_wp_query = $GLOBALS['wp_query'];
$GLOBALS['wp_query'] = $blog_query;
wpbeginner_numeric_posts_nav();
$GLOBALS['wp_query'] = $original_wp_query;
wp_reset_postdata();
?>
	</div>
</section>

<?php get_footer(); ?>
