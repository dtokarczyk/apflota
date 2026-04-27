<?php

/**
 * @param array<int|string, mixed>|\WP_Error|false $terms
 * @return list<\WP_Term>
 */
function wi_offer_normalize_terms($terms): array
{
    if (! is_array($terms)) {
        return [];
    }

    $out = [];
    foreach ($terms as $t) {
        if ($t instanceof WP_Term) {
            $out[] = $t;
        }
    }

    return $out;
}

$wi_offer_ctx = wi_offer_get_resolved_terms();
$wi_tax_parts = [];
if ($wi_offer_ctx['brand'] instanceof WP_Term) {
    $wi_tax_parts[] = [
        'taxonomy' => 'marka-auta',
        'field'    => 'term_id',
        'terms'    => (int) $wi_offer_ctx['brand']->term_id,
    ];
}
if ($wi_offer_ctx['model'] instanceof WP_Term) {
    $wi_tax_parts[] = [
        'taxonomy' => 'model',
        'field'    => 'term_id',
        'terms'    => (int) $wi_offer_ctx['model']->term_id,
    ];
}

?>
    <div class="sectionOfferOrder displayFlex flexXend flexYcenter">
        <div class="sectionOfferOrderTitle"><?php echo __("Sortuj według:", "wi"); ?></div>
        <div class="sectionOfferOrderBox">
            <div class="sectionOfferOrderButton"><?php echo __("Domyślnie", "wi"); ?></div>
            <div class="sectionOfferOrderList displayFlex flexWrap flexXstart flexYcenter">
                <div class="sectionOfferOrderListItem displayFlex flexXstart flexYcenter">
                    <input type="checkbox" id="order0" name="order" value="0"<?php if (!isset($_GET["order"]) || $_GET["order"] == "0") {
                        echo ' checked';
                    } ?>>
                    <label for="order0"><span></span><?php echo __("Domyślnie", "wi"); ?></label>
                </div>
                <div class="sectionOfferOrderListItem displayFlex flexXstart flexYcenter">
                    <input type="checkbox" id="orderpl" name="order" value="pl"<?php if ($_GET["order"] == "pl") {
                        echo ' checked';
                    } ?>>
                    <label for="orderpl"><span></span><?php echo __("Cena od najniższej", "wi"); ?></label>
                </div>
                <div class="sectionOfferOrderListItem displayFlex flexXstart flexYcenter">
                    <input type="checkbox" id="orderph" name="order" value="ph"<?php if ($_GET["order"] == "ph") {
                        echo ' checked';
                    } ?>>
                    <label for="orderph"><span></span><?php echo __("Cena od nawyższej", "wi"); ?></label>
                </div>
                <div class="sectionOfferOrderListItem displayFlex flexXstart flexYcenter">
                    <input type="checkbox" id="orderaz" name="order" value="az"<?php if ($_GET["order"] == "az") {
                        echo ' checked';
                    } ?>>
                    <label for="orderaz"><span></span><?php echo __("Alfabetycznie A-Z", "wi"); ?></label>
                </div>
                <div class="sectionOfferOrderListItem displayFlex flexXstart flexYcenter">
                    <input type="checkbox" id="orderza" name="order" value="za"<?php if ($_GET["order"] == "za") {
                        echo ' checked';
                    } ?>>
                    <label for="orderza"><span></span><?php echo __("Alfabetycznie Z-A", "wi"); ?></label>
                </div>
            </div>
        </div>
    </div>
    <div class="sectionOfferBox displayFlex flexWrap flexXstart flexYstretch">
    <?php if (!isset($_GET["order"]) || $_GET["order"] == "0") { ?>
        <?php $args = ['post_type' => 'post', 'posts_per_page' => '-1', 'orderby' => 'menu_order', 'order' => 'ASC']; ?>
    <?php } elseif ($_GET["order"] == "az") { ?>
        <?php $args = ['post_type' => 'post', 'posts_per_page' => '-1', 'orderby' => 'name', 'order' => 'ASC']; ?>
    <?php } elseif ($_GET["order"] == "za") { ?>
        <?php $args = ['post_type' => 'post', 'posts_per_page' => '-1', 'orderby' => 'name', 'order' => 'DESC']; ?>
    <?php } elseif ($_GET["order"] == "pl") { ?>
        <?php $args = ['post_type' => 'post', 'posts_per_page' => '-1', 'meta_key' => 'cena_od', 'orderby' => 'meta_value_num', 'order' => 'ASC']; ?>
    <?php } elseif ($_GET["order"] == "ph") { ?>
        <?php $args = ['post_type' => 'post', 'posts_per_page' => '-1', 'meta_key' => 'cena_od', 'orderby' => 'meta_value_num', 'order' => 'DESC']; ?>
    <?php } else { ?>
        <?php $args = ['post_type' => 'post', 'posts_per_page' => '-1', 'orderby' => 'menu_order', 'order' => 'ASC']; ?>
    <?php } ?>

    <?php
    if ($wi_tax_parts !== []) {
        $args['tax_query'] = array_merge(
            ['relation' => 'AND'],
            $wi_tax_parts
        );
    }
?>

    <?php $wp_query = new WP_Query($args); ?>
    <?php if (have_posts()) { ?>
        <?php while (have_posts()) {
            the_post(); ?>
            <?php // rodzaj nadwozia?>
            <?php $rodzajeNadwoziaClass = [];
            $rodzajeNadwozia = wi_offer_normalize_terms(get_the_terms(get_the_ID(), 'rodzaj-nadwozia')); ?>
            <?php foreach ($rodzajeNadwozia as $rodzajNadwozia) { ?>
                <?php $rodzajeNadwoziaClass[] = $rodzajNadwozia->term_id; ?>
            <?php } ?>
            <?php $rodzajeNadwoziaClass = implode(",", $rodzajeNadwoziaClass); ?>

            <?php // marka auta?>
            <?php $markiAutaClass = [];
            $markiAuta = wi_offer_normalize_terms(get_the_terms(get_the_ID(), 'marka-auta')); ?>
            <?php foreach ($markiAuta as $markaAuta) { ?>
                <?php $markiAutaClass[] = $markaAuta->term_id; ?>
            <?php } ?>
            <?php $markiAutaClass = implode(",", $markiAutaClass); ?>

            <?php // model?>
            <?php $modeleClass = [];
            $modele = wi_offer_normalize_terms(get_the_terms(get_the_ID(), 'model')); ?>
            <?php foreach ($modele as $modelTerm) { ?>
                <?php $modeleClass[] = $modelTerm->term_id; ?>
            <?php } ?>
            <?php $modeleClass = implode(",", $modeleClass); ?>

            <?php // rodzaj paliwa?>
            <?php $rodzajePaliwaClass = [];
            $rodzajePaliwa = wi_offer_normalize_terms(get_the_terms(get_the_ID(), 'rodzaj-paliwa')); ?>
            <?php foreach ($rodzajePaliwa as $rodzajPaliwa) { ?>
                <?php $rodzajePaliwaClass[] = $rodzajPaliwa->term_id; ?>
            <?php } ?>
            <?php $rodzajePaliwaClass = implode(",", $rodzajePaliwaClass); ?>

            <?php // skrzynia biegow?>
            <?php $skrzynieBiegowClass = [];
            $skrzynieBiegowDo = wi_offer_normalize_terms(get_the_terms(get_the_ID(), 'skrzynia-biegow')); ?>
            <?php foreach ($skrzynieBiegowDo as $skrzyniaBiegowDo) { ?>
                <?php $skrzynieBiegowClass[] = $skrzyniaBiegowDo->term_id; ?>
            <?php } ?>
            <?php $skrzynieBiegowClass = implode(",", $skrzynieBiegowClass); ?>

            <?php // segment?>
            <?php $segmentyClass = [];
            $segmenty = wi_offer_normalize_terms(get_the_terms(get_the_ID(), 'segment')); ?>
            <?php foreach ($segmenty as $segment) { ?>
                <?php $segmentyClass[] = $segment->term_id; ?>
            <?php } ?>
            <?php $segmentyClass = implode(",", $segmentyClass); ?>

            <?php
            $fuel_label = $rodzajePaliwa[0]->name ?? '';
            $gear_label = $skrzynieBiegowDo[0]->name ?? '';
            $segment_label = $segmenty[0]->name ?? '';
            ?>

            <a href="<?php echo esc_url(get_permalink()); ?>" class="sectionOfferItem"
               data-name="<?php echo esc_attr(get_the_title()); ?>"
               data-bodies="<?php echo esc_attr($rodzajeNadwoziaClass); ?>"
               data-mark="<?php echo esc_attr($markiAutaClass); ?>"
               data-model="<?php echo esc_attr($modeleClass); ?>"
               data-fuels="<?php echo esc_attr($rodzajePaliwaClass); ?>"
               data-installment="<?php echo esc_attr((string) get_field('cena_od')); ?>"
               data-transmission="<?php echo esc_attr($skrzynieBiegowClass); ?>"
               data-segment="<?php echo esc_attr($segmentyClass); ?>"
            >
                <span class="sectionOfferItemInside">
                    <span class="sectionOfferItemsImg">
                        <img class="img-full" alt="<?php echo esc_attr(get_the_title()); ?>" src="<?php $grafiki = get_field('zdjecie_glowne');
            echo isset($grafiki['sizes']['produkt-500x250']) ? esc_url($grafiki['sizes']['produkt-500x250']) : ''; ?>" />
                    </span>
                    <span class="sectionOfferItemDesc">
                        <span class="sectionOfferItemDesc1 displayFlex flexWrap flexXstart flexYcenter">
                            <span class="displayFlex flexXcenter flexYcenter">
                                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/fuel.svg" alt="fuel">
                                <?php echo esc_html($fuel_label); ?>
                            </span>
                            <span class="displayFlex flexXcenter flexYcenter">
                                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/gearbox.svg" alt="gearbox">
                                <?php echo esc_html($gear_label); ?>
                            </span>
                            <span class="displayFlex flexXcenter flexYcenter">
                                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/segment.svg" alt="segment">
                                <?php echo esc_html($segment_label); ?>
                            </span>
                        </span>
                        <span class="sectionOfferItemDesc2">
                            <span class="sectionOfferItemDescTitle"><?php echo esc_html(get_the_title()); ?></span>
                            <?php echo esc_html((string) get_field('silnik')); ?>
                            <span class="sectionOfferItemDescTitle"><?php echo __("od", "wi"); ?> <?php echo esc_html((string) get_field('cena_od')); ?> <?php echo __("zł", "wi"); ?></span>
                            <?php echo __("za miesiąc", "wi"); ?>
                        </span>
                    </span>
                </span>
            </a>
        <?php } ?>
    <?php } ?>
   <div class="sectionOfferItemNone"><?php echo __("Brak samochodów spełniających kryteria", "wi"); ?></div>
</div>
