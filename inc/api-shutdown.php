<?php
/**
 * Captivate API Tools (tabs: Shutdown + Sync)
 */
defined( 'ABSPATH' ) || exit;

// Pull in the Sync tool UI (safe if missing; we'll handle gracefully)
$sync_file = get_template_directory() . '/inc/sync-captivate-episodes.php';
if ( file_exists( $sync_file ) ) {
	require_once $sync_file;
}

/**
 * Register the option used by the Shutdown tool.
 */
add_action( 'admin_init', function () {
	// Register the boolean option (1/0) to toggle API calls globally.
	register_setting(
		'bbb_captivate_api_group',
		'disable_captivate_api',
		[
			'type'              => 'boolean',
			'sanitize_callback' => function ( $val ) {
				return (int) ( ! empty( $val ) );
			},
			'default'           => 0,
			'show_in_rest'      => false,
		]
	);
} );

/**
 * Add the parent admin page.
 */
add_action( 'admin_menu', function () {
	add_menu_page(
		'Captivate API Tools',
		'Captivate API Tools',
		'manage_options',
		'captivate-api-tools',
		'bbb_render_api_tools_page',
		'dashicons-admin-generic',
		80
	);
} );

/**
 * Render the parent page with tabs.
 */
if ( ! function_exists( 'bbb_render_api_tools_page' ) ) {
	function bbb_render_api_tools_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'bigbluebox' ) );
		}

		$tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'shutdown';
		?>
		<div class="wrap">
			<h1>Captivate API Tools</h1>

			<h2 class="nav-tab-wrapper">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=captivate-api-tools&tab=shutdown' ) ); ?>" class="nav-tab <?php echo ( $tab === 'shutdown' ? 'nav-tab-active' : '' ); ?>">
					API Shutdown
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=captivate-api-tools&tab=sync' ) ); ?>" class="nav-tab <?php echo ( $tab === 'sync' ? 'nav-tab-active' : '' ); ?>">
					Sync Episodes
				</a>
			</h2>

			<div style="margin-top:20px;">
				<?php
				if ( $tab === 'sync' ) {
					if ( function_exists( 'bbb_sync_captivate_episodes' ) ) {
						bbb_sync_captivate_episodes();
					} else {
						echo '<div class="notice notice-error"><p><strong>Sync tool not found.</strong> Missing <code>/inc/sync-captivate-episodes.php</code>.</p></div>';
					}
				} else {
					bbb_shutdown_api_tools();
				}
				?>
			</div>
		</div>
		<?php
	}
}

/**
 * Render the Shutdown tool UI (the old single-page form).
 */
if ( ! function_exists( 'bbb_shutdown_api_tools' ) ) {
	function bbb_shutdown_api_tools() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'bigbluebox' ) );
		}

		$disabled = (int) get_option( 'disable_captivate_api', 0 );
		?>
		<form method="post" action="options.php">
			<?php
			// Use the group we registered in admin_init.
			settings_fields( 'bbb_captivate_api_group' );
			// No sections registered; still safe to call for nonce/consistency.
			do_settings_sections( 'bbb_captivate_api_group' );
			?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">Disable Captivate API</th>
					<td>
						<label>
							<input type="checkbox" name="disable_captivate_api" value="1" <?php checked( 1, $disabled ); ?> />
							<span>Temporarily disables all calls to the Captivate API (useful during local development).</span>
						</label>
						<p class="description">This does not delete any cached values already saved to posts.</p>
					</td>
				</tr>
			</table>

			<?php submit_button( $disabled ? 'Enable API' : 'Disable API' ); ?>
		</form>
		<?php
	}
}