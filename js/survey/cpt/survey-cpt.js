jQuery( document ).ready( function( $ ) {

    /*
     |--------------------------------------------------------------------------
     | Save Survey Components
     | =========================================================================
     | Whenever the cpt entry "publish", "update" or "save draft" button is clicked
     | Save also the cpt entry ( survey ) components data
     |--------------------------------------------------------------------------
     */

    $( "body" ).on( "save_survey_components" , function( event , survey_components_processing_status ) {

        $( "#save-survey-cta" ).trigger( "click" , [ survey_components_processing_status ] );
        $( "#save-survey-thankyou" ).trigger( "click" , [ survey_components_processing_status ] );

    } );

    var survey_components_processing_status = null,
        submit_form = false;

    $( "#publishing-action #publish, #save-action #save-post" ).click( function ( e ) {

        var $this = $( this );

        if ( !submit_form ) {

            if ( survey_components_processing_status ) {

                // Check if all offer components have been processed
                var all_surveys_components_processed = true,
                    survey_components_has_errors     = false;
                for ( var offer_component_stat in survey_components_processing_status ) {

                    if ( survey_components_processing_status.hasOwnProperty( offer_component_stat ) ) {

                        all_surveys_components_processed = all_surveys_components_processed && survey_components_processing_status[ offer_component_stat ].process_complete;
                        survey_components_has_errors     = survey_components_has_errors || survey_components_processing_status[ offer_component_stat ].has_errors;

                    }

                }

                if ( all_surveys_components_processed ) {

                    $this.removeAttr( "disabled" );
                    $this.siblings( ".spinner" ).css( "visibility" , "hidden" );

                    if ( survey_components_has_errors ) {

                        survey_components_processing_status = null;
                        submit_form = false;
                        return false;

                    } else {

                        setTimeout( function() {

                            submit_form = true;
                            $this.trigger( "click" );

                        } , 250 );

                    }

                } else
                    setTimeout( function() { $this.trigger( "click" ); } , 250 );
                
            }

            if ( !survey_components_processing_status ) {

                // Initialize offer components processing status
                survey_components_processing_status = {
                    survey_cta_processing_status      : { process_complete : false , has_errors : true },
                    survey_thankyou_processing_status : { process_complete : false , has_errors : true }
                };

                // Allow external plugins extending this plugin to also initialize the processing status of the
                // offer components ( meta boxes ) that those plugins may have added.
                $( "body" ).trigger( "initialize_survey_components_processing_status" , [ survey_components_processing_status ] );

                // Trigger save offer components
                $( "body" ).trigger( "save_survey_components" , [ survey_components_processing_status ] );

                $this.attr( "disabled" , "disabled" );
                $this.siblings( ".spinner" ).css( "visibility" , "visible" );

                setTimeout( function() { $this.trigger( "click" ); } , 250 );

            }

            return false;

        }

        // If code reaches here, its submits the form

    } );

} );
