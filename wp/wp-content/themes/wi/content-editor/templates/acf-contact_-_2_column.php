<?php // Contact - 2 column //?> 
<?php if ($ceLayout == 'contact_-_2_column') { ?>  
    <?php containerStart(get_sub_field('container')); ?>
        <div id="ceID<?php echo $ceIteration; ?>" class="ceContact2columns">
            <?php if (have_rows("contact_-_2_column")) { ?>
                <div class="ceContact2columnsBox displayFlex flexWrap flexXstart flexYstretch">
                    <?php while (have_rows("contact_-_2_column")) {
                        the_row(); ?>
                        <div class="ceContact2columnsItem">
                            <div class="ceContact2columnsItemInside">
                                <?php echo get_sub_field('content'); ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?> 
        </div>
    <?php containerEnd(get_sub_field('container')); ?>
<?php } ?>