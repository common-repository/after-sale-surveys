<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class ASS_Survey_Entry_Guided_Tour {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    private static $_instance = null;

    const OPTION_SURVEY_ENTRY_GUIDED_TOUR_STATUS = 'ass_survey_entry_guided_tour_status';
    const STATUS_OPEN                            = 'open';
    const STATUS_CLOSE                           = 'close';

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
     * TEO_Initial_Guided_Tour constructor.
     *
     * @since 1.1.0
     * @access public
     */
    private function __construct() {

        $this->urls = apply_filters( 'ass_survey_entry_guided_tour_pages' , array() );

        $tours_array = array(
            array(
                'id'    => 'survey_entry_guide_intro',
                'elem'  => '#toplevel_page_woocommerce ul li a.current',
                'html'  => __( '<h3>Congratulations, you just added your first survey!</h3>
                                <p>Would you like to learn how to configure it?</p>
                                <p>It takes less than a minute and you\'ll then know exactly how to setup your first survey!</p>' , 'after-sale-surveys' ),
                'prev'  => null,
                'next'  => '@survey_entry_guide_title',
                'edge'  => 'left',
                'align' => 'left'
            ),
            array(
                'id'    => 'survey_entry_guide_title',
                'elem'  => '#titlediv',
                'html'  => __( '<h3>First, give your Survey a name.</h3>
                                <p>This is used internally for you to identify the survey in the system so make it something that describes what the survey is all about.</p>' , 'after-sale-surveys' ),
                'prev'  => '@survey_entry_guide_intro',
                'next'  => '@survey_entry_guide_main_content',
                'edge'  => 'top',
                'align' => 'center'
            ),
            array(
                'id'    => 'survey_entry_guide_main_content',
                'elem'  => '#wp-content-editor-container',
                'html'  => __( '<h3>Set the text that shows just prior to your questions.</h3>
                                <p>Here you can add a message or set of instructions to customers just before they start answering questions.</p>' , 'after-sale-surveys' ),
                'prev'  => '@survey_entry_guide_title',
                'next'  => '@survey_entry_guide_cta',
                'edge'  => 'left',
                'align' => 'center'
            ),
            array(
                'id'    => 'survey_entry_guide_cta',
                'elem'  => '#survey-cta',
                'html'  => __( '<h3>This is the Call-To-Action (CTA) text.</h3>
                                <p>When a customer finishes their order and comes back to the Order Received page they are asked to participate in a survey. This text you set here is shown in a Call-To-Action popup.</p>
                                <p>This is your chance to encourage them and tell them why they should fill in your survey.</p>' , 'after-sale-surveys' ),
                'prev'  => '@survey_entry_guide_main_content',
                'next'  => '@survey_entry_guide_thank_you',
                'edge'  => 'left',
                'align' => 'center'
            ),
            array(
                'id'    => 'survey_entry_guide_thank_you',
                'elem'  => '#survey-thank-you-message',
                'html'  => __( '<h3>This is the Thank You text.</h3>
                                <p>Once a customer has finished filling in your survey they are shown a Thank You popup.</p>
                                <p>The text you set here can be used to thank them for their participation. You can also give them a coupon or up-sell another product. The options are endless!</p>' , 'after-sale-surveys' ),
                'prev'  => '@survey_entry_guide_cta',
                'next'  => '@survey_entry_guide_questions',
                'edge'  => 'left',
                'align' => 'center'
            )
        );

        if ( in_array( 'after-sale-surveys-premium/after-sale-surveys-premium.php' , apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

            $tours_array[] = array(
                'id'    => 'survey_entry_guide_questions',
                'elem'  => '#survey-questions',
                'html'  => __( '<h3>Now its time to setup the questions on your survey.</h3>
                                <p>Click the <b>+Add Question</b> button to add your first question.</p>
                                <p>The free version only has Multiple Choice Single Answer, but the Premium add-on extend this to include many other question types.</p>
                                <p>Choose your question type, then add the possible answers that the customer will be able to choose from.</p>' , 'after-sale-surveys' ),
                'prev'  => '@survey_entry_guide_thank_you',
                'next'  => null,
                'edge'  => 'left',
                'align' => 'center'
            );

        } else {

            $tours_array[] = array(
                'id'    => 'survey_entry_guide_questions',
                'elem'  => '#survey-questions',
                'html'  => __( '<h3>Now its time to setup the questions on your survey.</h3>
                                <p>Click the <b>+Add Question</b> button to add your first question.</p>
                                <p>The free version only has Multiple Choice Single Answer, but the Premium add-on extend this to include many other question types.</p>
                                <p>Choose your question type, then add the possible answers that the customer will be able to choose from.</p>' , 'after-sale-surveys' ),
                'prev'  => '@survey_entry_guide_thank_you',
                'next'  => '@survey_entry_guide_plugin_upgrade',
                'edge'  => 'left',
                'align' => 'center'
            );

            $tours_array[] = array(
                'id'    => 'survey_entry_guide_plugin_upgrade',
                'elem'  => '#after-sale-surveys-upgrade',
                'html'  => sprintf( __( '<h3>This concludes the guide. You are now ready to setup your first survey!</h3>
                                         <p>Want to unlock all of the extra features you see here? The Premium add-on will unlock all this and more. We\'re adding new features all the time!</p>
                                         <p><a href="%1$s" target="_blank">Check out the Premium version now &rarr;</a></p>' , 'after-sale-surveys' ) , 'https://marketingsuiteplugin.com/product/after-sale-surveys/?utm_source=ASS&utm_medium=Settings%20Banner&utm_campaign=ASS' ),
                'prev'  => '@survey_entry_guide_questions',
                'next'  => null,
                'edge'  => 'right',
                'align' => 'center'
            );

        }
        
        $this->screens = apply_filters( 'ass_survey_entry_guided_tours' , array( 'as_survey' => $tours_array ) );

    }

    /**
     * Get the only instance of the class.
     *
     * @since 1.1.0
     * @access public
     *
     * @return ASS_Survey_Entry_Guided_Tour
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

        if ( get_option( self::OPTION_SURVEY_ENTRY_GUIDED_TOUR_STATUS ) === false )
            update_option( self::OPTION_SURVEY_ENTRY_GUIDED_TOUR_STATUS , self::STATUS_OPEN );

    }

    /**
     * Terminate guided tour options.
     *
     * @since 1.1.0
     * @access public
     */
    public function terminate_guided_tour_options() {

        delete_option( self::OPTION_SURVEY_ENTRY_GUIDED_TOUR_STATUS );

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
     * Close survey entry guided tour.
     *
     * @since 1.1.0
     * @access public
     */
    public function ass_close_survey_entry_guided_tour() {

        if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {

            if ( !check_ajax_referer( 'ass-close-survey-entry-guided-tour' , 'nonce' , false ) )
                wp_die( __( 'Security Check Failed' , 'after-sale-surveys' ) );

            update_option( self::OPTION_SURVEY_ENTRY_GUIDED_TOUR_STATUS , self::STATUS_CLOSE );

            wp_send_json_success();

        } else
            wp_die( __( 'Invalid AJAX Call' , 'after-sale-surveys' ) );

    }

} // end class