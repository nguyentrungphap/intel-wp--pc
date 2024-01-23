<?php
/**
 * UCP Affiliate Links
 */


class UCP_affiliate extends UCP {
  static $cookie_name = 'ucp-aff';
  
  static function check_cookie() {
    if (empty($_COOKIE[self::$cookie_name])) {
      return false;
    }

    global $wpdb;
    
    $query = $wpdb->prepare('SELECT id FROM ' . $wpdb->ucp_links . ' WHERE type = "affiliate" and secret_key = %s LIMIT 1', array(substr($_COOKIE[self::$cookie_name], 0, 8)));
    $cookie = $wpdb->get_var($query);
    
    if (empty($cookie)) {
      return false;
    } else {
      return $cookie;
    }
  } // check_cookie
  
  
  static function check_affiliate_link() {
    // completely ignore affiliates on preview
    if (is_user_logged_in() && isset($_GET['ucp_preview'])) {
      return;
    }
    
    // do we have a valid aff cookie
    if (($aff_id = self::check_cookie())) {
      self::add_view($aff_id);      
    } else {
      self::clean_affiliate_link_from_url();
    }
  } // check_affiliate_link
  
  
  static function clean_affiliate_link_from_url() {
    // missing or bad affiliate key in URL - don't do anyting
    if (empty($_GET['ucp-aff']) || strlen(trim($_GET['ucp-aff'])) != 8) {
      return;
    }

    $secret_key = trim(strtolower($_GET['ucp-aff']));

    // construct new link to redirect to
    global $wpdb;
    $options = parent::get_options();
    $url_params = $_GET;

    unset($url_params['ucp-aff']);
    $path = strtolower(@parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $clean_url = untrailingslashit(get_bloginfo('url')) . trailingslashit($path) . http_build_query($url_params);

    $query = $wpdb->prepare('SELECT * FROM ' . $wpdb->ucp_links . ' WHERE type = "affiliate" and secret_key = %s LIMIT 1', $secret_key);
    $link = $wpdb->get_row($query);

    // unknown affiliate key - do nothing
    if (empty($link)) {
      return;
    }

    setcookie(self::$cookie_name, $secret_key, time() + $options['affiliate_cookie_lifetime'] * DAY_IN_SECONDS, '/');
    $query = $wpdb->prepare('UPDATE ' . $wpdb->ucp_links . ' SET sessions = sessions + 1 WHERE id = %d LIMIT 1', array($link->id));
    $wpdb->query($query);

    wp_safe_redirect($clean_url);
    die;
  } // clean_affiliate_link_from_url
  
  
  static function add_view($aff_id) {
    global $wpdb;
    
    $query = $wpdb->prepare('UPDATE ' . $wpdb->ucp_links . ' SET views = views + 1 WHERE id = %d LIMIT 1', array($aff_id));
    $wpdb->query($query);
  } // add_view
} // class
