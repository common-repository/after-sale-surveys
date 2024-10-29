<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'ASS_Bootstrap' ) ) {

    /**
     * Class ASS_Bootstrap
     *
     * Model that houses the logic of booting up (activating) and shutting down (deactivating) After Sale Surveys plugin.
     *
     * @since 1.0.0
     */
    final class ASS_Bootstrap {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of ASS_Bootstrap.
         *
         * @since 1.0.0
         * @access private
         * @var ASS_Bootstrap
         */
        private static $_instance;

        /**
         * Property that holds various constants utilized throughout the plugin.
         *
         * @since 1.0.0
         * @access private
         * @var ASS_Constants
         */
        private $_plugin_constants;

        /**
         * Property that wraps the logic of survey custom post type.
         *
         * @since 1.0.0
         * @access private
         * @var ASS_Survey_CPT
         */
        private $_survey_cpt;

        /**
         * Property that wraps the logic of survey response custom post type.
         *
         * @since 1.0.0
         * @access private
         * @var ASS_Survey_Response_CPT
         */
        private $_survey_response_cpt;

        /**
         * Property that holds the plugin initial guided tour help pointers.
         *
         * @since 1.1.0
         * @access private
         * @var ASS_Initial_Guided_Tour
         */
        private $_initial_guided_tour;

        /**
         * Property that holds the plugin offer entry guided tour help pointers.
         *
         * @since 1.1.0
         * @access private
         * @var ASS_Survey_Entry_Guided_Tour
         */
        private $_survey_entry_guided_tour;




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
         * ASS_Bootstrap constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of ASS_Bootstrap model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants         = $dependencies[ 'ASS_Constants' ];
            $this->_survey_cpt               = $dependencies[ 'ASS_Survey_CPT' ];
            $this->_survey_response_cpt      = $dependencies[ 'ASS_Survey_Response_CPT' ];
            $this->_initial_guided_tour      = $dependencies[ 'ASS_Initial_Guided_Tour' ];
            $this->_survey_entry_guided_tour = $dependencies[ 'ASS_Survey_Entry_Guided_Tour' ];

        }

        /**
         * Ensure that only one instance of ASS_Bootstrap is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of ASS_Bootstrap model.
         * @return ASS_Bootstrap
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Method that houses the logic relating to activating After Sale Surveys plugin.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $network_wide
         */
        public function activate_plugin( $network_wide ) {

            global $wpdb;

            if ( is_multisite() ) {

                if ( $network_wide ) {

                    // get ids of all sites
                    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                    foreach ( $blog_ids as $blog_id ) {

                        switch_to_blog( $blog_id );
                        $this->_activate_plugin( $blog_id );

                    }

                    restore_current_blog();

                } else {

                    // activated on a single site, in a multi-site
                    $this->_activate_plugin( $wpdb->blogid );

                }

            } else {

                // activated on a single site
                $this->_activate_plugin( $wpdb->blogid );

            }

        }

        /**
         * Method to initialize a newly created site in a multi site set up.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $blog_id
         * @param $user_id
         * @param $domain
         * @param $path
         * @param $site_id
         * @param $meta
         */
        public function new_mu_site_init( $blog_id , $user_id , $domain , $path , $site_id , $meta ) {

            if ( is_plugin_active_for_network( 'after-sale-surveys/after-sale-surveys.php' ) ) {

                switch_to_blog( $blog_id );
                $this->_activate_plugin( $blog_id );
                restore_current_blog();

            }

        }

        /**
         * Initialize plugin options.
         *
         * @since 1.1.0
         * @access private
         */
        private function _initialize_plugin_options() {

            // Set initial value of 'no' for the option that sets the option that specify whether to delete the options on plugin uninstall
            if ( !get_option( $this->_plugin_constants->OPTION_CLEANUP_PLUGIN_OPTIONS() , false ) )
                update_option( $this->_plugin_constants->OPTION_CLEANUP_PLUGIN_OPTIONS() , 'no' );

        }

        /**
         * Create plugin custom tables.
         *
         * @since 1.1.0
         * @access private
         */
        private function _create_custom_tables() {

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            global $wpdb;

            $charset_collate = $wpdb->get_charset_collate();

            $latest_survey_offer_attempts_table_version = '1.0.0';
            $latest_survey_uptakes_table_version        = '1.0.0';
            $latest_survey_completions_table_version    = '1.0.0';

            if ( $latest_survey_offer_attempts_table_version != get_option( $this->_plugin_constants->CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS_VERSION() ) ) {

                $survey_offer_attempts_table_sql = "CREATE TABLE " . $this->_plugin_constants->CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS() . " (
                                                    id INT NOT NULL AUTO_INCREMENT,
                                                    survey_id INT NOT NULL,
                                                    customer_email VARCHAR(100) NOT NULL,
                                                    record_datetime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                                                    order_id INT NOT NULL,
                                                    client_ip varchar(50) NOT NULL,
                                                    user_agent text DEFAULT '' NOT NULL,
                                                    PRIMARY KEY  (id)
                                                    ) $charset_collate;";

                dbDelta( $survey_offer_attempts_table_sql );

                update_option( $this->_plugin_constants->CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS_VERSION() , $latest_survey_offer_attempts_table_version );

            }

            if ( $latest_survey_uptakes_table_version != get_option( $this->_plugin_constants->CUSTOM_TABLE_SURVEY_UPTAKES_VERSION() ) ) {

                $survey_uptakes_table_sql = "CREATE TABLE " . $this->_plugin_constants->CUSTOM_TABLE_SURVEY_UPTAKES() . " (
                                             id INT NOT NULL AUTO_INCREMENT,
                                             survey_id INT NOT NULL,
                                             customer_email VARCHAR(100) NOT NULL,
                                             record_datetime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                                             order_id INT NOT NULL,
                                             client_ip varchar(50) NOT NULL,
                                             user_agent text DEFAULT '' NOT NULL,
                                             PRIMARY KEY  (id)
                                             ) $charset_collate;";

                dbDelta( $survey_uptakes_table_sql );

                update_option( $this->_plugin_constants->CUSTOM_TABLE_SURVEY_UPTAKES_VERSION() , $latest_survey_uptakes_table_version );

            }

            if ( $latest_survey_completions_table_version != get_option( $this->_plugin_constants->CUSTOM_TABLE_SURVEY_COMPLETIONS_VERSION() ) ) {

                $survey_completions_sql = "CREATE TABLE " . $this->_plugin_constants->CUSTOM_TABLE_SURVEY_COMPLETIONS() . " (
                                           id INT NOT NULL AUTO_INCREMENT,
                                           survey_id INT NOT NULL,
                                           customer_email VARCHAR(100) NOT NULL,
                                           record_datetime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                                           order_id INT NOT NULL,
                                           response_id INT NOT NULL,
                                           client_ip varchar(50) NOT NULL,
                                           user_agent text DEFAULT '' NOT NULL,
                                           PRIMARY KEY  (id)
                                           ) $charset_collate;";

                dbDelta( $survey_completions_sql );

                update_option( $this->_plugin_constants->CUSTOM_TABLE_SURVEY_COMPLETIONS_VERSION() , $latest_survey_completions_table_version );

            }

        }

        /**
         * Migrate survey stats data from post meta to custom plugin tables.
         * TODO: Remove on later releases.
         *
         * @since 1.1.0
         * @access private
         */
        private function _migrate_survey_stats_data() {

            global $wpdb;

            if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $this->_plugin_constants->CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS() . "'" ) &&
                 $wpdb->get_var( "SHOW TABLES LIKE '" . $this->_plugin_constants->CUSTOM_TABLE_SURVEY_UPTAKES() . "'" ) &&
                 $wpdb->get_var( "SHOW TABLES LIKE '" . $this->_plugin_constants->CUSTOM_TABLE_SURVEY_COMPLETIONS() . "'" ) ) {

                $surveys = ASS_Helper::get_all_surveys();

                foreach ( $surveys as $survey ) {

                    // Migrate survey offer attempts data

                    $survey_offer_attempts = get_post_meta( $survey->ID , $this->_plugin_constants->POST_META_SURVEY_OFFER_ATTEMPTS() , true );
                    if ( !is_array( $survey_offer_attempts ) )
                        $survey_offer_attempts = array();

                    foreach ( $survey_offer_attempts as $offer_attempt ) {

                        $wpdb->insert(
                            $this->_plugin_constants->CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS(),
                            array(
                                'survey_id'       => $survey->ID,
                                'customer_email'  => $offer_attempt[ 'current_user' ]->user_email,
                                'record_datetime' => date( 'Y-m-d H:i:s' , $offer_attempt[ 'timestamp' ] ),
                                'order_id'        => $offer_attempt[ 'order_id' ],
                                'client_ip'       => $offer_attempt[ 'client_ip' ],
                                'user_agent'      => $offer_attempt[ 'user_agent' ]
                            )
                        );

                    }

                    delete_post_meta( $survey->ID , $this->_plugin_constants->POST_META_SURVEY_OFFER_ATTEMPTS() );

                    // Migrate survey uptakes data

                    $survey_uptakes = get_post_meta( $survey->ID , $this->_plugin_constants->POST_META_SURVEY_UPTAKES() , true );
                    if ( !is_array( $survey_uptakes ) )
                        $survey_uptakes = array();

                    foreach ( $survey_uptakes as $uptake ) {

                        $wpdb->insert(
                            $this->_plugin_constants->CUSTOM_TABLE_SURVEY_UPTAKES(),
                            array(
                                'survey_id'       => $survey->ID,
                                'customer_email'  => $uptake[ 'current_user' ]->user_email,
                                'record_datetime' => date( 'Y-m-d H:i:s' , $uptake[ 'timestamp' ] ),
                                'order_id'        => $uptake[ 'order_id' ],
                                'client_ip'       => $uptake[ 'client_ip' ],
                                'user_agent'      => $uptake[ 'user_agent' ]
                            )
                        );

                    }

                    delete_post_meta( $survey->ID , $this->_plugin_constants->POST_META_SURVEY_UPTAKES() );

                    // Migrate survey completion data

                    $survey_completions = get_post_meta( $survey->ID , $this->_plugin_constants->POST_META_SURVEY_COMPLETIONS() , true );
                    if ( !is_array( $survey_completions ) )
                        $survey_completions = array();

                    foreach ( $survey_completions as $completion ) {

                        $wpdb->insert(
                            $this->_plugin_constants->CUSTOM_TABLE_SURVEY_COMPLETIONS(),
                            array(
                                'survey_id'       => $survey->ID,
                                'customer_email'  => $completion[ 'current_user' ]->user_email,
                                'record_datetime' => date( 'Y-m-d H:i:s' , $completion[ 'timestamp' ] ),
                                'order_id'        => $completion[ 'order_id' ],
                                'response_id'     => $completion[ 'response_id' ],
                                'client_ip'       => $completion[ 'client_ip' ],
                                'user_agent'      => $completion[ 'user_agent' ]
                            )
                        );

                    }

                    delete_post_meta( $survey->ID , $this->_plugin_constants->POST_META_SURVEY_COMPLETIONS() );

                }

            }

        }

        /**
         * Before ASS 1.1.0, there is no notion of required/optional questions. All questions by default is required.
         * Therefore since we introduced this feature on 1.1.0, we need to add this new piece of data to existing questions.
         * If no 'required' key is present to the questions array data, add one and set to 'yes' by default.
         * TODO: Remove on later releases.
         *
         * @since 1.1.0
         * @access private
         */
        private function _add_additional_required_question_data_to_existing_questions() {

            $surveys = ASS_Helper::get_all_surveys();

            foreach ( $surveys as $survey ) {

                $survey_questions = get_post_meta( $survey->ID , $this->_plugin_constants->POST_META_SURVEY_QUESTIONS() , true );
                if ( !is_array( $survey_questions ) )
                    $survey_questions = array();

                foreach ( $survey_questions as $page_id => $questions )
                    foreach ( $questions as $questions_order => $question_data )
                        if ( !array_key_exists( 'required' , $question_data ) )
                            $survey_questions[ $page_id ][ $questions_order ][ 'required' ] = 'yes';

                update_post_meta( $survey->ID , $this->_plugin_constants->POST_META_SURVEY_QUESTIONS() , $survey_questions );

            }

        }

        /**
         * Actual function that houses the code to execute on plugin activation.
         *
         * @since 1.0.0
         * @since 1.1.0 Create custom tables.
         * @access private
         *
         * @param $blogid
         */
        private function _activate_plugin( $blogid ) {

            // Initialize plugin options
            $this->_initialize_plugin_options();

            // Register CPTs
            $this->_survey_cpt->register_survey_cpt();
            $this->_survey_response_cpt->register_survey_response_cpt();

            // Register plugin custom tables
            $this->_create_custom_tables();

            // Migrate survey stats data to custom tables
            $this->_migrate_survey_stats_data();

            // Add additional required question data to existing questions
            $this->_add_additional_required_question_data_to_existing_questions();

            // Help pointers
            $this->_initial_guided_tour->initialize_guided_tour_options();
            $this->_survey_entry_guided_tour->initialize_guided_tour_options();

            flush_rewrite_rules();

            update_option( 'ass_activation_code_triggered' , 'yes' );

        }

        /**
         * Method that houses the logic relating to deactivating After Sale Surveys plugin.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $network_wide
         */
        public function deactivate_plugin( $network_wide ) {

            global $wpdb;

            // check if it is a multisite network
            if ( is_multisite() ) {

                // check if the plugin has been activated on the network or on a single site
                if ( $network_wide ) {

                    // get ids of all sites
                    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                    foreach ( $blog_ids as $blog_id ) {

                        switch_to_blog( $blog_id );
                        $this->_deactivate_plugin( $wpdb->blogid );

                    }

                    restore_current_blog();

                } else {

                    // activated on a single site, in a multi-site
                    $this->_deactivate_plugin( $wpdb->blogid );

                }

            } else {

                // activated on a single site
                $this->_deactivate_plugin( $wpdb->blogid );

            }

        }

        /**
         * Actual method that houses the code to execute on plugin deactivation.
         *
         * @since 1.0.0
         * @access private
         *
         * @param $blogid
         */
        private function _deactivate_plugin( $blogid ) {

            $this->_initial_guided_tour->terminate_guided_tour_options();
            $this->_survey_entry_guided_tour->terminate_guided_tour_options();

            flush_rewrite_rules();

        }

        /**
         * Method that houses codes to be executed on init hook.
         *
         * @since 1.0.0
         * @access public
         */
        public function initialize() {

            if ( get_option( 'ass_activation_code_triggered' , false ) !== 'yes' ) {

                if ( ! function_exists( 'is_plugin_active_for_network' ) )
                    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

                $network_wide = is_plugin_active_for_network( 'after-sale-surveys/after-sale-surveys.php' );
                $this->activate_plugin( $network_wide );

            }

            // Register CPTs
            $this->_survey_cpt->register_survey_cpt();
            $this->_survey_response_cpt->register_survey_response_cpt();

        }

        /**
         * Initialize the plugin's settings page. Integrate to WooCommerce settings.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function initialize_plugin_settings_page() {

            $settings[] = include( $this->_plugin_constants->MODELS_ROOT_PATH() . "class-ass-settings.php" );
            return $settings;

        }




        /*
        |--------------------------------------------------------------------------
        | WP Integration
        |--------------------------------------------------------------------------
        */

        /**
         * Add plugin settings link custom action for the plugin in the plugin listings.
         *
         * @since 1.1.0
         * @access public
         *
         * @param $links
         * @param $file
         * @return boolean
         */
        public function plugin_listing_custom_action_links( $links , $file ) {

            if ( $file == $this->_plugin_constants->PLUGIN_BASENAME() . '/after-sale-surveys.php' ) {

                $settings_link = '<a href="admin.php?page=wc-settings&tab=ass_settings">' . __( 'Settings' , 'after-sale-surveys' ) . '</a>';
                array_unshift( $links , $settings_link );

            }

            return $links;

        }

    }

}
