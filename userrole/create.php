<?php

namespace CityOfHelsinki\WordPress\PrivateWebsite;

function privatewebsite_check_for_userrole() {
    return wp_roles()->is_role('helsinkiprivateuser');
}

function privatewebsite_create_userrole() {
    add_role('helsinkiprivateuser', 'Helsinki Private User', array());
}