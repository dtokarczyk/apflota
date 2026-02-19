<section id="defaultPage">
    <?php $page_id = get_the_ID(); ?>

    <?php if (have_rows('kreator_tresci', $page_id)) { ?>
    <?php
        $x = 0;
        while (have_rows('kreator_tresci', $page_id)) {
            the_row();
            ?>

        <?php // Odstęp //?>
        <?php if (get_row_layout() == 'odstep') { ?>
            <style>
                .odstep-<?php echo $x; ?> {
                    height: <?php echo get_sub_field('size'); ?>rem;
                }
                @media (max-width: 1700px) {
                    .odstep-<?php echo $x; ?> {
                        height: <?php echo str_replace(",", ".", (get_sub_field('size') * 0.9)); ?>rem;
                    }
                }
                @media (max-width: 1500px) {
                    .odstep-<?php echo $x; ?> {
                        height: <?php echo str_replace(",", ".", (get_sub_field('size') * 0.8)); ?>rem;
                    }
                }
                @media (max-width: 1365px) {
                    .odstep-<?php echo $x; ?> {
                        height: <?php echo str_replace(",", ".", (get_sub_field('size') * 0.7)); ?>rem;
                    }
                }
                @media (max-width: 992px) {
                    .odstep-<?php echo $x; ?> {
                        height: <?php echo str_replace(",", ".", (get_sub_field('size') * 0.6)); ?>rem;
                    }
                }
                @media (max-width: 768px) {
                    .odstep-<?php echo $x; ?> {
                        height: <?php echo str_replace(",", ".", (get_sub_field('size') * 0.55)); ?>rem;
                    }
                }
                @media (max-width: 550px) {
                    .odstep-<?php echo $x; ?> {
                        height: <?php echo str_replace(",", ".", (get_sub_field('size') * 0.5)); ?>rem;
                    }
                }
                @media (max-width: 400px) {
                    .odstep-<?php echo $x; ?> {
                        height: <?php echo str_replace(",", ".", (get_sub_field('size') * 0.45)); ?>rem;
                    }
                }
            </style>
            <div class="row">
                <div class="odstep-<?php echo $x; ?>"></div>
            </div>
        <?php } ?>


        <?php // Zdjęcie cała szerokość  //?>
        <?php if (get_row_layout() == 'zdjecie_cala_szerokosc') { ?>
            <div class="containerBig">
                <div class="row imgFull">
                    <img class="lazy img-full" data-src="<?php $zdjecie = get_sub_field('zdjecie');
            echo $zdjecie['url']; ?>" alt="<?php altIMG($zdjecie); ?>" />
                </div>
            </div>
        <?php } ?>


        <?php // Edytor treści //?>
        <?php if (get_row_layout() == 'edytor_tresci') { ?>
            <div class="containerBig">
                <div class="row">
                    <?php echo get_sub_field('edytor_tresci'); ?>
                </div>
            </div>
        <?php } ?>


        <?php // Edytor treści - pomniejszony //?>
        <?php if (get_row_layout() == 'edytor_tresci_-_pomniejszony') { ?>
            <div class="containerBig editorSmall">
                <div class="row">
                    <?php echo get_sub_field('edytor_tresci'); ?>
                </div>
            </div>
        <?php } ?>


        <?php // Edytor treści 2 kolumny //?>
        <?php if (get_row_layout() == 'edytor_tresci_2_kolumny') { ?>
            <div class="containerBig">
                <div class="row">
                    <div class="col-2">
                        <?php echo get_sub_field('edytor_tresci_1_kolumna'); ?>
                    </div>
                    <div class="col-2">
                        <?php echo get_sub_field('edytor_tresci_2_kolumna'); ?>
                    </div>
                </div>
            </div>
        <?php } ?>


        <?php // Edytor treści 2 kolumny - kontakt //?>
        <?php if (get_row_layout() == 'edytor_tresci_2_kolumny_-_kontakt') { ?>
            <div class="container col2Contact">
                <div class="row">
                    <div class="col-2">
                        <?php echo get_sub_field('edytor_tresci_1_kolumna'); ?>
                    </div>
                    <div class="col-2">
                        <?php echo get_sub_field('edytor_tresci_2_kolumna'); ?>
                    </div>
                </div>
            </div>
        <?php } ?>


        <?php // Edytor treści 3 kolumny //?>
        <?php if (get_row_layout() == 'edytor_tresci_3_kolumny') { ?>
            <div class="containerBig">
                <div class="row">
                    <div class="col-3">
                        <?php echo get_sub_field('edytor_tresci_1_kolumna'); ?>
                    </div>
                    <div class="col-3">
                        <?php echo get_sub_field('edytor_tresci_2_kolumna'); ?>
                    </div>
                    <div class="col-3">
                        <?php echo get_sub_field('edytor_tresci_3_kolumna'); ?>
                    </div>
                </div>
            </div>
        <?php } ?>





        <?php // Zdjęcie - Treść  //?>
        <?php if (get_row_layout() == 'zdjecie_-_tresc_-_wysrodkowane') { ?>
            <div class="containerBig">
                <div class="row textFoto">
                    <div class="textFotoImg">
                        <img class="lazy img-full" data-src="<?php $images = get_sub_field('edytor_tresci_1_kolumna');
            echo $images["sizes"]["img-700x1500"]; ?>" alt="<?php altIMG($images); ?>" />
                    </div>
                    <div class="textFotoContent">
                        <div>
                            <?php echo get_sub_field('edytor_tresci_2_kolumna'); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>


        <?php // Treść - Zdjęcie  //?>
        <?php if (get_row_layout() == 'tresc_-_zdjecie_-_wysrodkowane') { ?>
            <div class="containerBig">
                <div class="row textFoto textFotoRight">
                    <div class="textFotoContent">
                        <div>
                            <?php echo get_sub_field('edytor_tresci_1_kolumna'); ?>
                        </div>
                    </div>
                    <div class="textFotoImg">
                        <img class="lazy img-full" data-src="<?php $images = get_sub_field('edytor_tresci_2_kolumna');
            echo $images["sizes"]["img-700x1500"]; ?>" alt="<?php altIMG($images); ?>" />
                    </div>
                </div>
            </div>
        <?php } ?>




        <?php // Galeria //?>
        <?php if (get_row_layout() == 'galeria') { ?>
            <?php if (get_sub_field('galeria') != '') { ?>
                <?php
                    $images = "";
                $images = get_sub_field('galeria');
                if ($images) {
                    ?>
                    <div class="editorGallery">
                        <div class="containerBig">
                            <div class="editorGalleryBox displaFlex flexWrap flexXstart flexYstretch">
                                <?php foreach ($images as $image) { ?>
                                    <a class="editorGalleryItem displaFlex flexXstart flexYstart" href="<?php echo $image['url']; ?>" data-gallery="#gallery<?php echo $x; ?>">
                                        <span><img class="lazy img-full" data-src="<?php echo $image['sizes']['blog-460x280']; ?>" alt="<?php echo __("Galeria", "wi"); ?>" /></span>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php wp_reset_query(); ?>
            <?php } ?>
        <?php } ?>


        <?php // Zdjęcie cała szerokość - treść //?>
        <?php if (get_row_layout() == 'zdjecie_cala_szerokosc_-_tresc') { ?>
            <div id="topBanner" class="topBannerImg">
                <div class="lazy" data-src="<?php $zdjecie = get_sub_field('tlo');
            echo $zdjecie["sizes"]["topbanner"]; ?>">
                    <div class="topBannerContainer displaFlex flexWrap flexXbetween flexYcenter">
                        <div class="topBannerContainerBox">
                            <div class="topBannerTitle">
                                <?php echo get_sub_field('tresc'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>



        <?php // Ikona / treść //?>
        <?php if (get_row_layout() == 'ikona__tresc') { ?>
            <div id="sectionOffer">
                <div class="container">
                    <?php if (have_rows("ikona__tresc")) { ?>
                        <div class="sectionOfferBox displaFlex flexWrap flexXstart flexYstretch">
                            <?php while (have_rows("ikona__tresc")) {
                                the_row(); ?>
                                <div class="sectionOfferItem">
                                    <img data-src="<?php echo get_sub_field('zdjecie'); ?>" alt="img" class="lazy"/>
                                    <h5><?php echo get_sub_field('opis'); ?></h5>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>



        <?php // Lista //?>
        <?php if (get_row_layout() == 'lista') { ?>
            <div id="sectionList">
                <div class="container">
                    <?php if (have_rows("lista")) { ?>
                        <div class="sectionListBox displaFlex flexWrap flexXstart flexYstretch">
                            <?php while (have_rows("lista")) {
                                the_row(); ?>
                                <div class="sectionListItem">
                                    <div class="displaFlex flexXstart flexYstart">
                                        <img src="<?php echo get_template_directory_uri(); ?>/images/checkCircle.svg" alt="<?php echo __("check", "wi"); ?>" />
                                        <h5><?php echo get_sub_field('opis'); ?></h5>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>


        <?php // Jak działa platforma //?>
        <?php if (get_row_layout() == 'jak_dziala_platforma') { ?>
            <div id="sectionPlatform">
                <img class="lazy sectionPlatformBG" src="<?php echo get_template_directory_uri(); ?>/images/gradientPlatform.svg">
                <div class="container">
                    <div class="sectionPlatformTitle"><?php echo get_sub_field('naglowek'); ?></div>
                    <?php if (have_rows("jak_dziala_platforma")) { ?>
                        <div class="sectionPlatformBox">
                            <?php while (have_rows("jak_dziala_platforma")) {
                                the_row(); ?>
                                <div class="sectionPlatformItem displaFlex flexWrap flexXstart flexYstart">
                                    <div>
                                        <div class="sectionPlatformItemImg"><img src="<?php echo get_sub_field('ikona'); ?>" alt="<?php echo __("ikona", "wi"); ?>" /></div>
                                        <div class="sectionPlatformItemDesc"><?php echo get_sub_field('opis'); ?></div>
                                    </div>
                                    <div>
                                        <img src="<?php echo get_sub_field('grafika'); ?>" alt="<?php echo __("grafika", "wi"); ?>" />
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>



        <?php // Pakiety //?>
        <?php if (get_row_layout() == 'pakiety') { ?>
            <div id="sectionPackages">
                <div class="container">
                    <div class="displayTable">
                        <div class="displayTableRow displayTableRowTH">
                            <div class="displayTableCell"><h6><?php echo get_sub_field('naglowek_1'); ?></h6></div>
                            <div class="displayTableCell"><h6><?php echo get_sub_field('naglowek_2'); ?></h6></div>
                            <div class="displayTableCell"><h6><?php echo get_sub_field('naglowek_3'); ?></h6></div>
                            <div class="displayTableCell"><h6><?php echo get_sub_field('naglowek_4'); ?></h6></div>
                        </div>

                        <?php if (have_rows("tabela")) { ?>
                            <?php while (have_rows("tabela")) {
                                the_row(); ?>
                                <div class="displayTableRow">
                                    <div class="displayTableCell">
                                        <span><?php echo get_sub_field('nazwa'); ?></span>
                                    </div>
                                    <div class="displayTableCell">
                                        <div class="displayTableCellMobile"><span><?php echo $kolumna_2_nazwa; ?></span></div>
                                        <?php if (get_sub_field('pakiet_1') == 1) { ?>
                                            <div class="sectionTabsTableCheck1"><img src="<?php echo get_template_directory_uri(); ?>/images/checkTable.svg"></div>
                                        <?php } else { ?>
                                            <div class="sectionTabsTableCheck0"></div>
                                        <?php } ?>
                                    </div>
                                    <div class="displayTableCell">
                                        <div class="displayTableCellMobile"><span><?php echo $kolumna_3_nazwa; ?></span></div>
                                        <?php if (get_sub_field('pakiet_2') == 1) { ?>
                                            <div class="sectionTabsTableCheck1"><img src="<?php echo get_template_directory_uri(); ?>/images/checkTable.svg"></div>
                                        <?php } else { ?>
                                            <div class="sectionTabsTableCheck0"></div>
                                        <?php } ?>
                                    </div>
                                    <div class="displayTableCell">
                                        <div class="displayTableCellMobile"><span><?php echo $kolumna_4_nazwa; ?></span></div>
                                        <?php if (get_sub_field('pakiet_3') == 1) { ?>
                                            <div class="sectionTabsTableCheck1"><img src="<?php echo get_template_directory_uri(); ?>/images/checkTable.svg"></div>
                                        <?php } else { ?>
                                            <div class="sectionTabsTableCheck0"></div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>



        <?php // Subskrybcje //?>
        <?php if (get_row_layout() == 'subskrybcje') { ?>
            <div id="sectionSubscriptions">
                <div class="container">
                    <?php if (have_rows("subskrybcje")) { ?>
                        <div class="sectionSubscriptionsBox displaFlex flexWrap flexXstart flexYstretch">
                            <?php while (have_rows("subskrybcje")) {
                                the_row(); ?>
                                <div class="sectionSubscriptionsItem sectionSubscriptionsItem<?php echo get_sub_field('promocja'); ?>">
                                    <div>
                                        <h4 class="displaFlex flexXstart flexYcenter">
                                            <?php if (get_sub_field('promocja') == 1) { ?>
                                                <span class="h4promo"><?php echo __('Promocja', 'wi'); ?></span>
                                            <?php } ?>
                                            <span><?php echo get_sub_field('nazwa'); ?></span>
                                        </h4>
                                        <div class="sectionSubscriptionsDesc"><?php echo get_sub_field('opis'); ?></div>
                                        <div class="sectionSubscriptionsBottom">
                                            <div class="sectionSubscriptionsPrice displaFlex flexXstart flexYend">
                                                <?php if (get_sub_field('promocja') == 1) { ?>
                                                    <h3><?php echo get_sub_field('cena_promocyjna'); ?></h3>
                                                    <h6><?php echo get_sub_field('cena'); ?></h6>
                                                <?php } else { ?>
                                                    <h3><?php echo get_sub_field('cena'); ?></h3>
                                                <?php } ?>
                                            </div>
                                            <div class="sectionSubscriptionsTime"><?php echo get_sub_field('okres'); ?></div>
                                            <a href="<?php echo get_sub_field('odnosnik'); ?>" class="button button<?php echo get_sub_field('promocja'); ?> displaInlineFlex flexXcenter flexYcenter"><span><?php echo get_sub_field('nazwa_przycisku'); ?></span></a>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>


    <?php
        $x++;
        }
        ?>
    <?php } ?>
</section>

<?php wp_reset_query(); ?>

<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls"><div class="slides"></div><h3 class="title"></h3><a class="prev">‹</a><a class="next">›</a><a class="close">×</a><a class="play-pause"></a><ol class="indicator"></ol></div>