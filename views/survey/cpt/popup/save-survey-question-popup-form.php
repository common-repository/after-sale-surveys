<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div id="save-question-popup-form" class="white-popup mfp-hide <?php echo $read_only; ?>">

    <header>
        <h2 class="popup-title"></h2>
    </header>

    <div id="pop-up-processing-markup">
        <p><span class="spinner"></span><span class="processing-message"></span></p>
    </div>

    <div id="pop-up-form-markup">

        <div class="meta" style="display: none !important;">
            <span class="page-number"></span>
            <span class="order-number"></span>
        </div>

        <div class="form">

            <?php do_action( 'as_survey_before_add_question_fields' ); ?>

            <div class="field-set">
                <label for="order-number"><?php _e( 'Sort Order' , 'after-sale-surveys' ); ?></label>
                <input type="text" id="order-number" autocomplete="off">
            </div>

            <div class="field-set">
                <label for="required-question">
                <input type="checkbox" id="required-question" autocomplete="off">
                <?php _e( 'Required' , 'after-sale-surveys' ); ?>
                </label>
            </div>

            <div class="field-set">
                <label for="question-text"><?php _e( 'Question' , 'after-sale-surveys' ); ?></label>
                <input type="text" id="question-text" autocomplete="off">
            </div>

            <div class="field-set">
                <label for="question-type"><?php _e( 'Question Type' , 'after-sale-surveys' ); ?></label>
                <select id="question-type" autocomplete="off">
                    <option value=""><?php _e( '-- Select Question Type --' , 'after-sale-surveys' ); ?></option>
                    <?php foreach ( $question_types as $key => $text ) { ?>
                        <option value="<?php echo $key; ?>"><?php echo $text; ?></option>
                    <?php } ?>

                    <?php $show_teaser_questions = apply_filters( 'as_survey_show_teaser_questions' , true );

                    if ( $show_teaser_questions ) { ?>
                        
                        <option disabled value="multiple-choice-multiple-answer"><?php _e( 'Multiple Choice Multiple Answers (PREMIUM)' , 'after-sale-surveys' ); ?></option>
                        <option disabled value="single-line-text"><?php _e( 'Single Line Text (PREMIUM)' , 'after-sale-surveys' ); ?></option>
                        <option disabled value="paragraph-text"><?php _e( 'Paragraph Text (PREMIUM)' , 'after-sale-surveys' ); ?></option>
                        <option disabled value="drowpdown-single-answer"><?php _e( 'Dropdown Single Answer (PREMIUM)' , 'after-sale-surveys' ); ?></option>

                    <?php } ?>

                </select>
            </div>

            <?php do_action( 'as_survey_after_add_question_fields' ); ?>

        </div>

        <div id="survey-response-container">

            <div id="choices-section" class="question-response">

                <header id="choices-controls" class="cf">
                    <label for="choice-text"><?php _e( 'Enter a new choice' , 'after-sale-surveys' ); ?></label>
                    <input type="text" id="choice-text">
                    <input type="button" id="add-choice-btn" class="button button-primary" value="<?php _e( '+ Add choice' , 'after-sale-surveys' ); ?>">

                    <input type="button" id="edit-choice-btn" class="button button-primary" value="<?php _e( 'Edit choice' , 'after-sale-surveys' ); ?>">
                    <input type="button" id="cancel-edit-choice-btn" class="button button-secondary" value="<?php _e( 'Cancel' , 'after-sale-surveys' ); ?>" >
                </header>

                <h4 id="choices-list-heading"><?php _e( 'Choices' , 'after-sale-surveys' ); ?></h4>
                <div id="choices-list-container">
                    <ul id="choices-list"></ul>
                </div>

            </div><!--#choices-section-->

            <?php do_action( 'as_survey_print_additional_question_responses' ); ?>

        </div>

        <?php if ( $read_only ) { ?>

            <div class="question-popup-notice">
                <p class="desc"><?php _e( '<b>Note:</b> You cannot add or edit questions on this survey because it already has responses.' , 'after-sale-surveys-premium' ); ?></p>
            </div>

        <?php } ?>

        <footer>
            <span class="spinner"></span>
            <input type="button" id="save-survey-question-btn" class="button button-primary" value="<?php _e( 'Save Question' , 'after-sale-surveys' ); ?>" >
        </footer>

    </div><!--#pop-up-form-markup-->

</div>