<?php // FAQ //?>
<?php if ($ceLayout == 'faq') { ?>
    <div class="ceGrayBackground">
        <?php containerStart(get_sub_field('container')); ?>
            <div id="ceID<?php echo $ceIteration; ?>" class="ceFaq">
                <?php if (get_sub_field('heading') != '') { ?>
                    <div class="ceFaqTitle"><?php echo get_sub_field('heading'); ?></div>
                <?php } ?>
                <?php if (have_rows("faq")) { ?>
                    <?php while (have_rows("faq")) {
                        the_row(); ?>
                        <div class="ceFaqBox">
                            <div class="ceFaqQuestion">
                                <div class="ceFaqQuestionTitle">
                                    <?php echo get_sub_field('question'); ?>
                                </div>
                                <div class="ceFaqQuestionButton">
                                    <?php if (file_exists(get_template_directory() . '/images/arrowFAQ.svg')) { ?>
                                        <img src="<?php echo get_template_directory_uri(); ?>/images/arrowFAQ.svg" alt="Icon" />
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="ceFaqAnswer">
                                <div>
                                    <div class="ceFaqAnswerPaddingTop"></div>
                                    <?php echo get_sub_field('answer'); ?>
                                    <div class="ceFaqAnswerPaddingBottom"></div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        <?php containerEnd(get_sub_field('container')); ?>
    </div>
<?php } ?>