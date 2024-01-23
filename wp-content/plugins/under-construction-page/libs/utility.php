<?php
/**
 * UCP Utility & Helper functions
 */


class UCP_utility extends UCP {

  // keeping compatibility with WP < v4.4
  static function wp_get_server_protocol() {
    $protocol = $_SERVER['SERVER_PROTOCOL'];
    if (!in_array($protocol, array('HTTP/1.1', 'HTTP/2', 'HTTP/2.0'))) {
        $protocol = 'HTTP/1.0';
    }

    return $protocol;
  } // wp_get_server_protocol

  // todo
  // needed?
  static function encode_email($email) {
    $len = strlen($email);
    $out = '';

    for ($i = 0; $i < $len; $i++) {
      $out .= '&#'. ord($email[$i]) . ';';
    }

    return $out;
  } // encode_email


  // todo
  static function slashit($url) {
    if (strpos($url, '?') === false && substr($url, -4, 1) != '.') {
      $url = trailingslashit($url);
    }

    if ($url != '/') {
      $url = '/' . ltrim($url, '/');
    }

    return $url;
  } // slashit


  // todo
  // not used
  static function startslashit($url) {
    if ($url != '/') {
      $url = '/' . ltrim($url, '/');
    }

    return $url;
  } // startslashit


  // checkbox helper function
  static function checked($value, $current, $echo = false) {
    $out = '';

    if (!is_array($current)) {
      $current = (array) $current;
    }

    if (in_array($value, $current)) {
      $out = ' checked="checked" ';
    }

    if ($echo) {
      echo $out;
    } else {
      return $out;
    }
  } // checked


  // todo
  static function sort_select_options($item1, $item2) {
    return strnatcmp($item1['label'], $item2['label']);
  } // sort_select_list


  // helper function for saving options, mostly checkboxes
  static function check_var_isset($values, $variables) {
    foreach ($variables as $key => $value) {
      if (!isset($values[$key])) {
        $values[$key] = $value;
      }
    }

    return $values;
  } // check_var_isset


  static function create_toogle_switch($name, $options = array(), $output = true) {
    $default_options = array('value' => '1', 'saved_value' => '', 'option_key' => $name);
    $options = array_merge($default_options, $options);

    $out = "\n";
    $out .= '<div class="toggle-wrapper">';
    $out .= '<input type="checkbox" id="' . $name . '" ' . self::checked($options['value'], $options['saved_value']) . ' type="checkbox" value="' . $options['value'] . '" name="' . $options['option_key'] . '">';
    $out .= '<label for="' . $name . '" class="toggle"><span class="toggle_handler"></span></label>';
    $out .= '</div>';

    if ($output) {
      echo $out;
    } else {
      return $out;
    }
  } // create_toggle_switch


  // helper function for creating dropdowns
  static function create_select_options($options, $selected = null, $output = true) {
    $out = "\n";

    if(!is_array($selected)) {
      $selected = array($selected);
    }

    foreach ($options as $tmp) {
      $data = '';
      if (isset($tmp['disabled'])) {
        $data .= ' disabled="disabled" ';
      }
      if (in_array($tmp['val'], $selected)) {
        $out .= "<option selected=\"selected\" value=\"{$tmp['val']}\"{$data}>{$tmp['label']}&nbsp;</option>\n";
      } else {
        $out .= "<option value=\"{$tmp['val']}\"{$data}>{$tmp['label']}&nbsp;</option>\n";
      }
    } // foreach

    if ($output) {
      echo $out;
    } else {
      return $out;
    }
  } // create_select_options

  static function getUserLocation($user_ip){
    $ip_info = get_option('wf_optin_geodata', array());
    if (!empty($ip_info[$user_ip])) {
     return $ip_info[$user_ip];
    }
    
    $geo_req = wp_remote_get('http://api.ipstack.com/'.$user_ip.'?access_key=48461af560f93cf04b6df35d46e1b4cb&output=json&legacy=1', array('sslverify' => false));

    if (!is_wp_error($geo_req) && !empty($geo_req['response']['code']) && $geo_req['response']['code'] == 200) {
      $geo_data = json_decode(stripslashes($geo_req['body']));
      return @$geo_data->country_name.', '.@$geo_data->region.', '.@$geo_data->city;
    } else {
      return 'unknown';
    }
  }

  static function getUserIP() {
		if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
			if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
				$addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
				return trim($addr[0]);
			} else {
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
		}
		else if(!empty($_SERVER['REMOTE_ADDR'])){
			return $_SERVER['REMOTE_ADDR'];
		} else {
			return 'unknown.ip';
		}
	}

  /**
   * Parses a user agent string into its important parts
   *
   * @author Jesse G. Donat <donatj@gmail.com>
   * @link https://github.com/donatj/PhpUserAgent
   * @link http://donatstudios.com/PHP-Parser-HTTP_USER_AGENT
   * @param string|null $u_agent User agent string to parse or null. Uses $_SERVER['HTTP_USER_AGENT'] on NULL
   * @throws \InvalidArgumentException on not having a proper user agent to parse.
   * @return string[] an array with browser, version and platform keys
   */


  static function parse_user_agent( $u_agent = false ) {
    if( is_null($u_agent) ) {
      if( isset($_SERVER['HTTP_USER_AGENT']) ) {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
      } else {
        return '';
      }
    }
    $platform = null;
    $browser  = null;
    $version  = null;
    $empty = array( 'platform' => $platform, 'browser' => $browser, 'version' => $version );
    if( !$u_agent ) return $empty;
    if( preg_match('/\((.*?)\)/im', $u_agent, $parent_matches) ) {
      preg_match_all('/(?P<platform>BB\d+;|Android|CrOS|Tizen|iPhone|iPad|iPod|Linux|(Open|Net|Free)BSD|Macintosh|Windows(\ Phone)?|Silk|linux-gnu|BlackBerry|PlayBook|X11|(New\ )?Nintendo\ (WiiU?|3?DS|Switch)|Xbox(\ One)?)
          (?:\ [^;]*)?
          (?:;|$)/imx', $parent_matches[1], $result, PREG_PATTERN_ORDER);
      $priority = array( 'Xbox One', 'Xbox', 'Windows Phone', 'Tizen', 'Android', 'FreeBSD', 'NetBSD', 'OpenBSD', 'CrOS', 'X11' );
      $result['platform'] = array_unique($result['platform']);
      if( count($result['platform']) > 1 ) {
        if( $keys = array_intersect($priority, $result['platform']) ) {
          $platform = reset($keys);
        } else {
          $platform = $result['platform'][0];
        }
      } elseif( isset($result['platform'][0]) ) {
        $platform = $result['platform'][0];
      }
    }
    if( $platform == 'linux-gnu' || $platform == 'X11' ) {
      $platform = 'Linux';
    } elseif( $platform == 'CrOS' ) {
      $platform = 'Chrome OS';
    }
    preg_match_all('%(?P<browser>Camino|Kindle(\ Fire)?|Firefox|Iceweasel|IceCat|Safari|MSIE|Trident|AppleWebKit|
          TizenBrowser|Chrome|Vivaldi|IEMobile|Opera|OPR|Silk|Midori|Edge|CriOS|UCBrowser|Puffin|SamsungBrowser|
          Baiduspider|Googlebot|YandexBot|bingbot|Lynx|Version|Wget|curl|
          Valve\ Steam\ Tenfoot|
          NintendoBrowser|PLAYSTATION\ (\d|Vita)+)
          (?:\)?;?)
          (?:(?:[:/ ])(?P<version>[0-9A-Z.]+)|/(?:[A-Z]*))%ix',
      $u_agent, $result, PREG_PATTERN_ORDER);
    // If nothing matched, return null (to avoid undefined index errors)
    if( !isset($result['browser'][0]) || !isset($result['version'][0]) ) {
      if( preg_match('%^(?!Mozilla)(?P<browser>[A-Z0-9\-]+)(/(?P<version>[0-9A-Z.]+))?%ix', $u_agent, $result) ) {
        return array( 'platform' => $platform ?: null, 'browser' => $result['browser'], 'version' => isset($result['version']) ? $result['version'] ?: null : null );
      }
      return $empty;
    }
    if( preg_match('/rv:(?P<version>[0-9A-Z.]+)/si', $u_agent, $rv_result) ) {
      $rv_result = $rv_result['version'];
    }
    $browser = $result['browser'][0];
    $version = $result['version'][0];
    $lowerBrowser = array_map('strtolower', $result['browser']);
    $find = function ( $search, &$key, &$value = null ) use ( $lowerBrowser ) {
      $search = (array)$search;
      foreach( $search as $val ) {
        $xkey = array_search(strtolower($val), $lowerBrowser);
        if( $xkey !== false ) {
          $value = $val;
          $key   = $xkey;
          return true;
        }
      }
      return false;
    };
    $key = 0;
    $val = '';
    if( $browser == 'Iceweasel' || strtolower($browser) == 'icecat' ) {
      $browser = 'Firefox';
    } elseif( $find('Playstation Vita', $key) ) {
      $platform = 'PlayStation Vita';
      $browser  = 'Browser';
    } elseif( $find(array( 'Kindle Fire', 'Silk' ), $key, $val) ) {
      $browser  = $val == 'Silk' ? 'Silk' : 'Kindle';
      $platform = 'Kindle Fire';
      if( !($version = $result['version'][$key]) || !is_numeric($version[0]) ) {
        $version = $result['version'][array_search('Version', $result['browser'])];
      }
    } elseif( $find('NintendoBrowser', $key) || $platform == 'Nintendo 3DS' ) {
      $browser = 'NintendoBrowser';
      $version = $result['version'][$key];
    } elseif( $find('Kindle', $key, $platform) ) {
      $browser = $result['browser'][$key];
      $version = $result['version'][$key];
    } elseif( $find('OPR', $key) ) {
      $browser = 'Opera Next';
      $version = $result['version'][$key];
    } elseif( $find('Opera', $key, $browser) ) {
      $find('Version', $key);
      $version = $result['version'][$key];
    } elseif( $find('Puffin', $key, $browser) ) {
      $version = $result['version'][$key];
      if( strlen($version) > 3 ) {
        $part = substr($version, -2);
        if( ctype_upper($part) ) {
          $version = substr($version, 0, -2);
          $flags = array( 'IP' => 'iPhone', 'IT' => 'iPad', 'AP' => 'Android', 'AT' => 'Android', 'WP' => 'Windows Phone', 'WT' => 'Windows' );
          if( isset($flags[$part]) ) {
            $platform = $flags[$part];
          }
        }
      }
    } elseif( $find(array( 'IEMobile', 'Edge', 'Midori', 'Vivaldi', 'SamsungBrowser', 'Valve Steam Tenfoot', 'Chrome' ), $key, $browser) ) {
      $version = $result['version'][$key];
    } elseif( $rv_result && $find('Trident', $key) ) {
      $browser = 'MSIE';
      $version = $rv_result;
    } elseif( $find('UCBrowser', $key) ) {
      $browser = 'UC Browser';
      $version = $result['version'][$key];
    } elseif( $find('CriOS', $key) ) {
      $browser = 'Chrome';
      $version = $result['version'][$key];
    } elseif( $browser == 'AppleWebKit' ) {
      if( $platform == 'Android' && !($key = 0) ) {
        $browser = 'Android Browser';
      } elseif( strpos($platform, 'BB') === 0 ) {
        $browser  = 'BlackBerry Browser';
        $platform = 'BlackBerry';
      } elseif( $platform == 'BlackBerry' || $platform == 'PlayBook' ) {
        $browser = 'BlackBerry Browser';
      } else {
        $find('Safari', $key, $browser) || $find('TizenBrowser', $key, $browser);
      }
      $find('Version', $key);
      $version = $result['version'][$key];
    } elseif( $pKey = preg_grep('/playstation \d/i', array_map('strtolower', $result['browser'])) ) {
      $pKey = reset($pKey);
      $platform = 'PlayStation ' . preg_replace('/[^\d]/i', '', $pKey);
      $browser  = 'NetFront';
    }
    return array( 'platform' => $platform ?: null, 'browser' => $browser ?: null, 'version' => $version ?: null );
  }

  // get plugin info for lightbox
  static function update_details($return, $action, $args) {
    if (!parent::is_activated()) {
      return $return;
    }

    static $response = false;
    $options = parent::get_options();
    $plugin = 'under-construction-page';

    if ($action != 'plugin_information' || empty($args->slug) || ($args->slug != $plugin)) {
      return $return;
    }

    if(empty($response) || is_wp_error($response)) {
      $request_params = array('sslverify' => false, 'timeout' => 15, 'redirection' => 2);
      $request_args = array('action' => 'plugin_information',
                            'request_details' => serialize($args),
                            'timestamp' => time(),
                            'codebase' => 'pro',
                            'version' => parent::$version,
                            'license_key' => $options['license_key'],
                            'license_expires' => $options['license_expires'],
                            'license_type' => $options['license_type'],
                            'license_active' => $options['license_active'],
                            'site' => get_home_url());

      $url = add_query_arg($request_args, parent::$licensing_servers[0]);
      $response = wp_remote_get(esc_url_raw($url), $request_params);

      if (is_wp_error($response) || !wp_remote_retrieve_body($response)) {
        $url = add_query_arg($request_args, parent::$licensing_servers[1]);
        $response = wp_remote_get(esc_url_raw($url), $request_params);
      }
    } // if !$response

    if (is_wp_error($response) || !wp_remote_retrieve_body($response)) {
      $res = new WP_Error('plugins_api_failed', __('An unexpected HTTP error occurred during the API request.', 'under-construction-page'), $response->get_error_message());
    } else {
      $res = json_decode(wp_remote_retrieve_body($response), false);

      if (!is_object($res)) {
        $res = new WP_Error('plugins_api_failed', __('Invalid API respone.', 'under-construction-page'), wp_remote_retrieve_body($response));
      } else {
        $res->sections = (array) $res->sections;
        $res->banners = (array) $res->banners;
        $res->icons = (array) $res->icons;
      }
    }

    return $res;
  } // update_details

  // get info on new plugin version if one exists
  static function update_filter($current) {
    if (!parent::is_activated()) {
      return $current;
    }

    static $response = false;
    $options = parent::get_options();
    $plugin = 'under-construction-page/under-construction.php';
    $slug = 'under-construction-page';

    if(empty($response) || is_wp_error($response)) {
      $request_params = array('sslverify' => false, 'timeout' => 15, 'redirection' => 2);
      $request_args = array('action' => 'update_info',
                            'timestamp' => time(),
                            'codebase' => 'pro',
                            'version' => parent::$version,
                            'license_key' => $options['license_key'],
                            'license_expires' => $options['license_expires'],
                            'license_type' => $options['license_type'],
                            'license_active' => $options['license_active'],
                            'site' => get_home_url());

      $url = add_query_arg($request_args, parent::$licensing_servers[0]);
      $response = wp_remote_get(esc_url_raw($url), $request_params);

      if (is_wp_error($response)) {
        $url = add_query_arg($request_args, parent::$licensing_servers[1]);
        $response = wp_remote_get(esc_url_raw($url), $request_params);
      }
    } // if !$response

    if (!is_wp_error($response) && wp_remote_retrieve_body($response)) {
      $data = json_decode(wp_remote_retrieve_body($response), false);
      if (empty($current)) {
        $current = new stdClass();
      }
      if (empty($current->response)) {
        $current->response = array();
      }
      if (!empty($data) && is_object($data)) {
        $data->icons = (array) $data->icons;
        $data->banners = (array) $data->banners;
        $current->response[$plugin] = $data;
      }
    }

    return $current;
  } // update_filter
} // class
