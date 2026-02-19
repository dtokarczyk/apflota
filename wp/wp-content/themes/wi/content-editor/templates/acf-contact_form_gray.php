<?php // Contact form gray //?>
<?php if ($ceLayout == 'contact_form_gray') { ?>
    <div id="ceID<?php echo $ceIteration; ?>" class="ceInNumbers">
        <div id="sectionContactGormGray">
            <?php containerStart(get_sub_field('container')); ?>
                <div class="sectionContactGormGrayBox displayFlex flexWrap flexXbetween flexXstart">
                    <div class="sectionContactGormGrayItem">
                        <?php echo get_sub_field('editor'); ?>
                    </div>
                </div>
            <?php containerEnd(get_sub_field('container')); ?>
            <img src="<?php echo get_template_directory_uri() . '/images/number_bg.jpg'; ?>" class="img-full" alt="numbers_bg">
        </div>
    </div>
<?php } ?>