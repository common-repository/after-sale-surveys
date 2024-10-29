<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<!--TODO: Add Hooks-->
<div id="general-report" class="after-sale-survey-report woocommerce-reports-wide">

    <div class="postbox">

        <div class="inside chart-with-sidebar">

            <div id="report-sidebar" class="chart-sidebar">

                <ul id="report-filters" class="chart-widgets">

                    <?php do_action( 'as_survey_before_survey_report_filters' ); ?>

                    <li id="survey-filter" class="report-filter chart-widget">

                        <h4><?php _e( 'Survey' , 'after-sale-survey' ); ?></h4>

                        <form>
                            <select id="as-survey" data-placeholder="<?php _e( 'Choose a survey...' , 'after-sale-survey' ); ?>" autocomplete="off">

                                <option value=""></option>
                                
                                <?php foreach ( $surveys as $survey ) { ?>

                                    <option value="<?php echo $survey->ID; ?>"><?php echo $survey->post_title; ?></option>

                                <?php } ?>

                            </select>
                        </form>

                    </li>

                    <li id="date-range-filter" class="report-filter chart-widget">

                        <h4><?php _e( 'Date Range' , 'after-sale-surveys' ); ?></h4>

                        <form>
                            <input type="text" id="from-date" class="date-field" autocomplete="off" placeholder="<?php _e( 'From' , 'after-sale-surveys' ); ?>">

                            <input type="text" id="to-date" class="date-field" autocomplete="off" placeholder="<?php _e( 'To' , 'after-sale-surveys' ); ?>">
                        </form>

                    </li>

                    <?php do_action( 'as_survey_after_survey_report_filters' ); ?>

                </ul>

                <div id="report-filter-controls">

                    <?php do_action( 'as_survey_before_survey_report_controls' ); ?>

                    <input type="button" id="generate-report-btn" class="button button-primary" value="<?php _e( "Generate Report" , "after-sale-surveys" ); ?>">

                    <?php do_action( 'as_survey_after_survey_report_controls' ); ?>

                    <span class="spinner"></span>

                </div>

            </div>

            <div id="report-main" class="main">

                <div id="report-data">

                    <ul>
                        <?php do_action( 'as_survey_before_survey_report_data_tabs_nav' , 'after-sale-surveys' ); ?>
                        <li><a href="#responses-container"><?php _e( 'Responses' , 'after-sale-surveys' ); ?></a></li>
                        <li><a href="#participants-container"><?php _e( 'Participants' , 'after-sale-surveys' ); ?></a></li>
                        <?php do_action( 'as_survey_after_survey_report_data_tabs_nav' , 'after-sale-surveys' ); ?>
                    </ul>

                    <?php do_action( 'as_survey_before_survey_report_data_tabs_section' , 'after-sale-surveys' ); ?>

                    <div id="responses-container">
                        <p class="chart-prompt"><?php _e( '&larr; Choose a survey to view stats' , 'after-sale-surveys' ); ?></p>
                    </div>

                    <div id="participants-container">
                        <p class="chart-prompt"><?php _e( '&larr; Choose a survey to view stats' , 'after-sale-surveys' ); ?></p>
                    </div>

                    <?php do_action( 'as_survey_after_survey_report_data_tabs_section' , 'after-sale-surveys' ); ?>

                </div>

            </div>

        </div>

    </div>

</div><!--#general-report-->
