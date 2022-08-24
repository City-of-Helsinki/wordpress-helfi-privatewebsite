<?php

namespace CityOfHelsinki\WordPress\PrivateWebsite;

add_action('helsinki_privatewebsite_init', __NAMESPACE__ . '\\privatewebsite_check_media_restriction_file');

function privatewebsite_check_media_restriction_file() {    
    $path = trailingslashit( wp_upload_dir()['basedir'] );


    $restriction_enabled = '';
    $settings = get_option('helsinki-privatewebsite-settings', array());
    if (isset($settings['protect-media-files'])) {
        $restriction_enabled = $settings['protect-media-files'];
    }

    if (isset($restriction_enabled) && $restriction_enabled === 'on') {
        if (!file_exists($path . '.htaccess')) {
            privatewebsite_create_media_restriction_file();
        }
    }
    else {
        if (file_exists($path . '.htaccess')) {
            privatewebsite_remove_media_restriction_file();
        }
    }
}

function privatewebsite_create_media_restriction_file() {
    $path = trailingslashit( wp_upload_dir()['basedir'] );
    $file = PLUGIN_PATH . 'media-access/.htaccess';
    copy($file, trailingslashit( $path . '.htaccess' ));
}

function privatewebsite_remove_media_restriction_file() {
    $path = trailingslashit( wp_upload_dir()['basedir'] );
    if (file_exists($path . '.htaccess')) {
        unlink(trailingslashit( $path . '.htaccess' ));
    }
}