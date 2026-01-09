<?php
/**
 * Captivate ACF integration.
 *
 * @package Big_Blue_Box
 */

add_filter( 'acf/load_field/name=captivate_episode_selector', function( $field ) {

	// Optional: clear cache manually
	if ( isset( $_GET['clear_captivate_cache'] ) && current_user_can( 'manage_options' ) ) {
		$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_key( $_GET['_wpnonce'] ) : '';
		$allow = $nonce && wp_verify_nonce( $nonce, 'bbb_clear_captivate_cache' );
		if ( ! $allow && 'local' === wp_get_environment_type() ) {
			$allow = true;
		}
		if ( $allow ) {
			delete_transient( 'captivate_episodes_cache' );
			bbb_log( '♻️ Captivate episode cache cleared manually.' );
		} else {
			bbb_log( '⚠️ Captivate cache clear blocked (missing or invalid nonce).' );
		}
	}

	if ( ! defined( 'CAPTIVATE_API_TOKEN' ) || ! defined( 'CAPTIVATE_USER_ID' ) || ! defined( 'CAPTIVATE_SHOW_ID' ) ) {
		bbb_log( '❌ Missing Captivate credentials.' );
		return $field;
	}

	$all_episodes = get_transient( 'captivate_episodes_cache' );

	if ( false === $all_episodes ) {
		// Step 1: Authenticate
		$auth_response = wp_remote_post( 'https://api.captivate.fm/authenticate/token', [
			'timeout' => 10,
			'body'    => [
				'username' => CAPTIVATE_USER_ID,
				'token'    => CAPTIVATE_API_TOKEN,
			],
		] );

		if ( is_wp_error( $auth_response ) ) {
			bbb_log( '❌ Auth request failed: ' . $auth_response->get_error_message() );
			return $field;
		}

		$auth_code = wp_remote_retrieve_response_code( $auth_response );
		if ( $auth_code < 200 || $auth_code >= 300 ) {
			bbb_log( '❌ Auth failed. HTTP status: ' . $auth_code );
			return $field;
		}

		$auth_data    = json_decode( wp_remote_retrieve_body( $auth_response ), true );
		if ( ! is_array( $auth_data ) ) {
			bbb_log( '❌ Auth failed. Invalid JSON response.' );
			return $field;
		}
		$bearer_token = $auth_data['user']['token'] ?? null;

		if ( ! $bearer_token ) {
			bbb_log( '❌ Auth failed.' );
			return $field;
		}

		// Step 2: Fetch all episodes
		$episodes_response = wp_remote_get( 'https://api.captivate.fm/shows/' . urlencode( CAPTIVATE_SHOW_ID ) . '/episodes', [
			'timeout' => 15,
			'headers' => [
				'Authorization' => 'Bearer ' . $bearer_token,
				'Accept'        => 'application/json',
			],
		] );

		if ( is_wp_error( $episodes_response ) ) {
			bbb_log( '❌ Error fetching episodes: ' . $episodes_response->get_error_message() );
			return $field;
		}

		$episodes_code = wp_remote_retrieve_response_code( $episodes_response );
		if ( $episodes_code < 200 || $episodes_code >= 300 ) {
			bbb_log( '❌ Error fetching episodes. HTTP status: ' . $episodes_code );
			return $field;
		}

		$data         = json_decode( wp_remote_retrieve_body( $episodes_response ), true );
		$all_episodes = $data['episodes'] ?? [];

		set_transient( 'captivate_episodes_cache', $all_episodes, HOUR_IN_SECONDS );
	}

	// Show latest 5
	// $recent_episodes = array_slice( $all_episodes, 0, 15 );

	$field['choices'] = [];

	foreach ( $all_episodes as $ep ) {
		$title = $ep['title'] ?? 'Untitled Episode';
		$guid  = $ep['id'] ?? '';
		if ( $guid ) {
			$field['choices'][ $guid ] = $title;
		}
	}

	return $field;
});


// Populate ACF fields on save
add_action( 'acf/save_post', function( $post_id ) {

	if ( get_post_type( $post_id ) !== 'post' ) {
		return;
	}

	if ( get_option( 'disable_captivate_api' ) ) {
		bbb_log( '🛑 API disabled — skipping acf/save_post processing.' );
		return;
	}

	$guid = get_field( 'captivate_episode_selector', $post_id );
	if ( ! $guid ) {
		bbb_log( '❌ No GUID selected in captivate_episode_selector.' );
		return;
	}

	require_once get_template_directory() . '/inc/captivate-external-audio.php';

	$audio_url = bbb_get_captivate_audio_url( $guid );
	if ( $audio_url ) {
		update_field( 'captivate_audio_url', $audio_url, $post_id );
		update_post_meta( $post_id, 'captivate_episode_guid', $guid );
	} else {
		bbb_log( '❌ Failed to fetch audio URL.' );
	}

	// Auth to fetch episode data
	if ( ! defined( 'CAPTIVATE_API_TOKEN' ) || ! defined( 'CAPTIVATE_USER_ID' ) ) {
		bbb_log( '❌ Missing Captivate credentials.' );
		return;
	}

	$auth_response = wp_remote_post( 'https://api.captivate.fm/authenticate/token', [
		'timeout' => 10,
		'body'    => [
			'username' => CAPTIVATE_USER_ID,
			'token'    => CAPTIVATE_API_TOKEN,
		],
	] );

	if ( is_wp_error( $auth_response ) ) {
		bbb_log( '❌ Auth request failed on save_post: ' . $auth_response->get_error_message() );
		return;
	}

	$auth_code = wp_remote_retrieve_response_code( $auth_response );
	if ( $auth_code < 200 || $auth_code >= 300 ) {
		bbb_log( '❌ Auth failed on save_post. HTTP status: ' . $auth_code );
		return;
	}

	$auth_data    = json_decode( wp_remote_retrieve_body( $auth_response ), true );
	if ( ! is_array( $auth_data ) ) {
		bbb_log( '❌ Auth failed on save_post. Invalid JSON response.' );
		return;
	}
	$bearer_token = $auth_data['user']['token'] ?? null;

	if ( ! $bearer_token ) {
		bbb_log( '❌ Auth failed on save_post.' );
		return;
	}

	$episode_response = wp_remote_get( 'https://api.captivate.fm/episodes/' . rawurlencode( $guid ), [
		'timeout' => 10,
		'headers' => [
			'Authorization' => 'Bearer ' . $bearer_token,
			'Accept'        => 'application/json',
		],
	] );

	if ( is_wp_error( $episode_response ) ) {
		bbb_log( '❌ Episode request failed on save_post: ' . $episode_response->get_error_message() );
		return;
	}

	$episode_code = wp_remote_retrieve_response_code( $episode_response );
	if ( $episode_code < 200 || $episode_code >= 300 ) {
		bbb_log( '❌ Episode request failed on save_post. HTTP status: ' . $episode_code );
		return;
	}

	$response_body = wp_remote_retrieve_body( $episode_response );
	$data          = json_decode( $response_body, true );
	if ( ! is_array( $data ) ) {
		bbb_log( '❌ Episode response invalid JSON on save_post.' );
		return;
	}

	$episode = $data['episode'] ?? null;

	// Extract episode_type and episode_number safely
	$episode_type   = null;
	$episode_number = null;
	if ( is_array( $episode ) ) {
		$episode_type   = $episode['episode_type'] ?? null;
		$episode_number = $episode['episode_number'] ?? null;
	} else {
		$episode_type = $data['episode_type'] ?? null;
	}
	$episode_type = is_string( $episode_type ) ? $episode_type : '';

	// Always update episode type if available
	if ( $episode_type ) {
		$friendly_type = $episode_type;
		if ( strtolower( $episode_type ) === 'full' ) {
			$friendly_type = 'Standard Weekly Episode';
		} elseif ( strtolower( $episode_type ) === 'bonus' ) {
			$friendly_type = 'Bonus Episode';
		} else {
			$friendly_type = ucfirst( strtolower( $episode_type ) );
		}
		update_field( 'podcast_episode_type', $friendly_type, $post_id );
		bbb_log( "📦 Saved episode type: $friendly_type" );
	}

	if ( $episode_number !== null && $episode_number !== '' ) {
		update_field( 'podcast_episode_number', $episode_number, $post_id );
		bbb_log( "✅ Episode number set to: $episode_number" );
	} else {
		$normalized_type = $episode_type ? ucfirst( strtolower( $episode_type ) ) : '';
		if ( in_array( $normalized_type, [ 'Bonus', 'Trailer' ], true ) ) {
			update_field( 'podcast_episode_number', 'N/A', $post_id );
			bbb_log( "⚠️ $episode_type episode – set episode number to N/A (fallback, episode data missing or malformed)." );
		} else {
			bbb_log( '❌ Failed to set episode number. Episode data missing or malformed.' );
			bbb_log( print_r( $data, true ) );
		}
	}

}, 20 );


// Make certain fields readonly
add_filter( 'acf/load_field', function( $field ) {
	$read_only_fields = [
		'captivate_audio_url',
		'podcast_episode_number',
		'podcast_episode_type',
		'captivate_media_id',
		'podcast_transcript',
	];

	if ( in_array( $field['name'], $read_only_fields, true ) ) {
		$field['disabled'] = true;
	}

	return $field;
});
