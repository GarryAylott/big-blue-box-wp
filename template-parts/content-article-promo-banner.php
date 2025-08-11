<?php
/**
 * Article Promo Banner (Markup)
 *
 * Expects global $post set (setup_postdata($post) before include).
 * Keep markup lean; styling will live in your SCSS.
 *
 * @package bigbluebox
 */
?>
<?php
$post_id = isset( $post->ID ) ? $post->ID : get_the_ID();

$promo_headings = array(
	'Enjoying this article? Here’s your next stop in time and space…',
	'Enjoying this article? The TARDIS has another destination ready…',
	'Into this topic? Let’s open another TARDIS door…',
	'Liking this take? There’s another timey-wimey read queued up…',
	'Finding this read useful? Here’s your next temporal waypoint…',
	'Enjoying this post? The next one’s right here…',
    'Enjoying this article? Another one is materialising for you…',
    'Interested in this subject? The time rotor’s spinning for another one…',
);

$random_heading = $promo_headings[ array_rand( $promo_headings ) ];
?>
<aside class="article-promo-banner rounded" role="complementary" aria-label="<?php echo esc_attr_x( 'Suggested reading', 'aria label', 'bigbluebox' ); ?>">
	<h3 class="article-promo-banner__heading">
		<?php echo esc_html( $random_heading ); ?>
	</h3>

    <article class="article-promo-banner__post">
        <?php if ( has_post_thumbnail( $post_id ) ) : ?>
            <div class="article-promo-banner__img img-container">
                <img class="post-thumb-img img-hover rounded-small" src="<?php echo get_the_post_thumbnail_url( $post_id, 'homepage-thumb' ); ?>" width="387" height="217" alt="<?php echo esc_attr( get_the_title( $post_id ) ); ?>">
            </div>
        <?php endif; ?>
        
        <div class="article-promo-banner__content flow-small">
            <h3 class="article-promo-banner__title">
                <a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a>
            </h3>

            <p class="article-promo-banner__excerpt small">
                <?php echo wp_trim_words( get_the_excerpt(), 15 ); ?>
            </p>

            <footer class="entry-footer">
                <?php get_template_part( 'template-parts/content', 'author-meta', array( 'link_author_name' => false ) ); ?>
            </footer>
        </div>
    </article>
</aside>