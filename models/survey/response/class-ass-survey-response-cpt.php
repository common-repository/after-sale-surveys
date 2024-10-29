<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'ASS_Survey_Response_CPT' ) ) {

    /**
     * Class ASS_Survey_Response_CPT
     *
     * Model that houses the logic relating to Survey CPT.
     *
     * @since 1.0.0
     */
    final class ASS_Survey_Response_CPT {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of ASS_Survey_Response_CPT.
         *
         * @since 1.0.0
         * @access private
         * @var ASS_Survey_Response_CPT
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
         * ASS_Survey_Response_CPT constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of ASS_Survey_Response_CPT model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'ASS_Constants' ];

        }

        /**
         * Ensure that only one instance of ASS_Survey_Response_CPT is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of ASS_Survey_Response_CPT model.
         * @return ASS_Survey_Response_CPT
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Register Survey Response custom post type.
         *
         * @since 1.0.0
         * @access public
         */
        public function register_survey_response_cpt() {

            $labels = array(
                'name'                => __( 'Survey Responses' , 'after-sale-surveys' ),
                'singular_name'       => __( 'Survey Response' , 'after-sale-surveys' ),
                'menu_name'           => __( 'Survey Response' , 'after-sale-surveys' ),
                'parent_item_colon'   => __( 'Parent Survey Response' , 'after-sale-surveys' ),
                'all_items'           => __( 'Survey Responses' , 'after-sale-surveys' ),
                'view_item'           => __( 'View Survey Response' , 'after-sale-surveys' ),
                'add_new_item'        => __( 'Add Survey Response' , 'after-sale-surveys' ),
                'add_new'             => __( 'New Survey Response' , 'after-sale-surveys' ),
                'edit_item'           => __( 'Edit Survey Response' , 'after-sale-surveys' ),
                'update_item'         => __( 'Update Survey Response' , 'after-sale-surveys' ),
                'search_items'        => __( 'Search Survey Responses' , 'after-sale-surveys' ),
                'not_found'           => __( 'No Survey Response found' , 'after-sale-surveys' ),
                'not_found_in_trash'  => __( 'No Survey Responses found in Trash' , 'after-sale-surveys' ),
            );

            $args = array(
                'label'               => __( 'Survey Responses' , 'after-sale-surveys' ),
                'description'         => __( 'Survey Response Information Pages' , 'after-sale-surveys' ),
                'labels'              => $labels,
                'supports'            => array( 'title' ),
                'taxonomies'          => array(),
                'hierarchical'        => false,
                'public'              => false,
                'show_ui'             => true,
                'show_in_menu'        => 'woocommerce',
                'show_in_json'        => false,
                'query_var'           => true,
                'rewrite'             => array(),
                'show_in_nav_menus'   => false,
                'show_in_admin_bar'   => true,
                'menu_position'       => 26,
                'menu_icon'           => 'dashicons-format-aside',
                'can_export'          => true,
                'has_archive'         => true,
                'exclude_from_search' => true,
                'publicly_queryable'  => false,
                'capabilities'        => array(
                    'create_posts'           => 'do_not_allow', // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
                ),
                'map_meta_cap'        => true,
                'capability_type'     => 'post'
            );

            $args = apply_filters( 'as_survey_response_cpt_args' , $args );

            register_post_type( $this->_plugin_constants->SURVEY_RESPONSE_CPT_NAME() , $args );

        }

        /**
         * Register 'as_survey_response' cpt meta boxes.
         *
         * @since 1.0.0
         * @access public
         */
        public function register_survey_response_cpt_custom_meta_boxes() {

            // Respondent Data
            add_meta_box(
                'survey-respondent-data',
                __( 'Survey Respondent Data' , 'after-sale-surveys' ),
                array( $this , 'view_survey_respondent_data_meta_box' ),
                $this->_plugin_constants->SURVEY_RESPONSE_CPT_NAME(),
                'normal',
                'high'
            );

            // Response Data
            add_meta_box(
                'survey-response-data',
                __( 'Survey Response Data' , 'after-sale-surveys' ),
                array( $this , 'view_survey_response_data_meta_box' ),
                $this->_plugin_constants->SURVEY_RESPONSE_CPT_NAME(),
                'normal',
                'high'
            );

        }




        /*
        |--------------------------------------------------------------------------
        | Views
        |--------------------------------------------------------------------------
        */

        /**
         * Survey respondent data meta box view.
         *
         * @since 1.0.0
         * @access public
         */
        public function view_survey_respondent_data_meta_box() {

            global $post;

            $order_id = get_post_meta( $post->ID , $this->_plugin_constants->POST_META_RESPONSE_ORDER_ID() , true );
            $user_id  = get_post_meta( $post->ID , $this->_plugin_constants->POST_META_RESPONSE_USER_ID() , true );

            $user_data = get_userdata( $user_id );
            $order     = wc_get_order( $order_id );

            $billing_city    = ASS_Helper::get_order_data( $order , 'billing_city' );
            $billing_state   = ASS_Helper::get_order_data( $order , 'billing_state' );
            $billing_country = ASS_Helper::get_order_data( $order , 'billing_country' );

            $user_additional_details = array_merge(
                ASS_Helper::get_survey_response_respondent_data( $post->ID ) ,
                array( 'location'  =>  $billing_city . ', ' . $billing_state . ', ' . $billing_country )
            );

            $additional_details_labels = array(
                'ip_address'        =>  __( 'IP Address' , 'after-sale-surveys' ),
                'browser'           =>  __( 'Browser' , 'after-sale-surveys' ),
                'response_date'     =>  __( 'Response Date' , 'after-sale-surveys' ),
                'location'          =>  __( 'Location' , 'after-sale-surveys' )
            );

            // Note: $user_data could be null if this is a guest order
            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'survey/response/cpt/survey-respondent-data-meta-box.php' );

        }

        /**
         * Survey response data meta box view.
         *
         * @since 1.0.0
         * @access public
         */
        public function view_survey_response_data_meta_box() {

            global $post;

            $survey_id            = get_post_meta( $post->ID , $this->_plugin_constants->POST_META_RESPONSE_SURVEY_ID() , true );
            $survey_questions     = get_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_QUESTIONS() , true );
            $survey_response_data = get_post_meta( $post->ID , $this->_plugin_constants->POST_META_SURVEY_RESPONSES() , true );

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'survey/response/cpt/survey-response-data-meta-box.php' );

        }




        /*
        |--------------------------------------------------------------------------
        | Survey Response CPT Listing Custom Columns
        |--------------------------------------------------------------------------
        */

        /**
         * Add 'as_survey_response' cpt listing custom fields.
         *
         * @since 1.1.0
         * @access public
         *
         * @param array $columns CPT listing columns array.
         * @return array Modified CPT listing columns array.
         */
        public function add_survey_listing_custom_columns( $columns ) {

            $all_keys    = array_keys( $columns );
            $title_index = array_search( 'title' , $all_keys );

            $new_columns_array = array_slice( $columns , 0 , $title_index + 1 , true ) +
                                 apply_filters( 'as_survey_listing_custom_columns' ,
                                                array(
                                                    'survey_id' => __( 'Survey ID' , 'after-sale-surveys' ),
                                                    'order_id'  => __( 'Order ID' , 'after-sale-surveys' )
                                                ),
                                                $columns ) +
                                 array_slice( $columns , $title_index + 1 , NULL , true );

            return $new_columns_array;

        }

        /**
         * Add value to 'as_survey_response' cpt listing custom field.
         *
         * @since 1.1.0
         * @access public
         *
         * @param array $columns CPT listing columns array.
         * @param int   $post_id Post Id.
         */
        public function add_survey_listing_custom_columns_data( $column , $post_id ) {

            switch ( $column ) {

                case 'survey_id':

                    $survey_id      = get_post_meta( $post_id , $this->_plugin_constants->POST_META_RESPONSE_SURVEY_ID() , true );
                    $survey_status  = get_post_status( $survey_id ); ?>

                    <div class="survey-id">
                        <?php if ( $survey_status && $survey_status != 'trash' ) : ?>
                            <a href="<?php echo get_edit_post_link( $survey_id ); ?>">
                                <?php echo $survey_id; ?>
                            </a>
                        <?php else : ?>
                                <?php echo $survey_id; ?>
                        <?php endif; ?>
                    </div>

                    <?php break;

                case 'order_id':

                    $order_id       = get_post_meta( $post_id , $this->_plugin_constants->POST_META_RESPONSE_ORDER_ID() , true );
                    $order_status   = get_post_status( $order_id ); ?>

                    <div class="order-id">
                        <?php if ( $order_status && $order_status != 'trash' ) : ?>
                            <a href="<?php echo get_edit_post_link( $order_id ); ?>">
                                <?php echo $order_id; ?>
                            </a>
                        <?php else : ?>
                            <?php echo $order_id; ?>
                        <?php endif; ?>
                    </div>

                    <?php break;

                default :
                    break;

            }

            do_action( 'as_survey_listing_custom_columns_data' , $column , $post_id );

        }




        /*
        |--------------------------------------------------------------------------
        | AJAX Interfaces
        |--------------------------------------------------------------------------
        */

        /**
         * Save survey response.
         *
         * @since 1.0.0
         * @access public
         *
         * @param null $args
         * @param bool|true $ajax_call
         * @return array
         */
        public function as_survey_save_survey_response( $args = null , $ajax_call = true ) {

            if ( $ajax_call === true )
                $args = $_POST[ 'args' ];

            $survey_id     = $args[ 'survey_id' ];
            $order_id      = $args[ 'order_id' ];
            $user_id       = $args[ 'user_id' ];
            $response_data = $args[ 'response_data' ];
            $order         = wc_get_order( $order_id );
            $user          = $order->get_user();
            $user_email    = $user ? $user->user_email : $order->billing_email;

            $response_title = sprintf( __( 'Survey ID: %1$s' , 'after-sale-surveys' ) , $survey_id );
            $response_title = apply_filters( 'as_survey_new_survey_response_title' , $response_title , $survey_id );

            // Construct new survey response meta
            $response_meta = array(
                                $this->_plugin_constants->POST_META_RESPONSE_SURVEY_ID()  => $survey_id,
                                $this->_plugin_constants->POST_META_RESPONSE_ORDER_ID()   => $order_id,
                                $this->_plugin_constants->POST_META_RESPONSE_USER_ID()    => $user_id,
                                $this->_plugin_constants->POST_META_RESPONSE_USER_EMAIL() => $user_email,
                                $this->_plugin_constants->POST_META_SURVEY_RESPONSES()    => $response_data
                            );

            $response_meta = apply_filters( 'as_survey_new_survey_response_meta' , $response_meta , $args );

            $survey_response_id = wp_insert_post( array(
                                    'post_title'  => '',
                                    'post_status' => 'publish', // TODO: Add setting as to what will be the status of the new survey responses
                                    'post_type'   => $this->_plugin_constants->SURVEY_RESPONSE_CPT_NAME(),
                                    'meta_input'  => $response_meta
                                ) , true );

            if ( is_wp_error( $survey_response_id ) ) {

                // TODO: Log error?
                $response = array(
                    'status'        => 'fail',
                    'error_message' => $survey_response_id->get_error_message()
                );

            } else {

                $response_title .= ' ' . sprintf( __( 'Response ID: %1$s' , 'after-sale-surveys' ) , $survey_response_id );

                $survey_response_id = wp_update_post( array(
                    'ID'         => $survey_response_id,
                    'post_title' => $response_title,
                ) , true );

                if ( is_wp_error( $survey_response_id ) ) {

                    // TODO: Log error?
                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $survey_response_id->get_error_message()
                    );

                } else {

                    $response = array(
                        'status'      => 'success',
                        'response_id' => $survey_response_id
                    );

                    do_action( 'as_survey_after_save_survey_response' , $survey_response_id , $args );

                }

            }

            if ( $ajax_call === true ) {

                header( 'Content-Type: application/json' );
                echo json_encode( $response );
                die();

            } else
                return $response;

        }

    }

}
