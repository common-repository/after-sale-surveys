<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post; ?>

<div id="survey-response-data-meta-box" class="as-survey-meta-box">

    <div id="survey-response-data-meta" style="display: none !important;">
        <span class="response-id"><?php echo $post->ID; ?></span>
        <?php do_action( 'as_survey_response_data_meta' ); ?>
    </div>

    <?php do_action( 'as_survey_before_survey_response_container' ); ?>

    <div id="survey-response-data-container">

        <?php do_action( 'as_survey_before_survey_response_data_header' ); ?>

        <header id="survey-response-data-header">
            <p><b><?php _e( 'Survey ID' , 'after-sale-surveys' ); ?></b> : <a href="<?php echo home_url( "/wp-admin/post.php?post=" . $survey_id . "&action=edit" ); ?>" target="_blank"><?php echo $survey_id ?></a></p>
            <p><b><?php _e( 'Survey Title' , 'after-sale-surveys' ); ?></b> : <?php echo get_the_title( $survey_id ); ?></p>
        </header>

        <?php do_action( 'as_survey_after_survey_response_data_header' ); ?>

        <div id="survey-responses">

            <h2><?php _e( 'Response Data' , 'after-sale-surveys' ); ?></h2>

            <?php foreach ( $survey_response_data as $page_number => $response_data ) {

                foreach ( $response_data as $order_number => $response ) {

                    if ( $survey_questions[ $page_number ][ $order_number ][ 'question-type' ] == 'multiple-choice-single-answer' ) { ?>

                        <div class="response-item">
                            <p class="question"><span class="label"><?php _e( 'Question:' , 'after-sale-surveys' ); ?></span> <?php echo $survey_questions[ $page_number ][ $order_number ][ 'question-text' ]; ?></p>
                            <p class="answer"><span class="label"><?php _e( 'Answer:' , 'after-sale-surveys' ); ?></span> <?php echo $response[ 'answer' ]; ?></p>
                        </div>

                    <?php }

                    do_action( 'as_survey_print_survey_' . $survey_questions[ $page_number ][ $order_number ][ 'question-type' ] . '_question_response_item' , $page_number , $order_number , $response , $survey_questions , $survey_response_data ); ?>

                <?php }

            } ?>

        </div><!--#survey-responses-->

    </div><!-- #survey-response-data-container -->

    <?php do_action( 'as_survey_after_survey_response_container' ); ?>

</div><!--#survey-response-data-meta-box-->
