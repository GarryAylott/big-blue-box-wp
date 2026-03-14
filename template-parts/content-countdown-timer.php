<?php
/**
 * Countdown Timer — sidebar component.
 *
 * Displays a countdown to the next upcoming event (weeks, days, hours).
 * Data is managed via the ACF "Countdown Timer" options page.
 *
 * @package Big_Blue_Box
 */

$event_name = get_option( 'bbb_countdown_event_name', '' );
$event_date = get_option( 'bbb_countdown_event_date', '' );

if ( empty( $event_name ) || empty( $event_date ) ) {
	return;
}

$timezone = new DateTimeZone( 'Europe/London' );
$now      = new DateTime( 'now', $timezone );
$target   = new DateTime( $event_date, $timezone );

if ( $target <= $now ) {
	return;
}

$diff       = $now->diff( $target );
$total_days = $diff->days;
$weeks      = intdiv( $total_days, 7 );
$days       = $total_days % 7;
$hours      = $diff->h;
?>

<section class="countdown-timer flow-small">
	<h5>
		<?php esc_html_e( 'Next On-Screen Event', 'bigbluebox' ); ?>
	</h5>
	<p class="countdown-timer__event-name">
		<i data-lucide="clock" class="icon-step-0"></i>
		<?php echo esc_html( $event_name ); ?>
	</p>
	<div class="countdown-timer__values">
		<div class="countdown-timer__unit">
			<span class="countdown-timer__label"><?php esc_html_e( 'Weeks', 'bigbluebox' ); ?></span>
			<span class="countdown-timer__number"><?php echo absint( $weeks ); ?></span>
		</div>
		<div class="countdown-timer__unit">
			<span class="countdown-timer__label"><?php esc_html_e( 'Days', 'bigbluebox' ); ?></span>
			<span class="countdown-timer__number"><?php echo absint( $days ); ?></span>
		</div>
		<div class="countdown-timer__unit">
			<span class="countdown-timer__label"><?php esc_html_e( 'Hours', 'bigbluebox' ); ?></span>
			<span class="countdown-timer__number"><?php echo absint( $hours ); ?></span>
		</div>
	</div>
</section>
