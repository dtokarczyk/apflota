<?php // Editor full //?>
<?php if ($ceLayout == 'editor_full') { ?>
    <div id="ceID<?php echo $ceIteration; ?>" class="ceEditorColumn ceEditorColumn1">
        <div class="ceEditorColumnBox displayFlex flexWrap flexXbetween flexXstart">
            <div class="ceEditorColumnItem">
                <?php echo get_sub_field('editor'); ?>
            </div>
        </div>
    </div>
<?php } ?>