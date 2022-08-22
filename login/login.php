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
	return PLUGIN_PATH . 'login/index.php';
}

add_filter('lostpassword_redirect', __NAMESPACE__ . '\\helsinki_privatewebsite_lostpassword_redirect');
function helsinki_privatewebsite_lostpassword_redirect($redirect) {
	return trailingslashit( home_url() ) . '?checkemail=confirm';
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

add_action( 'helsinki_login_head', 'wp_enqueue_scripts', 1 );
add_action( 'helsinki_login_head', 'wp_resource_hints', 2 );
add_action( 'helsinki_login_head', 'locale_stylesheet' );
add_action( 'helsinki_login_head', 'wp_print_styles', 7 );
add_action( 'helsinki_login_head', 'wp_print_head_scripts', 8 );
add_action( 'helsinki_login_head', 'wp_site_icon', 99 );

//add_action( 'helsinki_login_header', __NAMESPACE__ . '\\helsinki_login_site_title' );

//add_action('helsinki_header_top', 'helsinki_header_skip', 5);
//add_action('helsinki_header_top', 'helsinki_topbar', 10);
//add_filter('body_class', 'helsinki_topbar_body_class', 10);

add_action('helsinki_login_header', 'helsinki_header_logo', 10);
//add_action('helsinki_header', 'helsinki_header_mobile_panel_toggle', 50);
//add_action('helsinki_header_bottom', 'helsinki_header_main_menu', 20);
//add_action('helsinki_header_bottom', 'helsinki_header_mobile_panel', 30);

//add_action('helsinki_header_mobile_panel', 'helsinki_header_mobile_menu', 20);
//add_action('helsinki_header_mobile_panel', 'helsinki_header_mobile_links', 30);

//if ( apply_filters( 'helsinki_header_languages_enabled', false ) ) {
add_action( 'helsinki_login_header', 'helsinki_header_languages', 20 );
//add_action('helsinki_header_mobile_panel', 'helsinki_header_languages', 10);
//}

add_action( 'helsinki_login_main', __NAMESPACE__ . '\\helsinki_login_notification', 10);
add_action( 'helsinki_login_main', __NAMESPACE__ . '\\helsinki_login_content', 10 );
add_action( 'helsinki_login_footer_top', __NAMESPACE__ . '\\helsinki_login_koros' );
add_action( 'helsinki_login_footer', 'helsinki_footer_logo' );
add_action( 'helsinki_login_footer', 'helsinki_footer_copyright' );

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

	$parts[] = '<h1>Login</h1>';

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
	_('Username or e-mail', 'helsinki-privatewebsite'),
	_('Password', 'helsinki-privatewebsite'),
	_('Remember me', 'helsinki-privatewebsite'),
	_('Login', 'helsinki-privatewebsite'),
	$query,
	_('Lost your password?', 'helsinki-privatewebsite')
	);

	$privatewebsite_settings = get_option('helsinki-privatewebsite-settings', array());

	$parts[] = sprintf(
		'<div>
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

	//$parts[] = helsinki_login_button();

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
		'<div">
			<section aria-label="%s" class="hds-notification hds-notification--success">
			<div class="hds-notification__content">			
				<div class="hds-notification__label" role="heading" aria-level="2">
					<span class="hds-icon hds-icon--check-circle-fill" aria-hidden="true"></span>
					<span>%s</span>
				</div>
				<div class="hds-notification__body">%s</div>
			</div>
			</section>
		</div>',
		_('Notification', 'helsinki-privatewebsite'),
		_('Password change requested', 'helsinki-privatewebsite'),
		_('We have sent e-mail instructions for changing the password.')
		);
	}
}

function helsinki_login_button() {
	return sprintf(
		'<a class="button hds-button" href="%s">%s</a>',
		'https://www.hel.fi',
		esc_html__( 'Go to hel.fi', 'helsinki-universal' )
	);
}

function helsinki_login_image() {
	return sprintf(
		'<img class="decoration" alt="" src="%s" width="823" height="1168">',
		trailingslashit( PLUGIN_URL ) . 'assets/images/login.png'
	);
}

function helsinki_login_koros( $data ) {
	echo helsinki_koros( 'login' );
}
