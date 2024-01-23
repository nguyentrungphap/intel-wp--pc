<?php
/*
 * UnderConstructionPage PRO
 * Interface - Tab - Main
 * (c) Web factory Ltd, 2015 - 2017
 */

class UCP_tab_main extends UCP {
  static function display() {
    $options = self::get_options();
    $default_options = self::default_options();
    
    $http_response_codes = array(array('val' => '200 OK', 'label' => __('200 OK', 'under-construction-page')),
                             array('val' => '503 Service Unavailable', 'label' => __('503 Service Unavailable', 'under-construction-page')),
                             array('val' => '404 Not Found', 'label' => __('404 Not Found', 'under-construction-page')));

    echo '<div class="tab-content">';
    echo '<div data-placeholder="chart-placeholder.jpg" class="chart-container" style="position: relative; height:333px; width:1000px">';
    echo '<canvas id="ucp-chart" width="1000" height="333"></canvas>';
    echo '</div>';

    echo '<table class="form-table">';

    echo '<tr valign="top">
    <th scope="row"><label for="status">' . __('Under Construction Mode', 'under-construction-page') . '</label></th>
    <td>';
    UCP_utility::create_toogle_switch('status_toggle', array('saved_value' => $options['status'], 'option_key' => UCP_OPTIONS_KEY . '[status]'));
    echo '<p class="description">By enabling under construction mode users will not be able to access the site\'s content. They will only see the under construction page.<br>To set exceptions configure <a href="#" class="change_tab" data-tab="access" data-tab2="access_rules">access rules</a></p>';
    echo '<div id="status_wrapper" class="field_wrapper">';
    echo '<br><label for="http_response_code"><b>HTTP Response Code</b></label>';
    echo '<select name="' . UCP_OPTIONS_KEY . '[http_response_code]' . '" id="http_response_code">';
    UCP_utility::create_select_options($http_response_codes, $options['http_response_code']);
    echo '</select>';
    echo '<p class="description">503 response is used only on maintenance mode and under construction pages, when the site should not be indexed by search engines until it\'s done. All other scenarios - coming soon page, landing page, sales page - should use the 200 response. Use the 404 one only if you know what you\'re doing.</p>';
    echo '</div>'; // field_wrapper
    echo '</td></tr>';
    
    echo '<tr>
    <th scope="row"><label for="start_date_toggle">' . __('Automatic Start Date &amp; Time', 'under-construction-page') . '</label></th>
    <td>';
    UCP_utility::create_toogle_switch('start_date_toggle', array('saved_value' => (empty($options['start_date']) || $options['start_date'] == '0000-00-00 00:00')? 0: 1, 'option_key' => UCP_OPTIONS_KEY . '[start_date_toggle]'));
    echo '<div id="start_date_wrapper"><input id="start_date" type="text" class="datepicker" name="' . UCP_OPTIONS_KEY . '[start_date]" value="' . esc_attr($options['start_date']) . '" placeholder="yyyy-mm-dd hh:mm"><span title="' . __('Open date & time picker', 'under-construction-page') . '" alt="' . __('Open date & time picker', 'under-construction-page') . '" class="show-datepicker dashicons dashicons-calendar-alt"></span></div>';
    echo '<p class="description">If enabled, under construction mode will automatically start showing on the selected date.<br>
    This option will not "auto-enable" under construction mode - it has to be set to "On".</p>';
    echo '</td></tr>';
    
    echo '<tr>
    <th scope="row"><label for="end_date_toggle">' . __('Automatic End Date &amp; Time', 'under-construction-page') . '</label></th>
    <td>';
    UCP_utility::create_toogle_switch('end_date_toggle', array('saved_value' => (empty($options['end_date']) || $options['end_date'] == '0000-00-00 00:00')? 0: 1, 'option_key' => UCP_OPTIONS_KEY . '[end_date_toggle]'));
    echo '<div id="end_date_wrapper"><input data-earliest="now" id="end_date" type="text" class="datepicker" name="' . UCP_OPTIONS_KEY . '[end_date]" value="' . esc_attr($options['end_date']) . '" placeholder="yyyy-mm-dd hh:mm"><span title="' . __('Open date & time picker', 'under-construction-page') . '" alt="' . __('Open date & time picker', 'under-construction-page') . '" class="show-datepicker dashicons dashicons-calendar-alt"></span></div>';
    echo '<p class="description">If enabled, under construction mode will automatically stop showing on the selected date.<br>
    This option will not "auto-enable" under construction mode - it has to be set to "On".</p>';
    echo '</td></tr>';

    echo '</table>';
    echo '</div>';

    parent::footer_save_button();
  } // display
} // class UCP_tab_main