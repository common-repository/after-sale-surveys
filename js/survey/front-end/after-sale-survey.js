/* global JQuery */
jQuery( document ).ready( function( $ ) {

    /*
     |--------------------------------------------------------------------------
     | Initialize Survey Queue
     |--------------------------------------------------------------------------
     */

    // Survey Queue must be an object or else we will not be able to pass it by refferrence. Making it an array won't do either.
    var survey_queue             = { 'survey_ids' : [] },
        first_survey_time_delay  = after_sale_survey_params.options[ 'time_delay' ]; // Time delay before loading first Survey ( in seconds ).

    $( ".after-sale-survey-popup" ).each( function() {

        survey_queue.survey_ids.push( $( this ).attr( "id" ) ); // Survey element id, not the survey id in the db

    } );




    /*
     |--------------------------------------------------------------------------
     | Utility Functions
     |--------------------------------------------------------------------------
     */

    function load_after_sale_survey( $survey_id ) {

        var $popup_options = {
            items               : { src: '#' + $survey_id },
            type                : 'inline',
            closeOnContentClick : false,
            closeOnBgClick      : false,
            enableEscapeKey     : false,
            showCloseBtn        : true,
            callbacks           : {
                                    open : function() {
                                        // Open callback function if different, we just can't override it coz if we do,
                                        // magnific popup instance might not be initialized yet as opposed to the close callback
                                        // where we are assured that an instance is already initiated.

                                        // Record the attempt of offering the offer to the customer
                                        var survey_cta = $( '#' + $survey_id + ' .after-sale-survey-cta' ),
                                            survey_id  = survey_cta.attr( 'data-survey-id' ),
                                            order_id   = survey_cta.attr( 'data-order-id' );

                                        $.ajax( {
                                            url      : after_sale_survey_params.ajaxurl,
                                            type     : 'POST',
                                            data     : { action : 'as_survey_record_survey_offer_attempt' , survey_id : survey_id , order_id : order_id , 'ajax-nonce' : after_sale_survey_params.nonce_record_survey_offer_attempt  },
                                            dataType : 'json'
                                        } )
                                        .done( function( data , text_status , jqxhr ) { $( 'body' ).trigger( 'as_survey_offer_attempt_done' , [ survey_id , order_id , data , text_status , jqxhr ] ); } )
                                        .fail( function( jqxhr , text_status , error_thrown ) { $( 'body' ).trigger( 'as_survey_offer_attempt_fail' , [ survey_id , order_id , jqxhr , text_status , error_thrown ] ); } )
                                        .always( function() { $( 'body' ).trigger( 'as_survey_offer_attempt_always' , [ survey_id , order_id ] ); } );

                                        $( "body" ).trigger( "after_sale_survey_open" , [ $survey_id , $.magnificPopup.instance ] );

                                    }
                                }
        };

        $( 'body' ).trigger( 'initialize_after_sale_survey_popup_options' , [ $popup_options , $survey_id ] );

        $.magnificPopup.open( $popup_options );

    }




    /*
     |--------------------------------------------------------------------------
     | Maybe Load First After Sale Survey
     |--------------------------------------------------------------------------
     */

    var current_survey_id = survey_queue.survey_ids.shift();

    if ( current_survey_id )
        setTimeout( function() { load_after_sale_survey( current_survey_id ); } , first_survey_time_delay );
    
    
    
    
    /*
     |--------------------------------------------------------------------------
     | Close Offer
     |--------------------------------------------------------------------------
     */

    $( "body" ).on( "load_next_after_sale_survey" , function( event , next_survey_id , survey_queue , $popup_instance ) {

        event.stopPropagation();

        // If next_survey_id is 'reload' or 'redirect' it means that don't load next survey coz we are going to reload/redirect this page.
        if ( next_survey_id !== 'reload' &&  next_survey_id !== 'redirect' ) {

            if ( next_survey_id ) {

                // If the next suvey id that is to be shown is in the survey queue, remove it from the queue
                // to prevent from re-showing the survey later when we proceed on popping survey id from the queue
                if ( $.inArray( next_survey_id , survey_queue.survey_ids ) ) {

                    survey_queue.survey_ids = jQuery.grep( survey_queue.survey_ids , function( value ) {
                        return value != next_survey_id;
                    } );

                }

            } else
                next_survey_id = survey_queue.survey_ids.shift();

            if ( next_survey_id ) {

                /*
                 * Intentional 1.5 seconds delay of showing up the next survey.
                 * The reason is if we show it immediately, if user clicks the close button, it may also click the
                 * newly shown ( next popup ) survey's close button, so the next survey seems to be not loaded.
                 */
                setTimeout( function() { load_after_sale_survey( next_survey_id ); } , 1500 );

            }

        }

        return $( this );

    } );

    // Override the close function of magnific popup
    $.magnificPopup.instance.close = function( next_survey_id ) {

        $( "body" ).trigger( "after_sale_survey_close" , [ next_survey_id , $.magnificPopup.instance ] );
        $( "body" ).trigger( "load_next_after_sale_survey" , [ next_survey_id , survey_queue , $.magnificPopup.instance ] );

        // You may call parent ("original") method like so:
        $.magnificPopup.proto.close.call( this /*, optional arguments */);

    };


    

    /*
     |--------------------------------------------------------------------------
     | Multiple Choice Single Answer
     |--------------------------------------------------------------------------
     */

    $( ".after-sale-survey" ).find( ".survey-questions .survey-question.multiple-choice-single-answer .multiple-choices label" ).click( function() {

        var $this = $( this );
        $this.siblings( 'input[type="radio"]' ).trigger( "click" );

    } );




    /*
     |--------------------------------------------------------------------------
     | Do Survey
     |--------------------------------------------------------------------------
     */

    $( ".after-sale-survey-cta .do-survey-btn" ).click( function() {

        var $this          = $( this ),
            $as_survey_cta = $this.closest( ".after-sale-survey-cta" ),
            $as_survey     = $as_survey_cta.siblings( ".after-sale-survey" ),
            survey_id      = $as_survey_cta.attr( 'data-survey-id' ),
            order_id       = $as_survey_cta.attr( 'data-order-id' );

        // Record survey uptake. Customer accepted to participate the survey.
        $.ajax( {
            url      : after_sale_survey_params.ajaxurl,
            type     : 'POST',
            data     : { action : 'as_survey_record_survey_uptake' , survey_id : survey_id , order_id : order_id , 'ajax-nonce' : after_sale_survey_params.nonce_record_survey_uptake  },
            dataType : 'json'
        } )
        .done( function( data , text_status , jqxhr ) { $( 'body' ).trigger( 'as_survey_uptake_done' , [ survey_id , order_id , data , text_status , jqxhr ] ); } )
        .fail( function( jqxhr , text_status , error_thrown ) { $( 'body' ).trigger( 'as_survey_uptake_fail' , [ survey_id , order_id , jqxhr , text_status , error_thrown ] ); } )
        .always( function() { $( 'body' ).trigger( 'as_survey_uptake_always' , [ survey_id , order_id ] ); } );

        $as_survey_cta.slideUp( 350 , function() {

            $as_survey.slideDown( 350 );

        } );

    } );




    /*
     |--------------------------------------------------------------------------
     | Submit Survey Response
     |--------------------------------------------------------------------------
     */

    $( ".after-sale-survey" ).on( "reset_form" , function() {

        event.stopPropagation();

        var $this = $( this );

        $this.find( "input" ).each( function() {

            var $current_field = $( this );

            switch ( $current_field.attr( 'type' ) ) {

                case 'radio':
                    $current_field.removeAttr( 'checked' );
                    break;

                case 'button':
                    $current_field.removeAttr( 'disabled' );
                    break;

            }

        } );

        $this.find( ".survey-submission-controls .spinner" ).remove();

        return $this;

    } );

    $( ".after-sale-survey" ).on( "validate_after_sale_survey" , function( event , $question , error_fields ) {

        event.stopPropagation();

        if ( $question.attr( "data-question-type" ) == 'multiple-choice-single-answer' ) {

            if ( $question.find( ".multiple-choices input[type='radio']:checked" ).length <= 0 && $question.attr( 'data-required' ) == 'yes' ) {

                $question.addClass( 'err' );
                error_fields.push( {
                    page_number   : $question.attr( "data-page-number" ),
                    order_number  : $question.attr( "data-order-number" ),
                    question_text : $.trim( $question.find( ".question-text" ).text() )
                } );

            } else
                $question.removeClass( 'err' );

        }
        
        return $( this );

    } );

    $( ".after-sale-survey" ).on( "construct_survey_response_data" , function( event , $question , response_data ) {

        event.stopPropagation();

        if ( $question.attr( "data-question-type" ) == 'multiple-choice-single-answer' ) {

            var page_number  = $.trim( $question.attr( "data-page-number" ) ),
                order_number = $.trim( $question.attr( "data-order-number" ) ),
                answer       = $.trim( $question.find( ".multiple-choices input[type='radio']:checked" ).val() ),
                data         = answer ? { answer : answer } : { answer : '' };

            if ( !response_data.hasOwnProperty( page_number ) )
                response_data[ page_number ] = {};

            if ( !response_data[ page_number ].hasOwnProperty( order_number ) )
                response_data[ page_number ][ order_number ] = data;

        }

        return $( this );

    } );

    $( ".after-sale-survey .submit-survey-btn" ).on( 'click' , function( event ) {

        var $this                       = $( this ),
            $as_survey                  = $this.closest( '.after-sale-survey' ),
            $as_survey_meta             = $as_survey.find( ".survey-meta" ),
            $survey_submission_controls = $as_survey.find( ".survey-submission-controls" ),
            $as_survey_thank_you        = $as_survey.siblings( ".after-sale-survey-thank-you" );

        $survey_submission_controls
            .prepend( "<span class='spinner'></span>" )
            .find( ".btn" ).attr( "disabled" , "disabled" );

        var error_fields = [];

        $as_survey.find( ".survey-question" ).each( function() {

            var $this = $( this );
            $as_survey.trigger( 'validate_after_sale_survey' , [ $this , error_fields ] );

        } );

        if ( error_fields.length > 0 ) {

            $this.remove( 'disabled' );

            $survey_submission_controls
                .find( ".btn" ).removeAttr( "disabled" ).end()
                .find( ".spinner" ).remove();

            // Scroll to first field with error
            window.scrollTo( 0 , 0 ); // Necessary to reset the offset, offset will base the parent window, not the popup.
            var offset = $as_survey.find( ".survey-question.err" ).first().offset();
            $( '.mfp-wrap, .mfp-container' ).animate( { scrollTop : offset.top + parseInt( parseInt( $( window ).height() , 10 ) * 1.21 , 10 ) , scrollLeft : 0 } );
            
            vex.dialog.alert( after_sale_survey_params.i18n_improper_filled_form );

            return false;

        } else {

            var response_data = {},
                survey_id     = $.trim( $as_survey.attr( "data-survey-id" ) );

            $as_survey.find( ".survey-question" ).each( function() {

                var $this = $( this );
                $as_survey.trigger( 'construct_survey_response_data' , [ $this , response_data ] );

            } );

            var args = {
                'survey_id'     : survey_id,
                'order_id'      : $.trim( $as_survey_meta.find( ".order-id" ).text() ),
                'user_id'       : $.trim( $as_survey_meta.find( ".user-id" ).text() ),
                'response_data' : response_data
            };

            $.ajax( {
                url : after_sale_survey_params.ajaxurl,
                type : 'POST',
                data : { action : 'as_survey_save_survey_response' , args : args },
                dataType : 'json'
            } )
            .done( function( data , text_status , jqxhr ) {

                if ( data.status == 'success' ) {

                    // Record survey completion. Customer completed the survey.
                    $.ajax( {
                        url      : after_sale_survey_params.ajaxurl,
                        type     : 'POST',
                        data     : { action : 'as_survey_record_survey_completion' , survey_id : args.survey_id , order_id : args.order_id , response_id : data.response_id , 'ajax-nonce' : after_sale_survey_params.nonce_record_survey_completion  },
                        dataType : 'json'
                    } )
                    .done( function( data , text_status , jqxhr ) { $( 'body' ).trigger( 'as_survey_completion_done' , [ args.survey_id , args.order_id , data , text_status , jqxhr ] ); } )
                    .fail( function( jqxhr , text_status , error_thrown ) { $( 'body' ).trigger( 'as_survey_completion_fail' , [ args.survey_id , args.order_id , jqxhr , text_status , error_thrown ] ); } )
                    .always( function() { $( 'body' ).trigger( 'as_survey_completion_always' , [ args.survey_id , args.order_id ] ); } );

                    // Move on top of the page
                    $( 'body' ).animate( {
                        scrollTop  : 0,
                        scrollLeft : 0
                    } );

                    $as_survey.slideUp( 350 , function() {
                        $as_survey_thank_you.slideDown( 350 );
                    } );

                } else {

                    console.log( data );
                    vex.dialog.alert( data.error_message );

                }

            } )
            .fail( function( jqxhr , text_status , data ) {

                console.log( jqxhr );
                vex.dialog.alert( after_sale_survey_params.i18n_response_save_failed );

            } )
            .always( function() {

                $survey_submission_controls
                    .find( ".btn" ).removeAttr( "disabled" ).end()
                    .find( ".spinner" ).remove();

            } );

        }

    } );




    /*
     |--------------------------------------------------------------------------
     | Survey Thank You
     |--------------------------------------------------------------------------
     */

    $( ".after-sale-survey-thank-you .close-thank-you-btn" ).click( function() {

        $.magnificPopup.instance.close();

    } );
    



    /*
     |--------------------------------------------------------------------------
     | Initialization
     |--------------------------------------------------------------------------
     */

    // Initialize Vex Library
    vex.defaultOptions.className = 'vex-theme-plain';

} );
