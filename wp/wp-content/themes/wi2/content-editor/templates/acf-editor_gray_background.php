<?php // Editor gray background // ?>
<?php if( $ceLayout == 'editor_gray_background' ) { ?>
    <div class="ceGrayBackground">
        <?php containerStart(get_sub_field('container')); ?>
            <div id="ceID<?php echo $ceIteration; ?>" class="ceEditorColumn ceEditorColumn1">
                <div class="ceEditorColumnBox displayFlex flexWrap flexXbetween flexXstart">
                    <div class="ceEditorColumnItem">
                        <?php echo get_sub_field('editor'); ?>
                    </div>
                </div>
            </div>
        <?php containerEnd(get_sub_field('container')); ?>
    </div>
<?php } ?>