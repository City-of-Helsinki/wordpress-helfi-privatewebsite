<?php

declare(strict_types = 1);

namespace CityOfHelsinki\WordPress\PrivateWebsite\Integrations\WPHelfiCookieConsent;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use CityOfHelsinki\WordPress\PrivateWebsite\Integrations\WPHelfiCookieConsent\Cookies\Helsinki_Login_Redirect;

\add_filter( 'wordpress_helfi_cookie_consent_known_cookies', __NAMESPACE__ . '\\provide_cookies' );
function provide_cookies( array $cookies ): array {
	require_once \plugin_dir_path( __FILE__ ) . 'cookies/class-helsinki-login-redirect.php';

	return array_merge( $cookies, array(
		Helsinki_Login_Redirect::class,
	) );
}
