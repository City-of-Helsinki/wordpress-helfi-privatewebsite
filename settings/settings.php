<?php

namespace CityOfHelsinki\WordPress\PrivateWebsite;

define(__NAMESPACE__ . '\\PAGE_SLUG', 'helsinki-privatewebsite-settings');

add_action( 'admin_init', __NAMESPACE__ . '\\privatewebsite_register_settings');
add_action( 'admin_menu', __NAMESPACE__ . '\\privatewebsite_settings_page' );
add_action( 'helsinki_privatewebsite_settings_tab_panel', __NAMESPACE__ . '\\privatewebsite_renderTabPanel' );
add_action( 'helsinki_privatewebsite_init', __NAMESPACE__ . '\\privatewebsite_settings_defaults');
add_action( 'helsinki_privatewebsite_init', __NAMESPACE__ . '\\privatewebsite_register_polylang_strings');

$tabs = array();

function privatewebsite_settings_page() {
    add_menu_page(
        __('Helsinki Private Website', 'helsinki-privatewebsite'),
        __('Helsinki Private Website', 'helsinki-privatewebsite'),
        apply_filters(
            'helsinki_private_website_settings_page_capability_requirement',
            'manage_options'
        ),
        PAGE_SLUG,
        __NAMESPACE__ . '\\privatewebsite_settings_renderpage',
        'dashicons-admin-network',
        null
    );
}

function privatewebsite_settings_renderpage() {
    $tabs = include PLUGIN_PATH . 'config/settings/tabs.php';

    include_once PLUGIN_PATH . 'views/settings/page.php';
}

function privatewebsite_renderTabPanel( string $tab ) {
    $tabs = include PLUGIN_PATH . 'config/settings/tabs.php';
    if ( ! isset( $tabs[$tab] ) ) {
        return;
    }

    $settingsConfig = privatewebsite_settingsConfig( $tab );
    $page = $settingsConfig['page'];
    $section = $settingsConfig['section'];
    unset( $settingsConfig );

    include_once PLUGIN_PATH . 'views/settings/form.php';
}

function privatewebsite_settingsConfig( string $tab ) {
    return array(
        'page' => PAGE_SLUG,
        'section' => $tab,
    );
}

function privatewebsite_sanitizeSettings( $option ) {
    if ( is_array( $option ) ) {
        $out = array();
        foreach ($option as $key => $value) {
            $out[$key] = sanitize_text_field( $value );
        }
        return $out;
    } else {
        return sanitize_text_field( $option );
    }
}

function privatewebsite_settings_defaults() {
    $settings = get_option(PAGE_SLUG, array());
    if (empty($settings)) {
        $config = include PLUGIN_PATH . 'config/settings/options.php';
        $defaults = array();
        foreach ($config as $tab) {
            foreach($tab as $section) {
                foreach($section['options'] as $option) {
                    if (isset($option['default'])) {
                        $defaults[$option['id']] = $option['default'];
                    }
                }
            }
        }
        add_option(PAGE_SLUG, $defaults);
    } 
}

function privatewebsite_register_settings() {
    $settings = include PLUGIN_PATH . 'config/settings/options.php';
    register_setting( PAGE_SLUG, PAGE_SLUG, 
        array(
            'type' => 'array',
            'description' => '',
            'sanitize_callback' => __NAMESPACE__ . '\\privatewebsite_sanitizeSettings',
            'show_in_rest' => false,
            'default' => array(
                'enabled' => null,
            )
        )
    );
    foreach ($settings as $tab) {
        foreach($tab as $section) {
            privatewebsite_settings_add_section($section);
        }
    }
}

function privatewebsite_settings_add_section($section) {
    add_settings_section($section['id'], $section['name'], function() use ($section) { if (isset($section['description'])) { privatewebsite_settings_section_description($section['description']); } }, $section['page']);
    foreach ($section['options'] as $option) {
        privatewebsite_settings_add_option($option, $section['id'], $section['page']);
    }
}

function privatewebsite_settings_section_description($description) {
    printf(
        '<p>%s</p>',
        $description
    );
}

function privatewebsite_settings_add_option($option, $section, $page) {
    $option['page'] = $page;
    add_settings_field($option['id'], $option['name'], __NAMESPACE__ . '\\privatewebsite_settings_option_callback', $page, $section, $option);
}

function privatewebsite_settings_option_callback(array $args) {
    privatewebsite_settings_input($args);
}

function privatewebsite_settings_input(array $args) {
    $description = '';
    if (isset($args['description'])) {
        $description = sprintf(
            '<p class="description">%s</p>',
            $args['description']
        );
    }

    $option = '';
    $settings = get_option(PAGE_SLUG, array());
    if (isset($settings[$args['id']])) {
        $option = $settings[$args['id']];
    }
    /*if (empty($option) && isset($args['default'])) {
        $option = $args['default'];
    }*/
    $value = '';

    if (isset($option)) {
        if ($args['type'] === 'checkbox') {
            if ($option === 'on') {
                $value = 'checked';
            }
        }
        else if ($args['type'] === 'textarea') {
            $value = esc_attr($option);
        }
        else {
            $value = sprintf(
                'value="%s"',
                esc_attr($option)
            );
        }
    }

    if ($args['type'] === 'textarea') {
        printf(
            '<textarea class="large-text" name="%s[%s]" rows="4" id="%s">%s</textarea>%s',
            $args['page'],
            $args['id'],
            $args['id'],
            $value,
            $description
        );
    }
    else {
        printf(
            '<input class="regular-text" name="%s[%s]" type="%s" id="%s" %s>%s',
            $args['page'],
            $args['id'],
            $args['type'],
            $args['id'],
            $value,
            $description
        );
    }
}

function privatewebsite_register_polylang_strings() {
    if (function_exists('pll_register_string')) {
        $config = include PLUGIN_PATH . 'config/settings/options.php';
        $settings = get_option(PAGE_SLUG, array());

        foreach($config as $tab) {
            foreach($tab as $section) {
                foreach($section['options'] as $option) {
                    if ($option['type'] === 'text' || $option['type'] === 'textarea') {
                        if (isset($settings[$option['id']])) {
                            pll_register_string($option['id'], $settings[$option['id']], 'helsinki-privatewebsite', false);
                        }
                    }
                }
            }
        }
    }
}