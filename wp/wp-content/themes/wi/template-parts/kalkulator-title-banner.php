<?php

$calculator_heading = get_the_title();

if (! is_string($calculator_heading) || $calculator_heading === '') {
    $calculator_heading = __('Kalkulator', 'wi');
}
?>

<div id="topBanner" class="topBannerOffer">
    <div class="topBanner">
        <div class="containerBig">
            <div class="topBannerContainer displayFlex flexWrap flexXstart flexYstretch">
                <div class="topBannerTitle displayFlex flexXstart flexYcenter">
                    <h1><?php echo esc_html($calculator_heading); ?></h1>
                </div>
            </div>
        </div>
    </div>
</div>