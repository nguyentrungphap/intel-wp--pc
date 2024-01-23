<?php
/*
 * UnderConstructionPage PRO
 * Interface - Tab - Leads
 * (c) Web factory Ltd, 2015 - 2017
 */

class UCP_tab_leads extends UCP {

  static function display() {
    global $wpdb;
    
    $leads = $wpdb->get_results('SELECT * FROM '.$wpdb->ucp_leads.' ORDER BY timestamp DESC');
    echo '<table id="leads" class="condensed">
    <thead>
    <tr>
      <th class="narrow">Lead Type</th>
      <th>Name</th>
      <th>Email</th>
      <th class="_narrow">Timestamp</th>
      <th class="">Message</th>
      <th class="_narrow">Location</th>
      <th>UA</th>';
    echo '<th class="actions"><a id="delete-all-leads" href="' . add_query_arg(array('action' => 'ucp_delete_all_leads', 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php')) . '" class="js-action delete-action delete-bypasslink"><span class="dashicons dashicons-trash"></span></a></th>';
    echo '</tr>
    </thead><tbody>';
    
    foreach( $leads as $lead) {
      echo '<tr>';
      if($lead->type == 'newsletter'){
         echo '<td class="narrow"><span data-tooltip="Lead generated from newsletter form"><span class="dashicons dashicons-email-alt" title="Newsletter"></span></span></td>';
      } else {
        echo '<td class="narrow"><span data-tooltip="Lead generated from contact form"><span class="dashicons dashicons-welcome-write-blog" title="Contact Form"></span></span></td>';
      }
      
      echo '<td>'.$lead->name.'</td>';
      echo '<td>'.$lead->email.'</td>';
      echo '<td class="narrow">';
      $date = new DateTime($lead->timestamp, new DateTimeZone('UTC'));
      try{
          if(get_option('gmt_offset')){
            $timezoneName = timezone_name_from_abbr("", get_option('gmt_offset')*3600, false);
            $date->setTimezone(new DateTimeZone($timezoneName));
        }
      } catch(Exception $e) {
          
      }
      echo $date->format('M j Y g:i A');      
      echo '</td>';  
      echo '<td class="">';
      if($lead->custom){
        $custom_fields = unserialize($lead->custom);
        foreach($custom_fields as $field => $data){
          echo '<strong>'.$field.':</strong>'.$data.'<br />'; 
        }
      }
      echo '</td>';
      echo '<td class="">'.$lead->location.' ('.$lead->ip.')</td>';
      $ua = UCP_utility::parse_user_agent($lead->user_agent);
      echo '<td class="">'.($ua?$ua['platform'].' '.$ua['browser'].' '.$ua['version']:'').'</td>';
      echo '<td class="actions"><a href="' . add_query_arg(array('action' => 'ucp_delete_lead', 'leadid' => $lead->id, 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php')) . '" class="js-action delete-action delete-bypasslink"><span class="dashicons dashicons-trash"></span></a></td>';
      echo '</tr>';
    }
    
    echo '</tbody></table>';

    echo '<p class="description">All leads, from contact and newsletter form are automatically saved here. Use the buttons above to export them in various formats or use <a class="change_tab" data-tab2="advanced_services" data-tab="advanced" href="#">external services</a> to automatically push to 3rd party services.</p>';
  } // display
} // class UCP_tab_leads
