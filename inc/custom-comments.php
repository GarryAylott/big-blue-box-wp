<?php
/**
 * Custom comments section.
 *
 * @package Big_Blue_Box
 */

 /**
 * Comments avatar handling
 */
add_filter('avatar_defaults', function($avatar_defaults) {
    $theme_default = get_template_directory_uri() . '/images/authors/author-avatar-small-default.webp';
    $avatar_defaults[$theme_default] = 'Big Blue Box Default';
    return $avatar_defaults;
});

// Helper: Check if a Gravatar exists for a given email
function bbb_gravatar_exists($id_or_email) {
    $email = '';
    if (is_numeric($id_or_email)) {
        $user = get_user_by('id', $id_or_email);
        if ($user) {
            $email = $user->user_email;
        }
    } elseif (is_object($id_or_email) && isset($id_or_email->comment_author_email)) {
        $email = $id_or_email->comment_author_email;
    } elseif (is_string($id_or_email)) {
        $email = $id_or_email;
    }

    if (empty($email)) {
        return false;
    }

    $hash = md5(strtolower(trim($email)));
    $gravatar_url = 'https://www.gravatar.com/avatar/' . $hash . '?d=404&s=64';

    $response = wp_remote_head($gravatar_url);
    if (!is_wp_error($response) && isset($response['response']['code']) && $response['response']['code'] == 200) {
        return true;
    }
    return false;
}

// Filter: Only override if no Gravatar exists
add_filter('get_avatar_url', function($url, $id_or_email, $args) {
    // Randomly select one of six default avatars
    $random = mt_rand(1, 6);
    $theme_default = get_template_directory_uri() . '/images/comment-avatar-default-' . $random . '.webp';
    if (!bbb_gravatar_exists($id_or_email)) {
        return $theme_default;
    }
    return $url;
}, 20, 3);

/**
 * Comments custom callback
 */
function custom_comments_callback($comment, $args, $depth) {
    $tag = ($args['style'] === 'div') ? 'div' : 'li';
    $comment_id = get_comment_ID();
    $author = get_comment_author();
    $date = get_comment_date('jS F Y \a\t H:i');

    // Determine avatar and type
    $avatar_type = '';
    $avatar_url = '';

    // Check if this comment is from the post author
    $is_post_author = ($comment->user_id === get_post_field('post_author', $comment->comment_post_ID));
    $is_team_member = ($comment->user_id && !$is_post_author);

    if ($comment->user_id) {
        // Registered user: get their avatar (Gravatar or custom)
        $avatar_url = get_avatar_url($comment->user_id, ['size' => 64]);
        if (strpos($avatar_url, 'gravatar.com/avatar/') !== false) {
            $avatar_type = 'gravatar-img';
        } else {
            $avatar_type = 'team-img';
        }
    } else {
        // Guest: use email to get Gravatar, fallback to default
        $avatar_url = get_avatar_url($comment, ['size' => 64]);
        if (bbb_gravatar_exists($comment)) {
            $avatar_type = 'gravatar-img';
        } else {
            $avatar_type = 'default-img';
        }
    }

    // If for any reason avatar_url is empty, fallback to a random default
    if (!$avatar_url) {
        $random = mt_rand(1, 6);
        $avatar_url = get_template_directory_uri() . '/images/comment-avatar-default-' . $random . '.webp';
        $avatar_type = 'default-img';
    }

    ?>

    <<?php echo $tag; ?> <?php comment_class('custom-comment depth-' . $depth); ?> id="comment-<?php echo $comment_id; ?>">
        <div class="comment-body flex">
            <div class="comment-avatar">
                <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($author); ?>" class="<?php echo esc_attr($avatar_type); ?>">
            </div>
            <div class="comment-container">
                <div class="comment-header">
                    <div class="comment-meta">
                        <div class="comment-author">
                            <?php echo esc_html($author); ?>
                            <?php if ($is_post_author) : ?>
                                <span class="team-badge">AUTHOR</span>
                            <?php elseif ($is_team_member) : ?>
                                <span class="team-badge">TEAM MEMBER</span>
                            <?php endif; ?>
                        </div>
                        <div class="comment-date"><?php echo esc_html($date); ?></div>
                    </div>
                    <div class="comment-reply">
                        <?php
                        comment_reply_link(
                            [
                                'add_below'  => 'comment',
                                'respond_id'  => 'respond',
                                'depth'       => $depth,
                                'max_depth'   => $args['max_depth'],
                                'reply_text'  => __('Reply') . ' <span class="reply-icon">↩</span>',
                                'class_reply' => 'comment-reply-link',
                                'before'      => '',
                                'after'       => '',
                            ],
                            $comment,
                            get_the_ID()
                        );
                        ?>
                    </div>
                </div>
                <div class="comment-content">
                    <?php comment_text(); ?>
                    <?php if ($comment->comment_approved === '0') : ?>
                        <p class="comment-awaiting-moderation small"><?php _e('Comment received! The TARDIS is holding it in the temporal buffer for moderation. It will be approved shortly.'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php
}
