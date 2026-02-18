<?php get_header();
setup_postdata($post); ?>

<div class="containerBig">
    <div class="sectioOfferSingle">
        <div class="sectioOfferBox displayFlex flexWrap flexXbetween flexYstart">
            <div class="sectioOfferItem">
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
            </div>
            <div class="sectioOfferItem">
                <div class="sectioOfferGallery">
                    <div class="sectioOfferGalleryBox">
                        <?php $images = "";
$images = get_field('galeria'); ?>
                        <?php if ($images) { ?>
                            <div class="sectioOfferGallerySlider sectioOfferGallerySliderSingle">
                                <?php foreach ($images as $image) { ?>
                                    <a class="sectioOfferGalleryItem" href="<?php echo $image['url']; ?>" data-gallery="#galleryOffer">
                                        <img class="img-full" src="<?php echo $image['sizes']['produkt-824x464']; ?>" alt="<?php echo __("Galeria", "wi"); ?>" />
                                    </a>
                                <?php } ?>
                            </div>
                            <div class="sectioOfferGallerySliderNav sectioOfferGallerySliderNavSingle">
                                <?php foreach ($images as $image) { ?>
                                    <div class="sectioOfferGalleryItemNav">
                                        <img class="img-full" src="<?php echo $image['sizes']['produkt-194x108']; ?>" alt="<?php echo __("Galeria", "wi"); ?>" />
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="sectioOfferGallerySlider sectioOfferGallerySliderSingle">
                                <?php $zdjecie = get_field('zdjecie_glowne');  ?>
                                <a class="sectioOfferGalleryItem" href="<?php echo $zdjecie['url']; ?>" data-gallery="#galleryOffer">
                                    <img class="img-full" src="<?php echo $zdjecie['sizes']['produkt-824x464']; ?>" alt="<?php echo __("Galeria", "wi"); ?>" />
                                </a>
                            </div>
                        <?php } ?> 
                    </div>
                </div>
                  
                <?php if (get_field('opis_pod_galeria') != "") { ?> 
                    <div class="sectioOfferGalleryDesc">
                        <?php echo get_field('opis_pod_galeria'); ?>
                    </div>
                <?php } ?> 
                
                <?php if (have_rows("informacje_podstawowe")) { ?> 
                    <div class="sectioOfferInformation">
                        <div class="h6"><?php echo get_field('informacje_podstawowe_-_naglowek'); ?></div>
                        <div class="sectioOfferInformationBox displayFlex flexWrap flexXstart flexYcenter">
                            <?php while (have_rows("informacje_podstawowe")) {
                                the_row(); ?>
                                <div class="sectioOfferInformationItem">
                                    <div class="sectioOfferInformationItemTitle"><?php echo get_sub_field('nazwa'); ?></div>
                                    <div class="sectioOfferInformationItemValue"><?php echo get_sub_field('wartosc'); ?></div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                
                <?php if (have_rows("faq")) { ?> 
                    <div id="contentEditor">
                        <div class="ceFaq">
                            <?php while (have_rows("faq")) {
                                the_row(); ?>
                                <div class="ceFaqBox">
                                    <div class="ceFaqQuestion">
                                        <div class="ceFaqQuestionTitle">
                                            <?php echo get_sub_field('naglowek'); ?>
                                        </div>
                                        <div class="ceFaqQuestionButton">
                                            <?php if (file_exists(get_template_directory().'/images/arrowFAQ.svg')) { ?>
                                                <img src="<?php echo get_template_directory_uri(); ?>/images/arrowFAQ.svg" alt="Icon" />
                                            <?php } ?> 
                                        </div>  
                                    </div>
                                    <div class="ceFaqAnswer">
                                        <div>
                                            <div class="ceFaqAnswerPaddingTop"></div>
                                            <?php echo get_sub_field('tresc'); ?>
                                            <div class="ceFaqAnswerPaddingBottom"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?> 
                        </div>
                    </div>
                <?php } ?>
                
                <?php if (have_rows("do_pobrania")) { ?> 
                    <div id="contentEditor">
                        <div class="ceFilesDownload">
                            <div class="h6"><?php echo get_field('do_pobrania_-_naglowek'); ?></div>
                            <div class="sectionAttachments sectionProductAttachments">
                                <?php while (have_rows("do_pobrania")) {
                                    the_row(); ?>
                                    <div class="attachment application">
                                        <a download="" target="_blank" href="<?php echo get_sub_field('zalacznik'); ?>" class="displayFlex flexXbetween flexYcenter">
                                            <span class="attachmentsTitle displayFlex flexXstart flexYcenter">
                                                <img src="<?php echo get_template_directory_uri(); ?>/images/downloadBefore.svg" alt="download" />
                                                <span><?php echo koncowki_luk_mod(get_sub_field('nazwa')); ?></span>
                                            </span>
                                            <span class="attachmentsSizeTypeBtn displayFlex flexXbetween flexYcenter">
                                                <span class="button buttonTransparent displayFlex flexXstart flexYcenter">
                                                    <span class="displayFlex flexXbetween flexYcenter">
                                                        <img src="<?php echo get_template_directory_uri(); ?>/images/download.svg" alt="download" />
                                                        <?php echo __('pobierz plik', 'wi'); ?>
                                                    </span>
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                <?php } ?> 
                            </div>
                        </div>
                    </div>
                <?php } ?>
                
            </div>
            <div class="sectioOfferItem">
                <?php if (get_field('csv', wpmlID(2)) != "") { ?>
                    <div class="sectioOfferCalc" carid="<?php echo get_field('id'); ?>">
                        <div class="sectioOfferCalcTitle"><?php echo get_field('kalkulator_-_twoja_rata_miesieczna', wpmlID(2)); ?></div>
                        <div class="sectioOfferCalcPrice"><span>----</span><?php echo __("zł", "wi"); ?></div>
                        <div class="sectioOfferCalcTitle"><?php echo get_field('kalkulator_-_okres_finasowania_w_miesiacach', wpmlID(2)); ?></div>
                        <div class="sectioOfferCalcMonths buttonCalcContainer displayFlex flexXcenter flexYcenter">
                            <div class="buttonCalc buttonCalcActive displayFlex flexXcenter flexYcenter"><span class="val" value="1">--</span></div>
                            <div class="buttonCalc displayFlex flexXcenter flexYcenter"><span class="val" value="2">--</span></div>
                            <div class="buttonCalc displayFlex flexXcenter flexYcenter"><span class="val" value="3">--</span></div>
                        </div>
                        <div class="sectioOfferCalcTitle"><?php echo get_field('kalkulator_-_oplata_wstepna', wpmlID(2)); ?></div>
                        <div class="sectioOfferCalcPercent buttonCalcContainer displayFlex flexXcenter flexYcenter">
                            <div class="buttonCalc displayFlex flexXcenter flexYcenter"><span class="val val0" value="0">----</span><span class="ins"><?php echo __("zł", "wi"); ?></span></div>
                            <div class="buttonCalc displayFlex flexXcenter flexYcenter"><span class="val val10" value="10">----</span><span class="ins"><?php echo __("zł", "wi"); ?></span></div>
                            <div class="buttonCalc buttonCalcActive displayFlex flexXcenter flexYcenter"><span class="val val20" value="20">----</span><span class="ins"><?php echo __("zł", "wi"); ?></span></div>
                        </div>
                        <div class="sectioOfferCalcTitle"><?php echo get_field('kalkulator_-_roczny_limit_kilometrow', wpmlID(2)); ?></div>
                        <div class="sectioOfferCalcKilometers buttonCalcContainer displayFlex flexXcenter flexYcenter" tys="<?php echo __("tyś", "wi"); ?>">
                            <div class="buttonCalc buttonCalcActive displayFlex flexXcenter flexYcenter"><span value="1">--</span><span class="ins"><?php echo __("tyś", "wi"); ?></span></div>
                            <div class="buttonCalc displayFlex flexXcenter flexYcenter"><span class="val" value="2">--</span><span class="ins"><?php echo __("tyś", "wi"); ?></span></div>
                            <div class="buttonCalc displayFlex flexXcenter flexYcenter"><span class="val" value="3">--</span><span class="ins"><?php echo __("tyś", "wi"); ?></span></div>
                            <div class="buttonCalc displayFlex flexXcenter flexYcenter"><span class="val" value="4">--</span><span class="ins"><?php echo __("tyś", "wi"); ?></span></div>
                        </div>
                        <div class="sectioOfferCalcButton">
                            <a class="button displayFlex flexXcenter flexYcenter" href="<?php echo get_permalink(wpmlID(168)); ?>"><?php echo get_field('kalkulator_-_zapytaj_o_szczegoly', wpmlID(2)); ?></a>
                        </div>
                    </div>
                <?php } ?>
                <?php if (get_field('w_pakiecie')) { ?> 
                    <div class="inPackage">
                        <div class="h6"><?php echo get_field('w_pakiecie_-_naglowek'); ?></div>
                        <div class="inPackageBox">
                            <?php $terms = get_field('w_pakiecie'); ?>
                            <?php foreach ($terms as $term) { ?>
                                <div class="inPackageItem displayFlex flexXstart flexYcenter">
                                    <img src="<?php echo get_field('ikona', 'w-pakiecie_' . $term->term_id); ?>" alt="<?php echo $term->name; ?>" />
                                    <?php if (get_field('zlamana_nazwa', 'w-pakiecie_' . $term->term_id) != "") { ?>
                                        <div><?php echo get_field('zlamana_nazwa', 'w-pakiecie_' . $term->term_id); ?></div>
                                    <?php } else { ?>
                                        <div><?php echo $term->name; ?></div>
                                    <?php } ?> 
                                </div>
                            <?php } ?> 
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div id="sectionOffer">
    <div class="containerBig">
        <div class="sectionOfferTitle">
			<?php echo get_field('zobacz_inne_oferty_-_naglowek', wpmlID(2)); ?>
        </div>
        <div class="sectionOfferBox displayFlex flexWrap flexXstart flexYstretch">
            <?php $offers = get_field('zobacz_inne_oferty'); ?>
            <?php if ($offers) { ?>
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
            <?php } else { ?>
                <?php
                    $args = array(
                        'post_type'         => 'post',
                        'posts_per_page'    => 4,
                        'orderby' => 'date',
                        'order'   => 'DESC',
                        'post__not_in' => array(get_the_ID())
                    );
                $wp_query = new WP_Query($args);
                ?>
                <?php while (have_posts()) {
                    the_post(); ?>
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
            <?php } ?>
        </div>
        <div class="sectionOfferButton">
            <a class="button displayInlineFlex flexXcenter flexYcenter" href="<?php echo slashAdd(home_url()); ?><?php echo __("oferta", "WordPress"); ?>">
                <?php echo __("Więcej ofert", "wi"); ?>
            </a>
        </div>
    </div>
    <img src="<?php echo get_template_directory_uri() . '/images/number_bg.jpg'; ?>" class="img-full" alt="numbers_bg">
</div>  

<?php wp_reset_postdata(); ?>
<?php wp_reset_query(); ?>

<?php the_content(); ?>
<?php get_template_part('content-editor/editor'); ?>

<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls"><div class="slides"></div><h3 class="title"></h3><a class="prev">‹</a><a class="next">›</a><a class="close">×</a><a class="play-pause"></a><ol class="indicator"></ol></div>

<?php get_footer(); ?>