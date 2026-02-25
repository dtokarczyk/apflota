<?php

function tox_popup_custom_frontand(): void
{
    if (get_field('popup_-_wlacz__wylacz', wpmlID(2)) != "" && get_field('popup_-_wlacz__wylacz', wpmlID(2)) == 1 && wpmlID(2) == get_the_ID()) {
        ?>
        <style>
            #wi-popup-2 {
                background-color: rgba(0,0,0,0.8);
                z-index: 100000000000000;
                position: fixed;
                display: none;
                justify-content: center;
                align-items: center;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
            }
            #wi-popup-2 img {
                max-height: 800px;
                width: 100%;
                margin: 0 auto;
                border-radius: 8px;
            }
            .wi-popup-2-wrap {
                display: block;
                padding: 0 20px;
                position: relative;
            }
            .wi-popup-2-close {
                position: absolute;
                color: white;
                font-size: 30px;
                top: -40px;
                right: -10px;
                cursor: pointer;
                transition-duration: 0.5s;
            }
            @media (max-width: 850px) {
                .wi-popup-2-close {
                    top: -45px;
                    right: 20px;
                }
            }
            #wi-popup-2 .wi-popup-2-wrap-content {
                display: block;
                max-height: 80vh;
                max-width: 730px;
                margin: 0 auto;
                background: white;
                padding: 50px;
                text-align: center;
                border-radius: 8px;
                color: black;
                overflow: auto;
            }
            #wi-popup-2 .wi-popup-2-wrap-content p {
                margin: 0;
            }
            @media (max-width: 1350px) {
                #wi-popup-2 .wi-popup-2-wrap-content {
                    max-height: calc(100vh - 60px);
                    padding: 30px;
                }
            }
            @media (max-width: 850px) {
                #wi-popup-2 .wi-popup-2-wrap-content {
                    max-width: 100%;
                }
            }
            @media (max-width: 450px) {
                #wi-popup-2 .wi-popup-2-wrap-content {
                    padding: 25px 20px;
                }
            }
            .wi-popup-2-close:hover {
                color: #777;
                transition-duration: 0.5s;
            }
        </style>

        <?php if (!isset($_COOKIE['apflotapopup'])) { ?>
            <span id="wi-popup-2" class="<?php echo $class_popup; ?>">
                <span class="wi-popup-2-wrap">
                    <?php if (get_field('popup_-_link', wpmlID(2)) != "") { ?><a href="<?php echo get_field('popup_-_link', wpmlID(2)); ?>" target="<?php echo get_field('popup_-_cel_odnosnik', wpmlID(2)); ?>"><?php } ?>
                        <?php if (get_field('popup_-_grafika', wpmlID(2)) != "") { ?>
                            <img src="<?php echo get_field('popup_-_grafika', wpmlID(2)); ?>" />
                        <?php } else { ?>
                            <span class="wi-popup-2-wrap-content"><?php echo get_field('pop-up_-_tresc', wpmlID(2)); ?></span>
                        <?php } ?>
                    <?php if (get_field('popup_-_link', wpmlID(2)) != "") { ?></a><?php } ?>
                    <span class="wi-popup-2-close">&#10006;</span>
                </span>
            </span>

            <script type='text/javascript'>

            !function(e){"function"==typeof define&&define.amd?define(["jquery"],e):"object"==typeof exports?module.exports=e(require("jquery")):e(jQuery)}(function(e){function n(e){return u.raw?e:encodeURIComponent(e)}function o(e){return u.raw?e:decodeURIComponent(e)}function i(e){return n(u.json?JSON.stringify(e):String(e))}function t(e){0===e.indexOf('"')&&(e=e.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\"));try{return e=decodeURIComponent(e.replace(c," ")),u.json?JSON.parse(e):e}catch(e){}}function r(n,o){var i=u.raw?n:t(n);return e.isFunction(o)?o(i):i}var c=/\+/g,u=e.cookie=function(t,c,s){if(arguments.length>1&&!e.isFunction(c)){if("number"==typeof(s=e.extend({},u.defaults,s)).expires){var d=s.expires,f=s.expires=new Date;f.setMilliseconds(f.getMilliseconds()+864e5*d)}return document.cookie=[n(t),"=",i(c),s.expires?"; expires="+s.expires.toUTCString():"",s.path?"; path="+s.path:"",s.domain?"; domain="+s.domain:"",s.secure?"; secure":""].join("")}for(var a=t?void 0:{},p=document.cookie?document.cookie.split("; "):[],l=0,m=p.length;l<m;l++){var x=p[l].split("="),g=o(x.shift()),j=x.join("=");if(t===g){a=r(j,c);break}t||void 0===(j=r(j))||(a[g]=j)}return a};u.defaults={},e.removeCookie=function(n,o){return e.cookie(n,"",e.extend({},o,{expires:-1})),!e.cookie(n)}});
            jQuery(document).ready(function ($) {
                // jQuery('body').css('overflow','hidden');
                jQuery('#wi-popup-2').css('display','flex');
                jQuery('#wi-popup-2').on('click', function () {
                    jQuery('#wi-popup-2').fadeTo( 1000 , 0, function() {
                        jQuery('#wi-popup-2').css('display','none');
                        // jQuery('body').css('overflow','auto');
                        jQuery.cookie("apflotapopup", '1', {path : '/'});
                    });
                });
                jQuery('#wi-popup-2 img').css('max-height', jQuery(window).height() - (jQuery(window).height() * 0.15));
                $( window ).resize(function() {
                    jQuery('#wi-popup-2 img').css('max-height', jQuery(window).height() - (jQuery(window).height() * 0.15));
                });
            });

            </script>
        <?php } ?>
    <?php
    }
}

add_action('wp_footer', 'tox_popup_custom_frontand');
