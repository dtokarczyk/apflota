<?php
$wi_form_ctx = wi_offer_get_resolved_terms();
$wi_form_base = untrailingslashit(wi_offer_base_url());
?>
<style>
    @media (min-width: 1251px) {

        .sectionOfferSearch .sectionSearch .selectBodies,
        .sectionOfferSearch .sectionSearch .selectOfferBrand,
        .sectionOfferSearch .sectionSearch .selectOfferModel {
            width: calc(100% / 3) !important;
        }

        .sectionOfferSearch .sectionSearch .selectInstallment,
        .sectionOfferSearch .sectionSearch .selectFuels,
        .sectionOfferSearch .sectionSearch .selectTransmission,
        .sectionOfferSearch .sectionSearch .selectSegment {
            width: 25% !important;
        }

        .sectionOfferSearch .sectionSearch .selectBodies {
            order: 1 !important;
        }

        .sectionOfferSearch .sectionSearch .selectOfferBrand {
            order: 2 !important;
        }

        .sectionOfferSearch .sectionSearch .selectOfferModel {
            order: 3 !important;
        }

        .sectionOfferSearch .sectionSearch .selectInstallment {
            order: 4 !important;
        }

        .sectionOfferSearch .sectionSearch .selectFuels {
            order: 5 !important;
        }

        .sectionOfferSearch .sectionSearch .selectTransmission {
            order: 6 !important;
        }

        .sectionOfferSearch .sectionSearch .selectSegment {
            order: 7 !important;
        }
    }
</style>

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

        <?php
        $markiAuta = get_terms(['taxonomy' => 'marka-auta', 'hide_empty' => false]);

if (is_array($markiAuta) && ! is_wp_error($markiAuta) && $markiAuta !== []) {
    $wi_cur_brand_slug = $wi_form_ctx['brand'] instanceof WP_Term ? $wi_form_ctx['brand']->slug : '';
    $wi_brand_button_label = $wi_form_ctx['brand'] instanceof WP_Term ? $wi_form_ctx['brand']->name : __("Wszystkie marki", "wi");
    ?>
            <div class="selectOfferBrand">
                <div class="checkboxBox">
                    <div class="checkboxTitle"><?php echo esc_html(__("Marka", "wi")); ?></div>
                    <div class="checkboxButton checkboxButtonCenter displayFlex flexXcenter flexYcenter"><?php echo esc_html($wi_brand_button_label); ?></div>
                    <div class="checkboxList displayFlex flexWrap flexXstart flexYcenter">
                        <div class="checkboxListItem displayFlex flexXstart flexYcenter">
                            <input type="radio" id="wi-offer-brand-all" name="wi_offer_brand" <?php checked('', $wi_cur_brand_slug); ?>
                                onchange="if(this.checked){var b='<?php echo esc_url($wi_form_base); ?>',q=window.location.search||'';window.location.href=b+'/'+q;}">
                            <label for="wi-offer-brand-all"><span></span><?php echo esc_html(__("Wszystkie marki", "wi")); ?></label>
                        </div>
                        <?php foreach ($markiAuta as $markaAuta) { ?>
                            <div class="checkboxListItem displayFlex flexXstart flexYcenter">
                                <input type="radio"
                                    id="wi-offer-brand-<?php echo esc_attr($markaAuta->term_id); ?>"
                                    name="wi_offer_brand"
                                    <?php checked($wi_cur_brand_slug, $markaAuta->slug); ?>
                                    onchange="if(this.checked){var b='<?php echo esc_url($wi_form_base); ?>',q=window.location.search||'';window.location.href=b+'/'+encodeURIComponent('<?php echo esc_js($markaAuta->slug); ?>').replace(/%2F/g,'/')+'/'+q;}">
                                <label for="wi-offer-brand-<?php echo esc_attr($markaAuta->term_id); ?>"><span></span><?php echo esc_html($markaAuta->name); ?></label>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php
}
?>

        <?php
$wi_models_for_brand = $wi_form_ctx['brand'] instanceof WP_Term ? wi_offer_get_models_for_brand($wi_form_ctx['brand']) : [];
$wi_cur_model_slug = $wi_form_ctx['model'] instanceof WP_Term ? $wi_form_ctx['model']->slug : '';
$wi_model_button_label = $wi_form_ctx['model'] instanceof WP_Term ? $wi_form_ctx['model']->name : __("Wszystkie modele", "wi");
?>
        <div class="selectOfferModel">
            <div class="checkboxBox">
                <div class="checkboxTitle"><?php echo esc_html(__("Model", "wi")); ?></div>
                <div class="checkboxButton checkboxButtonCenter displayFlex flexXcenter flexYcenter"><?php echo esc_html($wi_model_button_label); ?></div>
                <div class="checkboxList displayFlex flexWrap flexXstart flexYcenter">
                    <?php if ($wi_form_ctx['brand'] instanceof WP_Term) { ?>
                        <div class="checkboxListItem displayFlex flexXstart flexYcenter">
                            <input type="radio" id="wi-offer-model-all" name="wi_offer_model" <?php checked('', $wi_cur_model_slug); ?>
                                onchange="if(this.checked){var b='<?php echo esc_url($wi_form_base); ?>',q=window.location.search||'';window.location.href=b+'/'+encodeURIComponent('<?php echo esc_js($wi_form_ctx['brand']->slug); ?>').replace(/%2F/g,'/')+'/'+q;}">
                            <label for="wi-offer-model-all"><span></span><?php echo esc_html(__("Wszystkie modele", "wi")); ?></label>
                        </div>
                        <?php if ($wi_models_for_brand !== []) { ?>
                            <?php foreach ($wi_models_for_brand as $model_term) { ?>
                                <div class="checkboxListItem displayFlex flexXstart flexYcenter">
                                    <input type="radio"
                                        id="wi-offer-model-<?php echo esc_attr($model_term->term_id); ?>"
                                        name="wi_offer_model"
                                        <?php checked($wi_cur_model_slug, $model_term->slug); ?>
                                        onchange="if(this.checked){var b='<?php echo esc_url($wi_form_base); ?>',q=window.location.search||'';window.location.href=b+'/'+encodeURIComponent('<?php echo esc_js($wi_form_ctx['brand']->slug); ?>').replace(/%2F/g,'/')+'/'+encodeURIComponent('<?php echo esc_js($model_term->slug); ?>').replace(/%2F/g,'/')+'/'+q;}">
                                    <label for="wi-offer-model-<?php echo esc_attr($model_term->term_id); ?>"><span></span><?php echo esc_html($model_term->name); ?></label>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="checkboxListItem displayFlex flexXstart flexYcenter">
                                <input type="radio" id="wi-offer-model-none" name="wi_offer_model_disabled" disabled>
                                <label for="wi-offer-model-none"><span></span><?php echo esc_html(__("Brak modeli dla wybranej marki", "wi")); ?></label>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="checkboxListItem displayFlex flexXstart flexYcenter">
                            <input type="radio" id="wi-offer-model-select-brand" name="wi_offer_model_disabled" disabled>
                            <label for="wi-offer-model-select-brand"><span></span><?php echo esc_html(__("Najpierw wybierz markę", "wi")); ?></label>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

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