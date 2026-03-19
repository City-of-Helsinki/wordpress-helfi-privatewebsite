<?php

namespace CityOfHelsinki\WordPress\PrivateWebsite;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function private_user_role_name(): string {
	return 'helsinkiprivateuser';
}

function privatewebsite_check_for_userrole() {
    return wp_roles()->is_role( private_user_role_name() );
}

function privatewebsite_create_userrole() {
    add_role( private_user_role_name() , 'Helsinki Private User', array());
}

add_filter('show_admin_bar', __NAMESPACE__ . '\\privatewebsite_hide_adminbar_for_userrole');
function privatewebsite_hide_adminbar_for_userrole($roles) {
    $hideForRoles = [ private_user_role_name() ];
    if ( is_user_logged_in() ) {
        $user = wp_get_current_user();
        $currentUserRoles = $user->roles;
        $isMatching = array_intersect( $currentUserRoles, $hideForRoles);
        if ( !empty($isMatching) ) :
            return false;
        endif;
        return true;
    };
}
