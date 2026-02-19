<?php // Files to download //?>
<?php if ($ceLayout == 'files_to_download') { ?>
    <div class="ceGrayBackground">
        <?php containerStart(get_sub_field('container')); ?>
            <div id="ceID<?php echo $ceIteration; ?>" class="ceFilesDownload ceFilesDownload<?php echo get_sub_field('files_to_download_type'); ?>">
                <?php if (get_sub_field('heading') != '') { ?>
                    <div class="ceFilesDownloadTitle"><?php echo get_sub_field('heading'); ?></div>
                <?php } ?>
                <?php if (have_rows("files_to_download")) { ?>
                    <div class="sectionAttachments sectionProductAttachments">
                        <?php while (have_rows("files_to_download")) {
                            the_row(); ?>
                            <div class="attachment application">
                                <?php
                                        $file = get_sub_field('attachment');
                            $date = date_i18n("d F Y", strtotime($file['date']));
                            $filesize = filesize(get_attached_file($file['ID']));
                            $filesize = size_format($filesize, 0);
                            $mime_type = explode('/', $file['mime_type']);
                            ?>
                                <a download="" target="_blank" href="<?php echo $file['url']; ?>" class="displayFlex flexXbetween flexYcenter">
                                    <span class="attachmentsTitle displayFlex flexXstart flexYcenter">
                                        <img src="<?php echo get_template_directory_uri(); ?>/images/downloadBefore.svg" alt="download" />
                                        <span><?php echo koncowki_luk_mod(get_sub_field('name')); ?></span>
                                    </span>
                                    <span class="attachmentsSizeTypeBtn displayFlex flexXbetween flexYcenter">
                                        <span class="button buttonTransparent displayFlex flexXstart flexYcenter">
                                            <span class="displayFlex flexXbetween flexYcenter">
                                                <img src="<?php echo get_template_directory_uri(); ?>/images/download.svg" alt="download" />
                                                <?php echo __('pobierz plik', 'wi'); ?>
                                            </span>
                                        </span>
                                    </span>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php containerEnd(get_sub_field('container')); ?>
    </div>
<?php } ?>