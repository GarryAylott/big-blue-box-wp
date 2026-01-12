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
	__( 'Enjoying this article? Here’s your next stop in time and space…', 'bigbluebox' ),
	__( 'Enjoying this article? The TARDIS has another destination ready…', 'bigbluebox' ),
	__( 'Into this topic? Let’s open another TARDIS door…', 'bigbluebox' ),
	__( 'Liking this take? There’s another timey-wimey read queued up…', 'bigbluebox' ),
	__( 'Finding this read useful? Here’s your next temporal waypoint…', 'bigbluebox' ),
	__( 'Enjoying this post? The next one’s right here…', 'bigbluebox' ),
    __( 'Enjoying this article? Another one is materialising for you…', 'bigbluebox' ),
    __( 'Interested in this subject? The time rotor’s spinning for another one…', 'bigbluebox' ),
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
                <?php
                echo wp_get_attachment_image(
                    get_post_thumbnail_id( $post_id ),
                    'post-featured-card',
                    false,
                    [
						'class'   => 'post-thumb-img img-hover rounded-small',
						'sizes'   => '(min-width: 1200px) 25vw, (min-width: 900px) 33vw, 90vw',
						'loading' => 'lazy',
					]
                );
                ?>
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
