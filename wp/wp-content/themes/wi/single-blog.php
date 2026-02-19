<?php

/**
 * Single template for blog CPT. Hero, summary, TOC, content, like/dislike, related, social share, sticky sidebar.
 */
get_header();

while (have_posts()) : the_post();
    $post_id       = get_the_ID();
    $blog_page_id  = function_exists('wpmlID') ? wpmlID(2) : 2;
    $big_image     = get_field('blog_big_image');
    $reading_time   = get_field('blog_reading_time');
    if (empty(trim((string) $reading_time))) {
        $reading_time = '5 min';
    }
    $summary        = get_field('blog_summary');
    $likes          = (int) get_field('blog_likes');
    $dislikes       = (int) get_field('blog_dislikes');
    $banner_override = get_field('blog_sidebar_banner_override');
    $sidebar_banner  = (is_array($banner_override) && ! empty($banner_override['url'])) ? $banner_override : get_field('blog_sidebar_banner', 'option');
    $link_override   = get_field('blog_sidebar_banner_link_override');
    $sidebar_link    = (is_string($link_override) && $link_override !== '') ? $link_override : get_field('blog_sidebar_banner_link', 'option');

    $content = apply_filters('the_content', get_the_content());
    $toc_data = wi_generate_toc($content);
    $content_with_ids = $toc_data['content'];
    $toc_html = $toc_data['toc_html'];

    $terms = get_the_terms($post_id, 'blog-category');
    $category_name = ($terms && ! is_wp_error($terms)) ? $terms[0]->name : '';
    $share_url = urlencode(get_permalink());
    $share_title = urlencode(get_the_title());
    $share_text = urlencode(wp_trim_words($summary ?: get_the_excerpt(), 20));
    $fb_share = 'https://www.facebook.com/sharer/sharer.php?u=' . $share_url;
    $linkedin_share = 'https://www.linkedin.com/sharing/share-offsite/?url=' . $share_url;
    ?>

<section id="blogSingle" class="blog-single">
	<div class="blog-single-container">
		<?php if (function_exists('qt_custom_breadcrumbs')) {
		    qt_custom_breadcrumbs();
		} ?>
	</div>
		<!-- Hero: full-width image, title in container -->
		<?php if (! empty($big_image['sizes']['blog-hero']) || ! empty($big_image['url'])) : ?>
			<div class="blog-single-hero">
				<div class="blog-single-hero-inner">
					<?php if (! empty($big_image['sizes']['blog-hero'])) : ?>
						<img src="<?php echo esc_url($big_image['sizes']['blog-hero']); ?>" alt="<?php the_title_attribute(); ?>" class="blog-single-hero-img">
					<?php else : ?>
						<img src="<?php echo esc_url($big_image['url']); ?>" alt="<?php the_title_attribute(); ?>" class="blog-single-hero-img">
					<?php endif; ?>
					<div class="blog-single-hero-overlay"></div>
					<div class="blog-single-hero-title-wrap">
						<h1 class="blog-single-hero-title"><?php the_title(); ?></h1>
					</div>
				</div>
			</div>
		<?php endif; ?>
	<?php if (empty($big_image['sizes']['blog-hero']) && empty($big_image['url'])) : ?>
		<div class="blog-single-container">
			<h1 class="blog-single-title"><?php the_title(); ?></h1>
		</div>
	<?php endif; ?>

	<div class="blog-single-container">
			<div class="blog-single-layout">
				<!-- Main content -->
				<div class="blog-single-main">
					<div class="blog-single-summary-meta displayFlex flexWrap flexXstart flexYcenter flexXbetween">
						<div class="blog-single-summary-meta-left displayFlex flexWrap flexXstart flexYcenter">
							<?php if ($reading_time) : ?>
								<span class="blog-single-reading-time"><?php echo esc_html($reading_time); ?> <?php esc_html_e('do czytania', 'wi'); ?></span>
							<?php endif; ?>
							<?php if ($terms && ! is_wp_error($terms)) : ?>
								<?php
		                        $category_term = $terms[0];
							    $category_link = get_term_link($category_term, 'blog-category');
							    ?>
								<?php if (! is_wp_error($category_link)) : ?>
									<a href="<?php echo esc_url($category_link); ?>" class="blog-single-category-link"><?php echo esc_html($category_term->name); ?></a>
								<?php else : ?>
									<span class="blog-single-category"><?php echo esc_html($category_name); ?></span>
								<?php endif; ?>
							<?php elseif ($category_name) : ?>
								<span class="blog-single-category"><?php echo esc_html($category_name); ?></span>
							<?php endif; ?>
							<span class="blog-single-date"><?php echo get_the_date(); ?></span>
						</div>
						<div class="blog-single-share-inline">
							<span class="blog-single-share-label"><?php esc_html_e('Udostępnij to', 'wi'); ?></span>
							<div class="blog-single-share displayFlex flexWrap flexXstart flexYcenter">
								<a href="<?php echo esc_url($fb_share); ?>" target="_blank" rel="noopener noreferrer" class="blog-single-share-icon blog-single-share-fb" aria-label="Facebook">f</a>
								<a href="<?php echo esc_url($linkedin_share); ?>" target="_blank" rel="noopener noreferrer" class="blog-single-share-icon blog-single-share-linkedin" aria-label="LinkedIn">in</a>
							</div>
						</div>
					</div>
					<?php if ($summary) : ?>
						<div class="blog-single-summary-box">
							<div class="blog-single-summary"><?php echo wp_kses_post(nl2br($summary)); ?></div>
						</div>
					<?php endif; ?>
					<?php if ($toc_html) : ?>
						<nav class="blog-toc" aria-label="<?php esc_attr_e('Spis treści', 'wi'); ?>">
							<h2 class="blog-toc-title"><?php esc_html_e('Spis treści', 'wi'); ?></h2>
							<?php echo $toc_html; ?>
						</nav>
					<?php endif; ?>
					<div class="blog-single-content entry-content">
						<?php echo $content_with_ids; ?>
					</div>
					<div class="blog-single-feedback displayFlex flexWrap flexXstart flexYcenter">
						<span class="blog-single-feedback-label"><?php esc_html_e('Czy artykuł był pomocny?', 'wi'); ?></span>
						<button type="button" class="blog-like-btn button buttonTransparent displayFlex flexXcenter flexYcenter" data-post-id="<?php echo absint($post_id); ?>" data-type="like" aria-label="<?php esc_attr_e('Tak', 'wi'); ?>">
							<svg class="blog-feedback-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
							<span class="blog-like-count"><?php echo $likes; ?></span>
						</button>
						<button type="button" class="blog-dislike-btn button buttonTransparent displayFlex flexXcenter flexYcenter" data-post-id="<?php echo absint($post_id); ?>" data-type="dislike" aria-label="<?php esc_attr_e('Nie', 'wi'); ?>">
							<svg class="blog-feedback-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm7-13h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3"/></svg>
							<span class="blog-dislike-count"><?php echo $dislikes; ?></span>
						</button>
					</div>
					<?php
                    $related = wi_get_related_blog_posts($post_id, 3);
    if ($related->have_posts()) : ?>
						<div class="blog-related">
							<h2 class="blog-related-title"><?php esc_html_e('Powiązane posty blogowe', 'wi'); ?></h2>
							<div class="blog-grid">
								<?php
                set_query_var('blog_card_heading', 'h3');
        while ($related->have_posts()) : $related->the_post();
            get_template_part('template-parts/blog', 'card');
        endwhile;
        wp_reset_postdata();
        ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<!-- Sticky sidebar -->
				<aside class="blog-single-sidebar">
					<?php if (! empty($sidebar_banner['url'])) : ?>
						<div class="blog-sidebar-banner-sticky">
							<?php if (! empty($sidebar_link)) : ?>
								<a href="<?php echo esc_url($sidebar_link); ?>" target="_blank" rel="noopener noreferrer" class="blog-sidebar-banner-link">
									<img src="<?php echo esc_url($sidebar_banner['url']); ?>" alt="" class="img-full">
								</a>
							<?php else : ?>
								<img src="<?php echo esc_url($sidebar_banner['url']); ?>" alt="" class="img-full">
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</aside>
			</div>
		</div>
	</section>

<?php
endwhile;
get_footer();
