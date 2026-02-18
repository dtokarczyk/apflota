<?php // Images full //?>
<?php if ($ceLayout == 'image_full') { ?>
    <?php containerStart(get_sub_field('container')); ?>
        <div id="ceID<?php echo $ceIteration; ?>" class="ceImagesFull">
            <img src="<?php echo get_sub_field('image'); ?>" class="img-full"/>
        </div>
    <?php containerEnd(get_sub_field('container')); ?>
<?php } ?>