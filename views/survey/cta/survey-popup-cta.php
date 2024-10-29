<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="after-sale-survey-cta" data-survey-id="<?php echo $survey_id; ?>" data-order-id="<?php echo $order_id; ?>">

    <?php do_action( 'as_survey_before_survey_cta' ); ?>

    <header class="cta-header">
        <?php do_action( 'as_survey_before_survey_cta_header_content' ); ?>
        <h2><?php echo $survey_cta_title; ?></h2>
        <?php do_action( 'as_survey_after_survey_cta_header_content' ); ?>
    </header>

    <?php do_action( 'as_survey_before_survey_cta_body' ); ?>

    <div class="cta-body">
        <?php do_action( 'as_survey_before_survey_cta_body_content' ); ?>
        <?php echo apply_filters( 'the_content' , $survey_cta_content ); ?>
        <?php do_action( 'as_survey_after_survey_cta_body_content' ); ?>
    </div>

    <?php do_action( 'as_survey_after_survey_cta_body' ); ?>

    <footer class="cta-controls">
        <?php do_action( 'as_survey_before_survey_cta_controls_content' ); ?>
        <input type="button" class="do-survey-btn btn btn-primary" value="<?php echo apply_filters( 'as_survey_cta_pop_up_accept_survey_button_text' , __( 'Sure!' , 'after-sale-surveys' ) ); ?>">
        <?php do_action( 'as_survey_after_survey_cta_controls_content' ); ?>
    </footer>

    <?php do_action( 'as_survey_after_survey_cta' ); ?>

</div><!--.after-sale-survey-cta-->