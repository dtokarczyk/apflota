<?php // Space  // ?>
<?php if( $ceLayout == 'space' ) { ?>
    <div id="ceID<?php echo $ceIteration; ?>" class="ceSpace ceSpace<?php echo $ceIteration; ?> ceSpaceBG<?php echo get_sub_field('color_bg'); ?>">
        <style>
            .ceSpace<?php echo $ceIteration; ?> {
                min-height: <?php echo str_replace(",", ".", get_sub_field('space') / 10 / 2); ?>rem;
                max-height: <?php echo str_replace(",", ".", get_sub_field('space') / 10 * 1.2); ?>rem;
                height: <?php echo str_replace(",", ".", get_sub_field('space') * 100 / 1920); ?>vw;
            }
        </style>
    </div>
<?php } ?>