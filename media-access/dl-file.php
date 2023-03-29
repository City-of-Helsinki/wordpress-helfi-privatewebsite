<?php
/*
 * dl-file.php
 *
 * Protect uploaded files with login.
 *
 * @link http://wordpress.stackexchange.com/questions/37144/protect-wordpress-uploads-if-user-is-not-logged-in
 * @link https://gist.github.com/hakre/1552239
 *
 * @author hakre <http://hakre.wordpress.com/>
 * @license GPL-3.0+
 * @registry SPDX
 *
 * Includes fixes proposed here:
 * https://gist.github.com/hakre/1552239#gistcomment-1439472
 *
 * And here:
 * https://gist.github.com/hakre/1552239#gistcomment-1851131
 * https://gist.github.com/austinginder/927cbc11ca394e713430e41c2dd4a27d
 *
 * And here:
 * https://gist.github.com/hakre/1552239#gistcomment-2735755
 *
 * .htaccess similar to this one:
 * https://gist.github.com/hakre/1552239#gistcomment-1313010
 *
 */

//Fix to allow for large files without exhausting PHP memory:
//https://gist.github.com/hakre/1552239#gistcomment-1851131
//https://gist.github.com/austinginder/927cbc11ca394e713430e41c2dd4a27d
if (ob_get_level()) {
  ob_end_clean();
}

//Fix proposed here
//https://gist.github.com/hakre/1552239#gistcomment-1439472
ob_start();
$wp_load_path = preg_replace( '/wp-content(?!.*wp-content).*/', '', __DIR__ );
if ( !file_exists($wp_load_path . 'wp-load.php' )) {
  $wp_load_path = preg_replace( '/content(?!.*content).*/', 'wp/', __DIR__ );
  if ( !file_exists($wp_load_path . 'wp-load.php') ) {
    return false;
  }
}
require_once($wp_load_path . 'wp-load.php');
require_once ABSPATH . WPINC . '/formatting.php';
require_once ABSPATH . WPINC . '/capabilities.php';
require_once ABSPATH . WPINC . '/user.php';
require_once ABSPATH . WPINC . '/meta.php';
require_once ABSPATH . WPINC . '/post.php';
require_once ABSPATH . WPINC . '/pluggable.php';
wp_cookie_constants();
ob_get_clean();
ob_end_flush();

error_log("Before doing checks");
//Added is_user_member_of_blog as the multi-site fix recomended it
is_user_member_of_blog() && is_user_logged_in() || auth_redirect();
error_log("User is authenticated");

//Allowed users and roles to access protected folders.
//The main key always represent a subfolder of wp-content/uploads. Please note
//that the key: 'default' represent the folder: wp-content/uploads.
//You can set here the allowed users and roles for each folder. Please note that
//the 'administrator' role will be always allowed to access all the files
//Please also note that at this point, all users are already authenticated because
//they passed the cointraint:
//is_user_member_of_blog() && is_user_logged_in() || auth_redirect();
//So, if you want to say that all roles are allowed to access the website, then
//add the 'all' role.
$folder_permissions['default']['roles'] = array('all');
//$folder_permissions['ultimatemember']['users'] = array('my_user');
$folder_permissions['ultimatemember']['roles'] = array('subscriber', 'editor');
error_log("Folder permissions: " . var_export($folder_permissions, 1));

list($basedir) = array_values(
                   array_intersect_key(wp_upload_dir(), array('basedir' => 1))
                 ) + array(NULL);

//Fix for Multisite:
//https://gist.github.com/hakre/1552239#gistcomment-2735755
// Get the last occurence of the /sites/ part of the url
$sitepos = strrpos($basedir, '/sites/');
// Make sure the /sites/{int} is there
if ($sitepos !== false) {
  // Remove the /sites/{int}
  $basedir = preg_replace( '~\/sites\/\d+$~', '', $basedir );
  error_log("Multisite detected");
}
$basedir = realpath($basedir);
error_log("basedir: " . $basedir);

$file =  isset( $_GET['file'] ) ? $_GET['file'] : '';
error_log("file: " . $file);
$file_root_folder = substr($file, 0, strpos($file, '/'));
if (!array_key_exists($file_root_folder, $folder_permissions)) {
  //This means it is either the uploads folder or any other subfolder not listed
  //here, the default will be assumed
  $file_root_folder = 'default';
}
error_log("file_root_folder: " . $file_root_folder);

$folder_permission = $folder_permissions[$file_root_folder];
$folder_users = array_key_exists('users', $folder_permission) ?
                $folder_permission['users'] : array();
$folder_roles = array_key_exists('roles', $folder_permission) ?
                $folder_permission['roles'] : array();

error_log("folder_permission: " . var_export($folder_permission, 1));
error_log("folder_users: " . var_export($folder_users, 1));
error_log("folder_roles: " . var_export($folder_roles, 1));

$auth_user = wp_get_current_user();
$auth_user_login = $auth_user->user_login;
$auth_user_role = $auth_user->roles[0];

error_log("auth_user_login: " . $auth_user_login);
error_log("auth_user_role: " . $auth_user_role);

$is_user_allowed = in_array($auth_user_login, $folder_users);
$is_role_allowed = ($auth_user_role == 'administrator') ||
                   in_array($auth_user_role, $folder_roles) ||
                   in_array('all', $folder_roles);

error_log("is_user_allowed: " . var_export($is_user_allowed, 1));
error_log("is_role_allowed: " . var_export($is_role_allowed, 1));

if (!$is_user_allowed && !$is_role_allowed) {
  error_log("Issue 403, user doesn't have any permission");
  status_header(403);
  die('403 &#8212; Access denied.');
}

$file = rtrim($basedir, '/') . '/' .
        (isset( $_GET['file'] ) ? $_GET['file'] : '');
$file = realpath($file);
error_log("real path: " . $file);

if ($file === FALSE || !$basedir || !is_file($file)) {
  error_log("Issue 404");
  status_header(404);
  die('404 &#8212; File not found.');
}

if (strpos($file, $basedir) !== 0) {
  error_log("Issue 403");
  status_header(403);
  die('403 &#8212; Access denied.');
}

$mime = wp_check_filetype($file);
if(false === $mime[ 'type' ] && function_exists('mime_content_type'))
  $mime[ 'type' ] = mime_content_type( $file );

if ($mime['type'])
  $mimetype = $mime['type'];
else
  $mimetype = 'image/' . substr($file, strrpos($file, '.') + 1);

header( 'Content-Type: ' . $mimetype ); // always send this
if (false === strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS'))
  header('Content-Length: ' . filesize($file));

$last_modified = gmdate( 'D, d M Y H:i:s', filemtime($file));
$etag = '"' . md5($last_modified) . '"';
header("Last-Modified: $last_modified GMT");
header('ETag: ' . $etag);
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 100000000) . ' GMT');

// Support for Conditional GET
$client_etag = isset($_SERVER['HTTP_IF_NONE_MATCH'])?
                 stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) : false;

if (! isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
  $_SERVER['HTTP_IF_MODIFIED_SINCE'] = false;

$client_last_modified = trim( $_SERVER['HTTP_IF_MODIFIED_SINCE'] );
// If string is empty, return 0. If not, attempt to parse into a timestamp
$client_modified_timestamp = $client_last_modified?
                               strtotime($client_last_modified) : 0;

// Make a timestamp for our most recent modification...
$modified_timestamp = strtotime($last_modified);

if (($client_last_modified && $client_etag)?
       (($client_modified_timestamp >= $modified_timestamp) && ($client_etag == $etag))
     : (( $client_modified_timestamp >= $modified_timestamp) || ($client_etag == $etag))
   ) {
  error_log("Issue 304, got it from cache");
  status_header( 304 );
  exit;
}

error_log("Success, read file");
// If we made it this far, just serve the file
readfile( $file );