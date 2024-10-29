/* global jQuery */
jQuery( document ).ready( function( $ ) {

    /*
     |--------------------------------------------------------------------------
     | Variable Declaration
     |--------------------------------------------------------------------------
     */

    var $general_report         = $( "#general-report" ),
        $report_filters         = $general_report.find( "#report-filters" ),
        $report_filter_controls = $general_report.find( "#report-filter-controls" ),
        $generate_report_btn    = $general_report.find( "#generate-report-btn" ),
        $report_main            = $( "#report-main" ),
        $responses_container    = $( "#responses-container" ),
        $participants_container = $( "#participants-container" );
    
    
    
    
    /*
     |--------------------------------------------------------------------------
     | Render Survey Response Report
     |--------------------------------------------------------------------------
     */

    $generate_report_btn.on( 'validate_report_filter' , function( event , survey_id , $filter_field , error_filters ) {

        if ( $filter_field.attr( 'id' ) == 'survey-filter' ) {

            var survey_id = $.trim( $filter_field.find( "#as-survey" ).val() );

            if ( survey_id == '' )
                error_filters.push( "Please choose a survey to view stats" ); // TODO: Internationalize

            $filter_field.find( "#as-survey" ).val( survey_id );
            $filter_field.addClass( 'err' );

        }

    } );

    $generate_report_btn.on( 'construct_report_filter' , function( event , survey_id , $filter_field , filters ) {

        if ( $filter_field.attr( 'id' ) == 'date-range-filter' ) {

            filters.push( {
                'filter_type' : 'date_range',
                'from_date'   : $.trim( $filter_field.find( "#from-date" ).val() ),
                'to_date'     : $.trim( $filter_field.find( "#to-date" ).val() )
            } );

        }

    } );

    $responses_container.on( 'render_question_response_report_chart' , function( event , survey_id , response_data_report ) {

        var question_type = response_data_report[ 'question-type' ];

        if ( question_type == 'multiple-choice-single-answer' ) {

            // TODO: Make it open for extensibility, ex. like if they want to change to display percentage instead of value
            // TODO: or make the pie chart 3D, etc..

            var page_number        = response_data_report[ 'page-number' ],
                order_number       = response_data_report[ 'order-number' ],
                question_text      = response_data_report[ 'question-text' ],
                chart_div          = document.getElementById( 'chart_' + survey_id + '_' + page_number + '_' + order_number ),
                choices_report     = response_data_report[ 'multiple-choices' ],
                choices_report_arr = [];

            for ( var k in choices_report )
                if ( choices_report.hasOwnProperty( k ) )
                    choices_report_arr.push( [ k , choices_report[ k ] ] );

            // Create the data table.
            var options = {
                    'title'                    : question_text,
                    'titleTextStyle'           : { fontSize : 12 },
                    'width'                    : 400,
                    'height'                   : 300,
                    'pieSliceText'             : 'value',
                    'sliceVisibilityThreshold' : 0
                },
                data = new google.visualization.DataTable();

            data.addColumn( 'string' , 'Choice' );
            data.addColumn( 'number' , 'Total Responses' );
            data.addRows( choices_report_arr );

            var chart = new google.visualization.PieChart( chart_div );
            chart.draw( data , options );

        }

    } );

    $participants_container.on( 'render_survey_responses_table_report' , function( event , survey_id , table_data ) {

        var table_markup_heading = '<thead>' +
                                        '<tr>' +
                                            '<th>' + survey_response_general_report_params.i18n_order_no + '</th>' +
                                            '<th>' + survey_response_general_report_params.i18n_date + '</th>' +
                                            '<th>' + survey_response_general_report_params.i18n_user + '</th>' +
                                            '<th>' + survey_response_general_report_params.i18n_response_details + '</th>' +
                                        '</tr>' +
                                    '</thead>';

        $participants_container.trigger( 'generate_survey_responses_table_report_heading' , [ survey_id , table_data , table_markup_heading ] );

        var table_markup = '<table id="survey-response-table-report" class="wp-list-table widefat fixed striped posts">' +
                                table_markup_heading +
                            '<tbody id="the-list">';

        for ( var key in table_data ) {

            if ( table_data.hasOwnProperty( key ) ) {

                var user_data_markup = '';

                if ( table_data[ key ][ 'user_url' ] != "" )
                    user_data_markup = '<a href="' + table_data[ key ][ 'user_url' ] + '" target="_blank">' + table_data[ key ][ 'user_fullname' ] + '</a>';
                else
                    user_data_markup = table_data[ key ][ 'user_fullname' ];

                var table_entry = '<tr>' +
                                    '<td><a href="' + table_data[ key ][ 'order_url' ] + '" target="_blank">' + table_data[ key ][ 'order_id' ] + '</a></td>' +
                                    '<td>' + table_data[ key ][ 'response_date' ] + '</td>' +
                                    '<td>' + user_data_markup + '</td>' +
                                    '<td><a href="' + table_data[ key ][ 'response_url' ] + '" target="_blank">' + survey_response_general_report_params.i18n_details + '</a></td>' +
                                '</tr>';

                $participants_container.on( 'generate_survey_responses_table_report_entry' , [ survey_id , table_data , key , table_entry ] );

                table_markup += table_entry;

            }

        }

        table_markup += '</tbody></table>';

        $participants_container.html( table_markup );

    } );

    function initialize() {

        $generate_report_btn.click( function() {

            var $this = $( this );
            render_survey_report( $this );

        } );

    }

    function render_survey_report() {

        $report_main.find( ".survey-title" ).remove();
        $report_main.find( ".total-responses" ).remove();

        $responses_container.html( '<p class="chart-prompt">' + survey_response_general_report_params.i18n_generating_reports + '</p>' );
        $participants_container.html( '<p class="chart-prompt">' + survey_response_general_report_params.i18n_generating_reports + '</p>' );

        $report_filter_controls
            .find( ".button" ).attr( 'disabled' , 'disabled' ).end()
            .find( ".spinner" ).css( 'visibility' , 'visible' );

        // Validate report filters
        var error_filters = [];

        $report_filters.find( ".report-filter" ).each( function() {

            var $filter_field = $( this );
            $filter_field.removeClass( "err" );
            $generate_report_btn.trigger( "validate_report_filter" , [ survey_id , $filter_field , error_filters ] );

        } );

        if ( error_filters.length > 0 ) {

            var err_msg = "Report filter not filled up properly<br/>"; // TODO: Internationalize

            for ( var i = 0 ; i < error_filters.length ; i++ )
                err_msg += error_filters[ i ] + "<br/>";

            vex.dialog.alert( err_msg );

            $responses_container.html( '<p class="chart-prompt">' + survey_response_general_report_params.i18n_view_survey_stats + '</p>' );
            $participants_container.html( '<p class="chart-prompt">' + survey_response_general_report_params.i18n_view_survey_stats + '</p>' );

            $report_filter_controls
                .find( ".button" ).removeAttr( 'disabled' ).end()
                .find( ".spinner" ).css( 'visibility' , 'hidden' );

            return false;

        }

        // Construct report filters
        var survey_id = parseInt( $.trim( $general_report.find( "#as-survey" ).val() ) , 10 ),
            filters   = [];

        $report_filters.find( ".report-filter" ).each( function() {

            var $filter_field = $( this );
            $generate_report_btn.trigger( "construct_report_filter" , [ survey_id , $filter_field , filters ] );

        } );

        $generate_report_btn.trigger( "generate_report" , [ survey_id , filters ] );

        // get survey response report data
        as_survey_report_ajax_services.get_survey_responses_report( survey_id , filters )
            .done( function( data , text_status , jqxhr ) {

                if ( data.status == 'success' ) {

                    $generate_report_btn.trigger( 'success_retrieving_responses_report_data' , [ data , survey_id ] );

                    // Get survey response report data ( for table )
                    as_survey_report_ajax_services.get_survey_responses_list( survey_id , filters )
                        .done( function( data , text_status , jqxhr ) {
                            
                            if ( data.status == 'success' ) {

                                $generate_report_btn.trigger( 'success_retrieving_participants_report_data' , [ data , survey_id ] );

                                $participants_container.trigger( "render_survey_responses_table_report" , [ survey_id , data.table_report_data ] );

                            } else {

                                $generate_report_btn.trigger( 'fail_retrieving_participants_report_data' , [ data , survey_id ] );

                                vex.dialog.alert( data.error_message );
                                $participants_container.html( '<p class="chart-prompt">' + survey_response_general_report_params.i18n_view_survey_stats + '</p>' );

                            }

                        } )
                        .fail( function( jqxhr , text_status , error_thrown ) {

                            $generate_report_btn.trigger( 'error_retrieving_participants_report_data' , [ jqxhr , survey_id ] );

                            // TODO: Handle error
                            console.log( jqxhr );
                            $participants_container.html( '<p class="chart-prompt">' + survey_response_general_report_params.i18n_view_survey_stats + '</p>' );

                        } )
                        .always( function() {

                            $generate_report_btn.trigger( 'always_retrieving_participants_report_data' );

                            $report_filter_controls
                                .find( ".button" ).removeAttr( 'disabled' ).end()
                                .find( ".spinner" ).css( 'visibility' , 'hidden' );

                        } );

                    var response_data_report = data.response_data_report;
                    $responses_container.empty();

                    // The purpose of this chunk of code is to add div placeholders for which the various charts are going to be rendered.
                    // We "MUST" do it this way, otherwise there is a funny rendering bug on the charts.
                    // Ex. when rendering a chart on a newly created dom element that isn't attached yet to the dom itself.
                    for ( var key in response_data_report ) {

                        if ( response_data_report.hasOwnProperty( key ) ) {

                            var question_type = response_data_report[ key ][ 'question-type' ],
                                page_number   = response_data_report[ key ][ 'page-number' ],
                                order_number  = response_data_report[ key ][ 'order-number' ];

                            $responses_container.append( '<div id="chart_' + survey_id + '_' + page_number + '_' + order_number + '" data-survey-id="' + survey_id + '" data-page-number="' + page_number + '" data-order-number="' + order_number + '" data-question-type="' + question_type + '" class="chart-div"></div>' );

                        }

                    }

                    $report_main.prepend( '<p class="total-responses"><b>' + survey_response_general_report_params.i18n_total_responses + '</b> <span class="total">' + data.total_responses + '</span></p>' );
                    $report_main.prepend( '<h2 class="survey-title"><b>' + survey_response_general_report_params.i18n_survey + '</b> ' + $general_report.find( "#as-survey" ).find( "option[value='" + survey_id + "']" ).text() + '</h2>' );

                    for ( var key in response_data_report )
                        if ( response_data_report.hasOwnProperty( key ) )
                            $responses_container.trigger( 'render_question_response_report_chart' , [ survey_id , response_data_report[ key ] ] );

                } else {

                    $generate_report_btn.trigger( 'fail_retrieving_responses_report_data' , [ data , survey_id ] );

                    vex.dialog.alert( data.error_message );

                    $responses_container.html( '<p class="chart-prompt">' + survey_response_general_report_params.i18n_view_survey_stats + '</p>' );
                    $participants_container.html( '<p class="chart-prompt">' + survey_response_general_report_params.i18n_view_survey_stats + '</p>' );

                    $report_filter_controls
                        .find( ".button" ).removeAttr( 'disabled' ).end()
                        .find( ".spinner" ).css( 'visibility' , 'hidden' );

                }

            } )
            .fail( function( jqxhr , text_status , error_thrown ) {

                $generate_report_btn.trigger( 'error_retrieving_responses_report_data' , [ jqxhr , survey_id ] );

                // TODO: Handle error
                console.log( jqxhr );

                $responses_container.html( '<p class="chart-prompt">' + survey_response_general_report_params.i18n_view_survey_stats + '</p>' );
                $participants_container.html( '<p class="chart-prompt">' + survey_response_general_report_params.i18n_view_survey_stats + '</p>' );

                $report_filter_controls
                    .find( ".button" ).removeAttr( 'disabled' ).end()
                    .find( ".spinner" ).css( 'visibility' , 'hidden' );

            } )
            .always( function() {

                $generate_report_btn.trigger( 'always_retrieving_responses_report_data' );                

            } );

    }




    /*
     |--------------------------------------------------------------------------
     | Initialization
     |--------------------------------------------------------------------------
     */

    // Initialize google charts
    google.charts.load( 'current' , { packages : [ 'corechart' , 'table' ] } );
    google.charts.setOnLoadCallback( initialize );

    // Initialize report data tabs
    $report_main.find( "#report-data" ).tabs();

    // Initialize report filters
    $general_report.find( "#as-survey" ).chosen( { allow_single_deselect : true } );

    $report_filters.find( "#from-date" ).datepicker( {
        defaultDate: "+1w",
        showWeek: true,
        changeMonth: true,
        changeYear: true,
        onClose: function( selectedDate ) {
            $report_filters.find( "#to-date" ).datepicker( "option" , "minDate" , selectedDate );
        }
    } );

    $report_filters.find( "#to-date" ).datepicker( {
        defaultDate: "+1w",
        showWeek: true,
        changeMonth: true,
        changeYear: true,
        onClose: function( selectedDate ) {
            $report_filters.find( "#from-date" ).datepicker( "option" , "maxDate" , selectedDate );
        }
    } ) ;

    // Initialize Vex Library
    vex.defaultOptions.className = 'vex-theme-plain';

} );
