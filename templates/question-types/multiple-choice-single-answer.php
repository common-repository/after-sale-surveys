<?php
/**
 * Multiple choice single answer question
 *
 * This template can be overridden by copying it to yourtheme/after-sale-surveys/question-types/multiple-choice-single-answer.php
 *
 * NOTE: After Sale Surveys will need to update template files from time to time for improvements.
 * We try our best to do this as little as possible. On instances where this happens, You will need
 * to copy the updated template files to maintain compatibility.
 * You may need to re-apply or adjust your custom modifications for the overridden template/s.
 *
 * @template multiple-choice-single-answer
 * @see 	 http://docs.marketingsuite.com/after-sale-surveys/documentation/template-structure/
 * @author   Rymera Web Co.
 * @package  AfterSaleSurveys/Templates
 * @version  1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="survey-question <?php echo $question[ 'question-type' ]; ?>" data-question-type="<?php echo $question[ 'question-type' ]; ?>" data-survey-id="<?php echo $survey_id; ?>" data-page-number="<?php echo $page_number; ?>" data-order-number="<?php echo $order_number; ?>" data-required="<?php echo $question[ 'required' ]; ?>">

    <?php do_action( 'as_survey_before_' . $question[ 'question-type' ] . '_question' ); ?>

    <p class="question-text-container"><span class="order-number"><?php echo $order_number; ?></span>. <span class="question-text"><?php echo $question[ 'question-text' ]; ?></span> <?php echo $question[ 'required' ] == 'yes' ? '<span class="required">*</span>' : ''; ?></p>

    <?php if ( !isset( $question[ 'responses' ][ 'multiple-choices' ] ) ) { ?>

        <p class="err"><?php _e( 'This question has no choices.' , 'after-sale-surveys' ); ?></p>

    <?php } else { ?>

        <ul class="multiple-choices">

            <?php $question_identifier = 'queston_' . $page_number . '_' . $order_number;
            foreach( $question[ 'responses' ][ 'multiple-choices' ] as $choice ) { ?>

                <li>
                    <input type="radio" name="<?php echo $question_identifier; ?>" value="<?php echo $choice; ?>">
                    <label><?php echo $choice; ?></label>
                </li>

            <?php } ?>
            
        </ul>

    <?php } ?>

    <?php do_action( 'as_survey_after_' . $question[ 'question-type' ] . '_question' ); ?>

</div>
