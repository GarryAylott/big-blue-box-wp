<?php
/**
 * Event Timer — admin settings page.
 *
 * Registers a top-level admin menu page for managing the sidebar countdown timer.
 * Uses the native WordPress Settings API (no ACF Pro required).
 *
 * @package Big_Blue_Box
 */

/**
 * Register the settings page and menu item.
 */
function bbb_countdown_timer_menu() {
	add_menu_page(
		__( 'Event Timer', 'bigbluebox' ),
		__( 'Event Timer', 'bigbluebox' ),
		'manage_options',
		'countdown-timer',
		'bbb_countdown_timer_page',
		'dashicons-clock',
		30
	);
}
add_action( 'admin_menu', 'bbb_countdown_timer_menu' );

/**
 * Register settings and fields.
 */
function bbb_countdown_timer_settings_init() {
	register_setting( 'bbb_countdown_timer', 'bbb_countdown_event_name', array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => '',
	) );

	register_setting( 'bbb_countdown_timer', 'bbb_countdown_event_date', array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => '',
	) );

	add_settings_section(
		'bbb_countdown_timer_section',
		'',
		'__return_null',
		'countdown-timer'
	);

	add_settings_field(
		'bbb_countdown_event_name',
		__( 'Event Name', 'bigbluebox' ),
		'bbb_countdown_event_name_field',
		'countdown-timer',
		'bbb_countdown_timer_section'
	);

	add_settings_field(
		'bbb_countdown_event_date',
		__( 'Event Date & Time', 'bigbluebox' ),
		'bbb_countdown_event_date_field',
		'countdown-timer',
		'bbb_countdown_timer_section'
	);
}
add_action( 'admin_init', 'bbb_countdown_timer_settings_init' );

/**
 * Render the event name field.
 */
function bbb_countdown_event_name_field() {
	$value = get_option( 'bbb_countdown_event_name', '' );
	printf(
		'<input type="text" id="bbb_countdown_event_name" name="bbb_countdown_event_name" value="%s" class="regular-text" placeholder="%s">',
		esc_attr( $value ),
		esc_attr__( 'Event name', 'bigbluebox' )
	);
	echo '<p class="description">' . esc_html__( 'The name of the upcoming event to count down to.', 'bigbluebox' ) . '</p>';
}

/**
 * Render the event date/time field.
 */
function bbb_countdown_event_date_field() {
	$value = get_option( 'bbb_countdown_event_date', '' );
	printf(
		'<input type="datetime-local" id="bbb_countdown_event_date" name="bbb_countdown_event_date" value="%s">',
		esc_attr( $value )
	);
	echo '<p class="description">' . esc_html__( 'The date and time of the event. Countdown values are calculated automatically.', 'bigbluebox' ) . '</p>';
}

/**
 * Handle the "Remove Timer" action.
 */
function bbb_countdown_timer_handle_remove() {
	if ( ! isset( $_POST['bbb_remove_countdown_timer'] ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	check_admin_referer( 'bbb_remove_countdown_timer' );

	delete_option( 'bbb_countdown_event_name' );
	delete_option( 'bbb_countdown_event_date' );

	wp_safe_redirect( add_query_arg( 'bbb_timer_removed', '1', menu_page_url( 'countdown-timer', false ) ) );
	exit;
}
add_action( 'admin_init', 'bbb_countdown_timer_handle_remove' );

/**
 * Display admin notices on the Event Timer page.
 */
function bbb_countdown_timer_admin_notices() {
	$screen = get_current_screen();
	if ( ! $screen || 'toplevel_page_countdown-timer' !== $screen->id ) {
		return;
	}

	if ( isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'] ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Event timer saved.', 'bigbluebox' ) . '</p></div>';
	}

	if ( isset( $_GET['bbb_timer_removed'] ) && '1' === $_GET['bbb_timer_removed'] ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Event timer removed.', 'bigbluebox' ) . '</p></div>';
	}
}
add_action( 'admin_notices', 'bbb_countdown_timer_admin_notices' );

/**
 * Render the settings page.
 */
function bbb_countdown_timer_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$has_event = get_option( 'bbb_countdown_event_name', '' ) || get_option( 'bbb_countdown_event_date', '' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Event Timer', 'bigbluebox' ); ?></h1>
		<p><?php esc_html_e( 'Configure the countdown timer that appears at the top of the sidebar. Leave both fields empty to hide the countdown.', 'bigbluebox' ); ?></p>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'bbb_countdown_timer' );
			do_settings_sections( 'countdown-timer' );
			submit_button( __( 'Save Event', 'bigbluebox' ) );
			?>
		</form>
		<?php if ( $has_event ) : ?>
			<form method="post" style="margin-top: -8px;">
				<?php wp_nonce_field( 'bbb_remove_countdown_timer' ); ?>
				<input type="hidden" name="bbb_remove_countdown_timer" value="1">
				<button type="submit" class="button" style="color: #b32d2e; border-color: #b32d2e;"><?php esc_html_e( 'Remove Timer', 'bigbluebox' ); ?></button>
			</form>
		<?php endif; ?>
	</div>
	<?php
}
