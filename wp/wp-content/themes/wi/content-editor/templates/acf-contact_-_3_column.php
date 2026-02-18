<?php // Contact - 3 column //?> 
<?php if ($ceLayout == 'contact_-_3_column') { ?>  
    <?php containerStart(get_sub_field('container')); ?>
        <div id="ceID<?php echo $ceIteration; ?>" class="ceContact3columns">
            <?php if (have_rows("contact_-_3_column")) { ?>
                <div class="ceContact3columnsBox displayFlex flexWrap flexXstart flexYstretch">
                    <?php while (have_rows("contact_-_3_column")) {
                        the_row(); ?>
                        <div class="ceContact3columnsItem">
                            <div class="ceContact3columnsItemInside">
                                <?php if (get_sub_field('image') != '') { ?>  
                                    <span class="ceContact3columnsImg">
                                        <img data-src="<?php $image = get_sub_field('image');
                                    echo $image['sizes']['img-120x120']; ?>"  class="lazy img-full"/>
                                    </span>
                                <?php } ?>
                                <?php echo get_sub_field('content'); ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?> 
        </div>
    <?php containerEnd(get_sub_field('container')); ?>
<?php } ?>