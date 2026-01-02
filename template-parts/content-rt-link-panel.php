<?php
/**
 * Template part for displaying the Round Table link panel.
 *
 * @package Big_Blue_Box
 */
?>

<section class="wrapper">
    <div class="round-table-link-panel rounded">
        <div class="round-table-link-panel__content flow-small">
            <h3><?php esc_html_e( 'The Roundtable Podcast Episodes', 'bigbluebox' ); ?></h3>
            <p><?php esc_html_e( 'Once a month we gather our writing team around a virtual round table to chat about anything and everything Doctor Who. Topics range from recent news, the latest merch, conventions and much more.', 'bigbluebox' ); ?></p>
            <a class="button" href="<?php echo esc_url('https://feeds.captivate.fm/doctor-who-big-blue-box-podcast/round-table-episodes/'); ?>" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e( 'Listen to the Roundtable Episodes Now', 'bigbluebox' ); ?>
            </a>
        </div>
    </div>
</section>
