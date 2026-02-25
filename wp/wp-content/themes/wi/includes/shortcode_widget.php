<?php

declare(strict_types=1);

// SHORTCODE -------------------------------------------------------------------

function wi_kotwica_shortcode($atts, $content = null)
{
    return '<div id="' . $content . '"></div>';
}
add_shortcode('kotwica', 'wi_kotwica_shortcode');

function wi_wrapper_shortcode($atts, $content = null)
{
    $html = '<div class="colWrapper displayFlex flexXbetween flexYstart">';
    $html .= do_shortcode($content);
    $html .= '</div>';
    return $html;
}
add_shortcode('wrapper', 'wi_wrapper_shortcode');

function wi_contact2col_shortcode($atts, $content = null)
{
    $html = '<div class="contact2col displayFlex flexWrap flexXstart flexYstart">';
    $html .= do_shortcode($content);
    $html .= '</div>';
    return $html;
}
add_shortcode('contact2col', 'wi_contact2col_shortcode');

function wi_icon_shortcode($atts, $content = null)
{
    $html = '<div class="iconText displayFlex flexXstart flexYcenter">';
    $html .= '<img src="' . $atts['img'] . '" />';
    $html .= '<h3>' . do_shortcode($content) . '</h3>';
    $html .= '</div>';
    return $html;
}
add_shortcode('icon', 'wi_icon_shortcode');


function wi_col_2_shortcode($atts, $content = null)
{
    $html = '<div class="colWrapper2">';
    $html .= do_shortcode($content);
    $html .= '</div>';
    return $html;
}
add_shortcode('col-2', 'wi_col_2_shortcode');

function wi_marginsmall_shortcode($atts, $content = null)
{
    $html = '<div class="spaceSmall"></div>';
    return $html;
}
add_shortcode('odstepMaly', 'wi_marginsmall_shortcode');
add_shortcode('spaceSmall', 'wi_marginsmall_shortcode');

function wi_margin_shortcode($atts, $content = null)
{
    $html = '<div class="spaceRegular"></div>';
    return $html;
}
add_shortcode('odstep', 'wi_margin_shortcode');
add_shortcode('space', 'wi_margin_shortcode');

function wi_marginbig_shortcode($atts, $content = null)
{
    $html = '<div class="spaceBig"></div>';
    return $html;
}
add_shortcode('odstepDuzy', 'wi_marginbig_shortcode');
add_shortcode('spaceBig', 'wi_marginbig_shortcode');

function wi_line_shortcode($atts, $content = null)
{
    $html = '<div class="hrLine"></div>';
    return $html;
}
add_shortcode('linia', 'wi_line_shortcode');

function wi_wiecej_shortcode($atts, $content = null)
{
    $atts = shortcode_atts(
        [
            'wiecej' => '',
            'mniej' => '',
        ],
        $atts,
        'wiecej',
    );
    $wiecej = $atts['wiecej'];
    $mniej = $atts['mniej'];
    if ($wiecej == "") {
        $wiecej = __("Zobacz więcej", "wi");
    }
    if ($mniej == "") {
        $mniej = __("Zobacz mniej", "wi");
    }

    $html = '<div class="contentMore" id="contentMore' . hash('crc32', $content) . '">' . do_shortcode($content) . '</div>';
    $html .= '<button class="button contentMoreButton" data-id="contentMore' . hash('crc32', $content) . '" data-open="' . $wiecej . '" data-close="' . $mniej . '">' . $wiecej . '</button>';
    return $html;
}
add_shortcode('wiecej', 'wi_wiecej_shortcode');


function wi_button_shortcode($atts, $content = null)
{
    $atts = shortcode_atts(
        [
            'link' => '',
            'nazwa' => '',
            'target' => '',
        ],
        $atts,
        'link',
    );
    $link = $atts['link'];
    $text = $atts['nazwa'];
    $target = $atts['target'];
    if ($target == "") {
        $target = "_self";
    }
    $html = '<a class="button displayInlineFlex flexXcenter flexYcenter" href="' . $link . '" target="' . $target . '"><span>';
    $html .= $text;
    $html .= '</span></a>';
    return $html;
}
add_shortcode('przycisk', 'wi_button_shortcode');


function wi_buttontransparent_shortcode($atts, $content = null)
{
    $atts = shortcode_atts(
        [
            'link' => '',
            'nazwa' => '',
            'target' => '',
        ],
        $atts,
        'link',
    );
    $link = $atts['link'];
    $text = $atts['nazwa'];
    $target = $atts['target'];
    if ($target == "") {
        $target = "_self";
    }
    $html = '<a class="button buttonTransparent displayInlineFlex flexXcenter flexYcenter" href="' . $link . '" target="' . $target . '"><span>';
    $html .= $text;
    $html .= '</span></a>';
    return $html;
}
add_shortcode('przyciskTransparenty', 'wi_buttontransparent_shortcode');


function wi_buttonline_shortcode($atts, $content = null)
{
    $atts = shortcode_atts(
        [
            'link' => '',
            'nazwa' => '',
            'target' => '',
        ],
        $atts,
        'link',
    );
    $link = $atts['link'];
    $text = $atts['nazwa'];
    $target = $atts['target'];
    if ($target == "") {
        $target = "_self";
    }
    $html = '<a class="button buttonSlim" href="' . $link . '" target="' . $target . '">';
    $html .= $text;
    $html .= '</a>';
    return $html;
}
add_shortcode('przyciskLinie', 'wi_buttonline_shortcode');


function wi_phone_shortcode($atts, $content = null)
{
    $html = '<a class="contactURL displayFlex flexXstart flexYcenter" href="tel:';
    $html .= str_replace("-", "", str_replace(" ", "", str_replace("-", "", str_replace("(", "", str_replace(")", "", $content)))));
    $html .= '">';
    $html .= $content;
    $html .= '</a>';
    return $html;
}
add_shortcode('telefon', 'wi_phone_shortcode');
add_shortcode('phone', 'wi_phone_shortcode');

function wi_email_shortcode($atts, $content = null)
{
    $html = '<a class="contactEmail displayFlex flexXstart flexYcenter" href="mailto:';
    $html .= $content;
    $html .= '">';
    $html .= $content;
    $html .= '</a>';
    return $html;
}
add_shortcode('email', 'wi_email_shortcode');

function wi_emailButton_shortcode($atts, $content = null)
{
    $html = '<a class="button buttonTransparent displayInlineFlex flexXcenter flexYcenter" href="mailto:';
    $html .= $content;
    $html .= '">';
    $html .= $content;
    $html .= '</a>';
    return $html;
}
add_shortcode('emailButton', 'wi_emailButton_shortcode');

function wi_yes_shortcode($atts, $content = null)
{
    return '<div class="tableIcon"><img src="' . get_template_directory_uri() . '/images/yes.svg" class="img-responsive" /></div>';
}
add_shortcode('yes', 'wi_yes_shortcode');

function wi_no_shortcode($atts, $content = null)
{
    return '<div class="tableIcon"><img src="' . get_template_directory_uri() . '/images/no.svg" class="img-responsive" /></div>';
}
add_shortcode('no', 'wi_no_shortcode');

function wi_ikona_telefon_shortcode($atts, $content = null)
{
    $html = '<a class="contactIcon displayFlex flexXstart flexYcenter" href="tel:' . str_replace("-", "", str_replace(" ", "", str_replace("-", "", str_replace("(", "", str_replace(")", "", $content))))) . '" target="_blank">';
    $html .= '<img src="' . get_template_directory_uri() . '/images/phoneIcon.svg" class="img-responsive svg"/>';
    $html .= $content;
    $html .= '</a>';
    return $html;
}
add_shortcode('phoneIcon', 'wi_ikona_telefon_shortcode');

function wi_ikona_telefon2_shortcode($atts, $content = null)
{
    $html = '<a class="contactIcon displayFlex flexXstart flexYcenter" href="tel:' . str_replace("-", "", str_replace(" ", "", str_replace("-", "", str_replace("(", "", str_replace(")", "", $content))))) . '" target="_blank">';
    $html .= '<img src="' . get_template_directory_uri() . '/images/phoneIcon2.svg" class="img-responsive svg"/>';
    $html .= $content;
    $html .= '</a>';
    return $html;
}
add_shortcode('phoneIcon2', 'wi_ikona_telefon2_shortcode');

function wi_ikona_email_shortcode($atts, $content = null)
{
    $html = '<a class="contactIcon contactIconEmail displayFlex flexXstart flexYcenter" href="mailto:' . $content . '" target="_blank">';
    $html .= '<img src="' . get_template_directory_uri() . '/images/emailIcon.svg" class="img-responsive svg"/>';
    $html .= $content;
    $html .= '</a>';
    return $html;
}
add_shortcode('emailIcon', 'wi_ikona_email_shortcode');


function wi_tableResponsive_shortcode($atts, $content = null)
{
    $html = '<div class="tableResponsive">';
    $html .= do_shortcode($content);
    $html .= '</div>';
    return $html;
}
add_shortcode('tableResponsive', 'wi_tableResponsive_shortcode');


function wi_powiekszony_shortcode($atts, $content = null)
{
    $html = '<span class="textBig">';
    $html .= do_shortcode($content);
    $html .= '</span>';
    return $html;
}
add_shortcode('textBig', 'wi_powiekszony_shortcode');


function wi_gray_shortcode($atts, $content = null)
{
    $html = '<span class="textGray">';
    $html .= do_shortcode($content);
    $html .= '</span>';
    return $html;
}
add_shortcode('gray', 'wi_gray_shortcode');


function wi_centerMobile_shortcode($atts, $content = null)
{
    $html = '<span class="centerMobile">';
    $html .= do_shortcode($content);
    $html .= '</span>';
    return $html;
}
add_shortcode('centerMobile', 'wi_centerMobile_shortcode');


function wi_nadtytulem_shortcode($atts, $content = null)
{
    $atts = shortcode_atts(
        [
            'color' => '',
        ],
        $atts,
        'nadTytulem',
    );

    if ($atts['color'] != "") {
        $color = ' style="color:' . $atts['color'] . '"';
    } else {
        $color = "";
    }

    $html = '<span class="overTitle"' . $color . '>';
    $html .= do_shortcode($content);
    $html .= '</span>';
    return $html;
}
add_shortcode('nadTytulem', 'wi_nadtytulem_shortcode');


// wi_lang_widget
function wi_lang_widget(): void
{
    register_sidebar([
        'name' => __('Language switcher', 'theme-slug'),
        'id' => 'widget_lang_switcher',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>',
    ]);
}
add_action('widgets_init', 'wi_lang_widget');
