<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'ASS_AJAX_Interfaces' ) ) {

    /**
     * Class ASS_AJAX_Interfaces
     *
     * Model that houses the various AJAX interfaces of the plugin.
     *
     * @since 1.0.1
     */
    final class ASS_AJAX_Interfaces {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of ASS_AJAX_Interfaces.
         *
         * @since 1.0.1
         * @access private
         * @var ASS_AJAX_Interfaces
         */
        private static $_instance;

        /**
         * ASS_Constants instance. Holds various constants this class uses.
         *
         * @since 1.0.1
         * @access private
         * @var ASS_Constants
         */
        private $_plugin_constants;

        /**
         * Property that wraps the logic of survey reports.
         *
         * @since 1.0.1
         * @access private
         * @var ASS_Survey_Report
         */
        private $_survey_report;



        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
        */

        /**
         * Cloning is forbidden.
         *
         * @since 1.0.1
         * @access public
         */
        public function __clone () {

            _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'after-sale-surveys' ) , '1.0.1' );

        }

        /**
         * Unserializing instances of this class is forbidden.
         *
         * @since 1.0.1
         * @access public
         */
        public function __wakeup () {

            _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'after-sale-surveys' ) , '1.0.1' );

        }

        /**
         * ASS_AJAX_Interfaces constructor.
         *
         * @since 1.0.1
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants    = $dependencies[ 'ASS_Constants' ];
            $this->_survey_report       = $dependencies[ 'ASS_Survey_Report' ];

        }

        /**
         * Ensure that there is only one instance of ASS_AJAX_Interfaces is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.1
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return ASS_AJAX_Interfaces
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

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
         * @access public
         */
        public function as_survey_record_survey_offer_attempt() {

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'as-survey-record-survey-offer-attempt' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'after-sale-surveys' ) );

				$survey_id = filter_var( $_POST[ 'survey_id' ] , FILTER_SANITIZE_NUMBER_INT );
				$order_id  = filter_var( $_POST[ 'order_id' ] , FILTER_SANITIZE_NUMBER_INT );

                $new_index = $this->_survey_report->record_survey_offer_attempt( $survey_id , $order_id );

                if ( is_wp_error( $new_index ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $new_index->get_error_message()
                    );

                } else {

                    $response = array(
                        'status' => 'success',
                        'index'  => $new_index
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

			} else
                wp_die( __( 'Invalid AJAX Call' , 'after-sale-surveys' ) );

        }

        /**
         * Record survey uptake event. The customer accepted to participate the survey so the survey questions are then shown.
         *
         * @since 1.0.1
         * @access public
         */
        public function as_survey_record_survey_uptake() {

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'as-survey-record-survey-uptake' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'after-sale-surveys' ) );

				$survey_id = filter_var( $_POST[ 'survey_id' ] , FILTER_SANITIZE_NUMBER_INT );
				$order_id  = filter_var( $_POST[ 'order_id' ] , FILTER_SANITIZE_NUMBER_INT );

                $new_index = $this->_survey_report->record_survey_uptake( $survey_id , $order_id );

                if ( is_wp_error( $new_index ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $new_index->get_error_message()
                    );

                } else {

                    $response = array(
                        'status' => 'success',
                        'index'  => $new_index
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

			} else
                wp_die( __( 'Invalid AJAX Call' , 'after-sale-surveys' ) );

        }

        /**
         * Record survey completion. The customer completed the survey.
         *
         * @since 1.0.1
         * @access public
         */
        public function as_survey_record_survey_completion() {

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'as-survey-record-survey-completion' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'after-sale-surveys' ) );

				$survey_id   = filter_var( $_POST[ 'survey_id' ] , FILTER_SANITIZE_NUMBER_INT );
				$order_id    = filter_var( $_POST[ 'order_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $response_id = filter_var( $_POST[ 'response_id' ] , FILTER_SANITIZE_NUMBER_INT );

                $new_index = $this->_survey_report->record_survey_completion( $survey_id , $order_id , $response_id );

                if ( is_wp_error( $new_index ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $new_index->get_error_message()
                    );

                } else {

                    $response = array(
                        'status' => 'success',
                        'index'  => $new_index
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

			} else
                wp_die( __( 'Invalid AJAX Call' , 'after-sale-surveys' ) );

        }

    }

}
