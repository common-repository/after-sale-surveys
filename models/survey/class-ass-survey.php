<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'ASS_Survey' ) ) {

    /**
     * Class ASS_Survey
     *
     * Model that houses the logic relating to Survey.
     *
     * @since 1.0.0
     */
    final class ASS_Survey {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of ASS_Survey.
         *
         * @since 1.0.0
         * @access private
         * @var ASS_Survey
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
         * ASS_Survey constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of ASS_Survey model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'ASS_Constants' ];

        }

        /**
         * Ensure that only one instance of ASS_Survey is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of ASS_Survey model.
         * @return ASS_Survey
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Get survey ids to load.
         *
         * @since 1.0.1
         * @access public
         *
         * @param $order_id WC_Order Order Id.
         * @return array Array of stdClass objects.
         */
        public function get_surveys_to_load( $order_id ) {

            $number_of_surveys_to_load = apply_filters( 'as_survey_number_of_surveys_to_load' , 1 );
            $order                     = wc_get_order( $order_id );
            $customer                  = $order->get_user();

            if ( !$customer ) {

                $customer = new stdClass();

                $customer->ID         = 0;
                $customer->first_name = ASS_Helper::get_order_data( $order , 'billing_first_name' );
                $customer->last_name  = ASS_Helper::get_order_data( $order , 'billing_last_name' );
                $customer->user_email = ASS_Helper::get_order_data( $order , 'billing_email' );
                $customer->roles      = array( 'guest' );

            }

            $surveys = ASS_Helper::get_all_surveys( $number_of_surveys_to_load );
            $surveys = apply_filters( 'as_survey_filter_surveys' , $surveys , $order , $customer );

            return $surveys;

        }

        /**
         * Render survey cta.
         *
         * @since 1.0.0
         * @access public
         */
        public function render_survey_cta( $survey_id , $order_id ) {

            $survey_cta_title   = get_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_CTA_TITLE() , true );
            $survey_cta_content = get_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_CTA_CONTENT() , true );

            include ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'survey/cta/survey-popup-cta.php' );

        }

        /**
         * Render after sale survey pop up.
         * This includes the pop up itself, hooks and the survey from.
         * Note: hooks are used in order to add the survey cta and thank you as well.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $order_id
         * @return string
         */
        public function render_survey_popup( $order_id ) {

            $surveys = $this->get_surveys_to_load( $order_id );

            foreach ( $surveys as $survey ) {

                $survey_id        = $survey->ID;
                $survey_post      = get_post( $survey_id );
                $survey_questions = get_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_QUESTIONS() , true );
                if ( !is_array( $survey_questions ) )
                    $survey_questions = array();

                $survey_submission_controls = '';

                // Construct survey heading
                ob_start();
                wc_get_template( 'survey/survey-heading.php' , array( 'survey_post' => $survey_post ) , $this->_plugin_constants->THEME_TEMPLATE_PATH() , $this->_plugin_constants->TEMPLATES_ROOT_PATH() );
                $survey_heading_markup = ob_get_clean();

                if ( empty( $survey_questions ) || ( isset( $survey_questions[1] ) && empty( $survey_questions[1] ) ) ) {

                    // TODO: Log errors
                    $survey_questions_markup = '<p class="survey-no-registered-questions">' . __( 'This survey has no registered questions' , 'after-sale-surveys' ) . '</p>';

                } else {

                    // Construct survey questions
                    $survey_questions = get_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_QUESTIONS() , true );
                    if ( !is_array( $survey_questions ) )
                        $survey_questions = array();

                    ob_start(); ?>

                    <div class="survey-questions">

                        <?php foreach( $survey_questions as $page_number => $question_data ) {

                            foreach( $question_data as $order_number => $question ) {

                                $located = wc_locate_template( 'question-types/' . $question[ 'question-type' ] . '.php' , $this->_plugin_constants->THEME_TEMPLATE_PATH() , $this->_plugin_constants->TEMPLATES_ROOT_PATH() );

                                if ( file_exists( $located ) )
                                    wc_get_template( 'question-types/' . $question[ 'question-type' ] . '.php' , array( 'survey_id' => $survey_id , 'page_number' => $page_number ,'order_number' => $order_number , 'question' => $question ) , $this->_plugin_constants->THEME_TEMPLATE_PATH() , $this->_plugin_constants->TEMPLATES_ROOT_PATH() );
                                else
                                    do_action( 'as_survey_load_question_template' , $question , $survey_id , $page_number , $order_number );

                            }

                            break; // Only iterate once. ASS only has the notion of 1 paged survey

                        } ?>

                    </div><!--#survey-questions-->

                    <?php $survey_questions_markup = ob_get_clean();

                    // Construct survey submission controls
                    ob_start();
                    wc_get_template( 'survey/survey-submission-controls.php' , array() , $this->_plugin_constants->THEME_TEMPLATE_PATH() , $this->_plugin_constants->TEMPLATES_ROOT_PATH() );
                    $survey_submission_controls = ob_get_clean();

                }

                ob_start(); ?>

                <div id="after-sale-survey-popup-<?php echo $survey_id; ?>" class="after-sale-survey-popup white-popup mfp-hide" <?php do_action( 'assp_survey_popup_data_attributes' , $survey_id ); ?> data-survey-id="<?php echo $survey_id; ?>">

                    <?php do_action( 'as_survey_before_survey' , $survey_id , $order_id ); ?>

                    <form class='after-sale-survey' data-survey-id='<?php echo $survey_id; ?>' autocomplete='off'>

                        <div class="survey-meta" style="display: none !important;">
                            <span class="order-id"><?php echo $order_id; ?></span>
                            <span class="user-id"><?php echo get_current_user_id(); ?></span>
                        </div>

                        <?php echo $survey_heading_markup . $survey_questions_markup . $survey_submission_controls; ?>

                    </form>

                    <?php do_action( 'as_survey_after_survey' , $survey_id , $order_id ); ?>

                </div>

                <?php
                $full_survey_markup = ob_get_clean();

                echo apply_filters( 'as_survey_full_survey_markup' , $full_survey_markup );

            }

        }

        /**
         * Render after sale survey thank you.
         *
         * @since 1.0.0
         * @access public
         */
        public function render_survey_thank_you( $survey_id , $order_id ) {

            $survey_thank_you_title   = get_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_THANK_YOU_TITLE() , true );
            $survey_thank_you_content = get_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_THANK_YOU_CONTENT() , true );

            include ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'survey/thank-you/survey-thank-you.php' );

        }

    }

}
