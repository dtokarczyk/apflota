    <div class="sectionOfferOrder displayFlex flexXend flexYcenter">
        <div class="sectionOfferOrderTitle"><?php echo __("Sortuj według:","wi"); ?></div>
        <div class="sectionOfferOrderBox">
            <div class="sectionOfferOrderButton"><?php echo __("Domyślnie","wi"); ?></div>
            <div class="sectionOfferOrderList displayFlex flexWrap flexXstart flexYcenter">
                <div class="sectionOfferOrderListItem displayFlex flexXstart flexYcenter">
                    <input type="checkbox" id="order0" name="order" value="0"<?php if (!isset($_GET["order"]) || $_GET["order"] == "0") { echo ' checked'; } ?>>
                    <label for="order0"><span></span><?php echo __("Domyślnie","wi"); ?></label>
                </div>
                <div class="sectionOfferOrderListItem displayFlex flexXstart flexYcenter">
                    <input type="checkbox" id="orderpl" name="order" value="pl"<?php if ($_GET["order"] == "pl") { echo ' checked'; } ?>>
                    <label for="orderpl"><span></span><?php echo __("Cena od najniższej","wi"); ?></label>
                </div>
                <div class="sectionOfferOrderListItem displayFlex flexXstart flexYcenter">
                    <input type="checkbox" id="orderph" name="order" value="ph"<?php if ($_GET["order"] == "ph") { echo ' checked'; } ?>>
                    <label for="orderph"><span></span><?php echo __("Cena od nawyższej","wi"); ?></label>
                </div>
                <div class="sectionOfferOrderListItem displayFlex flexXstart flexYcenter">
                    <input type="checkbox" id="orderaz" name="order" value="az"<?php if ($_GET["order"] == "az") { echo ' checked'; } ?>>
                    <label for="orderaz"><span></span><?php echo __("Alfabetycznie A-Z","wi"); ?></label>
                </div>
                <div class="sectionOfferOrderListItem displayFlex flexXstart flexYcenter">
                    <input type="checkbox" id="orderza" name="order" value="za"<?php if ($_GET["order"] == "za") { echo ' checked'; } ?>>
                    <label for="orderza"><span></span><?php echo __("Alfabetycznie Z-A","wi"); ?></label>
                </div>
            </div>
        </div>
    </div>
    <div class="sectionOfferBox displayFlex flexWrap flexXstart flexYstretch">
    <?php if (!isset($_GET["order"]) || $_GET["order"] == "0") { ?>
        <?php $args = array('post_type' => 'post', 'posts_per_page' => '-1', 'orderby' => 'menu_order', 'order' => 'ASC'); ?>
    <?php } elseif($_GET["order"] == "az") { ?>  
        <?php $args = array('post_type' => 'post', 'posts_per_page' => '-1', 'orderby' => 'name', 'order' => 'ASC'); ?>
    <?php } elseif($_GET["order"] == "za") { ?>  
        <?php $args = array('post_type' => 'post', 'posts_per_page' => '-1', 'orderby' => 'name', 'order' => 'DESC'); ?>
    <?php } elseif($_GET["order"] == "pl") { ?>  
        <?php $args = array('post_type' => 'post', 'posts_per_page' => '-1', 'meta_key' => 'cena_od', 'orderby' => 'meta_value_num', 'order' => 'ASC'); ?>
    <?php } elseif($_GET["order"] == "ph") { ?>  
        <?php $args = array('post_type' => 'post', 'posts_per_page' => '-1', 'meta_key' => 'cena_od', 'orderby' => 'meta_value_num', 'order' => 'DESC'); ?>
    <?php } ?> 
        
    <?php $wp_query = new WP_Query($args); ?>
    <?php if (have_posts()) { ?>
        <?php while (have_posts()) { the_post(); ?>
            <?php // rodzaj nadwozia ?>
            <?php $rodzajeNadwoziaClass = []; $rodzajeNadwozia = get_the_terms(get_the_ID(), 'rodzaj-nadwozia'); ?>
            <?php foreach ($rodzajeNadwozia as $rodzajNadwozia) { ?>
                <?php $rodzajeNadwoziaClass[] = $rodzajNadwozia->term_id; ?>
            <?php } ?>
            <?php $rodzajeNadwoziaClass = implode(",", $rodzajeNadwoziaClass); ?>
    
            <?php // marka auta ?>
            <?php $markiAutaClass = []; $markiAuta = get_the_terms(get_the_ID(), 'marka-auta'); ?>
            <?php foreach ($markiAuta as $markaAuta) { ?>
                <?php $markiAutaClass[] = $markaAuta->term_id; ?>
            <?php } ?>
            <?php $markiAutaClass = implode(",", $markiAutaClass); ?>

            <?php // rodzaj paliwa ?>
            <?php $rodzajePaliwaClass = []; $rodzajePaliwa = get_the_terms(get_the_ID(), 'rodzaj-paliwa'); ?>
            <?php foreach ($rodzajePaliwa as $rodzajPaliwa) { ?>
                <?php $rodzajePaliwaClass[] = $rodzajPaliwa->term_id; ?>
            <?php } ?>
            <?php $rodzajePaliwaClass = implode(",", $rodzajePaliwaClass); ?>
    
            <?php // skrzynia biegow ?>
            <?php $skrzynieBiegowClass = []; $skrzynieBiegowDo = get_the_terms(get_the_ID(), 'skrzynia-biegow'); ?>
            <?php foreach ($skrzynieBiegowDo as $skrzyniaBiegowDo) { ?>
                <?php $skrzynieBiegowClass[] = $skrzyniaBiegowDo->term_id; ?>
            <?php } ?>
            <?php $skrzynieBiegowClass = implode(",", $skrzynieBiegowClass); ?>
    
            <?php // segment ?>
            <?php $segmentyClass = []; $segmenty = get_the_terms(get_the_ID(), 'segment'); ?>
            <?php foreach ($segmenty as $segment) { ?>
                <?php $segmentyClass[] = $segment->term_id; ?>
            <?php } ?>
            <?php $segmentyClass = implode(",", $segmentyClass); ?>

            <a href="<?php echo get_permalink(); ?>" class="sectionOfferItem" 
               data-name="<?php echo get_the_title(); ?>" 
               data-bodies="<?php echo $rodzajeNadwoziaClass; ?>" 
               data-mark="<?php echo $markiAutaClass; ?>" 
               data-fuels="<?php echo $rodzajePaliwaClass; ?>" 
               data-installment="<?php echo get_field('cena_od'); ?>" 
               data-transmission="<?php echo $skrzynieBiegowClass; ?>" 
               data-segment="<?php echo $segmentyClass; ?>"
            >
                <span class="sectionOfferItemInside">
                    <span class="sectionOfferItemsImg">
                        <img class="img-full" alt="<?php echo get_the_title(); ?>" src="<?php $grafiki = get_field('zdjecie_glowne'); echo $grafiki['sizes']['produkt-500x250']; ?>" />
                    </span>
                    <span class="sectionOfferItemDesc">
                        <span class="sectionOfferItemDesc1 displayFlex flexWrap flexXstart flexYcenter">
                            <span class="displayFlex flexXcenter flexYcenter">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/fuel.svg" alt="fuel">
                                <?php echo $rodzajePaliwa[0]->name;?>
                            </span>
                            <span class="displayFlex flexXcenter flexYcenter">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/gearbox.svg" alt="gearbox">
                                <?php echo $skrzynieBiegowDo[0]->name; ?>
                            </span>
                            <span class="displayFlex flexXcenter flexYcenter">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/segment.svg" alt="segment">
                                <?php echo $segmenty[0]->name; ?>
                            </span>
                        </span>
                        <span class="sectionOfferItemDesc2">
                            <span class="sectionOfferItemDescTitle"><?php echo get_the_title(); ?></span>
                            <?php echo get_field('silnik'); ?>
                            <span class="sectionOfferItemDescTitle"><?php echo __("od","wi"); ?> <?php echo get_field('cena_od'); ?> <?php echo __("zł","wi"); ?></span>
                            <?php echo __("za miesiąc","wi"); ?>
                        </span>
                    </span>
                </span>
            </a>
        <?php } ?>
    <?php } ?>
   <div class="sectionOfferItemNone"><?php echo __("Brak samochodów spełniających kryteria","wi"); ?></div>
</div>