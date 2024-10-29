<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class ASS_Initial_Guided_Tour {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    private static $_instance = null;

    const OPTION_INITIAL_GUIDED_TOUR_STATUS = 'ass_initial_guided_tour_status';
    const STATUS_OPEN                       = 'open';
    const STATUS_CLOSE                      = 'close';

    private $urls;
    private $screens;




    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Cloning is forbidden.
     *
     * @since 1.1.0
     * @access public
     */
    public function __clone () {

        _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'after-sale-surveys' ) , '1.1.0' );

    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.1.0
     * @access public
     */
    public function __wakeup () {

        _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'after-sale-surveys' ) , '1.1.0' );

    }

    /**
     * ASS_Initial_Guided_Tour constructor.
     *
     * @since 1.1.0
     * @access public
     */
    private function __construct() {

        $this->urls = apply_filters( 'ass_initial_guided_tour_pages' , array(
            'plugin-listing'   => admin_url( 'plugins.php' ),
            'orders-listing'   => admin_url( 'edit.php?post_type=shop_order' ),
            'ass-settings'     => admin_url( 'admin.php?page=wc-settings&tab=ass_settings' ),
            'ass-listing'      => admin_url( 'edit.php?post_type=as_survey' ),
            'response-listing' => admin_url( 'edit.php?post_type=as_survey_response' )
        ) );

        $this->screens = apply_filters( 'ass_initial_guided_tours' , array(
            'plugins' => array(
                'elem'  => '#menu-plugins .menu-top',
                'html'  => __( '<h3>Welcome to After Sale Surveys!</h3>
                                <p>Would you like to go on a guided tour of the plugin? Takes less than 30 seconds.</p>' , 'after-sale-surveys' ),
                'prev'  => null,
                'next'  => $this->urls[ 'orders-listing' ],
                'edge'  => 'left',
                'align' => 'left'
            ),
            'edit-shop_order' => array(
                'elem'  => '#toplevel_page_woocommerce ul li a.current',
                'html'  => __( '<h3>After Sale Surveys is made for surveying customers after they complete their purchase.</h3>
                                <p>It can give you ongoing insights into all the things you\'ve ever wanted to know about your customers.</p>
                                <p>Asking your customers to complete a survey right after they ordered is the best time to ask because you already have their full attention.</p>' ),
                'prev'  => $this->urls[ 'plugin-listing' ],
                'next'  => $this->urls[ 'ass-settings' ],
                'edge'  => 'left',
                'align' => 'left'
            ),
            'woocommerce_page_wc-settings' => array(
                'elem'  => '.nav-tab-active',
                'html'  => __( '<h3>This is the settings area.</h3>
                                <p>Here is where you can configure important plugin options that affect the way your surveys are run.</p>
                                <p>You can come back here anytime after the tour to configure these settings.</p>' , 'after-sale-surveys' ),
                'prev'  => $this->urls[ 'orders-listing' ],
                'next'  => $this->urls[ 'ass-listing' ],
                'edge'  => 'top',
                'align' => 'left'
            ),
            'edit-as_survey' => array(
                'elem'  => '#toplevel_page_woocommerce ul li a.current',
                'html'  => __( '<h3>This is the Surveys List.</h3>
                                <p>It shows you all the surveys you currently have running on your store.</p>
                                <p>You can run multiple surveys at the same time, directing certain customers to different surveys based on who they are and what they bought.</p>' , 'after-sale-surveys' ),
                'prev'  => $this->urls[ 'ass-settings' ],
                'next'  => $this->urls[ 'response-listing' ],
                'edge'  => 'left',
                'align' => 'left'
            ),
            'edit-as_survey_response' => array(
                'elem'  => '#toplevel_page_woocommerce ul li a.current',
                'html'  => sprintf( __( '<h3>This is the Survey Responses.</h3>
                                        <p>Here you can find every response on every survey from your customers.</p>
                                        <p>You can drill down into particular responses to get more details about how they answered the questions.</p>
                                        <p>This concludes the tour. Click on the button below to add your first survey:</p>
                                        <p><a id="ass-add-first-survey" href="%1$s" class="button button-primary">Add My First Survey</a></p>' , 'after-sale-surveys' ) , admin_url( 'post-new.php?post_type=as_survey' ) ),
                'prev'  => $this->urls[ 'ass-listing' ],
                'next'  => null,
                'edge'  => 'left',
                'align' => 'left'
            )
        ) );

    }

    /**
     * Get the only instance of the class.
     *
     * @since 1.1.0
     * @access public
     *
     * @return ASS_Initial_Guided_Tour
     */
    public static function instance() {

        if ( !self::$_instance )
            self::$_instance = new self();

        return self::$_instance;

    }

    /**
     * Get current screen.
     * 
     * @since 1.1.0
     * @access public
     */
    public function get_current_screen() {

        $screen = get_current_screen();

        if ( !empty( $this->screens[ $screen->id ] ) )
            return $this->screens[ $screen->id ];

        return false;

    }

    /**
     * Initialize guided tour options.
     *
     * @since 1.1.0
     * @access public
     */
    public function initialize_guided_tour_options() {

        if ( get_option( self::OPTION_INITIAL_GUIDED_TOUR_STATUS ) === false )
            update_option( self::OPTION_INITIAL_GUIDED_TOUR_STATUS , self::STATUS_OPEN );
        
    }

    /**
     * Terminate guided tour options.
     *
     * @since 1.1.0
     * @access public
     */
    public function terminate_guided_tour_options() {
        
        delete_option( self::OPTION_INITIAL_GUIDED_TOUR_STATUS );

    }

    /**
     * Get screens with registered guide.
     * 
     * @since 1.1.0
     * @access public
     */
    public function get_screens() {

        return $this->screens;

    }

    /**
     * Close initial guided tour.
     * 
     * @since 1.1.0
     * @access public
     */
    public function ass_close_initial_guided_tour() {

        if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {

            if ( !check_ajax_referer( 'ass-close-initial-guided-tour' , 'nonce' , false ) )
                wp_die( __( 'Security Check Failed' , 'after-sale-surveys' ) );

            update_option( self::OPTION_INITIAL_GUIDED_TOUR_STATUS , self::STATUS_CLOSE );

            wp_send_json_success();

        } else
            wp_die( __( 'Invalid AJAX Call' , 'after-sale-surveys' ) );
        
    }

} // end class