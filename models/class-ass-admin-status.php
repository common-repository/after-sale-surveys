<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'ASS_Admin_Status' ) ) {

    /**
     * Model that houses the logic of various plugin admin status.
     * Ex. template override status. Could add some more info status in the future.
     *
     * Class ASS_Admin_Status
     */
    class ASS_Admin_Status {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of ASS_Admin_Status.
         *
         * @since 1.0.0
         * @access private
         * @var ASS_Admin_Status
         */
        private static $_instance;

        /**
         * ASS_Constants instance. Holds various constants this class uses.
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
         * ASS_Admin_Status constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'ASS_Constants' ];

        }

        /**
         * Ensure that there is only one instance of ASS_Admin_Status is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return ASS_Admin_Status
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Render admin status, specifically for the overridden template files status.
         *
         * @since 1.0.0
         * @access public
         */
        public function render_ass_template_status() {

            $template_root_path = $this->_plugin_constants->TEMPLATES_ROOT_PATH();

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'admin-status/template-status/template-status.php' );

        }

    }

}