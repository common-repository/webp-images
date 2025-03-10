<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'Invalid request.' );
}

function webp_images_execCommand($cmd){
    exec($cmd, $output, $returnCode);
    if (($returnCode == 0) && isset($output[0])) {
        return $output;
    }

    $binary = explode($cmd, ' ')[0];
    if ($returnCode == 127) {
        throw new \Exception(
            'Cannot execute "' . $cmd . '". ' .
            'Failed with status code 127 which indicates that ' . $binary . ' is not installed.'
        );
    }
    if ($returnCode == 126) {
        $who = '';
        exec('whoami', $whoOutput, $whoReturnCode);
        if (($whoReturnCode == 0) && (isset($whoOutput[0]))) {
            $who = $whoOutput[0];
        }
        throw new \Exception(
            'Cannot execute "' . $cmd . '". ' .
            'Failed with status code 126 which means "Permission denied". ' .
            ($who != '' ? $who : 'The user that the command was run with') . ' ' .
            'does not have permission to execute the ' . $binary . ' binary'
        );
    }
    throw new \Exception(
        'Cannot execute "' . $cmd . '". ' .
        'Failed with status code: ' . $returnCode
    );
}

function webp_images_getCwebpStatus(){
    if (!function_exists('exec')) {
        return [false, 'exec() is not enabled.'];
    }
    try {
        $output = webp_images_execCommand('cwebp -version');
        return [true, 'Version: ' . (isset($output[0]) ? $output[0] : ' Not available')];
    } catch (\Exception $e) {
        return [false, $e->getMessage()];
    }
}

function webp_images_getImageMagickStatus(){
    if (!function_exists('exec')) {
        return [false, 'exec() is not enabled.'];
    }

    try {
        $versionOutput = webp_images_execCommand('convert -version');
        $version = (isset($versionOutput[0]) ? $versionOutput[0] : ' Not available');
        $version = str_replace(' http://www.imagemagick.org', '', $version);

        exec('convert -list delegate', $output, $returnCode);
        foreach ($output as $line) {
            if (preg_match('#webp\\s*=#i', $line)) {
                $hasWebPDelegate = true;
                break;
            }
        }
        if (!$hasWebPDelegate) {
            // try other command
            exec('convert -list configure', $output, $returnCode);
            foreach ($output as $line) {
                if (preg_match('#DELEGATE.*webp#i', $line)) {
                    $hasWebPDelegate = true;
                }
            }
        }
        if (!$hasWebPDelegate) {
            return [0, 'Imagick was compiled without webp support.'];
            // This will often also be visibly by running phpinfo()
            //echo phpinfo();
        }

        return [true, $version];
    } catch (\Exception $e) {
        return [false, $e->getMessage()];
    }

}

function webp_images_getVipsStatus(){
    if (!extension_loaded('vips')) {
        return [false, 'Vips extension is not installed.'];
    }

    if (!function_exists('vips_image_new_from_file')) {
        return [false, 'Vips extension seems to be installed, however something is not right: ' .
                'the function "vips_image_new_from_file" is not available.'];
    }

    $versionInfo = '';
    if (function_exists('vips_version')) {
        $versionInfo .= 'vipslib version: ' . vips_version() . '<br>';
    }
    if (function_exists('phpversion')) {
        $versionInfo .= 'vips extension version: ' . phpversion('vips');
    }


    return [true, $versionInfo];
}

function webp_images_getImagickStatus(){
    if (!extension_loaded('imagick')) {
        return [false, 'iMagick extension is not installed.'];
    }

    if (!class_exists('\\Imagick')) {
        return [false, 'iMagick is installed, but not correctly. The class Imagick is not available'];
    }

    $im = new \Imagick();

    $formats = array_map('strtoupper', $im->queryformats());
    if (!in_array('WEBP', $formats)) {
        return [false, 'iMagick was compiled without WebP support.'];
    }
    if (!in_array('PNG', $formats)) {
        return [false, 'iMagick was compiled without PNG support and can therefore not convert PNG images to WEBP'];
    }
    if (!in_array('JPEG', $formats)) {
        return [false, 'iMagick was compiled without JPEG support and can therefore not convert JPEG images to WEBP'];
    }

    $versionInfo = '';

    if (method_exists($im, 'getVersion')) {
        try {
            $versionInfo = 'Version: ' . $im->getVersion()['versionString'];
            // ie: "ImageMagick 6.7.6-1 2012-04-09 Q16 http://www.imagemagick.org".
            // remove "http://www.imagemagick.org" from the string.
            $versionInfo = str_replace(' http://www.imagemagick.org', '', $versionInfo);


        } catch (\Exception $e) {
            $versionInfo = 'Version could not be detected (threw an exception)';
        }
    } else {
        $versionInfo = 'Version info is not available.';
    }

    return [true, $versionInfo];
}

function webp_images_getGraphicsMagickStatus(){
    if (!extension_loaded('Gmagick')) {
        return [false, 'Gmagick extension is not installed.'];
    }

    if (!class_exists('Gmagick')) {
        return [false, 'Gmagick is installed, but not correctly. The class Gmagick is not available'];
    }

    $im = new \Gmagick();
    $formats = array_map('strtoupper', $im->queryformats());
    if (!in_array('WEBP', $formats)) {
        return [false, 'Gmagick was compiled without WebP support.'];
    }
    if (!in_array('PNG', $formats)) {
        return [false, 'Gmagick was compiled without PNG support and can therefore not convert PNG images to WEBP'];
    }
    if (!in_array('JPEG', $formats)) {
        return [false, 'Gmagick was compiled without JPEG support and can therefore not convert JPEG images to WEBP'];
    }

    $versionInfo = '';
    if (method_exists($im, 'getVersion')) {
        try {
            $versionInfo = 'Version: ' . $im->getVersion()['versionString'];
            // ie: "GraphicsMagick 1.3.31 2018-11-17 Q8 http://www.GraphicsMagick.org/".
            // remove "http://www.GraphicsMagick.org/" from the string.
            $versionInfo = str_replace(' http://www.GraphicsMagick.org/', '', $versionInfo);
        } catch (\Exception $e) {
            $versionInfo = 'Version could not be detected (threw an exception)';
        }
    } else {
        $versionInfo = 'Version info is not available.';
    }

    return [true, $versionInfo];
}

function webp_images_getGdStatus(){
    if (!extension_loaded('gd')) {
        return [false, 'Gd extension is not installed'];
    }
    if (!function_exists('imagewebp')) {
        return [false, 'Gd has been compiled without webp support.'];
    }

    if (!function_exists('imagecreatefrompng')) {
        return [false, 'Gd has been compiled without PNG support and can therefore not convert PNG images to WEBP'];
    }
    if (!function_exists('imagecreatefromjpeg')) {
        return [false, 'Gd has been compiled without JPEG support and can therefore not convert JPEG images to WEBP'];
    }

    if (function_exists('gd_info')) {
        $info = gd_info();
        $note = '<br><i>Note that Gd is inferior to the other methods as it does not support any webp options. ' .
            'It can for example not produce lossless webps which makes it inadequate for converting PNG to WEBP.</i>';
        if (isset($info['GD Version'])) {
            return [true, 'Version: ' . $info['GD Version'] . $note];
        } else {
            return [true, 'Info: ' . print_r($info, true) . $note];
        }

    } else {
        return [true, 'Working! No version info available'];
    }

}