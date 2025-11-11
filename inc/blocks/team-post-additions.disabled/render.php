<?php
/**
 * Render callback for Team Post Additions block
 */

function bbb_render_team_post_additions_block( $attributes ) {
    $user_id = isset( $attributes['userId'] ) ? absint( $attributes['userId'] ) : 0;
    $content = isset( $attributes['content'] ) ? sanitize_textarea_field( $attributes['content'] ) : '';

    if ( ! $user_id || ! $content ) {
        return '';
    }

    $user = get_userdata( $user_id );
    if ( ! $user ) {
        return '';
    }

    ob_start(); ?>
    <div class="team-post-additions">
        <div class="team-post-additions__author">
            <?php echo get_avatar( $user->ID, 48 ); ?>
            <span class="team-post-additions__name"><?php echo esc_html( $user->display_name ); ?></span>
        </div>
        <div class="team-post-additions__content">
            <p><?php echo nl2br( esc_html( $content ) ); ?></p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}