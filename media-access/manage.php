<?php

namespace CityOfHelsinki\WordPress\PrivateWebsite;

add_action('helsinki_privatewebsite_init', __NAMESPACE__ . '\\privatewebsite_check_media_restriction_file');
add_action('do_feed', __NAMESPACE__ . '\\privatewebsite_disable_rss_feeds', 1);
add_action('do_feed_rdf', __NAMESPACE__ . '\\privatewebsite_disable_rss_feeds', 1);
add_action('do_feed_rss', __NAMESPACE__ . '\\privatewebsite_disable_rss_feeds', 1);
add_action('do_feed_rss2', __NAMESPACE__ . '\\privatewebsite_disable_rss_feeds', 1);
add_action('do_feed_atom', __NAMESPACE__ . '\\privatewebsite_disable_rss_feeds', 1);
add_action('do_feed_rss2_comments', __NAMESPACE__ . '\\privatewebsite_disable_rss_feeds', 1);
add_action('do_feed_atom_comments', __NAMESPACE__ . '\\privatewebsite_disable_rss_feeds', 1);

function privatewebsite_upload_dir_path() {
    return wp_upload_dir()['basedir'];
}

function privatewebsite_check_media_restriction_file() {    
    $path = trailingslashit( privatewebsite_upload_dir_path() );


    $restriction_enabled = '';
    $settings = get_option('helsinki-privatewebsite-settings', array());
    if (isset($settings['login-page-enabled'])) {
        $restriction_enabled = $settings['login-page-enabled'];
    }

    if (isset($restriction_enabled) && $restriction_enabled === 'on') {
        if (!file_exists($path . '.htaccess') || !file_exists($path . 'dl-file.php')) {
            privatewebsite_create_media_restriction_files();
        }
    }
    else {
        if (file_exists($path . '.htaccess') || file_exists($path . 'dl-file.php')) {
            privatewebsite_remove_media_restriction_files();
        }
    }
}

function privatewebsite_create_media_restriction_files() {
    $path = trailingslashit( privatewebsite_upload_dir_path() );
    $htAccessfile = PLUGIN_PATH . 'media-access/.htaccess';
    $dlFile = PLUGIN_PATH . 'media-access/dl-file.php';
    copy($htAccessfile, $path . '.htaccess' );
    copy($dlFile, $path . 'dl-file.php' );
}

function privatewebsite_remove_media_restriction_files() {
    $path = trailingslashit( privatewebsite_upload_dir_path() );
    if (file_exists($path . '.htaccess')) {
        unlink($path . '.htaccess' );
        unlink($path . 'dl-file.php' );
    }
}

function privatewebsite_disable_rss_feeds() {
    $settings = get_option('helsinki-privatewebsite-settings', array());
    if (isset($settings['login-page-enabled'])) {
        $login_page_enabled = $settings['login-page-enabled'];
    }

    if (isset($login_page_enabled) && $login_page_enabled === 'on') {
        wp_die( __('No feed available, please visit our <a href="'. get_bloginfo('url') .'">homepage</a>!') );
    }
}
