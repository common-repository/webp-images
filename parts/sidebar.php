<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Invalid request.' );
}

if( !$this->is_pro_version_active() ){ ?>
    <div class="webp-images-sidebar-content">
        <h2><?php _e('WebP Images PRO', 'webp-images');?></h2>
        <p><?php _e('The <u>PRO version</u> of this fantastic plugin allows you to set <u>Conversion quality</u> and <u>Auto conversion on upload</u>.', 'webp-images');?></p>
        <p><a href="https://totalpress.org/plugins/webp-images?utm_source=wp-dashboard&utm_medium=installed-plugin&utm_campaign=webp-images" class="button button-primary button-hero" target="_blank" title="<?php _e( 'WordPress WebP plugin', 'webp-images' ); ?>"><?php _e( 'Get PRO', 'webp-images' ); ?></a></p>
        <p><a href="https://wordpress.org/plugins/webp-images/" class="button button-secondary button-hero" target="_blank"><?php _e( 'Get support', 'webp-images' ); ?></a></p>
        <p><a href="https://wordpress.org/support/plugin/webp-images/reviews/#new-post" class="button button-secondary button-hero" target="_blank"><?php _e( 'Write a review', 'webp-images' ); ?></a></p>
    </div>
<?php } else { ?>
    <div class="webp-images-sidebar-content">
        <h2><?php _e('WebP Images PRO', 'webp-images');?></h2>
        <p><?php _e('Thanks for using the <u>PRO version</u> of this fantastic plugin.', 'webp-images');?></p>
        <p><a href="https://wordpress.org/plugins/webp-images/" class="button button-secondary button-hero" target="_blank"><?php _e( 'Get support', 'webp-images' ); ?></a></p>
        <p><a href="https://wordpress.org/support/plugin/webp-images/reviews/#new-post" class="button button-secondary button-hero" target="_blank"><?php _e( 'Write a review', 'webp-images' ); ?></a></p>
    </div>
<?php }