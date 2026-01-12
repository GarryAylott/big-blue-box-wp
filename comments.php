<?php
/**
 * The template for displaying comments
 *
 * @package Big_Blue_Box
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">
    <h2 class="title">
        <?php
        $comments_number = get_comments_number();
        if ( $comments_number > 0 ) {
                printf(
                        esc_html( _n( '%s comment so far', '%s comments so far', $comments_number, 'bigbluebox' ) ),
                        number_format_i18n( $comments_number )
                );
        } else {
                esc_html_e( 'Comments', 'bigbluebox' );
        }
        ?>
    </h2>

    <?php if (have_comments()) : ?>
        <ol class="comment-list">
            <?php
            wp_list_comments([
                'style'       => 'div',
                'short_ping'  => true,
                'avatar_size' => 64,
                'callback'    => 'custom_comments_callback',
                'max_depth'   => 5,
            ]);
            ?>
        </ol>
        <?php the_comments_navigation(); ?>
    <?php else : ?>
        <div class="no-comments flex">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/images/no-comments-dr.webp' ); ?>" width="160" height="169" alt="<?php echo esc_attr__( 'No comments', 'bigbluebox' ); ?>" loading="lazy">
            <p>
                <?php
                echo wp_kses_post(
                        sprintf(
                                __( '<span class="bold">%1$s</span><br />%2$s', 'bigbluebox' ),
                                esc_html__( 'No one has left a comment yet.', 'bigbluebox' ),
                                esc_html__( 'Be the first and get the conversation going.', 'bigbluebox' )
                        )
                );
                ?>
            </p>
        </div>
    <?php endif; ?>

    <?php if ( comments_open() ) :
        $commenter = wp_get_current_commenter();
        $fields    = [];

        // Only show name/email fields for logged-out users.
        if ( ! is_user_logged_in() ) {
                $fields = [
                        'author_email_group' => sprintf(
                                '<div class="comment-form-fields flex">
                                        <p class="comment-form-author form-input-group">
                                                <label for="author">%1$s</label>
                                                <input id="author" name="author" type="text" placeholder="%2$s" value="%3$s" required />
                                        </p>
                                        <p class="comment-form-email form-input-group">
                                                <label for="email">%4$s</label>
                                                <input id="email" name="email" type="email" placeholder="%5$s" value="%6$s" required />
                                        </p>
                                </div>',
                                esc_html__( 'Your name', 'bigbluebox' ),
                                esc_attr__( 'John Smith', 'bigbluebox' ),
                                esc_attr( $commenter['comment_author'] ),
                                esc_html__( 'Your email address', 'bigbluebox' ),
                                esc_attr__( 'johnsmith@gallifrey.com', 'bigbluebox' ),
                                esc_attr( $commenter['comment_author_email'] )
                        ),
                        'cookies'             => sprintf(
                                '<div class="comment-form-footer">
                                        <p class="comment-form-cookies-consent small flex">
                                                <input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"%1$s />
                                                <label for="wp-comment-cookies-consent">%2$s</label>
                                        </p>',
                                empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"',
                                esc_html__( 'Save my name and email in this browser for the next time I comment.', 'bigbluebox' )
                        ),
                ];
        }

        comment_form(
                [
                        'fields'               => $fields,
                        'comment_field'        => sprintf(
                                '<p class="comment-form-comment form-input-group">
                                        <label for="comment">%1$s</label>
                                        <textarea id="comment" name="comment" placeholder="%2$s" required></textarea>
                                </p>',
                                esc_html__( 'Comment', 'bigbluebox' ),
                                esc_attr__( 'Your comment here…', 'bigbluebox' )
                        ),
                        'class_form'           => 'custom-comment-form',
                        'title_reply'          => esc_html__( 'Leave a Comment', 'bigbluebox' ),
                        'title_reply_before'   => '<h3 class="comment-form-title">',
                        'title_reply_after'    => '</h3>',
                        'comment_notes_before' => sprintf(
                                '<p class="comment-form-intro small">%s</p>',
                                wp_kses(
                                        sprintf(
                                                /* translators: %s: Code of Conduct link. */
                                                __( 'All comments are manually moderated in accordance with our <a class="link-alt" href="%s">Code of Conduct</a>.<br />Your email address will <strong>not</strong> be published. All fields are required.', 'bigbluebox' ),
                                                esc_url( home_url( '/code-of-conduct' ) )
                                        ),
                                        [
                                                'a'      => [
                                                        'class' => true,
                                                        'href'  => true,
                                                ],
                                                'br'     => [],
                                                'strong' => [],
                                        ]
                                )
                        ),
                        'label_submit'         => esc_html__( 'Submit Comment', 'bigbluebox' ),
                        'cancel_reply_link'    => esc_html__( 'Cancel reply', 'bigbluebox' ),
                        'submit_button'        => '<button type="submit" class="button">%4$s</button>',
                        'submit_field'         => '%1$s %2$s</div>',
                        'logged_in_as'         => sprintf(
                                '<p class="logged-in-as comment-form-intro small">%s</p>',
                                sprintf(
                                        esc_html__( 'Logged in as %s.', 'bigbluebox' ),
                                        esc_html( wp_get_current_user()->display_name )
                                )
                        ),
                ]
        );
    endif; ?>
