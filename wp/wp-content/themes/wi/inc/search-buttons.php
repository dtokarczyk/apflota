<div class="searchSelected displayFlex flexWrap flexXstart flexYstart">
    <?php $rodzajeNadwozia = get_terms(['taxonomy' => 'rodzaj-nadwozia', 'hide_empty' => false]); ?>
    <?php if (!empty($rodzajeNadwozia)) { ?><?php foreach ($rodzajeNadwozia as $rodzajNadwozia) { ?>
        <div class="searchSelectedButton searchSelectedButtonBodies displayFlex flexXcenter flexYcenter bodies<?php echo $rodzajNadwozia->term_id; ?>" data-id="bodies<?php echo $rodzajNadwozia->term_id; ?>">
            <?php echo $rodzajNadwozia->name; ?>
            <img src="<?php echo get_template_directory_uri(); ?>/images/remove.svg" class="svg searchSelectedRemove" alt="remove">
        </div>
    <?php } ?><?php } ?>

    <?php $markiAuta = get_terms(['taxonomy' => 'marka-auta', 'hide_empty' => false]); ?>
    <?php if (!empty($markiAuta)) { ?><?php foreach ($markiAuta as $markaAuta) { ?>
        <div class="searchSelectedButton searchSelectedButtonMark displayFlex flexXcenter flexYcenter mark<?php echo $markaAuta->term_id; ?>" data-id="mark<?php echo $markaAuta->term_id; ?>">
            <?php echo $markaAuta->name; ?>
            <img src="<?php echo get_template_directory_uri(); ?>/images/remove.svg" class="svg searchSelectedRemove" alt="remove">
        </div>
    <?php } ?><?php } ?>

    <div class="searchSelectedButton searchSelectedButtonInstallment displayFlex flexXcenter flexYcenter<?php if ($_GET["installment"] > 0) { ?> show<?php } ?>" data-id="installment">
        <span><?php echo intval($_GET["installment"]); ?></span>zł
        <img src="<?php echo get_template_directory_uri(); ?>/images/remove.svg" class="svg searchSelectedRemove" alt="remove">
    </div>

    <?php $rodzajePaliwa = get_terms(['taxonomy' => 'rodzaj-paliwa', 'hide_empty' => false]); ?>
    <?php if (!empty($rodzajePaliwa)) { ?><?php foreach ($rodzajePaliwa as $rodzajPaliwa) { ?>
        <div class="searchSelectedButton searchSelectedButtonFuels displayFlex flexXcenter flexYcenter fuels<?php echo $rodzajPaliwa->term_id; ?>" data-id="fuels<?php echo $rodzajPaliwa->term_id; ?>">
            <?php echo $rodzajPaliwa->name; ?>
            <img src="<?php echo get_template_directory_uri(); ?>/images/remove.svg" class="svg searchSelectedRemove" alt="remove">
        </div>
    <?php } ?><?php } ?>

    <?php $skrzynieBiegowDo = get_terms(['taxonomy' => 'skrzynia-biegow', 'hide_empty' => false]); ?>
    <?php if (!empty($skrzynieBiegowDo)) { ?><?php foreach ($skrzynieBiegowDo as $skrzyniaBiegowDo) { ?>
        <div class="searchSelectedButton searchSelectedButtonTransmission displayFlex flexXcenter flexYcenter transmission<?php echo $skrzyniaBiegowDo->term_id; ?>" data-id="transmission<?php echo $skrzyniaBiegowDo->term_id; ?>">
            <?php echo $skrzyniaBiegowDo->name; ?>
            <img src="<?php echo get_template_directory_uri(); ?>/images/remove.svg" class="svg searchSelectedRemove" alt="remove">
        </div>
    <?php } ?><?php } ?>

    <?php $segmenty = get_terms(['taxonomy' => 'segment', 'hide_empty' => false]); ?>
    <?php if (!empty($segmenty)) { ?><?php foreach ($segmenty as $segment) { ?>
        <div class="searchSelectedButton searchSelectedButtonSegments displayFlex flexXcenter flexYcenter segment<?php echo $segment->term_id; ?>" data-id="segment<?php echo $segment->term_id; ?>">
            <?php echo $segment->name; ?>
            <img src="<?php echo get_template_directory_uri(); ?>/images/remove.svg" class="svg searchSelectedRemove" alt="remove">
        </div>
    <?php } ?><?php } ?>

    <button class="displayFlex flexXcenter flexYcenter buttonReset" type="submit">
        <span><?php echo __("Wyczyść wszystkie", "wi"); ?></span>
        <img src="<?php echo get_template_directory_uri(); ?>/images/remove.svg" class="svg searchSelectedRemove" alt="remove">
    </button>
</div>