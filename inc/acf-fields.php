<?php
/**
 * Captivate ACF integration.
 *
 * @package Big_Blue_Box
 */

add_filter( 'acf/load_field/name=captivate_episode_selector', function( $field ) {

	// Optional: clear cache manually
	if ( isset( $_GET['clear_captivate_cache'] ) && current_user_can( 'manage_options' ) ) {
		delete_transient( 'captivate_episodes_cache' );
		error_log('♻️ Captivate episode cache cleared manually.');
	}

	if ( ! defined( 'CAPTIVATE_API_TOKEN' ) || ! defined( 'CAPTIVATE_USER_ID' ) || ! defined( 'CAPTIVATE_SHOW_ID' ) ) {
		error_log('❌ Missing Captivate credentials.');
		return $field;
	}

	$all_episodes = get_transient( 'captivate_episodes_cache' );

	if ( false === $all_episodes ) {
		// Step 1: Authenticate
		$auth_response = wp_remote_post( 'https://api.captivate.fm/authenticate/token', [
			'body' => [
				'username' => CAPTIVATE_USER_ID,
				'token'    => CAPTIVATE_API_TOKEN,
			],
		] );

		$auth_data    = json_decode( wp_remote_retrieve_body( $auth_response ), true );
		$bearer_token = $auth_data['user']['token'] ?? null;

		if ( ! $bearer_token ) {
			error_log('❌ Auth failed.');
			return $field;
		}

		// Step 2: Fetch all episodes
		$episodes_response = wp_remote_get( 'https://api.captivate.fm/shows/' . urlencode( CAPTIVATE_SHOW_ID ) . '/episodes', [
			'headers' => [
				'Authorization' => 'Bearer ' . $bearer_token,
				'Accept'        => 'application/json',
			],
		] );

		if ( is_wp_error( $episodes_response ) ) {
			error_log( '❌ Error fetching episodes: ' . $episodes_response->get_error_message() );
			return $field;
		}

		$data         = json_decode( wp_remote_retrieve_body( $episodes_response ), true );
		$all_episodes = $data['episodes'] ?? [];

		set_transient( 'captivate_episodes_cache', $all_episodes, HOUR_IN_SECONDS );
	}

	// Show latest 5
	$recent_episodes = array_slice( $all_episodes, 0, 15 );

	$field['choices'] = [];

	foreach ( $recent_episodes as $ep ) {
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
		error_log( '🛑 API disabled — skipping acf/save_post processing.' );
		return;
	}

	$guid = get_field( 'captivate_episode_selector', $post_id );
	if ( ! $guid ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( '❌ No GUID selected in captivate_episode_selector.' );
		}
		return;
	}

	require_once get_template_directory() . '/inc/captivate-external-audio.php';

	$audio_url = bbb_get_captivate_audio_url( $guid );
	if ( $audio_url ) {
		update_field( 'captivate_audio_url', $audio_url, $post_id );
		update_post_meta( $post_id, 'captivate_episode_guid', $guid );
	} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( '❌ Failed to fetch audio URL.' );
	}

	// Auth to fetch episode data
	if ( ! defined( 'CAPTIVATE_API_TOKEN' ) || ! defined( 'CAPTIVATE_USER_ID' ) ) {
		error_log( '❌ Missing Captivate credentials.' );
		return;
	}

	$auth_response = wp_remote_post( 'https://api.captivate.fm/authenticate/token', [
		'body' => [
			'username' => CAPTIVATE_USER_ID,
			'token'    => CAPTIVATE_API_TOKEN,
		],
	] );

	$auth_data    = json_decode( wp_remote_retrieve_body( $auth_response ), true );
	$bearer_token = $auth_data['user']['token'] ?? null;

	if ( ! $bearer_token ) {
		error_log( '❌ Auth failed on save_post.' );
		return;
	}

	$episode_response = wp_remote_get( "https://api.captivate.fm/episodes/$guid", [
		'headers' => [
			'Authorization' => 'Bearer ' . $bearer_token,
			'Accept'        => 'application/json',
		],
	] );

	$response_body = wp_remote_retrieve_body( $episode_response );
	$data          = json_decode( $response_body, true );

	$episode = $data['episode'] ?? null;

// Extract episode_type and episode_number safely
$episode_type = null;
$episode_number = null;
if ( is_array( $episode ) ) {
	$episode_type = $episode['episode_type'] ?? null;
	$episode_number = $episode['episode_number'] ?? null;
} else {
	$episode_type = $data['episode_type'] ?? null;
}

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
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( "📦 Saved episode type: $friendly_type" );
	}
}

if ( $episode_number !== null && $episode_number !== '' ) {
	update_field( 'podcast_episode_number', $episode_number, $post_id );
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( "✅ Episode number set to: $episode_number" );
	}
} else {
	$normalized_type = ucfirst( strtolower( $episode_type ) );
	if ( in_array( $normalized_type, [ 'Bonus', 'Trailer' ], true ) ) {
		update_field( 'podcast_episode_number', 'N/A', $post_id );
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( "⚠️ $episode_type episode – set episode number to N/A (fallback, episode data missing or malformed)." );
		}
	} else {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( '❌ Failed to set episode number. Episode data missing or malformed.' );
			error_log( print_r( $data, true ) );
		}
	}
}

}, 20 );


// Make certain fields readonly
add_filter( 'acf/load_field', function( $field ) {
	$read_only_fields = [
		'captivate_audio_url',
		'podcast_episode_number',
		'podcast_episode_type',
	];

	if ( in_array( $field['name'], $read_only_fields, true ) ) {
		$field['disabled'] = true;
	}

	return $field;
});
