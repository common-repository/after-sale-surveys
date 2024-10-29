<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'ASS_Survey_Report' ) ) {

    /**
     * Class ASS_Survey_Report
     *
     * Model that houses the logic of generating various survey reports.
     *
     * @since 1.0.0
     */
    final class ASS_Survey_Report {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of ASS_Survey_Report.
         *
         * @since 1.0.0
         * @access private
         * @var ASS_Survey_Report
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
         * ASS_Survey_Report constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of ASS_Survey_Report model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'ASS_Constants' ];

        }

        /**
         * Ensure that only one instance of ASS_Survey_Report is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of ASS_Survey_Report model.
         * @return ASS_Survey_Report
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Integrate after sale survey's plugin reporting to WooCommerce reports.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $reports
         * @return mixed
         */
        public function after_sale_survey_report( $reports ) {

            $report_sections = array(
                                    "general" => array(
                                                    'title'       => __( 'General' , 'after-sale-surveys' ),
                                                    'description' => '',
                                                    'hide_title'  => true,
                                                    'callback'    => array( $this , 'render_general_report' )
                                                )
                                );

            $report_sections = apply_filters( 'as_survey_report_sections' , $report_sections );

            $reports[ 'as_survey_responses' ] = array(
                                                    'title'   => __( 'Survey Responses' , 'after-sale-surveys' ),
                                                    'reports' => $report_sections
                                                );

            return $reports;

        }




        /*
        |--------------------------------------------------------------------------
        | Views
        |--------------------------------------------------------------------------
        */

        /**
         * Render the general report.
         *
         * @since 1.0.0
         * @access public
         */
        public function render_general_report() {

            $surveys = ASS_Helper::get_all_surveys( apply_filters( 'as_survey_surveys_to_view_report' , 1 ) );

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'survey/report/survey-general-report.php' );

        }

        


        /*
        |--------------------------------------------------------------------------
        | Record Survey Stats
        |--------------------------------------------------------------------------
        */

        /**
         * Record an attempt to offer the survey to a customer. Survey CTA is shown.
         *
         * @since 1.0.1
         * @since 1.1.0 Records data to plugin custom table.
         * @access public
         *
         * @param int $survey_id Survey Id.
         * @param int $order_id  Order Id that triggered the survey.
         * @return int ID of last entry.
         */
        public function record_survey_offer_attempt( $survey_id , $order_id ) {

            global $wpdb;

            if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $this->_plugin_constants->CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS() . "'" ) ) {

                $current_user = wp_get_current_user();

                $wpdb->insert( 
                    $this->_plugin_constants->CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS(), 
                    array( 
                        'survey_id'       => $survey_id,
                        'customer_email'  => $current_user->ID ? $current_user->user_email : '',
                        'record_datetime' => current_time( 'mysql' ),
                        'order_id'        => $order_id,
                        'client_ip'       => ASS_Helper::get_client_ip(),
                        'user_agent'      => $_SERVER[ 'HTTP_USER_AGENT' ]
                    )
                );

                return $wpdb->insert_id; // Latest id inserted. Auto-increment field.

            } else
                return new WP_Error( 'as-survey-record_survey_offer_attempt-missing-survey-offer-attempts-table' , __( "Missing required plugin table for survey offer attempts." , "after-sale-surveys" ) , array( 'survey_id' => $survey_id , 'order_id' => $order_id ) );
            
        }

        /**
         * Record survey uptake event. The customer accepted to participate the survey so the survey questions are then shown.
         *
         * @since 1.0.1
         * @since 1.1.0 Records data to plugin custom table.
         * @access public
         *
         * @param int $survey_id Survey Id.
         * @param int $order_id  Order Id that triggered the survey.
         * @return int ID of last entry.
         */
        public function record_survey_uptake( $survey_id , $order_id ) {

            global $wpdb;

            if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $this->_plugin_constants->CUSTOM_TABLE_SURVEY_UPTAKES() . "'" ) ) {

                $current_user = wp_get_current_user();

                $wpdb->insert(
                    $this->_plugin_constants->CUSTOM_TABLE_SURVEY_UPTAKES(), 
                    array( 
                        'survey_id'       => $survey_id,
                        'customer_email'  => $current_user->ID ? $current_user->user_email : '',
                        'record_datetime' => current_time( 'mysql' ),
                        'order_id'        => $order_id,
                        'client_ip'       => ASS_Helper::get_client_ip(),
                        'user_agent'      => $_SERVER[ 'HTTP_USER_AGENT' ]
                    )
                );

                return $wpdb->insert_id; // Latest id inserted. Auto-increment field.

            } else
                return new WP_Error( 'as-survey-record_survey_uptake-missing-survey-offer-uptake-table' , __( "Missing required plugin table for survey uptakes." , "after-sale-surveys" ) , array( 'survey_id' => $survey_id , 'order_id' => $order_id ) );
            
        }

        /**
         * Record survey completion. The customer completed the survey.
         *
         * @since 1.0.1
         * @since 1.1.0 Records data to plugin custom table.
         * @access public
         *
         * @param int $survey_id   Survey Id.
         * @param int $order_id    Order Id that triggered the survey.
         * @param int $response_id Response Id.
         * @return int ID of last entry.
         */
        public function record_survey_completion( $survey_id , $order_id , $response_id ) {

            global $wpdb;

            if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $this->_plugin_constants->CUSTOM_TABLE_SURVEY_COMPLETIONS() . "'" ) ) {

                $current_user = wp_get_current_user();

                $wpdb->insert(
                    $this->_plugin_constants->CUSTOM_TABLE_SURVEY_COMPLETIONS(), 
                    array( 
                        'survey_id'       => $survey_id,
                        'customer_email'  => $current_user->ID ? $current_user->user_email : '',
                        'record_datetime' => current_time( 'mysql' ),
                        'order_id'        => $order_id,
                        'response_id'     => $response_id,
                        'client_ip'       => ASS_Helper::get_client_ip(),
                        'user_agent'      => $_SERVER[ 'HTTP_USER_AGENT' ]
                    )
                );

                return $wpdb->insert_id; // Latest id inserted. Auto-increment field.
                
            } else
                return new WP_Error( 'as-survey-record_survey_completion-missing-survey-offer-uptake-table' , __( "Missing required plugin table for survey completions." , "after-sale-surveys" ) , array( 'survey_id' => $survey_id , 'order_id' => $order_id , 'response_id' => $response_id ) );
            
        }

        

        
        /*
        |--------------------------------------------------------------------------
        | AJAX Interfaces
        |--------------------------------------------------------------------------
        */

        /**
         * Get survey response report data.
         *
         * @since 1.0.0
         * @access public
         *
         * @param null $survey_id
         * @param null $filters
         * @param bool|true $ajax_call
         * @return array
         */
        public function as_survey_get_survey_responses_report( $survey_id = null , $filters = null , $ajax_call = true ) {

            if ( $ajax_call === true ) {

                $survey_id = $_POST[ 'survey_id' ];
                $filters   = $_POST[ 'filters' ];

            }

            if ( get_post_status( $survey_id ) === false ) {

                $response = array(
                                'status'        => 'fail',
                                'error_message' => __( 'Specified survey does not exist' , 'after-sale-surveys' )
                            );

            } elseif ( get_post_type( $survey_id ) != $this->_plugin_constants->SURVEY_CPT_NAME() ) {

                $response = array(
                                'status'        => 'fail',
                                'error_message' => __( 'Specified survey id does not represent a survey post type' , 'after-sale-surveys' )
                            );

            } else {

                $survey_questions = get_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_QUESTIONS() , true );
                if ( !is_array( $survey_questions ) )
                    $survey_questions = array();

                $survey_responses = ASS_Helper::get_survey_responses( $survey_id , $filters );
                if ( !is_array( $survey_responses ) )
                    $survey_responses = array();

                if ( array_key_exists( 'error_filters' , $survey_responses ) ) {

                    $err_msg = __( 'There are errors on the survey report filters' , 'after-sale-surveys' ) . "<br/>";

                    foreach ( $survey_responses[ 'error_filters' ] as $err )
                        $err_msg .= $err . "<br/>";

                    $response = array(
                                    'status'        => 'fail',
                                    'error_message' => $err_msg
                                );

                } else {

                    if ( empty( $survey_questions ) ) {

                        $response = array(
                            'status'        => 'fail',
                            'error_message' => __( 'Specified survey does not have any questions' , 'after-sale-surveys' )
                        );

                    } elseif ( empty( $survey_responses ) ) {

                        if ( !empty( $filters ) ) {

                            $response = array(
                                'status'        => 'fail',
                                'error_message' => __( 'No survey responses data for the specified filter/s' , 'after-sale-surveys' )
                            );

                        } else {

                            $response = array(
                                'status'        => 'fail',
                                'error_message' => __( 'Specified survey does not have any responses' , 'after-sale-surveys' )
                            );

                        }

                    } else {

                        // Construct response data report
                        $survey_response_report = array();

                        foreach ( $survey_questions as $page_number => $questions_data ) {

                            foreach ( $questions_data as $order_number => $question ) {

                                $question_response_report = $this->construct_question_response_report( $survey_id , $question , $survey_responses , $page_number , $order_number );
                                $survey_response_report[] = $question_response_report;

                            }

                        }

                        $response = array(
                            'status'               => 'success',
                            'total_responses'      => count( $survey_responses ),
                            'response_data_report' => $survey_response_report
                        );

                    }

                }

            }

            if ( $ajax_call === true ) {

                header( "Content-Type: application/json" );
                echo json_encode( $response );
                die();

            } else
                return $response;

        }

        /**
         * Construct the report data for the responses of a specific question.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $survey_id
         * @param $question
         * @param $survey_responses
         * @param $page_number
         * @param $order_number
         * @return mixed
         */
        public function construct_question_response_report( $survey_id , $question , $survey_responses , $page_number , $order_number ) {

            if ( $question[ 'question-type' ] == 'multiple-choice-single-answer' ) {

                $responses_data_arr = array();

                foreach ( $survey_responses as $response_obj ) {

                    $response_data = get_post_meta( $response_obj->ID , $this->_plugin_constants->POST_META_SURVEY_RESPONSES() , true );

                    if ( isset( $response_data[ $page_number ][ $order_number ][ 'answer' ] ) && !in_array( $response_data[ $page_number ][ $order_number ][ 'answer' ] , array( '' , false , null ) ) )
                        $responses_data_arr[] = $response_data[ $page_number ][ $order_number ][ 'answer' ];

                }

                // TODO: Important: Here is the tricky part. check if answer has special characters, this is gonna be an issue.
                $counted_responses_data_arr = array_count_values( $responses_data_arr );

                foreach ( $question[ 'responses' ][ 'multiple-choices' ] as $choice )
                    if ( !array_key_exists( $choice , $counted_responses_data_arr ) )
                        $counted_responses_data_arr[ $choice ] = 0;

                $question_response_report   = array(
                                                'page-number'      => $page_number,
                                                'order-number'     => $order_number,
                                                'question-type'    => $question[ 'question-type' ],
                                                'question-text'    => $question[ 'question-text' ],
                                                'multiple-choices' => $counted_responses_data_arr
                                            );

            } else
                $question_response_report = array();

            return apply_filters( 'as_survey_' . $question[ 'question-type' ] . '_question_response_report' , $question_response_report , $survey_id , $question , $survey_responses , $page_number , $order_number );

        }

        /**
         * Retrieve list of responses of a given survey. Each responses item list contains information about each specific response.
         *
         * @since 1.0.0
         * @access public
         *
         * @param null $survey_id
         * @param null $filters
         * @param bool|true $ajax_call
         * @return array
         */
        public function as_survey_get_survey_responses_list( $survey_id = null , $filters = null , $ajax_call = true ) {

            if ( $ajax_call === true ) {

                $survey_id = $_POST[ 'survey_id' ];
                $filters   = $_POST[ 'filters' ];

            }

            if ( get_post_status( $survey_id ) === false ) {

                $response = array(
                    'status'        => 'fail',
                    'error_message' => __( 'Specified survey does not exist' , 'after-sale-surveys' )
                );

            } elseif ( get_post_type( $survey_id ) != $this->_plugin_constants->SURVEY_CPT_NAME() ) {

                $response = array(
                    'status'        => 'fail',
                    'error_message' => __( 'Specified survey id does not represent a survey post type' , 'after-sale-surveys' )
                );

            } else {

                $survey_responses = ASS_Helper::get_survey_responses( $survey_id , $filters );
                if ( !is_array( $survey_responses ) )
                    $survey_responses = array();

                if ( empty( $survey_responses ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => __( 'Specified survey does not have any responses' , 'after-sale-surveys' )
                    );

                } else {

                    // Construct table report data
                    $table_report_data = array();

                    foreach ( $survey_responses as $survey_response ) {

                        $order_id = get_post_meta( $survey_response->ID , $this->_plugin_constants->POST_META_RESPONSE_ORDER_ID() , true );
                        $user_id  = get_post_meta( $survey_response->ID , $this->_plugin_constants->POST_META_RESPONSE_USER_ID() , true );

                        $order_url    = get_admin_url() . "post.php?post=" . $order_id . "&action=edit";
                        $response_url = get_admin_url() . "post.php?post=" . $survey_response->ID . "&action=edit";

                        if ( $user_id ) {

                            $user_data     = get_userdata( $user_id );
                            $user_fullname = $user_data->first_name . " " . $user_data->last_name;
                            $user_url      = get_admin_url() . "user-edit.php?user_id=" . $user_id;

                        } else {

                            $order_obj     = new WC_Order( $order_id );
                            $user_fullname = $order_obj->get_formatted_billing_full_name();
                            $user_url      = "";

                        }

                        $table_report_data[] = apply_filters( 'as_survey_table_report_data_item' , array(
                                                                                                        'order_id'      => $order_id,
                                                                                                        'order_url'     => $order_url,
                                                                                                        'response_date' => $survey_response->post_date,
                                                                                                        'user_fullname' => $user_fullname,
                                                                                                        'user_url'      => $user_url,
                                                                                                        'response_url'  => $response_url
                                                                                                    ) , $survey_response->ID , $survey_id , $order_id , $user_id );

                    }

                    $response = array(
                        'status'            => 'success',
                        'total_responses'   => count( $survey_responses ),
                        'table_report_data' => $table_report_data
                    );

                }

            }

            if ( $ajax_call === true ) {

                header( "Content-Type: application/json" );
                echo json_encode( $response );
                die();

            } else
                return $response;

        }

    }

}
