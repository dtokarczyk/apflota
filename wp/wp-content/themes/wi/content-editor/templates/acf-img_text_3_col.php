<?php // Images / Text 3 columns //?> 
<?php if ($ceLayout == 'img_text_3_col') { ?>  
    <?php containerStart(get_sub_field('container')); ?>
        <div id="ceID<?php echo $ceIteration; ?>" class="ceImgText3columns">
            <?php if (have_rows("images__text_3_columns")) { ?>
                <div class="ceImgText3columnsBox displayFlex flexWrap flexXstart flexYstart">
                    <?php while (have_rows("images__text_3_columns")) {
                        the_row(); ?>
                        <a class="ceImgText3columnsItem" href="<?php echo get_sub_field('url'); ?>">
                            <span class="ceImgText3columnsItemInside">
                                <span class="ceImgText3columnsImg">
                                    <img data-src="<?php $image = get_sub_field('images');
                        echo $image['sizes']['img-464x464']; ?>"  class="lazy img-full"/>
                                </span>
                                <span class="ceImgText3columnsDesc displayFlex flexXcenter flexYcenter">
                                    <span>
                                        <?php echo get_sub_field('content'); ?>
                                        <span class="button buttonTransparent displayInlineFlex flexXcenter flexYcenter"><?php echo get_sub_field('button'); ?></span>
                                    </span>
                                </span>
                            </span>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?> 
        </div>
    <?php containerEnd(get_sub_field('container')); ?>
<?php } ?>