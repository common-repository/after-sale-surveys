<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post; ?>

<div id="survey-thankyou-meta-box">

    <div class="meta" style="display: none !important;">
        <span class="survey-id"><?php echo $post->ID; ?></span>
    </div>

    <h3><?php _e( 'Thank You Message' , 'after-sale-surveys' ); ?></h3>

    <p class="desc"><?php _e( 'The thank you message is shown to customers after they successfully fill in the survey. Thank them for their participation and if you like you can even add little extras such as a coupon code for their next order.' , 'after-sale-surveys' ); ?></p>

    <div class="field-set">
        <label for=""><?php _e( 'Title' , 'after-sale-surveys' ); ?></label>

        <input type="text" id="survey-thankyou-title" value="<?php echo $title; ?>" autocomplete="off">
    </div>

    <div class="field-set">
        <label><?php _e( 'Content' , 'after-sale-surveys' ); ?></label>

        <?php wp_editor( $content , 'survey-thankyou-content' , $editor_settings ); ?>
    </div>

    <div class="field-set button-field-set">
        <input type="button" id="save-survey-thankyou" class="button button-primary" value="<?php _e( 'Save' , 'after-sale-surveys' ); ?>">
        <span class="spinner"></span>
    </div>

</div><!--#survey-thankyou-meta-box-->