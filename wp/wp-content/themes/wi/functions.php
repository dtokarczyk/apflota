<?php

declare(strict_types=1);

@include 'content-editor/config.php';
@include 'database/bootstrap.php';
@include 'includes/calc/csv-import.php';
@include 'includes/calc/rest-api.php';
@include 'includes/calc/admin-page.php';
@include 'includes/migrations-admin.php';
//@include 'includes/cookie.php';
@include 'includes/wp_enqueue.php';
@include 'includes/shortcode_widget.php';
@include 'includes/source_backend.php';
@include 'includes/source_frontend.php';
@include 'includes/blog-setup.php';
@include 'includes/blog-helpers.php';
@include 'includes/popup.php';
