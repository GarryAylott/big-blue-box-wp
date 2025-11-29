<?php
/**
 * Custom search field for sidebar
 */
?>

<form role="search" method="get" class="search-form js-search-form" action="<?php echo esc_url(home_url('/')); ?>">
<?php
// Hide the title block (svg and h4 title) on error 404 page.
global $bbb_hide_search_form_title;
$bbb_show_search_title = ! ( isset( $bbb_hide_search_form_title ) && true === $bbb_hide_search_form_title );
?>

<?php if ( $bbb_show_search_title ) : ?>
        <div class="search-form-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="34" fill="none" viewBox="0 0 30 34">
                <g fill="#4C83F6" clip-path="url(#a)">
                    <path d="m14.956 5.372 3.378 1.275V1.884L14.956.5M14.956 5.372l-3.29 1.243V1.82L14.956.5" />
                    <path fill-rule="evenodd" d="M28.926 33.5V14.287h1.077V12h-.115L15.533 6.647V33.5h13.393Zm-6.7-19.413-5.003-1.295.002 7.034 5 .846v-6.585Zm.516.152 4.434 1.244v6.085l-4.42-.77-.014-6.56ZM14.46 6.647.09 11.98l-.093.002v2.347h1.107V33.5H14.46V6.647ZM6.608 22.795l-3.247.5v5.437l3.248-.207v-5.73Zm6.17-2.97-5 .85v-6.584l5-1.298v7.033Zm-10 1.749 4.444-.774-.002-6.559-4.444 1.249.001 6.084Zm7.203 5.422c.756-.055 1.385-.835 1.385-1.739 0-.904-.629-1.553-1.386-1.45-.733.099-1.313.87-1.313 1.73 0 .858.58 1.51 1.314 1.459Z" clip-rule="evenodd" />
                </g>
                <defs>
                    <clipPath id="a">
                        <path fill="#fff" d="M0 .5h30v33H0z" />
                    </clipPath>
                </defs>
            </svg>
            <h4><?php esc_html_e('Search for anything in The Big Blue Box', 'bigbluebox'); ?></h4>
        </div>
    <?php endif; ?>
    <div class="inputs">
        <?php $search_input_id = 'search-field-' . uniqid(); ?>
        <label for="<?php echo esc_attr($search_input_id); ?>" class="screen-reader-text"><?php _e('Search for:', 'bigbluebox'); ?></label>
        <input type="search" id="<?php echo esc_attr($search_input_id); ?>" class="search-field" 
            placeholder="<?php esc_attr_e('Where is Dalek Tat?', 'bigbluebox'); ?>" 
            value="<?php echo esc_attr(get_search_query()); ?>" name="s" required />
        <button type="submit" class="button search-submit">Search</button>
    </div>
</form>
