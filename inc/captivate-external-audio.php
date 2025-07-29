<?php
/**
 * Retrieve episode audio from Captivate's API.
 *
 * @package Big_Blue_Box
 */

function bbb_get_captivate_audio_url( $guid ) {
	if ( ! $guid ) {
		error_log( '❌ GUID missing in bbb_get_captivate_audio_url.' );
		return false;
	}

	// ✅ Check if API is globally disabled via admin
	if ( get_option( 'disable_captivate_api' ) ) {
		error_log( '🛑 API disabled via admin setting.' );
		error_log( '🔊 Using fallback audio URL for local dev.' );
		return 'https://big-blue-box.local/wp-content/uploads/2025/07/BBB-Ep426.mp3';
	}

	$cache_key = 'captivate_audio_' . md5( $guid );
	$cached = get_transient( $cache_key );
	if ( $cached ) {
		error_log( '✅ Returning cached audio URL.' );
		return $cached;
	}

	// Avoid API calls outside admin or save_post
	if (
		! is_admin() &&
		! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) &&
		! ( defined( 'REST_REQUEST' ) && REST_REQUEST )
	) {
		error_log( '🚫 Skipping API fetch – front-end context and no cache.' );
		return false;
	}

	error_log( '🚀 Starting auth request...' );

	$auth_response = wp_remote_post( 'https://api.captivate.fm/authenticate/token', [
		'body' => [
			'username' => CAPTIVATE_USER_ID,
			'token'    => CAPTIVATE_API_TOKEN,
		],
	] );

	if ( is_wp_error( $auth_response ) ) {
		error_log( '❌ Auth request failed: ' . $auth_response->get_error_message() );
		return false;
	}

	$auth_body = wp_remote_retrieve_body( $auth_response );
	$auth_data = json_decode( $auth_body, true );
	$bearer_token = $auth_data['user']['token'] ?? null;

	if ( ! $bearer_token ) {
		error_log( '❌ Captivate auth failed. Response body: ' . $auth_body );
		return false;
	}

	error_log( '✅ Got bearer token.' );

	// Fetch episode
	$episode_response = wp_remote_get( "https://api.captivate.fm/episodes/$guid", [
		'headers' => [ 'Authorization' => 'Bearer ' . $bearer_token ],
	] );

	if ( is_wp_error( $episode_response ) ) {
		error_log( '❌ Episode request failed: ' . $episode_response->get_error_message() );
		return false;
	}

	$episode_data = json_decode( wp_remote_retrieve_body( $episode_response ), true );
	$media_id = $episode_data['episode']['media_id'] ?? null;

	if ( ! $media_id ) {
		error_log( '❌ No media_id found in episode. Will retry next time.' );
		return false;
	}

	error_log( "✅ Got media_id: $media_id" );

	$media_response = wp_remote_get( "https://api.captivate.fm/media/$media_id", [
		'headers' => [ 'Authorization' => 'Bearer ' . $bearer_token ],
	] );

	if ( is_wp_error( $media_response ) ) {
		error_log( '❌ Media request failed: ' . $media_response->get_error_message() );
		return false;
	}

	$media_data = json_decode( wp_remote_retrieve_body( $media_response ), true );
	$audio_url = $media_data['media']['media_url'] ?? null;

	if ( $audio_url ) {
		set_transient( $cache_key, $audio_url, DAY_IN_SECONDS );
		error_log( '✅ Audio URL found and cached.' );
		return $audio_url;
	}

	error_log( '❌ No media_url found in media response.' );
	return false;
}
