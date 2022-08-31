<?php
return array(
	'general' => array(
        array(
            'id' => 'privatewebsite-general',
            'name' => __('General', 'helsinki-privatewebsite'),
            'page' => 'helsinki-privatewebsite-settings',
            'description' => __('Manage which plugin features are enabled.', 'helsinki-privatewebsite'),
            'options' => array(
                array(
                    'id' => 'login-page-enabled',
                    'name' => __('Protect website', 'helsinki-privatewebsite'),
                    'type' => 'checkbox',
                    'description' => __('Forces users to login to view website content.', 'helsinki-privatewebsite'),
                    'default' => 'on'
                ),
                array(
                    'id' => 'protect-media-files',
                    'name' => __('Protect media files', 'helsinki-privatewebsite'),
                    'type' => 'checkbox',
                    'description' => __('Direct access to media files will be blocked if the user is not logged in. Access will still be enabled for image files (png/jpg/gif).', 'helsinki-privatewebsite'),
                    'default' => 'on'
                ),
            ),
        ),
	),
	'login-page' => array(
        array(
            'id' => 'privatewebsite-login-page',
            'name' => __('Login page', 'helsinki-privatewebsite'),
            'page' => 'helsinki-privatewebsite-settings',
            'description' => __('Include additional information on the login page. Add possible translations from the String translations -page.', 'helsinki-privatewebsite'),
            'options' => array(
                array(
                    'id' => 'wp_login-page-content',
                    'name' => __('Text', 'helsinki-privatewebsite'),
                    'type' => 'editor'
                ),
            ),
        ),
	),
);
