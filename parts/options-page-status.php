<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'Invalid request.' );
}
?>
<div class="webp-images-bulk-infos status">
    <?php
    $tests = [
        ['Vips', 'webp_images_getVipsStatus', 'https://github.com/rosell-dk/webp-convert/wiki/Installing-vips-extension'],
        ['CwebP', 'webp_images_getCwebpStatus', 'https://developers.google.com/speed/webp/docs/compiling'],
        ['Imagick', 'webp_images_getImagickStatus', 'https://github.com/rosell-dk/webp-convert/wiki/Installing-Imagick-extension-with-WebP-support'],
        ['GraphicsMagick', 'webp_images_getGraphicsMagickStatus', 'https://github.com/rosell-dk/webp-convert/wiki/Installing-GMagick-PHP-extension-with-WebP-support'],
        ['Imagemagick', 'webp_images_getImageMagickStatus', ''],
        ['Gd', 'webp_images_getGdStatus', 'https://github.com/rosell-dk/webp-convert/wiki/Installing-Gd-extension-with-WebP-support'],
    ];

    foreach ($tests as $i => list($method, $functionName, $installInstructionsURL)) {
        list($success, $statusText) = $testResult = call_user_func($functionName);
        echo '<div class="webp-images-bulk-info'.($i == 5 ? ' last' : '').'">';
        echo '<div>' . $method . '</div>';
        if ($success) {
            echo '<div><span class="dashicons dashicons-yes"></span></div>';
        } else {
            echo '<div><span class="dashicons dashicons-no"></span></div>';
            if ($installInstructionsURL != '') {
                $statusText .= '<br>Installation instructions are available <a href="' . $installInstructionsURL . '" target="_blank" rel="nofollow">here</a>';
            }
        }
        echo '<div>' . $statusText . '</div>';
        echo '</div>';
    }
    ?>
</div>