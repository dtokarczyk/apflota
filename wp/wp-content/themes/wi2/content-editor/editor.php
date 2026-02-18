<div id="contentEditor"> 
    <?php
        if (have_rows('content_editor', get_the_ID())) {
            
            @include_once 'shortcode_function.php';
            
            $ceIteration = 0;
            while (have_rows('content_editor', get_the_ID())) {
                the_row();
                $ceLayout = get_row_layout();

                $directory = __DIR__ . '/templates/';
                $array = array_diff(scandir($directory), array('..', '.'));

                foreach ($array as &$item) {
                    if (substr($item, 0, 1) != "_" && strpos($item, 'acf') !== false) {
                        @include __DIR__ . '/templates/' . $item;
                    }
                }

                $ceIteration++;
            }
            echo '<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls"><div class="slides"></div><h3 class="title"></h3><a class="prev">‹</a><a class="next">›</a><a class="close">×</a><a class="play-pause"></a><ol class="indicator"></ol></div>';
        }
        wp_reset_query();
    ?>
</div>