<?php
/**
 * Survey submission controls.
 *
 * This template can be overridden by copying it to yourtheme/after-sale-surveys/survey/survey-submission-controls.php.
 *
 * NOTE: After Sale Surveys will need to update template files from time to time for improvements.
 * We try our best to do this as little as possible. On instances where this happens, You will need
 * to copy the updated template files to maintain compatibility.
 * You may need to re-apply or adjust your custom modifications for the overridden template/s.
 *
 * @template survey-submission-controls
 * @see 	 http://docs.marketingsuite.com/after-sale-surveys/documentation/template-structure/
 * @author   Rymera Web Co.
 * @package  AfterSaleSurveys/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'as_survey_before_survey_submission_controls_container' ); ?>

<div class="survey-submission-controls">

    <?php do_action( 'as_survey_before_survey_submission_controls' ); ?>

    <input type="button" class="submit-survey-btn btn btn-primary" value="<?php echo apply_filters( 'as_survey_submit_survey_response_button_text' , __( 'Submit Survey' , 'after-sale-surveys' ) ); ?>">

    <?php do_action( 'as_survey_after_survey_submission_controls' ); ?>

</div>

<?php do_action( 'as_survey_after_survey_submission_controls_container' ); ?>
