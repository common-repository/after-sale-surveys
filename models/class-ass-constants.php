<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'ASS_Constants' ) ) {

    /**
     * Class ASS_Constants
     *
     * Model that houses the various constants After Sale Surveys plugin utilizes.
     *
     * @since 1.0.0
     */
    final class ASS_Constants {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of ASS_Constants.
         *
         * @since 1.0.0
         * @access private
         * @var ASS_Constants
         */
        private static $_instance;

        /**
         * Property that holds the plugin's main file directory path.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_MAIN_PLUGIN_FILE_PATH;

        /**
         * Property that holds the plugin's root directory path.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_PLUGIN_DIR_PATH;

        /**
         * Property that holds the plugin's root directory url.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_PLUGIN_DIR_URL;

        /**
         * Property that holds the plugin's basename.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_PLUGIN_BASENAME;

        /**
         * Property that holds the plugin's unique token.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_TOKEN;

        /**
         * Property that holds the plugin's 'current' version.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_VERSION;

        /**
         * Property that holds the plugin's text domain. Used for internationalization.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_TEXT_DOMAIN;

        /**
         * Property that holds the 'css' root directory url.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_CSS_ROOT_URL;

        /**
         * Property that holds the 'images' root directory url.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_IMAGES_ROOT_URL;

        /**
         * Property that holds the 'js' root directory url.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_JS_ROOT_URL;

        /**
         * Property that holds the 'models' root directory path.
         *
         * @since 1.0.0
         * @access public
         * @var string
         */
        private $_MODELS_ROOT_PATH;

        /**
         * property that holds 'templates' root directory path.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_TEMPLATES_ROOT_PATH;

        /**
         * Property that holds the path of the current theme overridden plugin template files.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_THEME_TEMPLATE_PATH;

        /**
         * Property that holds the 'views' root directory path.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_VIEWS_ROOT_PATH;

        /**
         * Property that holds the Survey custom post type name.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_SURVEY_CPT_NAME;

        /**
         * Property that holds the Survey Response custom post type name.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_SURVEY_RESPONSE_CPT_NAME;

        /**
         * Property that holds the array of user roles that are allowed to manage "After Sale Surveys" plugin.
         *
         * @since 1.1.0
         * @access private
         * @var array
         */
        private $_ROLES_ALLOWED_TO_MANAGE_ASS;

        /**
         * Property that holds the Survey Response custom post type meta boxes.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_SURVEY_CPT_META_BOXES;

        /**
         * Property that holds the type of survey questions.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_QUESTION_TYPES;

        /**
         * Property that holds the question types that are considered as multiple choice questions.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_MULTIPLE_CHOICE_QUESTION_TYPES;

        /**
         * Property that holds the headings for the survey questions table on the backend.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_QUESTIONS_TABLE_HEADINGS;

        /**
         * Property that holds the table row actions for the survey questions table on the backend.
         *
         * @since 1.0.0
         * @access private
         * @var string;
         */
        private $_QUESTIONS_TABLE_ROW_ACTIONS;


        /*
        |--------------------------------------------------------------------------
        | Survey Post Meta Constants Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Survey questions post meta key.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_SURVEY_QUESTIONS;

        /**
         * Survey post meta key that holds the number of times an offer has been offered. (Survey CTA shown).
         *
         * @since 1.0.1
         * @access private
         * @var string
         */
        private $_POST_META_SURVEY_OFFER_ATTEMPTS;

        /**
         * Survey post meta key that holds the number of times a survey has been accepted.
         *
         * @since 1.0.1
         * @access private
         * @var string
         */
        private $_POST_META_SURVEY_UPTAKES;

        /**
         * Survey post meta key that holds the number of times a survey has been completed.
         *
         * @since 1.0.1
         * @access private
         * @var string
         */
        private $_POST_META_SURVEY_COMPLETIONS;


        /*
        |--------------------------------------------------------------------------
        | Plugin Custom Tables
        |--------------------------------------------------------------------------
        */

        /**
         * Survey offer attempts custom plugin table name.
         *
         * @since 1.1.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS;

        /**
         * Survey offer attempts custom plugin table version.
         *
         * @since 1.1.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS_VERSION;

        /**
         * Survey uptakes custom plugin table name.
         *
         * @since 1.1.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_SURVEY_UPTAKES;

        /**
         * Survey uptakes custom plugin table version.
         *
         * @since 1.1.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_SURVEY_UPTAKES_VERSION;

        /**
         * Survey completions custom plugin table name.
         *
         * @since 1.1.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_SURVEY_COMPLETIONS;

        /**
         * Survey completions custom plugin table version.
         *
         * @since 1.1.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_SURVEY_COMPLETIONS_VERSION;


        /*
        |--------------------------------------------------------------------------
        | Survey Response Post Meta Constants Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Survey response survey id post meta key.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_RESPONSE_SURVEY_ID;

        /**
         * Survey response order id post meta key.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_RESPONSE_ORDER_ID;

        /**
         * Survey response user id post meta key.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_RESPONSE_USER_ID;

        /**
         * Survey response user email post meta key.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_RESPONSE_USER_EMAIL;

        /**
         * Survey responses meta key.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_SURVEY_RESPONSES;

        /**
         * Survey responses meta key.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_RESPONSE_ADDITIONAL_DETAILS;


        /*
        |--------------------------------------------------------------------------
        | Post Meta
        |--------------------------------------------------------------------------
        */

        // Survey CTA

        /**
         * Property that holds the title of the survey cta of a specific survey.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_SURVEY_CTA_TITLE;

        /**
         * Property that holds the content of the survey cta of a specific survey.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_SURVEY_CTA_CONTENT;

        // Survey Thank You

        /**
         * Property that holds the title of a survey thank you of a specific survey.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_SURVEY_THANK_YOU_TITLE;

        /**
         * Propety that holds the content of a survey thank you of a specific survey.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_SURVEY_THANK_YOU_CONTENT;


        /*
        |--------------------------------------------------------------------------
        | Plugin Options
        |--------------------------------------------------------------------------
        */

        // Dev Options

        /**
         * Property that holds the option of either cleaning up all plugin options upon plugin un-installation.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_OPTION_CLEANUP_PLUGIN_OPTIONS;




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
         * ASS_Constants constructor. Initialize various property values.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct() {

            global $wpdb;

            // Paths
            $this->_MAIN_PLUGIN_FILE_PATH = WP_PLUGIN_DIR . '/after-sale-surveys/after-sale-surveys.php';

            $this->_PLUGIN_DIR_PATH = plugin_dir_path( $this->_MAIN_PLUGIN_FILE_PATH );
            $this->_PLUGIN_DIR_URL  = plugin_dir_url( $this->_MAIN_PLUGIN_FILE_PATH );
            $this->_PLUGIN_BASENAME = plugin_basename( dirname( $this->_MAIN_PLUGIN_FILE_PATH ) );

            $this->_CSS_ROOT_URL    = $this->_PLUGIN_DIR_URL . 'css/';
            $this->_IMAGES_ROOT_URL = $this->_PLUGIN_DIR_URL . 'images/';
            $this->_JS_ROOT_URL     = $this->_PLUGIN_DIR_URL . 'js/';

            $this->_MODELS_ROOT_PATH    = $this->_PLUGIN_DIR_PATH . 'models/';
            $this->_TEMPLATES_ROOT_PATH = $this->_PLUGIN_DIR_PATH . 'templates/';
            $this->_THEME_TEMPLATE_PATH = apply_filters( 'as_survey_theme_template_path' , 'after-sale-surveys' );
            $this->_VIEWS_ROOT_PATH     = $this->_PLUGIN_DIR_PATH . 'views/';

            $this->_TOKEN       = 'ass'; // short for after sale surveys
            $this->_VERSION     = '1.1.2';
            $this->_TEXT_DOMAIN = 'after-sale-surveys';

            $this->_SURVEY_CPT_NAME          = 'as_survey'; // Survey cpt name
            $this->_SURVEY_RESPONSE_CPT_NAME = 'as_survey_response'; // Survey response cpt name

            $this->_SURVEY_CPT_META_BOXES = apply_filters( 'as_survey_cpt_meta_boxes' , array(
                'survey-questions' => array(
                    'title'    => __( 'Survey Questions' , 'after-sale-surveys' ),
                    'callback' => 'view_survey_questions_meta_box',
                    'cpt'      => $this->_SURVEY_CPT_NAME,
                    'context'  => 'normal',
                    'priority' => 'high'
                ),
                'survey-cta' => array(
                    'title'    => __( 'Survey CTA' , 'after-sale-surveys' ),
                    'callback' => 'view_survey_cta_meta_box',
                    'cpt'      => $this->_SURVEY_CPT_NAME,
                    'context'  => 'normal',
                    'priority' => 'low'
                ),
                'survey-thank-you-message' => array(
                    'title'    => __( 'Survey Thank You Message' , 'after-sale-surveys' ),
                    'callback' => 'view_survey_thank_you_message_meta_box',
                    'cpt'      => $this->_SURVEY_CPT_NAME,
                    'context'  => 'normal',
                    'priority' => 'low'
                ),
                'after-sale-surveys-upgrade' => array(
                    'title'    => __( 'Premium Add-on' , 'after-sale-surveys' ),
                    'callback' => 'view_ass_upgrade_meta_box',
                    'cpt'      => $this->_SURVEY_CPT_NAME,
                    'context'  => 'side',
                    'priority' => 'low'
                )
            ) );

            $this->_ROLES_ALLOWED_TO_MANAGE_ASS = apply_filters( 'ass_roles_allowed_to_manage_ass' , array( 'administrator' ) );

            // Question types
            $this->_QUESTION_TYPES = array( 'multiple-choice-single-answer' => __( 'Multiple Choice Single Answer' , 'after-sale-surveys' ) );
            $this->_QUESTION_TYPES = apply_filters( 'as_survey_initialize_question_types' , $this->_QUESTION_TYPES );

            $this->_MULTIPLE_CHOICE_QUESTION_TYPES = array( 'multiple-choice-single-answer' );
            $this->_MULTIPLE_CHOICE_QUESTION_TYPES = apply_filters( 'as_survey_multiple_choice_question_types' , $this->_MULTIPLE_CHOICE_QUESTION_TYPES );

            // Questions table
            $this->_QUESTIONS_TABLE_HEADINGS = array(
                'order-number'    => __( 'Order' , 'after-sale-surveys' ),
                'question-text'   => __( 'Question' , 'after-sale-surveys' ),
                'question-type'   => __( 'Question Type' , 'after-sale-surveys' ),
                'required'        => __( 'Required' , 'after-sale-surveys' ),
                'column-controls' => ''
            );
            $this->_QUESTIONS_TABLE_HEADINGS = apply_filters( 'as_survey_initialize_questions_table_headings' , $this->_QUESTIONS_TABLE_HEADINGS );

            $this->_QUESTIONS_TABLE_ROW_ACTIONS = '<span class="control edit-control dashicons dashicons-edit"></span>' .
                                                  '<span class="control delete-control dashicons dashicons-no"></span>';

            $this->_QUESTIONS_TABLE_ROW_ACTIONS = apply_filters( 'as_survey_initialize_questions_table_row_actions' , $this->_QUESTIONS_TABLE_ROW_ACTIONS );

            // Survey cpt post meta
            $this->_POST_META_SURVEY_QUESTIONS      = 'survey_questions';
            $this->_POST_META_SURVEY_OFFER_ATTEMPTS = 'survey_offer_attempts';
            $this->_POST_META_SURVEY_UPTAKES        = 'survey_uptakes';
            $this->_POST_META_SURVEY_COMPLETIONS    = 'survey_completions';

            // Plugin Custom Tables
            $this->_CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS         = $wpdb->prefix . 'ass_survey_offer_attempts';
            $this->_CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS_VERSION = 'ass_survey_offer_attempts_table_version';
            $this->_CUSTOM_TABLE_SURVEY_UPTAKES                = $wpdb->prefix . 'ass_survey_uptakes';
            $this->_CUSTOM_TABLE_SURVEY_UPTAKES_VERSION        = 'ass_survey_uptakes_table_version';
            $this->_CUSTOM_TABLE_SURVEY_COMPLETIONS            = $wpdb->prefix . 'ass_survey_completions';
            $this->_CUSTOM_TABLE_SURVEY_COMPLETIONS_VERSION    = 'ass_survey_completions_table_version';

            // Post Meta

            // Survey response cpt post meta
            $this->_POST_META_RESPONSE_SURVEY_ID           = 'response_survey_id';
            $this->_POST_META_RESPONSE_ORDER_ID            = 'response_order_id';
            $this->_POST_META_RESPONSE_USER_ID             = 'response_user_id';
            $this->_POST_META_RESPONSE_USER_EMAIL          = 'response_user_email';
            $this->_POST_META_SURVEY_RESPONSES             = 'survey_responses';
            $this->_POST_META_RESPONSE_ADDITIONAL_DETAILS  = 'response_additional_details';

            // Survey CTA
            $this->_POST_META_SURVEY_CTA_TITLE   = 'as_survey_cta_title';
            $this->_POST_META_SURVEY_CTA_CONTENT = 'as_survey_cta_content';

            // Survey Title
            $this->_POST_META_SURVEY_THANK_YOU_TITLE   = 'as_survey_thank_you_title';
            $this->_POST_META_SURVEY_THANK_YOU_CONTENT = 'as_survey_thank_you_content';

            // Options

            // Dev Options
            $this->_OPTION_CLEANUP_PLUGIN_OPTIONS = 'as_survey_cleanup_plugin_options';

        }

        /**
         * Ensure that there is only one instance of ASS_Constants is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @return ASS_Constants
         */
        public static function instance() {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self();

            return self::$_instance;

        }

        /*
        |--------------------------------------------------------------------------
        | Property Getters
        |--------------------------------------------------------------------------
        |
        | Getter functions to read properties of the class.
        | These properties serves as the constants consumed by the plugin.
        |
        */

        /**
         * Return _MAIN_PLUGIN_FILE_PATH. Property that holds the plugin's main file directory path.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function MAIN_PLUGIN_FILE_PATH() {

            return $this->_MAIN_PLUGIN_FILE_PATH;

        }

        /**
         * Return _PLUGIN_DIR_PATH property. Property that holds the plugin's root directory path.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function PLUGIN_DIR_PATH() {

            return $this->_PLUGIN_DIR_PATH;

        }

        /**
         * Return _PLUGIN_DIR_URL property. Property that holds the plugin's root directory url.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function PLUGIN_DIR_URL() {

            return $this->_PLUGIN_DIR_URL;

        }

        /**
         * Return _PLUGIN_BASENAME property. Property that holds the plugin's basename.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function PLUGIN_BASENAME() {

            return $this->_PLUGIN_BASENAME;

        }

        /**
         * Return _TOKEN property. Property that holds the plugin's unique token.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function TOKEN() {

            return $this->_TOKEN;

        }

        /**
         * Return _VERSION property. Property that holds the plugin's 'current' version.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function VERSION() {

            return $this->_VERSION;

        }

        /**
         * Return _TEXT_DOMAIN property. Property that holds the 'views' root directory path.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function TEXT_DOMAIN() {

            return $this->_TEXT_DOMAIN;

        }

        /**
         * Return _CSS_ROOT_URL property. Property that holds the 'css' root directory url.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function CSS_ROOT_URL() {

            return $this->_CSS_ROOT_URL;

        }

        /**
         * Return _IMAGES_ROOT_URL property. Property that holds the 'images' root directory url.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function IMAGES_ROOT_URL() {

            return $this->_IMAGES_ROOT_URL;

        }

        /**
         * Return _JS_ROOT_URL property. Property that holds the 'js' root directory url.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function JS_ROOT_URL() {

            return $this->_JS_ROOT_URL;

        }

        /**
         * Return _MODELS_ROOT_PATH. Property that holds the 'models' root directory path.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function MODELS_ROOT_PATH() {

            return $this->_MODELS_ROOT_PATH;

        }

        /**
         * Return _TEMPLATES_ROOT_PATH. Property that holds 'templates' root directory path.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function TEMPLATES_ROOT_PATH() {

            return $this->_TEMPLATES_ROOT_PATH;

        }

        /**
         * Return _THEME_TEMPLATE_PATH. Property that holds the path of the current theme overridden plugin template files.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function THEME_TEMPLATE_PATH() {

            return $this->_THEME_TEMPLATE_PATH;

        }

        /**
         * Return _VIEWS_ROOT_PATH property. Property that holds the 'views' root directory path.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function VIEWS_ROOT_PATH() {

            return $this->_VIEWS_ROOT_PATH;

        }

        /**
         * Return _SURVEY_CPT_NAME property. Property that holds the Survey custom post type name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function SURVEY_CPT_NAME() {

            return $this->_SURVEY_CPT_NAME;

        }

        /**
         * Return _SURVEY_RESPONSE_CPT_NAME. Property that holds the Survey Response custom post type name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function SURVEY_RESPONSE_CPT_NAME() {

            return $this->_SURVEY_RESPONSE_CPT_NAME;

        }

        /**
         * Return _SURVEY_CPT_META_BOXES. Property that holds the Survey Response custom post type meta boxes.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function SURVEY_CPT_META_BOXES() {

            return $this->_SURVEY_CPT_META_BOXES;

        }

        /**
         * Return _ROLES_ALLOWED_TO_MANAGE_ASS. Property that holds the array of user roles that are allowed to manage "After Sale Surveys" plugin.
         *
         * @since 1.1.0
         * @access public
         *
         * @return array
         */
        public function ROLES_ALLOWED_TO_MANAGE_ASS() {

            return $this->_ROLES_ALLOWED_TO_MANAGE_ASS;

        }

        /**
         * Return _QUESTION_TYPES property. Property that holds survey question types.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function QUESTION_TYPES() {

            return $this->_QUESTION_TYPES;

        }

        /**
         * Return _QUESTIONS_TABLE_HEADINGS property. Property that holds the headings for the survey questions table on the backend.
         *
         * @since 1.0.0
         * @access public
         *
         * @return mixed
         */
        public function QUESTIONS_TABLE_HEADINGS() {

            return $this->_QUESTIONS_TABLE_HEADINGS;

        }

        /**
         * Return _MULTIPLE_CHOICE_QUESTION_TYPES property. Property that holds the question types that are considered as multiple choice questions.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function MULTIPLE_CHOICE_QUESTION_TYPES() {

            return $this->_MULTIPLE_CHOICE_QUESTION_TYPES;

        }

        /**
         * Return _QUESTIONS_TABLE_ROW_ACTIONS. Property that holds the table row actions for the survey questions table on the backend.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function QUESTIONS_TABLE_ROW_ACTIONS() {

            return $this->_QUESTIONS_TABLE_ROW_ACTIONS;

        }




        /*
        |--------------------------------------------------------------------------
        | Survey Post Meta Constants Property Getters
        |--------------------------------------------------------------------------
        */

        /**
         * Return _POST_META_SURVEY_QUESTIONS property. Property that holds survey questions post meta key.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_SURVEY_QUESTIONS() {

            return $this->_POST_META_SURVEY_QUESTIONS;

        }

        /**
         * Return _POST_META_SURVEY_OFFER_ATTEMPTS. Survey post meta key that holds the number of times an offer has been offered. (Survey CTA shown).
         *
         * @since 1.0.1
         * @access public
         * @return string
         */
        public function POST_META_SURVEY_OFFER_ATTEMPTS() {

            return $this->_POST_META_SURVEY_OFFER_ATTEMPTS;

        }

        /**
         * Return _POST_META_SURVEY_UPTAKES. Survey post meta key that holds the number of times a survey has been accepted.
         *
         * @since 1.0.1
         * @access public
         * @return string
         */
        public function POST_META_SURVEY_UPTAKES() {

            return $this->_POST_META_SURVEY_UPTAKES;

        }

        /**
         * Return _POST_META_SURVEY_COMPLETIONS. Survey post meta key that holds the number of times a survey has been completed.
         *
         * @since 1.0.1
         * @access public
         * @return string
         */
        public function POST_META_SURVEY_COMPLETIONS() {

            return $this->_POST_META_SURVEY_COMPLETIONS;

        }


        /*
        |--------------------------------------------------------------------------
        | Plugin Custom Tables
        |--------------------------------------------------------------------------
        */

        /**
         * Return _CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS. Survey offer attempts custom plugin table name.
         *
         * @since 1.1.0
         * @access public
         *
         * @return string
         */
        public function CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS() {

            return $this->_CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS;

        }

        /**
         * Return _CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS_VERSION. Survey offer attempts custom plugin table version.
         *
         * @since 1.1.0
         * @access public
         *
         * @return string
         */
        public function CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS_VERSION() {

            return $this->_CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS_VERSION;

        }

        /**
         * Return _CUSTOM_TABLE_SURVEY_UPTAKES. Survey uptakes custom plugin table name.
         *
         * @since 1.1.0
         * @access public
         *
         * @return string
         */
        public function CUSTOM_TABLE_SURVEY_UPTAKES() {

            return $this->_CUSTOM_TABLE_SURVEY_UPTAKES;

        }

        /**
         * Return _CUSTOM_TABLE_SURVEY_UPTAKES_VERSION. Survey uptakes custom plugin table version.
         *
         * @since 1.1.0
         * @access public
         *
         * @return string
         */
        public function CUSTOM_TABLE_SURVEY_UPTAKES_VERSION() {

            return $this->_CUSTOM_TABLE_SURVEY_UPTAKES_VERSION;

        }

        /**
         * Return _CUSTOM_TABLE_SURVEY_COMPLETIONS. Survey completions custom plugin table name.
         *
         * @since 1.1.0
         * @access public
         *
         * @return string
         */
        public function CUSTOM_TABLE_SURVEY_COMPLETIONS() {

            return $this->_CUSTOM_TABLE_SURVEY_COMPLETIONS;

        }

        /**
         * Return _CUSTOM_TABLE_SURVEY_COMPLETIONS_VERSION. Survey completions custom plugin table version.
         *
         * @since 1.1.0
         * @access public
         *
         * @return string
         */
        public function CUSTOM_TABLE_SURVEY_COMPLETIONS_VERSION() {

            return $this->_CUSTOM_TABLE_SURVEY_COMPLETIONS_VERSION;

        }


        /*
        |--------------------------------------------------------------------------
        | Survey Response Post Meta Constants Property Getters
        |--------------------------------------------------------------------------
        */

        /**
         * Return _POST_META_RESPONSE_SURVEY_ID. Survey response survey id post meta key.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_RESPONSE_SURVEY_ID() {

            return $this->_POST_META_RESPONSE_SURVEY_ID;

        }

        /**
         * Return _POST_META_RESPONSE_ORDER_ID. Survey response order id post meta key.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function  POST_META_RESPONSE_ORDER_ID() {

            return $this->_POST_META_RESPONSE_ORDER_ID;

        }

        /**
         * Return _POST_META_RESPONSE_USER_ID. Survey response user id post meta key.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_RESPONSE_USER_ID() {

            return $this->_POST_META_RESPONSE_USER_ID;

        }

        /**
         * Return _POST_META_RESPONSE_USER_EMAIL. Survey response user email post meta key.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_RESPONSE_USER_EMAIL() {

            return $this->_POST_META_RESPONSE_USER_EMAIL;

        }

        /**
         * Return _POST_META_RESPONSE_ADDITIONAL_DETAILS. Survey response user additional details.
         *
         * @since 1.1.1
         * @access public
         *
         * @return string
         */
        public function POST_META_RESPONSE_ADDITIONAL_DETAILS() {

            return $this->_POST_META_RESPONSE_ADDITIONAL_DETAILS;

        }

        /**
         * Return _POST_META_SURVEY_RESPONSES. Survey responses meta key.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_SURVEY_RESPONSES() {

            return $this->_POST_META_SURVEY_RESPONSES;

        }




        /*
        |--------------------------------------------------------------------------
        | Plugin Options Property Getters
        |--------------------------------------------------------------------------
        */

        // Post Meta

        /**
         * Return _POST_META_SURVEY_CTA_TITLE. Property that holds the title of the survey cta of a specific survey.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_SURVEY_CTA_TITLE() {

            return $this->_POST_META_SURVEY_CTA_TITLE;

        }

        /**
         * Return _POST_META_SURVEY_CTA_CONTENT. Property that holds the content of the survey cta of a specific survey.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_SURVEY_CTA_CONTENT() {

            return $this->_POST_META_SURVEY_CTA_CONTENT;

        }

        /**
         * Return _POST_META_SURVEY_THANK_YOU_TITLE. Property that holds the title of a survey thank you of a specific survey.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_SURVEY_THANK_YOU_TITLE() {

            return $this->_POST_META_SURVEY_THANK_YOU_TITLE;

        }

        /**
         * Return _POST_META_SURVEY_THANK_YOU_CONTENT. Propety that holds the content of a survey thank you of a specific survey.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_SURVEY_THANK_YOU_CONTENT() {

            return $this->_POST_META_SURVEY_THANK_YOU_CONTENT;

        }

        // Dev Options

        /**
         * Return _OPTION_CLEANUP_PLUGIN_OPTIONS. Property that holds the option of either cleaning up all plugin options upon plugin un-installation.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function OPTION_CLEANUP_PLUGIN_OPTIONS() {

            return $this->_OPTION_CLEANUP_PLUGIN_OPTIONS;

        }

    }

}
