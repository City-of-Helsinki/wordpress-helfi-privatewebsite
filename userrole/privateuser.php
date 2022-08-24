<?php

namespace CityOfHelsinki\WordPress\PrivateWebsite;

function privatewebsite_check_for_userrole() {
    return wp_roles()->is_role('helsinkiprivateuser');
}

function privatewebsite_create_userrole() {
    add_role('helsinkiprivateuser', 'Helsinki Private User', array());
}

add_filter('show_admin_bar', __NAMESPACE__ . '\\privatewebsite_hide_adminbar_for_userrole');
function privatewebsite_hide_adminbar_for_userrole($roles) {
    $hideForRoles = ['helsinkiprivateuser'];
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