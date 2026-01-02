<?php
/**
 * Hero background helpers.
 *
 * @package Big_Blue_Box
 */

/**
 * Get a random hero background image URL.
 *
 * @param array $args Optional args: 'pool' (array of filenames), 'default' (string filename).
 * @return string
 */
function bbb_get_random_hero_bg( $args = [] ) {
	$defaults = [
		'pool'    => [
			'pagebg_tardis-int-1.webp',
			'pagebg_tardis-int-2.webp',
			'pagebg_tardis-int-3.webp',
			'pagebg_tardis-int-4.webp',
			'pagebg_tardis-int-5.webp',
			'pagebg_tardis-int-6.webp',
			'pagebg_tardis-int-7.webp',
			'pagebg_tardis-int-8.webp',
		],
		'default' => 'pagebg_default.webp',
	];

	$args = wp_parse_args( $args, $defaults );
	$pool = array_values( array_filter( (array) $args['pool'] ) );

	if ( empty( $pool ) ) {
		return get_template_directory_uri() . '/images/' . $args['default'];
	}

	$random_key = array_rand( $pool );
	return get_template_directory_uri() . '/images/' . $pool[ $random_key ];
}
