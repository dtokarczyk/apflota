<?php

declare(strict_types=1);

/**
 * Admin subpage: Kokpit → Migracje. Lists migrations and allows running pending ones.
 */

if (! defined('ABSPATH')) {
    return;
}

add_action('admin_menu', 'wi_migrations_register_submenu');

function wi_migrations_register_submenu(): void
{
    add_submenu_page(
        'index.php',
        __('Migracje', 'wi'),
        __('Migracje', 'wi'),
        'manage_options',
        'wi-migrations',
        'wi_migrations_render_page'
    );
}

add_action('admin_post_wi_run_migrations', 'wi_migrations_handle_run');

function wi_migrations_handle_run(): void
{
    if (! current_user_can('manage_options')) {
        wp_die(esc_html__('Brak uprawnień.', 'wi'));
    }
    check_admin_referer('wi_run_migrations');

    $runner = wi_migrations_get_runner();
    if ($runner === null) {
        wp_safe_redirect(add_query_arg(['wi_migrations_error' => '1'], admin_url('index.php?page=wi-migrations')));
        exit;
    }

    $result = $runner->runPending();
    $url    = admin_url('index.php?page=wi-migrations');
    if (! empty($result['errors'])) {
        $url = add_query_arg('wi_migrations_errors', implode('|', array_map('urlencode', $result['errors'])), $url);
    } else {
        $url = add_query_arg('wi_migrations_run', (string) $result['run'], $url);
    }
    wp_safe_redirect($url);
    exit;
}

function wi_migrations_get_runner(): ?MigrationRunner
{
    $runner_file = get_template_directory() . '/database/MigrationRunner.php';
    if (! file_exists($runner_file) || ! class_exists('Illuminate\Database\Capsule\Manager')) {
        return null;
    }
    require_once $runner_file;
    return new MigrationRunner();
}

function wi_migrations_render_page(): void
{
    $runner = wi_migrations_get_runner();
    if ($runner === null) {
        echo '<div class="wrap"><h1>' . esc_html__('Migracje', 'wi') . '</h1>';
        echo '<p class="notice notice-error">' . esc_html__('Migracje wymagają załadowanego Eloquent (PDO MySQL).', 'wi') . '</p></div>';
        return;
    }

    $status   = $runner->status();
    $total    = count($status);
    $deployed = count(array_filter($status, static fn(array $s): bool => $s['status'] === 'run'));
    $pending  = $total - $deployed;

    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('Migracje', 'wi') . '</h1>';

    if (isset($_GET['wi_migrations_error']) && $_GET['wi_migrations_error'] === '1') {
        echo '<div class="notice notice-error"><p>' . esc_html__('Nie można uruchomić migracji.', 'wi') . '</p></div>';
    }
    if (! empty($_GET['wi_migrations_errors'])) {
        $errors = array_map('urldecode', explode('|', (string) $_GET['wi_migrations_errors']));
        echo '<div class="notice notice-error"><p><strong>' . esc_html__('Błędy:', 'wi') . '</strong></p><ul>';
        foreach ($errors as $err) {
            echo '<li>' . esc_html($err) . '</li>';
        }
        echo '</ul></div>';
    }
    if (isset($_GET['wi_migrations_run'])) {
        $run = (int) $_GET['wi_migrations_run'];
        echo '<div class="notice notice-success"><p>' . esc_html(sprintf(__('Uruchomiono %d migracji.', 'wi'), $run)) . '</p></div>';
    }

    echo '<p><strong>' . esc_html__('Wdrożone:', 'wi') . '</strong> ' . (int) $deployed . ' / ' . (int) $total . ' ';
    echo '<a href="' . esc_url(admin_url('index.php?page=wi-migrations')) . '" class="button">' . esc_html__('Sprawdź migracje', 'wi') . '</a></p>';

    if ($pending > 0) {
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" style="display:inline-block">';
        echo '<input type="hidden" name="action" value="wi_run_migrations" />';
        wp_nonce_field('wi_run_migrations');
        echo '<p><button type="submit" class="button button-primary">' . esc_html__('Uruchom oczekujące migracje', 'wi') . '</button></p>';
        echo '</form>';
    }

    echo '<table class="wp-list-table widefat fixed striped" style="margin-top:16px">';
    echo '<thead><tr><th>' . esc_html__('Migracja', 'wi') . '</th><th>' . esc_html__('Status', 'wi') . '</th></tr></thead><tbody>';
    foreach ($status as $row) {
        $name   = $row['migration'];
        $run    = $row['status'] === 'run';
        $label  = $run ? __('Wdrożona', 'wi') : __('Oczekująca', 'wi');
        $class  = $run ? '' : ' style="color:#b32d2e"';
        echo '<tr><td>' . esc_html($name) . '</td><td' . $class . '>' . esc_html($label) . '</td></tr>';
    }
    echo '</tbody></table></div>';
}
