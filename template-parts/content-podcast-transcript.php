<?php
/**
 * Template part for displaying podcast transcript accordion.
 *
 * @package Big_Blue_Box
 */

$transcript = get_field( 'podcast_transcript' );

if ( empty( $transcript ) ) {
	return;
}
?>

<div class="podcast-transcript">
	<details class="podcast-transcript__accordion">
		<summary class="podcast-transcript__summary">
			<span class="podcast-transcript__title">
				<i data-lucide="file-text"></i>
				<?php esc_html_e( 'Read Episode Transcript', 'bigbluebox' ); ?>
			</span>
			<span class="podcast-transcript__icon" aria-hidden="true">
				<i data-lucide="chevron-down" class="icon-step-1"></i>
			</span>
		</summary>
		<div class="podcast-transcript__content flow">
			<?php echo wp_kses_post( $transcript ); ?>
		</div>
	</details>
</div>
