<?php
/**
 * Plugin Name: After Sale Surveys
 * Plugin URI: https://marketingsuiteplugin.com
 * Description: Survey WooCommerce customers and capture customer insights just after they’ve purchased.
 * Version: 1.1.2
 * Author: Rymera Web Co
 * Author URI: https://rymera.com.au
 * Requires at least: 4.4.2
 * Tested up to: 4.7.0
 *
 * Text Domain: after-sale-surveys
 * Domain Path: /languages/
 *
 * @package After_Sale_Surveys
 * @category Core
 * @author Rymera Web Co
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'After_Sale_Surveys' ) ) {

    /**
     * After Sale Surveys plugin main class.
     *
     * This serves as the plugin's main Controller.
     *
     * @since 1.0.0.
     */
    final class After_Sale_Surveys {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Single main instance of After Sale Surveys plugin.
         *
         * @since 1.0.0
         * @access private
         * @var After_Sale_Surveys
         */
        private static $_instance;


        /*
        |--------------------------------------------------------------------------
        | Model Properties.
        |--------------------------------------------------------------------------
        |
        | These properties are instances of various models Events Manager
        | Seat Manager  utilizes. These models handles the logic of the
        | various aspects of the plugin. Ex. Internationalization, loading of
        | various scripts, booting the plugin, and other various business logic.
        |
        */

        /**
         * Property that holds various constants utilized throughout the plugin.
         *
         * @since 1.0.0
         * @access public
         * @var ASS_Constants
         */
        public $constants;

        /**
         * Property that holds various helper functions utilized throughout the plugin.
         *
         * @since 1.0.0
         * @access
         * @var ASS_Helper
         */
        public $helper;

        /**
         * Property that wraps the logic of Internationalization.
         *
         * @since 1.0.0
         * @access public
         * @var ASS_I18n
         */
        public $i18n;

        /**
         * Property that wraps the logic of loading js and css scripts the plugin utilizes.
         *
         * @since 1.0.0
         * @access public
         * @var ASS_Script_Loader
         */
        public $script_loader;

        /**
         * Property that wraps the logic of booting up and shutting down the plugin.
         *
         * @since 1.0.0
         * @access public
         * @var ASS_Bootstrap
         */
        public $bootstrap;

        /**
         * Property that wraps the logic of displaying various admin status of the plugin.
         *
         * @since 1.0.0
         * @access public
         * @var ASS_Admin_Status
         */
        public $admin_status;


        /*
        |--------------------------------------------------------------------------
        | Shop
        |--------------------------------------------------------------------------
        */

        /**
         * Property that houses the logic of the various helper functions related to the shop's products.
         *
         * @since 1.1.0
         * @access public
         * @var ASS_Product
         */
        public $product;


        /*
        |--------------------------------------------------------------------------
        | Help Pointers
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the plugin initial guided tour help pointers.
         *
         * @since 1.1.0
         * @access public
         * @var ASS_Initial_Guided_Tour
         */
        public $initial_guided_tour;

        /**
         * Property that holds the plugin offer entry guided tour help pointers.
         *
         * @since 1.1.0
         * @access public
         * @var ASS_Survey_Entry_Guided_Tour
         */
        public $survey_entry_guided_tour;


        /*
        |--------------------------------------------------------------------------
        | Survey Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that wraps the logic of survey custom post type.
         *
         * @since 1.0.0
         * @access public
         * @var ASS_Survey_CPT
         */
        public $survey_cpt;

        /**
         * Property that wraps the logic of survey.
         *
         * @since 1.0.0
         * @access public
         * @var ASS_Survey
         */
        public $survey;

        /**
         * Property that wraps the logic of survey response custom post type.
         *
         * @since 1.0.0
         * @access public
         * @var ASS_Survey_Response_CPT
         */
        public $survey_response_cpt;

        /**
         * Property that wraps the logic of survey reports.
         *
         * @since 1.0.0
         * @access public
         * @var ASS_Survey_Report
         */
        public $survey_report;

        /**
         * Property that wraps the logic of ajax interfaces of the plugin.
         *
         * @since 1.0.1
         * @access public
         * @var ASS_AJAX_Interfaces
         */
        public $ajax_interface;




        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
        */

        /**
         * Cloning is forbidden.
         *
         * @since 1.0.0
         * @access public
         */
        public function __clone () {

            _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'after-sale-surveys' ) , '1.0.0' );

        }

        /**
         * Unserializing instances of this class is forbidden.
         *
         * @since 1.0.0
         * @access public
         */
        public function __wakeup () {

            _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'after-sale-surveys' ) , '1.0.0' );

        }

        /**
         * After_Sale_Surveys constructor.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct() {

            register_deactivation_hook( __FILE__ , array( $this , 'general_deactivation_code' ) );

            if ( $this->_check_plugin_dependencies() ) {

                $this->_load_dependencies();
                $this->_init();
                $this->_exe();

            } else {

                // Display notice that plugin dependency ( WooCommerce ) is not present.
                add_action( 'admin_notices' , array( $this , 'missing_plugin_dependencies_notice' ) );

            }

        }

        /**
         * Ensure that only one instance of After Sale Surveys is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @return After_Sale_Surveys
         */
        public static function instance() {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self();

            return self::$_instance;

        }

        /**
         * General code base to be always executed on plugin deactivation.
         *
         * @since 1.1.1
         * @access public
         *
         * @param boolean $network_wide Flag that determines if the plugin is activated network wide.
         */
        public function general_deactivation_code( $network_wide ) {

            global $wpdb;

            // check if it is a multisite network
            if ( is_multisite() ) {

                // check if the plugin has been activated on the network or on a single site
                if ( $network_wide ) {

                    // get ids of all sites
                    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                    foreach ( $blog_ids as $blog_id ) {

                        switch_to_blog( $blog_id );
                        delete_option( 'ass_activation_code_triggered' );

                    }

                    restore_current_blog();

                } else {

                    // activated on a single site, in a multi-site
                    delete_option( 'ass_activation_code_triggered' );

                }

            } else {

                // activated on a single site
                delete_option( 'ass_activation_code_triggered' );

            }

        }

        /**
         * Check for plugin dependencies of After Sale Surveys plugin.
         *
         * @since 1.0.0
         * @access private
         *
         * @return bool
         */
        private function _check_plugin_dependencies() {

            // Makes sure the plugin is defined before trying to use it
            if ( !function_exists( 'is_plugin_active' ) )
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            return is_plugin_active( 'woocommerce/woocommerce.php' );

        }

        /**
         * Add notice to notify users that a required plugin dependency of After Sale Surveys plugin is missing.
         *
         * @since 1.0.0
         * @access public
         */
        public function missing_plugin_dependencies_notice() {

            $plugin_file = 'woocommerce/woocommerce.php';
            $wc_file = trailingslashit( WP_PLUGIN_DIR ) . plugin_basename( $plugin_file );

            $wc_install_text = '<a href="' . wp_nonce_url( 'update.php?action=install-plugin&plugin=woocommerce', 'install-plugin_woocommerce' ) . '">' . __( 'Click here to install from WordPress.org repo &rarr;' , 'after-sale-surveys' ) . '</a>';
            if ( file_exists( $wc_file ) )
                $wc_install_text = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_file . '&amp;plugin_status=all&amp;s', 'activate-plugin_' . $plugin_file ) . '" title="' . __( 'Activate this plugin' , 'after-sale-surveys' ) . '" class="edit">' . __( 'Click here to activate &rarr;' , 'after-sale-surveys' ) . '</a>'; ?>

            <div class="error">
                <p>
                    <?php _e( '<b>After Sale Surveys</b> plugin missing dependency.<br/><br/>Please ensure you have the <a href="http://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a> plugin installed and activated.<br/>' , 'after-sale-surveys' ); ?>
                    <?php echo $wc_install_text; ?>
                </p>
            </div>

            <?php

        }

        /**
         * Load controllers that handles various business logic of the plugin.
         *
         * @since 1.0.0
         * @access private
         */
        private function _load_dependencies() {

            include_once ( 'models/class-ass-constants.php' );
            include_once ( 'models/class-ass-helper.php' );
            include_once ( 'models/class-ass-i18n.php' );
            include_once ( 'models/class-ass-admin-status.php' );
            include_once ( 'models/class-ass-script-loader.php' );
            include_once ( 'models/class-ass-bootstrap.php' );

            // Help Pointers
            include_once ( 'models/help-pointers/class-ass-initial-guided-tour.php' );
            include_once ( 'models/help-pointers/class-ass-survey-entry-guided-tour.php' );

            // Shop
            include_once ( 'models/shop/class-ass-product.php' );

            // Survey
            include_once ( 'models/survey/cpt/class-ass-survey-cpt.php' );
            include_once ( 'models/survey/class-ass-survey.php' );
            include_once ( 'models/survey/response/class-ass-survey-response-cpt.php' );
            include_once ( 'models/survey/report/class-ass-survey-report.php' );

            // AJAX
            include_once ( 'models/class-ass-ajax-interfaces.php' );

        }

        /**
         * Initialize the plugin.
         *
         * Initialize various property values and instantiate controller properties.
         *
         * @since 1.0.0
         * @access private
         */
        private function _init() {

            /*
             * Note: We are using "Dependency Injection" to inject anything a specific controller requires in order
             * for it to perform its job. This makes models decoupled and is very modular.
             */

            $this->constants = ASS_Constants::instance();
            $common_deps     = array( 'ASS_Constants' => $this->constants );

            $this->i18n          = ASS_I18n::instance( $common_deps );
            $this->admin_status  = ASS_Admin_Status::instance( $common_deps );

            // Help Pointers
            $this->initial_guided_tour      = ASS_Initial_Guided_Tour::instance( $common_deps );
            $this->survey_entry_guided_tour = ASS_Survey_Entry_Guided_Tour::instance( $common_deps );

            // Shop
            $this->product = ASS_Product::instance( $common_deps );

            // Survey
            $this->survey_cpt          = ASS_Survey_CPT::instance( $common_deps );
            $this->survey              = ASS_Survey::instance( $common_deps );
            $this->survey_response_cpt = ASS_Survey_Response_CPT::instance( $common_deps );
            $this->survey_report       = ASS_Survey_Report::instance( $common_deps );

            $this->script_loader = ASS_Script_Loader::instance( array(
                'ASS_Constants'                => $this->constants,
                'ASS_Survey'                   => $this->survey,
                'ASS_Initial_Guided_Tour'      => $this->initial_guided_tour,
                'ASS_Survey_Entry_Guided_Tour' => $this->survey_entry_guided_tour
            ) );

            // AJAX
            $this->ajax_interface = ASS_AJAX_Interfaces::instance( array(
                'ASS_Constants'     => $this->constants,
                'ASS_Survey_Report' => $this->survey_report
            ) );

            $bootstrap_deps = array(
                'ASS_Constants'                => $this->constants,
                'ASS_Survey_CPT'               => $this->survey_cpt,
                'ASS_Survey_Response_CPT'      => $this->survey_response_cpt,
                'ASS_Initial_Guided_Tour'      => $this->initial_guided_tour,
                'ASS_Survey_Entry_Guided_Tour' => $this->survey_entry_guided_tour
            );
            $this->bootstrap = ASS_Bootstrap::instance( $bootstrap_deps );

        }

        /**
         * Run the plugin. This is the main "method controller", this is where the various processes
         * are being routed to the appropriate models to handle them.
         *
         * @since 1.0.0
         * @access private
         */
        private function _exe() {

            /*
            |--------------------------------------------------------------------------
            | Internationalization
            |--------------------------------------------------------------------------
            */
            add_action( 'plugins_loaded' , array( $this->i18n , 'load_plugin_textdomain' ) );


            /*
            |--------------------------------------------------------------------------
            | Bootstrap
            |--------------------------------------------------------------------------
            */
            register_activation_hook( __FILE__ , array( $this->bootstrap , 'activate_plugin' ) );
            register_deactivation_hook( __FILE__ , array( $this->bootstrap , 'deactivate_plugin' ) );

            // Execute plugin initialization ( plugin activation ) on every newly created site in a multi site set up
            add_action( 'wpmu_new_blog' , array( $this->bootstrap , 'new_mu_site_init' ) , 10 , 6 );

            add_action( 'init' , array( $this->bootstrap , 'initialize' ) );
            add_action( 'init' , array( $this , 'register_ajax_handlers' ) );


            /*
            |--------------------------------------------------------------------------
            | Load JS and CSS Scripts
            |--------------------------------------------------------------------------
            */
            add_action( 'admin_enqueue_scripts' , array( $this->script_loader , 'load_backend_scripts' ) , 10 , 1 );
            add_action( 'wp_enqueue_scripts' , array( $this->script_loader , 'load_frontend_scripts' ) );


            /*
            |--------------------------------------------------------------------------
            | WP Integration
            |--------------------------------------------------------------------------
            */

            // Add custom action links for the plugin in the plugin listings
            add_filter( 'plugin_action_links' , array( $this->bootstrap , 'plugin_listing_custom_action_links' ) , 10 , 2 );


            /*
            |--------------------------------------------------------------------------
            | Survey
            |--------------------------------------------------------------------------
            */

            // Survey
            add_action( 'add_meta_boxes' , array( $this->survey_cpt , 'register_survey_cpt_custom_meta_boxes' ) );
            add_action( 'save_post' , array( $this->survey_cpt , 'save_post' ) , 10 , 1 );
            add_action( 'delete_post' , array( $this->survey_cpt , 'clean_up_survey_data' ) , 10 , 1 );

            // Survey CPT Listing Mods
            add_filter( 'bulk_actions-edit-as_survey' , array( $this->survey_cpt , 'remove_bulk_edit_on_survey_listing' ) , 10 , 1 );
            add_filter( 'post_row_actions' , array( $this->survey_cpt , 'remove_quick_edit_on_survey_listing' ) , 10 , 1 );
            add_filter( 'manage_as_survey_posts_columns' , array( $this->survey_cpt , 'add_survey_listing_column' ) , 10 , 1 );
            add_action( 'manage_as_survey_posts_custom_column' , array( $this->survey_cpt , 'add_survey_listing_column_data' ) , 11 , 2 );

            // Survey CTA
            add_action( 'as_survey_before_survey', array( $this->survey , 'render_survey_cta' ) , 10 , 2 );

            // Survey popup
            add_action( 'woocommerce_thankyou' , array( $this->survey , 'render_survey_popup' ) , 10 , 1 );

            // Survey Thank You
            add_action( 'as_survey_after_survey' , array( $this->survey , 'render_survey_thank_you' ) , 10 , 2 );


            /*
            |--------------------------------------------------------------------------
            | Survey Response
            |--------------------------------------------------------------------------
            */

            // Custom Survey Response CPT Columns
            add_filter( 'manage_as_survey_response_posts_columns' , array( $this->survey_response_cpt , 'add_survey_listing_custom_columns' ) , 10 , 1 );
            add_action( 'manage_as_survey_response_posts_custom_column' , array( $this->survey_response_cpt , 'add_survey_listing_custom_columns_data' ) , 10 , 2 );

            // Survey Response
            add_action( 'add_meta_boxes' , array( $this->survey_response_cpt , 'register_survey_response_cpt_custom_meta_boxes' ) );

            // Survey Report
            add_filter( 'woocommerce_admin_reports' , array( $this->survey_report , 'after_sale_survey_report' ) , 10 , 1 );

            // Print Admin Notice On The Survey CPT Single Entry Admin Page If Survey Is On Read Only Mode ( Meaning Already Have Responses )
            add_action( 'admin_notices' , array( $this->survey_cpt , 'survey_read_only_notice' ) );


            /*
            |--------------------------------------------------------------------------
            | Settings
            |--------------------------------------------------------------------------
            */

            // Register Settings Page
            add_filter( "woocommerce_get_settings_pages" , array( $this->bootstrap , 'initialize_plugin_settings_page' ) , 10 , 1 );


            /*
            |--------------------------------------------------------------------------
            | Admin Status
            |--------------------------------------------------------------------------
            */

            // Render Admin Status
            add_action( 'woocommerce_system_status_report' , array( $this->admin_status , 'render_ass_template_status' ) );

        }

        /**
         * Register the various ajax interfaces the plugin exposes. This is the main controller for ajax interfaces.
         *
         * @since 1.0.0
         * @access public
         */
        public function register_ajax_handlers() {

            // TODO: Add nonces to ajax interfaces, specially form submission

            // Plugin Help Pointers
            add_action( 'wp_ajax_ass_close_initial_guided_tour' , array( $this->initial_guided_tour , 'ass_close_initial_guided_tour' ) );
            add_action( 'wp_ajax_ass_close_survey_entry_guided_tour' , array( $this->survey_entry_guided_tour , 'ass_close_survey_entry_guided_tour' ) );

            // Survey CPT
            add_action( 'wp_ajax_as_survey_save_survey_cta' , array( $this->survey_cpt , 'as_survey_save_survey_cta' ) );
            add_action( 'wp_ajax_as_survey_save_survey_thankyou' , array( $this->survey_cpt , 'as_survey_save_survey_thankyou' ) );
            add_action( 'wp_ajax_as_survey_load_survey_questions_on_datatables' , array( $this->survey_cpt , 'as_survey_load_survey_questions_on_datatables' ) );
            add_action( 'wp_ajax_as_survey_get_new_question_order_number' , array( $this->survey_cpt , 'as_survey_get_new_question_order_number' ) );
            add_action( 'wp_ajax_as_survey_load_survey_question_choices' , array( $this->survey_cpt , 'as_survey_load_survey_question_choices' ) );
            add_action( 'wp_ajax_as_survey_get_question_data' , array( $this->survey_cpt , 'as_survey_get_question_data' ) );
            add_action( 'wp_ajax_as_survey_save_survey_question' , array( $this->survey_cpt , 'as_survey_save_survey_question' ) );
            add_action( 'wp_ajax_as_survey_delete_survey_question' , array( $this->survey_cpt , 'as_survey_delete_survey_question' ) );

            // Survey Response CPT
            add_action( 'wp_ajax_as_survey_save_survey_response' , array( $this->survey_response_cpt , 'as_survey_save_survey_response' ) );
            add_action( 'wp_ajax_nopriv_as_survey_save_survey_response' , array( $this->survey_response_cpt , 'as_survey_save_survey_response' ) );

            // Survey Report
            add_action( 'wp_ajax_as_survey_record_survey_offer_attempt' , array( $this->ajax_interface , 'as_survey_record_survey_offer_attempt' ) );
            add_action( 'wp_ajax_as_survey_record_survey_uptake' , array( $this->ajax_interface , 'as_survey_record_survey_uptake' ) );
            add_action( 'wp_ajax_as_survey_record_survey_completion' , array( $this->ajax_interface , 'as_survey_record_survey_completion' ) );
            add_action( 'wp_ajax_nopriv_as_survey_record_survey_offer_attempt' , array( $this->ajax_interface , 'as_survey_record_survey_offer_attempt' ) );
            add_action( 'wp_ajax_nopriv_as_survey_record_survey_uptake' , array( $this->ajax_interface , 'as_survey_record_survey_uptake' ) );
            add_action( 'wp_ajax_nopriv_as_survey_record_survey_completion' , array( $this->ajax_interface , 'as_survey_record_survey_completion' ) );
            add_action( 'wp_ajax_as_survey_get_survey_responses_report' , array( $this->survey_report , 'as_survey_get_survey_responses_report' ) );
            add_action( 'wp_ajax_as_survey_get_survey_responses_list' , array( $this->survey_report , 'as_survey_get_survey_responses_list' ) );

        }

    }

}

/**
 * Main instance of After Sale Surveys.
 *
 * Returns the main instance of After Sale Surveys to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return After_Sale_Surveys
 */
function AS_Surveys() {
    return After_Sale_Surveys::instance();
}

// Global for backwards compatibility.
$GLOBALS[ 'after_sale_surveys' ] = AS_Surveys();
