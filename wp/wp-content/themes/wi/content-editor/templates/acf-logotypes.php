<?php // Logotypes //?>
<?php if ($ceLayout == 'logotypes') { ?>
    <div id="sectionLogotype">
        <div class="containerBig">
            <div class="sectionLogotypeBox">
                <?php if (have_rows("logotypes")) {
                    $i = 1; ?>
                    <?php while (have_rows("logotypes")) {
                        the_row(); ?>
                        <?php $logotyp = get_sub_field('logotyp'); ?>
                        <?php $odnosnik = get_sub_field('odnosnik'); ?>
                        <?php if ($odnosnik != "") { ?>
                            <a href="<?php echo $odnosnik; ?>" title="logotypy" target="_blank">
                        <?php } else { ?>
                            <div>
                        <?php } ?>
                                <span><img src="<?php echo $logotyp; ?>" class="img-responsive"/></span>
                        <?php if ($odnosnik != "") { ?>
                            </a>
                        <?php } else { ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>