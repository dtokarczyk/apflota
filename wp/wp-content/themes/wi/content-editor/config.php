<?php

// Register image
function ce_register_image(): void
{
    //add_image_size('ce_img-640x480', 640, 480, true);
    //add_image_size('ce_img-960xX', 960, 2000, false);
}
add_action('after_setup_theme', 'ce_register_image');

// JSON layouts save

function ce_json_save_layouts($path)
{
    $path = get_stylesheet_directory() . '/content-editor/acf/';
    return $path;
}
//add_filter('acf/settings/save_json', 'ce_json_save_layouts');

function ce_json_load_layouts($paths)
{
    unset($paths[0]);
    $paths[] = get_stylesheet_directory() . '/content-editor/acf/';
    return $paths;
}
//add_filter('acf/settings/load_json', 'ce_json_load_layouts');


// Hidden layout

function ce_hidden_layout(): void
{ ?>
    <style>
        /* Global */
        .term-php #wpcontent #edittag {
            max-width: 100%;
        }
        .acf-fc-popup a {
            display: none;
        }
        .acf-editor-wrap iframe {
            min-height: 150px;
        }

        /* Gallery */
        .acf-gallery .acf-gallery-attachment .thumbnail img {
            width: 100%;
        }

        /* Editor columns */
        .layout[data-layout="editor_columns"] .acf-fields {
            display: flex;
            flex-wrap: nowrap;
        }
        .layout[data-layout="editor_columns"] .acf-fields .acf-field {
            width: 100%;
            min-width: 80px;
            padding: 16px 5px;
            min-height: auto!important;
        }
        @media (max-width: 1300px) {
            .layout[data-layout="editor_columns"] .acf-fields {
                flex-wrap: wrap;
            }
            .layout[data-layout="editor_columns"] .acf-fields .acf-field {
                min-width: 100%;
                padding: 16px;
            }
        }
        <?php
            $directory = __DIR__ . '/templates/';
    $array = array_diff(scandir($directory), ['..', '.']);

    foreach ($array as &$item) {
        if (substr($item, 0, 1) != "_" && str_contains($item, 'acf')) {
            $item = str_replace(
                "acf-",
                "",
                str_replace(
                    "acf_",
                    "",
                    str_replace(
                        ".php",
                        "",
                        $item,
                    ),
                ),
            );
            echo '.acf-fc-popup a[data-layout="' . $item . '"] { display: block; }';
        }
    }
    ?>
    </style>
<?php }
add_action('admin_footer', 'ce_hidden_layout');
?>