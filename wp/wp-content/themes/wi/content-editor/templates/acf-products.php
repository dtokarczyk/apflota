<?php // Products //?>
<?php if ($ceLayout == 'products') { ?>
    <div id="sectionOffer">
        <?php containerStart(get_sub_field('container')); ?>
            <div class="sectionOfferBox displayFlex flexWrap flexXstart flexYstretch">
                <?php $products = get_sub_field('products'); ?>
                <?php if ($products) { ?>
                    <?php foreach ($products as $product) { ?>
                        <a href="<?php echo get_permalink($product->ID); ?>" class="sectionOfferItem sectionOfferItemShow">
                            <span class="sectionOfferItemInside">
                                <span class="sectionOfferItemsImg">
                                    <img class="img-full" alt="<?php echo get_the_title($product->ID); ?>" src="<?php $grafiki = get_field('zdjecie_glowne', $product->ID);
                        echo $grafiki['sizes']['produkt-360x270']; ?>"/>
                                    <?php if (get_field('new', $product->ID) == 1 || get_field('premium', $product->ID) == 1) { ?>
                                        <span class="sectionOfferItemsImgButtons displayFlex flexXstart flexYcenter">
                                            <?php if (get_field('new', $product->ID) == 1) { ?>
                                                <span class="sectionOfferItemsImgButtonsItem New"><?php echo __('Nowość', 'wi'); ?></span>
                                            <?php } ?>
                                            <?php if (get_field('premium', $product->ID) == 1) { ?>
                                                <span class="sectionOfferItemsImgButtonsItem Premium"><?php echo __('Premium', 'wi'); ?></span>
                                            <?php } ?>
                                        </span>
                                    <?php } ?>
                                    <span class="sectionOfferItemsImgSignet">
                                        <img src="<?php echo get_template_directory_uri(); ?>/images/simaxSignet.svg" alt="simax signet">
                                    </span>
                                </span>
                                <span class="sectionOfferItemDesc displayFlex flexXstart flexYcenter">
                                    <span><?php echo get_the_title($product->ID); ?></span>
                                </span>
                            </span>
                        </a>
                    <?php } ?>
                <?php } ?>
            </div>
        <?php containerEnd(get_sub_field('container')); ?>
    </div>
<?php } ?>