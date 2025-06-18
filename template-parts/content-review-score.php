<?php
/**
 * Template part for displaying the closing thoughts and review score for Articles.
 *
 * @package Big_Blue_Box
 */
?>

<?php
$closing_text  = get_field( 'closing_thoughts_text' );
$closing_score = get_field( 'closing_thoughts_score' );

if ( $closing_text || ($closing_score && $closing_score > 0) ) :
    // Convert score to a float (handles half-points).
    $score = (float) $closing_score;
    
    // Calculate circle stroke offset based on the score (out of 10).
    // For an SVG circle of radius 50, circumference ~ 314.		
    $radius = 50;
    $circumference = 2 * M_PI * $radius;
    // E.g. if score=5, offset= half the circumference → 314 - (5/10)*314 = 157.
    $offset = $circumference - ( ( $score / 10 ) * $circumference );
    ?>
    
    <div class="closing-thoughts rounded">
        <div class="closing-thoughts__text">
            <h4 class="closing-thoughts-title"><?php esc_html_e( 'Our Score and Final Thoughts', 'bigbluebox' ); ?></h4>
            
            <?php if ( $closing_text ) : ?>
                <p><?php echo esc_html( $closing_text ); ?></p>
            <?php endif; ?>
        </div>

        <?php if ( $closing_score && $closing_score > 0 ) : ?>
            <div class="score-wrapper" aria-label="<?php printf( esc_attr__( 'Score: %s out of 10', 'bigbluebox' ), esc_attr( $score ) ); ?>">
                <!-- SVG Container -->
                <svg 
                    class="score-circle" 
                    width="120" 
                    height="120" 
                    viewBox="0 0 120 120" 
                    role="img"
                >
                    <!-- Background circle (full grey ring) -->
                    <circle
                        class="score-circle-bg"
                        cx="60"
                        cy="60"
                        r="50"
                    />
                    <!-- Stroke circle showing portion of the circumference -->
                    <circle
                        class="score-circle-stroke"
                        cx="60"
                        cy="60"
                        r="50"
                        style="stroke-dasharray: <?php echo esc_attr( $circumference ); ?>; stroke-dashoffset: <?php echo esc_attr( $offset ); ?>;"
                    />
                </svg>
                <div class="score-value"><?php echo esc_html( $score ); ?></div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>