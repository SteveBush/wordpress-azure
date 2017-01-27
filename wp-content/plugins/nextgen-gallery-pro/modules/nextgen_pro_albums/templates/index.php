<?php $this->start_element('nextgen_gallery.gallery_container', 'container', $displayed_gallery); ?>
<div class="ngg-pro-album <?php echo esc_attr($css_class) ?>" id="<?php echo esc_attr($id) ?>">
    <?php $i = 0; foreach ($entities as $entity) { ?>
        <div class='image_container'>
        <div class='image_link_wrapper'>
            <?php if ($open_gallery_in_lightbox AND $entity->entity_type == 'gallery'): ?>
                <a href="<?php echo esc_attr($entity->previewpic_image_url)?>"
                   data-fullsize="<?php echo esc_attr($entity->previewpic_image_url) ?>"
                   data-src="<?php echo esc_attr($entity->previewpic_image_url) ?>"
                   data-thumbnail="<?php echo esc_attr($entity->previewpic_thumbnail_url)?>"
                   data-title="<?php echo esc_attr($entity->previewpic_image->alttext)?>"
                   data-description="<?php echo esc_attr(stripslashes($entity->previewpic_image->description))?>"
                   data-image-id="<?php echo esc_attr($entity->previewpic)?>"
                   <?php echo $entity->displayed_gallery->effect_code ?>
            <?php else: ?>
            <a href="<?php echo esc_attr($entity->link)?>"
               title="<?php echo esc_attr($entity->galdesc)?>"
               class="gallery_link"
           <?php endif ?>
                <?php if($hovercaptions) { ?>
                    data-ngg-captions-enabled="true"
                    data-ngg-captions-id='<?php echo $displayed_gallery->id(); ?>'
                    data-title="<?php echo esc_attr($entity->title); ?>"
                    data-description="<?php echo esc_attr($entity->galdesc); ?>"
                <?php } ?>
                >
                <?php M_NextGen_PictureFill::render_picture_element($entity->previewpic, $thumbnail_size_name, array('class'=>'gallery_preview'))?>
            </a>
            <a href="<?php echo esc_attr($entity->link)?>"
               title="<?php echo esc_attr($entity->title); ?>"
               class="caption_link" ><?php echo_safe_html($entity->title) ?></a>
            <div class="image_description"><?php echo_safe_html(nl2br($entity->galdesc)); ?></div>
            <br class="clear"/>
        </div>
        </div>
        <?php $i++; ?>
    <?php } ?>
</div>
<?php $this->end_element(); ?>
