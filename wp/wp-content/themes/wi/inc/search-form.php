<div class="sectionOfferSearch">
    <h2><?php echo __('Oferta', 'wi'); ?></h2>
    <div class="sectionSearch formSearch displayFlex flexWrap flexXstart flexYcenter">
        <?php $rodzajeNadwozia = get_terms(['taxonomy' => 'rodzaj-nadwozia', 'hide_empty' => false]); ?>
        <?php if (!empty($rodzajeNadwozia)) { ?>
            <div class="selectBodies">
                <div class="checkboxBox">
                    <div class="checkboxTitle"><?php echo __("Typ nadwozia", "wi"); ?></div>
                    <div class="checkboxButton"><?php echo __("Wybierz", "wi"); ?></div>
                    <div class="checkboxList displayFlex flexWrap flexXstart flexYcenter">
                        <?php foreach ($rodzajeNadwozia as $rodzajNadwozia) { ?>
                            <div class="checkboxListItem displayFlex flexXstart flexYcenter">
                                <input type="checkbox" id="bodies<?php echo $rodzajNadwozia->term_id; ?>" name="bodies" value="<?php echo $rodzajNadwozia->term_id; ?>" <?php if (in_array($rodzajNadwozia->term_id, explode(",", $_GET["bodies"]))) {
                                    echo ' checked';
                                } ?> />
                                <label for="bodies<?php echo $rodzajNadwozia->term_id; ?>"><span></span><?php echo $rodzajNadwozia->name; ?></label>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php $markiAuta = get_terms(['taxonomy' => 'marka-auta', 'hide_empty' => false]); ?>
        <?php if (!empty($markiAuta)) { ?>
            <div class="selectMark">
                <div class="checkboxBox">
                    <div class="checkboxTitle"><?php echo __("Marka", "wi"); ?></div>
                    <div class="checkboxButton"><?php echo __("Wybierz", "wi"); ?></div>
                    <div class="checkboxList displayFlex flexWrap flexXstart flexYcenter">
                        <?php foreach ($markiAuta as $markaAuta) { ?>
                            <div class="checkboxListItem displayFlex flexXstart flexYcenter">
                                <input type="checkbox" id="mark<?php echo $markaAuta->term_id; ?>" name="mark" value="<?php echo $markaAuta->term_id; ?>" <?php if (in_array($markaAuta->term_id, explode(",", $_GET["mark"]))) {
                                    echo ' checked';
                                } ?> />
                                <label for="mark<?php echo $markaAuta->term_id; ?>"><span></span><?php echo $markaAuta->name; ?></label>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php $ratyDo = get_terms(['taxonomy' => 'rata-do', 'hide_empty' => false]); ?>
        <?php if (!empty($ratyDo)) { ?>
            <div class="selectInstallment">
                <div class="installmentPriceSlider">
                    <div class="installmentPriceSliderTop displayFlex flexXbetween flexYcenter">
                        <div><?php echo __("Miesięczna rata do:", "wi"); ?></div>
                        <?php $installmentMin = get_field('wyszukiwarka_cena_od', wpmlID(2)); ?>
                        <?php $installmentMax = get_field('wyszukiwarka_cena_do', wpmlID(2)); ?>
                        <?php $installmentStep = get_field('wyszukiwarka_wielkosc_kroku', wpmlID(2)); ?>
                        <?php if ($_GET["installment"] > 0) { ?>
                            <?php $installmentCheck = intval($_GET["installment"]); ?>
                        <?php } else { ?>
                            <?php $installmentCheck = $installmentMax; ?>
                        <?php } ?>
                        <div class="installmentPriceSliderTopPln"><output class="installmentPriceOutput"><?php echo $installmentCheck; ?></output> zł netto</div>
                    </div>
                    <div class="installmentPriceSliderBottom">
                        <input id="inputRange" class="installmentPrice" name="installment" type="range" min="<?php echo $installmentMin; ?>" max="<?php echo $installmentMax; ?>" value="<?php echo $installmentCheck; ?>" step="<?php echo $installmentStep; ?>">
                    </div>
                </div>
            </div>
        <?php } ?>


        <?php $rodzajePaliwa = get_terms(['taxonomy' => 'rodzaj-paliwa', 'hide_empty' => false]); ?>
        <?php if (!empty($rodzajePaliwa)) { ?>
            <div class="selectFuels">
                <div class="checkboxBox">
                    <div class="checkboxButton checkboxButtonCenter displayFlex flexXcenter flexYcenter">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/fuel.svg" alt="fuel">
                        <?php echo __("Rodzaj paliwa", "wi"); ?>
                    </div>
                    <div class="checkboxList displayFlex flexWrap flexXstart flexYcenter">
                        <?php foreach ($rodzajePaliwa as $rodzajPaliwa) { ?>
                            <div class="checkboxListItem displayFlex flexXstart flexYcenter">
                                <input type="checkbox" id="fuels<?php echo $rodzajPaliwa->term_id; ?>" name="fuels" value="<?php echo $rodzajPaliwa->term_id; ?>" <?php if (in_array($rodzajPaliwa->term_id, explode(",", $_GET["fuels"]))) {
                                    echo ' checked';
                                } ?> />
                                <label for="fuels<?php echo $rodzajPaliwa->term_id; ?>"><span></span><?php echo $rodzajPaliwa->name; ?></label>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php $skrzynieBiegowDo = get_terms(['taxonomy' => 'skrzynia-biegow', 'hide_empty' => false]); ?>
        <?php if (!empty($skrzynieBiegowDo)) { ?>
            <div class="selectTransmission">
                <div class="checkboxBox">
                    <div class="checkboxButton checkboxButtonCenter displayFlex flexXcenter flexYcenter">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/gearbox.svg" alt="gearbox">
                        <?php echo __("Skrzynia biegów", "wi"); ?>
                    </div>
                    <div class="checkboxList displayFlex flexWrap flexXstart flexYcenter">
                        <?php foreach ($skrzynieBiegowDo as $skrzyniaBiegowDo) { ?>
                            <div class="checkboxListItem displayFlex flexXstart flexYcenter">
                                <input type="checkbox" id="transmission<?php echo $skrzyniaBiegowDo->term_id; ?>" name="transmission" value="<?php echo $skrzyniaBiegowDo->term_id; ?>" <?php if (in_array($skrzyniaBiegowDo->term_id, explode(",", $_GET["transmission"]))) {
                                    echo ' checked';
                                } ?> />
                                <label for="transmission<?php echo $skrzyniaBiegowDo->term_id; ?>"><span></span><?php echo $skrzyniaBiegowDo->name; ?></label>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php $segmenty = get_terms(['taxonomy' => 'segment', 'hide_empty' => false]); ?>
        <?php if (!empty($segmenty)) { ?>
            <div class="selectSegment">
                <div class="checkboxBox">
                    <div class="checkboxButton checkboxButtonCenter displayFlex flexXcenter flexYcenter">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/segment.svg" alt="segment">
                        <?php echo __("Segment", "wi"); ?>
                    </div>
                    <div class="checkboxList displayFlex flexWrap flexXstart flexYcenter">
                        <?php foreach ($segmenty as $segment) { ?>
                            <div class="checkboxListItem displayFlex flexXstart flexYcenter">
                                <input type="checkbox" id="segment<?php echo $segment->term_id; ?>" name="segment" value="<?php echo $segment->term_id; ?>" <?php if (in_array($segment->term_id, explode(",", $_GET["segment"]))) {
                                    echo ' checked';
                                } ?> />
                                <label for="segment<?php echo $segment->term_id; ?>"><span></span><?php echo $segment->name; ?></label>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>