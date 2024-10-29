<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'ASS_Script_Loader' ) ) {

    /**
     * Class ASS_Script_Loader
     *
     * Model that houses the logic of loading various js and css scripts After Sale Surveys plugin utilizes.
     *
     * @since 1.0.0
     */
    final class ASS_Script_Loader {

        /**
         * Property that holds the single main instance of ASS_Script_Loader.
         *
         * @since 1.0.0
         * @access private
         * @var ASS_Script_Loader
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
         * Property that wraps the logic of survey.
         *
         * @since 1.0.0
         * @access private
         * @var ASS_Survey
         */
        private $_survey;

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
         * ASS_Script_Loader constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         */
        public function __construct( $dependencies ) {
            
            $this->_plugin_constants         = $dependencies[ 'ASS_Constants' ];
            $this->_survey                   = $dependencies[ 'ASS_Survey' ];
            $this->_initial_guided_tour      = $dependencies[ 'ASS_Initial_Guided_Tour' ];
            $this->_survey_entry_guided_tour = $dependencies[ 'ASS_Survey_Entry_Guided_Tour' ];

        }

        /**
         * Ensure that there is only one instance of ASS_Script_Loader is loaded or can be loaded.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return ASS_Script_Loader
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;
            
        }

        /**
         * Load backend js and css scripts.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $handle Unique identifier of the current backend page.
         */
        public function load_backend_scripts( $handle ) {

            $screen = get_current_screen();

            $post_type = get_post_type();
            if ( !$post_type && isset( $_GET[ 'post_type' ] ) )
                $post_type = $_GET[ 'post_type' ];

            if ( ( $handle == 'post-new.php' || $handle == 'post.php' ) && $post_type == $this->_plugin_constants->SURVEY_CPT_NAME() ) {
                // 'as_survey' cpt new post and edit single post page

                wp_enqueue_style( 'as_survey_datatables_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/DataTables/datatables.min.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'as_survey_magnific-popup_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/magnific-popup/magnific-popup.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'as_survey_vex_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/css/vex.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'as_survey_vex-theme-plain_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/css/vex-theme-plain.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'as_survey_survey-cpt_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'survey/cpt/survey-cpt.css' , array() , $this->_plugin_constants->VERSION() , 'all' );

                wp_enqueue_script( 'as_survey_datatables_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/DataTables/datatables.min.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'as_survey_magnific-popup_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/magnific-popup/magnific-popup.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'as_survey_vex_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/js/vex.combined.min.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'as_survey-cta_js' , $this->_plugin_constants->JS_ROOT_URL() . 'survey/cpt/survey-cta.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'as_survey-thank-you_js' , $this->_plugin_constants->JS_ROOT_URL() . 'survey/cpt/survey-thank-you.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'as_survey_questions-backend-ajax-services_js' , $this->_plugin_constants->JS_ROOT_URL() . 'survey/cpt/questions-backend-ajax-services.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'as_survey_questions-datatables-config_js' , $this->_plugin_constants->JS_ROOT_URL() . 'survey/cpt/questions-datatables-config.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'as_survey_questions-meta-box_js' , $this->_plugin_constants->JS_ROOT_URL() . 'survey/cpt/questions-meta-box.js' , array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-sortable' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'as_survey_survey-cpt_js' , $this->_plugin_constants->JS_ROOT_URL() . 'survey/cpt/survey-cpt.js' , array( 'jquery' , 'as_survey-cta_js' , 'as_survey-thank-you_js' ) , $this->_plugin_constants->VERSION() , true );
                
                wp_localize_script( 'as_survey-cta_js' , 'survey_cta_params' , array(
                    'i18n_cta_title_empty'               => _x( 'Survey CTA Title Empty' , 'after-sale-surveys' ),
                    'i18n_cta_content_empty'             => _x( 'Survey CTA Content Empty' , 'after-sale-surveys' ),
                    'i18n_please_fill_cta_form_properly' => _x( 'Please Fill Survey CTA Form Properly' , 'after-sale-surveys' ),
                    'i18n_survey_cta_save_success'       => _x( 'Successfully Saved Survey CTA Data' , 'after-sale-surveys' ),
                    'i18n_survey_cta_save_fail'          => _x( 'Failed To Saved Survey CTA Data' , 'after-sale-surveys' )
                ) );

                wp_localize_script( 'as_survey-thank-you_js' , 'survey_thankyou_params' , array(
                    'i18n_thankyou_title_empty'               => _x( 'Survey Thank You Title Empty' , 'after-sale-surveys' ),
                    'i18n_thankyou_content_empty'             => _x( 'Survey Thank You Content Empty' , 'after-sale-surveys' ),
                    'i18n_please_fill_thankyou_form_properly' => _x( 'Please Fill Survey Thank You Form Properly' , 'after-sale-surveys' ),
                    'i18n_survey_thankyou_save_success'       => _x( 'Successfully Saved Survey Thank You Data' , 'after-sale-surveys' ),
                    'i18n_survey_thankyou_save_fail'          => _x( 'Failed To Saved Survey Thank You Data' , 'after-sale-surveys' )
                ) );

                wp_localize_script( 'as_survey_questions-datatables-config_js' , 'questions_datatables_config_params' , array(
                    'i18n_empty_survey' => _x( 'No survey questions' , 'after-sale-surveys' )
                ) );
                
                wp_localize_script( 'as_survey_questions-meta-box_js' , 'survey_questions_meta_box_params' , array(
                    'i18n_no_choices_available'     => _x( 'No Choices Available' , 'after-sale-surveys' ),
                    'i18n_retrieving_choices'       => _x( 'Please Wait. Retrieving Choices...' , 'after-sale-surveys' ),
                    'i18n_retrieving_question_data' => _x( 'Please wait. Retrieving question data...' , 'after-sale-surveys' ),
                    'i18n_edit_question'            => _x( 'Edit Question' , 'after-sale-surveys' ),
                    'i18n_order_number'             => _x( 'Order Number' , 'after-sale-surveys' ),
                    'i18n_question_text'            => _x( 'Question Text' , 'after-sale-surveys' ),
                    'i18n_question_type'            => _x( 'Question Type' , 'after-sale-surveys' ),
                    'i18n_no_choices_supplied'      => _x( 'No Multiple Choices' , 'after-sale-surveys' ),
                    'i18n_confirm_delete_question'  => _x( 'Are you sure to delete this question?' , 'after-sale-surveys' ),
                    'i18n_question_deleted'         => _x( 'Question successfully deleted' , 'after-sale-surveys' ),
                    'i18n_add_choice_text'          => _x( 'Please add choice text' , 'after-sale-surveys' ),
                    'i18n_add_question'             => _x( 'Add Question' , 'after-sale-surveys' ),
                    'i18n_fill_form_properly'       => _x( 'Some fields are not field up properly' , 'after-sale-surveys' ),
                    'i18n_question_added'           => _x( 'New question successfully added' , 'after-sale-surveys' ),
                    'i18n_question_add_failed'      => _x( 'Failed to add new question' , 'after-sale-surveys' ),
                    'i18n_question_edited'          => _x( 'Question successfully edited' , 'after-sale-surveys' ),
                    'i18n_question_edit_failed'     => _x( 'Cannot edit question' , 'after-sale-surveys' ),
                ) );

            } elseif ( ( $handle == 'post-new.php' || $handle == 'post.php' ) && $post_type == $this->_plugin_constants->SURVEY_RESPONSE_CPT_NAME() ) {
                // 'as_survey_response' cpt new post and edit single post page

                wp_enqueue_style( 'as_survey_survey-response-meta-box_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'survey/response/cpt/survey-response-meta-box.css' , array() , $this->_plugin_constants->VERSION() , 'all' );

            } elseif ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'wc-reports' && isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'as_survey_responses' ) {
                // Reports page.

                wp_enqueue_style( 'as_survey_vex_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/css/vex.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'as_survey_vex-theme-plain_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/css/vex-theme-plain.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'as_survey_chosen_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/chosen/chosen.min.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'as_survey_survey-response-general-report_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'survey/report/general-report/survey-response-general-report.css' , array() , $this->_plugin_constants->VERSION() , 'all' );

                wp_enqueue_script( 'as_survey_google_chart_js' , 'https://www.gstatic.com/charts/loader.js' , array() , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'as_survey_vex_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/js/vex.combined.min.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'as_survey_chosen_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/chosen/chosen.jquery.min.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'as_survey_survey-report-backend-ajax-services_js' , $this->_plugin_constants->JS_ROOT_URL() . 'survey/report/general-report/survey-report-backend-ajax-services.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'as_survey_survey-response-general-report_js' , $this->_plugin_constants->JS_ROOT_URL() . 'survey/report/general-report/survey-response-general-report.js' , array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-tabs' , 'jquery-ui-datepicker' ) , $this->_plugin_constants->VERSION() , true );
                wp_localize_script( 'as_survey_survey-response-general-report_js' , 'survey_response_general_report_params' , array(
                    'i18n_generating_reports' => _x( 'Please wait. Generating reports...' , 'after-sale-surveys' ),
                    'i18n_view_survey_stats'  => _x( '&larr; Choose a survey to view stats' , 'after-sale-surveys' ),
                    'i18n_survey_select'      => _x( 'Please select a survey' , 'after-sale-surveys' ),
                    'i18n_survey'             => _x( 'Survey:' , 'after-sale-surveys' ),
                    'i18n_order_no'           => _x( 'Order No' , 'after-sale-surveys' ),
                    'i18n_date'               => _x( 'Date' , 'after-sale-surveys' ),
                    'i18n_user'               => _x( 'User' , 'after-sale-surveys' ),
                    'i18n_response_details'   => _x( 'Response Details' , 'after-sale-surveys' ),
                    'i18n_details'            => _x( 'Details' , 'after-sale-surveys' ),
                    'i18n_total_responses'    => _x( 'Total Respones:' , 'after-sale-surveys' )
                ) );

            } elseif ( in_array( $screen->id, array( 'woocommerce_page_wc-settings' ) ) && isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'ass_settings' ) {

                // Settings

                if ( !isset( $_GET[ 'section' ] ) || $_GET[ 'section' ] == '' ) {

                    // General

                    wp_enqueue_style( 'as_survey_general-options_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'survey/settings/general/general-options.css' , array() , $this->_plugin_constants->VERSION() , 'all' );

                }

            }

            // Help Pointers
            if ( get_option( ASS_Initial_Guided_Tour::OPTION_INITIAL_GUIDED_TOUR_STATUS , false ) == ASS_Initial_Guided_Tour::STATUS_OPEN && array_key_exists( $screen->id , $this->_initial_guided_tour->get_screens() ) ) {

                wp_enqueue_style( 'ass_plugin-guided-tour_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'admin/plugin-guided-tour.css' , array( 'wp-pointer' ) , $this->_plugin_constants->VERSION() , 'all' );                

                wp_enqueue_script( 'ass_plugin-initial-guided-tour_js' , $this->_plugin_constants->JS_ROOT_URL() . 'admin/plugin-initial-guided-tour.js' , array( 'wp-pointer' , 'thickbox' ) , $this->_plugin_constants->VERSION() , true );

                wp_localize_script( 'ass_plugin-initial-guided-tour_js' , 'ass_initial_guided_tour_params', array(
                    'actions' => array( 'close_tour' => 'ass_close_initial_guided_tour' ),
                    'nonces'  => array( 'close_tour' => wp_create_nonce( 'ass-close-initial-guided-tour' ) ),
                    'screen'  => $this->_initial_guided_tour->get_current_screen(),
                    'height'  => 640,
                    'width'   => 640,
                    'texts'   => array(
                                    'btn_prev_tour'  => __( 'Previous' , 'after-sale-surveys' ),
                                    'btn_next_tour'  => __( 'Next' , 'after-sale-surveys' ),
                                    'btn_close_tour' => __( 'Close' , 'after-sale-surveys' ),
                                    'btn_start_tour' => __( 'Start Tour' , 'after-sale-surveys' )
                                ),
                    'urls'    => array( 'ajax' => admin_url( 'admin-ajax.php' ) ),
                    'post'    => isset( $post ) && isset( $post->ID ) ? $post->ID : 0
                ) );

            }
            
            if ( get_option( ASS_Survey_Entry_Guided_Tour::OPTION_SURVEY_ENTRY_GUIDED_TOUR_STATUS , false ) == ASS_Survey_Entry_Guided_Tour::STATUS_OPEN && array_key_exists( $screen->id , $this->_survey_entry_guided_tour->get_screens() ) ) {
                
                wp_enqueue_style( 'ass_plugin-guided-tour_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'admin/plugin-guided-tour.css' , array( 'wp-pointer' ) , $this->_plugin_constants->VERSION() , 'all' );                

                wp_enqueue_script( 'ass_plugin-survey-entry-guided-tour_js' , $this->_plugin_constants->JS_ROOT_URL() . 'admin/plugin-survey-entry-guided-tour.js' , array( 'wp-pointer' , 'thickbox' ) , $this->_plugin_constants->VERSION() , true );

                wp_localize_script( 'ass_plugin-survey-entry-guided-tour_js' , 'ass_survey_entry_guided_tour_params', array(
                    'actions' => array( 'close_tour' => 'ass_close_survey_entry_guided_tour' ),
                    'nonces'  => array( 'close_tour' => wp_create_nonce( 'ass-close-survey-entry-guided-tour' ) ),
                    'screen'  => $this->_survey_entry_guided_tour->get_current_screen(),
                    'height'  => 640,
                    'width'   => 640,
                    'texts'   => array(
                                    'btn_prev_tour'  => __( 'Previous' , 'after-sale-surveys' ),
                                    'btn_next_tour'  => __( 'Next' , 'after-sale-surveys' ),
                                    'btn_close_tour' => __( 'Close' , 'after-sale-surveys' ),
                                    'btn_start_tour' => __( 'Start Tour' , 'after-sale-surveys' )
                                ),
                    'urls'    => array( 'ajax' => admin_url( 'admin-ajax.php' ) ),
                    'post'    => isset( $post ) && isset( $post->ID ) ? $post->ID : 0
                ) );

            }

        }

        /**
         * Load frontend js and css scripts.
         *
         * @since 1.0.0
         * @access public
         */
        public function load_frontend_scripts() {

            global $post, $wp;

            if ( is_checkout() && !empty( $wp->query_vars[ 'order-received' ] ) ) {
                // Order received page

                $order_id = $wp->query_vars[ 'order-received' ];                
                $surveys  = $this->_survey->get_surveys_to_load( $order_id );

                if ( !empty( $surveys ) ) {

                    wp_enqueue_style( 'as_survey_magnific-popup_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/magnific-popup/magnific-popup.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                    wp_enqueue_style( 'as_survey_vex_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/css/vex.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                    wp_enqueue_style( 'as_survey_vex-theme-plain_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/css/vex-theme-plain.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                    wp_enqueue_style( 'as_survey_multiple-choice-single-answer_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'survey/frontend/question-types/multiple-choice-single-answer.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                    wp_enqueue_style( 'as_survey_after-sale-survey_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'survey/frontend/after-sale-survey.css' , array() , $this->_plugin_constants->VERSION() , 'all' );

                    wp_enqueue_script( 'as_survey_magnific-popup_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/magnific-popup/magnific-popup.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                    wp_enqueue_script( 'as_survey_vex_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/js/vex.combined.min.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                    wp_enqueue_script( 'as_survey_after-sale-survey_js' , $this->_plugin_constants->JS_ROOT_URL() . 'survey/front-end/after-sale-survey.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );

                    wp_localize_script( 'as_survey_after-sale-survey_js' , 'after_sale_survey_params' , array(
                        'ajaxurl'                           => admin_url( 'admin-ajax.php' ),
                        'options'                           => apply_filters( 'as_survey_frontend_survey_options' , array( 'time_delay' => 2000 ) ),
                        'nonce_record_survey_offer_attempt' => wp_create_nonce( 'as-survey-record-survey-offer-attempt' ),
                        'nonce_record_survey_uptake'        => wp_create_nonce( 'as-survey-record-survey-uptake' ),
                        'nonce_record_survey_completion'    => wp_create_nonce( 'as-survey-record-survey-completion' ),
                        'i18n_improper_filled_form'         => _x( 'Some fields are not filled properly' , 'after-sale-surveys' ),
                        'i18n_response_saved'               => _x( 'Survey response successfully save' , 'after-sale-surveys' ),
                        'i18n_response_save_failed'         => _x( 'Failed to save survey response' , 'after-sale-surveys' )
                    ) );

                    do_action( 'assp_load_after_sale_surveys_scripts' , $surveys );

                }

            }

        }

    }

}
