<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'ASS_I18n' ) ) {

    /**
     * Class ASS_I18n
     *
     * Model that houses the logic of internationalizing After Sale Surveys plugin.
     *
     * @since 1.0.0
     */
    final class ASS_I18n {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of ASS_I18n.
         *
         * @since 1.0.0
         * @access private
         * @var ASS_I18n
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
         * ASS_I18n constructor.
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
         * Ensure that there is only one instance of ASS_I18n is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return ASS_I18n
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Load plugin text domain.
         *
         * @since 1.0.0
         * @access public
         */
        public function load_plugin_textdomain() {

            load_plugin_textdomain( $this->_plugin_constants->TEXT_DOMAIN() , false , $this->_plugin_constants->PLUGIN_BASENAME() . '/languages' );

        }

    }

}