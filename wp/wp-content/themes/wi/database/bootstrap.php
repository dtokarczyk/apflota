<?php

declare(strict_types=1);

/**
 * Eloquent ORM bootstrap for WordPress.
 * Uses DB_* constants and $table_prefix from wp-config.php.
 */

if (! defined('ABSPATH')) {
    return;
}

$theme_dir = dirname(__DIR__);
$autoload = $theme_dir . '/vendor/autoload.php';

if (! file_exists($autoload)) {
    return;
}

require_once $autoload;

if (! class_exists('Illuminate\Database\Capsule\Manager')) {
    return;
}

use Illuminate\Database\Capsule\Manager as Capsule;

global $table_prefix;

$capsule = new Capsule();
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => DB_HOST,
    'database'  => DB_NAME,
    'username'  => DB_USER,
    'password'  => DB_PASSWORD,
    'charset'   => defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4',
    'collation' => defined('DB_COLLATE') && DB_COLLATE ? DB_COLLATE : 'utf8mb4_unicode_ci',
    'prefix'    => $table_prefix,
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

require_once $theme_dir . '/database/models/CalcRate.php';
require_once $theme_dir . '/database/models/CalcUpload.php';
