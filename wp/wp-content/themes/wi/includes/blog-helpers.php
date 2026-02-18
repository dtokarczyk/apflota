<?php

/**
 * Blog helpers: TOC generator and related posts.
 */

/**
 * Generate table of contents from content headings (h2, h3) and add ids to headings.
 *
 * @param string $content Post content HTML.
 * @return array ['toc_html' => string, 'content' => string]
 */
function wi_generate_toc($content)
{
    if (empty($content) || ! class_exists('DOMDocument')) {
        return array( 'toc_html' => '', 'content' => $content );
    }

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $headings = $xpath->query('//h2 | //h3');
    $toc_items = array();
    $used_ids = array();

    foreach ($headings as $heading) {
        $text = trim($heading->textContent);
        if (empty($text)) {
            continue;
        }
        $slug = sanitize_title($text);
        $base  = $slug;
        $counter = 0;
        while (isset($used_ids[ $slug ])) {
            $counter++;
            $slug = $base . '-' . $counter;
        }
        $used_ids[ $slug ] = true;
        $heading->setAttribute('id', $slug);
        $tag = $heading->nodeName;
        $toc_items[] = array(
            'id'   => $slug,
            'text' => $text,
            'tag'  => $tag,
        );
    }

    $toc_html = '';
    if (! empty($toc_items)) {
        $toc_html = '<ul class="blog-toc-list">';
        $in_sublist = false;
        $has_open_li = false;
        foreach ($toc_items as $item) {
            $level = $item['tag'];
            if ($level === 'h2') {
                if ($in_sublist) {
                    $toc_html .= '</ul>';
                    $in_sublist = false;
                }
                if ($has_open_li) {
                    $toc_html .= '</li>';
                }
                $toc_html .= '<li class="blog-toc-item blog-toc-h2"><a href="#' . esc_attr($item['id']) . '" class="blog-toc-link">' . esc_html($item['text']) . '</a>';
                $has_open_li = true;
            } else {
                if (! $in_sublist) {
                    $toc_html .= '<ul class="blog-toc-sublist">';
                    $in_sublist = true;
                }
                $toc_html .= '<li class="blog-toc-item blog-toc-h3"><a href="#' . esc_attr($item['id']) . '" class="blog-toc-link">' . esc_html($item['text']) . '</a></li>';
            }
        }
        if ($in_sublist) {
            $toc_html .= '</ul>';
        }
        if ($has_open_li) {
            $toc_html .= '</li>';
        }
        $toc_html .= '</ul>';
    }

    $new_content = $dom->saveHTML();
    // DOMDocument may wrap in html/body; strip if present.
    if (preg_match('/<body[^>]*>(.*)<\/body>/is', $new_content, $m)) {
        $new_content = $m[1];
    }

    return array(
        'toc_html' => $toc_html,
        'content'  => $new_content,
    );
}

/**
 * Get related blog posts (same blog-category, exclude current).
 *
 * @param int $post_id Current post ID.
 * @param int $count   Number of posts to return.
 * @return WP_Query
 */
function wi_get_related_blog_posts($post_id, $count = 3)
{
    $terms = get_the_terms($post_id, 'blog-category');
    if (! $terms || is_wp_error($terms)) {
        return new WP_Query(array( 'post__in' => array( 0 ) ));
    }
    $term_ids = array_map(function ($t) {
        return $t->term_id;
    }, $terms);
    $args = array(
        'post_type'      => 'blog',
        'posts_per_page' => $count,
        'post__not_in'   => array( $post_id ),
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => array(
            array(
                'taxonomy' => 'blog-category',
                'field'    => 'term_id',
                'terms'    => $term_ids,
            ),
        ),
    );
    return new WP_Query($args);
}
