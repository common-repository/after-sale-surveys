<table class="wc_status_table widefat" cellspacing="0">

    <thead>
        <tr>
            <th colspan="3" data-export-label="Templates">
                <?php _e( 'After Sale Survey Templates', 'after-sale-surveys' ); ?>
                <?php echo wc_help_tip( __( 'This section shows any files that are overriding the default After Sale Survey template pages.', 'after-sale-surveys' ) ); ?>
            </th>
        </tr>
    </thead>

    <tbody>
    <?php

        $template_paths     = array( 'AfterSaleSurveys' => $template_root_path );
        $scanned_files      = array();
        $found_files        = array();
        $outdated_templates = false;

        foreach ( $template_paths as $plugin_name => $template_path ) {

            $scanned_files = WC_Admin_Status::scan_template_files( $template_path );

            foreach ( $scanned_files as $file ) {

                if ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
                    $theme_file = get_stylesheet_directory() . '/' . $file;
                } elseif ( file_exists( get_stylesheet_directory() . '/after-sale-surveys/' . $file ) ) {
                    $theme_file = get_stylesheet_directory() . '/after-sale-surveys/' . $file;
                } elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
                    $theme_file = get_template_directory() . '/' . $file;
                } elseif( file_exists( get_template_directory() . '/after-sale-surveys/' . $file ) ) {
                    $theme_file = get_template_directory() . '/after-sale-surveys/' . $file;
                } else {
                    $theme_file = false;
                }

                if ( ! empty( $theme_file ) ) {

                    // Fix path specifically for windows
                    $base       = str_replace( "\\" , "/" , WP_CONTENT_DIR . '/themes/' );
                    $theme_file = str_replace( "\\" , "/" , $theme_file );

                    $core_version  = WC_Admin_Status::get_file_version( $template_path . $file );
                    $theme_version = WC_Admin_Status::get_file_version( $theme_file );

                    if ( $core_version && ( empty( $theme_version ) || version_compare( $theme_version, $core_version, '<' ) ) ) {

                        if ( ! $outdated_templates )
                            $outdated_templates = true;

                        $found_files[ $plugin_name ][] = sprintf( __( '<code>%s</code> version <strong style="color:red">%s</strong> is out of date. The core version is %s', 'after-sale-surveys' ), str_replace( $base , '' , $theme_file ), $theme_version ? $theme_version : '-', $core_version );

                    } else
                        $found_files[ $plugin_name ][] = sprintf( '<code>%s</code>', str_replace( $base , '' , $theme_file ) );

                }

            }

        }

        if ( $found_files ) {

            foreach ( $found_files as $plugin_name => $found_plugin_files ) { ?>

                <tr>
                    <td data-export-label="Overrides"><?php _e( 'Overrides', 'after-sale-surveys' ); ?> (<?php echo $plugin_name; ?>):</td>
                    <td class="help">&nbsp;</td>
                    <td><?php echo implode( ', <br/>', $found_plugin_files ); ?></td>
                </tr>

            <?php }

        } else { ?>

            <tr>
                <td data-export-label="Overrides"><?php _e( 'Overrides', 'after-sale-surveys' ); ?>:</td>
                <td class="help">&nbsp;</td>
                <td>&ndash;</td>
            </tr>

        <?php }

        if ( true === $outdated_templates ) { ?>

            <tr>
                <td>&nbsp;</td>
                <td class="help">&nbsp;</td>
                <!--TODO: We can have our own docs here for fixing outdated overridden templates -->
                <td><a href="http://docs.woothemes.com/document/fix-outdated-templates-woocommerce/" target="_blank"><?php _e( 'Learn how to update outdated templates', 'after-sale-surveys' ) ?></a></td>
            </tr>

        <?php } ?>

    </tbody>

</table>