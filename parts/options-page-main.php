<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'Invalid request.' );
}
if(!$this->is_pro_version_active()){ ?>
    <p><?php printf(__('To <span class="pro-required">set Conversion quality and Auto conversion on upload</span>, please <a href="%s" target="_blank" title="Get PRO version">get the PRO version</a>.', 'webp-images'), 'https://www.andreadegiovine.it/risorse/plugin/webp-images?utm_source=tools_plugin_page&utm_medium=plugin_page&utm_campaign=webp_images' ); ?></p>
<?php } ?>
<p><?php _e('This plugin use a special <strong>htaccess rules</strong> to shows the converted WebP versions (if they exist) instead the originals.<br>It works with <strong>all images used in frontend uploaded and managed by the WordPress media library</strong> (it does not work with images uploaded via ftp or alternatives).', 'webp-images');?></p>
<p><?php _e('If it generates errors, bugs or malfunctions, use the bulk function (below) to <strong>remove all Webp versions</strong> and just finished deactivate the plugin.<br>Check if the .htaccess file still contains "WebP Images" rules and delete them.', 'webp-images');?></p>
<p><?php _e('This plugin can <strong>automatically converts the uploaded images</strong> into WebP format.', 'webp-images');?></p>
<?php
if(has_action('webp_images_options_page')){
    do_action('webp_images_options_page');
} else { ?>
    <form>
        <div class="webp-images-bulk-infos">
            <div class="webp-images-bulk-info"><div class="pro-required"><?php _e('Conversion quality (0-100)', 'webp-images');?></div><div>
                    <input type="number" disabled></div></div>
            <div class="webp-images-bulk-info last"><div class="pro-required"><?php _e('Auto conversion on upload', 'webp-images');?></div><div><label class="switch">
                        <input type="checkbox" disabled />
                        <span class="slider"></span>
                    </label></div></div>
        </div>
        <div class="webp-images-bulk-actions">
            <?php submit_button(__('Save Settings', 'webp-images'),'secondary button-hero', 'submit', false, ['disabled' => true]); ?>
        </div>
    </form>
<?php } ?>
<h2><?php _e('Bulk images management', 'webp-images');?></h2>
<p><?php _e('Use these tools to convert or delete images in WebP format.', 'webp-images');?></p>
<div class="webp-images-bulk-infos">
    <div class="webp-images-bulk-info total"><div><?php _e('Total images in media', 'webp-images');?></div><div><?php echo $this->count_media_images();?></div></div>
    <div class="webp-images-bulk-info nowebp"><div><?php _e('Images not converted', 'webp-images');?></div><div><?php echo $this->count_not_webp_media_images();?></div></div>
    <div class="webp-images-bulk-info webp"><div><?php _e('Images converted to WebP', 'webp-images');?></div><div><?php echo $this->count_webp_media_images();?></div></div>
</div>
<div class="webp-images-bulk-actions">
    <button class="button button-primary button-hero wepb-images-start-conversion" data-webp-images-label="<?php _e('Conversion in progress', 'webp-images');?>"<?php echo 0 == $this->count_not_webp_media_images() ? 'disabled="disabled"' : '' ;?>><?php _e('Convert all images to WebP', 'webp-images');?></button>
    <button class="button button-secondary button-hero wepb-images-bulk-delete" data-webp-images-label="<?php _e('Deleting in progress', 'webp-images');?>"<?php echo 0 == $this->count_webp_media_images() ? 'disabled="disabled"' : '' ;?>><?php _e('Remove all WebP images', 'webp-images');?></button>
</div>
