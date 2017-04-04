<?php $this->start_element('nextgen_gallery.gallery_container', 'container', $displayed_gallery); ?>
<div class="ngg-pro-album <?php echo esc_attr($css_class) ?>" id="<?php echo esc_attr($id) ?>">
    <?php
    foreach ($entities as $entity) { ?>
        <div class='image_container'>
            <div class='image_link_wrapper'>
                <?php
                if ($open_gallery_in_lightbox AND $entity->entity_type == 'gallery') {
                    $anchor = $entity->displayed_gallery->effect_code . "
                              href='" . esc_attr($entity->link) . "'
                              title='" . esc_attr($entity->galdesc) . "'
                              data-src='" .  esc_attr($entity->previewpic_image_url) . "'
                              data-fullsize='" .  esc_attr($entity->previewpic_image_url) . "'
                              data-thumbnail='" . esc_attr($entity->previewpic_thumbnail_url) . "'
                              data-title='" . esc_attr($entity->previewpic_image->alttext) . "'
                              data-description='" . esc_attr(stripslashes($entity->previewpic_image->description)) . "'
                              data-image-id='" . esc_attr($entity->previewpic) . "'";
                } else {
                    $anchor = "href='" . esc_attr($entity->link) . "'
                               title='" . esc_attr($entity->galdesc) . "'";
                } ?>
                <span class="gallery_link">
                    <a <?php echo $anchor; ?>><?php M_NextGen_PictureFill::render_picture_element(
                            $entity->previewpic, $thumbnail_size_name, array('class'=>'gallery_preview')
                        ); ?>
                    </a>
                </span>
                <span class="caption_link">
                    <a <?php echo $anchor; ?>><?php echo_safe_html($entity->title) ?></a>
                </span>
                <div class="image_description">
                    <?php echo_safe_html(nl2br($entity->galdesc)); ?>
                </div>
                <br class="clear"/>
            </div>
        </div>
    <?php } ?>
</div>
<?php $this->end_element(); ?>