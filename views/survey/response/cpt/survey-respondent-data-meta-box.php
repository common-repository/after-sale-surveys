<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post; ?>

<div id="survey-respondent-data-meta-box" class="as-survey-meta-box">

    <div id="survey-respondent-data-meta" style="display: none !important;">
        <span class="response-id"><?php echo $post->ID; ?></span>
        <?php do_action( 'as_survey_respondent_data_meta' ); ?>
    </div>

    <div id="survey-respondent-data-container">

        <?php do_action( 'as_survey_before_respondent_data' ); ?>

        <div id="survey-respondent-data">

            <p class="respondent-data">
                <b>Order No.:</b> <a href="<?php echo home_url( "/wp-admin/post.php?post=" . $order_id . "&action=edit" ); ?>" target="_blank"><?php echo $order_id; ?></a>
            </p>

            <?php if (!$user_data ) { ?>

                <p class="respondent-data">
                    <b><?php _e( 'Customer Details (Guest Order):' , 'after-sale-surveys' ); ?></b><br>
                    <b><?php _e( 'Name:' , 'after-sale-surveys' ); ?></b> <?php echo  $order->get_formatted_billing_full_name(); ?>
                </p>

            <?php } else { ?>

                <p class="respondent-data">
                    <b><?php _e( 'Customer:' , 'after-sale-surveys' ); ?></b> <a href="<?php echo home_url( "/wp-admin/user-edit.php?user_id=" . $user_data->ID ); ?>" target="_blank"><?php echo $user_data->first_name; ?> <?php echo $user_data->last_name; ?></a>
                </p>

            <?php } ?>

            <?php if ( is_array( $user_additional_details ) && ! empty( $user_additional_details ) ) :
                     foreach ( $user_additional_details as $key => $value ) : ?>

                        <p class="respondent-ip-<?php echo $value; ?>">
                            <strong><?php echo $additional_details_labels[ $key ] . ':'; ?></strong>
                            <?php echo $value; ?>
                        </p>

            <?php    endforeach;
                  endif; ?>

        </div><!--#survey-respondent-data-->

        <?php do_action( 'as_survey_after_respondent_data' ); ?>

    </div><!-- #survey-respondent-data-container -->

</div><!--#survey-respondent-data-meta-box-->
