<?php // Steps - Icon 4 columns //?>
<?php if ($ceLayout == 'steps_-_icon_4_columns') { ?>
    <?php containerStart(get_sub_field('container')); ?>
        <div id="ceID<?php echo $ceIteration; ?>" class="ceStepsIcon4columns">
            <?php if (have_rows("steps_-_icon_4_columns")) {
                $s = 1; ?>
                <div class="ceStepsIcon4columnsBox displayFlex flexWrap flexXstart flexYstretch">
                    <?php while (have_rows("steps_-_icon_4_columns")) {
                        the_row(); ?>
                        <div class="ceStepsIcon4columnsItem">
                            <div class="ceStepsIcon4columnsItemInside">
                                <div class="ceStepsIcon4columnsStep">
                                    <?php echo __("krok", "wi"); ?> <span><?php if ($s < 10) {
                                        echo "0";
                                    };
                        echo $s; ?></span>
                                </div>
                                <div class="ceStepsIcon4columnsIcon">
                                    <img src="<?php echo get_sub_field('icon'); ?>" />
                                </div>
                                <div class="ceStepsIcon4columnsDesc"><?php echo get_sub_field('content'); ?></div>
                            </div>
                        </div>
                        <?php $s++; ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php containerEnd(get_sub_field('container')); ?>
<?php } ?>