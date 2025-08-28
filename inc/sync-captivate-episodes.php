<?php
/**
 * Admin tool: Batch-match & sync Captivate episodes to existing WP posts.
 * - Dry-run by default (no DB writes).
 * - Optional "Apply changes" mode to update ACF/meta.
 *
 * Requires:
 * - Constants: CAPTIVATE_API_TOKEN, CAPTIVATE_USER_ID, CAPTIVATE_SHOW_ID
 * - Existing ACF fields:
 *     - captivate_episode_selector (stores GUID)
 *     - captivate_audio_url
 *     - podcast_episode_number
 *     - podcast_episode_type    (string: 'bonus', 'trailer', '' etc.)
 *
 * Uses the existing bbb_get_captivate_audio_url( $guid ) for audio URL.
 */


defined( 'ABSPATH' ) || exit;

// Always require captivate-external-audio.php if available (for bbb_get_captivate_audio_url)
if ( ! function_exists( 'bbb_get_captivate_audio_url' ) ) {
	$audio_lib = get_template_directory() . '/inc/captivate-external-audio.php';
	if ( file_exists( $audio_lib ) ) {
		require_once $audio_lib;
	}
}

/**
 * Small helpers
 */
if ( ! function_exists( 'bbb_sync_normalize' ) ) {
	function bbb_sync_normalize( $s ) {
		$s = wp_strip_all_tags( (string) $s );
		$s = html_entity_decode( $s, ENT_QUOTES | ENT_HTML5 );
		$s = strtolower( $s );
		$s = preg_replace( '/\s+/', ' ', $s );
		$s = trim( $s );
		return $s;
	}
}

if ( ! function_exists( 'bbb_sync_can_use_api' ) ) {
	function bbb_sync_can_use_api() {
		// Global kill switch from the “API Shutdown” tool
		$disabled = (int) get_option( 'disable_captivate_api', 0 );
		return $disabled === 0;
	}
}

/**
 * Fetch ALL Captivate episodes (from cache unless forced).
 * Returns: array of episodes or WP_Error.
 */
if ( ! function_exists( 'bbb_captivate_fetch_all_episodes' ) ) {
	function bbb_captivate_fetch_all_episodes( $force_refresh = false ) {
		$cache_key = 'captivate_episodes_cache_full';
		if ( ! $force_refresh ) {
			$cached = get_transient( $cache_key );
			if ( is_array( $cached ) && $cached ) {
				return $cached;
			}
		}

		if ( ! defined( 'CAPTIVATE_API_TOKEN' ) || ! defined( 'CAPTIVATE_USER_ID' ) || ! defined( 'CAPTIVATE_SHOW_ID' ) ) {
			return new WP_Error( 'captivate_creds_missing', 'Captivate credentials are missing.' );
		}

		// Auth
		$auth_response = wp_remote_post( 'https://api.captivate.fm/authenticate/token', [
			'timeout' => 10,
			'body'    => [
				'username' => CAPTIVATE_USER_ID,
				'token'    => CAPTIVATE_API_TOKEN,
			],
		] );

		if ( is_wp_error( $auth_response ) ) {
			return $auth_response;
		}

		$auth_data    = json_decode( wp_remote_retrieve_body( $auth_response ), true );
		$bearer_token = $auth_data['user']['token'] ?? null;
		if ( ! $bearer_token ) {
			return new WP_Error( 'captivate_auth_failed', 'Captivate auth failed.' );
		}

		// Episodes (Captivate returns all in one go for the show)
		$episodes_response = wp_remote_get(
			'https://api.captivate.fm/shows/' . rawurlencode( CAPTIVATE_SHOW_ID ) . '/episodes',
			[
				'timeout' => 15,
				'headers' => [
					'Authorization' => 'Bearer ' . $bearer_token,
					'Accept'        => 'application/json',
				],
			]
		);
		if ( is_wp_error( $episodes_response ) ) {
			return $episodes_response;
		}
		$data     = json_decode( wp_remote_retrieve_body( $episodes_response ), true );
		$episodes = $data['episodes'] ?? [];

		// Cache for 12 hours
		set_transient( $cache_key, $episodes, 12 * HOUR_IN_SECONDS );

		return $episodes;
	}
}

/**
 * Try to match a post to an episode by title (exact first, then fuzzy).
 * Returns the episode array on match, or null.
 */
if ( ! function_exists( 'bbb_match_episode_for_post' ) ) {
	function bbb_match_episode_for_post( WP_Post $post, array $episodes, int $threshold = 82 ) {
		$title_norm = bbb_sync_normalize( get_the_title( $post ) );

		if ( ! $title_norm ) {
			return null;
		}

		// 1) Exact match on title
		foreach ( $episodes as $ep ) {
			$ep_title_norm  = bbb_sync_normalize( $ep['title'] ?? '' );
			$ep_itunes_norm = bbb_sync_normalize( $ep['itunes_title'] ?? '' );

			if ( $ep_title_norm && $ep_title_norm === $title_norm ) {
				return $ep;
			}
			if ( $ep_itunes_norm && $ep_itunes_norm === $title_norm ) {
				return $ep;
			}
		}

		// 2) Fuzzy match using similar_text (simple & built-in)
		$best = [ 'score' => 0, 'episode' => null ];

		foreach ( $episodes as $ep ) {
			$ep_title_norm  = bbb_sync_normalize( $ep['title'] ?? '' );
			$ep_itunes_norm = bbb_sync_normalize( $ep['itunes_title'] ?? '' );

			$score = 0;
			if ( $ep_title_norm ) {
				similar_text( $title_norm, $ep_title_norm, $pct1 );
				$score = max( $score, (float) $pct1 );
			}
			if ( $ep_itunes_norm ) {
				similar_text( $title_norm, $ep_itunes_norm, $pct2 );
				$score = max( $score, (float) $pct2 );
			}

			if ( $score > $best['score'] ) {
				$best = [ 'score' => $score, 'episode' => $ep ];
			}
		}

		if ( $best['episode'] && $best['score'] >= $threshold ) {
			return $best['episode'];
		}

		return null;
	}
}

/**
 * Actually apply updates to a post for a matched episode.
 */
if ( ! function_exists( 'bbb_apply_episode_to_post' ) ) {
	function bbb_apply_episode_to_post( WP_Post $post, array $episode ) {
		$post_id  = $post->ID;
		$guid     = $episode['id'] ?? '';
		$ep_type  = $episode['episode_type'] ?? '';
		$ep_num   = $episode['episode_number'] ?? null;

		if ( ! $guid ) {
			return new WP_Error( 'no_guid', 'Episode is missing an ID (guid).' );
		}

		// Save GUID to selector + meta (so your front-end can use it)
		update_field( 'captivate_episode_selector', $guid, $post_id );
		update_post_meta( $post_id, 'captivate_episode_guid', $guid );

		// Save "type" so you can show “Bonus Episode”/“Trailer”
		update_field( 'podcast_episode_type', (string) $ep_type, $post_id );

		// Save number or friendly label fallback
		if ( $ep_num !== null && $ep_num !== '' ) {
			update_field( 'podcast_episode_number', $ep_num, $post_id );
		} elseif ( in_array( $ep_type, [ 'bonus', 'trailer' ], true ) ) {
			update_field( 'podcast_episode_number', ucfirst( $ep_type ) . ' Episode', $post_id );
		}

		// Audio URL (re-uses your existing function + cache)
		if ( ! function_exists( 'bbb_get_captivate_audio_url' ) ) {
			$audio_lib = get_template_directory() . '/inc/captivate-external-audio.php';
			if ( file_exists( $audio_lib ) ) {
				require_once $audio_lib;
			}
		}
		if ( function_exists( 'bbb_get_captivate_audio_url' ) ) {
			$audio_url = bbb_get_captivate_audio_url( $guid );
			if ( $audio_url ) {
				update_field( 'captivate_audio_url', $audio_url, $post_id );
			}
		}

		return true;
	}
}

/**
 * Render the admin UI and run the sync.
 */
if ( ! function_exists( 'bbb_sync_captivate_episodes' ) ) {
	function bbb_sync_captivate_episodes() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'bigbluebox' ) );
		}

		// Defaults
		$threshold    = isset( $_POST['threshold'] ) ? max( 50, min( 100, (int) $_POST['threshold'] ) ) : 82;
		$apply        = ! empty( $_POST['apply_changes'] );
		$force        = ! empty( $_POST['force_refresh'] );
		$dry_run      = empty( $_POST['apply_changes'] ); // if not applying, it's dry-run
		$limit_output = isset( $_POST['limit_output'] ) ? max( 10, min( 1000, (int) $_POST['limit_output'] ) ) : 100;
		$resync_all   = ! empty( $_POST['resync_all'] );

		// Warn if API is globally disabled
		if ( ! bbb_sync_can_use_api() ) {
			echo '<div class="notice notice-warning"><p><strong>API Shutdown is currently ON.</strong> The Sync tool may not fetch fresh data unless you force a refresh below. You can toggle this under the “API Shutdown” tab.</p></div>';
		}

		?>
		<h2>Sync Captivate Episodes</h2>
		<p>This tool attempts to match each published <em>podcasts</em> post to a Captivate episode by title, then saves GUID, audio URL, number/type.</p>

		<form method="post" style="margin-top: 1rem;">
			<?php wp_nonce_field( 'bbb_sync_captivate_episodes_action', 'bbb_sync_nonce' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="threshold">Similarity threshold</label></th>
					<td>
						<input name="threshold" id="threshold" type="number" min="50" max="100" value="<?php echo esc_attr( $threshold ); ?>" />
						<p class="description">0–100. Titles that score at least this percentage will be auto-matched (after attempting exact matches).</p>
					</td>
				</tr>

				<tr>
					<th scope="row">Force refresh from Captivate</th>
					<td>
						<label><input type="checkbox" name="force_refresh" value="1" <?php checked( $force ); ?> /> Ignore cached episodes and fetch fresh.</label>
					</td>
				</tr>

				<tr>
					<th scope="row">Apply changes</th>
					<td>
						<label><input type="checkbox" name="apply_changes" value="1" <?php checked( $apply ); ?> /> Actually write updates to posts (not just a dry run).</label>
						<p class="description">Leave unchecked to preview (dry run). Check to update ACF fields/meta.</p>
					</td>
				</tr>

				<tr>
					<th scope="row">Re-sync all posts</th>
					<td>
						<label><input type="checkbox" name="resync_all" value="1" <?php checked( $resync_all ); ?> /> Re-sync all posts, even if already linked.</label>
						<p class="description">Check to update all posts, even those already linked to a Captivate episode.</p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="limit_output">Limit results displayed</label></th>
					<td>
						<input name="limit_output" id="limit_output" type="number" min="10" max="1000" value="<?php echo esc_attr( $limit_output ); ?>" />
					</td>
				</tr>
			</table>

			<?php submit_button( $apply ? 'Run Sync (Apply Changes)' : 'Scan (Dry Run)' ); ?>
		</form>
		<?php

		// Only proceed on submit
		if ( empty( $_POST['bbb_sync_nonce'] ) || ! wp_verify_nonce( $_POST['bbb_sync_nonce'], 'bbb_sync_captivate_episodes_action' ) ) {
			return;
		}

		// Fetch episodes
		if ( ! bbb_sync_can_use_api() && ! $force ) {
			echo '<div class="notice notice-error"><p>API is disabled and "Force refresh" was not selected. Cannot fetch episodes. Enable API or tick "Force refresh".</p></div>';
			return;
		}

		$episodes = bbb_captivate_fetch_all_episodes( $force );
		if ( is_wp_error( $episodes ) ) {
			echo '<div class="notice notice-error"><p><strong>Failed to fetch episodes:</strong> ' . esc_html( $episodes->get_error_message() ) . '</p></div>';
			return;
		}

		// Build normalized episode index ONCE
		$episodes_indexed = [];
		foreach ( $episodes as $ep ) {
			foreach ( [ 'title', 'itunes_title' ] as $field ) {
				if ( ! empty( $ep[ $field ] ) ) {
					$key = bbb_sync_normalize( $ep[ $field ] );
					if ( $key ) {
						$episodes_indexed[ $key ] = $ep;
					}
				}
			}
		}

		// Query all published podcast posts, only IDs and titles for performance
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
			echo '<div class="notice notice-info"><p>No published posts found in the “podcasts” category.</p></div>';
			return;
		}

		$updated    = 0;
		$skipped    = 0;
		$unmatched  = 0;
		$shown      = 0;

		echo '<h3>Results</h3>';
		echo '<table class="widefat striped" style="margin-top:1rem">';
		echo '<thead><tr>';
		echo '<th>Post</th><th>Matched Episode</th><th>Action</th>';
		echo '</tr></thead><tbody>';

		foreach ( $q->posts as $post_id ) {
			$post = get_post( $post_id );
			$post_title = get_the_title( $post_id );

			$existing_guid = get_post_meta( $post_id, 'captivate_episode_guid', true );
			if ( $existing_guid && ! $resync_all ) {
				$skipped++;
				if ( $shown < $limit_output ) {
					echo '<tr>';
					echo '<td><a href="' . esc_url( get_edit_post_link( $post_id ) ) . '">#' . (int) $post_id . '</a><br>' . esc_html( $post_title ) . '</td>';
					echo '<td><em>Already linked (GUID present)</em></td>';
					echo '<td>Skipped</td>';
					echo '</tr>';
					$shown++;
				}
				continue;
			}

			// Try exact by normalized title first (fast path)
			$title_norm = bbb_sync_normalize( $post_title );
			$match      = $episodes_indexed[ $title_norm ] ?? null;

			// If no exact, fuzzy
			if ( ! $match ) {
				$match = bbb_match_episode_for_post( $post, $episodes, $threshold );
			}

			if ( ! $match ) {
				$unmatched++;
				if ( $shown < $limit_output ) {
					echo '<tr>';
					echo '<td><a href="' . esc_url( get_edit_post_link( $post_id ) ) . '">#' . (int) $post_id . '</a><br>' . esc_html( $post_title ) . '</td>';
					echo '<td><strong>No match</strong></td>';
					echo '<td>—</td>';
					echo '</tr>';
					$shown++;
				}
				continue;
			}

			$action = $dry_run ? 'Would update' : 'Updated';

			if ( ! $dry_run ) {
				$res = bbb_apply_episode_to_post( $post, $match );
				if ( is_wp_error( $res ) ) {
					$action = 'Error: ' . $res->get_error_message();
				} else {
					$updated++;
				}
			}

			if ( $shown < $limit_output ) {
				echo '<tr>';
				echo '<td><a href="' . esc_url( get_edit_post_link( $post_id ) ) . '">#' . (int) $post_id . '</a><br>' . esc_html( $post_title ) . '</td>';
				echo '<td>';
				echo '“' . esc_html( $match['title'] ?? '' ) . '”';
				if ( ! empty( $match['episode_type'] ) ) {
					echo '<br><small>Type: ' . esc_html( $match['episode_type'] ) . '</small>';
				}
				if ( isset( $match['episode_number'] ) && $match['episode_number'] !== '' ) {
					echo '<br><small>Number: ' . esc_html( $match['episode_number'] ) . '</small>';
				}
				echo '</td>';
				echo '<td>' . esc_html( $action ) . '</td>';
				echo '</tr>';
				$shown++;
			}
		}
		wp_reset_postdata();

		echo '</tbody></table>';

		echo '<p><strong>Summary:</strong> ';
		echo 'Updated: ' . (int) $updated . ' &nbsp;|&nbsp; ';
		echo 'Skipped (already linked): ' . (int) $skipped . ' &nbsp;|&nbsp; ';
		echo 'Unmatched: ' . (int) $unmatched;
		echo '</p>';

		if ( $dry_run ) {
			echo '<div class="notice notice-info"><p>This was a dry run. Check “Apply changes” to write updates.</p></div>';
		}
	}
}