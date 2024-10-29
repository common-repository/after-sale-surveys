<?php
/**
 * Survey heading.
 *
 * This template can be overridden by copying it to yourtheme/after-sale-surveys/survey/survey-heading.php.
 *
 * NOTE: After Sale Surveys will need to update template files from time to time for improvements.
 * We try our best to do this as little as possible. On instances where this happens, You will need
 * to copy the updated template files to maintain compatibility.
 * You may need to re-apply or adjust your custom modifications for the overridden template/s.
 *
 * @template survey-heading
 * @see 	 http://docs.marketingsuite.com/after-sale-surveys/documentation/template-structure/
 * @author   Rymera Web Co.
 * @package  AfterSaleSurveys/Templates
 * @version  1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

do_action( 'as_survey_before_survey_heading' ); ?>

<header class="survey-heading">
    <h2 class="survey-title"><?php echo $survey_post->post_title; ?></h2>
    <div class="survey-description">
        <?php echo apply_filters( 'the_content' , $survey_post->post_content ); ?>
    </div>
</header>

<?php do_action( 'as_survey_after_survey_heading' ); ?>