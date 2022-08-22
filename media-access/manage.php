<?php

namespace CityOfHelsinki\WordPress\PrivateWebsite;

function privatewebsite_create_media_restriction_file() {
    $path = trailingslashit( wp_upload_dir()['basedir'] );
    $file = trailingslashit(PLUGIN_PATH . '/media-access/.htaccess');
    copy($file, trailingslashit( $path . $file ));
}

function privatewebsite_remove_media_restriction_file() {
    $path = trailingslashit( wp_upload_dir()['basedir'] );
    $file = trailingslashit(PLUGIN_PATH . '/media-access/.htaccess');
    unlink(trailingslashit( $path . $file ));
}