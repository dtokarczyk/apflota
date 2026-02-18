<?php // Images / Editor //?>
<?php if ($ceLayout == 'images_editor') { ?>
    <?php containerStart(get_sub_field('container')); ?>
        <div id="ceID<?php echo $ceIteration; ?>" class="ceImagesEditor">
            <div class="ceImagesEditorBox ceImagesEditorBox<?php echo get_sub_field('image_position'); ?> displayFlex flexWrap flexXstart flexYstretch">
                <?php if (get_sub_field('image_size') == 1) { ?>
                    <div class="ceImagesEditorItem ceImagesEditorItemImg" style="background: url(<?php $image = get_sub_field('image');
                    echo $image['sizes']['ce_img-704xX']; ?>)">
                    </div>
                <?php } else { ?>
                    <div class="ceImagesEditorItem ceImagesEditorItemImg displayFlex flexXstart flexYcenter">
                        <img data-src="<?php $image = get_sub_field('image');
                    echo $image['sizes']['ce_img-704xX']; ?>" class="lazy img-full"/>
                    </div>
                <?php } ?>
                <div class="ceImagesEditorItem ceImagesEditorItemDesc displayFlex <?php if (get_sub_field('editor_position') == 1) { ?>flexYstart<?php } else { ?>flexYcenter<?php } ?>">
                    <div><?php echo get_sub_field('editor'); ?></div>
                </div>
            </div>
        </div>
    <?php containerEnd(get_sub_field('container')); ?>
<?php } ?>