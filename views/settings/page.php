<?php
$page = $_GET['page'] ?? '';
$selected_tab = $_GET['tab'] ?? 'general';
$selected_panel = $tabs[$selected_tab] ?? array();
$admin_url = admin_url( 'admin.php' );
?>
<section id="helsinki-privatewebsite-settings" class="settings">
	<header class="settings__header">
		<h1 class="settings__title">
			<?php esc_html_e( 'Helsinki Private Website', 'helsinki-privatewebsite' ); ?>
		</h1>
	</header>

	<div class="settings__tabs nav-tab-wrapper">
		<?php
			foreach ($tabs as $key => $config) :
				$selected = $selected_tab === $key;
				$classes = array(
					'settings__tab',
					'nav-tab'
				);
				if ( $selected ) {
					$classes[] = 'nav-tab-active';
				}
				$url = add_query_arg(
					array(
						'page' => $page,
						'tab' => $key,
					),
					$admin_url
				);
			?>
			<a id="<?php echo esc_attr( $key ); ?>" class="<?php echo implode( ' ', $classes ); ?>" href="<?php echo esc_url( $url ); ?>">
				<?php echo esc_html( $config['title'] ); ?>
			</a>
		<?php endforeach; ?>
	</div>

	<div class="settings__panels">
		<?php if ( $selected_panel ) : ?>
			<div id="<?php echo esc_attr( $selected_tab ); ?>-tab" class="settings__panel">
				<?php

					if ( ! empty( $selected_panel['description'] ) ) {
						echo '<p class="description">' . esc_html( $selected_panel['description'] ) . '</p>';
					}

					do_action( 'helsinki_privatewebsite_settings_tab_panel', $selected_tab );
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
