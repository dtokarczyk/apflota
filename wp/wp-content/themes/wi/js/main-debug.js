$(document).ready(function() {
    setTimeout(function(){ 
        $("#sectionLoader").animate({
            opacity: 0
        }, 200, function() {
            $('#sectionLoader').addClass('sectionLoaderHide');
        });
    }, 500);
});

$(window).on('load', function() { 
    $(function() {
        setTimeout(function() {
            $('.lazy').Lazy();
        }, 500);
    });
    
    if($(".sectionNewsBox > a").hasClass("sectionNewsItem")) { 
        setTimeout(function() {
            var temp_height = 0;
            $('.sectionNewsItemDesc').each( function() {
                if(temp_height < $(this).outerHeight()) {
                    temp_height = $(this).outerHeight();
                }
            });
            $('.sectionNewsItemDesc').css("min-height",temp_height);
            $(window).resize(function() {
                var temp_height = 0;
                $('.sectionNewsItemDesc').each( function() {
                    if(temp_height < $(this).outerHeight()) {
                        temp_height = $(this).outerHeight();
                    }
                });
                $('.sectionNewsItemDesc').css("min-height",temp_height);
            });
        }, 300);
    }
}); 

var browserWindow = null;
$(window).on('resize', function(e) {
    if(browserWindow > 1350){
        var tmp1 = browserWindow - 100;
        var tmp2 = browserWindow + 100;
        var current = $(window).width();
        var lock = 0;
        if(current > tmp2 || current < tmp1){
            if(lock == 1){
                return;
            }
            if(browserWindow > 960 && current > 960){
                return;
            }
            lock = 1;
            $('html').animate({
                opacity: 0,
            }, 100, function() {
                window.location.href = window.location.href;
            });
        }
    }
});

$(window).load(function () {
    browserWindow = $( window ).width();
});

$(document).ready(function() {

    // item_height_full
    function item_height_full(item_class, item_height = 0) {
        $(item_class).each(function(){
            if(item_height < $(this).height()) {
                item_height = $(this).height();
            }
        });
        $(item_class).height(item_height);
    }
    
    // item_height
    function item_height(item_class, item_height = 0) {
        $(item_class).each(function(){
            if(item_height < $(this).outerHeight()) {
                item_height = $(this).outerHeight();
            }
        });
        $(item_class).height(item_height);
    }
    
    // item_width
    function item_width(item_class, item_width = 0) {
        if($(window).width() > 768) {
            $(item_class).each(function(){
                if(item_width < $(this).outerWidth()) {
                    item_width = $(this).outerWidth();
                }
            });
            $(item_class).width(item_width);
        }
    }
    
        
    // products desc height
    function menuCenter() {
        if($(window).width() > 1100) {
            $('.sectionHeaderLogo').css('min-width','0px');
            $('.sectionHeaderMenuBoxCenter').css("width","auto");
            $('.sectionHeaderMenuUl1').css("width","auto");
            if($('.sectionHeaderBox').outerWidth() - $('.sectionHeaderMenuUl2').outerWidth() * 2 - 50 > $('.sectionHeaderMenuUl1').outerWidth()) {
                $('.sectionHeaderLogo').css('min-width',$('.sectionHeaderMenuUl2').outerWidth());
            } else {
                $('.sectionHeaderLogo').css('min-width',$('.sectionHeaderLogo > a').outerWidth());
            }
            $('.sectionHeaderMenuBoxCenter').css("width",$('.sectionHeaderBox').width() - $('.sectionHeaderLogo').outerWidth());
            $('.sectionHeaderMenuUl1').css("width","100%");
        }
    }
    setTimeout(function() {
        menuCenter();
    }, 200);
    $(window).resize(function() {
        menuCenter();
    });

    // Admin Bar
    $('#wpadminbar').prepend( "<div class='switch_admin_bar'></div>");
    $('#wpadminbar').addClass('hide2');
    $('.switch_admin_bar').click(function(){
        $(this).toggleClass('hide2');
        show_admin();
    });
    function show_admin(){
        $('#wpadminbar').toggleClass('hide2');
        setTimeout(function(){
            $('#wpadminbar').addClass('hide2');
        }, 30000);
    }
    
    
    // recpatcha 
    if($("form").hasClass("wpcf7-form")) {
        $("body").append("<style>.grecaptcha-badge { z-index: 999998; } </style>");
    } else {
        $("body").append("<style>.grecaptcha-badge { display: none; } </style>");
    }
    

    // link # disable action
    $("a").on("click", function (e) {
        if($(this).attr('href') == "#") {
            e.preventDefault();
        }
    });
    
    // img SVG convert to SVG code if class .svg
    $('img.svg').each(function(){
        var $img = jQuery(this);
        var imgID = $img.attr('id');
        var imgClass = $img.attr('class');
        var imgURL = $img.attr('src');

        $.get(imgURL, function(data) {
            var $svg = $(data).find('svg');
            if(typeof imgID !== 'undefined') {
                $svg = $svg.attr('id', imgID);
            }
            if(typeof imgClass !== 'undefined') {
                $svg = $svg.attr('class', imgClass+' replaced-svg');
            }
            $svg = $svg.removeAttr('xmlns:a');
            if(!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
                $svg.attr('viewBox', '0 0 ' + $svg.attr('height') + ' ' + $svg.attr('width'))
            }
            $img.replaceWith($svg);
        }, 'xml');
    });
    
    $('.checkbox-wi .more-wi-button').click(function(e){
        e.preventDefault();
        $("."+$(this).data('more')).toggleClass('more-wi-open');
        $($(this)).toggleClass('more-span-wi-open')
    });

    $('.wpcf7-checkbox .wpcf7-list-item').each(function(i){
        $('label input',this).prependTo(this);
        $('.more-wi-button',$(this).parent().parent().parent()).appendTo($(this).parent().parent());
    });
    
    $('.checkbox-wi').each(function() {
        $(".checkbox-wi-button",$(this)).appendTo('.checkbox-wi .wpcf7-list-item-label');
    });

    $('.wpcf7-checkbox .wpcf7-list-item').click(function(e) {
        var checkBoxes = $(this).find("input").prop("checked");
        if(!checkBoxes) {
            $(this).find("input").prop("checked",true);
        } else {
            $(this).find("input").prop("checked",false);
        }
    });
    
    $('.checkbox-wi').click(function(e) {
        var target = $(e.target);
        if(!target.is("a")) { } else {
            var checkBoxes = $(this).find("input").prop("checked");
            if(!checkBoxes) {
                $(this).find("input").prop("checked",true);
            } else {
                $(this).find("input").prop("checked",false);
            }
            window.open(target.attr("href"), '_blank');
        }
    });
    
    $('.checkbox-wi').each(function(i){
        $(this).find('.checkbox-wi-url').appendTo($(this).find('label'));
    });
    
    if($(".your-message > textarea").hasClass("wpcf7-textarea")) {
        setTimeout(function(){
            var paddingTextarea = parseInt($('.your-message .wpcf7-textarea').css('padding-top')) * 2 + 4;
            $('.your-message .wpcf7-textarea').height($('.formItemHeight').height() - paddingTextarea);
        }, 500);
    }
    
    // ajax-loader move
    $(".wpcf7").append($(".ajax-loader"));

    // FAQ
    $('.ceFaqAnswer').css('max-height','');
    $('.ceFaqBox.ceFaqOpen .ceFaqAnswer').css('max-height',$('.ceFaqBox.ceFaqOpen .ceFaqAnswer').prop("scrollHeight"));
    $('.ceFaqQuestionButton,.ceFaqQuestionTitle').click(function() {
        var panel = $('.ceFaqAnswer',$(this).parent().parent());
        if($(this).parent().parent().hasClass('ceFaqOpen')) {
            $(this).parent().parent().removeClass('ceFaqOpen');
            panel.css('max-height','');
        } else {
            $('.ceFaqBox').removeClass('ceFaqOpen');
            $('.ceFaqBox .ceFaqAnswer').css('max-height','');
            $(this).parent().parent().addClass('ceFaqOpen');
            panel.css('max-height',panel.prop("scrollHeight"));
        }
    });
    
    
    if($("#sectionLogotype > div >").hasClass("sectionLogotypeBox")) {
        $(".sectionLogotypeBox").slick({
            dots: false,
            arrows: false,
            infinite: true,
            autoplay: true,
            autoplaySpeed: 7000,
            cssEase: 'linear',
            slidesToShow: 4,
            slidesToScroll: 1,
            variableWidth: true,
            pauseOnHover: false,
            lazyLoad: 'ondemand',
            responsive: [
            {
                breakpoint: 1400,
                settings: {
                    slidesToShow: 3,
                }
            },
            {
                breakpoint: 950,
                settings: {
                    slidesToShow: 2,
                }
            },
            {
                breakpoint: 500,
                settings: {
                    slidesToShow: 1,
                }
            }
            ]
        });
        if($(window).width() > 1400) {
            $(".sectionLogotypeBox .slick-slide").css("width",$("#sectionLogotype .slick-list").width() / 4);
        } else if($(window).width() > 950) {
            $(".sectionLogotypeBox .slick-slide").css("width",$("#sectionLogotype .slick-list").width() / 3);
        } else if($(window).width() > 500) {
            $(".sectionLogotypeBox .slick-slide").css("width",$("#sectionLogotype .slick-list").width() / 2);
        } else {
            $(".sectionLogotypeBox .slick-slide").css("width",$("#sectionLogotype .slick-list").width());
        }
        $(window).resize(function() {
            if($(window).width() > 1400) {
                $(".sectionLogotypeBox .slick-slide").css("width",$("#sectionLogotype .slick-list").width() / 4);
            } else if($(window).width() > 950) {
                $(".sectionLogotypeBox .slick-slide").css("width",$("#sectionLogotype .slick-list").width() / 3);
            } else if($(window).width() > 500) {
                $(".sectionLogotypeBox .slick-slide").css("width",$("#sectionLogotype .slick-list").width() / 2);
            } else {
                $(".sectionLogotypeBox .slick-slide").css("width",$("#sectionLogotype .slick-list").width());
            }
        });
    }
    
    
    if($(".single .sectionOfferBox > a").hasClass("sectionOfferItem")) {
        $(".single .sectionOfferBox").slick({
            dots: true,
            arrows: true,
            infinite: true,
            autoplay: true,
            autoplaySpeed: 7000,
            cssEase: 'linear',
            slidesToShow: 3,
            slidesToScroll: 1,
            variableWidth: true,
            pauseOnHover: false,
            lazyLoad: 'ondemand',
            prevArrow: '<div class="a-left control-c prev slick-prev"><svg width="46" height="51" viewBox="0 0 46 51" fill="none" xmlns="http://www.w3.org/2000/svg"><g filter="url(#filter0_dddd_10027_8324)"><path d="M3 20C3 8.95431 11.9543 0 23 0C34.0457 0 43 8.95431 43 20C43 31.0457 34.0457 40 23 40C11.9543 40 3 31.0457 3 20Z" fill="white"/><path d="M3.5 20C3.5 9.23045 12.2304 0.5 23 0.5C33.7696 0.5 42.5 9.23045 42.5 20C42.5 30.7696 33.7696 39.5 23 39.5C12.2304 39.5 3.5 30.7696 3.5 20Z" stroke="#D9D9D9"/><mask id="mask0_10027_8324" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="13" y="10" width="20" height="20"><rect x="13" y="10" width="20" height="20" fill="#D9D9D9"/></mask><g mask="url(#mask0_10027_8324)"><path d="M21.3335 25L16.3335 20L21.3335 15L22.5002 16.2083L19.5418 19.1667H29.6668V20.8333H19.5418L22.5002 23.7917L21.3335 25Z" fill="#BB2608"/></g></g><defs><filter id="filter0_dddd_10027_8324" x="0" y="0" width="46" height="51" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="1"/><feGaussianBlur stdDeviation="0.5"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_10027_8324"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="2"/><feGaussianBlur stdDeviation="1"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.09 0"/><feBlend mode="normal" in2="effect1_dropShadow_10027_8324" result="effect2_dropShadow_10027_8324"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="5"/><feGaussianBlur stdDeviation="1.5"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0"/><feBlend mode="normal" in2="effect2_dropShadow_10027_8324" result="effect3_dropShadow_10027_8324"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="8"/><feGaussianBlur stdDeviation="1.5"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.01 0"/><feBlend mode="normal" in2="effect3_dropShadow_10027_8324" result="effect4_dropShadow_10027_8324"/><feBlend mode="normal" in="SourceGraphic" in2="effect4_dropShadow_10027_8324" result="shape"/></filter></defs></svg></div>',
            nextArrow: '<div class="a-right control-c next slick-next"><svg width="46" height="51" viewBox="0 0 46 51" fill="none" xmlns="http://www.w3.org/2000/svg"><g filter="url(#filter0_dddd_10027_8325)"><path d="M3 20C3 8.95431 11.9543 0 23 0C34.0457 0 43 8.95431 43 20C43 31.0457 34.0457 40 23 40C11.9543 40 3 31.0457 3 20Z" fill="white"/><path d="M3.5 20C3.5 9.23045 12.2304 0.5 23 0.5C33.7696 0.5 42.5 9.23045 42.5 20C42.5 30.7696 33.7696 39.5 23 39.5C12.2304 39.5 3.5 30.7696 3.5 20Z" stroke="#D9D9D9"/><mask id="mask0_10027_8325" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="13" y="10" width="20" height="20"><rect x="13" y="10" width="20" height="20" fill="#D9D9D9"/></mask><g mask="url(#mask0_10027_8325)"><path d="M24.6666 25L23.4999 23.7917L26.4583 20.8333H16.3333V19.1667H26.4583L23.4999 16.2083L24.6666 15L29.6666 20L24.6666 25Z" fill="#BB2608"/></g></g><defs><filter id="filter0_dddd_10027_8325" x="0" y="0" width="46" height="51" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="1"/><feGaussianBlur stdDeviation="0.5"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_10027_8325"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="2"/><feGaussianBlur stdDeviation="1"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.09 0"/><feBlend mode="normal" in2="effect1_dropShadow_10027_8325" result="effect2_dropShadow_10027_8325"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="5"/><feGaussianBlur stdDeviation="1.5"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0"/><feBlend mode="normal" in2="effect2_dropShadow_10027_8325" result="effect3_dropShadow_10027_8325"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="8"/><feGaussianBlur stdDeviation="1.5"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.01 0"/><feBlend mode="normal" in2="effect3_dropShadow_10027_8325" result="effect4_dropShadow_10027_8325"/><feBlend mode="normal" in="SourceGraphic" in2="effect4_dropShadow_10027_8325" result="shape"/></filter></defs></svg></div>',
            responsive: [
                {
                    breakpoint: 1350,
                    settings: {
                        slidesToShow: 2,
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 1,
                    }
                }
            ]
        });
        if($(window).width() > 1350) {
            $(".single .sectionOfferBox .slick-slide").css("width",$(".single .sectionOfferBox .slick-list").width() / 3);
        } else if($(window).width() > 768) {
            $(".single .sectionOfferBox .slick-slide").css("width",$(".single .sectionOfferBox .slick-list").width() / 2);
        } else {
            $(".single .sectionOfferBox .slick-slide").css("width",$(".single .sectionOfferBox .slick-list").width());
        }
        $(window).resize(function() {
            if($(window).width() > 1350) {
                $(".single .sectionOfferBox .slick-slide").css("width",$(".single .sectionOfferBox .slick-list").width() / 3);
            } else if($(window).width() > 768) {
                $(".single .sectionOfferBox .slick-slide").css("width",$(".single .sectionOfferBox .slick-list").width() / 2);
            } else {
                $(".single .sectionOfferBox .slick-slide").css("width",$(".single .sectionOfferBox .slick-list").width());
            }
        });
    }
    setTimeout(function(){
        item_height_full('.sectionOfferItemDesc');
    }, 500);
    
    
    if($(".single .sectioOfferGalleryBox > div").hasClass("sectioOfferGallerySlider")) {
        $('.single .sectioOfferGallerySliderSingle').slick({
            dots: false,
            arrows: false,
            speed: 500,
            autoplay: true,
            autoplaySpeed: 7000,
            pauseOnHover: false,
            swipe: true,
            lazyLoad: 'ondemand',
            slidesToShow: 1,
            slidesToScroll: 1,
            fade: true,
            asNavFor: '.sectioOfferGallerySliderNavSingle',
            prevArrow: '<div class="a-left control-c prev slick-prev displayFlex flexXcenter flexYcenter"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13 4L7 10L13 16" stroke="white" stroke-width="1.5"/></svg></div>',
            nextArrow: '<div class="a-right control-c next slick-next displayFlex flexXcenter flexYcenter"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 16L13 10L7 4" stroke="white" stroke-width="1.5"/></svg></div>',
        });
    }
    if($(".single .sectioOfferGalleryBox > div").hasClass("sectioOfferGallerySliderNavSingle")) {
        $('.single .sectioOfferGallerySliderNavSingle').slick({
            dots: false,
            arrows: false,
            speed: 500,
            autoplay: true,
            autoplaySpeed: 7000,
            pauseOnHover: false,
            swipe: true,
            lazyLoad: 'ondemand',
            slidesToShow: 4,
            slidesToScroll: 1,
            asNavFor: '.sectioOfferGallerySliderSingle',
            focusOnSelect: true,
            prevArrow: '<div class="a-left control-c prev slick-prev displayFlex flexXcenter flexYcenter"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13 4L7 10L13 16" stroke="white" stroke-width="1.5"/></svg></div>',
            nextArrow: '<div class="a-right control-c next slick-next displayFlex flexXcenter flexYcenter"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 16L13 10L7 4" stroke="white" stroke-width="1.5"/></svg></div>',
            responsive: [
                {
                    breakpoint: 1250,
                    settings: {
                        slidesToShow: 3,
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2,
                    }
                }
            ]
        });
    }
    
    
    if($(window).width() > 1100) {
        if($(window).scrollTop() > 10) {
            $("header").addClass("headerScroll0");
        } else {
            $("header").removeClass("headerScroll0");
        }
    }
    if($("body").hasClass("home")) {
        var topHeight = $("#sectionSlider").outerHeight();
    } else {
        var topHeight = $("#topBanner").outerHeight();
    }
    if($(window).scrollTop() > topHeight - $("header").outerHeight()) {
        $("header").addClass("headerScroll");
    } else {
        $("header").removeClass("headerScroll");
    }
    $(window).scroll(function() {
        if($(window).width() > 1100) {
            if($(window).scrollTop() > 10) {
                $("header").addClass("headerScroll0");
            } else {
                $("header").removeClass("headerScroll0");
            }
        }
        if($("body").hasClass("home")) {
            var topHeight = $("#sectionSlider").height();
        } else {
            var topHeight = $("#topBanner").height();
        }
        if($(window).scrollTop() > topHeight - $("header").outerHeight()) {
            $("header").addClass("headerScroll");
        } else {
            $("header").removeClass("headerScroll");
        }
    });
 
    
    // Header Menu -  mobile
    $(".sectionHeaderMenuHamburger").on("click", function (e) {
        if($(".menu-item-has-children").hasClass("sectionHeaderSubMenuActive")) {
            $(".menu-item-has-children").removeClass('sectionHeaderSubMenuActive');
            if(!$(".sectionHeaderMenu").hasClass('sectionHeaderMenuOpen')) {
                $(".sectionHeaderMenuButton").removeClass('sectionHeaderMenuButtonOpen');
            }
        } else {
            if($(this).parent().hasClass('sectionHeaderMenuButtonOpen')) {
                $(".sectionHeaderMenuButton").removeClass('sectionHeaderMenuButtonOpen');
                $(".sectionHeaderMenu").removeClass('sectionHeaderMenuOpen');
                $("header").removeClass('headerMenuOpen');
            } else {
                $(".sectionHeaderMenuButton").addClass('sectionHeaderMenuButtonOpen');
                $(".sectionHeaderMenu").addClass('sectionHeaderMenuOpen');
                $("header").addClass('headerMenuOpen');
            }
        }
    });
    if($(window).width() > 1100) {
        $(".sectionHeaderSubMenu > a").on("click", function (e) {
            //e.preventDefault();
        });
        $('.sectionHeaderSubMenu').hover(function() {
            $(this).addClass('open');
        }, function() {
            $(this).removeClass('open');
        });
        
        $(".menu-item-has-children > a").on("click", function (e) {
            //e.preventDefault();
        });
        $('.menu-item-has-children').hover(function() {
            $(this).addClass('open');
        }, function() {
            $(this).removeClass('open');
        });
    } else {
        $(".sectionHeaderSubMenu > a").on("click", function (e) {
            e.preventDefault();
            if($(this).parent().hasClass('open')) {
                $(this).parent().removeClass('open');
            } else {
                $(".sectionHeaderMenuButton").addClass('sectionHeaderMenuButtonOpen');
                $(".menu-item-has-children, .sectionHeaderSubMenu").removeClass('open');
                $(this).parent().addClass('open');
            }
        });
        
        $(".menu-item-has-children > a").on("click", function (e) {
            if($(this).parent().hasClass('menuOffer')) { } else {
                e.preventDefault();
                if($(this).parent().hasClass('open')) {
                    $(this).parent().removeClass('open');
                } else {
                    $(".sectionHeaderMenuButton").addClass('sectionHeaderMenuButtonOpen');
                    $(".menu-item-has-children, .sectionHeaderSubMenu").removeClass('open');
                    $(this).parent().addClass('open');
                }
            }
        });
    }


    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    // sectionProductGalleryBig
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

    if($(".sectionProductItem > div").hasClass("sectionProductGalleryBig")) {
        $(".sectionProductGalleryBig").slick({
            dots: false,
            arrows: true,
            infinite: true,
            autoplay: true,
            fade: true,
            autoplaySpeed: 10000,
            slidesToShow: 1,
            slidesToScroll: 1,
            pauseOnHover: false,
            swipe: true,
            prevArrow: '<div class="a-left control-c prev slick-prev"><svg xmlns="http://www.w3.org/2000/svg" width="11.106" height="12" viewBox="0 0 11.106 12"><path id="Polygon_2" data-name="Polygon 2" d="M6,0l6,11.106H0Z" transform="translate(0 12) rotate(-90)" fill="#fff"/></svg></div>',
            nextArrow: '<div class="a-right control-c next slick-next"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="11.105px" height="12px" viewBox="0 0 11.105 12" enable-background="new 0 0 11.105 12" xml:space="preserve"><g id="Group_266" transform="translate(-241.5 -5579.5)"><path id="Polygon_2" fill="#FFFFFF" d="M252.606,5585.5l-11.105,6v-12L252.606,5585.5z"/></g></svg></div>',
        });
    }

    // sectionWhyWorthBox
    if($(".sectionWhyWorthBox > div").hasClass("sectionWhyWorthItem")) {
        $(".sectionWhyWorthBox").slick({
            dots: true,
            arrows: false,
            infinite: true,
            autoplay: true,
            fade: true,
            autoplaySpeed: 10000,
            slidesToShow: 1,
            slidesToScroll: 1,
            pauseOnHover: false,
            swipe: true,
        });
    }
    setTimeout(function(){
        item_height_full('.sectionWhyWorthItemDesc');
    }, 500);
    
    
    // gallerySlider
    $('.ceGalleryBox_gallery_type1').each(function(index){
        $(this).slick({
            dots: false,
            arrows: true,
            speed: 500,
            autoplay: true,
            cssEase: 'linear',
            slidesToShow: 4,
            slidesToScroll: 1,
            autoplaySpeed: 5000,
            pauseOnHover: false,
            swipe: true,
            lazyLoad: 'ondemand',
            prevArrow: '<div class="a-left control-c prev slick-prev"><svg xmlns="http://www.w3.org/2000/svg" width="11.106" height="12" viewBox="0 0 11.106 12"><path id="Polygon_2" data-name="Polygon 2" d="M6,0l6,11.106H0Z" transform="translate(0 12) rotate(-90)" fill="#fff"/></svg></div>',
            nextArrow: '<div class="a-right control-c next slick-next"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="11.105px" height="12px" viewBox="0 0 11.105 12" enable-background="new 0 0 11.105 12" xml:space="preserve"><g id="Group_266" transform="translate(-241.5 -5579.5)"><path id="Polygon_2" fill="#FFFFFF" d="M252.606,5585.5l-11.105,6v-12L252.606,5585.5z"/></g></svg></div>',
            responsive: [
            {
                breakpoint: 1250,
                settings: {
                    slidesToShow: 3,
                }
            },
            {
                breakpoint: 800,
                settings: {
                    slidesToShow: 2,
                }
            },
            {
                breakpoint: 450,
                settings: {
                    slidesToShow: 1,
                }
            }
            ]
        });
        $('.ceGalleryBox_gallery_type1').each(function(i) {
            $(".slick-cloned .ceGalleryItem",$(this)).attr("data-gallery","#galleryFoto" + i);
        });
    }); 
    
    if($(".sectionTopbannerSubMenu > div > div").hasClass("sectionTopbannerSubMenuBox")) {
        $(".topBannerNone").css("height",$(".topBannerNone").outerHeight() + $(".sectionTopbannerSubMenu").outerHeight());
    }
    $(".sectionTopbannerSubMenu a").click(function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: $($(this).attr("href")).offset().top - $("header").outerHeight() - $(".sectionTopbannerSubMenu").outerHeight() + 2}, 'slow');
    }); 

    // headerTopScroll
    if($("body").hasClass("home")) {
        $(".headerTopScroll").click(function() {
            $(".sectionHeaderMenuButtonOpen .sectionHeaderMenuHamburger").click();
            if($(window).width() > 600) {
                $('html, body').animate({scrollTop: $("#offer_search").offset().top - $("header").outerHeight() + 1}, 'slow');
            } else {
                $('html, body').animate({scrollTop: $("#offer_search").offset().top - $("header").outerHeight() + 1}, 'slow');
            }
        }); 
    }
    
    // topbannerScroll
    $(".topbannerScroll").click(function() {
        if($(window).width() > 600) {
            $('html, body').animate({scrollTop: $("#offer_search").offset().top - $("header").outerHeight() + 1}, 'slow');
        } else {
            $('html, body').animate({scrollTop: $("#offer_search").offset().top - $("header").outerHeight() + 1}, 'slow');
        }
    }); 
    
    
    // headerTopScroll
    if($("body").hasClass("home")) {
        $(".headerSliderScroll").click(function() {
            $(".sectionHeaderMenuButtonOpen .sectionHeaderMenuHamburger").click();
            if($(window).width() > 600) {
                $('html, body').animate({scrollTop: $("#sliderScroll").offset().top - $("header").outerHeight() + 1}, 'slow');
            } else {
                $('html, body').animate({scrollTop: $("#sliderScroll").offset().top - $("header").outerHeight() + 1}, 'slow');
            }
        }); 
    }
    
    
    
    // sectionTableItem
    setTimeout(function(){
        $('.sectionTableItem').each(function() {
            var temp_this = $(this);
            var temp_height = 0;
            $(".sectionTableItemCol > span",temp_this).each(function() {
                if(temp_height < $(this).outerHeight()) {
                    temp_height = $(this).outerHeight();
                }
            });
            $(".sectionTableItemCol > span",temp_this).css("min-height",temp_height);
        });
    }, 500);
});

$(window).on('load', function() { 
    
    if($(".sectionOfferSearch > div").hasClass("sectionSearch")) {
        // URL update data
        function dataUrlUpdate($checboxArray,$getName) {
            var $url = new URL(window.location);
            if($checboxArray.length === 0) {
                $url.searchParams.delete($getName);
            } else {
                $url.searchParams.set($getName, $checboxArray);
            }
            history.pushState({}, "", $url);
        }

        // Refresh list item
        function refreshListItem($getNameArray) {
            $(".sectionOfferBox").addClass("hide");
            setTimeout(function(){
                var $url = new URL(window.location);
                var $selectArray = [];
                $.each($getNameArray, function( index, value ) {
                    var $get = $url.searchParams.get(value);
                    if($get !== null) {
                        $selectArray[value] = [];
                        $get = $get.split(',');
                        $.each($get, function( index2, value2 ) {
                            $selectArray[value][index2] = value2;
                        });
                    }
                });
                $(".sectionOfferItem").each(function() {
                    var $this = $(this);
                    $this.removeClass("show");
                    var $ite = [];
                    $.each($getNameArray, function( index, value ) {
                        var $get = $url.searchParams.get(value);
                        if($get !== null) {
                            $ite[value] = 0;
                            if(value == "installment") {
                                var $itemArray = $this.attr('data-' + value);
                                $.each($selectArray[value], function(index2, value2) {
                                    if($itemArray <= value2) {
                                        $ite[value]++;
                                    }
                                });
                            } else {
                                var $itemArray = $this.attr('data-' + value).split(',');
                                $.each($selectArray[value], function(index2, value2) {
                                    if($.inArray(value2, $itemArray) !== -1) {
                                        $ite[value]++;
                                    }
                                });
                            }
                        }
                    });
                    var $iteShow = 0;
                    var $iteCount = 0;
                    $.each($getNameArray, function( index, value ) {
                        if(typeof $ite[value] !== "undefined") {
                            if($ite[value] > 0) {
                                $iteShow++;
                            }
                            $iteCount++;
                        }
                    });
                    if($iteShow == $iteCount) {
                        $this.addClass("show");
                    }
                });
                searchRefresh();
            }, 100);
            setTimeout(function(){
                $(".sectionOfferBox").removeClass("hide");
            }, 300);
        }
        setTimeout(function(){
            refreshListItem(["bodies","mark","fuels","installment","transmission","segment"]);
        }, 400);


        // Refresh list item
        function refreshDataComposition() {
            // hide bodies
            $(".selectBodies .checkboxListItem").removeClass("hide");
            // hide mark
            $(".selectMark .checkboxListItem").removeClass("hide");
            // hide fuels
            $(".selectFuels .checkboxListItem").removeClass("hide");
            // hide installment
            $(".selectInstallment .checkboxListItem").removeClass("hide");
            // hide Transmission
            $(".selectTransmission .checkboxListItem").removeClass("hide");
            // hide segment
            $(".selectSegment .checkboxListItem").removeClass("hide");
        }
        setTimeout(function(){
            refreshDataComposition();
        }, 500);


        // reset value
        function resetValue($getNameArray) {
            $.each($getNameArray, function( index, value ) {
                $(value + " input:checkbox:checked").each(function(){
                    $(this).prop("checked",false);
                });
            });
            $(".installmentPriceOutput").val($(".selectInstallment input").attr("max"));
            $(".selectInstallment input").val($(".selectInstallment input").attr("max"));
            var url = new URL(window.location);
            url.searchParams.delete('bodies');
            url.searchParams.delete('mark');
            url.searchParams.delete('fuels');
            url.searchParams.delete('installment');
            url.searchParams.delete('transmission');
            url.searchParams.delete('segment');
            history.pushState({}, "", url);
            $(".searchSelectedButton").removeClass("show");
            $(".buttonReset").removeClass("show");
            refreshData(["bodies","mark","fuels","installment","transmission","segment"]);
            refreshListItem(["bodies","mark","fuels","installment","transmission","segment"]);
        }
        
      
        // Checbox array
        function checboxArray($sectionClass,$getName,$this) {
            var $checboxArray = [];
            $($sectionClass + " input:checkbox:checked").each(function(){
                $checboxArray.push($(this).val());
            });
            dataUrlUpdate($checboxArray,$getName);
            // show button
            $(".searchSelectedButton").removeClass("show");
            refreshData(["bodies","mark","fuels","installment","transmission","segment"]);
            refreshListItem(["bodies","mark","fuels","installment","transmission","segment"]);
            return $checboxArray;
        }
        
        // Checbox array transmission
        function checboxArrayTransmission($sectionClass,$getName,$this) {
            var $checboxArray = [];
            
            $checboxArray.push($this.target.value);

            dataUrlUpdate($checboxArray,$getName);
            // show button
            $(".searchSelectedButton").removeClass("show");
            refreshData(["bodies","mark","fuels","installment","transmission","segment"]);
            refreshListItem(["bodies","mark","fuels","installment","transmission","segment"]);
            return $checboxArray;
        }
        
        // Load data
        function refreshData($getNameArray) {
            var $url = new URL(window.location);
            var $count = 0;
            $.each($getNameArray, function( index, value ) {
                var $get = $url.searchParams.get(value);
                if($get !== null) {
                    if(value == "installment") {
                        $(".searchSelectedButtonInstallment").addClass("show");
                    } else {
                        $get = $get.split(',');
                        $.each($get, function( index2, value2 ) {
                            $("." + value + value2).addClass("show");
                        });
                    }
                    $count++;
                }
            });
            refreshDataComposition();
            if($count > 0) {
                $(".buttonReset").addClass("show");
            } else {
                $(".buttonReset").removeClass("show");
            }
            
            $(".sectionOfferItemNone").removeClass("show");
            
            setTimeout(function(){
                if($(".sectionOfferItem.show").length == 0) {
                    $(".sectionOfferItemNone").addClass("show");
                }
            }, 601);
        }


        searchRefresh();
        refreshData(["bodies","mark","fuels","installment","transmission","segment"]);

        setTimeout(function(){
            $(".sectionOfferItemNone").removeClass("show");
            if($(".sectionOfferItem.show").length == 0) {
                $(".sectionOfferItemNone").addClass("show");
            }
        }, 1000);


        // search 
        function searchRefresh() {
            $(".sectionOfferBox").addClass("hide");
            setTimeout(function(){
                $(".sectionOfferBox").removeClass("hide");
            }, 300);
        }

        // Checbox change detected
        $(".selectBodies input").on('click', function() {
            checboxArray(".selectBodies", "bodies", $(this));
        });
        $(".selectMark input").on('click', function() {
            checboxArray(".selectMark", "mark", $(this));
        });
        $(".selectInstallment input").on("change", function(event) {
            checboxArrayTransmission(".selectInstallment", "installment", event);
        });
        $(".selectFuels input").on('click', function() {
            checboxArray(".selectFuels", "fuels", $(this));
        });
        $(".selectTransmission input").on('click', function() {
            checboxArray(".selectTransmission", "transmission", $(this));
        });
        $(".selectSegment input").on('click', function() {
            checboxArray(".selectSegment", "segment", $(this));
        });

        // Reset checbox
        $(".buttonReset").click(function () {
            $(".sectionSearch input:checkbox:checked").each(function(){
                $(this).prop("checked",false);
            });
            $(".searchSelectedButton").removeClass("show");
            $(".buttonReset").removeClass("show");
            $("#inputSearch").val("");
            $(".installmentPriceOutput").val($(".selectInstallment input").attr("max"));
            $(".selectInstallment input").val($(".selectInstallment input").attr("max"));
            var url = new URL(window.location);
            url.searchParams.delete('bodies');
            url.searchParams.delete('mark');
            url.searchParams.delete('fuels');
            url.searchParams.delete('installment');
            url.searchParams.delete('transmission');
            url.searchParams.delete('segment');
            history.pushState({}, "", url);
            refreshListItem(["bodies","mark","fuels","installment","transmission","segment"]);
            $(".selectBodies .checkboxListItem").removeClass("hide");
            $(".selectMark .checkboxListItem").removeClass("hide");
            $(".selectFuels .checkboxListItem").removeClass("hide");
            $(".selectInstallment .checkboxListItem").removeClass("hide");
            $(".selectTransmission .checkboxListItem").removeClass("hide");
            $(".selectSegment .checkboxListItem").removeClass("hide");
        });


        // Search selected button
        $(".searchSelectedButton").click(function () {
            if($(this).hasClass("inputSearchButton")) { 
                $(this).removeClass("show");
                $("#inputSearch").val("");
                searchRefresh();
                refreshData(["bodies","mark","fuels","installment","transmission","segment"]);
            } else {
                $("#" + $(this).attr("data-id")).click();
            }
        });
    }

    
    // Checbox select menu open
    if($(window).width() > 1100) {
        $('.checkboxBox').hover(function() {
            $(this).addClass('checkboxBoxShow');
        }, function() {
            $(this).removeClass('checkboxBoxShow');
        });
    } else {
        $(document).click(function (e) {
            if($(e.target).closest(".checkboxBox, .sectionSearch").length) { } else {
               $('.checkboxBox').removeClass('checkboxBoxShow');
            }
        });
        $(".checkboxButton").on("click", function (e) {
            var $this = $(this).addClass('checkboxButtonClick');
            setTimeout(function() {
                if($(".checkboxButtonClick").parent().hasClass("checkboxBoxShow")) {
                    $(".checkboxButtonClick").parent().removeClass('checkboxBoxShow');
                } else {
                    e.preventDefault();
                    if($this.parent().hasClass('checkboxBoxShow')) {
                        $this.parent().removeClass('checkboxBoxShow');
                    } else {
                        $this.parent().addClass('checkboxBoxShow');
                    }
                }
            }, 200);
        });
    }
    
    
    // Order //////////////////////////////////////////////////
    if($(window).width() > 1100) {
        $('.sectionOfferOrderBox').hover(function() {
            $(this).addClass('sectionOfferOrderShow');
        }, function() {
            $(this).removeClass('sectionOfferOrderShow');
        });
    } else {
        $(document).click(function (e) {
            if($(e.target).closest(".sectionOfferOrderBox, .sectionSearch").length) { } else {
               $('.sectionOfferOrderBox').removeClass('sectionOfferOrderShow');
            }
        });
        $(".sectionOfferOrderButton").on("click", function (e) {
            var $this = $(this).addClass('sectionOfferOrderButtonClick');
            setTimeout(function() {
                if($(".sectionOfferOrderButtonClick").parent().hasClass("sectionOfferOrderShow")) {
                    $(".sectionOfferOrderButtonClick").parent().removeClass('sectionOfferOrderShow');
                } else {
                    e.preventDefault();
                    if($this.parent().hasClass('sectionOfferOrderShow')) {
                        $this.parent().removeClass('sectionOfferOrderShow');
                    } else {
                        $this.parent().addClass('sectionOfferOrderShow');
                    }
                }
            }, 200);
        });
    }
    
    setTimeout(function() {
        $(".sectionOfferOrderButton").text($(".sectionOfferOrderListItem input:checkbox:checked + label").text());
    }, 300);
    $(".sectionOfferOrderListItem").on("click", function (e) {
        $(".sectionOfferOrderListItem input").prop("checked",false);
        $("input", $(this)).prop("checked",true);
        $(".sectionOfferOrderButton").text($("label", $(this)).text());
        dataUrlUpdate($("input", $(this)).attr("value"),"order");
        location.reload();
    });
    
    $(".installmentPrice").on("change mousemove touchmove", function(event) {
        $(".installmentPriceOutput").text(event.target.value);
        $(".searchSelectedButtonInstallment span").text(event.target.value);
    });
    
    if($(".sectionOfferSearch > div").hasClass("sectionSearch")) {
        var progress = "";
        var range = $("#inputRange");
        setTimeout(function() {
            progress = ($(".installmentPriceOutput").text() - range.attr("min")) / (range.attr("max") - range.attr("min")) * 100;
            $("#inputRange").css("background-image", 'linear-gradient(to right, #BB2608 ' + progress + '%, #D9D9D9 ' + progress + '%)');
        }, 600);
        $("#inputRange").on("input change", function () {
            progress = ($(".installmentPriceOutput").text() - range.attr("min")) / (range.attr("max") - range.attr("min")) * 100;
            $("#inputRange").css("background-image", 'linear-gradient(to right, #BB2608 ' + progress + '%, #D9D9D9 ' + progress + '%)');
        });
    }
});


$(window).on('load', function() { 
    
    var parallaxScene = new ScrollMagic.Controller();
    var progressValue = 0;
    var tween = new TimelineMax()


    $('.ceAnimateUP').addClass('ceAnimateUPAnimate');
    $('.ceAnimateUP').each(function() {
        var thisSVG = this;
        var tweenSVG = new ScrollMagic.Scene({ 
            triggerElement: thisSVG,
            offset: 0,
            duration: $(window).height() + $(thisSVG).height(),
            triggerHook: 0.6
        }).on('enter', function () {
            $(thisSVG).removeClass('ceAnimateUPAnimate');
        }).on("leave", function () {
            $(thisSVG).addClass('ceAnimateUPAnimate');
        })
        //.addIndicators()
        .setTween(tween)
        .addTo(parallaxScene);
    });
    
    
    // more
    $('.sectioOfferMoreButton').click(function() {
        if($(this).hasClass('sectioOfferMoreButtonOpen')) {
            $(this).removeClass('sectioOfferMoreButtonOpen');
            $('.sectioOfferMore').removeClass('sectioOfferMoreOpen').css('max-height','');
        } else {
            $(this).addClass('sectioOfferMoreButtonOpen');
            $('.sectioOfferMore').addClass('sectioOfferMoreOpen').css('max-height',$('.sectioOfferMore').prop("scrollHeight"));
        }
    });
    
    
    function wi_create_cookie(name, value, days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            var expires = "; expires=";
        }
        else var expires = "";
        document.cookie = name + "=" + value + expires + "; path=/";
    } 
    $('.wi-popup-close').on('click', function () {
        wi_create_cookie("wipopup", '1', 1)
        $('#wi-popup').removeClass('wi-popup-show');
    });
    
    
    if($(".sectionNumbersBox > div").hasClass("sectionNumbersItem")) {
        var temp_scroll = 0;
        $(window).scroll(function() {
            if(temp_scroll == 0) {
                var bottom_of_object = $('.sectionNumbersBox').offset().top + $('.sectionNumbersBox').outerHeight() / 2;
                var bottom_of_window = $(window).scrollTop() + $(window).height();
                if(bottom_of_window > bottom_of_object - 200) {
                    $('.counter').each(function () {
                        var size = $(this).attr('data-count').split(".")[1] ? $(this).attr('data-count').split(".")[1].length : 0;
                        $(this).animate({
                            Counter: $(this).attr('data-count')
                        }, {
                            duration: 2000,
                            easing: 'swing',
                            step: function (now) {
                                now = parseFloat(now).toFixed(size).toString();
                                //$(this).text(now);
                                $(this).text(now.replace(/(\d+)(\d{3})/, '$1' + ' ' + '$2'));
                            }
                        });
                    }); 
                    temp_scroll++;
                }
            }
        });
    }
    
    
    // Kalkulator (single API call: full config)
    if($(".sectioOfferCalc > div").hasClass("sectioOfferCalcTitle")) {
        var lowpriceData;
        var priceData;
        var monthkmData;
        var feeData;
        var rateData;
        var calcPriceAnimFrame = null;
        var lastCalcPriceTarget = null;

        function formatPrice(num) {
            return String(Math.round(num)).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1 ');
        }
        function animateCalcPrice(target) {
            var $span = $(".sectioOfferCalcPrice span");
            if (calcPriceAnimFrame !== null) {
                cancelAnimationFrame(calcPriceAnimFrame);
            }
            var targetNum = typeof target === "number" ? target : parseInt(String(target).replace(/\s/g, ""), 10);
            if (isNaN(targetNum)) {
                lastCalcPriceTarget = null;
                $span.text(target === undefined || target === null ? "----" : target);
                return;
            }
            var currentStr = $span.text().replace(/\s/g, "");
            var start = parseInt(currentStr, 10) || 0;
            if (start === targetNum) {
                lastCalcPriceTarget = targetNum;
                $span.text(formatPrice(targetNum));
                return;
            }
            var duration = 320;
            var startTime = null;
            function tick(timestamp) {
                if (!startTime) startTime = timestamp;
                var elapsed = timestamp - startTime;
                var progress = Math.min(elapsed / duration, 1);
                progress = 1 - (1 - progress) * (1 - progress);
                var value = start + (targetNum - start) * progress;
                $span.text(formatPrice(value));
                if (progress < 1) {
                    calcPriceAnimFrame = requestAnimationFrame(tick);
                } else {
                    calcPriceAnimFrame = null;
                }
            }
            lastCalcPriceTarget = targetNum;
            calcPriceAnimFrame = requestAnimationFrame(tick);
        }

        $.getJSON($("body").attr("path") + "/carapi?all=1&id=" + $(".sectioOfferCalc").attr("carid"), function (data) {
            lowpriceData = data.lowprice || {};
            priceData = data.price || {};
            monthkmData = data.monthkm || {};
            feeData = data.fee || {};
            rateData = data.rate || {};
            $(".sectioOfferCalc").addClass("relode");
            animateCalcPrice(data.lowpriceall);
            var low_price_val = data.lowpriceall != null ? String(data.lowpriceall) : $(".sectioOfferCalcPrice span").text();
            lastCalcPriceTarget = data.lowpriceall;
            mon_km_per(low_price_val);
        });
        
        // najnizsza rata dla miesiecy
        function low_price(mon) {
            $.each(lowpriceData, function (key_top, val_top) {
                if(mon == key_top) {
                    animateCalcPrice(val_top.price);
                }
            });
        } 
        // najnizsza rata dla wybranych wartosci
        function low_price_checked() {
            var monValue = $(".sectioOfferCalcMonths .buttonCalcActive .val").attr("value");
            var kilValue = $(".sectioOfferCalcKilometers .buttonCalcActive .val").attr("value");
            var perValue = $(".sectioOfferCalcPercent .buttonCalcActive .val").attr("value");
            animateCalcPrice(rateData[monValue][kilValue.toString() + "000"][perValue].rate);
        } 
        // mesiace, km, procent dla danej kwoty _ zaznaczenie
        function mon_km_per(price) {
            var monValue = $(".sectioOfferCalcMonths .buttonCalcActive .val").attr("value");
            $.each(priceData, function (key_top, val_top) {
                if((price + "mon" + val_top.month) == key_top && (monValue == 1 || monValue == val_top.month)) {
                     // IDV
                    $(".sectioOfferCalc").attr("caridv",val_top.idv);
                    // mon
                    $(".sectioOfferCalcMonths .buttonCalc").remove();
                    $.each(monthkmData, function (key, val) {
                        $(".sectioOfferCalcMonths").append('<div class="buttonCalc displayFlex flexXcenter flexYcenter"><span class="val val'+key+'" value="'+key+'">'+key+'</span></div>');
                    });
                    $(".sectioOfferCalcMonths .val" + val_top.month).parent().addClass('buttonCalcActive');
                    // km
                    $(".sectioOfferCalcKilometers .buttonCalc").remove();
                    $.each(monthkmData, function (key, val) {
                        if(val_top.month == key) {
                            $.each(val, function (key2, val2) {
                                $(".sectioOfferCalcKilometers").append('<div class="buttonCalc displayFlex flexXcenter flexYcenter"><span class="val val'+val2+'" value="'+val2+'">'+ (parseInt(val2) / (parseInt(val_top.month) / 12))  +'</span><span class="ins">'+$(".sectioOfferCalcKilometers").attr("tys")+'</span></div>');
                            });
                            $(".sectioOfferCalcKilometers .val" + val_top.km.toString().slice(0,-3)).parent().addClass('buttonCalcActive');
                        }
                    });
                    // %
                    $.each(feeData, function (key, val) {
                        if(val_top.month == key) {
                            $.each(val[val_top.km], function (key2, val2) {
                                $(".sectioOfferCalcPercent .val" + key2).text(val2.fee.toString().replace(/(?=\d{3}$)/,' '));
                                if(val_top.percent == key2) {
                                    $(".sectioOfferCalcFee span").text(val2.fee.toString().replace(/(?=\d{3}$)/,' '));
                                }
                            });
                        }
                    });
                    $(".sectioOfferCalcPercent .buttonCalc").removeClass('buttonCalcActive');
                    $(".sectioOfferCalcPercent .val" + val_top.percent).parent().addClass('buttonCalcActive');
                }
            });
            setTimeout(function() {
                $(".sectioOfferCalc").removeClass("relode");
            }, 200);
        } 
        
        // mesiace, km, procent dla danej kwoty _ zaznaczenie
        function percent(price) {
            $.each(priceData, function (key_top, val_top) {
                if(price + "mon" + val_top.month == key_top) {
                    // IDV
                    $(".sectioOfferCalc").attr("caridv",val_top.idv);
                    // %
                    $.each(feeData, function (key, val) {
                        if(val_top.month == key) {
                            $.each(val[val_top.km], function (key2, val2) {
                                if(val_top.percent == key2) {
                                    $(".sectioOfferCalcFee span").text(val2.fee.toString().replace(/(?=\d{3}$)/,','));
                                }
                            });
                        }
                    });
                }
            });
            setTimeout(function() {
                $(".sectioOfferCalc").removeClass("relode");
            }, 400);
        } 
        
        // klikniecie w miesiac
        $(document).on("click",".sectioOfferCalcMonths .buttonCalc",function() {
            $(".sectioOfferCalc").addClass("relode");
            $(".sectioOfferCalcMonths .buttonCalc").removeClass('buttonCalcActive');
            $(".sectioOfferCalcMonths .val" + $(".val",$(this)).attr("value")).parent().addClass('buttonCalcActive');
            low_price($(".val",$(this)).attr("value"));

            setTimeout(function() {
                var low_price_val = lastCalcPriceTarget != null ? String(lastCalcPriceTarget) : $(".sectioOfferCalcPrice span").text();
                mon_km_per(low_price_val);
            }, 350);
        });
        
        // klikniecie w przebieg km
        $(document).on("click",".sectioOfferCalcKilometers .buttonCalc",function() {
            $(".sectioOfferCalcKilometers .buttonCalc").removeClass('buttonCalcActive');
            $(".sectioOfferCalcKilometers .val" + $(".val",$(this)).attr("value")).parent().addClass('buttonCalcActive');
            $(".sectioOfferCalc").addClass("relode");
            low_price_checked();

            setTimeout(function() {
                var low_price_val = lastCalcPriceTarget != null ? String(lastCalcPriceTarget) : $(".sectioOfferCalcPrice span").text();
                percent(low_price_val);
            }, 350);
        });
        
        // klikniecie w % wkladu
        $(document).on("click",".sectioOfferCalcPercent .buttonCalc",function() {
            $(".sectioOfferCalcPercent .buttonCalc").removeClass('buttonCalcActive');
            $(".sectioOfferCalcPercent .val" + $(".val",$(this)).attr("value")).parent().addClass('buttonCalcActive');
            $(".sectioOfferCalc").addClass("relode");
            low_price_checked();

            setTimeout(function() {
                var low_price_val = lastCalcPriceTarget != null ? String(lastCalcPriceTarget) : $(".sectioOfferCalcPrice span").text();
                percent(low_price_val);
            }, 350);
        });
    }
    
    // car specification - url to button
    if($(".sectioOfferItem > div").hasClass("sectioOfferCalc")) {
        function carSpecification() {
            var carHref = $(".sectioOfferCalcButton a").attr('href').split('?');
            /*
            var carID = $(".sectioOfferCalc").attr('carid');
            var carTitle = $(".topBannerTitle h1 p").text();
            var carMonths = $(".sectioOfferCalcMonths .buttonCalcActive span").attr('value');
            var carPercent = $(".sectioOfferCalcPercent .buttonCalcActive span").attr('value');
            var carKilometers = $(".sectioOfferCalcKilometers .buttonCalcActive span").attr('value');
            if(carMonths > 1 && carPercent > 1 && carKilometers > 1) {
                $(".sectioOfferCalcButton a").attr('href',carHref[0] + "?" + encodeURI("car-specification=" + carTitle + " - " + carID + " id - " + carMonths + " mies - " + carPercent + " proc - " + carKilometers / (carMonths / 12) + " km"));
            }
            */
            var carIDV = $(".sectioOfferCalc").attr('caridv');
            if (typeof carIDV !== 'undefined') {
                $(".sectioOfferCalcButton a").attr('href',carHref[0] + "?" + encodeURI("car-idv=" + carIDV));
            } else {
                $(".sectioOfferCalcButton a").attr('href',carHref[0]);
            }
            setTimeout(function() {
                carSpecification();
            }, 1000);
        }
        carSpecification();
    }
    // car specification add to form
    if($(".wpcf7-text").hasClass("carSpecification")) {
        setTimeout(function() {
            var sPageURL = window.location.search.substring(1);
            var sPageURL = sPageURL.split('&');
            var sPageURL = sPageURL[0].split('=');
            if(sPageURL[0] == "car-idv") {
                $('.wpcf7-form-control-wrap[data-name="select-sprawa"] .wpcf7-select').val('Oferty').change().css("pointer-events","none");
                $(".carSpecification").val(decodeURI(sPageURL[1]));
                $('html, body').animate({scrollTop: $("#car-specification").offset().top - $("header").outerHeight() + 1}, 'slow');
            }
        }, 700);
    }
});