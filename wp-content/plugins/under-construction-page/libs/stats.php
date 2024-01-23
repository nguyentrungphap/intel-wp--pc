<?php
/**
 * UCP Stats
 */


class UCP_stats extends UCP {
  static function add_visit() {
    // previews are not counted
    if (isset($_GET['ucp_preview'])) {
      return;
    }

    global $wpdb;
    parent::session_start();
    $date = substr(current_time('mysql'), 0, 10);

    if (!empty($_SESSION['last_session_date']) && $_SESSION['last_session_date'] == $date) {
      // we already have a session on this date
      // only increase view
      $query = $wpdb->prepare('UPDATE ' . $wpdb->ucp_stats . ' SET views = views + 1 WHERE date = %s LIMIT 1', array($date));
      $result = $wpdb->query($query);
      if (!$result) {
        // the row for this date should already exist but it doesn't
        // either a session issue, or the date has incremented in the mean time
        $query = $wpdb->prepare('INSERT INTO ' . $wpdb->ucp_stats . ' (date, views, sessions, conversions) VALUES (%s, 1, 1, 0) ON DUPLICATE KEY UPDATE views = views + 1, sessions = sessions + 1', array($date));
        $result = $wpdb->query($query);
      }
    } else {
      // new session
      // add session and view
      $query = $wpdb->prepare('INSERT INTO ' . $wpdb->ucp_stats . ' (date, views, sessions, conversions) VALUES (%s, 1, 1, 0) ON DUPLICATE KEY UPDATE views = views + 1, sessions = sessions + 1', array($date));
      $result = $wpdb->query($query);
    }

    // make sure we keep the date up-to-date
    $_SESSION['last_session_date'] = $date;
  } // add_visit


  // todo
  static function add_conversion() {
    // previews are not counted
    if (isset($_GET['ucp_preview'])) {
      return;
    }

    global $wpdb;
    parent::session_start();
    $date = substr(current_time('mysql'), 0, 10);

    // edge case
    // either session broke or date incremented
    if (empty($_SESSION['last_session_date']) || $_SESSION['last_session_date'] != $date) {
      self::add_visit();
    }

    $query = $wpdb->prepare('UPDATE ' . $wpdb->ucp_stats . ' SET conversions = conversions + 1 WHERE date = %s LIMIT 1', array($date));
    $result = $wpdb->query($query);
    if (!$result) {
      // the row for this date should already exist but it doesn't
      // either a session issue, or the date has incremented in the mean time
      $query = $wpdb->prepare('INSERT INTO ' . $wpdb->ucp_stats . ' (date, views, sessions, conversions) VALUES (%s, 1, 1, 1) ON DUPLICATE KEY UPDATE conversions = conversions + 1', array($date));
      $result = $wpdb->query($query);
    }

    // add conversion for affiliate link
    if (($aff_id = UCP_affiliate::check_cookie())) {
      $query = $wpdb->prepare('UPDATE ' . $wpdb->ucp_links . ' SET conversions = conversions + 1 WHERE id = %d LIMIT 1', array($aff_id));
      $wpdb->query($query);
    }
  } // add_conversion


  // todo
  static function get_data($mode = 'chart') {
    global $wpdb;
    $stats = array('dates' => array(), 'views' => array(), 'sessions' => array(), 'conversions' => array());
    $totals = array('views' => 0, 'sessions' => 0, 'conversions' => 0, 'days' => 0);

    $today = substr(current_time('mysql'), 0, 10);
    $month_ago = date('Y-m-d', current_time('timestamp') - 31 * DAY_IN_SECONDS);

    $days = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->ucp_stats . ' WHERE date >= %s ORDER BY date ASC LIMIT 33', array($month_ago)));

    if ($days) {
      $len = sizeof($days);
      $previous_day = false;
      for ($i = 0; $i < $len; $i++) {
        $day = $days[$i];

        // do we have a gap?
        if (!empty($previous_day) && $previous_day != date('Y-m-d', strtotime($day->date) - DAY_IN_SECONDS)) {
          $gap_day = date('Y-m-d', strtotime($previous_day) + DAY_IN_SECONDS);
          $previous_day = $gap_day;
          $stats['dates'][] = $gap_day;
          $stats['views'][] = 0;
          $stats['sessions'][] = 0;
          $stats['conversions'][] = 0;
          $i--;
          continue;
        }
        $previous_day = $day->date;

        $stats['dates'][] = $day->date;
        $stats['views'][] = (int) $day->views;
        $stats['sessions'][] = (int) $day->sessions;
        $stats['conversions'][] = (int) $day->conversions;

        $totals['views'] += (int) $day->views;
        $totals['sessions'] += (int) $day->sessions;
        $totals['conversions'] += (int) $day->conversions;

      } // for

      $days_limit = 0;
      // check last date, should be today
      while($today != $stats['dates'][sizeof($stats['dates'])-1] && $days_limit<1000) {
        $last_date = $stats['dates'][sizeof($stats['dates'])-1];
        $next_date = date('Y-m-d', strtotime($last_date) + DAY_IN_SECONDS);
        $stats['dates'][] = $next_date;
        $stats['views'][] = 0;
        $stats['sessions'][] = 0;
        $stats['conversions'][] = 0;
        $days_limit++;
      }

      $diff = date_diff(date_create($stats['dates'][0]), date_create($today), true);
      $diff = $diff->days + 1;
    } else {
      $diff = 0;
    }

    $totals['days'] = $diff;
    $totals['views'] = number_format($totals['views'], 0);
    $totals['sessions'] = number_format($totals['sessions'], 0);
    $totals['conversions'] = number_format($totals['conversions'], 0);
    $stats['totals'] = $totals;

    return $stats;
  } // get_data


  // reset stats - truncate table
  static function reset($redirect = true) {
    global $wpdb;

    $wpdb->query('TRUNCATE TABLE ' . $wpdb->ucp_stats);

    if (false === $redirect) {
      return true;
    }

    parent::add_settings_error('Statistics have been reset.', 'notice-info');

    if (!empty($_GET['redirect'])) {
      if (strpos($_GET['redirect'], 'settings-updated=true') == false) {
        $_GET['redirect'] .= '&settings-updated=true';
      }
      wp_safe_redirect($_GET['redirect']);
    } else {
      wp_safe_redirect(admin_url());
    }

    exit;
  } // reset
} // class
