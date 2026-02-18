<?php // Customer zone form //?>
<?php if ($ceLayout == 'customer_zone_form') { ?>
    <div id="ceCustomerZone">
        <?php containerStart(get_sub_field('container')); ?>
            <div class="ceCustomerZone">
                <a class="button buttonTransparent displayInlineFlex flexXcenter flexYcenter" href="<?php echo get_permalink(wpmlID(164)); ?>" target="_self">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/arrowBack.svg" class="img-responsive" alt="back" style="margin-right:12px"/>
                    <span><?php echo __("Powrót do strefy klienta", "wi"); ?></span>
                </a>
                <hr />
                <div class="spaceBig"></div><div class="spaceRegular"></div>
                <div class="ceCustomerZoneBox displayFlex flexWrap flexXbetween flexXstart">
                    <div class="ceCustomerZoneItem">
                        <?php echo get_sub_field('content_-_1_column'); ?>
                    </div>
                    <div class="ceCustomerZoneItem">
                        <?php echo get_sub_field('content_-_2_column'); ?>
                    </div>
                </div>
            </div>
        <?php containerEnd(get_sub_field('container')); ?>
    </div>
<?php } ?>