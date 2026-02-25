<?php

declare(strict_types=1);

/**
 * Admin menu and page for Calculator (Kalkulator).
 */

if (! defined('ABSPATH')) {
    return;
}

add_action('admin_menu', 'wi_calc_register_admin_page');
add_action('load-toplevel_page_wi-calculator', 'wi_calc_hide_plugin_notices');

function wi_calc_hide_plugin_notices(): void
{
    remove_all_actions('admin_notices');
    remove_all_actions('all_admin_notices');
}

function wi_calc_register_admin_page(): void
{
    add_menu_page(
        __('Kalkulator', 'wi'),
        __('Kalkulator', 'wi'),
        'manage_options',
        'wi-calculator',
        'wi_calc_render_admin_page',
        'dashicons-calculator',
        301
    );
}

function wi_calc_render_admin_page(): void
{
    echo '<div class="wrap wi-calc-page-wrap">';
    echo '<div class="wi-calc-admin-wrapper"><div id="wi-calc-admin"></div></div>';
    echo '</div>';
}

add_action('admin_enqueue_scripts', 'wi_calc_enqueue_admin_scripts', 10, 1);

function wi_calc_enqueue_admin_scripts(string $hook): void
{
    if ($hook !== 'toplevel_page_wi-calculator') {
        return;
    }

    $script_path = get_template_directory() . '/js/admin-calc/index.js';
    $css_path    = get_template_directory() . '/js/admin-calc/index.css';
    $asset_path  = get_template_directory() . '/js/admin-calc/index.asset.php';

    if (! file_exists($script_path)) {
        return;
    }

    if (file_exists($css_path)) {
        wp_enqueue_style(
            'wi-calc-admin',
            get_template_directory_uri() . '/js/admin-calc/index.css',
            [],
            filemtime($css_path)
        );
    }
    wp_add_inline_style('wp-admin', '
        .toplevel_page_wi-calculator .wi-calc-page-wrap { margin: 0 -20px 0 -10px; }
        .toplevel_page_wi-calculator .wi-calc-admin-wrapper {
            background: #fff;
            padding: 20px;
        }
        .toplevel_page_wi-calculator .wi-calc-admin-wrapper .wi-calc-admin-wrap { background: #fff; }
    ');

    $asset = file_exists($asset_path) ? include $asset_path : ['dependencies' => [], 'version' => filemtime($script_path)];
    wp_enqueue_script(
        'wi-calc-admin',
        get_template_directory_uri() . '/js/admin-calc/index.js',
        $asset['dependencies'],
        $asset['version'] ?? filemtime($script_path),
        true
    );
    wp_localize_script('wi-calc-admin', 'wiCalcAdmin', [
        'apiUrl'   => rest_url('wi-calc/v1'),
        'nonce'    => wp_create_nonce('wp_rest'),
    ]);
}

add_action('after_switch_theme', 'wi_calc_run_migrations_on_activation');

function wi_calc_run_migrations_on_activation(): void
{
    $runner_file = get_template_directory() . '/database/MigrationRunner.php';
    if (! file_exists($runner_file)) {
        return;
    }
    require_once $runner_file;
    if (! class_exists('MigrationRunner')) {
        return;
    }
    $runner = new MigrationRunner();
    $runner->ensureMigrationsTable();
    $runner->runPending();
}
