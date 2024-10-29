/* global jQuery */
jQuery( document ).ready( function( $ ) {

    var $survey_cta_metabox = $( "#survey-cta-meta-box" ),
        $survey_cta_title   = $survey_cta_metabox.find( "#survey-cta-title" ),
        $save_survey_cta     = $survey_cta_metabox.find( "#save-survey-cta" ),
        survey_id           = $survey_cta_metabox.find( '.meta .survey-id' ).text();
    

    $survey_cta_metabox.on( 'normal_mode' , function( event ) {

        event.stopPropagation();

        $survey_cta_metabox.find( '.field-set.button-field-set .spinner' ).css( {
            'display'    : 'none',
            'visibility' : 'hidden'
        } );

        $save_survey_cta.removeAttr( 'disabled' );

        return $( this );

    } );

    $survey_cta_metabox.on( 'processing_mode' , function( event ) {

        event.stopPropagation();

        $survey_cta_metabox.find( '.field-set.button-field-set .spinner' ).css( {
            'display'    : 'inline-block',
            'visibility' : 'visible'
        } );

        $save_survey_cta.attr( 'disabled' , 'disabled' );

        return $( this );

    } );

    $survey_cta_metabox.on( 'construct_data' , function( event , data , errors ) {

        event.stopPropagation();

        var $this   = $( this ),
            title   = $.trim( $survey_cta_title.val() ),
            content = '';

        if ( $this.find( "#survey-cta-content_ifr" ).contents().find( "#tinymce" ).text() != "" )
            content = $.trim( $this.find( "#survey-cta-content_ifr" ).contents().find( "#tinymce" ).html() ); 
        
        if ( title )
            data.title = title;
        else
            errors.error_messages.push( survey_cta_params.i18n_cta_title_empty );

        if ( content )
            data.content = content;
        else
            errors.error_messages.push( survey_cta_params.i18n_cta_content_empty );

        return $( this );

    } );

    $save_survey_cta.on( 'click' , function( event , options_processing_stat ) {

        var data   = {},
            errors = { error_messages : [] };

        $survey_cta_metabox
            .trigger( 'processing_mode' )
            .trigger( 'construct_data' , [ data , errors ] );
        
        if ( errors.error_messages.length ) {
            
            var err_msg = '<strong>' + survey_cta_params.i18n_please_fill_cta_form_properly + '</strong><br>';

            for ( var i = 0 ; i < errors.error_messages.length ; i++ )
                err_msg += errors.error_messages[ i ] + '<br>';
            
            vex.dialog.alert( err_msg );

            $survey_cta_metabox.trigger( 'normal_mode' );

            if ( options_processing_stat )
                options_processing_stat.survey_cta_processing_status.process_complete = true;

        } else {

            if ( options_processing_stat )
                options_processing_stat.survey_cta_processing_status.has_errors = false;

            $.ajax( {
                url      : ajaxurl,
                type     : 'POST',
                data     : { action : 'as_survey_save_survey_cta' , survey_id : survey_id , data : data },
                dataType : 'json'
            } )
            .done( function( data , text_status , jqxhr ) {

                if ( !options_processing_stat ) {

                    if ( data.status == 'success' )
                        vex.dialog.alert( survey_cta_params.i18n_survey_cta_save_success );
                    else {

                        console.log( data );
                        vex.dialog.alert( data.error_message );

                    }

                }

            } )
            .fail( function( jqxhr , text_status , error_thrown ) {

                if ( !options_processing_stat ) {

                    console.log( jqxhr );
                    vex.dialog.alert( survey_cta_params.i18n_survey_cta_save_fail );

                }

            } )
            .always( function() {

                $survey_cta_metabox.trigger( 'normal_mode' );

                if ( options_processing_stat )
                    options_processing_stat.survey_cta_processing_status.process_complete = true;

            } );

        }

    } );

} );