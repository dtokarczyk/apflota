<?php

// Shortcode ///////////////////////////////////////////////////////////////////

function wi_spacesmall_shortcode($atts, $content = null) {
    $html = '<div class="spaceSmall"></div>';
    return $html;
}
add_shortcode( 'spaceSmall', 'wi_spacesmall_shortcode' );


function wi_space_shortcode($atts, $content = null) {
    $html = '<div class="spaceRegular"></div>';
    return $html;
}
add_shortcode( 'space', 'wi_space_shortcode' );

function wi_spacebig_shortcode($atts, $content = null) {
    $html = '<div class="spaceBig"></div>';
    return $html;
}
add_shortcode( 'spaceBig', 'wi_spacebig_shortcode' );

// Function ////////////////////////////////////////////////////////////////////

function containerStart($container) {
    if ($container == 0) {
        echo '<div class="containerEditor">';
    } elseif ($container == 1) {
        echo '<div class="container">';
    } elseif ($container == 2) {
        echo '<div class="containerBig">';
    } elseif ($container == 3) {
        echo '<div class="containerBig2">';
    }
}

function containerEnd($container) {
    if ($container < 4) {
        echo '</div>';
    }
}
?>