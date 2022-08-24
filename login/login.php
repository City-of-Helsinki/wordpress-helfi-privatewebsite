<?php

namespace CityOfHelsinki\WordPress\PrivateWebsite;

add_action( 'template_include', __NAMESPACE__ . '\\helsinki_privatewebsite_login_template', 999998 );
function helsinki_privatewebsite_login_template( $template ) {
	$settings = get_option('helsinki-privatewebsite-settings', array());
	$login_enabled = isset($settings['login-page-enabled']) ? $settings['login-page-enabled'] : 'on';
	if ( is_user_logged_in() || $login_enabled !== 'on' ) {
		return $template;
	}
	if (!is_home() && !is_front_page()) {
		nocache_headers();
		$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
		$forwarding_url = urlencode($base_url . $_SERVER["REQUEST_URI"]);
		wp_redirect( trailingslashit( home_url() ) . '?query=' . $forwarding_url , '302' );
		exit;
	}
	helsinki_login_hooks();
	return PLUGIN_PATH . 'login/index.php';
}

add_filter('lostpassword_redirect', __NAMESPACE__ . '\\helsinki_privatewebsite_lostpassword_redirect');
function helsinki_privatewebsite_lostpassword_redirect($redirect) {
	return trailingslashit( home_url() ) . '?checkemail=confirm';
}

add_action('after_password_reset', __NAMESPACE__ . '\\helsinki_privatewebsite_after_password_reset');
function helsinki_privatewebsite_after_password_reset() {
	wp_redirect( trailingslashit( home_url() )  . '?action=resetpass');
}

function helsinki_privatewebsite_login_page_data() {
	$logo = helsinki_get_svg_logo();

	$site_title       = get_bloginfo( 'title' );
	$site_description = get_bloginfo( 'description' );

	return array(
		'site_url' => site_url(),
		'site_title' => $site_title,
		'site_description' => $site_description,
		'logo' => $logo,
		'logo_ext' => $logo ? pathinfo( $logo, PATHINFO_EXTENSION ) : '',
	);
}

add_action('wp_login_failed', __NAMESPACE__ . '\\helsinki_privatewebsite_login_failed', 10, 2);
function helsinki_privatewebsite_login_failed( $username, $error ) {
	$referrer = $_SERVER['HTTP_REFERER'];
	if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
		$url = parse_url($referrer);
		$args = array();
		parse_str($url['query'], $args);
		var_dump($args);
		exit;
		wp_redirect( $referrer . '?login=failed' );
		exit;
	}
}

add_filter( 'authenticate', __NAMESPACE__ . '\\helsinki_privatewebsite_authenticate_username_password', 30, 3);
function helsinki_privatewebsite_authenticate_username_password( $user, $username, $password )
{
    if ( is_a($user, 'WP_User') ) { return $user; }

    if ( empty($username) || empty($password) )
    {
        $error = new \WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
        return $error;
    }
}

add_action('helsinki_header_top', __NAMESPACE__ . '\\helsinki_privatewebsite_logout_bar', 9);

//add_action('after_theme_setup', __NAMESPACE__ . '\\helsinki_login_hooks');


function helsinki_login_hooks() {
	$scheme = helsinki_login_get_scheme();
	add_filter('helsinki_login_body_class', 'helsinki_scheme_body_class', 10);
	if ( helsinki_scheme_has_invert_color() ) {
		add_filter('helsinki_login_body_class', 'helsinki_scheme_invert_color_body_class', 10);
	}
	add_action( 'helsinki_login_head', 'wp_enqueue_scripts', 1 );
	add_action( 'helsinki_login_head', 'wp_resource_hints', 2 );
	add_action( 'helsinki_login_head', 'locale_stylesheet' );
	add_action('helsinki_login_head', function() use ($scheme) { helsinki_scheme_root_styles($scheme); }, 6 );
	add_action( 'helsinki_login_head', 'wp_print_styles', 7 );
	add_action( 'helsinki_login_head', 'wp_print_head_scripts', 8 );

	add_action( 'helsinki_login_head', 'wp_site_icon', 99 );
	add_action('helsinki_login_header', 'helsinki_header_logo', 10);
	add_action( 'helsinki_login_header', 'helsinki_header_languages', 20 );
	add_action('helsinki_login_mobile_header', 'helsinki_header_languages', 10);
	add_action( 'helsinki_login_main', __NAMESPACE__ . '\\helsinki_login_notification', 10);
	add_action( 'helsinki_login_main', __NAMESPACE__ . '\\helsinki_login_content', 10 );
	add_action( 'helsinki_login_footer_top', __NAMESPACE__ . '\\helsinki_login_koros' );
	add_action( 'helsinki_login_footer', 'helsinki_footer_logo' );
	add_action( 'helsinki_login_footer', 'helsinki_footer_copyright' );
	add_action( 'helsinki_login_bottom', 'wp_print_footer_scripts' );
}

function helsinki_login_site_title( $data ) {
	printf(
		'<div class="site-title">%s</div>',
		sprintf(
			'<span>%s</span>',
			esc_attr( $data['site_title'] )
		)
	);
}

function helsinki_login_content( $data ) {
	$parts = array();

	$query = '';
	if (isset($_GET['query'])) {
		$query = esc_attr( urldecode($_GET['query']));
	}

	$parts[] = '<h1>' . __('Login', 'helsinki-privatewebsite') . '</h1>';

	if ( $data['site_description'] ) {
		$parts[] = wp_kses_post( wpautop( $data['site_description'] ) );
	}
    
    $parts[] = sprintf('
	<form name="loginform" id="loginform" action="/wp-login.php" method="post">
		<div class="hds-text-input login-username">
			<label class="hds-text-input__label" for="user_login">%s</label>
			<input type="text" name="log" id="user_login" autocomplete="username" class="input hds-text-input__input" value="" size="20">
		</div>
		<div class="hds-text-input login-password">
			<label class="hds-text-input__label" for="user_pass">%s</label>
			<input type="password" name="pwd" id="user_pass" autocomplete="current-password" class="input hds-text-input__input" value="" size="20">
		</div>
		<div class="hds-checkbox login-remember">
			<input name="rememberme" type="checkbox" id="rememberme" value="forever" class="hds-checkbox__input">
			<label for="rememberme" class="hds-checkbox__label">%s</label>
		</div>
		<div class="login-submit">
			<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="%s">
			<input type="hidden" name="redirect_to" value="%s">
		</div>
		<div>
			<a href="/wp-login.php?action=lostpassword">%s</a>
		</div>
	</form>',
	__('Username or e-mail', 'helsinki-privatewebsite'),
	__('Password', 'helsinki-privatewebsite'),
	__('Remember me', 'helsinki-privatewebsite'),
	__('Login', 'helsinki-privatewebsite'),
	isset($query) ? $query : esc_attr(home_url()),
	__('Lost your password?', 'helsinki-privatewebsite')
	);

	$privatewebsite_settings = get_option('helsinki-privatewebsite-settings', array());

	if (function_exists('pll__')) {
		if (isset($privatewebsite_settings['custom-content-heading'])) {
			$privatewebsite_settings['custom-content-heading'] = pll__($privatewebsite_settings['custom-content-heading']);
		}
		if (isset($privatewebsite_settings['custom-content-content'])) {
			$privatewebsite_settings['custom-content-content'] = pll__($privatewebsite_settings['custom-content-content']);
		}
		if (isset($privatewebsite_settings['custom-content-link1-text'])) {
			$privatewebsite_settings['custom-content-link1-text'] = pll__($privatewebsite_settings['custom-content-link1-text']);
		}
		if (isset($privatewebsite_settings['custom-content-link1-url'])) {
			$privatewebsite_settings['custom-content-link1-url'] = pll__($privatewebsite_settings['custom-content-link1-url']);
		}
		if (isset($privatewebsite_settings['custom-content-link2-text'])) {
			$privatewebsite_settings['custom-content-link2-text'] = pll__($privatewebsite_settings['custom-content-link2-text']);
		}
		if (isset($privatewebsite_settings['custom-content-link2-url'])) {
			$privatewebsite_settings['custom-content-link2-url'] = pll__($privatewebsite_settings['custom-content-link2-url']);
		}
	}

	$parts[] = sprintf(
		'<div class="login-additional-info">
			%s
			%s
			%s
			%s
		</div>',
		isset($privatewebsite_settings['custom-content-heading']) ? '<h2>' . $privatewebsite_settings['custom-content-heading'] . '</h2>' : '',
		isset($privatewebsite_settings['custom-content-content']) ? '<p>' . $privatewebsite_settings['custom-content-content'] . '</p>' : '',
		isset($privatewebsite_settings['custom-content-link1-text']) && isset($privatewebsite_settings['custom-content-link1-url']) ? '<a href="'. $privatewebsite_settings['custom-content-link1-url'] .'">' . $privatewebsite_settings['custom-content-link1-text'] . '</a>' : '',
		isset($privatewebsite_settings['custom-content-link2-text']) && isset($privatewebsite_settings['custom-content-link2-url']) ? '<a href="'. $privatewebsite_settings['custom-content-link2-url'] .'">' . $privatewebsite_settings['custom-content-link2-text'] . '</a>' : ''
	);


	printf(
		'<div class="grid m-up-2">
			<div class="grid__column">%s</div>
			<div class="grid__column">%s</div>
		</div>',
		implode( '', $parts ),
		helsinki_login_image()
	);
}

function helsinki_login_notification() {
	if (isset($_GET['checkemail']) && $_GET['checkemail'] === 'confirm') {
		printf(
			helsinki_login_notification_template(),
			privatewebsite_random_string(),
			__('Notification', 'helsinki-privatewebsite'),
			esc_attr('success'),
			helsinki_get_svg_icon('check-circle-fill'),
			__('Password change requested', 'helsinki-privatewebsite'),
			__('We have sent e-mail instructions for changing the password.', 'helsinki-privatewebsite'),
			__( 'Close notification', 'helsinki-privatewebsite' ),
			helsinki_get_svg_icon('cross')
		);
	}
	if (isset($_GET['action']) && $_GET['action'] === 'resetpass') {
		printf(
			helsinki_login_notification_template(),
			privatewebsite_random_string(),
			__('Notification', 'helsinki-privatewebsite'),
			esc_attr('success'),
			helsinki_get_svg_icon('check-circle-fill'),
			__('Password has been changed', 'helsinki-privatewebsite'),
			__('Please login with your username and your new password.', 'helsinki-privatewebsite'),
			__( 'Close notification', 'helsinki-privatewebsite' ),
			helsinki_get_svg_icon('cross')
		);	
	}
	if (isset($_GET['login']) && $_GET['login'] === 'failed') {
		printf(
			helsinki_login_notification_template(),
			privatewebsite_random_string(),
			__('Notification', 'helsinki-privatewebsite'),
			esc_attr('error'),
			helsinki_get_svg_icon('error'),
			__('Error logging in', 'helsinki-privatewebsite'),
			__('Incorrect username, e-mail or password.', 'helsinki-privatewebsite'),
			__( 'Close notification', 'helsinki-privatewebsite' ),
			helsinki_get_svg_icon('cross')
		);	
	}
}

function helsinki_login_notification_template() {
	return 
	'<div class="notifications">
		<section id="%s" aria-label="%s" class="notification hds-notification hds-notification--%s">
		<div class="hds-notification__content">			
			<div class="hds-notification__label" role="heading" aria-level="2">
				<span class="hds-icon" aria-hidden="true">%s</span>
				<span class="label-inner">%s</span>
			</div>
			<div class="hds-notification__body">%s</div>
		</div>
		<button class="button-reset close hds-notification__close-button" type="button">
			<span class="screen-reader-text">
				%s
			</span>
			%s
		</button>
		</section>
	</div>';
}

function helsinki_login_image() {
	return sprintf(
		'<img class="decoration" alt="" src="%s" width="823" height="1168">',
		trailingslashit( PLUGIN_URL ) . 'assets/images/login.png'
	);
}

function helsinki_login_get_scheme() {
	$current_scheme = '';
	if (function_exists('helsinki_theme_mod')) {
		$current_scheme = helsinki_theme_mod('helsinki_general_style', 'scheme');
	}
	return $current_scheme;
}

function helsinki_login_koros( $data ) {
	echo helsinki_koros( 'login' );
}

function helsinki_privatewebsite_logout_bar() {
	printf(
	'<div id="logout_bar">
		<div class="hds-container hds-container--wide flex-container flex-container--align-center">
			<div class="logout__link">
				<a href="%s">%s</a></div>
		</div>
	</div>',
	wp_logout_url(),
	__('Logout', 'helsinki-privatewebsite')
	);
}
