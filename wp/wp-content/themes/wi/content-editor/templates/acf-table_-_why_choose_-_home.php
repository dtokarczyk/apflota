<?php // Table - why choose - home //?>
<?php if ($ceLayout == 'table_-_why_choose_-_home') { ?>
    <div id="sectionTable">
        <div class="containerBig">
            <div class="sectionTableTitle"><?php echo get_field('tabela_-_naglowek', wpmlID(2)); ?></div>
            <div class="sectionTable">
                <table class="sectionTableBox">
                    <?php if (have_rows("tabela", wpmlID(2))) { ?>
                        <?php while (have_rows("tabela", wpmlID(2))) {
                            the_row(); ?>
                            <tr class="sectionTableItem">
                                <td class="sectionTableItemCol"><span><?php echo do_shortcode(get_sub_field('kolumna_1')); ?></span></td>
                                <td class="sectionTableItemCol"><span><?php echo do_shortcode(get_sub_field('kolumna_2')); ?></span></td>
                                <td class="sectionTableItemCol"><span><?php echo do_shortcode(get_sub_field('kolumna_3')); ?></span></td>
                                <td class="sectionTableItemCol"><span><?php echo do_shortcode(get_sub_field('kolumna_4')); ?></span></td>
                                <td class="sectionTableItemCol"><span><?php echo do_shortcode(get_sub_field('kolumna_5')); ?></span></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </table>
            </div>
            <div><?php echo get_field('tabela_-_pod', wpmlID(2)); ?></div>
        </div>
    </div>
<?php } ?>