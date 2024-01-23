<?php
/*
 * UnderConstructionPage PRO
 * Interface - Tab - Access
 * (c) Web factory Ltd, 2015 - 2017
 */

class UCP_tab_access extends UCP {
  static function display() {
    $tabs[] = array('id' => 'tab_access_rules', 'class' => 'tab-content', 'label' => __('Access Rules', 'under-construction-page'), 'callback' => array(__CLASS__, 'tab_rules'));
    $tabs[] = array('id' => 'tab_access_links', 'class' => 'tab-content', 'label' => __('Direct Access Links', 'under-construction-page'), 'callback' => array(__CLASS__, 'tab_links'));

    echo '<div id="tabs_access" class="ui-tabs ucp-tabs-2nd-level">';
    echo '<ul>';
    foreach ($tabs as $tab) {
      echo '<li><a href="#' . $tab['id'] . '">' . $tab['label'] . '</a></li>';
    }
    echo '</ul>';

    foreach ($tabs as $tab) {
      if(is_callable($tab['callback'])) {
        echo '<div style="display: none;" id="' . $tab['id'] . '" class="' . $tab['class'] . '">';
        call_user_func($tab['callback']);
        echo '</div>';
      }
    } // foreach
    echo '</div>'; // second level of tabs

    self::dialogs();
  } // display


  static function dialogs() {
    $link_expire_types = array(array('val' => '', 'label' => __('Link never expires (default)', 'under-construction-page')),
                             array('val' => 'date', 'label' => __('Link expires after set date', 'under-construction-page')),
                             array('val' => 'sessions', 'label' => __('Link expires after a set amount of sessions', 'under-construction-page')),
                             array('val' => 'ip', 'label' => __('Link expires after set amount of IPs have accessed it', 'under-construction-page')));

    echo '<div id="access-link-dialog" style="display: none;" title="UI Dialog"><span class="ui-helper-hidden-accessible"><input type="text"></span>';
    echo '<table class="form-table">';

    echo '<tr>';
    echo '<th><label for="link_name">Name / Description</label></th>';
    echo '<td><input type="text" value="" class="regular-text" name="link_name" id="link_name" placeholder="Preview link for client">';
    echo '<p class="description">For internal use only.</p></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<th><label for="link_expire_type">Expire Rule</label></th>';
    echo '<td><select class="" name="link_expire_type" id="link_expire_type">';
    UCP_utility::create_select_options($link_expire_types, '');
    echo '</select>';
    echo '<p class="description">To limit access to the site and prevent abuse, the link can automatically expire after a specified date/time, a certain amount of sessions (the number of different users that have clicked the link) or after a defined number of different IPs have accessed it.</p></td>';
    echo '</tr>';

    echo '<tr id="link_expire_value_container_number">';
    echo '<th><label for="link_expire_value">Expire Details</label></th>';
    echo '<td><input type="number" class="small-text" step="1" min="1" name="link_expire_value" id="link_expire_value" placeholder="10" value="">';
    echo '<p class="description">Number of unique sessions or IPs after which the link expires.</p></td>';
    echo '</tr>';

    echo '<tr id="link_expire_value_container_date">';
    echo '<th><label for="link_expire_value2">Expire Date</label></th>';
    echo '<td><input id="link_expire_value2" type="text" data-earliest="now" class="datepicker" name="link_expire_value2" value="" placeholder="yyyy-mm-dd hh:mm"><span title="' . __('Open date & time picker', 'under-construction-page') . '" alt="' . __('Open date & time picker', 'under-construction-page') . '" class="show-datepicker dashicons dashicons-calendar-alt"></span> <span title="Clear date & time" alt="' . __('Clear date & time', 'under-construction-page') . '" class="clear-datepicker dashicons dashicons-no"></span>';
    echo '<p class="description">Select a date and time after which the link will expire.</p></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<th>';
    echo '<input type="hidden" name="link_id" id="link_id">';
    echo '<a href="#" class="js-action button button-primary" id="save-new-access-link"><span class="dashicons dashicons-update"></span>Add New Direct Access Link</a><a href="#" class="js-action button button-primary" id="save-access-link"><span class="dashicons dashicons-update"></span>Save Changes</a></th>';
    echo '<td class="textright"><a href="#" class="js-action button button-secondary ucp-close-ui-dialog">Cancel</a></td>';
    echo '</tr>';

    echo '</table>';
    echo '</div>'; // dialog
  } // dialogs


  static function tab_rules() {
    $options = self::get_options();
    $roles = $users = array();

    $tmp_roles = get_editable_roles();
    foreach ($tmp_roles as $tmp_role => $details) {
      $name = translate_user_role($details['name']);
      $roles[] = array('val' => $tmp_role,  'label' => $name);
    }

    $tmp_users = get_users(array('fields' => array('id', 'display_name')));
    foreach ($tmp_users as $user) {
      $users[] = array('val' => $user->id, 'label' => $user->display_name);
    }

    $url_rules_types = array(array('val' => '0', 'label' => __('Disabled', 'under-construction-page')),
                             array('val' => 'whitelist', 'label' => __('Listed URLs will NEVER be affected by UCP', 'under-construction-page')),
                             array('val' => 'blacklist', 'label' => __('ONLY listed URLs CAN BE affected by UCP', 'under-construction-page')));

    echo '<div>';
    echo '<table class="form-table">';
    echo '<tr valign="top">
    <th scope="row"><label for="whitelisted_ips">' . __('Whitelisted IP Addresses', 'under-construction-page') . '</label></th>
    <td><textarea data-autoresize="1" placeholder="122.45.23.22
122.45.25.211" rows="3" cols="25" class="code" id="whitelisted_ips" name="' . UCP_OPTIONS_KEY . '[whitelisted_ips]">';
    if(is_array($options['whitelisted_ips'])){
      echo esc_textarea(implode("\n", $options['whitelisted_ips']));
    }
    echo '</textarea><p class="description">Selected IPs will <b>not</b> be affected by the under construction mode and their users will always see the "normal" site. Write one IP per line. Wildcards are not supported. IPv6 is supported.<br>Your IP address is: ' . UCP_utility::getUserIP() . '</p>';
    echo '</td></tr>';

    echo '<tr valign="top" id="whitelisted-roles">
    <th scope="row">' . __('Whitelisted User Roles', 'under-construction-page') . '</th>
    <td>';

    $cnt = 1;
    foreach ($roles as $tmp_role) {
      echo  '<input name="' . UCP_OPTIONS_KEY . '[whitelisted_roles][]" id="roles-' . $tmp_role['val'] . '" ' . UCP_utility::checked($tmp_role['val'], $options['whitelisted_roles'], false) . ' value="' . $tmp_role['val'] . '" type="checkbox" /> <label for="roles-' . $tmp_role['val'] . '">' . $tmp_role['label'] . '</label><br />';
      if ($cnt == 7 && sizeof($roles) > 7) {
        echo '<span class="hidden" id="more-roles">';
      }
      $cnt++;
    } // foreach
    if ($cnt >= 7 && sizeof($roles) > 7) {
        echo '</span><a href="#" id="show-all-roles">Show all user roles</a>';
      }
    echo '<p class="description">Selected user roles will <b>not</b> be affected by the under construction mode and will always see the "normal" site when logged in.</p>';
    echo '</td></tr>';

    echo '<tr valign="top">
    <th scope="row"><label for="whitelisted_users">' . __('Whitelisted Users', 'under-construction-page') . '</label></th>
    <td><select id="whitelisted_users" class="select2" style="width: 50%; max-width: 300px;" name="' . UCP_OPTIONS_KEY . '[whitelisted_users][]" multiple>';
    UCP_utility::create_select_options($users, $options['whitelisted_users'], true);
    echo '</select><p class="description">Selected users (when logged in) will <b>not</b> be affected by the under construction mode and will always see the "normal" site.</p>';
    echo '</td></tr>';

    echo '<tr>';
    echo '<th><label for="url_rules_type">URL Based Rules</label></th>';
    echo '<td><select name="' . UCP_OPTIONS_KEY . '[url_rules_type]' . '" id="url_rules_type">';
    UCP_utility::create_select_options($url_rules_types, $options['url_rules_type']);
    echo '</select>';
    echo '<div id="url_rules_wrapper" class="field_wrapper">';
    echo '<textarea data-autoresize="1" placeholder="/my-page/
/tag/featured/" rows="3" cols="24" class="code" id="url_rules" name="' . UCP_OPTIONS_KEY . '[url_rules]">';
    if(is_array($options['url_rules'])){
        echo esc_textarea(implode("\n", $options['url_rules']));
    }
    echo '</textarea>';
    echo '<p class="description">Enter one URL per line. Start and end URLs with a forward slash (/).</p></div>';
    echo '<p class="description">Use this option to set per URL rules and lock down the entire site except selected pages; or lock down just some pages and leave all others accessible to visitors. If second option is used all other access rules still apply.</p>';
    echo '</td>';
    echo '</tr>';
    
    echo '<tr>
    <th scope="row"><label for="direct_access_password">Direct Access Password</label></th>
    <td>';
    echo '<input id="direct_access_password" type="text" class="regular-text" name="' . UCP_OPTIONS_KEY . '[direct_access_password]' . '" value="' . esc_attr($options['direct_access_password']) . '" placeholder="min 4 characters">';
    echo '<p class="description">Direct Access Password is the most user-friendly way (especially when working with clients) to give selected users access to the "normal" site. Choose a password, save changes, and send users this link: <a href="' . get_home_url() . '/#access-site-form">' . get_home_url() . '/#access-site-form</a> or enable the option below to show the password form button.<br>The password has to be at least 4 characters long.</p>';
    echo '</td></tr>';
    
    echo '<tr>
    <th scope="row"><label for="password_button">Password Form Button</label></th>
    <td>';
    UCP_utility::create_toogle_switch('password_button', array('saved_value' => $options['password_button'], 'option_key' => UCP_OPTIONS_KEY . '[password_button]'));
    echo '<p class="description">Show a discrete button to the direct access password form in the lower right corner of the under construction page.<br>The button will only show if the Direct Access Password is configured.</p>';
    echo '</td></tr>';
    
    echo '<tr valign="top">
    <th scope="row"><label for="site_password_toggle">' . __('Password Protect Under Construction Page', 'under-construction-page') . '</label></th>
    <td>';
    UCP_utility::create_toogle_switch('site_password_toggle', array('saved_value' => (empty($options['site_password']))? 0: 1, 'option_key' => UCP_OPTIONS_KEY . '[site_password_toggle]'));
    echo '<p class="description">Protected the entire site with a password you choose. Only those with the password can view the under construction page.</p>';
    echo '<div id="site_password_wrapper">';
    echo '<div class="field_wrapper"><br><label for="site_password"><b>Password</b></label><input id="site_password" type="text" name="' . UCP_OPTIONS_KEY . '[site_password]" value="' . esc_attr($options['site_password']) . '" placeholder="min 4 characters">';
    echo '<p class="description">' . __('The password has to be at least 4 characters long.', 'under-construction-page') . '</p></div>';

    echo '</div>'; // site_password_wrapper
    echo '</td></tr>';

    echo '</table>';
    echo '</div>';

    parent::footer_save_button();
  } // tab_rules

  static function tab_links() {
    global $wpdb;
    $links = array();

    $links = $wpdb->get_results('SELECT * FROM ' . $wpdb->ucp_links . ' WHERE type = "access" ORDER BY id ASC');

    echo '<table id="direct_access_links" class="condensed">
      <tr class="header">
        <th class="actions"><a data-tooltip="Add new direct access link" href="#" class="js-action add-access-link"><span class="dashicons dashicons-plus"></span></a><a data-tooltip="Delete all direct access links" class="js-action delete-action delete-all-access-links" href="#"><span class="dashicons dashicons-trash"></span></a></th>
        <th>Name / Description</th>
        <th>Link</th>
        <th>Expire Rule</th>
        <th class="narrow">Sessions</th>
      </tr>';

    foreach ($links as $link) {
      $expires = '';

      // sessions or  date
      switch ($link->expire_type) {
        case 'sessions':
          if ($link->expire_value <= $link->sessions) {
            $expires = '<b class="expired">expired</b> after ' . number_format($link->expire_value, 0) . ($link->expire_value == 1? ' session': ' sessions');
          } else {
            $expires = 'expires after ' . number_format($link->expire_value, 0) . ($link->expire_value == 1? ' session': ' sessions') . '; ';
            $expires .= number_format($link->expire_value - $link->sessions, 0) . ($link->expire_value - $link->sessions == 1? ' session': ' sessions') . ' left';
          }
        break;
        case 'date':
          $current_date = substr(current_time('mysql'), 0, 16);

          if ($link->expire_value <= $current_date) {
            $expires = '<b class="expired">expired</b> on ' . date(get_option('date_format'), strtotime($link->expire_value)) . ' @ ' . date(get_option('time_format'), strtotime($link->expire_value));
          } else {
            $expires = 'expires after ' . date(get_option('date_format'), strtotime($link->expire_value)) . ' @ ' . date(get_option('time_format'), strtotime($link->expire_value));
          }
        break;
        case 'ip':
          $ips = unserialize($link->ips);
          if (!is_array($ips)) {
            $ips_cnt = 0;
          } else {
            $ips_cnt = sizeof($ips);
          }

          if ($link->expire_value <= $ips_cnt) {
            $expires = '<b class="expired">expired</b> on ' . date(get_option('date_format'), strtotime($link->expire_value)) . ' @ ' . date(get_option('time_format'), strtotime($link->expire_value));
          } else {
            $expires = 'total IP slots: ' . number_format($link->expire_value, 0) . '; ';
            $expires .= 'available: ' . number_format($link->expire_value - $ips_cnt, 0) . '';
            if ($ips_cnt) {
              $expires .= '; whitelisted IPs: ' . implode(', ', array_keys($ips)) . '';
            }
          }
        break;
        default:
          $expires = 'doesn\'t expire';
      }

      echo '<tr data-link-id="' . esc_attr($link->id) . '" data-link-name="' . esc_attr($link->name) . '" data-link-expire-type="' . esc_attr($link->expire_type) . '" data-link-expire-value="' . esc_attr($link->expire_value) . '">';
      echo '<td class="actions"><a data-tooltip="Edit direct access link" class="js-action edit-action edit-access-link" href="#"><span class="dashicons dashicons-edit"></span></a><a data-tooltip="Delete direct access link" class="js-action delete-action delete-access-link" href="#"><span class="dashicons dashicons-trash"></span></a></td>';
      $time_tooltip = 'Created on ' . $link->created;
      if ($link->modified && $link->modified[0] != '0') {
        $time_tooltip .= '; modified on ' . $link->modified;
      }

      echo '<td><span data-tooltip="' . esc_attr($time_tooltip) . '" class="link_name">' . esc_html($link->name) . '</span></td>';
      $short_link = trailingslashit(str_replace(array('https://', 'http://'), '', get_bloginfo('url')));
      echo '<td><code class="clipboard-copy tooltipped" title="Click to copy link to clipboard" data-tooltip="Click to copy link to clipboard" data-clipboard-text="' . trailingslashit(get_bloginfo('url')) . '?ucp-access=' . $link->secret_key . '">' . $short_link . '?ucp-access=' . $link->secret_key . '</code></td>';
      echo '<td>' . $expires . '</td>';
      echo '<td class="narrow">' . number_format($link->sessions, 0) . '</td>';
      echo '</tr>';
    } // foreach link
    echo '</table>';

    echo '<p class="' . (!empty($links)? 'hidden': '') . '" id="no-access-links">You don\'t have any Direct Access Links. <a href="#" class="js-action add-access-link">Create one</a> to enable users access to the site when under construction mode is enabled.</p>';

    echo '<p class="description tab-content"><br>Direct Access Links are the most flexible and user-friendly way (especially when working with clients) to give only selected visitors access to the "normal" site. Simply generate a new link, configure expiration options and share it with users to allow them access to the site.<br>
    When a user clicks on a link for the first time, it will be counted as one session. All other clicks in the site are counted as that same session. Sessions time out after two hours of inactivity. If an expire time is set, users\' sessions will be terminated once the time runs out regardless if they already have an active session or not.<br>
    If IP based expiring is used there is no need to ask visitors for their IP. When they access the link for the first time their IP will be saved and counted as "one IP used". New users (or different IPs to be more precise) will be able to use the link as long as there are more free IP slots available.</p>';
  } // tab_links
} // class UCP_tab_access
