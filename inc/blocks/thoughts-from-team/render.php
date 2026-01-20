<?php
/**
 * Template used via block.json "render" to output the Thoughts from the Team block on the front end.
 *
 * Variables available from register_block_type_from_metadata():
 * - $attributes (array)
 * - $content (string)
 * - $block (WP_Block)
 */

$entries = isset( $attributes['entries'] ) && is_array( $attributes['entries'] ) ? $attributes['entries'] : array();

if ( empty( $entries ) ) {
	return;
}
?>
<section class="team-thoughts">
	<h2 class="team-thoughts__heading">
		<?php echo esc_html__( 'Thoughts from the team', 'bigbluebox' ); ?>
	</h2>
	<?php foreach ( $entries as $entry ) :
		$user_id = isset( $entry['userId'] ) ? absint( $entry['userId'] ) : 0;
		$text    = isset( $entry['content'] ) ? (string) $entry['content'] : '';

		if ( ! $user_id || '' === trim( $text ) ) {
			continue;
		}

		$user = get_userdata( $user_id );
		if ( ! $user ) {
			continue;
		}

		$first_name = get_user_meta( $user_id, 'first_name', true );
		if ( empty( $first_name ) ) {
			$display_name_parts = explode( ' ', $user->display_name );
			$first_name         = $display_name_parts ? $display_name_parts[0] : $user->display_name;
		}

		$avatar = get_avatar(
			$user_id,
			72,
			'',
			sprintf(
				/* translators: %s is the contributor's first name. */
				esc_attr__( '%s', 'bigbluebox' ),
				$first_name
			),
			array( 'class' => 'author-avatar-img' )
		);
		?>
		<div class="team-thoughts__entry">
			<div class="team-thoughts__author">
				<?php echo $avatar;?>
				<h4 class="team-thoughts__name">
					<?php echo esc_html( $first_name ); ?>
				</h4>
			</div>
			<div class="team-thoughts__content flow-small">
				<?php echo wpautop( wp_kses_post( $text ) ); ?>
			</div>
		</div>
	<?php endforeach; ?>
</section>
