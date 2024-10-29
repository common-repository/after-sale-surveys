// ajaxurl : Provided by WordPress when script is loaded via admin_enqueue_script
var as_survey_report_ajax_services = function() {

    var get_survey_responses_report = function( survey_id , filters ) {

            return jQuery.ajax( {
                url      : ajaxurl,
                type     : 'POST',
                data     : { action : 'as_survey_get_survey_responses_report' , survey_id : survey_id , filters : filters },
                dataType : 'json'
            } );

        },
        get_survey_responses_list = function( survey_id , filters ) {

            return jQuery.ajax( {
                url      : ajaxurl,
                type     : 'POST',
                data     : { action : 'as_survey_get_survey_responses_list' , survey_id : survey_id , filters : filters },
                dataType : 'json'
            } );

        };

    return {
        get_survey_responses_report : get_survey_responses_report,
        get_survey_responses_list   : get_survey_responses_list
    };

}();