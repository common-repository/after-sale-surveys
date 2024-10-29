// ajaxurl : Provided by WordPress when script is loaded via admin_enqueue_script
var as_survey_questions_backend_ajax_services = function() {

    var get_new_question_order_number = function( survey_id , page_number ) {

            return jQuery.ajax( {
                url      : ajaxurl,
                type     : "POST",
                data     : { action : "as_survey_get_new_question_order_number" , survey_id : survey_id , page_number : page_number },
                dataType : "json"
            } );

        },
        load_survey_question_choices = function( survey_id , page_number , question_order_number ) {

            return jQuery.ajax( {
                url      : ajaxurl,
                type     : "POST",
                data     : { action : "as_survey_load_survey_question_choices" , survey_id : survey_id , page_number : page_number , question_order_number : question_order_number },
                dataType : "json"
            } );

        },
        get_question_data = function( survey_id , page_number , order_number ) {

            return jQuery.ajax( {
                url : ajaxurl,
                type : "POST",
                data : { action: "as_survey_get_question_data" , survey_id : survey_id , page_number: page_number , order_number : order_number }
            } );

        },
        save_survey_question = function( survey_id , question_data ) {

            return jQuery.ajax( {
                url : ajaxurl,
                type : "POST",
                data : { action : "as_survey_save_survey_question" , survey_id : survey_id , question_data : question_data },
                dataType : "json"
            } );

        },
        delete_survey_question = function( survey_id , page_number , order_number ) {

            return jQuery.ajax( {
                url : ajaxurl,
                type : "POST",
                data : { action : "as_survey_delete_survey_question" , survey_id : survey_id , page_number: page_number , order_number : order_number },
                dataType : "json"
            } );

        };

    return {
        get_new_question_order_number    : get_new_question_order_number,
        load_survey_question_choices : load_survey_question_choices,
        get_question_data            : get_question_data,
        save_survey_question         : save_survey_question,
        delete_survey_question       : delete_survey_question
    };

}();