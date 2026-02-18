<?php // Editor columns // ?>
<?php if( $ceLayout == 'editor_columns' ) { ?>
    <?php containerStart(get_sub_field('container')); ?>
        <div id="ceID<?php echo $ceIteration; ?>" class="ceEditorColumn ceEditorColumn<?php echo get_sub_field('number_of_columns'); ?>">
            <div class="ceEditorColumnBox displayFlex flexWrap flexXbetween flexXstart">
                <div class="ceEditorColumnItem">
                    <?php echo get_sub_field('editor_column_1'); ?>
                </div>

                <?php if(get_sub_field('editor_column_2') != "" && get_sub_field('number_of_columns') > 1) { ?>
                    <div class="ceEditorColumnItem">
                        <?php echo get_sub_field('editor_column_2'); ?>
                    </div>
                <?php } ?>

                <?php if(get_sub_field('editor_column_3') != "" && get_sub_field('number_of_columns') > 2) { ?>
                    <div class="ceEditorColumnItem">
                        <?php echo get_sub_field('editor_column_3'); ?>
                    </div>
                <?php } ?>

                <?php if(get_sub_field('editor_column_4') != "" && get_sub_field('number_of_columns') > 3) { ?>
                    <div class="ceEditorColumnItem">
                        <?php echo get_sub_field('editor_column_4'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php containerEnd(get_sub_field('container')); ?>
<?php } ?>