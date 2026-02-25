<?php

declare(strict_types=1);

/**
 * Blog helpers: TOC generator and related posts.
 */

/**
 * Generate table of contents from content headings (h2, h3, h4) and add ids to headings.
 *
 * Wraps content in a single root element before parsing so DOMDocument correctly handles
 * multiple top-level elements (known libxml issue with LIBXML_HTML_NOIMPLIED).
 *
 * @param string $content Post content HTML.
 * @return array ['toc_html' => string, 'content' => string]
 */
function wi_generate_toc($content)
{
    if (empty($content) || ! class_exists('DOMDocument')) {
        return ['toc_html' => '', 'content' => $content];
    }

    // Single root wrapper so libxml parses all siblings (h2, h3, h4, p, etc.) correctly.
    $wrapped = '<div id="wi-toc-root">' . $content . '</div>';

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $headings = $xpath->query('//h2 | //h3 | //h4');
    $toc_items = [];
    $used_ids = [];

    foreach ($headings as $heading) {
        $text = trim($heading->textContent);
        if (empty($text)) {
            continue;
        }
        $slug = sanitize_title($text);
        $base   = $slug;
        $counter = 0;
        while (isset($used_ids[$slug])) {
            $counter++;
            $slug = $base . '-' . $counter;
        }
        $used_ids[$slug] = true;
        $heading->setAttribute('id', $slug);
        $tag = strtolower($heading->nodeName);
        $toc_items[] = [
            'id'   => $slug,
            'text' => $text,
            'tag'  => $tag,
        ];
    }

    $toc_html = wi_build_toc_list($toc_items);

    // Get inner HTML of wrapper (original content with ids added), not the whole document.
    $root = $dom->getElementById('wi-toc-root');
    $new_content = '';
    if ($root && $root->childNodes->length > 0) {
        foreach ($root->childNodes as $child) {
            $new_content .= $dom->saveHTML($child);
        }
    } else {
        // Fallback: strip wrapper from saveHTML of document
        $new_content = $dom->saveHTML();
        $new_content = preg_replace('/^<div id="wi-toc-root">|<\/div>\s*$/s', '', $new_content);
    }

    return [
        'toc_html' => $toc_html,
        'content'  => $new_content,
    ];
}

/**
 * Build nested TOC list HTML from items (h2, h3, h4).
 *
 * @param array $toc_items Items with keys: id, text, tag (h2|h3|h4).
 * @return string HTML <ul> list.
 */
function wi_build_toc_list(array $toc_items)
{
    if (empty($toc_items)) {
        return '';
    }

    $toc_html = '<ul class="blog-toc-list">';
    $stack = []; // [ 'h2' => true, 'h3' => true ] = open levels
    $has_open_li = false;

    foreach ($toc_items as $item) {
        $level = $item['tag'];
        $depth = (int) str_replace('h', '', $level);

        // Close deeper or same-level lists and lis
        while (! empty($stack) && end($stack) >= $depth) {
            $toc_html .= '</li>';
            $has_open_li = false;
            array_pop($stack);
            $toc_html .= '</ul>';
        }

        // Open one <ul> per level when going deeper (e.g. h2 -> h4 opens two uls).
        $current_depth = empty($stack) ? 2 : end($stack);
        for ($d = $current_depth + 1; $d <= $depth; $d++) {
            $toc_html .= '<ul class="blog-toc-sublist">';
            $stack[] = $d;
        }

        if ($has_open_li) {
            $toc_html .= '</li>';
        }

        $toc_html .= '<li class="blog-toc-item blog-toc-' . esc_attr($level) . '">';
        $toc_html .= '<a href="#' . esc_attr($item['id']) . '" class="blog-toc-link">' . esc_html($item['text']) . '</a>';
        $has_open_li = true;
    }

    if ($has_open_li) {
        $toc_html .= '</li>';
    }
    while (! empty($stack)) {
        array_pop($stack);
        $toc_html .= '</ul>';
    }
    $toc_html .= '</ul>';

    return $toc_html;
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
        return new WP_Query(['post__in' => [0]]);
    }
    $term_ids = array_map(fn($t) => $t->term_id, $terms);
    $args = [
        'post_type'      => 'blog',
        'posts_per_page' => $count,
        'post__not_in'   => [$post_id],
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => [
            [
                'taxonomy' => 'blog-category',
                'field'    => 'term_id',
                'terms'    => $term_ids,
            ],
        ],
    ];
    return new WP_Query($args);
}
