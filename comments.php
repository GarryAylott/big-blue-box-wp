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
        if ($comments_number > 0) {
            printf(
                _n('%s comment so far', '%s comments so far', $comments_number, 'big-blue-box'),
                number_format_i18n($comments_number)
            );
        } else {
            echo 'Comments';
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
            <img src="<?php echo get_template_directory_uri(); ?>/images/no-comments-dr.webp" width="160" height="169" alt="No comments">
            <p><span class="bold">No one has left a comment yet.</span><br />Be the first and get the conversation going.</p>
        </div>
    <?php endif; ?>

    <?php if (comments_open()) :
        $commenter = wp_get_current_commenter();
        $fields = [
            'author_email_group' =>
                '<div class="comment-form-fields flex">'
                . '<p class="comment-form-author form-input-group">'
                    . '<label for="author">Your name*</label>'
                    . '<input id="author" name="author" type="text" placeholder="John Smith" value="' . esc_attr($commenter['comment_author']) . '" required />'
                . '</p>'
                . '<p class="comment-form-email form-input-group">'
                    . '<label for="email">Your email address*</label>'
                    . '<input id="email" name="email" type="email" placeholder="johnsmith@gallifrey.com" value="' . esc_attr($commenter['comment_author_email']) . '" required />'
                . '</p>'
                . '</div>',
            'cookies' =>
                '<div class="comment-form-footer">'
                . '<p class="comment-form-cookies-consent small flex">'
                    . '<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . ( empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"' ) . ' />'
                    . '<label for="wp-comment-cookies-consent">Save my name and email in this browser for the next time I comment.</label>'
                . '</p>',
        ];

        comment_form([
            'fields' => $fields,
            'comment_field' => '<p class="comment-form-comment form-input-group">'
                . '<label for="comment">Comment*</label>'
                . '<textarea id="comment" name="comment" placeholder="Your comment here..." required></textarea>'
                . '</p>',
            'class_form' => 'custom-comment-form',
            'title_reply' => 'Leave a Comment',
            'title_reply_before' => '<h3 class="comment-form-title">',
            'title_reply_after' => '</h3>',
            'comment_notes_before' => '<p class="comment-form-intro small">All comments are manually moderated in accordance with our <a class="link-alt" href="/code-of-conduct">Code of Conduct</a>.<br />Your email address will <strong>not</strong> be published. All fields are required.</p>',
            'label_submit' => 'Submit Comment',
            'cancel_reply_link' => 'Cancel reply',
            'submit_button' => '<button type="submit" class="button">%4$s</button>',
            'submit_field'  => '%1$s %2$s</div>',
            'logged_in_as' => '<p class="logged-in-as comment-form-intro small">Logged in as ' . esc_html(wp_get_current_user()->display_name) . '.</p>',
        ]);
    endif; ?>
