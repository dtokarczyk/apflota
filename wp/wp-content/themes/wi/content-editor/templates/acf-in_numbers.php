<?php // In numbers //?> 
<?php if ($ceLayout == 'in_numbers') { ?>  
    <div id="ceID<?php echo $ceIteration; ?>" class="ceInNumbers">
        <div id="sectionNumbers">
            <?php containerStart(get_sub_field('container')); ?>
                <?php if (get_sub_field('heading') != '') { ?>  
                    <div class="sectionNumbersTitle"><?php echo get_sub_field('heading'); ?></div>
                <?php } ?>
                <?php if (have_rows("in_numbers")) { ?>
                    <div class="sectionNumbersBox displaFlex flexWrap flexXstart flexYstretch">
                        <?php while (have_rows("in_numbers")) {
                            the_row(); ?>
                            <div class="sectionNumbersItem displaFlex flexXstart flexYstart">
                                <div class="sectionNumbersItemInside">
                                    <div class="sectionNumbersItemNumber<?php if (get_sub_field('description') != '') { ?> sectionNumbersItemNumberLine<?php } ?>">
                                        <span class="likeH2"><?php echo get_sub_field('before_number'); ?><span class="counter" data-count="<?php echo get_sub_field('number'); ?>" data-count-check="<?php echo get_sub_field('number'); ?>">0</span><?php echo get_sub_field('after_number'); ?></span>
                                    </div>
                                    <?php if (get_sub_field('description') != '') { ?>  
                                        <div class="sectionNumbersItemDesc"><?php echo get_sub_field('description'); ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?> 
            <?php containerEnd(get_sub_field('container')); ?>
            <img src="<?php echo get_template_directory_uri() . '/images/number_bg.jpg'; ?>" class="img-full" alt="numbers_bg">
        </div>
    </div>
<?php } ?>