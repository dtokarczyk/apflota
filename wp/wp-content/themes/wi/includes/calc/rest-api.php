<?php

declare(strict_types=1);

/**
 * REST API for calculator: rates CRUD, import, uploads history, migrations.
 */

if (! defined('ABSPATH')) {
    return;
}

add_action('rest_api_init', 'wi_calc_register_rest_routes');

function wi_calc_register_rest_routes(): void
{
    $check = fn() => current_user_can('manage_options');

    register_rest_route('wi-calc/v1', '/cars', [
        'methods'             => 'GET',
        'callback'            => 'wi_calc_rest_get_cars',
        'permission_callback' => $check,
    ]);

    register_rest_route('wi-calc/v1', '/rates', [
        'methods'             => 'GET',
        'callback'            => 'wi_calc_rest_get_rates',
        'permission_callback' => $check,
        'args'                => [
            'car_id'  => ['type' => 'string', 'required' => false],
            'month'   => ['type' => 'integer', 'required' => false],
            'page'    => ['type' => 'integer', 'default' => 1],
            'per_page' => ['type' => 'integer', 'default' => 100],
        ],
    ]);

    register_rest_route('wi-calc/v1', '/rates', [
        'methods'             => 'POST',
        'callback'            => 'wi_calc_rest_create_rate',
        'permission_callback' => $check,
        'args'                => [
            'car_id'  => ['type' => 'string', 'required' => true],
            'idv'     => ['type' => 'string', 'required' => true],
            'month'   => ['type' => 'integer', 'required' => true],
            'km'      => ['type' => 'integer', 'required' => true],
            'percent' => ['type' => 'integer', 'required' => true],
            'fee'     => ['type' => 'integer', 'required' => true],
            'rate'    => ['type' => 'integer', 'required' => true],
        ],
    ]);

    register_rest_route('wi-calc/v1', '/rates/(?P<id>\d+)', [
        'methods'             => 'PUT',
        'callback'            => 'wi_calc_rest_update_rate',
        'permission_callback' => $check,
        'args'                => [
            'id'      => ['type' => 'integer', 'required' => true],
            'car_id'  => ['type' => 'string', 'required' => false],
            'idv'     => ['type' => 'string', 'required' => false],
            'month'   => ['type' => 'integer', 'required' => false],
            'km'      => ['type' => 'integer', 'required' => false],
            'percent' => ['type' => 'integer', 'required' => false],
            'fee'     => ['type' => 'integer', 'required' => false],
            'rate'    => ['type' => 'integer', 'required' => false],
        ],
    ]);

    register_rest_route('wi-calc/v1', '/rates/(?P<id>\d+)', [
        'methods'             => 'DELETE',
        'callback'            => 'wi_calc_rest_delete_rate',
        'permission_callback' => $check,
        'args'                => ['id' => ['type' => 'integer', 'required' => true]],
    ]);

    register_rest_route('wi-calc/v1', '/import/validate', [
        'methods'             => 'POST',
        'callback'            => 'wi_calc_rest_validate_import',
        'permission_callback' => $check,
    ]);

    register_rest_route('wi-calc/v1', '/import', [
        'methods'             => 'POST',
        'callback'            => 'wi_calc_rest_import_handler',
        'permission_callback' => $check,
    ]);

    register_rest_route('wi-calc/v1', '/uploads', [
        'methods'             => 'GET',
        'callback'            => 'wi_calc_rest_get_uploads',
        'permission_callback' => $check,
    ]);

    register_rest_route('wi-calc/v1', '/migrations', [
        'methods'             => 'GET',
        'callback'            => 'wi_calc_rest_get_migrations',
        'permission_callback' => $check,
    ]);

    register_rest_route('wi-calc/v1', '/migrations/run', [
        'methods'             => 'POST',
        'callback'            => 'wi_calc_rest_run_migrations',
        'permission_callback' => $check,
    ]);
}

function wi_calc_rest_get_cars(WP_REST_Request $request): WP_REST_Response
{
    $posts = get_posts([
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'meta_key'       => 'id',
        'meta_compare'   => 'EXISTS',
    ]);
    $items = [];
    foreach ($posts as $post) {
        $car_id = get_field('id', $post->ID);
        if ($car_id !== null && $car_id !== '') {
            $items[] = [
                'car_id' => (string) $car_id,
                'title'  => get_the_title($post->ID),
            ];
        }
    }
    return new WP_REST_Response(['items' => $items]);
}

function wi_calc_rest_get_rates(WP_REST_Request $request): WP_REST_Response
{
    $query = CalcRate::query()->orderBy('car_id')->orderBy('month')->orderBy('km')->orderBy('percent');

    if ($request->get_param('car_id') !== null && $request->get_param('car_id') !== '') {
        $query->where('car_id', $request->get_param('car_id'));
    }
    if ($request->get_param('month') !== null && $request->get_param('month') !== '') {
        $query->where('month', (int) $request->get_param('month'));
    }

    $perPage = (int) $request->get_param('per_page');
    $page    = (int) $request->get_param('page');
    $total   = $query->count();
    $items   = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

    return new WP_REST_Response([
        'items' => $items->toArray(),
        'total' => $total,
        'pages' => (int) ceil($total / $perPage),
    ]);
}

function wi_calc_rest_create_rate(WP_REST_Request $request): WP_REST_Response
{
    $data = [
        'car_id'  => $request->get_param('car_id'),
        'idv'     => $request->get_param('idv'),
        'month'   => (int) $request->get_param('month'),
        'km'      => (int) $request->get_param('km'),
        'percent' => (int) $request->get_param('percent'),
        'fee'     => (int) $request->get_param('fee'),
        'rate'    => (int) $request->get_param('rate'),
    ];
    $rate = CalcRate::create($data);
    return new WP_REST_Response($rate->toArray(), 201);
}

function wi_calc_rest_update_rate(WP_REST_Request $request): WP_REST_Response
{
    $id   = (int) $request->get_param('id');
    $rate = CalcRate::find($id);
    if (! $rate) {
        return new WP_REST_Response(['message' => 'Not found'], 404);
    }
    $params = ['car_id', 'idv', 'month', 'km', 'percent', 'fee', 'rate'];
    foreach ($params as $key) {
        if ($request->get_param($key) !== null) {
            $rate->$key = $key === 'car_id' || $key === 'idv' ? $request->get_param($key) : (int) $request->get_param($key);
        }
    }
    $rate->save();
    return new WP_REST_Response($rate->toArray());
}

function wi_calc_rest_delete_rate(WP_REST_Request $request): WP_REST_Response
{
    $id   = (int) $request->get_param('id');
    $rate = CalcRate::find($id);
    if (! $rate) {
        return new WP_REST_Response(['message' => 'Not found'], 404);
    }
    $rate->delete();
    return new WP_REST_Response(['deleted' => true]);
}

function wi_calc_rest_validate_import(WP_REST_Request $request): WP_REST_Response
{
    $files = $request->get_file_params();
    if (empty($files['file'])) {
        return new WP_REST_Response(['error' => 'No file uploaded'], 400);
    }
    $file = $files['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return new WP_REST_Response(['error' => 'Upload error'], 400);
    }
    $path = $file['tmp_name'];
    $rows = wi_calc_parse_csv_file($path);
    $result = wi_calc_validate_csv($rows, 10);
    return new WP_REST_Response([
        'valid_count'   => count($result['valid_rows']),
        'error_count'   => count($result['errors']),
        'errors'        => $result['errors'],
        'preview'       => $result['preview'],
        'total_checked' => $result['total_data_rows'],
        'valid_rows'    => $result['valid_rows'],
    ]);
}

function wi_calc_rest_import_handler(WP_REST_Request $request): WP_REST_Response
{
    $files = $request->get_file_params();
    if (! empty($files['file']) && $files['file']['error'] === UPLOAD_ERR_OK) {
        return wi_calc_rest_import_from_upload($request);
    }
    return wi_calc_rest_import($request);
}

function wi_calc_rest_import(WP_REST_Request $request): WP_REST_Response
{
    $mode = $request->get_param('mode');
    if (! in_array($mode, ['replace', 'append'], true)) {
        $mode = 'replace';
    }

    $validRows = $request->get_param('valid_rows');
    if (! is_array($validRows)) {
        $validRows = [];
    }

    $originalName = $request->get_param('original_name');
    if (! is_string($originalName)) {
        $originalName = '';
    }

    $userId = get_current_user_id();
    $result = wi_calc_import_csv($validRows, $mode, $userId, $originalName);

    return new WP_REST_Response($result);
}

function wi_calc_rest_import_from_upload(WP_REST_Request $request): WP_REST_Response
{
    $files = $request->get_file_params();
    if (empty($files['file'])) {
        return new WP_REST_Response(['error' => 'No file'], 400);
    }
    $file = $files['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return new WP_REST_Response(['error' => 'Upload error'], 400);
    }
    $path   = $file['tmp_name'];
    $rows   = wi_calc_parse_csv_file($path);
    $result = wi_calc_validate_csv($rows, 0);
    $mode   = $request->get_param('mode') ?: 'replace';
    if ($mode !== 'append') {
        $mode = 'replace';
    }
    $importResult = wi_calc_import_csv($result['valid_rows'], $mode, get_current_user_id(), $file['name']);
    return new WP_REST_Response($importResult);
}

function wi_calc_rest_get_uploads(WP_REST_Request $request): WP_REST_Response
{
    $items = CalcUpload::query()->orderByDesc('created_at')->limit(100)->get();
    return new WP_REST_Response(['items' => $items->toArray()]);
}

function wi_calc_rest_get_migrations(WP_REST_Request $request): WP_REST_Response
{
    $runner_file = get_template_directory() . '/database/MigrationRunner.php';
    if (! file_exists($runner_file)) {
        return new WP_REST_Response(['items' => [], 'pending' => 0]);
    }
    require_once $runner_file;
    $runner = new MigrationRunner();
    $status = $runner->status();
    $pending = count(array_filter($status, static fn($s) => $s['status'] === 'pending'));
    return new WP_REST_Response(['items' => $status, 'pending' => $pending]);
}

function wi_calc_rest_run_migrations(WP_REST_Request $request): WP_REST_Response
{
    $runner_file = get_template_directory() . '/database/MigrationRunner.php';
    if (! file_exists($runner_file)) {
        return new WP_REST_Response(['run' => 0, 'errors' => ['MigrationRunner not found']], 500);
    }
    require_once $runner_file;
    $runner = new MigrationRunner();
    $result = $runner->runPending();
    return new WP_REST_Response($result);
}
