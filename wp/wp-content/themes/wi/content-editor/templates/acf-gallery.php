<?php // Gallery //?>
<?php if ($ceLayout == 'gallery') { ?>
    <?php containerStart(get_sub_field('container')); ?>
        <div id="ceID<?php echo $ceIteration; ?>" class="ceGallery">
           <?php if (get_sub_field('gallery') != '') { ?>
                <?php $images = ""; ?>
                <?php $images = get_sub_field('gallery'); ?>
                <?php if ($images) { ?>
                    <div class="ceGalleryBox ceGalleryBox_gallery_type0 displayFlex flexWrap flexXstart flexXstart">
                        <?php foreach ($images as $image) { ?>
                            <div class="ceGalleryItem" data-href="<?php echo $image['url']; ?>" data-gallery="#gallery<?php echo $ceIteration; ?>" title="<?php echo $image['alt']; ?>">
                                <img src="<?php echo $image['sizes']['blog-550x313']; ?>" class="img-full" alt="<?php echo $image['alt']; ?>"/>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php wp_reset_query(); ?>
            <?php } ?>
        </div>
    <?php containerEnd(get_sub_field('container')); ?>
<?php } ?>