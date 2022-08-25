<form id="<?php echo esc_attr( $tab ); ?>-settings-form" class="settings__form" action="options.php" method="post">
	<?php
		settings_fields( $page );
		do_settings_sections( $page );
		submit_button(
			__( 'Save settings', 'helsinki-privatewebsite' ),
			'small',
			'',
			true,
			null
		);
	?>
</form>
