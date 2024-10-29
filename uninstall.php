<?php if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();

/**
 * Function that houses the code that cleans up the plugin on un-installation.
 *
 * @since 1.0.0
 */
function as_survey_plugin_cleanup() {

    include_once ( 'models/class-ass-constants.php' );

    $plugin_constants = ASS_Constants::instance();

    if ( get_option( $plugin_constants->OPTION_CLEANUP_PLUGIN_OPTIONS() ) == 'yes' ) {
        
        global $wpdb;

        // Delete plugin options
        delete_option( $plugin_constants->OPTION_CLEANUP_PLUGIN_OPTIONS() );

        // Drop custom tables
        $wpdb->query( "DROP TABLE IF EXISTS " . $plugin_constants->CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS() );
        $wpdb->query( "DROP TABLE IF EXISTS " . $plugin_constants->CUSTOM_TABLE_SURVEY_UPTAKES() );
        $wpdb->query( "DROP TABLE IF EXISTS " . $plugin_constants->CUSTOM_TABLE_SURVEY_COMPLETIONS() );

        // Delete all surveys
        $wpdb->query( "DELETE FROM $wpdb->posts WHERE post_type = '" . $plugin_constants->SURVEY_CPT_NAME() . "'" );

        // Delete all survey responses
        $wpdb->query( "DELETE FROM $wpdb->posts WHERE post_type = '" . $plugin_constants->SURVEY_RESPONSE_CPT_NAME() . "'" );

        flush_rewrite_rules();

    }

}

if ( function_exists( 'is_multisite' ) && is_multisite() ) {

    global $wpdb;

    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

    foreach ( $blog_ids as $blog_id ) {

        switch_to_blog( $blog_id );
        as_survey_plugin_cleanup();

    }

    restore_current_blog();

    return;

} else
    as_survey_plugin_cleanup();