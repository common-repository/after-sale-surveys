<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post; ?>

<div id="survey-cta-meta-box" >

    <div class="meta" style="display: none !important;">
        <span class="survey-id"><?php echo $post->ID; ?></span>
    </div>

    <h3><?php _e( 'Popup Call To Action' , 'after-sale-surveys' ); ?></h3>

    <p class="desc"><?php _e( 'This section lets you configure a short message intended to entice customers into filling in your survey. We find that short, snappy and to the point messages work well here and will result in the greatest number of customers filling in your survey.' , 'after-sale-surveys' ); ?></p>

    <div class="field-set">
        <label for=""><?php _e( 'Title' , 'after-sale-surveys' ); ?></label>

        <input type="text" id="survey-cta-title" value="<?php echo $title; ?>" autocomplete="off">
    </div>

    <div class="field-set">
        <label><?php _e( 'Content' , 'after-sale-surveys' ); ?></label>

        <?php wp_editor( $content , 'survey-cta-content' , $editor_settings ); ?>
    </div>

    <div class="field-set button-field-set">
        <input type="button" id="save-survey-cta" class="button button-primary" value="<?php _e( 'Save' , 'after-sale-surveys' ); ?>">
        <span class="spinner"></span>
    </div>

</div><!--#survey-cta-meta-box-->