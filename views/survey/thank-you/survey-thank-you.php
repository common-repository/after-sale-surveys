<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="after-sale-survey-thank-you" data-survey-id="<?php echo $survey_id; ?>" data-order-id="<?php echo $order_id; ?>">

    <?php do_action( 'as_survey_before_survey_thank_you' ); ?>

    <header class="thank-you-header">
        <?php do_action( 'as_survey_before_survey_thank_you_header_content' ); ?>
        <h2><?php echo $survey_thank_you_title; ?></h2>
        <?php do_action( 'as_survey_after_survey_thank_you_header_content' ); ?>
    </header>

    <div class="thank-you-body">
        <?php do_action( 'as_survey_before_survey_thank_you_body_content' ); ?>
        <p><?php echo apply_filters( 'the_content' , $survey_thank_you_content ); ?></p>
        <?php do_action( 'as_survey_after_survey_thank_you_body_content' ); ?>
    </div>

    <div class="thank-you-footer">
        <?php do_action( 'as_survey_before_survey_thank_you_controls_content' ); ?>
        <input type="button" class="close-thank-you-btn btn btn-primary" value="<?php echo apply_filters( 'as_survey_thank_you_pop_up_close_button_text' , __( 'Close' , 'after-sale-surveys' ) ); ?>">
        <?php do_action( 'as_survey_after_survey_thank_you_controls_content' ); ?>
    </div>

    <?php do_action( 'as_survey_after_survey_thank_you' ); ?>

</div><!--.after-sale-survey-thank-you-->