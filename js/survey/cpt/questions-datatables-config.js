// Available via window object globally
var as_survey_questions_datatable_handle = null,
    as_survey_questions_datatable_config = {
    "processing": true,
    "serverSide": true,
    "order": [ [ 0 , 'asc' ] ],
    "searching": false,
    //"dom": '<"top"lfp<"clear">><"#survey-questions-table-container" rt><"bottom"ip<"clear">>',
    "columnDefs": [
        {
            "targets"   : 0,
            "className" : "order-number"
        },
        {
            "targets"   : 1,
            "className" : "question-text"
        },
        {
            "targets"   : 2,
            "className" : "question-type"
        },
        {
            "targets"    : 3,
            "searchable" : false,
            "orderable"  : false,
            "className"  : "required"
        },
        {
            "targets"    : 4,
            "searchable" : false,
            "orderable"  : false,
            "className"  : "column-controls"
        }
    ],
    "preDrawCallback": function( settings ) {

        // Before draw
        jQuery( "#survey-questions-table" ).trigger( "retrieving_data_mode" );

    },
    "drawCallback": function( settings ) {

        // After draw
        jQuery( "#survey-questions-table" ).trigger( "normal_mode" );

    },
    "language": {
        "zeroRecords": questions_datatables_config_params.i18n_empty_survey
    },
    "ajax": {
        "url"      : ajaxurl, // Provided by WordPress when script is loaded via admin_enqueue_script
        "type"     : "POST",
        "data"     : { action : "as_survey_load_survey_questions_on_datatables" , survey_id : 0 }, // survey id of 0 just a placeholder, will overridden on initialization
        "dataType" : "json"
    }
};