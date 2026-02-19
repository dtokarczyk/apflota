<?php wp_reset_query(); ?>
<?php if (get_field('cta_-_on__off', wpmlID(2)) != 1 && get_the_ID() != wpmlID(168)) { ?>
    <div class="sectionCTA">
        <div class="containerBig">
            <div class="sectionCTADesc<?php if (wpmlID(2) == get_the_ID()) { ?> ceAnimateUP<?php } ?>">
                <div class="sectionCTADescInside displayFlex flexWrap flexXbetween flexYcenter">
                    <div class="sectionCTADescCol1">
                        <span class="displayFlex flexXcenter flexYcenter">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/cta_phone.svg" alt="phone">
                            <?php echo get_field('cta_-_tresc', wpmlID(2)); ?>
                        </span>
                    </div>
                    <div class="sectionCTADescCol2 displayFlex flexWrap flexXstart flexYcenter">
                        <?php if (have_rows("cta_-_telefony", wpmlID(2))) {
                            $i = 1; ?>
                            <div class="sectionCTAPhoneBox">
                                <?php while (have_rows("cta_-_telefony", wpmlID(2))) {
                                    the_row(); ?>
                                    <a href="tel:<?php echo phoneUrl(get_sub_field('telefon')); ?>" class="button buttonTransparent buttonIconLeft displayFlex flexXstart flexYcenter">
                                        <img src="<?php echo get_template_directory_uri(); ?>/images/phoneIcon.svg" class="svg img-responsive" alt="phone" />
                                        <?php echo get_sub_field('telefon'); ?>
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <a href="<?php echo get_permalink(wpmlID(168)); ?>" class="button displayInlineFlex flexXcenter flexYcenter"><?php echo get_field('cta_-_nazwa_przycisku', wpmlID(2)); ?></a>
                    </div>
                </div>
                <div class="sectionCTAImg">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/cta.png" class="img-responsive" alt="cta">
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<footer>
    <div class="containerBig">
        <div class="sectionFooterLine"></div>
        <div class="sectionFooterBox displayFlex flexXstart flexYstretch">
            <div class="sectionFooterItem">
                <a class="sectionFooterItemLogo" href="<?php echo home_url(); ?>">
                    <img src="<?php echo get_field('logo', wpmlID(2)); ?>" class="svg img-responsive"/>
                </a>

                <?php if (have_rows("socialmedia", wpmlID(2))) { ?>
                    <div class="sectionSocialMedia">
                        <?php while (have_rows("socialmedia", wpmlID(2))) {
                            the_row(); ?>
                            <a href="<?php echo get_sub_field('link'); ?>" target="_blank">
                                <img class="svg" src="<?php echo get_sub_field('ikona'); ?>" />
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php echo get_field('footer_-_dane_firmowe', wpmlID(2)); ?>
            </div>

            <div class="sectionFooterItem displayFlex flexXstart flexYstart">
                <div>
                    <?php if (have_rows("footer_-_menu", wpmlID(2))) { ?>
                        <div>
                            <?php while (have_rows("footer_-_menu", wpmlID(2))) {
                                the_row(); ?>
                                <a href="<?php echo get_sub_field('odnosnik'); ?>" target="<?php echo get_sub_field('target'); ?>">
                                    <?php echo get_sub_field('nazwa'); ?>
                                </a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="sectionFooterItem displayFlex flexXstart flexYstart">
                <div>
                    <?php if (have_rows("footer_-_menu_2", wpmlID(2))) { ?>
                        <div>
                            <?php while (have_rows("footer_-_menu_2", wpmlID(2))) {
                                the_row(); ?>
                                <a href="<?php echo get_sub_field('odnosnik'); ?>" target="<?php echo get_sub_field('target'); ?>">
                                    <?php echo get_sub_field('nazwa'); ?>
                                </a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="sectionFooterBox2 displayFlex flexXstart flexYstretch">
        <div class="containerBig">
            <div class="sectionFooterItemMenu displayFlex flexXbetween flexYcenter">
                <div>
                    <?php if (have_rows("footer_-_menu_left", wpmlID(2))) { ?>
                        <?php while (have_rows("footer_-_menu_left", wpmlID(2))) {
                            the_row(); ?>
                            <a href="<?php echo get_sub_field('odnosnik'); ?>" target="<?php echo get_sub_field('target'); ?>">
                                <?php echo get_sub_field('nazwa'); ?>
                            </a>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="sectionFooterItemMenuRight">
                    <?php if (have_rows("footer_-_menu_right", wpmlID(2))) { ?>
                        <?php while (have_rows("footer_-_menu_right", wpmlID(2))) {
                            the_row(); ?>
                            <a href="<?php echo get_sub_field('odnosnik'); ?>" target="<?php echo get_sub_field('target'); ?>">
                                <?php echo get_sub_field('nazwa'); ?>
                            </a>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

</body>
</html>