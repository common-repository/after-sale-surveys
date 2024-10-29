<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists('ASS_Survey_CPT') ) {

    /**
     * Class ASS_Survey_CPT
     *
     * Model that houses the logic relating to Survey CPT.
     *
     * @since 1.0.0
     */
    final class ASS_Survey_CPT {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of ASS_Survey_CPT.
         *
         * @since 1.0.0
         * @access private
         * @var ASS_Survey_CPT
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
         * ASS_Survey_CPT constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of ASS_Survey_CPT model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'ASS_Constants' ];

        }

        /**
         * Ensure that only one instance of ASS_Survey_CPT is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of ASS_Survey_CPT model.
         * @return ASS_Survey_CPT
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Register Survey custom post type.
         *
         * @since 1.0.0
         * @access public
         */
        public function register_survey_cpt() {

            $labels = array(
                'name'                => __( 'After Sale Surveys' , 'after-sale-surveys' ),
                'singular_name'       => __( 'After Sale Survey' , 'after-sale-surveys' ),
                'menu_name'           => __( 'After Sale Survey' , 'after-sale-surveys' ),
                'parent_item_colon'   => __( 'Parent After Sale Survey' , 'after-sale-surveys' ),
                'all_items'           => __( 'After Sale Surveys' , 'after-sale-surveys' ),
                'view_item'           => __( 'View After Sale Survey' , 'after-sale-surveys' ),
                'add_new_item'        => __( 'Add After Sale Survey' , 'after-sale-surveys' ),
                'add_new'             => __( 'New After Sale Survey' , 'after-sale-surveys' ),
                'edit_item'           => __( 'Edit After Sale Survey' , 'after-sale-surveys' ),
                'update_item'         => __( 'Update After Sale Survey' , 'after-sale-surveys' ),
                'search_items'        => __( 'Search After Sale Surveys' , 'after-sale-surveys' ),
                'not_found'           => __( 'No After Sale Survey found' , 'after-sale-surveys' ),
                'not_found_in_trash'  => __( 'No After Sale Surveys found in Trash' , 'after-sale-surveys' ),
            );

            $args = array(
                'label'               => __( 'After Sale Surveys' , 'after-sale-surveys' ),
                'description'         => __( 'After Sale Survey Information Pages' , 'after-sale-surveys' ),
                'labels'              => $labels,
                'supports'            => array( 'title' , 'editor' ),
                'taxonomies'          => array(),
                'hierarchical'        => false,
                'public'              => false,
                'show_ui'             => true,
                //'show_in_menu'        => true,
                //'show_in_menu'        => 'edit.php?post_type=shop_order',
                'show_in_menu'        => 'woocommerce',
                'show_in_json'        => false,
                'query_var'           => true,
                'rewrite'             => array(),
                'show_in_nav_menus'   => false,
                'show_in_admin_bar'   => true,
                'menu_position'       => 26,
                'menu_icon'           => 'dashicons-forms',
                'can_export'          => true,
                'has_archive'         => true,
                'exclude_from_search' => true,
                'publicly_queryable'  => false,
                'capability_type'     => 'post'
            );

            $surveys = ASS_Helper::get_all_surveys( null , array( 'publish' , 'pending' , 'draft' , 'future' , 'private' , 'inherit' , 'trash' ) );

            if ( !empty( $surveys ) ) {

                $args[ 'capabilities' ] = array( 'create_posts' => 'do_not_allow' ); // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups
                $args[ 'map_meta_cap' ] = true;

            }

            $args = apply_filters( 'as_survey_cpt_args' , $args );

            register_post_type( $this->_plugin_constants->SURVEY_CPT_NAME() , $args );

        }

        /**
         * Register 'as_survey' cpt meta boxes.
         *
         * @since 1.0.0
         * @access public
         */
        public function register_survey_cpt_custom_meta_boxes() {

            foreach ( $this->_plugin_constants->SURVEY_CPT_META_BOXES() as $id => $data ) {

                $callback = is_array( $data[ 'callback' ] ) ? $data[ 'callback' ] : array( $this , $data[ 'callback' ] );

                add_meta_box(
                    $id,
                    $data[ 'title' ],
                    $callback,
                    $data[ 'cpt' ],
                    $data[ 'context' ],
                    $data[ 'priority' ]
                );

            }

        }

        /**
         * Save 'as_survey' cpt entry.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $post_id
         */
        public function save_post( $post_id ) {

            if ( get_post_type( $post_id ) == $this->_plugin_constants->SURVEY_CPT_NAME() ) {

                // On every survey post creation, always make sure to add 1 blank question page.
                $survey_questions = get_post_meta( $post_id , $this->_plugin_constants->POST_META_SURVEY_QUESTIONS() , true );
                if ( empty( $survey_questions ) )
                    update_post_meta( $post_id , $this->_plugin_constants->POST_META_SURVEY_QUESTIONS() , array( 1 => array() ) );

            }

        }

        /**
         * Clean up survey data if survey is deleted.
         *
         * @since 1.1.0
         * @access public
         *
         * @param int $post_id Survey id.
         */
        public function clean_up_survey_data( $post_id ) {

            if ( get_post_type( $post_id ) == $this->_plugin_constants->SURVEY_CPT_NAME() ) {

                global $wpdb;

                // Delete all survey responses
                $wpdb->query( "DELETE FROM $wpdb->posts
                                     WHERE ID IN (
                                         SELECT post_id
                                         FROM $wpdb->postmeta
                                         WHERE meta_key = '" . $this->_plugin_constants->POST_META_RESPONSE_SURVEY_ID() . "'
                                         AND meta_value = '" . $post_id . "'
                                     )" );

                // Delete all survey stats
                $stats_table = array(
                    $this->_plugin_constants->CUSTOM_TABLE_SURVEY_OFFER_ATTEMPTS(),
                    $this->_plugin_constants->CUSTOM_TABLE_SURVEY_UPTAKES(),
                    $this->_plugin_constants->CUSTOM_TABLE_SURVEY_COMPLETIONS()
                );

                foreach ( $stats_table as $st )
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $st . "'" ) )
                        $wpdb->query( "DELETE FROM " . $st .  " WHERE survey_id = " . $post_id );

            }

        }

        /**
         * Print admin notice if survey is in read only mode ( Meaning already has responses ).
         *
         * @since 1.0.0
         * @access public
         */
        public function survey_read_only_notice() {

            global $hook_suffix, $post;

            $post_type = get_post_type();
            if ( !$post_type && isset( $_GET[ 'post_type' ] ) )
                $post_type = $_GET[ 'post_type' ];

            if ( ( $hook_suffix == 'post-new.php' || $hook_suffix == 'post.php' ) && $post_type == $this->_plugin_constants->SURVEY_CPT_NAME() ) {

                $survey_responses = ASS_Helper::get_survey_responses( $post->ID );

                if ( !empty( $survey_responses ) ) { ?>

                    <div class="error">
                        <p><?php _e( "<b>Note:</b> You cannot add or edit questions on this survey because it already has responses.<br/>You can either delete all responses for this survey so you can edit it or create a new survey with your desired changes." , "after-sale-surveys" ); ?></p>
                    </div>

                <?php }

            }

        }




        /*
        |--------------------------------------------------------------------------
        | Views
        |--------------------------------------------------------------------------
        */

        /**
         * Survey CTA meta box view.
         *
         * @since 1.0.0
         * @access public
         */
        public function view_survey_cta_meta_box() {

            global $post;

            $editor_settings = array(
                                    'textarea_rows' => 20,
                                    'wpautop'       => true,
                                    'tinymce'       => array( 'height' => 200 )
                                );

            $title   = get_post_meta( $post->ID , $this->_plugin_constants->POST_META_SURVEY_CTA_TITLE() , true );
            $content = get_post_meta( $post->ID , $this->_plugin_constants->POST_META_SURVEY_CTA_CONTENT() , true );

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'survey/cpt/view-survey-cta-meta-box.php' );

        }

        /**
         * Survey Thank You Message meta box view.
         *
         * @since 1.0.0
         * @access public
         */
        public function view_survey_thank_you_message_meta_box() {

            global $post;

            $editor_settings = array(
                                    'textarea_rows' => 20,
                                    'wpautop'       => true,
                                    'tinymce'       => array( 'height' => 200 )
                                );

            $title   = get_post_meta( $post->ID , $this->_plugin_constants->POST_META_SURVEY_THANK_YOU_TITLE() , true );
            $content = get_post_meta( $post->ID , $this->_plugin_constants->POST_META_SURVEY_THANK_YOU_CONTENT() , true );

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'survey/cpt/view-survey-thank-you-message-meta-box.php' );

        }

        /**
         * Survey questions meta box view.
         *
         * @since 1.0.0
         * @access public
         */
        public function view_survey_questions_meta_box() {

            global $post;

            $table_headings  = $this->_plugin_constants->QUESTIONS_TABLE_HEADINGS();
            $question_types  = $this->_plugin_constants->QUESTION_TYPES();
            $views_root_path = $this->_plugin_constants->VIEWS_ROOT_PATH();

            $survey_responses = ASS_Helper::get_survey_responses( $post->ID );
            $read_only        = !empty( $survey_responses ) ? 'read-only' : '';

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'survey/cpt/survey-questions-meta-box.php' );

        }

        /**
         * Timed email offer upgrade meta box.
         *
         * @since 1.0.0
         * @access public
         */
        public function view_ass_upgrade_meta_box() {

            $banner_img_url = $this->_plugin_constants->IMAGES_ROOT_URL() . 'ass-premium-upsell-edit-screen.png';

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'survey/cpt/view-ass-upgrade-meta-box.php' );

        }




        /*
        |--------------------------------------------------------------------------
        | Survey CPT Listing Mods
        |--------------------------------------------------------------------------
        */

        /**
         * Remove bulk edit on survey listing.
         *
         * @since 1.1.0
         * @access public
         *
         * @param array $actions Array of bulk actions.
         * @return array Modified array of bulk actions.
         */
        public function remove_bulk_edit_on_survey_listing( $actions ) {

            unset( $actions[ 'edit' ] );
            return $actions;

        }

        /**
         * Remove quick edit on survey listing.
         *
         * @since 1.1.0
         * @access public
         *
         * @param array $actions Array of action links.
         * @return array Modified array of action links.
         */
        public function remove_quick_edit_on_survey_listing( $actions ) {

            if ( get_post_type() === $this->_plugin_constants->SURVEY_CPT_NAME() )
                unset( $actions['inline hide-if-no-js'] );

            return $actions;

        }




        /*
        |--------------------------------------------------------------------------
        | AJAX Interfaces
        |--------------------------------------------------------------------------
        */

        /**
         * Save Survey CTA Data.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function as_survey_save_survey_cta( $survey_id = null , $data = null , $ajax_call = true ) {

            if ( $ajax_call === true ) {

                $survey_id = filter_var( $_REQUEST[ 'survey_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $data      = $_REQUEST[ 'data' ];

            }

            if ( !filter_var( $data , FILTER_CALLBACK , array( 'options' => array( $this , 'validate_survey_cta_data' ) ) ) ) {

                $response = array(
                    'status'        => 'fail',
                    'error_message' => __( 'Invalid Survey CTA Data' , 'after-sale-surveys' )
                );

            } else {

                $data = $this->sanitize_survey_cta_data( $data );

                update_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_CTA_TITLE() , $data[ 'title' ] );
                update_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_CTA_CONTENT() , $data[ 'content' ] );

                $response = array( 'status' => 'success' );

            }

            if ( $ajax_call === true ) {

                header( 'Content-Type: application/json' );
                echo json_encode( $response );
                die();

            } else
                return $response;

        }

        /**
         * Save Survey Thank You Data.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function as_survey_save_survey_thankyou( $survey_id = null , $data = null , $ajax_call = true ) {

            if ( $ajax_call === true ) {

                $survey_id = filter_var( $_REQUEST[ 'survey_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $data      = $_REQUEST[ 'data' ];

            }

            if ( !filter_var( $data , FILTER_CALLBACK , array( 'options' => array( $this , 'validate_survey_thankyou_data' ) ) ) ) {

                $response = array(
                    'status'        => 'fail',
                    'error_message' => __( 'Invalid Survey Thank You Data' , 'after-sale-surveys' )
                );

            } else {

                $data = $this->sanitize_survey_thankyou_data( $data );

                update_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_THANK_YOU_TITLE() , $data[ 'title' ] );
                update_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_THANK_YOU_CONTENT() , $data[ 'content' ] );

                $response = array( 'status' => 'success' );

            }

            if ( $ajax_call === true ) {

                header( 'Content-Type: application/json' );
                echo json_encode( $response );
                die();

            } else
                return $response;

        }

        /**
         * Return survey questions in format that is compatible with datatables library requires.
         * This data is then in turn populated to the survey questions datatables on the admin.
         *
         * @since 1.0.0
         * @access public
         *
         * @param null $survey_id
         * @param bool|true $ajax_call
         * @return array
         */
        public function as_survey_load_survey_questions_on_datatables( $survey_id = null , $ajax_call = true ) {

            if ( $ajax_call === true )
                $survey_id = $_REQUEST[ 'survey_id' ]; // post id of 'as_survey' cpt entry.

            $survey_questions = get_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_QUESTIONS() , true );
            if ( !is_array( $survey_questions ) )
                $survey_questions = array();

            $data = array(
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => array()
            );
            $question_types = $this->_plugin_constants->QUESTION_TYPES();

            foreach ( $survey_questions as $page_number => $page_questions ) {

                foreach ( $page_questions as $sort_order => $question ) {

                    if ( !in_array( 'after-sale-surveys-premium/after-sale-surveys-premium.php' , apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) &&
                         $question[ 'question-type' ] != 'multiple-choice-single-answer' )
                         continue;

                    $d = array(
                        $sort_order,
                        $question[ 'question-text' ],
                        $question_types[ $question[ 'question-type' ] ],
                        $question[ 'required' ],
                        $this->_plugin_constants->QUESTIONS_TABLE_ROW_ACTIONS()
                    );

                    $d = apply_filters( 'as_survey_questions_table_item_data' , $d , $page_number , $sort_order , $question , $survey_id );

                    $data[ 'data' ][] = $d;
                    $data[ 'recordsTotal' ]++;
                    $data[ 'recordsFiltered' ]++;

                }

            }

            // Sort survey questions
            usort( $data[ 'data' ] , array( $this , 'sort_survey_questions' ) );

            // Length and Paging
            $data[ 'data' ] = array_slice( $data[ 'data' ] , $_REQUEST[ 'start' ] , $_REQUEST[ 'length' ] );

            if ( $ajax_call === true ) {

                header( 'Content-Type: application/json' );
                echo json_encode( $data );
                die();

            } else
                return $data;

        }

        /**
         * Return multiple choice type question's multiple choices.
         *
         * @param null $survey_id
         * @param null $page_number
         * @param null $question_order_number
         * @param bool|true $ajax_call
         * @return array
         */
        public function as_survey_load_survey_question_choices( $survey_id = null , $page_number = null , $question_order_number = null , $ajax_call = true ) {

            if ( $ajax_call === true ) {

                $survey_id             = $_REQUEST[ 'survey_id' ];
                $page_number           = $_REQUEST[ 'page_number' ];
                $question_order_number = $_REQUEST[ 'question_order_number' ];

            }

            $questions = get_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_QUESTIONS() , true );
            if ( !is_array( $questions ) )
                $questions = array();

            if ( !isset( $questions[ $page_number ][ $question_order_number ] ) ) {

                $response = array(
                    'status'                => 'fail',
                    'error_message'         => __( 'Specified question does not exist' , 'after-sale-surveys' ),
                    'page_number'           => $page_number,
                    'question_order_number' => $question_order_number
                );

            } else {

                $question = $questions[ $page_number ][ $question_order_number ];

                if ( in_array( $question[ 'question-type' ] , $this->_plugin_constants->MULTIPLE_CHOICE_QUESTION_TYPES() ) ) {

                    $choices = isset( $question[ 'responses' ][ 'multiple-choices' ] ) ? $question[ 'responses' ][ 'multiple-choices' ] : array();

                    $response = array(
                        'status'  => 'success',
                        'choices' => $choices
                    );

                } else {

                    // Must be triggered by an edit
                    // Editing a non-multiple choice question to a multiple choice one
                    // We return empty choices if thats the case

                    $response = array(
                        'status'  => 'success',
                        'choices' => array()
                    );

                }

            }

            if ( $ajax_call === true ) {

                header( 'Content-Type: application/json' );
                echo json_encode( $response );
                die();

            } else
                return $response;

        }

        /**
         * Get new question order number.
         *
         * @since 1.0.0
         * @access public
         *
         * @param null $survey_id
         * @param null $page_number
         * @param bool|true $ajax_call
         * @return array
         */
        public function as_survey_get_new_question_order_number( $survey_id = null , $page_number = null , $ajax_call = true ) {

            if ( $ajax_call === true ) {

                $survey_id   = $_REQUEST[ 'survey_id' ];
                $page_number = $_REQUEST[ 'page_number' ];

            }

            $survey_questions = get_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_QUESTIONS() , true );
            if ( !is_array( $survey_questions ) )
                $survey_questions = array();

            if ( !array_key_exists( $page_number , $survey_questions ) ) {

                $response = array(
                    'status'        => 'fail',
                    'error_message' => __( 'Question page number does not exist' , 'after-sale-surveys' )
                );

            } else {

                end( $survey_questions[ $page_number ] );
                $new_question_order_number = key( $survey_questions[ $page_number ] ) ? ( (int) key( $survey_questions[ $page_number ] ) + 1 ) : 1;

                $response = array(
                    'status'                    => 'success',
                    'new_question_order_number' => $new_question_order_number
                );

            }

            if ( $ajax_call === true ) {

                header( "Content-Type: application/json" );
                echo json_encode( $response );
                die();

            } else
                return $response;

        }

        /**
         * Get specific question data.
         *
         * @since 1.0.0
         * @access public
         *
         * @param null $survey_id
         * @param null $page_number
         * @param null $order_number
         * @param bool|true $ajax_call
         * @return array
         */
        public function as_survey_get_question_data( $survey_id = null , $page_number = null , $order_number = null , $ajax_call = true ) {

            if ( $ajax_call === true ) {

                $survey_id    = $_REQUEST[ 'survey_id' ];
                $page_number  = $_REQUEST[ 'page_number' ];
                $order_number = $_REQUEST[ 'order_number' ];

            }

            $survey_questions = get_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_QUESTIONS() , true );
            if ( !is_array( $survey_questions ) )
                $survey_questions = array();

            if ( !isset( $survey_questions[ $page_number ][ $order_number ] ) ) {

                $response = array(
                    'status'        => 'success',
                    'error_message' => __( 'Specified question does not exist' , 'after-sale-surveys' ),
                    'page_number'   => $page_number,
                    'order_number'  => $order_number
                );

            } else {

                $question = $survey_questions[ $page_number ][ $order_number ];

                $response = array(
                    'status'   => 'success',
                    'question' => $question
                );

            }

            if ( $ajax_call === true ) {

                header( "Content-Type: application/json" );
                echo json_encode( $response );
                die();

            } else
                return $response;

        }

        /**
         * Save survey question. Could be add or edit.
         *
         * @since 1.0.0
         * @access public
         *
         * @param null $survey_id
         * @param null $question_data
         * @param bool|true $ajax_call
         * @return array
         */
        public function as_survey_save_survey_question( $survey_id = null , $question_data = null , $ajax_call = true ) {

            if ( $ajax_call === true ) {

                $question_data = $_REQUEST[ 'question_data' ];
                $survey_id     = $_REQUEST[ 'survey_id' ];

            }

            // DO not allow editing of survey entry if it already has responses
            $survey_responses = ASS_Helper::get_survey_responses( $survey_id );

            if ( !empty( $survey_responses ) ) {

                $err_msg = __( 'You cannot add or edit questions on this survey because it already has responses.' , 'after-sale-surveys' ) . '<br/>' .
                           __( 'You can either delete all responses for this survey so you can edit it or create a new survey with your desired changes.' , 'after-sale-surveys' ) . '<br/>';

                $response = array(
                    'status'        => 'fail',
                    'error_message' => $err_msg
                );

            } else {

                $survey_questions = get_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_QUESTIONS() , true );
                if ( !is_array( $survey_questions ) )
                    $survey_questions = array();

                if ( $question_data[ 'mode' ] == 'add-question' ) {

                    $page_number         = $question_data[ 'page-number' ];
                    $order_number        = $question_data[ 'order-number' ];
                    $question            = $this->_construct_question( $question_data );
                    $source_order_number = false;

                    if ( $order_number <= 0 )
                        $order_number = 1;
                    elseif ( !array_key_exists( $order_number , $survey_questions[ $page_number ] ) ) {

                        $order_number = $this->as_survey_get_new_question_order_number( $survey_id , $page_number , false );
                        $order_number = $order_number[ 'new_question_order_number' ];

                    }

                    $needs_reordering = ( array_key_exists( $page_number , $survey_questions ) && ( array_key_exists( $order_number , $survey_questions[ $page_number ] ) ) ) ? true : false;

                } elseif ( $question_data[ 'mode' ] == 'edit-question' ) {

                    $original_page_number  = $question_data[ 'original-page-number' ];
                    $original_order_number = $question_data[ 'original-order-number' ];
                    $page_number           = $question_data[ 'page-number' ];
                    $order_number          = $question_data[ 'order-number' ];
                    $needs_reordering      = true;

                    if ( $order_number <= 0 )
                        $order_number = 1;
                    elseif ( !array_key_exists( $order_number , $survey_questions[ $page_number ] ) ) {

                        $order_number = $this->as_survey_get_new_question_order_number( $survey_id , $page_number , false );
                        $order_number = $order_number[ 'new_question_order_number' ];

                    }

                    $question = $this->_construct_question( $question_data );

                    if ( $original_page_number == $page_number )
                        $source_order_number = $original_order_number;
                    else
                        $source_order_number = false;

                }

                if ( $needs_reordering ) {

                    // Lower number in priority has higher precedence

                    if ( $source_order_number && $order_number > $source_order_number )
                        $this->_reorder_survey_components( $survey_questions[ $page_number ] , $question , $order_number , $source_order_number , 'forward' ); // Order number move to a much lower priority
                    else
                        $this->_reorder_survey_components( $survey_questions[ $page_number ] , $question , $order_number , $source_order_number , 'backward' ); // Order number move to a much higher priority

                } else {

                    // No need for re-ordering but may require order number correction
                    $new_order_number = $this->as_survey_get_new_question_order_number( $survey_id , $page_number , false );
                    $new_order_number = $new_order_number[ 'new_question_order_number' ];

                    if ( $new_order_number != $order_number )
                        $order_number = $new_order_number;

                    $survey_questions[ $page_number ][ $order_number ] = $question;

                }

                if ( $question_data[ 'mode' ] == 'edit-question' && $original_page_number != $page_number )
                    unset( $survey_questions[ $original_page_number ][ $original_order_number ] );

                update_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_QUESTIONS() , $survey_questions );

                $response = array( 'status' => 'success' );

            }

            if ( $ajax_call === true ) {

                header( 'Content-Type: application/json' );
                echo json_encode( $response );
                die();

            } else
                return $response;

        }

        /**
         * Delete survey question.
         *
         * @since 1.0.0
         * @access public
         *
         * @param null $survey_id
         * @param null $page_number
         * @param null $order_number
         * @param bool|true $ajax_call
         * @return array
         */
        public function as_survey_delete_survey_question( $survey_id = null , $page_number = null , $order_number = null , $ajax_call = true ) {

            if ( $ajax_call === true ) {

                $survey_id    = $_REQUEST[ 'survey_id' ];
                $page_number  = $_REQUEST[ 'page_number' ];
                $order_number = $_REQUEST[ 'order_number' ];

            }

            // DO not allow editing of survey entry if it already has responses
            $survey_responses = ASS_Helper::get_survey_responses( $survey_id );

            if ( !empty( $survey_responses ) ) {

                $err_msg = __( 'You can not delete questions for this survey because it already have responses.' , 'after-sale-surveys' ) . '<br/>' .
                           __( 'You can either delete all responses for this survey so you can edit it or Just create a new survey with your desired changes.' , 'after-sale-surveys' ) . '<br/>';

                $response = array(
                                'status'        => 'fail',
                                'error_message' => $err_msg
                            );

            } else {

                $survey_questions = get_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_QUESTIONS() , true );
                if ( !is_array( $survey_questions ) )
                    $survey_questions = array();

                if ( !isset( $survey_questions[ $page_number ][ $order_number ] ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => __( 'Specified question to be removed does not exist' , 'after-sale-surveys' ),
                        'page_number'   => $page_number,
                        'order_number'  => $order_number
                    );

                } else {

                    $temp_questions       = array();
                    $order_number_counter = 0;

                    unset( $survey_questions[ $page_number ][ $order_number ] );

                    // Adjust question order number
                    foreach ( $survey_questions[ $page_number ] as $question ) {

                        $order_number_counter++;
                        $temp_questions[ $order_number_counter ] = $question;

                    }

                    $survey_questions[ $page_number ] = $temp_questions;

                    update_post_meta( $survey_id , $this->_plugin_constants->POST_META_SURVEY_QUESTIONS() , $survey_questions );

                    $response = array( 'status' => 'success' );

                }

            }

            if ( $ajax_call === true ) {

                header( 'Content-Type: application/json' );
                echo json_encode( $response );
                die();

            } else
                return $response;

        }




        /*
        |--------------------------------------------------------------------------
        | Utilities
        |--------------------------------------------------------------------------
        */

        /**
         * Validate survey cta data.
         *
         * @since 1.0.0
         * @access public
         *
         * @return boolean
         */
        public function validate_survey_cta_data( $data ) {

            if ( is_array( $data ) &&
                 array_key_exist( 'title' , $data ) && $data[ 'title' ] &&
                 array_key_exist( 'content' , $data ) && $data[ 'content' ] ) {

                return apply_filters( 'as_survey_additional_survey_cta_data_validation' , true , $data );

            } else
                return false;

        }

        /**
         * Sanitize survey cta data.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function sanitize_survey_cta_data( $data ) {

            $data[ 'title' ]   = filter_var( $data[ 'title' ] , FILTER_SANITIZE_STRING );
            $data[ 'content' ] = wp_kses_post( $data[ 'content' ] );

            return apply_filters( 'as_survey_additiional_survey_cta_data_sanitation' , $data );

        }

        /**
         * Validate survey thankyou data.
         *
         * @since 1.0.0
         * @access public
         *
         * @return boolean
         */
        public function validate_survey_thankyou_data( $data ) {

            if ( is_array( $data ) &&
                 array_key_exist( 'title' , $data ) && $data[ 'title' ] &&
                 array_key_exist( 'content' , $data ) && $data[ 'content' ] ) {

                return apply_filters( 'as_survey_additional_survey_thankyou_data_validation' , true , $data );

            } else
                return false;

        }

        /**
         * Sanitize survey thank you data.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function sanitize_survey_thankyou_data( $data ) {

            $data[ 'title' ]   = filter_var( $data[ 'title' ] , FILTER_SANITIZE_STRING );
            $data[ 'content' ] = wp_kses_post( $data[ 'content' ] );

            return apply_filters( 'as_survey_additiional_survey_thankyou_data_sanitation' , $data );

        }

        /**
         * Sort survey questions. Supports sorting with multiple element as base for sorting.
         * Ex. sort by page, then by order number, then by title, so on and so forth.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $a
         * @param $b
         * @param null $column Column or index to base sorting
         * @param null $dir Sort direction, 'asc' or 'desc'
         * @return int
         */
        public function sort_survey_questions( $a , $b , $column = null , $dir = null ) {

            if ( is_null( $column ) || is_null( $dir ) ) {

                foreach( $_REQUEST[ 'order' ] as $order ) {

                    $result = $this->sort_survey_questions( $a , $b , $order[ 'column' ] , $order[ 'dir' ] );

                    if ( $result == 0 )
                        continue;

                    return $result;

                }

            } else {

                if ( $a[ $column ] == $b[ $column ] )
                    return 0;

                if ( $dir == 'asc' )
                    return ( $a[ $column ] < $b[ $column ] ) ? -1 : 1;
                elseif ( $dir == 'desc' )
                    return ( $a[ $column ] > $b[ $column ] ) ? -1 : 1;

            }

        }

        /**
         * Re-order survey components (Survey page and Survey page questions).
         * Make sure that survey question page and survey question order numbers are in "order".
         *
         * @since 1.0.0
         * @access public
         *
         * @param $components
         * @param $source_data
         * @param $destination_index
         * @param $source_index
         * @param $direction
         */
        private function _reorder_survey_components( &$components , $source_data , $destination_index , $source_index , $direction ) {

            if ( $destination_index && $source_index && $destination_index == $source_index ) {

                // Self update
                $components[ $destination_index ] = $source_data;

            } elseif ( $source_data && !$destination_index ) {

                // Append to the end. Usually on adding new questions
                end( $components );
                $destination_index = ( (int) key( $components ) ) + 1 ;
                $components[ $destination_index ] = $source_data;

            } elseif ( $source_data && $destination_index ) {

                if ( $direction == 'backward' ) {

                    reset( $components );

                    while( key( $components ) != $destination_index )
                        next( $components );

                    next( $components );

                } elseif ( $direction == 'forward' ) {

                    end( $components );

                    while( key( $components ) != $destination_index )
                        prev( $components );

                    prev( $components );

                }

                $new_destination_index = key( $components );
                $new_source_data       = $components[ $destination_index ];

                if ( $source_data ) {

                    $components[ $destination_index ] = $source_data;

                    if ( $source_index )
                        $components[ $source_index ] = null;

                    $this->_reorder_survey_components( $components , $new_source_data , $new_destination_index , $source_index , $direction );

                }

            }

        }

        /**
         * Construct question data.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $question_data
         * @return mixed
         */
        private function _construct_question( $question_data ) {

            $question = array();

            $question[ 'required' ]      = $question_data[ 'required' ];
            $question[ 'question-text' ] = $question_data[ 'question-text' ];
            $question[ 'question-type' ] = $question_data[ 'question-type' ];
            $question[ 'responses' ]     = array();

            if ( $question[ 'question-type' ] == 'multiple-choice-single-answer' ) {

                $question[ 'responses' ][ 'multiple-choices' ] = array();
                $order_number = 0;

                foreach ( $question_data[ 'multiple-choices' ] as $q ) {

                    $order_number++;
                    $question[ 'responses' ][ 'multiple-choices' ][ $order_number ] = $q;

                }

            }

            return apply_filters( 'as_survey_construct_' . $question[ 'question-type' ] . '_question' , $question , $question_data );

        }

        /**
         * Check validity of a save post action.
         *
         * @since 1.0.0
         * @access private
         *
         * @param $post_id
         * @return bool
         */
        private function _valid_save_post_action( $post_id ) {

            if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || !current_user_can( 'edit_page' , $post_id ) || get_post_type() != $this->_plugin_constants->SURVEY_CPT_NAME() || empty( $_POST ) )
                return false;
            else
                return true;

        }



        /*
        |--------------------------------------------------------------------------
        | CPT entry custom columns
        |--------------------------------------------------------------------------
        */

        /**
         * Add custom columns to survey cpt entry listing.
         *
         * @since 1.1.1
         * @access public
         *
         * @param $columns array CPT listing columns array.
         * @return array Modified CPT listing columns array.
         */
        public function add_survey_listing_column( $columns ) {

            $all_keys    = array_keys( $columns );
            $title_index = array_search( 'title' , $all_keys );

            $new_columns_array = array_slice( $columns , 0 , $title_index + 1 , true ) +
                                 apply_filters( 'as_survey_custom_columns' ,
                                                array(
                                                    'total_respondents' => __( 'Total Respondents' , 'after-sale-surveys' )
                                                ),
                                                $columns ) +
                                 array_slice( $columns , $title_index + 1 , NULL , true );

            return $new_columns_array;

        }

        /**
         * Add values to the custom columns of survey cpt entry listing.
         *
         * @since 1.1.1
         * @access public
         *
         * @param $columns array CPT listing columns array.
         * @param $post_id int/string Post Id.
         */
        public function add_survey_listing_column_data( $column , $post_id ) {

            switch ( $column ) {

                case 'total_respondents':
                    $respondents = ASS_Helper::get_survey_total_respondents( $post_id );
                    echo '<div class="total-respondents">' . $respondents . '</div>';
                    do_action( 'as_survey_after_total_respondents_custom_column' , $column , $post_id );
                    break;

            }

            do_action( 'as_survey_custom_columns_data' , $column , $post_id );

        }

    }

}
