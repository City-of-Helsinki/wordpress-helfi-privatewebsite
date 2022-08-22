<?php

namespace CityOfHelsinki\WordPress\PrivateWebsite;

$data = helsinki_privatewebsite_login_page_data();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php esc_attr( bloginfo( 'charset' ) ); ?>" />
		<?php //mtnc_get_page_title(); ?>
		<meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, minimum-scale=1">
		<meta name="description" content="<?php echo esc_attr( $data['site_description'] ); ?>"/>
		<meta http-equiv="X-UA-Compatible" content="" />
		<meta property="og:site_name" content="<?php printf( esc_attr( '%s - %s' ), $data['site_title'], $data['site_description'] ); ?>"/>
		<meta property="og:title" content="<?php echo _('Login', 'helsinki-privatewebsite') . ' | ' . $data['site_title']; ?>"/>
		<meta property="og:type" content="Login"/>
		<meta property="og:url" content="<?php echo esc_url( $data['site_url'] ); ?>"/>
		<meta property="og:description" content="<?php //echo esc_attr( $data['page_description'] ); ?>"/>
		<?php if ( ! empty( $data['logo'] ) ) : ?>
			<meta property="og:image" content="<?php echo esc_url( $data['logo'] ); ?>" />
			<meta property="og:image:url" content="<?php echo esc_url( $data['logo'] ); ?>"/>
			<meta property="og:image:secure_url" content="<?php echo esc_url( $data['logo'] ); ?>"/>
			<meta property="og:image:type" content="<?php echo esc_attr( $data['logo_ext'] ); ?>"/>
		<?php endif; ?>

		<?php do_action( 'helsinki_login_head' ); ?>
	</head>

	<body class="login">

		<?php do_action( 'helsinki_login_top', $data ); ?>

		<header id="masthead" class="<?php helsinki_header_classes(); ?>" role="banner">
			<?php do_action( 'helsinki_login_header_top', $data ); ?>

			<div class="hds-container navigation__content hds-container--wide flex-container">
			  <?php do_action( 'helsinki_login_header', $data ); ?>
			</div>

			<?php do_action( 'helsinki_login_header_bottom', $data ); ?>
		</header>

		<main id="main" role="main">
			<?php do_action( 'helsinki_login_main_top', $data ); ?>

		    <div class="content">

				<div class="hds-container content__container">

					<div class="content__main">
						<?php do_action( 'helsinki_login_main', $data ); ?>
					</div>

				</div>
		    </div>

			<?php do_action( 'helsinki_login_main_bottom', $data ); ?>
		</main>

		<footer id="footer" class="<?php helsinki_footer_classes(); ?>" role="contentinfo">
			<?php do_action( 'helsinki_login_footer_top', $data ); ?>

		    <section class="footer__bottom hds-container">
				<?php do_action( 'helsinki_login_footer', $data ); ?>
		    </section>

			<?php do_action( 'helsinki_login_footer_bottom', $data ); ?>
		</footer>

  		<?php do_action( 'helsinki_login_bottom', $data ); ?>

	</body>
</html>
