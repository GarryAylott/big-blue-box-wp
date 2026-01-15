<?php
/**
 * Post Date Helpers
 *
 * @package Big_Blue_Box
 */

/**
 * Get the "Last updated" date string for a post.
 *
 * Returns a formatted string if the post was modified more than 24 hours
 * after its original publish date. Returns an empty string otherwise.
 *
 * @param int|null $post_id Optional. Post ID. Defaults to current post.
 * @param string   $format  Optional. PHP date format. Defaults to 'F j, Y'.
 * @return string The formatted "Last updated on [date]" string, or empty.
 */
function bbb_get_last_updated_date( ?int $post_id = null, string $format = 'F j, Y' ): string {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	if ( ! $post_id ) {
		return '';
	}

	// Get timestamps for comparison (using GMT for consistency)
	$published_timestamp = get_post_time( 'U', true, $post_id );
	$modified_timestamp  = get_post_modified_time( 'U', true, $post_id );

	// Bail if we cannot get valid timestamps
	if ( ! $published_timestamp || ! $modified_timestamp ) {
		return '';
	}

	// Define the grace period (24 hours in seconds)
	$grace_period = (int) apply_filters( 'bbb_last_updated_grace_period', DAY_IN_SECONDS );

	// Check if modified date is beyond the grace period
	$time_difference = $modified_timestamp - $published_timestamp;

	if ( $time_difference <= $grace_period ) {
		return '';
	}

	// Get the formatted modified date (uses site timezone)
	$modified_date = get_the_modified_date( $format, $post_id );

	if ( ! $modified_date ) {
		return '';
	}

	// Build the output string (translatable)
	$output = sprintf(
		/* translators: %s: The formatted date (e.g., "January, 2024") */
		esc_html__( 'Last updated on %s', 'bigbluebox' ),
		esc_html( $modified_date )
	);

	/**
	 * Filter the last updated date output.
	 *
	 * @param string $output           The formatted output string.
	 * @param string $modified_date    The formatted date string.
	 * @param int    $post_id          The post ID.
	 * @param int    $time_difference  Seconds between publish and modified.
	 */
	return apply_filters( 'bbb_last_updated_date', $output, $modified_date, $post_id, $time_difference );
}

/**
 * Check if a post has been significantly updated (beyond grace period).
 *
 * Utility function for conditional checks in templates.
 *
 * @param int|null $post_id Optional. Post ID. Defaults to current post.
 * @return bool True if post was updated beyond grace period.
 */
function bbb_post_was_updated( ?int $post_id = null ): bool {
	return '' !== bbb_get_last_updated_date( $post_id );
}
