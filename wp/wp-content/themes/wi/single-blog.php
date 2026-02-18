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
    $summary        = get_field('blog_summary');
    $likes          = (int) get_field('blog_likes');
    $dislikes       = (int) get_field('blog_dislikes');
    $sidebar_banner = get_field('blog_sidebar_banner', $blog_page_id);
    $sidebar_link  = get_field('blog_sidebar_banner_link', $blog_page_id);

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
	<!-- Hero image -->
	<?php if (! empty($big_image['sizes']['blog-hero']) || ! empty($big_image['url'])) : ?>
		<div class="blog-single-hero">
			<?php if (! empty($big_image['sizes']['blog-hero'])) : ?>
				<img src="<?php echo esc_url($big_image['sizes']['blog-hero']); ?>" alt="<?php the_title_attribute(); ?>" class="img-full blog-single-hero-img">
			<?php else : ?>
				<img src="<?php echo esc_url($big_image['url']); ?>" alt="<?php the_title_attribute(); ?>" class="img-full blog-single-hero-img">
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="containerBig">
		<div class="blog-single-layout displayFlex flexWrap flexXstart flexYstretch">
			<!-- Main content -->
			<div class="blog-single-main">
				<div class="blog-single-meta displayFlex flexWrap flexXstart flexYcenter">
					<?php if ($reading_time) : ?>
						<span class="blog-single-reading-time"><?php echo esc_html($reading_time); ?> <?php esc_html_e('do czytania', 'wi'); ?></span>
					<?php endif; ?>
					<?php if ($category_name) : ?>
						<span class="blog-single-category"><?php echo esc_html($category_name); ?></span>
					<?php endif; ?>
				</div>
				<h1 class="blog-single-title"><?php the_title(); ?></h1>
				<?php if ($summary) : ?>
					<div class="blog-single-summary"><?php echo wp_kses_post(nl2br($summary)); ?></div>
				<?php endif; ?>
				<div class="blog-single-date-share displayFlex flexWrap flexXbetween flexYcenter">
					<span class="blog-single-date"><?php echo get_the_date(); ?></span>
					<div class="blog-single-share displayFlex flexWrap flexXstart flexYcenter">
						<span class="blog-single-share-label"><?php esc_html_e('Udostępnij', 'wi'); ?></span>
						<a href="<?php echo esc_url($fb_share); ?>" target="_blank" rel="noopener noreferrer" class="blog-single-share-icon blog-single-share-fb" aria-label="Facebook">f</a>
						<a href="<?php echo esc_url($linkedin_share); ?>" target="_blank" rel="noopener noreferrer" class="blog-single-share-icon blog-single-share-linkedin" aria-label="LinkedIn">in</a>
					</div>
				</div>
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
						<span class="blog-like-icon">👍</span>
						<span class="blog-like-count"><?php echo $likes; ?></span>
					</button>
					<button type="button" class="blog-dislike-btn button buttonTransparent displayFlex flexXcenter flexYcenter" data-post-id="<?php echo absint($post_id); ?>" data-type="dislike" aria-label="<?php esc_attr_e('Nie', 'wi'); ?>">
						<span class="blog-dislike-icon">👎</span>
						<span class="blog-dislike-count"><?php echo $dislikes; ?></span>
					</button>
				</div>
				<?php
                    $related = wi_get_related_blog_posts($post_id, 3);
    if ($related->have_posts()) : ?>
					<div class="blog-related">
						<h2 class="blog-related-title"><?php esc_html_e('Powiązane wpisy', 'wi'); ?></h2>
						<div class="blog-related-grid displayFlex flexWrap flexXstart flexYstretch">
							<?php while ($related->have_posts()) : $related->the_post(); ?>
								<article class="blog-related-card">
									<a href="<?php the_permalink(); ?>" class="blog-related-card-link displayFlex flexWrap flexXstart flexYstretch">
										<?php
                            $small_img = get_field('blog_small_image');
							    if (! empty($small_img['sizes']['blog-small'])) : ?>
											<div class="blog-related-card-image">
												<img src="<?php echo esc_url($small_img['sizes']['blog-small']); ?>" alt="<?php the_title_attribute(); ?>" class="img-full">
											</div>
										<?php elseif (has_post_thumbnail()) : ?>
											<div class="blog-related-card-image"><?php the_post_thumbnail('blog-small', array( 'class' => 'img-full' )); ?></div>
										<?php endif; ?>
										<div class="blog-related-card-body">
											<h3 class="blog-related-card-title"><?php the_title(); ?></h3>
										</div>
									</a>
								</article>
							<?php endwhile; ?>
						</div>
					</div>
					<?php wp_reset_postdata();
    endif; ?>
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
