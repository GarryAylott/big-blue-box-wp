<?php
/**
 * Helpers for rendering author social links sourced via ACF.
 *
 * @package Big_Blue_Box
 */

/**
 * Map of supported social networks and their icon metadata.
 *
 * @return array<string, array<string, mixed>>
 */
function bbb_get_author_social_icon_map() {
	$map = array(
		'twitter'  => array(
			'label'  => esc_html__( 'X (Twitter)', 'bigbluebox' ),
			'icon'   => 'social-icon-x.svg',
			'class'  => 'author-social-x',
			'width'  => 24,
			'height' => 24,
		),
		'instagram' => array(
			'label'  => esc_html__( 'Instagram', 'bigbluebox' ),
			'icon'   => 'social-icon-insta.svg',
			'class'  => 'author-social-insta',
			'width'  => 24,
			'height' => 24,
		),
		'facebook'  => array(
			'label'  => esc_html__( 'Facebook', 'bigbluebox' ),
			'icon'   => 'social-icon-fb.svg',
			'class'  => 'author-social-fb',
			'width'  => 24,
			'height' => 24,
		),
		'tiktok'    => array(
			'label'  => esc_html__( 'TikTok', 'bigbluebox' ),
			'icon'   => 'social-icon-tiktok.svg',
			'class'  => 'author-social-tiktok',
			'width'  => 24,
			'height' => 24,
		),
	);

	return apply_filters( 'bigbluebox_author_social_icon_map', $map );
}

/**
 * Retrieve the configured social links for an author.
 *
 * @param int $user_id WordPress user ID.
 *
 * @return array<int, array<string, mixed>>
 */
function bbb_get_author_social_links( $user_id ) {
	$user_id = absint( $user_id );

	if ( ! $user_id ) {
		return array();
	}

	$user_key     = 'user_' . $user_id;
	$icon_map     = bbb_get_author_social_icon_map();
	$social_links = array();

	foreach ( $icon_map as $field_key => $icon_meta ) {
		$link_url = get_field( $field_key, $user_key );

		if ( is_array( $link_url ) && isset( $link_url['url'] ) ) {
			$link_url = $link_url['url'];
		}

		$link_url = is_string( $link_url ) ? trim( $link_url ) : '';

		if ( '' === $link_url ) {
			continue;
		}

		$social_links[] = array(
			'network'   => $field_key,
			'url'       => esc_url_raw( $link_url ),
			'label'     => $icon_meta['label'],
			'icon_type' => 'asset',
			'icon'      => $icon_meta['icon'],
			'class'     => $icon_meta['class'],
			'width'     => isset( $icon_meta['width'] ) ? absint( $icon_meta['width'] ) : 24,
			'height'    => isset( $icon_meta['height'] ) ? absint( $icon_meta['height'] ) : 24,
		);
	}

	$rss_link = get_author_feed_link( $user_id );

	if ( $rss_link ) {
		$social_links[] = array(
			'network'   => 'rss',
			'url'       => esc_url_raw( $rss_link ),
			'label'     => esc_html__( 'RSS feed', 'bigbluebox' ),
			'icon_type' => 'lucide',
			'icon'      => 'rss',
			'class'     => 'author-social-rss',
			'width'     => 24,
			'height'    => 24,
		);
	}

	return apply_filters( 'bigbluebox_author_social_links', $social_links, $user_id );
}

/**
 * Map lucide icons to their SVG instructions for inline rendering.
 *
 * @return array<string, array<int, array<string, mixed>>>
 */
function bbb_get_lucide_icon_map() {
	$icons = array(
		'rss' => array(
			array(
				'tag'        => 'path',
				'attributes' => array(
					'd' => 'M4 11a9 9 0 0 1 9 9',
				),
			),
			array(
				'tag'        => 'path',
				'attributes' => array(
					'd' => 'M4 4a16 16 0 0 1 16 16',
				),
			),
			array(
				'tag'        => 'circle',
				'attributes' => array(
					'cx' => '5',
					'cy' => '19',
					'r'  => '1',
				),
			),
		),
	);

	return apply_filters( 'bigbluebox_lucide_icon_map', $icons );
}

/**
 * Build SVG markup for a lucide icon using inline paths so the output
 * does not rely on JS for hydration.
 *
 * @param string $name   Icon name.
 * @param string $class  Additional classes.
 * @param array  $args   Width/height overrides.
 *
 * @return string
 */
function bbb_get_lucide_svg_markup( $name, $class = '', $args = array() ) {
	$icons = bbb_get_lucide_icon_map();
	$name  = strtolower( sanitize_key( $name ) );

	if ( empty( $icons[ $name ] ) ) {
		return '';
	}

	$defaults = array(
		'width'  => 24,
		'height' => 24,
	);

	$args = wp_parse_args( $args, $defaults );

	$attributes = array(
		'class'            => trim( 'lucide lucide-' . $name . ' ' . $class ),
		'aria-hidden'      => 'true',
		'focusable'        => 'false',
		'width'            => absint( $args['width'] ),
		'height'           => absint( $args['height'] ),
		'viewBox'          => '0 0 24 24',
		'fill'             => 'none',
		'stroke'           => 'currentColor',
		'stroke-width'     => '2',
		'stroke-linecap'   => 'round',
		'stroke-linejoin'  => 'round',
	);

	$attribute_markup = array();

	foreach ( $attributes as $attribute_name => $attribute_value ) {
		$attribute_markup[] = sprintf(
			'%1$s="%2$s"',
			esc_attr( $attribute_name ),
			esc_attr( (string) $attribute_value )
		);
	}

	$child_markup = array();

	foreach ( $icons[ $name ] as $shape ) {
		if ( empty( $shape['tag'] ) ) {
			continue;
		}

		$tag        = tag_escape( $shape['tag'] );
		$attributes = array();

		if ( ! empty( $shape['attributes'] ) && is_array( $shape['attributes'] ) ) {
			foreach ( $shape['attributes'] as $attribute_name => $attribute_value ) {
				$attributes[] = sprintf(
					'%1$s="%2$s"',
					esc_attr( $attribute_name ),
					esc_attr( (string) $attribute_value )
				);
			}
		}

		$child_markup[] = sprintf(
			'<%1$s%2$s />',
			$tag,
			empty( $attributes ) ? '' : ' ' . implode( ' ', $attributes )
		);
	}

	return sprintf(
		'<svg %1$s>%2$s</svg>',
		implode( ' ', $attribute_markup ),
		implode( '', $child_markup )
	);
}

/**
 * Render social link markup for an author.
 *
 * @param int   $user_id WordPress user ID.
 * @param array $args    Optional args controlling markup.
 *
 * @return string
 */
function bbb_render_author_social_links( $user_id, $args = array() ) {
	$links = bbb_get_author_social_links( $user_id );

	if ( empty( $links ) ) {
		return '';
	}

	$defaults = array(
		'container'             => 'div',
		'container_class'       => '',
		'container_attributes'  => array(),
		'item_element'          => '',
		'item_class'            => '',
		'link_class'            => 'has-external-icon',
		'link_rel'              => 'noopener',
		'link_target'           => '_blank',
		'aria_label_pattern'    => '%s',
		'author_name'           => '',
		'icon_class'            => 'img-hover',
		'lucide_class'          => 'icon-bold',
	);

	$args = wp_parse_args( $args, $defaults );

	$container_open  = '';
	$container_close = '';

	if ( ! empty( $args['container'] ) ) {
		$attributes = array();

		if ( ! empty( $args['container_class'] ) ) {
			$attributes['class'] = $args['container_class'];
		}

		if ( ! empty( $args['container_attributes'] ) && is_array( $args['container_attributes'] ) ) {
			$attributes = array_merge( $attributes, $args['container_attributes'] );
		}

		$attribute_parts = array();

		foreach ( $attributes as $attribute_name => $attribute_value ) {
			if ( '' === $attribute_value ) {
				continue;
			}

			$attribute_parts[] = sprintf(
				'%1$s="%2$s"',
				esc_attr( $attribute_name ),
				esc_attr( $attribute_value )
			);
		}

		$container_tag = tag_escape( $args['container'] );
		$container_open = sprintf(
			'<%1$s%2$s>',
			$container_tag,
			empty( $attribute_parts ) ? '' : ' ' . implode( ' ', $attribute_parts )
		);
		$container_close = sprintf( '</%s>', $container_tag );
	}

	$items_markup  = array();
	$icon_base_uri = trailingslashit( get_template_directory_uri() ) . 'images/icons/';

	foreach ( $links as $link ) {
		$aria_label_pattern = (string) $args['aria_label_pattern'];

		if ( false !== strpos( $aria_label_pattern, '%2$s' ) ) {
			$link_aria_label = sprintf(
				$aria_label_pattern,
				$args['author_name'],
				$link['label']
			);
		} else {
			$link_aria_label = sprintf(
				$aria_label_pattern,
				$link['label']
			);
		}

		$icon_markup = '';

		if ( 'lucide' === $link['icon_type'] ) {
			$lucide_class = trim( (string) $args['lucide_class'] . ' ' . $link['class'] );
			$icon_markup  = bbb_get_lucide_svg_markup(
				$link['icon'],
				$lucide_class,
				array(
					'width'  => isset( $link['width'] ) ? absint( $link['width'] ) : 24,
					'height' => isset( $link['height'] ) ? absint( $link['height'] ) : 24,
				)
			);

			if ( '' === $icon_markup ) {
				$icon_markup = sprintf(
					'<i data-lucide="%1$s" class="%2$s" aria-hidden="true"></i>',
					esc_attr( $link['icon'] ),
					esc_attr( $lucide_class )
				);
			}
		} else {
			$icon_classes = trim( $args['icon_class'] . ' ' . $link['class'] );
			$icon_markup  = sprintf(
				'<img src="%1$s" class="%2$s" alt="%3$s" width="%4$d" height="%5$d">',
				esc_url( $icon_base_uri . ltrim( $link['icon'], '/' ) ),
				esc_attr( $icon_classes ),
				esc_attr( $link['label'] ),
				isset( $link['width'] ) ? absint( $link['width'] ) : 24,
				isset( $link['height'] ) ? absint( $link['height'] ) : 24
			);
		}

		$link_attributes = array(
			'href'       => esc_url( $link['url'] ),
			'class'      => trim( $args['link_class'] ),
			'target'     => $args['link_target'],
			'rel'        => $args['link_rel'],
			'aria-label' => $link_aria_label,
		);

		if ( empty( $link_attributes['class'] ) ) {
			unset( $link_attributes['class'] );
		}

		if ( empty( $link_attributes['target'] ) ) {
			unset( $link_attributes['target'] );
		}

		if ( empty( $link_attributes['rel'] ) ) {
			unset( $link_attributes['rel'] );
		}

		$link_attribute_parts = array();

		foreach ( $link_attributes as $attribute_name => $attribute_value ) {
			$link_attribute_parts[] = sprintf(
				'%1$s="%2$s"',
				esc_attr( $attribute_name ),
				esc_attr( $attribute_value )
			);
		}

		$link_markup = sprintf(
			'<a %1$s>%2$s</a>',
			implode( ' ', $link_attribute_parts ),
			$icon_markup
		);

		if ( ! empty( $args['item_element'] ) ) {
			$item_tag   = tag_escape( $args['item_element'] );
			$item_class = trim( $args['item_class'] );

			$items_markup[] = sprintf(
				'<%1$s%2$s>%3$s</%1$s>',
				$item_tag,
				$item_class ? ' class="' . esc_attr( $item_class ) . '"' : '',
				$link_markup
			);
		} else {
			$items_markup[] = $link_markup;
		}
	}

	$markup = implode( '', $items_markup );

	if ( $container_open ) {
		$markup = $container_open . $markup . $container_close;
	}

	return $markup;
}
