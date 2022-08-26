<?php

namespace CityOfHelsinki\WordPress\PrivateWebsite;

add_action('helsinki_privatewebsite_init', __NAMESPACE__ . '\\privatewebsite_check_media_restriction_file');

function privatewebsite_upload_dir_path() {
    if (is_multisite()) {
        return wp_upload_dir()['basedir'].'/sites/'.get_current_blog_id();
    }
    return wp_upload_dir()['basedir'];
}

function privatewebsite_check_media_restriction_file() {    
    $path = trailingslashit( privatewebsite_upload_dir_path() );


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
    $path = trailingslashit( privatewebsite_upload_dir_path() );
    $file = PLUGIN_PATH . 'media-access/.htaccess';
    copy($file, $path . '.htaccess' );
}

function privatewebsite_remove_media_restriction_file() {
    $path = trailingslashit( privatewebsite_upload_dir_path() );
    if (file_exists($path . '.htaccess')) {
        unlink($path . '.htaccess' );
    }
}