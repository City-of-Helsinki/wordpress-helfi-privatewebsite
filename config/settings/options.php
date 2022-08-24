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
                    'name' => __('Login page', 'helsinki-privatewebsite'),
                    'type' => 'checkbox',
                    'description' => __('Forces users to login to view website content.', 'helsinki-privatewebsite'),
                    'default' => 'on'
                ),
                array(
                    'id' => 'protect-media-files',
                    'name' => __('Protect media files', 'helsinki-privatewebsite'),
                    'type' => 'checkbox',
                    'description' => __('Direct access to media files will be blocked if the user is not logged in.', 'helsinki-privatewebsite'),
                    'default' => 'on'
                ),
            ),
        ),
	),
	'login-page' => array(
        array(
            'id' => 'privatewebsite-login-page',
            'name' => __('Info Section Heading & Content', 'helsinki-privatewebsite'),
            'page' => 'helsinki-privatewebsite-settings',
            'description' => __('Include additional information on the login page.', 'helsinki-privatewebsite'),
            'options' => array(
                array(
                    'id' => 'custom-content-heading',
                    'name' => __('Info Heading', 'helsinki-privatewebsite'),
                    'type' => 'text'
                ),
                array(
                    'id' => 'custom-content-content',
                    'name' => __('Info Content', 'helsinki-privatewebsite'),
                    'type' => 'textarea'
                ),
            ),
        ),
        array(
            'id' => 'privatewebsite-login-page-link1',
            'name' => 'Link 1',
            'page' => 'helsinki-privatewebsite-settings',
            'options' => array(
                array(
                    'id' => 'custom-content-link1-text',
                    'name' => 'Link 1 Text',
                    'type' => 'text'
                ),
                array(
                    'id' => 'custom-content-link1-url',
                    'name' => 'Link 1 Url',
                    'type' => 'text'
                ),
            ),
        ),
        array(
            'id' => 'privatewebsite-login-page-link2',
            'name' => 'Link 2',
            'page' => 'helsinki-privatewebsite-settings',
            'options' => array(
                array(
                    'id' => 'custom-content-link2-text',
                    'name' => 'Link 1 Text',
                    'type' => 'text'
                ),
                array(
                    'id' => 'custom-content-link2-url',
                    'name' => 'Link 1 Url',
                    'type' => 'text'
                ),
            ),
        ),

	),
);
