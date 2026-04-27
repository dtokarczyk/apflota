<?php

/**
 * Stacked title for long-term rental listing (base → +marka → +model).
 */

$wi_title_base = __('Wynajem długoterminowy', 'wi');
$wi_title_brand = null;
$wi_title_model = null;

if (function_exists('wi_offer_get_resolved_terms')) {
    $wi_ctx = wi_offer_get_resolved_terms();
    if ($wi_ctx['brand'] instanceof WP_Term) {
        $wi_title_brand = $wi_ctx['brand'];
    }
    if ($wi_ctx['model'] instanceof WP_Term) {
        $wi_title_model = $wi_ctx['model'];
    }
}

?>

<div id="topBanner" class="topBannerOffer">
    <div class="topBanner">
        <div class="containerBig">
            <div class="topBannerContainer displayFlex flexWrap flexXstart flexYstretch">
                <div class="topBannerTitle displayFlex flexXstart flexYcenter" style="width:100%;">
                    <?php
                    $wi_header_title = $wi_title_base;
if ($wi_title_brand instanceof WP_Term) {
    $wi_header_title .= ' ' . $wi_title_brand->name;
}
if ($wi_title_model instanceof WP_Term) {
    $wi_header_title .= ' ' . $wi_title_model->name;
}
?>
                    <h1 style="margin:0;"><?php echo esc_html($wi_header_title); ?></h1>
                </div>
            </div>
        </div>
    </div>
</div>
