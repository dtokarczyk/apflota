<?php
/**
* Template name: Strona główna
*/
get_header();
setup_postdata($post);
?>

<div id="sectionSlider">
    <div class="sectionSliderImg">
        <?php if (get_field('slider_-_video', wpmlID(2)) != "") { ?>
            <video autoplay muted playsinline loop>
                <source src="<?php echo get_field('slider_-_video', wpmlID(2)); ?>" type="video/mp4">
            </video>
        <?php } else { ?>
            <img src="<?php echo get_field('slider_-_zdjecie', wpmlID(2)); ?>" class="img-responsive"/>
        <?php } ?>
    </div>
    <?php if (get_field('slider_-_naglowek', wpmlID(2)) != "") { ?>
        <div class="sectionSliderTitle">
            <div class="containerBig">
                <?php echo get_field('slider_-_naglowek', wpmlID(2)); ?>
            </div>
        </div>
    <?php } ?>
    <div class="headerSliderScroll displayFlex flexXcenter flexYcenter">
        <img src="<?php echo get_template_directory_uri(); ?>/images/scroll.svg" class="svg img-responsive" alt="scroll">
    </div>
    <div id="sliderScroll"></div>
</div>


<?php if (get_field('o_nas_-_on__off', wpmlID(2)) != 1) { ?>
    <div class="overflowHidden">
        <div class="containerBig">
            <div class="section50x50">
                <?php while (have_rows("o_nas_-_5050", wpmlID(2))) {
                    the_row(); ?>
                    <div class="section50x50box displayFlex flexWrap flexXstart flexYcenter ceAnimateUP">
                        <div class="section50x50boxDesc"><?php echo get_sub_field('tresc'); ?></div>
                        <div class="section50x50boxImg"><img data-src="<?php $zdjecie = get_sub_field('zdjecie');
                    echo $zdjecie["sizes"]["ce_img-704xX"]; ?>" alt="img" class="img-full lazy" /></div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>


<?php if (get_field('oferta_-_on__off', wpmlID(2)) != 1) { ?>
    <div id="sectionOffer">
        <div class="containerBig">
            <div class="sectionOfferTitle ceAnimateUP"><?php echo get_field('oferta_-_naglowek', wpmlID(2)); ?></div>
            <div class="sectionOfferContainer ceAnimateUP">
                <?php $offers = get_field("oferta_-_polecane", wpmlID(2)); ?>
                <?php if ($offers) { ?>
                    <div class="sectionOfferBox displayFlex flexWrap flexXstart flexYstretch">
                        <?php foreach ($offers as $post) {
                            setup_postdata($post); ?>
                            <a href="<?php echo get_permalink(); ?>" class="sectionOfferItem">
                                <span class="sectionOfferItemInside">
                                    <span class="sectionOfferItemsImg">
                                        <img class="img-full" alt="<?php echo get_the_title(); ?>" src="<?php $grafiki = get_field('zdjecie_glowne');
                            echo $grafiki['sizes']['produkt-500x250']; ?>" />
                                    </span>
                                    <span class="sectionOfferItemDesc">
                                        <span class="sectionOfferItemDesc1 displayFlex flexWrap flexXstart flexYcenter">
                                            <span class="displayFlex flexXcenter flexYcenter">
                                                <img src="<?php echo get_template_directory_uri(); ?>/images/fuel.svg" alt="fuel">
                                                <?php $rodzajePaliwa = get_the_terms(get_the_ID(), 'rodzaj-paliwa');
                            echo $rodzajePaliwa[0]->name;?>
                                            </span>
                                            <span class="displayFlex flexXcenter flexYcenter">
                                                <img src="<?php echo get_template_directory_uri(); ?>/images/gearbox.svg" alt="gearbox">
                                                <?php $skrzynieBiegowDo = get_the_terms(get_the_ID(), 'skrzynia-biegow');
                            echo $skrzynieBiegowDo[0]->name; ?>
                                            </span>
                                            <span class="displayFlex flexXcenter flexYcenter">
                                                <img src="<?php echo get_template_directory_uri(); ?>/images/segment.svg" alt="segment">
                                                <?php $segmenty = get_the_terms(get_the_ID(), 'segment');
                            echo $segmenty[0]->name; ?>
                                            </span>
                                        </span>
                                        <span class="sectionOfferItemDesc2">
                                            <span class="sectionOfferItemDescTitle"><?php echo get_the_title(); ?></span>
                                            <?php echo get_field('silnik'); ?>
                                            <span class="sectionOfferItemDescTitle"><?php echo __("od", "wi"); ?> <?php echo get_field('cena_od'); ?> <?php echo __("zł", "wi"); ?></span>
                                            <?php echo __("za miesiąc", "wi"); ?>
                                        </span>
                                    </span>
                                </span>
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if (get_field('oferta_-_odnosnik', wpmlID(2)) != "") { ?>
                    <div class="sectionOfferButton">
                        <a class="button displayInlineFlex flexXcenter flexYcenter" href="<?php echo get_field('oferta_-_odnosnik', wpmlID(2)); ?>">
                            <?php echo get_field('oferta_-_nazwa_przycisku', wpmlID(2)); ?>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
        <img src="<?php echo get_template_directory_uri() . '/images/number_bg.jpg'; ?>" class="img-full" alt="numbers_bg">
    </div>
    <?php wp_reset_postdata(); ?>
<?php } ?>


<?php if (get_field('tabela_-_on__off', wpmlID(2)) != 1) { ?>
    <div id="sectionTable">
        <div class="containerBig">
            <div class="sectionTableTitle ceAnimateUP"><?php echo get_field('tabela_-_naglowek', wpmlID(2)); ?></div>
            <div class="sectionTable ceAnimateUP">
                <table class="sectionTableBox">
                    <?php if (have_rows("tabela", wpmlID(2))) { ?>
                        <?php while (have_rows("tabela", wpmlID(2))) {
                            the_row(); ?>
                            <tr class="sectionTableItem">
                                <td class="sectionTableItemCol"><span><?php echo do_shortcode(get_sub_field('kolumna_1')); ?></span></td>
                                <td class="sectionTableItemCol"><span><?php echo do_shortcode(get_sub_field('kolumna_2')); ?></span></td>
                                <td class="sectionTableItemCol"><span><?php echo do_shortcode(get_sub_field('kolumna_3')); ?></span></td>
                                <td class="sectionTableItemCol"><span><?php echo do_shortcode(get_sub_field('kolumna_4')); ?></span></td>
                                <td class="sectionTableItemCol"><span><?php echo do_shortcode(get_sub_field('kolumna_5')); ?></span></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </table>
            </div>
            <div><?php echo get_field('tabela_-_pod', wpmlID(2)); ?></div>
        </div>
    </div>
<?php } ?>


<?php if (get_field('sekcja_5050_-_on__off', wpmlID(2)) != 1) { ?>
    <div class="overflowHidden">
        <div class="containerBig">
            <div class="section50x50">
                <?php while (have_rows("sekcja_5050", wpmlID(2))) {
                    the_row(); ?>
                    <div class="section50x50box ceAnimateUP displayFlex flexWrap flexXstart flexYcenter">
                        <div class="section50x50boxDesc"><?php echo get_sub_field('tresc'); ?></div>
                        <div class="section50x50boxImg"><img data-src="<?php $zdjecie = get_sub_field('zdjecie');
                    echo $zdjecie["sizes"]["ce_img-704xX"]; ?>" alt="img" class="img-full lazy" /></div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>


<?php if (get_field('zaufali_nam_-_on__off', wpmlID(2)) != 1) { ?>
    <div id="sectionLogotype">
        <div class="containerBig">
            <div class="sectionOfferTitle ceAnimateUP"><?php echo get_field('zaufali_nam_-_naglowek', wpmlID(2)); ?></div>
            <div class="sectionLogotypeBox ceAnimateUP">
                <?php if (have_rows("zaufali_nam", wpmlID(2))) {
                    $i = 1; ?>
                    <?php while (have_rows("zaufali_nam", wpmlID(2))) {
                        the_row(); ?>
                        <?php $logotyp = get_sub_field('logotyp'); ?>
                        <?php $odnosnik = get_sub_field('odnosnik'); ?>
                        <?php if ($odnosnik != "") { ?>
                            <a href="<?php echo $odnosnik; ?>" title="logotypy" target="_blank">
                        <?php } else { ?>
                            <div>
                        <?php } ?>
                                <span><img src="<?php echo $logotyp; ?>" class="img-responsive"/></span>
                        <?php if ($odnosnik != "") { ?>
                            </a>
                        <?php } else { ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>


<?php get_footer(); ?>