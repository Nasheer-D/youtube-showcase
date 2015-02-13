<?php
/**
 * Add-On Page Functions
 *
 * @package     EMD
 * @copyright   Copyright (c) 2014,  Emarket Design
 * @since       WPAS 4.2
 */
if (!defined('ABSPATH')) exit;
/**
 * Show emdplugins plugins and extensions
 *
 * @param string $textdomain
 * @since WPAS 4.2
 *
 * @return html page content
 */
function emd_display_store($textdomain) {
	global $title;
	ob_start(); ?>
<div class="wrap">
<h2><?php echo $title;?> &nbsp;&mdash;&nbsp;<a href="https://emdplugins.com/plugins?pk_source=plugin-addons-page&pk_medium=plugin&pk_campaign=<?php echo $textdomain;?>-addonspage&pk_content=browseall" class="button-primary" title="<?php _e( 'Browse All', 'emd-plugins' ); ?>" target="_blank"><?php _e( 'Browse All', 'emd-plugins' ); ?></a>
</h2>
<p><?php _e('The following plugins extend and expand the functionality of your app.','emd-plugins'); ?></p>
                <?php echo emd_add_ons(); ?>
        </div>
        <?php
        echo ob_get_clean();
}
/**
 * Get plugin and extension list from emdplugins site and save it in a transient
 *
 * @since WPAS 4.2
 *
 * @return $cache html content
 */
function emd_add_ons() {
        //if ( false === ( $cache = get_transient( 'emd_store_feed' ) ) ) {
                $feed = wp_remote_get( 'https://emd-plugin-site.s3.amazonaws.com/addons.html');
                if ( ! is_wp_error( $feed ) ) {
                        if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
                                $cache = wp_remote_retrieve_body( $feed );
                                //set_transient( 'emd_store_feed', $cache, 3600 );
                        }
                } else {
                        $cache = '<div class="error"><p>' . __( 'There was an error retrieving the extensions list from the server. Please try again later.', 'emd-plugins' ) . '</div>';
                }
        //}
        return $cache;
}
