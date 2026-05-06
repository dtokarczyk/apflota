<?php

/**
 * Title banner for the offer listing pages.
 * Uses the page title set in WordPress admin (editable per page, Yoast-friendly).
 */

?>

<div id="topBanner" class="topBannerOffer">
    <div class="topBanner">
        <div class="containerBig">
            <div class="topBannerContainer displayFlex flexWrap flexXstart flexYstretch">
                <div class="topBannerTitle displayFlex flexXstart flexYcenter" style="width:100%;">
                    <h1 style="margin:0;"><?php echo esc_html(get_the_title()); ?></h1>
                </div>
            </div>
        </div>
    </div>
</div>