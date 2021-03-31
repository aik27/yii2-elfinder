<?php
/**
 * Date: 31.03.2021
 * Time: 0:14
 */

namespace aik27\elfinder\plugin;

use aik27\elfinder\PluginInterface;

class AutoResize extends PluginInterface
{
    public $bind = [
        'upload.presave' => 'onUpLoadPreSave'
    ];

    /**
     * @return string
     */
    public function getName()
    {
        return 'AutoResize';
    }

    public function onUpLoadPreSave(&$path, &$name, $src, $elfinder, $volume)
    {
        $opts['maxWidth'] = $this->getOption('maxWidth', $volume);
        $opts['maxHeight'] = $this->getOption('maxHeight', $volume);
        $opts['quality'] = $this->getOption('quality', $volume);
        $opts['preserveExif'] = $this->getOption('preserveExif', $volume);
        $opts['targetType'] = $this->getOption('targetType', $volume);
        $opts['forceEffect'] = $this->getOption('forceEffect', $volume);
        
        $imageType = null;
        $srcImgInfo = null;
        
        if (extension_loaded('fileinfo') && function_exists('mime_content_type')) {
            $mime = mime_content_type($src);
            if (substr($mime, 0, 5) !== 'image') {
                return false;
            }
        }
        
        if (extension_loaded('exif') && function_exists('exif_imagetype')) {
            $imageType = exif_imagetype($src);
            if ($imageType === false) {
                return false;
            }
        } else {
            $srcImgInfo = getimagesize($src);
            if ($srcImgInfo === false) {
                return false;
            }
            $imageType = $srcImgInfo[2];
        }

        // check target image type
        $imgTypes = array(
            IMAGETYPE_GIF => IMG_GIF,
            IMAGETYPE_JPEG => IMG_JPEG,
            IMAGETYPE_PNG => IMG_PNG,
            IMAGETYPE_BMP => IMG_WBMP,
            IMAGETYPE_WBMP => IMG_WBMP
        );
        
        if (!isset($imgTypes[$imageType]) || !($opts['targetType'] & $imgTypes[$imageType])) {
            return false;
        }

        if (!$srcImgInfo) {
            $srcImgInfo = getimagesize($src);
        }

        if ($opts['forceEffect'] || $srcImgInfo[0] > $opts['maxWidth'] || $srcImgInfo[1] > $opts['maxHeight']) {
            return $this->resize($volume, $src, $srcImgInfo, $opts['maxWidth'], $opts['maxHeight'], $opts['quality'], $opts['preserveExif']);
        }

        return false;
    }

    private function resize($volume, $src, $srcImgInfo, $maxWidth, $maxHeight, $jpgQuality, $preserveExif)
    {
        $zoom = min(($maxWidth / $srcImgInfo[0]), ($maxHeight / $srcImgInfo[1]));
        $width = round($srcImgInfo[0] * $zoom);
        $height = round($srcImgInfo[1] * $zoom);
        $unenlarge = true;
        $checkAnimated = true;

        return $volume->imageUtil('resize', $src, compact('width', 'height', 'jpgQuality', 'preserveExif', 'unenlarge', 'checkAnimated'));
    }
}