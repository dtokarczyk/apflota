jQuery(window).on('load', function() {
    jQuery('.post-type-produkty .postbox').each(function(){
        if(jQuery(this).attr("id").includes("bruk")) {
            jQuery(".acf-field[data-name='filtry']").append(jQuery(this));
        }
    });
});  