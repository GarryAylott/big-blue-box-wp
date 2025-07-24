<?php
/**
 * Captivate ACF integration.
 *
 * @package Big_Blue_Box
 */

 add_action( 'admin_menu', function() {
	add_menu_page(
		'Captivate API Settings',
		'Captivate API',
		'manage_options',
		'captivate-api-settings',
		'bbb_render_captivate_api_settings_page',
		'dashicons-admin-generic',
		80
	);
});

add_action( 'admin_init', function() {
	register_setting( 'bbb_captivate_api_group', 'disable_captivate_api' );
});

function bbb_render_captivate_api_settings_page() {
	?>
	<div class="wrap">
		<h1>Captivate API Master On/Off</h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'bbb_captivate_api_group' );
			do_settings_sections( 'bbb_captivate_api_group' );
			$disabled = get_option( 'disable_captivate_api' );
			?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Disable Captivate API</th>
					<td>
						<input type="checkbox" name="disable_captivate_api" value="1" <?php checked( 1, $disabled ); ?> />
						<p class="description">Temporarily disables all calls to the Captivate API. Useful during local development.</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
