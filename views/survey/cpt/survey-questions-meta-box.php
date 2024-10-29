<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post; ?>

<div id="survey-question-meta-box" class="as-survey-meta-box <?php echo $read_only; ?>">

    <div id="survey-question-meta" style="display: none !important;">
        <span class="survey-id"><?php echo $post->ID; ?></span>
    </div>

    <?php if ( $read_only ) { ?>

        <div class="meta-box-notice">
            <p class="desc"><?php _e( '<b>Note:</b> You cannot add or edit questions on this survey because it already has responses.' , 'after-sale-surveys' ); ?></p>
        </div>

    <?php } ?>

    <div class="survey-question-controls">
        <?php do_action( 'as_survey_print_additional_question_controls' ); ?>
        <a href="#save-question-popup-form" id="open-save-question-popup-form" class="button button-primary"><?php _e( '+ Add Question' , 'after-sale-surveys' ); ?></a>
    </div>

    <div>
        <table id="survey-questions-table" class="wp-list-table widefat fixed striped" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <?php foreach ( $table_headings as $class => $text ) { ?>
                        <th class="<?php echo $class; ?>"><?php echo $text; ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <?php foreach ( $table_headings as $class => $text ) { ?>
                        <th class="<?php echo $class; ?>"><?php echo $text; ?></th>
                    <?php } ?>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="pop-boxes">

        <?php include_once ( $views_root_path . 'survey/cpt/popup/save-survey-question-popup-form.php' ); ?>

    </div>

</div><!--#survey-question-meta-box-->
