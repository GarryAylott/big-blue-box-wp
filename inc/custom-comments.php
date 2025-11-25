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
    $avatar_defaults[$theme_default] = __( 'Big Blue Box Default', 'bigbluebox' );
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
    $cache_key = 'bbb_gravatar_exists_' . $hash;

    $cached = get_transient($cache_key);
    if (false !== $cached) {
        return (bool) $cached;
    }

    // Use a higher size so retina layouts keep the image sharp.
    $gravatar_url = 'https://www.gravatar.com/avatar/' . $hash . '?d=404&s=256';

    $response = wp_remote_head($gravatar_url);
    $exists = !is_wp_error($response) && isset($response['response']['code']) && $response['response']['code'] == 200;

    set_transient($cache_key, $exists ? 1 : 0, DAY_IN_SECONDS);

    return $exists;
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

// Add custom class to comment reply link
add_filter(
    'comment_reply_link',
    function ($link, $args) {
        if (empty($args['class_reply'])) {
            return $link;
        }

        $classes = array_filter(array_map('trim', explode(' ', (string) $args['class_reply'])));

        if (empty($classes) || false === strpos($link, 'comment-reply-link')) {
            return $link;
        }

        $classes = array_map('sanitize_html_class', $classes);
        $extra_class = implode(' ', $classes);

        return str_replace(
            'class="comment-reply-link"',
            'class="comment-reply-link ' . $extra_class . '"',
            $link
        );
    },
    10,
    2
);

// Add custom class to cancel reply link
add_filter(
    'cancel_comment_reply_link',
    function ($link_html) {
        if (!is_string($link_html) || '' === $link_html) {
            return $link_html;
        }

        $cancel_link_class = sanitize_html_class('link-action');

        if (str_contains($link_html, $cancel_link_class)) {
            return $link_html;
        }

        if (str_contains($link_html, 'class=')) {
            return preg_replace_callback(
                "/class=(['\"])([^'\"]*)\\1/",
                function ($matches) use ($cancel_link_class) {
                    $existing_classes = preg_split('/\s+/', trim($matches[2]));
                    if (!is_array($existing_classes)) {
                        $existing_classes = [];
                    }

                    if (!in_array($cancel_link_class, $existing_classes, true)) {
                        $existing_classes[] = $cancel_link_class;
                    }

                    $class_value = trim(implode(' ', array_filter($existing_classes)));

                    return sprintf('class=%1$s%2$s%1$s', $matches[1], $class_value);
                },
                $link_html,
                1
            );
        }

        return str_replace('<a ', sprintf('<a class="%s" ', $cancel_link_class), $link_html);
    }
);

// Custom comments callback
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
        $avatar_url = get_avatar_url($comment->user_id, ['size' => 256]);
        if (strpos($avatar_url, 'gravatar.com/avatar/') !== false) {
            $avatar_type = 'gravatar-img';
        } else {
            $avatar_type = 'team-img';
        }
    } else {
        // Guest: use email to get Gravatar, fallback to default
        $avatar_url = get_avatar_url($comment, ['size' => 256]);
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
                                <span class="team-badge"><?php echo esc_html__( 'AUTHOR', 'bigbluebox' ); ?></span>
                            <?php elseif ($is_team_member) : ?>
                                <span class="team-badge"><?php echo esc_html__( 'BIG BLUE BOX TEAM', 'bigbluebox' ); ?></span>
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
                                'reply_text'  => sprintf(
                                        '%s <span class="reply-icon"><i data-lucide="reply" class="icon-step-0"></i></span>',
                                        esc_html__( 'Reply', 'bigbluebox' )
                                ),
                                'class_reply'     => 'link-action',
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
                        <p class="comment-awaiting-moderation small"><?php esc_html_e( 'Comment received! The TARDIS is holding it in the temporal buffer for moderation and will approve (or not) shortly.', 'bigbluebox' ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php
}
