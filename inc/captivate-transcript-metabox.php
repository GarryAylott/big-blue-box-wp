<?php
/**
 * Captivate Transcript Fetch Button
 *
 * Adds a fetch button to the existing podcast_transcript ACF field
 * for fetching episode transcripts from the Captivate API.
 *
 * @package Big_Blue_Box
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render the transcript fetch UI above the podcast_transcript field.
 * Only for administrators.
 *
 * @param array $field ACF field array.
 */
add_action( 'acf/render_field/name=podcast_transcript', function ( $field ) {
	// Only for administrators.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	global $post;
	if ( ! $post ) {
		return;
	}

	$guid                = get_field( 'captivate_episode_selector', $post->ID );
	$existing_transcript = get_field( 'podcast_transcript', $post->ID );
	$api_disabled        = (int) get_option( 'disable_captivate_api', 0 );

	$stripped_transcript = trim( wp_strip_all_tags( $existing_transcript ) );
	$word_count          = ! empty( $stripped_transcript ) ? str_word_count( $stripped_transcript ) : 0;
	$has_transcript      = $word_count > 0;

	wp_nonce_field( 'bbb_fetch_transcript_action', 'bbb_transcript_nonce' );
	?>
	<div id="bbb-transcript-fetch-ui">
		<p id="bbb-transcript-status" style="margin: 0 0 8px 0;">
			<?php if ( $has_transcript ) : ?>
				<strong>Status:</strong> Transcript added to post (<?php echo esc_html( number_format( $word_count ) ); ?> words)
			<?php else : ?>
				<strong>Status:</strong> No transcript added to post
			<?php endif; ?>
		</p>

		<?php if ( $api_disabled ) : ?>
			<p class="description" style="color: #d63638; margin: 0 0 8px 0;">
				<strong>API is disabled.</strong> Enable it under Captivate API Tools to fetch transcripts.
			</p>
		<?php elseif ( ! $guid ) : ?>
			<p class="description" style="margin: 0 0 8px 0;">
				Select an episode from the Captivate Episode Selector field first.
			</p>
		<?php endif; ?>

		<p id="bbb-transcript-message" style="margin: 0 0 8px 0; display: none;"></p>

		<?php if ( $has_transcript ) : ?>
			<p style="margin: 0 0 8px 0;">
				<label>
					<input type="checkbox" id="bbb-overwrite-transcript" />
					Overwrite existing transcript
				</label>
			</p>
		<?php endif; ?>

		<p style="margin: 0;">
			<button type="button"
				id="bbb-fetch-transcript-btn"
				class="button button-secondary"
				data-post-id="<?php echo esc_attr( $post->ID ); ?>"
				data-has-transcript="<?php echo $has_transcript ? '1' : '0'; ?>"
				<?php disabled( $api_disabled || ! $guid ); ?>>
				Fetch Transcript
			</button>
			<span id="bbb-transcript-spinner" class="spinner" style="float: none; margin-top: 0;"></span>
		</p>
	</div>
	<?php
}, 5 ); // Priority 5 to render before the field.

/**
 * AJAX handler for fetching a single transcript.
 */
add_action( 'wp_ajax_bbb_fetch_single_transcript', function () {
	// Verify nonce.
	if ( ! check_ajax_referer( 'bbb_fetch_transcript_action', 'nonce', false ) ) {
		wp_send_json_error( [ 'message' => 'Invalid security token. Please refresh the page.' ] );
	}

	// Check capability.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( [ 'message' => 'You do not have permission to perform this action.' ] );
	}

	// Check API status.
	if ( (int) get_option( 'disable_captivate_api', 0 ) ) {
		wp_send_json_error( [ 'message' => 'Captivate API is currently disabled.' ] );
	}

	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

	if ( ! $post_id ) {
		wp_send_json_error( [ 'message' => 'Invalid post ID.' ] );
	}

	// Get the episode GUID.
	$guid = get_field( 'captivate_episode_selector', $post_id );

	if ( ! $guid ) {
		wp_send_json_error( [ 'message' => 'No episode selected. Please select an episode first.' ] );
	}

	// Ensure transcript functions are available.
	$transcript_file = get_template_directory() . '/inc/sync-captivate-transcripts.php';
	if ( ! file_exists( $transcript_file ) ) {
		wp_send_json_error( [ 'message' => 'Transcript sync module not found.' ] );
	}
	require_once $transcript_file;

	// Authenticate with Captivate.
	$bearer_token = bbb_captivate_authenticate();

	if ( is_wp_error( $bearer_token ) ) {
		wp_send_json_error( [ 'message' => 'Authentication failed: ' . $bearer_token->get_error_message() ] );
	}

	// Get or fetch media_id.
	$media_id = get_field( 'captivate_media_id', $post_id );

	if ( ! $media_id ) {
		$media_id = bbb_fetch_media_id_for_episode( $guid, $bearer_token );

		if ( is_wp_error( $media_id ) ) {
			wp_send_json_error( [ 'message' => 'Failed to get media ID: ' . $media_id->get_error_message() ] );
		}

		// Save media_id for future use.
		update_field( 'captivate_media_id', $media_id, $post_id );
	}

	// Fetch transcript.
	$raw_transcript = bbb_fetch_transcript_for_media_id( $media_id, $bearer_token );

	if ( is_wp_error( $raw_transcript ) ) {
		wp_send_json_error( [ 'message' => 'Failed to fetch transcript: ' . $raw_transcript->get_error_message() ] );
	}

	// No transcript available at source.
	if ( null === $raw_transcript ) {
		wp_send_json_error( [
			'message'      => 'Transcript not present',
			'no_transcript' => true,
		] );
	}

	// Normalize transcript.
	$normalized_transcript = bbb_normalize_transcript( $raw_transcript );

	// Check if normalized transcript is empty or just whitespace/empty tags.
	$stripped = trim( wp_strip_all_tags( $normalized_transcript ) );
	if ( empty( $stripped ) ) {
		wp_send_json_error( [
			'message'      => 'Transcript not present',
			'no_transcript' => true,
		] );
	}

	// Save transcript.
	$result = bbb_apply_transcript_to_post( $post_id, $media_id, $normalized_transcript );

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( [ 'message' => 'Failed to save transcript: ' . $result->get_error_message() ] );
	}

	$word_count = str_word_count( $stripped );

	wp_send_json_success( [
		'message'    => 'Transcript saved successfully!',
		'word_count' => number_format( $word_count ),
	] );
} );

/**
 * Enqueue admin scripts for the transcript fetch button.
 *
 * @param string $hook_suffix Current admin page.
 */
add_action( 'admin_enqueue_scripts', function ( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, [ 'post.php', 'post-new.php' ], true ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	global $post;
	if ( ! $post || 'post' !== $post->post_type ) {
		return;
	}

	// Inline script for the fetch button.
	$script = <<<'JS'
(function() {
	document.addEventListener('DOMContentLoaded', function() {
		var btn = document.getElementById('bbb-fetch-transcript-btn');
		var spinner = document.getElementById('bbb-transcript-spinner');
		var status = document.getElementById('bbb-transcript-status');
		var message = document.getElementById('bbb-transcript-message');
		var overwriteCheckbox = document.getElementById('bbb-overwrite-transcript');

		if (!btn) return;

		var hasTranscript = btn.dataset.hasTranscript === '1';

		btn.addEventListener('click', function(e) {
			e.preventDefault();

			// Check if overwrite checkbox exists and is required but not checked.
			if (hasTranscript && overwriteCheckbox && !overwriteCheckbox.checked) {
				message.innerHTML = '<span style="color: #dba617;">Please check the "Overwrite existing transcript" box to proceed.</span>';
				message.style.display = 'block';
				return;
			}

			// Show loading state.
			btn.disabled = true;
			btn.textContent = 'Fetching...';
			spinner.classList.add('is-active');
			message.style.display = 'none';

			// Make AJAX request.
			var formData = new FormData();
			formData.append('action', 'bbb_fetch_single_transcript');
			formData.append('post_id', btn.dataset.postId);
			formData.append('nonce', document.getElementById('bbb_transcript_nonce').value);

			fetch(ajaxurl, {
				method: 'POST',
				body: formData,
				credentials: 'same-origin'
			})
			.then(function(response) { return response.json(); })
			.then(function(data) {
				spinner.classList.remove('is-active');
				btn.disabled = false;
				btn.textContent = 'Fetch Transcript';

				if (data.success) {
					status.innerHTML = '<strong>Status:</strong> Transcript added to post (' + data.data.word_count + ' words)';
					message.innerHTML = '<span style="color: #00a32a;">' + data.data.message + '</span>';
					message.style.display = 'block';

					// Update state and hide checkbox since we now have a transcript.
					hasTranscript = true;
					btn.dataset.hasTranscript = '1';

					// Add checkbox if it doesn't exist, or keep it visible.
					if (!overwriteCheckbox) {
						var checkboxHtml = '<p style="margin: 0 0 8px 0;"><label><input type="checkbox" id="bbb-overwrite-transcript" /> Overwrite existing transcript</label></p>';
						message.insertAdjacentHTML('afterend', checkboxHtml);
						overwriteCheckbox = document.getElementById('bbb-overwrite-transcript');
					}
					if (overwriteCheckbox) {
						overwriteCheckbox.checked = false;
					}
				} else {
					// Check if this is a "no transcript at source" error.
					if (data.data && data.data.no_transcript) {
						status.innerHTML = '<strong>Status:</strong> <span style="color: #d63638;">No transcript added to episode at Captivate</span>';
						message.innerHTML = '<span style="color: #d63638;">' + data.data.message + '</span>';
					} else {
						message.innerHTML = '<span style="color: #d63638;">' + (data.data.message || 'An error occurred.') + '</span>';
					}
					message.style.display = 'block';
				}
			})
			.catch(function(error) {
				spinner.classList.remove('is-active');
				btn.disabled = false;
				btn.textContent = 'Fetch Transcript';
				message.innerHTML = '<span style="color: #d63638;">Network error. Please try again.</span>';
				message.style.display = 'block';
			});
		});
	});
})();
JS;

	wp_add_inline_script( 'jquery', $script );
} );
