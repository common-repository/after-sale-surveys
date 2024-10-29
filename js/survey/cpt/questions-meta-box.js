/* global JQuery */
jQuery( document).ready( function( $ ) {

    /*
     |--------------------------------------------------------------------------
     | Variables
     |--------------------------------------------------------------------------
     */

    var $survey_question_meta_box         = $( "#survey-question-meta-box" ),
        $survey_question_meta             = $survey_question_meta_box.find( "#survey-question-meta" ),
        $survey_questions_table           = $survey_question_meta_box.find( "#survey-questions-table" ),
        $save_question_popup_form_trigger = $survey_question_meta_box.find( "#open-save-question-popup-form" ),
        $save_question_popup_form         = $survey_question_meta_box.find( "#save-question-popup-form" ),
        $survey_response_container        = $survey_question_meta_box.find( "#survey-response-container" ),
        $choices_section                  = $survey_question_meta_box.find( "#choices-section" ),
        $choices_controls                 = $choices_section.find( "#choices-controls" ),
        $choices_list                     = $choices_section.find( "#choices-list" ),
        survey_id                         = $.trim( $survey_question_meta.find( ".survey-id" ).text() ),
        empty_choices_markup              = '<li class="empty-choices"><p>' + survey_questions_meta_box_params.i18n_no_choices_available + '</p></li>',
        retrieving_choices_markup         = '<li class="retrieving-choices"><p>' + survey_questions_meta_box_params.i18n_retrieving_choices + '</p></li>';


    /*
     |--------------------------------------------------------------------------
     | Functions
     | - Utility functions used throughout the plugin
     |--------------------------------------------------------------------------
     */

    // Construct new choice item markup
    function get_choice_item_markup( choice_text ) {

        return '<li>' +
                    '<span class="drag-handle"><span class="dashicons dashicons-sort"></span></span>' +
                    '<span class="choice">' + choice_text + '</span>' +
                    '<span class="controls">' +
                        '<span class="control edit-control dashicons dashicons-edit"></span>' +
                        '<span class="control delete-control dashicons dashicons-no"></span>' +
                    '</span>' +
                '</li>';

    }




    /*
     |--------------------------------------------------------------------------
     | Survey Questions Table
     | - Processing behavior
     |--------------------------------------------------------------------------
     */

    $survey_questions_table.on( "retrieving_data_mode" , function() {

        $( this ).addClass( "retrieving-data-mode" );
        return false; // Just in case, prevent event from bubbling up

    } );

    $survey_questions_table.on( "processing_mode" , function( event , $tr ) {

        $( this ).addClass( "processing-mode" );
        $tr.addClass( "processing-row" ).find(".column-controls" ).prepend('<span class="spinner"></span>' );

        return false; // Just in case, prevent event from bubbling up

    } );

    $survey_questions_table.on( "normal_mode" , function() {

        $( this )
            .removeClass( "retrieving-data-mode" )
            .removeClass( "processing-mode" )
            .find( "tr" )
                .removeClass( "processing-row" )
            .find( ".column-controls .spinner" )
                .remove();

        return false; // Just in case, prevent event from bubbling up

    } );




    /*
     |--------------------------------------------------------------------------
     | Question Type Change
     | - Depending on the question type, additional fields will appear
     | - Multiple choice questions will add multiple choices field
     |--------------------------------------------------------------------------
     */

    $choices_section.on( 'load_survey_question_choices' , function() {

        var choices_items_markup = '',
            $this = $( this );

        if ( $this.closest( "#save-question-popup-form" ).attr( "data-mode" ) == "add-question" ) {

            if ( $choices_list.find( "li" ).length <= 0 )
                $choices_list.html( empty_choices_markup );

            $this.css( "display" , "block" );

        } else if ( $this.closest( "#save-question-popup-form" ).attr( "data-mode" ) == "edit-question" ) {

            if ( $choices_list.find( "li" ).length <= 0 ) {

                $save_question_popup_form.trigger( 'disable_form_actions' );
                $save_question_popup_form.find( '#add-choice-btn' ).attr( 'disabled' , 'disabled' );

                $this.css( "display" , "block" );
                $choices_list.html( retrieving_choices_markup );

                var page_number  = parseInt( $save_question_popup_form.find( ".meta .page-number" ).text() , 10 ),
                    order_number = parseInt( $save_question_popup_form.find( ".meta .order-number" ).text() , 10 );

                as_survey_questions_backend_ajax_services.load_survey_question_choices( survey_id , page_number , order_number )
                    .done( function( data , text_status , jqxhr ) {

                        if ( data.status == 'success' ) {

                            for ( var order_number in data[ 'choices' ] )
                                if ( data[ 'choices' ].hasOwnProperty( order_number ) )
                                    choices_items_markup += get_choice_item_markup( data[ 'choices' ][ order_number ] );
                            
                            if ( choices_items_markup == '' )
                                $choices_list.html(empty_choices_markup );
                            else {

                                $choices_list.html( choices_items_markup );

                                if ( !$choices_list.hasClass( "ui-sortable" ) )
                                    $choices_list.sortable( { handle: ".dashicons-sort" } ).disableSelection();

                            }

                        } else {

                            vex.dialog.alert( data.error_message );

                        }

                    } )
                    .fail( function( jqxhr , text_status , error_thrown ) {

                        // TODO: Handle errors
                        console.log( jqxhr );

                    } )
                    .always( function() {

                        $save_question_popup_form.trigger( 'enable_form_actions' );
                        $save_question_popup_form.find( '#add-choice-btn' ).removeAttr( 'disabled' );

                    } );

            } else
                $this.css( "display" , "block" );

        }

    } );

    $save_question_popup_form.on( 'disable_form_actions' , function() {

        $save_question_popup_form.find( "#save-survey-question-btn" ).attr( "disabled" , "disabled" );

    } );

    $save_question_popup_form.on( 'enable_form_actions' , function() {

        $save_question_popup_form.find( "#save-survey-question-btn" ).removeAttr( "disabled" );

    } );

    $save_question_popup_form.find( '#save-survey-question-btn' ).on( 'processing_mode' , function() {

        $( this )
            .attr( 'disabled' , 'disabled' )
            .siblings( '.spinner' )
                .css( 'visibility' , 'visible' );

        // Required so event don't bubble up to the container form's synonymous 'processing_mode' custom event.
        return false;

    } );

    $save_question_popup_form.find( '#save-survey-question-btn' ).on( 'normal_mode' , function() {

        $( this )
            .removeAttr( 'disabled' )
            .siblings( '.spinner' )
                .css( 'visibility' , 'hidden' );

        // Required so event don't bubble up to the container form's synonymous 'processing_mode' custom event.
        return false;

    } );

    $save_question_popup_form.find( '#question-type' ).change( function() {

        var $this = $( this ),
            question_type = $this.val();

        $survey_response_container.find( '.question-response' ).css( 'display' , 'none' );

        if ( question_type == 'multiple-choice-single-answer' )
            $choices_section.trigger( 'load_survey_question_choices' );

        $this.trigger( 'survey_question_type_change' );

    } );




    /*
     |--------------------------------------------------------------------------
     | Edit Question ( Pre-load Data To Pop up form )
     | - Initialize the question pop up form for editing a question
     | - Does not contain the logic of actual editing of the question
     |--------------------------------------------------------------------------
     */

    $save_question_popup_form.on( 'disable_question_ordering_fields' , function() {

        $save_question_popup_form.find( "#order-number" ).attr( "disabled" , "disabled" );

    } );

    $save_question_popup_form.on( 'enable_question_ordering_fields' , function() {

        $save_question_popup_form.find( "#order-number" ).removeAttr( "disabled" );

    } );

    $save_question_popup_form.on( "processing_mode" , function( event , processing_message ) {

        $save_question_popup_form
            .find( "#pop-up-processing-markup" ).css( "display" , "block" )
            .find( ".processing-message" ).text( processing_message ).end().end()
            .find( "#pop-up-form-markup" ).css( "display" , "none" );

    } );

    $save_question_popup_form.on( "normal_mode" , function() {

        $save_question_popup_form
            .find( "#pop-up-processing-markup" ).css( "display" , "none" )
            .find( ".processing-message" ).text( "" ).end().end()
            .find( "#pop-up-form-markup" ).css( "display" , "block" );

    } );

    $survey_questions_table.delegate( '.edit-control' , 'click' , function() {

        var $this       = $( this ),
            $tr         = $this.closest( 'tr' ),
            ajax_handle = null;

        $tr.addClass( 'processing-row' );
        $save_question_popup_form.trigger( "disable_question_ordering_fields" );
        $save_question_popup_form.trigger( "processing_mode" , [ survey_questions_meta_box_params.i18n_retrieving_question_data ] );

        $save_question_popup_form.attr( "data-mode" , "edit-question" ).find( ".popup-title" ).text( survey_questions_meta_box_params.i18n_edit_question );
        $choices_list.empty();

        var page_number  = 1,
            order_number = parseInt( $.trim( $tr.find( ".order-number" ).text() ) , 10 );

        $survey_questions_table.trigger( "edit_question" , [ $tr , survey_id , page_number , order_number ] );

        $save_question_popup_form
            .find( ".meta" )
                .find( ".page-number" ).text( page_number ).end()
                .find( ".order-number" ).text( order_number );

        $.magnificPopup.open( {
            items: { src: '#save-question-popup-form' },
            type: 'inline',
            callbacks : {
                beforeOpen: function() {

                    $save_question_popup_form.find( "#choice-text" ).val( "" );

                    // Allow external plugins to do initializations before opening the questions pop up form in edit mode
                    $save_question_popup_form.trigger( "question_popup_form_before_open" , [ "edit-question" ] );

                    ajax_handle = as_survey_questions_backend_ajax_services.get_question_data( survey_id , page_number , order_number )
                        .done( function( data , text_status , jqxhr ) {

                            $save_question_popup_form.find( ".meta .page-number" ).text( page_number );
                            $save_question_popup_form.find( ".meta .order-number" ).text( order_number );

                            $save_question_popup_form.find( "#order-number" ).val( order_number );

                            if ( data[ 'question' ][ 'required' ] == 'yes' )
                                $save_question_popup_form.find( "#required-question" ).attr( 'checked' , 'checked' );
                            else
                                $save_question_popup_form.find( "#required-question" ).removeAttr( 'checked' );
                            
                            $save_question_popup_form.find( "#question-text" ).val( data[ 'question' ][ "question-text" ] );
                            $save_question_popup_form.find( "#question-type" ).val( data[ 'question' ][ "question-type" ] ).trigger( 'change' );
                            
                        } )
                        .fail( function( jqxhr , text_status , error_thrown ) {

                            // TODO: Handle errors

                        } )
                        .always( function() {

                            $save_question_popup_form.trigger( "enable_question_ordering_fields" );
                            $save_question_popup_form.trigger( "normal_mode" );

                        } );

                },
                close: function() {

                    ajax_handle.abort();
                    $survey_questions_table.find( "tr" ).removeClass( 'processing-row' );

                }
            }
        } );

    } );




    /*
     |--------------------------------------------------------------------------
     | Save Question
     | - Add / Edit question
     |--------------------------------------------------------------------------
     */

    $save_question_popup_form.find( "#order-number" ).blur( function() { $( this ).val( $.trim( $( this ).val() ) ).removeClass( 'err' ); } );

    $save_question_popup_form.find( "#question-text" ).blur( function() { $( this ).val( $.trim( $( this ).val() ) ).removeClass( 'err' ); } );

    $save_question_popup_form.find( "#question-type" ).blur( function() { $( this ).val( $.trim( $( this ).val() ) ).removeClass( 'err' ); } );

    $save_question_popup_form.on( "construct_question_response" , function( event  , question_data ) {

        if ( question_data[ 'question-type' ] == 'multiple-choice-single-answer' ) {

            question_data[ 'multiple-choices' ] = {};

            if ( $choices_list.find( "li.empty-choices" ).length <= 0 && $choices_list.find( "li" ).length > 0 ) {

                var order_number = 0;

                $choices_list.find( "li" ).each( function() {

                    var $this = $( this );

                    order_number++;
                    question_data[ 'multiple-choices' ][ order_number ] = $.trim( $this.find( ".choice" ).text() );

                } );

            }

        }

    } );

    $save_question_popup_form.on( "validate_question_data" , function( event , question_data , error_fields ) {

        if ( question_data[ "order-number" ] == "" || isNaN( question_data[ "order-number" ] ) ) {

            $save_question_popup_form.find( "#order-number" ).val( "" ).addClass( "err" );
            error_fields.push( survey_questions_meta_box_params.i18n_order_number );

        }

        if ( question_data[ "question-text" ] == '' ) {

            $save_question_popup_form.find( "#question-text" ).val( "" ).addClass( "err" );
            error_fields.push( survey_questions_meta_box_params.i18n_question_text );

        }

        if ( question_data[ "question-type" ] == '' ) {

            $save_question_popup_form.find( "#question-type" ).val( "" ).addClass( "err" );
            error_fields.push( survey_questions_meta_box_params.i18n_question_type );

        } else if ( question_data[ "question-type" ] == 'multiple-choice-single-answer' ) {

            if ( $.isEmptyObject( question_data[ 'multiple-choices' ] ) )
                error_fields.push( survey_questions_meta_box_params.i18n_no_choices_supplied );

        }

        // Allow external plugins to hook into plugin validation process
        $save_question_popup_form.trigger( "validating_question_data" , [ question_data , error_fields ] );

    } );

    $save_question_popup_form.find( "#save-survey-question-btn" ).click( function() {

        var $this         = $( this ),
            question_data = {},
            error_fields  = [];

        $this.trigger( "processing_mode" );

        // Construct question data
        question_data[ "mode" ]          = $save_question_popup_form.attr( "data-mode" );
        question_data[ "page-number" ]   = 1;
        question_data[ "order-number" ]  = parseInt( $.trim( $save_question_popup_form.find( "#order-number" ).val() ) , 10 );
        question_data[ "required" ]      = $save_question_popup_form.find( "#required-question" ).is( ":checked" ) ? 'yes' : 'no';
        question_data[ "question-text" ] = $.trim( $save_question_popup_form.find( "#question-text" ).val() );
        question_data[ "question-type" ] = $.trim( $save_question_popup_form.find( "#question-type" ).val() );

        // Add additional necessary data when in edit mode
        if ( question_data[ "mode" ] == 'edit-question' ) {

            question_data[ 'original-page-number' ]  = parseInt( $save_question_popup_form.find( ".meta .page-number" ).text() , 10 );
            question_data[ 'original-order-number' ] = parseInt( $save_question_popup_form.find( ".meta .order-number" ).text() , 10 );

        }

        // Allow external plugins to modify question data
        $save_question_popup_form.trigger( "construct_question_data" , [ question_data ] );

        // Construct question response
        $save_question_popup_form.trigger( "construct_question_response" , [ question_data ] );

        // Validate question data
        $save_question_popup_form.trigger( "validate_question_data" , [ question_data , error_fields ] );

        if ( error_fields.length ) {

            var message = survey_questions_meta_box_params.i18n_fill_form_properly + "<br><br>";

            for ( var i = 0 ; i < error_fields.length ; i++ )
                message += "<b>" + error_fields[ i ] + "<b/><br>";

            vex.dialog.alert( message );

            $this.trigger( "normal_mode" );

            return false;

        }

        as_survey_questions_backend_ajax_services.save_survey_question( survey_id , question_data )
            .done( function( data , text_status , jqxhr ) {

                if ( data.status == 'success' ) {

                    var msg = ( question_data[ "mode" ] == 'add-question' ) ? survey_questions_meta_box_params.i18n_question_added : survey_questions_meta_box_params.i18n_question_edited;

                    $.magnificPopup.close();
                    as_survey_questions_datatable_handle.ajax.reload( null , false );
                    vex.dialog.alert( msg );

                } else {

                    // TODO: Provide some way to present whatever the error is

                    var msg = ( question_data[ "mode" ] == 'add-question' ) ? survey_questions_meta_box_params.i18n_question_add_failed : survey_questions_meta_box_params.i18n_question_edit_failed;

                    msg += '<br><br>' + data.error_message;

                    vex.dialog.alert( msg );

                }

            } )
            .fail( function( jqxhr , text_status , error_thrown ) {

                // TODO: Handle errors
                console.log( jqxhr );

            } )
            .always( function() {

                $this.trigger( "normal_mode" );

            } );

    } );




    /*
     |--------------------------------------------------------------------------
     | Delete Question
     |--------------------------------------------------------------------------
     */

    $survey_questions_table.delegate( '.delete-control' , 'click' , function() {

        var $this          = $( this ),
            $tr            = $this.closest( 'tr' ),
            question_text  = $.trim( $tr.find( '.question-text' ).text() ),
            dialog_message = survey_questions_meta_box_params.i18n_confirm_delete_question;

            dialog_message = ( question_text != '' ) ? dialog_message + '<br><b>"' + question_text + '"</b>' : dialog_message;

        $survey_questions_table.trigger( "processing_mode" , [ $tr ] );

        vex.dialog.confirm( {
            message: dialog_message,
            callback: function( value ) {

                if ( value ) {

                    var page_number  = 1,
                        order_number = parseInt( $.trim( $tr.find( ".order-number" ).text() ) , 10 );

                    $survey_questions_table.trigger( "delete_question" , [ $tr , survey_id , page_number , order_number ] );

                    as_survey_questions_backend_ajax_services.delete_survey_question( survey_id , page_number , order_number )
                        .done( function( data , text_status , jqxhr ) {

                            if ( data.status == 'success' ) {

                                as_survey_questions_datatable_handle.ajax.reload( null , false );
                                vex.dialog.alert( survey_questions_meta_box_params.i18n_question_deleted );

                            } else {

                                // TODO: Handle errors
                                vex.dialog.alert( data.error_message );

                            }

                        } )
                        .fail( function( jqxhr , text_status , error_thrown ) {

                            // TODO: Handle errors

                        } )
                        .always( function() {

                            $survey_questions_table.trigger( "normal_mode" );

                        } );

                } else
                    $survey_questions_table.trigger( "normal_mode" );

            }

        } );

    } );




    /*
     |--------------------------------------------------------------------------
     | Question Multiple Choices
     |--------------------------------------------------------------------------
     */

    $save_question_popup_form.find( "#choice-text").blur( function() { $( this ).val( $.trim( $( this ).val() ) ); } );

    $save_question_popup_form.find( "#add-choice-btn" ).click( function() {

        var new_choice          = $save_question_popup_form.find( "#choice-text" ).val(),
            choice_items_markup = '';

        if ( new_choice == '' ) {

            vex.dialog.alert( survey_questions_meta_box_params.i18n_add_choice_text );
            return false;

        }

        choice_items_markup += get_choice_item_markup( new_choice );

        if ( $choices_list.find( "li.empty-choices" ).length > 0 )
            $choices_list.empty();

        $choices_list.append( choice_items_markup );

        $save_question_popup_form.find( "#choice-text" ).val( '' );

        if ( !$choices_list.hasClass( "ui-sortable" ) )
            $choices_list.sortable( { handle: ".dashicons-sort" } ).disableSelection();

        return false;

    } );

    $save_question_popup_form.find( "#edit-choice-btn" ).click( function() {

        var choice_text = $.trim( $save_question_popup_form.find( "#choice-text" ).val() );

        if ( choice_text == "" ) {

            vex.dialog.alert( survey_questions_meta_box_params.i18n_add_choice_text );
            return false;

        }

        $choices_list.find( "li.processing").find( ".choice" ).text( choice_text );

        // Code reuse
        $save_question_popup_form.find( "#cancel-edit-choice-btn" ).trigger( "click" );

    } );

    $save_question_popup_form.find( "#cancel-edit-choice-btn" ).click( function() {

        $save_question_popup_form.find( "#choice-text" ).val( "" );

        $choices_controls
            .find( "#add-choice-btn" ).css( "display" , "inline-block" ).end()
            .find( "#edit-choice-btn" ).css( "display" , "none" ).end()
            .find( "#cancel-edit-choice-btn" ).css( "display" , "none" );

        $choices_list.removeClass( "processing" )
            .find( "li" ).removeClass( "processing" );

    } );

    $choices_list.delegate( '.edit-control' , 'click' , function() {

        var $this = $( this ),
            $li   = $this.closest( "li" );
        
        $choices_list.addClass( "processing" );
        $li.addClass( "processing" );

        var question_text = $.trim( $li.find( ".choice" ).text() );

        $save_question_popup_form.find( "#choice-text" ).val( question_text );

        $choices_controls
            .find( "#add-choice-btn" ).css( "display" , "none" ).end()
            .find( "#edit-choice-btn" ).css( "display" , "inline-block" ).end()
            .find( "#cancel-edit-choice-btn" ).css( "display" , "inline-block" );

    } );

    $choices_list.delegate( '.delete-control' , 'click' , function() {

        var $this = $( this ),
            $li   = $this.closest( 'li' );

        $li.remove();

        if ( $choices_list.find( "li" ).length <= 0 ) {

            if ( $choices_list.hasClass( "ui-sortable" ) )
                $choices_list.sortable( "destroy" );

            $choices_list.append( empty_choices_markup );

        }

    } );




    /*
     |--------------------------------------------------------------------------
     | Initialize Save Question Pop Up Form
     | - This initialization is only exclusive to the form being opened by the
     |   '+ Add Question' button. Forms opened by 'edit controls' on the questions
     |   table have different set of initialization.
     |--------------------------------------------------------------------------
     */

    $save_question_popup_form.find( "#order-number" ).on( "get_new_question_order_number" , function(event , page_number ) {

        $save_question_popup_form.trigger( "disable_form_actions" );
        $save_question_popup_form.trigger( "disable_question_ordering_fields" );

        as_survey_questions_backend_ajax_services.get_new_question_order_number( survey_id , page_number )
            .done( function( data , text_status , jqxhr ) {

                if ( data.status == 'success' )
                    $save_question_popup_form.find( "#order-number" ).val( data[ 'new_question_order_number' ] );
                else {

                    vex.dialog.alert( data.error_message );

                }

            } )
            .fail( function( jqxhr , text_status , error_thrown ) {

                // TODO: Handle error
                console.log( jqxhr );

            } )
            .always( function() {

                $save_question_popup_form.trigger( 'enable_question_ordering_fields' );
                $save_question_popup_form.trigger( 'enable_form_actions' );

            } );

    } );

    $save_question_popup_form_trigger.magnificPopup( {
        type:      'inline',
        midClick:  true, // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
        callbacks: {
            beforeOpen: function() {

                $save_question_popup_form.attr( "data-mode" , "add-question" ).find( ".popup-title" ).text( survey_questions_meta_box_params.i18n_add_question );
                $save_question_popup_form.find( "#question-text" ).val( "" );
                $save_question_popup_form.find( "#choice-text" ).val( "" );
                $choices_list.empty();

                // Allow external plugins to do initializations before opening the questions pop up form in add mode
                $save_question_popup_form.trigger( "question_popup_form_before_open" , [ "add-question" ] );

                $save_question_popup_form.find( "#order-number" ).val( "" ).trigger( "get_new_question_order_number" , [ 1 ] );
                $save_question_popup_form.find( "#question-type" ).val( "" ).trigger( "change" );

            }
        }
    } );




    /*
     |--------------------------------------------------------------------------
     | Initialization
     |--------------------------------------------------------------------------
     */

    // Only allow numbers for sort order field on pop up form
    $save_question_popup_form.find( "#order-number" ).keyup( function() {

        var raw_text    = $( this ).val();
        var return_text = raw_text.replace( /[^0-9]/g , '' );
        $( this ).val( return_text );

    } );

    // Initialize Vex Library
    vex.defaultOptions.className = 'vex-theme-plain';

} );

jQuery( window ).load( function() {

    /*
     |--------------------------------------------------------------------------
     | Initialize Survey Questions Datatable
     |--------------------------------------------------------------------------
     |
     | - We need to run this on window.load, window.load runs after document.ready
     |   window.load after all assets are loaded ( css , js, images , etc...)
     |   document.ready run after dom is fully constructed
     | - The reason mainly is for extensibility with ASSP, we need ASSP to hook into
     |   'before_initialize_questions_datatable' event, so ASSP needs to attached a
     |   callback first, then ASS executes the event.
     */

    var $survey_question_meta_box = jQuery( "#survey-question-meta-box" ),
        $survey_questions_table   = $survey_question_meta_box.find( "#survey-questions-table" ),
        $survey_question_meta     = $survey_question_meta_box.find( "#survey-question-meta" ),
        survey_id                 = jQuery.trim( $survey_question_meta.find( ".survey-id" ).text() );


    $survey_question_meta_box.on( "before_initialize_questions_datatable" , function() {

        // Set survey id
        as_survey_questions_datatable_config[ "ajax" ][ "data" ][ "survey_id" ] = survey_id;

    } );

    // Trigger custom event to allow external plugins to modify the data tables config
    $survey_question_meta_box.trigger( "before_initialize_questions_datatable" );

    // Initialize survey questions data table.
    as_survey_questions_datatable_handle = $survey_questions_table.DataTable( as_survey_questions_datatable_config );

} );