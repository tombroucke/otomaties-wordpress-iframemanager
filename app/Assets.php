<?php

namespace Otomaties\OtomatiesWordpressIframemanager;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 */

class Assets
{
    /**
     * Get full asset url
     *
     * @param string $filename
     * @return string
     */
    public static function find(string $filename) : string
    {
        $filename = rtrim(ltrim($filename, '/'), '/');
        $publicPath = plugins_url('public/', dirname(__FILE__));
        $file = basename($filename);
        $file_array = explode('.', $file);

        static $manifest;

        if (empty($manifest)) {
            $manifest_path = __DIR__ . '/../public/assets.json';
            $manifest = new JsonManifest($manifest_path);
        }
        $themanifest = $manifest->get();
        if (array_key_exists($file_array[0], $themanifest)
            && array_key_exists($file_array[1], $themanifest[ $file_array[0] ])
        ) {
            return $publicPath . $manifest->get()[ $file_array[0] ][ $file_array[1] ];
        }
        return $publicPath . $filename;
    }
}