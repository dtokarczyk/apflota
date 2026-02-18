<?php // Icon 4 col // ?> 
<?php if($ceLayout == 'icon_4_col') { ?>  
    <?php containerStart(get_sub_field('container')); ?>
        <div id="ceID<?php echo $ceIteration; ?>" class="ceIcon4columns">
            <?php if( have_rows("icon_4_columns") ) { ?>
                <div class="ceIcon4columnsBox displayFlex flexWrap flexXstart flexYstart">
                    <?php while( have_rows("icon_4_columns") ) { the_row(); ?>
                        <div class="ceIcon4columnsItem">
                            <div class="ceIcon4columnsIcon">
                                <img src="<?php echo get_sub_field('icon'); ?>" />
                            </div>
                            <div class="ceIcon4columnsDesc"><?php echo get_sub_field('content'); ?></div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?> 
        </div>
    <?php containerEnd(get_sub_field('container')); ?>
<?php } ?>