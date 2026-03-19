<?php

declare(strict_types = 1);

namespace CityOfHelsinki\WordPress\PrivateWebsite\Integrations\WPHelfiSiteCore\TwoFactor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use function \CityOfHelsinki\WordPress\PrivateWebsite\private_user_role_name;
use WP_User;

\add_filter(
	'helsinki_site_core_force_enable_two_factor',
	__NAMESPACE__ . '\\disable_private_user_2fa',
	10, 2
);

function disable_private_user_2fa( bool $force_2fa, WP_User $user ): bool {
	return in_array( private_user_role_name(), (array) $user->roles )
		? false
		: $force_2fa;
}
