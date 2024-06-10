<?php

use App\Models\Setting;
use Intervention\Image\ImageManager;
use Jenssegers\Date\Date;

Date::setLocale('ru');

include_once('php-html-css-js-minifier.php');

if (!function_exists('setting')) {
    function setting($key, $name = 0)
    {
        $value = Setting::where("key", $key)->first();
        if ($name) {
            return count((array)$value) ? $value->name : '';
        } else {
            return count((array)$value) ? $value->value : '';
        }
    }
}

function superadmin()
{
    return Auth::user()->hasRole('superadmin');
}

function getImage($imageData, $size)
{
    $json = json_decode($imageData, true);
    $jsonValidate = is_string($imageData) && is_array($json) ? true : false;
    $resizes = [
        'minimal',
        'small',
        'medium',
        'large',
        'maximum'
    ];
    if ($jsonValidate) {
        $imageFile = $json[0]['path'] . '/' . $json[0]['name'];
    } else {
        $imageFile = $imageData;
    }
    $found = 0;
    $resizedImage = $imageFile;
    foreach ($resizes as $resize) {
        if ($size != $resize) {
            continue;
        }
        $resizedImage = preg_replace('~^/upload~', '/upload/resized/' . $size, $imageFile);
        if (file_exists(public_path($resizedImage))) {
            $found = 1;
            break;
        } else {
            $size = next($resizes);
        }
    }
    return $found ? $resizedImage : $imageFile;
}

if (!function_exists('getResizedImage')) {
    function getResizedImage($imageFile, $size)
    {

        if (empty($imageFile)) {
            return '';
        }

        $imageResizedFolders = [
            'minimal',
            'small',
            'medium',
            'large'
        ];

        $resizedImagesFile = '/upload/resized/' . $size . str_replace('upload/', '', $imageFile);
//        dd(public_path() .$resizedImagesFile);
        while (!file_exists(public_path() . $resizedImagesFile)) {
            foreach ($imageResizedFolders as $folder) {
                if ($folder != $size) {
                    array_shift($imageResizedFolders);
                    continue;
                }
                break;
            }
            if (count($imageResizedFolders) == 0) {
                break;
            }
            $size = next($imageResizedFolders);
            $resizedImagesFile = '/upload/resized/' . $size . str_replace('upload/', '', $imageFile);
        }

        if (empty($size)) {
            $resizedImagesFile = $imageFile;
        }
//        dump($resizedImagesFile);
        return $resizedImagesFile;
    }
}
if (!function_exists('russianDate')) {
    function russianDate($carbon, $format = 'F d, Y')
    {
        $drawDate = new Date($carbon);
        return $drawDate->format($format);
    }
}

if (!function_exists('resizeImage')) {
    function resizeImage($imageFile, $width, $height, $crop = 0, $watermark = 0)
    {
        $imageFile = urldecode(is_array($imageFile) ? $imageFile[0] : $imageFile);
        $imagePath = public_path(preg_replace('~^\/~', '', $imageFile));
        $extention = last(explode('.', $imageFile));
        if (in_array($extention, ['svg'])) {
            return $imageFile;
        }

        $watermarkImg = $watermark ? public_path() . setting('watermark') : '';

        if (!$imageFile || !file_exists($imagePath)) {
            return 'https://via.placeholder.com/' . ($width > 0 ? $width : 640) . 'x' . ($height > 0 ? $height : 480) . '.jpg';
        }
        $hash = substr(sha1($width . '-' . $height . '-' . $crop . '-' . ($watermark ? md5_file($watermarkImg) : '') . '-' . md5_file($imagePath)), 0, 8);


        if (preg_match('~upload/storage~', $imageFile)) {
            $imageFileNew = str_replace('/upload/storage', 'upload/resized', preg_replace('~(\.' . $extention . ')$~', '-' . $hash . '$1', $imageFile));
        } else {
            $imageFileNew = str_replace('/upload', 'upload/resized', preg_replace('~(\.' . $extention . ')$~', '-' . $hash . '$1', $imageFile));
        }

        $existsFile = preg_replace('~' . $extention . '$~', 'webp', $imageFileNew);
        if (file_exists(public_path($existsFile))) {
            return '/' . $existsFile;
        }

        $manager = new ImageManager(array('driver' => 'imagick'));
        $image = $manager->make($imagePath);

        $myWidth = $image->width();
        $myHeight = $image->height();
        $proportion = $myWidth / $myHeight;

        if ($height == 0 && $proportion) {
            $height = $width / $proportion;
        } elseif ($width == 0 && $proportion) {
            $width = $height / $proportion;
        }

        if ($watermark && setting('wm_switcher')) {
            $wm_img = $manager->make($watermarkImg);
            $wm_size = setting('wm_minsize');
            $image->insert($wm_img, 'center');
        } elseif (0 && ($myWidth < $width || $myHeight < $height)) {
            return $imageFile;
        }

        if ($crop && $width > 0 && $height > 0) {
            if ($width >= $height) {
                if ($myHeight * $width / $myWidth >= $height) {
                    $image->resize($width, $myHeight * $width / $myWidth);
                } else {
                    $image->resize($myWidth * $height / $myHeight, $height);
                }
            } else {
                $image->resize($myWidth * $height / $myHeight, $height);
            }
            $image->crop($width, $height);
        } else {
            if ($proportion >= 1) {
                $image->resize($width, $width / $proportion);
            } else {
                $image->resize($height * $proportion, $height);
            }
        }

        $newImageFilename = $image->filename . '-' . $hash . '.' . $extention;
        $imageDir = public_path(str_replace($newImageFilename, '', $imageFileNew));

        if (!file_exists($imageDir)) {
            mkdir($imageDir, 0755, true);
        }
        if (in_array($extention, ['jpeg', 'jpg', 'png'])) {
            // $image->interlace();
        }

        $image->encode('webp');
        $imageFileNew = str_replace($extention, 'webp', $imageFileNew);
        $image->save(public_path($imageFileNew), setting('image_quality') ?: 100);

        return '/' . ltrim($imageFileNew, '/');
    }
}
