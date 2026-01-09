<?php
/**
 * Admin tool: Batch-sync Captivate transcripts to existing WP posts.
 * - Dry-run by default (no DB writes).
 * - Optional "Apply changes" mode to update ACF fields.
 *
 * Requires:
 * - Constants: CAPTIVATE_API_TOKEN, CAPTIVATE_USER_ID
 * - Existing ACF fields:
 *     - captivate_episode_guid (or captivate_episode_selector)
 *     - captivate_media_id
 *     - podcast_transcript
 *
 * @package Big_Blue_Box
 */

defined( 'ABSPATH' ) || exit;

/**
 * Authenticate with Captivate API and return bearer token.
 *
 * @return string|WP_Error Bearer token or error.
 */
if ( ! function_exists( 'bbb_captivate_authenticate' ) ) {
	function bbb_captivate_authenticate() {
		if ( ! defined( 'CAPTIVATE_API_TOKEN' ) || ! defined( 'CAPTIVATE_USER_ID' ) ) {
			return new WP_Error( 'captivate_creds_missing', 'Captivate credentials are missing.' );
		}

		$auth_response = wp_remote_post( 'https://api.captivate.fm/authenticate/token', [
			'timeout' => 30,
			'body'    => [
				'username' => CAPTIVATE_USER_ID,
				'token'    => CAPTIVATE_API_TOKEN,
			],
		] );

		if ( is_wp_error( $auth_response ) ) {
			return $auth_response;
		}

		$auth_code = wp_remote_retrieve_response_code( $auth_response );
		if ( $auth_code < 200 || $auth_code >= 300 ) {
			return new WP_Error( 'captivate_auth_failed', 'Captivate auth failed. HTTP status: ' . $auth_code );
		}

		$auth_data    = json_decode( wp_remote_retrieve_body( $auth_response ), true );
		$bearer_token = $auth_data['user']['token'] ?? null;

		if ( ! $bearer_token ) {
			return new WP_Error( 'captivate_auth_failed', 'Captivate auth failed. No token in response.' );
		}

		return $bearer_token;
	}
}

/**
 * Fetch media_id for an episode from the Captivate API.
 *
 * @param string $guid         Episode GUID.
 * @param string $bearer_token Valid bearer token.
 * @return string|WP_Error Media ID or error.
 */
if ( ! function_exists( 'bbb_fetch_media_id_for_episode' ) ) {
	function bbb_fetch_media_id_for_episode( $guid, $bearer_token ) {
		$episode_response = wp_remote_get( 'https://api.captivate.fm/episodes/' . rawurlencode( $guid ), [
			'timeout' => 30,
			'headers' => [
				'Authorization' => 'Bearer ' . $bearer_token,
				'Accept'        => 'application/json',
			],
		] );

		if ( is_wp_error( $episode_response ) ) {
			return $episode_response;
		}

		$episode_code = wp_remote_retrieve_response_code( $episode_response );
		if ( $episode_code < 200 || $episode_code >= 300 ) {
			return new WP_Error( 'episode_fetch_failed', 'Episode fetch failed. HTTP status: ' . $episode_code );
		}

		$data     = json_decode( wp_remote_retrieve_body( $episode_response ), true );
		$media_id = $data['episode']['media_id'] ?? null;

		if ( ! $media_id ) {
			return new WP_Error( 'no_media_id', 'No media_id found in episode data.' );
		}

		return (string) $media_id;
	}
}

/**
 * Fetch transcript for a media_id from the Captivate API.
 *
 * @param string $media_id     Media ID.
 * @param string $bearer_token Valid bearer token.
 * @return string|null|WP_Error Transcript text, null if not available, or error.
 */
if ( ! function_exists( 'bbb_fetch_transcript_for_media_id' ) ) {
	function bbb_fetch_transcript_for_media_id( $media_id, $bearer_token, $debug = false ) {
		// Try the spark endpoint first (with show ID)
		if ( defined( 'CAPTIVATE_SHOW_ID' ) && CAPTIVATE_SHOW_ID ) {
			$url = 'https://api.captivate.fm/spark/' . rawurlencode( CAPTIVATE_SHOW_ID ) . '/media/' . rawurlencode( $media_id ) . '/transcript';
		} else {
			// Fallback to direct media endpoint
			$url = 'https://api.captivate.fm/media/' . rawurlencode( $media_id ) . '/transcript';
		}

		if ( $debug ) {
			bbb_log( "🔍 Fetching transcript from: $url" );
		}

		$transcript_response = wp_remote_get( $url, [
			'timeout' => 30,
			'headers' => [
				'Authorization' => 'Bearer ' . $bearer_token,
				'Accept'        => 'application/json',
			],
		] );

		if ( is_wp_error( $transcript_response ) ) {
			if ( $debug ) {
				bbb_log( '❌ Transcript request error: ' . $transcript_response->get_error_message() );
			}
			return $transcript_response;
		}

		$transcript_code = wp_remote_retrieve_response_code( $transcript_response );
		$body = wp_remote_retrieve_body( $transcript_response );
		$content_type = wp_remote_retrieve_header( $transcript_response, 'content-type' );

		if ( $debug ) {
			bbb_log( "📡 Transcript API response code: $transcript_code" );
			bbb_log( "📋 Content-Type: $content_type" );
			bbb_log( '📏 Body length: ' . strlen( $body ) . ' bytes' );
			bbb_log( '📄 Transcript API response body (first 500 chars): ' . substr( $body, 0, 500 ) );
		}

		// 404 means no transcript available - graceful skip
		if ( $transcript_code === 404 ) {
			if ( $debug ) {
				bbb_log( '⚠️ Got 404 - trying alternate endpoint or no transcript available' );
			}
			return null;
		}

		if ( $transcript_code < 200 || $transcript_code >= 300 ) {
			if ( $debug ) {
				bbb_log( "❌ Bad status code: $transcript_code, Body: " . substr( $body, 0, 200 ) );
			}
			return new WP_Error( 'transcript_fetch_failed', 'Transcript fetch failed. HTTP status: ' . $transcript_code . ' - ' . substr( $body, 0, 100 ) );
		}

		if ( empty( $body ) ) {
			if ( $debug ) {
				bbb_log( '⚠️ Empty body despite 200 status' );
			}
			return null;
		}

		// Check if the response is JSON with an empty or null transcript
		$json = json_decode( $body, true );
		if ( is_array( $json ) ) {
			// Check for various empty transcript indicators
			if ( isset( $json['transcript'] ) && empty( $json['transcript'] ) ) {
				if ( $debug ) {
					bbb_log( '⚠️ JSON response has empty transcript field' );
				}
				return null;
			}
			// If it's a valid JSON with content, return the body
			if ( $debug ) {
				bbb_log( '✅ Got JSON response with keys: ' . implode( ', ', array_keys( $json ) ) );
			}
		}

		return $body;
	}
}

/**
 * Normalize transcript content to clean HTML.
 * Detects format (SRT, VTT, JSON, HTML, plain text) and converts appropriately.
 *
 * @param string $raw_transcript Raw transcript content.
 * @return string Normalized HTML.
 */
if ( ! function_exists( 'bbb_normalize_transcript' ) ) {
	function bbb_normalize_transcript( $raw_transcript ) {
		$raw_transcript = trim( $raw_transcript );

		if ( empty( $raw_transcript ) ) {
			return '';
		}

		// Try JSON first
		$json = json_decode( $raw_transcript, true );
		if ( is_array( $json ) ) {
			return bbb_convert_json_transcript_to_html( $json );
		}

		// SRT format detection: lines with timestamps like "00:00:00,000 --> 00:00:05,000"
		if ( preg_match( '/\d{2}:\d{2}:\d{2},\d{3}\s*-->\s*\d{2}:\d{2}:\d{2},\d{3}/', $raw_transcript ) ) {
			return bbb_convert_srt_to_html( $raw_transcript );
		}

		// VTT format detection: starts with "WEBVTT"
		if ( stripos( trim( $raw_transcript ), 'WEBVTT' ) === 0 ) {
			return bbb_convert_vtt_to_html( $raw_transcript );
		}

		// Already HTML: contains <p> or <span> or <div> tags
		if ( preg_match( '/<(p|span|div)[^>]*>/i', $raw_transcript ) ) {
			return wp_kses_post( $raw_transcript );
		}

		// Plain text: wrap paragraphs in <p> tags
		return bbb_convert_plaintext_to_html( $raw_transcript );
	}
}

/**
 * Convert JSON transcript to HTML.
 *
 * @param array $json Decoded JSON data.
 * @return string HTML content.
 */
if ( ! function_exists( 'bbb_convert_json_transcript_to_html' ) ) {
	function bbb_convert_json_transcript_to_html( $json ) {
		$html = '';

		// Structure: { "transcript": { "transcript_json": [...] } } (Captivate/Assembly AI format)
		if ( isset( $json['transcript']['transcript_json'] ) && is_array( $json['transcript']['transcript_json'] ) ) {
			$paragraphs = [];
			$current_speaker = '';
			$current_text = [];

			foreach ( $json['transcript']['transcript_json'] as $segment ) {
				$speaker = isset( $segment['speaker'] ) ? $segment['speaker'] : '';

				// Extract text from words array
				$segment_text = '';
				if ( isset( $segment['words'] ) && is_array( $segment['words'] ) ) {
					$words = [];
					foreach ( $segment['words'] as $word_data ) {
						if ( isset( $word_data['text'] ) ) {
							$words[] = $word_data['text'];
						}
					}
					$segment_text = implode( ' ', $words );
				}

				// Group by speaker for better readability
				if ( $speaker && $speaker !== $current_speaker ) {
					// Save previous speaker's text
					if ( $current_text ) {
						$combined = implode( ' ', $current_text );
						if ( $current_speaker ) {
							$paragraphs[] = '<p><strong>' . esc_html( $current_speaker ) . ':</strong> ' . esc_html( $combined ) . '</p>';
						} else {
							$paragraphs[] = '<p>' . esc_html( $combined ) . '</p>';
						}
					}
					// Start new speaker
					$current_speaker = $speaker;
					$current_text = [ $segment_text ];
				} else {
					// Continue with current speaker
					$current_text[] = $segment_text;
				}
			}

			// Don't forget the last speaker's text
			if ( $current_text ) {
				$combined = implode( ' ', $current_text );
				if ( $current_speaker ) {
					$paragraphs[] = '<p><strong>' . esc_html( $current_speaker ) . ':</strong> ' . esc_html( $combined ) . '</p>';
				} else {
					$paragraphs[] = '<p>' . esc_html( $combined ) . '</p>';
				}
			}

			return implode( "\n", $paragraphs );
		}

		// Structure: { "transcript": "..." } or { "text": "..." }
		if ( isset( $json['transcript'] ) && is_string( $json['transcript'] ) ) {
			return bbb_convert_plaintext_to_html( $json['transcript'] );
		}

		if ( isset( $json['text'] ) && is_string( $json['text'] ) ) {
			return bbb_convert_plaintext_to_html( $json['text'] );
		}

		// Structure: { "segments": [{ "text": "...", "start": 0 }] }
		if ( isset( $json['segments'] ) && is_array( $json['segments'] ) ) {
			$paragraphs = [];
			foreach ( $json['segments'] as $segment ) {
				if ( isset( $segment['text'] ) && is_string( $segment['text'] ) ) {
					$text = trim( $segment['text'] );
					if ( $text ) {
						$paragraphs[] = '<p>' . esc_html( $text ) . '</p>';
					}
				}
			}
			return implode( "\n", $paragraphs );
		}

		// Structure: { "words": [{ "word": "Hello" }] }
		if ( isset( $json['words'] ) && is_array( $json['words'] ) ) {
			$words = [];
			foreach ( $json['words'] as $word_data ) {
				if ( isset( $word_data['word'] ) ) {
					$words[] = $word_data['word'];
				}
			}
			if ( $words ) {
				return '<p>' . esc_html( implode( ' ', $words ) ) . '</p>';
			}
		}

		// Fallback: try to extract any text content
		$text = bbb_extract_text_from_json( $json );
		if ( $text ) {
			return bbb_convert_plaintext_to_html( $text );
		}

		// Last resort: pretty print the JSON
		return '<pre>' . esc_html( wp_json_encode( $json, JSON_PRETTY_PRINT ) ) . '</pre>';
	}
}

/**
 * Recursively extract text from JSON structure.
 *
 * @param mixed $data JSON data.
 * @return string Extracted text.
 */
if ( ! function_exists( 'bbb_extract_text_from_json' ) ) {
	function bbb_extract_text_from_json( $data ) {
		if ( is_string( $data ) ) {
			return $data;
		}

		if ( ! is_array( $data ) ) {
			return '';
		}

		$texts = [];
		foreach ( $data as $key => $value ) {
			if ( in_array( $key, [ 'text', 'content', 'transcript', 'body' ], true ) && is_string( $value ) ) {
				$texts[] = $value;
			} elseif ( is_array( $value ) ) {
				$nested = bbb_extract_text_from_json( $value );
				if ( $nested ) {
					$texts[] = $nested;
				}
			}
		}

		return implode( ' ', $texts );
	}
}

/**
 * Convert SRT subtitle format to HTML paragraphs.
 *
 * @param string $srt SRT content.
 * @return string HTML content.
 */
if ( ! function_exists( 'bbb_convert_srt_to_html' ) ) {
	function bbb_convert_srt_to_html( $srt ) {
		// Split by double newline to get subtitle blocks
		$blocks = preg_split( '/\n\s*\n/', $srt );
		$paragraphs = [];

		foreach ( $blocks as $block ) {
			$lines = explode( "\n", trim( $block ) );

			// Skip index number and timestamp lines, get text
			$text_lines = [];
			foreach ( $lines as $line ) {
				$line = trim( $line );
				// Skip numeric index
				if ( preg_match( '/^\d+$/', $line ) ) {
					continue;
				}
				// Skip timestamp line
				if ( preg_match( '/\d{2}:\d{2}:\d{2},\d{3}\s*-->/', $line ) ) {
					continue;
				}
				if ( $line ) {
					$text_lines[] = $line;
				}
			}

			if ( $text_lines ) {
				$paragraphs[] = '<p>' . esc_html( implode( ' ', $text_lines ) ) . '</p>';
			}
		}

		return implode( "\n", $paragraphs );
	}
}

/**
 * Convert WebVTT format to HTML paragraphs.
 *
 * @param string $vtt VTT content.
 * @return string HTML content.
 */
if ( ! function_exists( 'bbb_convert_vtt_to_html' ) ) {
	function bbb_convert_vtt_to_html( $vtt ) {
		// Remove WEBVTT header and metadata
		$vtt = preg_replace( '/^WEBVTT.*?\n\n/s', '', $vtt );

		// Split by double newline to get cue blocks
		$blocks = preg_split( '/\n\s*\n/', $vtt );
		$paragraphs = [];

		foreach ( $blocks as $block ) {
			$lines = explode( "\n", trim( $block ) );

			$text_lines = [];
			foreach ( $lines as $line ) {
				$line = trim( $line );
				// Skip timestamp line (VTT uses . not ,)
				if ( preg_match( '/\d{2}:\d{2}:\d{2}\.\d{3}\s*-->/', $line ) ) {
					continue;
				}
				// Skip cue identifiers
				if ( preg_match( '/^[a-zA-Z0-9_-]+$/', $line ) ) {
					continue;
				}
				if ( $line ) {
					// Strip VTT tags like <v Speaker>
					$line = preg_replace( '/<[^>]+>/', '', $line );
					$text_lines[] = $line;
				}
			}

			if ( $text_lines ) {
				$paragraphs[] = '<p>' . esc_html( implode( ' ', $text_lines ) ) . '</p>';
			}
		}

		return implode( "\n", $paragraphs );
	}
}

/**
 * Convert plain text to HTML, splitting on double newlines.
 *
 * @param string $text Plain text.
 * @return string HTML content.
 */
if ( ! function_exists( 'bbb_convert_plaintext_to_html' ) ) {
	function bbb_convert_plaintext_to_html( $text ) {
		$text = trim( $text );

		if ( empty( $text ) ) {
			return '';
		}

		// Normalize line endings
		$text = str_replace( [ "\r\n", "\r" ], "\n", $text );

		// Split on double newlines for paragraphs
		$paragraphs = preg_split( '/\n\s*\n/', $text );
		$html_paragraphs = [];

		foreach ( $paragraphs as $para ) {
			$para = trim( $para );
			if ( $para ) {
				// Convert single newlines to spaces within paragraphs
				$para = preg_replace( '/\n/', ' ', $para );
				$para = preg_replace( '/\s+/', ' ', $para );
				$html_paragraphs[] = '<p>' . esc_html( $para ) . '</p>';
			}
		}

		return implode( "\n", $html_paragraphs );
	}
}

/**
 * Apply transcript to a post.
 *
 * @param int    $post_id    Post ID.
 * @param string $media_id   Captivate media ID.
 * @param string $transcript Normalized transcript HTML.
 * @return true|WP_Error Success or error.
 */
if ( ! function_exists( 'bbb_apply_transcript_to_post' ) ) {
	function bbb_apply_transcript_to_post( $post_id, $media_id, $transcript ) {
		// Save media_id for future reference
		update_field( 'captivate_media_id', $media_id, $post_id );

		// Save transcript
		update_field( 'podcast_transcript', $transcript, $post_id );

		bbb_log( "📝 Transcript saved for post $post_id (media_id: $media_id)" );

		return true;
	}
}

/**
 * Check if API is enabled.
 *
 * @return bool
 */
if ( ! function_exists( 'bbb_transcript_can_use_api' ) ) {
	function bbb_transcript_can_use_api() {
		$disabled = (int) get_option( 'disable_captivate_api', 0 );
		return $disabled === 0;
	}
}

/**
 * Render the admin UI and run the transcript sync.
 */
if ( ! function_exists( 'bbb_sync_captivate_transcripts' ) ) {
	function bbb_sync_captivate_transcripts() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'bigbluebox' ) );
		}

		// Defaults
		$apply         = ! empty( $_POST['apply_changes'] );
		$force         = ! empty( $_POST['force_refresh'] );
		$dry_run       = empty( $_POST['apply_changes'] );
		$only_missing  = isset( $_POST['only_missing'] ) ? ! empty( $_POST['only_missing'] ) : true;
		$limit_output  = isset( $_POST['limit_output'] ) ? max( 10, min( 500, (int) $_POST['limit_output'] ) ) : 100;
		$max_posts     = isset( $_POST['max_posts'] ) ? max( 1, min( 50, (int) $_POST['max_posts'] ) ) : 10;
		$debug_mode    = ! empty( $_POST['debug_mode'] );
		$batch_size    = 5;

		// Warn if API is globally disabled
		if ( ! bbb_transcript_can_use_api() ) {
			echo '<div class="notice notice-warning"><p><strong>API Shutdown is currently ON.</strong> The Transcript Sync tool will not function until you enable the API under the "API Shutdown" tab.</p></div>';
		}

		?>
		<h2>Sync Captivate Transcripts</h2>
		<p>This tool fetches podcast transcripts from the Captivate API and stores them locally for SEO purposes.</p>
		<p><strong>Note:</strong> Posts must have a Captivate episode linked (run "Sync Episodes" first if needed).</p>

		<form method="post" style="margin-top: 1rem;">
			<?php wp_nonce_field( 'bbb_sync_captivate_transcripts_action', 'bbb_sync_transcripts_nonce' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">Force refresh from Captivate</th>
					<td>
						<label><input type="checkbox" name="force_refresh" value="1" <?php checked( $force ); ?> /> Re-fetch media_id even if already stored.</label>
					</td>
				</tr>

				<tr>
					<th scope="row">Apply changes</th>
					<td>
						<label><input type="checkbox" name="apply_changes" value="1" <?php checked( $apply ); ?> /> Actually write transcripts to posts (not just a dry run).</label>
						<p class="description">Leave unchecked to preview (dry run). Check to save transcripts.</p>
					</td>
				</tr>

				<tr>
					<th scope="row">Only posts without transcripts</th>
					<td>
						<label><input type="checkbox" name="only_missing" value="1" <?php checked( $only_missing ); ?> /> Skip posts that already have a transcript saved.</label>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="max_posts">Max posts to process</label></th>
					<td>
						<input name="max_posts" id="max_posts" type="number" min="1" max="50" value="<?php echo esc_attr( $max_posts ); ?>" />
						<p class="description">Process this many posts per run to avoid timeouts. Run multiple times to process all posts.</p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="limit_output">Limit results displayed</label></th>
					<td>
						<input name="limit_output" id="limit_output" type="number" min="10" max="500" value="<?php echo esc_attr( $limit_output ); ?>" />
					</td>
				</tr>

				<tr>
					<th scope="row">Debug mode</th>
					<td>
						<label><input type="checkbox" name="debug_mode" value="1" <?php checked( $debug_mode ); ?> /> Log API responses to debug log.</label>
						<p class="description">Check your debug.log file after running to see the raw API responses.</p>
					</td>
				</tr>
			</table>

			<?php submit_button( $apply ? 'Sync Transcripts (Apply Changes)' : 'Scan for Transcripts (Dry Run)' ); ?>
		</form>
		<?php

		// Only proceed on submit
		if ( empty( $_POST['bbb_sync_transcripts_nonce'] ) || ! wp_verify_nonce( $_POST['bbb_sync_transcripts_nonce'], 'bbb_sync_captivate_transcripts_action' ) ) {
			return;
		}

		// Check API status
		if ( ! bbb_transcript_can_use_api() ) {
			echo '<div class="notice notice-error"><p>API is disabled. Enable API under the "API Shutdown" tab to sync transcripts.</p></div>';
			return;
		}

		// Authenticate
		$bearer_token = bbb_captivate_authenticate();
		if ( is_wp_error( $bearer_token ) ) {
			echo '<div class="notice notice-error"><p><strong>Authentication failed:</strong> ' . esc_html( $bearer_token->get_error_message() ) . '</p></div>';
			return;
		}

		// Query all published podcast posts
		$meta_query = [];

		// Must have a GUID
		$meta_query[] = [
			'relation' => 'OR',
			[
				'key'     => 'captivate_episode_guid',
				'compare' => 'EXISTS',
			],
			[
				'key'     => 'captivate_episode_selector',
				'compare' => 'EXISTS',
			],
		];

		$q = new WP_Query( [
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'category_name'  => 'podcasts',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'fields'         => 'ids',
			'no_found_rows'  => true,
		] );

		if ( empty( $q->posts ) ) {
			echo '<div class="notice notice-info"><p>No published posts found in the "podcasts" category.</p></div>';
			return;
		}

		$total_posts   = count( $q->posts );
		$updated       = 0;
		$skipped_exist = 0;
		$skipped_none  = 0;
		$skipped_guid  = 0;
		$errors        = 0;
		$shown         = 0;
		$processed     = 0;
		$api_calls     = 0; // Track posts that actually hit the API

		echo '<p><strong>Found ' . (int) $total_posts . ' podcast posts.</strong> Processing up to ' . (int) $max_posts . ' posts this run.</p>';

		echo '<h3>Results</h3>';
		echo '<table class="widefat striped" style="margin-top:1rem">';
		echo '<thead><tr>';
		echo '<th>Post</th><th>Media ID</th><th>Transcript Preview</th><th>Status</th>';
		echo '</tr></thead><tbody>';

		foreach ( $q->posts as $post_id ) {
			// Stop if we've processed enough posts that required API calls
			if ( $api_calls >= $max_posts ) {
				break;
			}

			$post_title = get_the_title( $post_id );

			// Get GUID
			$guid = get_post_meta( $post_id, 'captivate_episode_guid', true );
			if ( ! $guid ) {
				$guid = get_field( 'captivate_episode_selector', $post_id );
			}

			if ( ! $guid ) {
				$skipped_guid++;
				if ( $shown < $limit_output ) {
					echo '<tr>';
					echo '<td><a href="' . esc_url( get_edit_post_link( $post_id ) ) . '">#' . (int) $post_id . '</a><br>' . esc_html( $post_title ) . '</td>';
					echo '<td>—</td>';
					echo '<td>—</td>';
					echo '<td><em>No GUID - run Episode Sync first</em></td>';
					echo '</tr>';
					$shown++;
				}
				continue;
			}

			// Check if transcript already exists
			$existing_transcript = get_field( 'podcast_transcript', $post_id );
			if ( $only_missing && ! empty( $existing_transcript ) ) {
				$skipped_exist++;
				if ( $shown < $limit_output ) {
					echo '<tr>';
					echo '<td><a href="' . esc_url( get_edit_post_link( $post_id ) ) . '">#' . (int) $post_id . '</a><br>' . esc_html( $post_title ) . '</td>';
					echo '<td>' . esc_html( get_field( 'captivate_media_id', $post_id ) ?: '—' ) . '</td>';
					echo '<td><em>' . esc_html( wp_trim_words( wp_strip_all_tags( $existing_transcript ), 15, '...' ) ) . '</em></td>';
					echo '<td>Skipped (already has transcript)</td>';
					echo '</tr>';
					$shown++;
				}
				continue;
			}

			// Get or fetch media_id
			$media_id = get_field( 'captivate_media_id', $post_id );

			if ( ! $media_id || $force ) {
				$media_id = bbb_fetch_media_id_for_episode( $guid, $bearer_token );

				if ( is_wp_error( $media_id ) ) {
					$errors++;
					if ( $shown < $limit_output ) {
						echo '<tr>';
						echo '<td><a href="' . esc_url( get_edit_post_link( $post_id ) ) . '">#' . (int) $post_id . '</a><br>' . esc_html( $post_title ) . '</td>';
						echo '<td>—</td>';
						echo '<td>—</td>';
						echo '<td><strong>Error:</strong> ' . esc_html( $media_id->get_error_message() ) . '</td>';
						echo '</tr>';
						$shown++;
					}
					continue;
				}
			}

			// Fetch transcript - this counts as an API call
			$api_calls++;
			$raw_transcript = bbb_fetch_transcript_for_media_id( $media_id, $bearer_token, $debug_mode );

			// Debug output - make a direct API call to show raw response
			if ( $debug_mode && $shown < $limit_output ) {
				// Make a test call to see the raw response (use same endpoint as the function)
				if ( defined( 'CAPTIVATE_SHOW_ID' ) && CAPTIVATE_SHOW_ID ) {
					$test_url = 'https://api.captivate.fm/spark/' . rawurlencode( CAPTIVATE_SHOW_ID ) . '/media/' . rawurlencode( $media_id ) . '/transcript';
				} else {
					$test_url = 'https://api.captivate.fm/media/' . rawurlencode( $media_id ) . '/transcript';
				}
				$test_response = wp_remote_get( $test_url, [
					'timeout' => 30,
					'headers' => [
						'Authorization' => 'Bearer ' . $bearer_token,
						'Accept'        => 'application/json',
					],
				] );

				$test_code = wp_remote_retrieve_response_code( $test_response );
				$test_body = wp_remote_retrieve_body( $test_response );
				$test_content_type = wp_remote_retrieve_header( $test_response, 'content-type' );

				echo '<tr style="background: #fffbcc;">';
				echo '<td colspan="4"><strong>DEBUG for post #' . (int) $post_id . ':</strong><br>';
				echo 'API URL: ' . esc_html( $test_url ) . '<br>';
				echo 'HTTP Status: ' . esc_html( $test_code ) . '<br>';
				echo 'Content-Type: ' . esc_html( $test_content_type ) . '<br>';
				echo 'Body Length: ' . strlen( $test_body ) . ' bytes<br>';
				echo 'Raw response type: ' . esc_html( gettype( $raw_transcript ) ) . '<br>';
				if ( is_string( $raw_transcript ) ) {
					echo 'Response length: ' . strlen( $raw_transcript ) . ' chars<br>';
					echo 'First 500 chars: <pre>' . esc_html( substr( $raw_transcript, 0, 500 ) ) . '</pre>';
				} elseif ( is_wp_error( $raw_transcript ) ) {
					echo 'Error: ' . esc_html( $raw_transcript->get_error_message() );
				} elseif ( $raw_transcript === null ) {
					echo 'Response is NULL<br>';
					if ( ! empty( $test_body ) ) {
						echo 'Raw body (first 500 chars): <pre>' . esc_html( substr( $test_body, 0, 500 ) ) . '</pre>';
					} else {
						echo 'Body is empty';
					}
				}
				echo '</td></tr>';
			}

			if ( is_wp_error( $raw_transcript ) ) {
				$errors++;
				if ( $shown < $limit_output ) {
					echo '<tr>';
					echo '<td><a href="' . esc_url( get_edit_post_link( $post_id ) ) . '">#' . (int) $post_id . '</a><br>' . esc_html( $post_title ) . '</td>';
					echo '<td>' . esc_html( $media_id ) . '</td>';
					echo '<td>—</td>';
					echo '<td><strong>Error:</strong> ' . esc_html( $raw_transcript->get_error_message() ) . '</td>';
					echo '</tr>';
					$shown++;
				}
				continue;
			}

			if ( $raw_transcript === null ) {
				$skipped_none++;
				if ( $shown < $limit_output ) {
					echo '<tr>';
					echo '<td><a href="' . esc_url( get_edit_post_link( $post_id ) ) . '">#' . (int) $post_id . '</a><br>' . esc_html( $post_title ) . '</td>';
					echo '<td>' . esc_html( $media_id ) . '</td>';
					echo '<td>—</td>';
					echo '<td>No transcript available</td>';
					echo '</tr>';
					$shown++;
				}
				continue;
			}

			// Normalize transcript
			$normalized_transcript = bbb_normalize_transcript( $raw_transcript );

			if ( empty( $normalized_transcript ) ) {
				$skipped_none++;
				if ( $shown < $limit_output ) {
					echo '<tr>';
					echo '<td><a href="' . esc_url( get_edit_post_link( $post_id ) ) . '">#' . (int) $post_id . '</a><br>' . esc_html( $post_title ) . '</td>';
					echo '<td>' . esc_html( $media_id ) . '</td>';
					echo '<td>—</td>';
					echo '<td>Empty transcript</td>';
					echo '</tr>';
					$shown++;
				}
				continue;
			}

			$action  = $dry_run ? 'Would update' : 'Updated';
			$preview = wp_trim_words( wp_strip_all_tags( $normalized_transcript ), 15, '...' );

			if ( ! $dry_run ) {
				$result = bbb_apply_transcript_to_post( $post_id, $media_id, $normalized_transcript );
				if ( is_wp_error( $result ) ) {
					$action = 'Error: ' . $result->get_error_message();
					$errors++;
				} else {
					$updated++;
				}
			}

			if ( $shown < $limit_output ) {
				echo '<tr>';
				echo '<td><a href="' . esc_url( get_edit_post_link( $post_id ) ) . '">#' . (int) $post_id . '</a><br>' . esc_html( $post_title ) . '</td>';
				echo '<td>' . esc_html( $media_id ) . '</td>';
				echo '<td>' . esc_html( $preview ) . '</td>';
				echo '<td>' . esc_html( $action ) . '</td>';
				echo '</tr>';
				$shown++;
			}

			$processed++;

			// Small delay every batch_size posts to avoid hammering API
			if ( $processed % $batch_size === 0 ) {
				usleep( 100000 ); // 100ms
			}
		}
		wp_reset_postdata();

		echo '</tbody></table>';

		echo '<p style="margin-top: 1rem;"><strong>Summary:</strong> ';
		echo 'Updated: ' . (int) $updated . ' &nbsp;|&nbsp; ';
		echo 'Skipped (already has transcript): ' . (int) $skipped_exist . ' &nbsp;|&nbsp; ';
		echo 'Skipped (no transcript available): ' . (int) $skipped_none . ' &nbsp;|&nbsp; ';
		echo 'Skipped (no GUID): ' . (int) $skipped_guid . ' &nbsp;|&nbsp; ';
		echo 'Errors: ' . (int) $errors;
		echo '</p>';

		// Calculate remaining posts that need processing
		$remaining = $total_posts - $skipped_exist - $skipped_guid - $api_calls;
		if ( $remaining > 0 && $api_calls >= $max_posts ) {
			echo '<div class="notice notice-warning"><p><strong>More posts to process!</strong> Approximately ' . (int) $remaining . ' posts remain. Run the sync again to continue.</p></div>';
		}

		if ( $dry_run ) {
			echo '<div class="notice notice-info"><p>This was a dry run. Check "Apply changes" to save transcripts to posts.</p></div>';
		}
	}
}
