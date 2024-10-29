<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'ASS_Settings' ) ) {

    /**
     * Class ASS_Settings
     *
     * Integrate into WooCommerce settings page and initialize After Sale Surveys settings page.
     * We do it in traditional way ( none singleton pattern ) for full compatibility with woocommerce
     * settings page integration requirements.
     *
     * @since 1.0.0
     */
    class ASS_Settings extends WC_Settings_Page {

        /**
         * ASS_Constants instance. Holds various constants this class uses.
         *
         * @since 1.0.0
         * @access private
         * @var ASS_Constants
         */
        private $_plugin_constants;

        /**
         * ASS_Settings constructor.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct() {

            $this->_plugin_constants = ASS_Constants::instance(); // Not dependency injection but this is safe as ASS_Constants class is already loaded.

            $this->id    = 'ass_settings';
            $this->label = __( 'After Sale Surveys' , 'after-sale-surveys' );

            add_filter( 'woocommerce_settings_tabs_array' , array( $this, 'add_settings_page' ), 30 ); // 30 so it is after the API tab
            add_action( 'woocommerce_settings_' . $this->id , array( $this, 'output' ) );
            add_action( 'woocommerce_settings_save_' . $this->id , array( $this, 'save' ) );
            add_action( 'woocommerce_sections_' . $this->id , array( $this, 'output_sections' ) );

            // Custom settings fields
            add_action( 'woocommerce_admin_field_ass_help_resources_controls' , array( $this , 'render_ass_help_resources_controls' ) );
            add_action( 'woocommerce_admin_field_ass_upgrade_banner_controls' , array( $this , 'render_ass_upgrade_banner_controls' ) );
            add_action( 'woocommerce_admin_field_ass_wysiwyg' , array( $this , 'render_ass_wysiwyg' ) );

            do_action( 'as_survey_settings_construct' );

        }

        /**
         * Get sections.
         *
         * @return array
         * @since 1.0.0
         */
        public function get_sections() {

            $sections = array(
                ''                         => __( 'General' , 'after-sale-surveys' ),
                'ass_setting_help_section' => __( 'Help' , 'after-sale-surveys' )
            );

            return apply_filters( 'woocommerce_get_sections_' . $this->id , $sections );

        }

        /**
         * Output the settings.
         *
         * @since 1.0.0
         */
        public function output() {

            global $current_section;

            $settings = $this->get_settings( $current_section );
            WC_Admin_Settings::output_fields( $settings );

        }

        /**
         * Save settings.
         *
         * @since 1.0.0
         */
        public function save() {

            global $current_section;

            $settings = $this->get_settings( $current_section );

            // Filter wysiwyg content so it gets stored properly after sanitizing
            if ( !empty( $_POST[ 'wysiwyg_content' ] ) && isset( $_POST[ 'wysiwyg_content' ] ) )
                foreach ( $_POST[ 'wysiwyg_content' ] as $index => $content )
                    $_POST[ $index ] = htmlentities ( wpautop( $content ) );

            do_action( 'as_survey_before_save_settings' , $settings );

            WC_Admin_Settings::save_fields( $settings );

            do_action( 'as_survey_after_save_settings' , $settings );

        }

        /**
         * Get settings array.
         *
         * @param string $current_section
         *
         * @return mixed
         * @since 1.0.0
         */
        public function get_settings( $current_section = '' ) {

            if ( $current_section == 'ass_setting_help_section' ) {

                // Help Section Options
                $settings = apply_filters( 'ass_setting_help_section_options' , $this->_get_help_section_options() );

            } else {

                // General Section Options
                $settings = apply_filters( 'ass_setting_general_section_options' , $this->_get_general_section_options() );

            }

            return apply_filters( 'woocommerce_get_settings_' . $this->id , $settings , $current_section );

        }




        /*
         |--------------------------------------------------------------------------------------------------------------
         | Section Settings
         |--------------------------------------------------------------------------------------------------------------
         */

        /**
         * Get general section options.
         *
         * @since 1.0.0
         * @access private
         *
         * @return array
         */
        private function _get_general_section_options() {

            $all_pages = ASS_Helper::get_all_site_pages();
            $all_pages_option = array( '' => '' );

            foreach ( $all_pages as $page )
                $all_pages_option[ $page->ID ] = $page->post_title;

            return array(

                array(
                    'title' =>  __( 'General Options', 'after-sale-surveys' ),
                    'type'  =>  'title',
                    'desc'  =>  '',
                    'id'    =>  'ass_general_main_title'
                ),

                array(
                    'name'  =>  '',
                    'type'  =>  'ass_upgrade_banner_controls',
                    'desc'  =>  '',
                    'id'    =>  'ass_upgrade_banner',
                ),

                array(
                    'type'  =>  'sectionend',
                    'id'    =>  'ass_general_sectionend'
                )

            );

        }

        /**
         * Get help section options
         *
         * @since 1.0.0
         * @access private
         *
         * @return array
         */
        private function _get_help_section_options() {

            return array(

                array(
                    'title' => __( 'Help Options' , 'after-sale-surveys' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'ass_help_main_title'
                ),

                array(
                    'name' => '',
                    'type' => 'ass_help_resources_controls',
                    'desc' => '',
                    'id'   => 'ass_help_resources',
                ),

                array(
                    'title' => __( 'Clean up plugin options on un-installation' , 'after-sale-surveys' ),
                    'type'  => 'checkbox',
                    'desc'  => __( 'Clean up plugin options and remove all survey data on un-installation. <b>Note: This process is irreversible.</b>' , 'after-sale-surveys' ),
                    'id'    => $this->_plugin_constants->OPTION_CLEANUP_PLUGIN_OPTIONS()
                ),

                array(
                    'type' => 'sectionend',
                    'id'   => 'ass_help_sectionend'
                )

            );

        }




        /*
         |--------------------------------------------------------------------------------------------------------------
         | Custom Settings Fields
         |--------------------------------------------------------------------------------------------------------------
         */

        /**
         * Render help resources controls.
         *
         * @param $value
         *
         * @since 1.0.0
         */
        public function render_ass_help_resources_controls( $value ) {
            ?>

            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for=""><?php _e( 'Knowledge Base' , 'after-sale-surveys' ); ?></label>
                </th>
                <td class="forminp forminp-<?php echo sanitize_title( $value[ 'type' ] ); ?>">
                    <?php echo sprintf( __( 'Looking for documentation? Please see our growing <a href="%1$s" target="_blank">Knowledge Base</a>' , 'after-sale-surveys' ) , "https://marketingsuiteplugin.com/knowledge-base/after-sale-surveys/?utm_source=ASS&utm_medium=Settings%20Help&utm_campaign=ASS" ); ?>
                </td>
            </tr>

            <?php
        }

        /**
         * Render upgrade banner for ASS.
         *
         * @param $value
         *
         * @since 1.0.0
         */
        public function render_ass_upgrade_banner_controls( $value ) {
            ?>

            <tr valign="top">
                <th scope="row" class="titledesc">
                    <a style="outline: none; display: inline-block;" target="_blank" href="https://marketingsuiteplugin.com/product/after-sale-surveys/?utm_source=ASS&utm_medium=Settings%20Banner&utm_campaign=ASS"><img style="outline: none;" src="<?php echo $this->_plugin_constants->IMAGES_ROOT_URL() . 'ass-premium-upsell-settings.png'; ?>" alt="<?php _e( 'After Sales Surveys Premium' , 'after-sales-surveys' ); ?>"/></a>
                </th>
            </tr>

            <?php
        }

        /**
         * Render custom ass wysiwyg settings field.
         *
         * @since 1.4.0
         * @access public
         *
         * @param $data
         *
         */
        public function render_ass_wysiwyg( $data ) {
            ?>

            <tr valign="top">

                <th scope="row" class="titledesc">
                    <label for="<?php echo $data[ 'id' ]; ?>"><?php echo $data[ 'title' ]; ?></label>
                </th>

                <td class="forminp forminp-<?php echo sanitize_title( $data[ 'type' ] ); ?>">
                    <style type="text/css"><?php echo "div#wp-" . $data[ 'id' ] . "-wrap{width: 70% !important;}"; ?></style>

                    <?php $data_id_option = get_option( $data[ 'id' ] );

                    $ass_wysiwyg_val = !empty( $data_id_option ) ? $data_id_option : $data[ 'default' ];

                    wp_editor( html_entity_decode( $ass_wysiwyg_val ), $data[ 'id' ], array(
                        'wpautop' 		=> true,
                        'textarea_name'	=> "wysiwyg_content[" . $data[ 'id' ] . "]"
                    ) ); ?>
                </td>

            </tr>

        <?php
        }

    }

}

return new ASS_Settings();