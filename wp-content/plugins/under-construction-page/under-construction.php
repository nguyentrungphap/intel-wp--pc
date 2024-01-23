<?php
/*
  Plugin Name: UnderConstructionPage PRO
  Plugin URI: https://underconstructionpage.com/
  Description: Hide your site behind a great looking under construction page while you do maintenance work.
  Author: Web factory Ltd
  Version: 5.71
  Author URI: http://www.webfactoryltd.com/
  Text Domain: under-construction-page
  Domain Path: lang

  Copyright 2015 - 2020  Web factory Ltd  (email: ucp@webfactoryltd.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

define('UCP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('UCP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('UCP_OPTIONS_KEY', 'ucp_options');
define('UCP_META_KEY', 'ucp_meta');
define('UCP_POINTERS_KEY', 'ucp_pointers');
define('UCP_NOTICES_KEY', 'ucp_notices');
define('UCP_TEMPLATES_KEY', 'ucp_templates');
define('UCP_TEMPLATES_URL', 'https://templates.underconstructionpage.com');


require_once UCP_PLUGIN_DIR . 'interface/tab_main.php';
require_once UCP_PLUGIN_DIR . 'interface/tab_design.php';
require_once UCP_PLUGIN_DIR . 'interface/tab_access.php';
require_once UCP_PLUGIN_DIR . 'interface/tab_advanced.php';
require_once UCP_PLUGIN_DIR . 'interface/tab_leads.php';
require_once UCP_PLUGIN_DIR . 'interface/tab_support.php';
require_once UCP_PLUGIN_DIR . 'interface/tab_license.php';

require_once UCP_PLUGIN_DIR . 'libs/utility.php';
require_once UCP_PLUGIN_DIR . 'libs/MailChimp.php';
require_once UCP_PLUGIN_DIR . 'libs/stats.php';
require_once UCP_PLUGIN_DIR . 'libs/affiliate.php';
require_once UCP_PLUGIN_DIR . 'libs/export_import.php';
require_once UCP_PLUGIN_DIR . 'libs/templates.php';


// main plugin class
class UCP
{
    static $version = 0;
    static $pro = true;
    static $licensing_servers = array('https://license1.underconstructionpage.com/', 'https://license2.underconstructionpage.com/');
    static $type;

    // get plugin version from header
    static function get_plugin_version()
    {
        $plugin_data = get_file_data(__FILE__, array('version' => 'Version'), 'plugin');
        self::$version = $plugin_data['version'];

        return $plugin_data['version'];
    } // get_plugin_version


    // hook things up
    static function init()
    {
        if ((wp_doing_ajax() || !is_admin()) && !isset($_SESSION) && strpos($_SERVER['REQUEST_URI'], 'wp-json/') === false) {
            @session_start();
        }

        // check if minimal required WP version is present
        if (false === self::check_wp_version(4.6)) {
            return false;
        }

        if (is_admin()) {
            // upgrade/install DB if needed
            self::maybe_upgrade();

            if (isset($_GET['ucp-generate-image'])) {
                UCP_templates::generate();
            }

            if (isset($_GET['ucp_wl'])) {
                $options = self::get_options();
                if ($_GET['ucp_wl'] == 'true') {
                    $options['whitelabel'] = 1;
                } else {
                    $options['whitelabel'] = 0;
                }
                update_option(UCP_OPTIONS_KEY, $options);
            }

            // add UCP menu to admin tools menu group
            add_action('admin_menu', array(__CLASS__, 'admin_menu'));

            // settings registration
            add_action('admin_init', array(__CLASS__, 'register_settings'));

            // updates
            add_filter('pre_set_site_transient_update_plugins', array('UCP_utility', 'update_filter'));
            add_filter('plugins_api', array('UCP_utility', 'update_details'), 100, 3);

            // aditional links in plugin description and footer
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(__CLASS__, 'plugin_action_links'));
            add_filter('plugin_row_meta', array(__CLASS__, 'plugin_meta_links'), 10, 2);
            if (self::whitelabel_filter()) {
                add_filter('admin_footer_text', array(__CLASS__, 'admin_footer_text'));
            }

            // manages admin header notifications
            add_action('admin_notices', array(__CLASS__, 'admin_notices'));
            add_action('admin_action_ucp_dismiss_notice', array(__CLASS__, 'dismiss_notice'));
            add_action('admin_action_ucp_change_status', array(__CLASS__, 'change_status'));
            add_action('admin_action_ucp_export_settings', array('UCP_ei', 'options_send_export_file'));
            add_action('admin_action_ucp_reset_stats', array('UCP_stats', 'reset'));
            add_action('admin_action_ucp_reset_settings', array(__CLASS__, 'reset_settings'));
            add_action('admin_action_ucp_delete_lead', array(__CLASS__, 'delete_lead'));
            add_action('admin_action_ucp_delete_all_leads', array(__CLASS__, 'delete_all_leads'));
            add_action('admin_action_install_weglot', array(__CLASS__, 'install_weglot'));

            // enqueue admin scripts
            add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_enqueue_scripts'));
            add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_dequeue_scripts'), 999);

            // AJAX endpoints
            add_action('wp_ajax_ucp_submit_support_message', array(__CLASS__, 'submit_support_message_ajax'));
            add_action('wp_ajax_ucp_dismiss_pointer', array(__CLASS__, 'dismiss_pointer_ajax'));
            add_action('wp_ajax_ucp_access_links', array(__CLASS__, 'access_links_ajax'));
            add_action('wp_ajax_ucp_affiliate_links', array(__CLASS__, 'affiliate_links_ajax'));
            add_action('wp_ajax_ucp_get_mc_lists', array(__CLASS__, 'get_mc_lists_ajax'));

            // Templates
            add_action('admin_menu', array('UCP_templates', 'admin_menu'));
            add_action('admin_enqueue_scripts', array('UCP_templates', 'admin_enqueue_scripts'));
            add_action('wp_ajax_ucp_editor_load', array('UCP_templates', 'ucp_editor_load_template'));
            add_action('wp_ajax_ucp_editor_save', array('UCP_templates', 'ucp_editor_save_template'));
            add_action('wp_ajax_ucp_editor_save_thumbnail', array('UCP_templates', 'ucp_editor_save_template_thumbnail'));
            add_action('wp_ajax_ucp_editor_export', array('UCP_templates', 'ucp_editor_export_template'));
            add_action('wp_ajax_ucp_editor_reset', array('UCP_templates', 'ucp_editor_reset_template'));
            add_action('wp_ajax_ucp_editor_unsplash_api', array('UCP_templates', 'ucp_editor_unsplash_api'));
            add_action('wp_ajax_ucp_editor_unsplash_download', array('UCP_templates', 'ucp_editor_unsplash_download'));
            add_action('load-posts_page_ucp_editor', array('UCP_templates', 'ucp_editor_clean_admin_page'));
            add_action('wp_ajax_ucp_submit_form', array('UCP_templates', 'ucp_submit_form'));
            add_action('wp_ajax_nopriv_ucp_submit_form', array('UCP_templates', 'ucp_submit_form'));
            add_action('admin_action_ucp_activate_template', array('UCP_templates', 'activate_template'));
            add_action('admin_action_ucp_delete_template', array('UCP_templates', 'delete_template'));
            add_action('admin_action_ucp_install_template', array('UCP_templates', 'install_template'));
            add_action('admin_action_ucp_refresh_templates', array('UCP_templates', 'refresh_templates'));
            add_action('wp_ajax_ucp_install_template_js', array('UCP_templates', 'install_template_js'));
            add_action('wp_ajax_ucp_validate_license', array('UCP_templates', 'validate_license_ajax'));
            add_action('wp_ajax_ucp_install_template', array('UCP_templates', 'install_template'));

            add_action('wp_ajax_ucp_check_login', array('UCP_templates', 'check_password_unlock_ajax'));
            add_action('wp_ajax_nopriv_ucp_check_login', array('UCP_templates', 'check_password_unlock_ajax'));

            add_action('wp_ajax_ucp_refresh_templates', array('UCP_templates', 'refresh_templates_ajax'));
            add_action('wp_ajax_ucp_save_lc', array('UCP_templates', 'ucp_save_license_ajax'));

            add_filter('install_plugins_table_api_args_featured', array(__CLASS__, 'featured_plugins_tab'));
        } else {
            // main plugin logic
            add_action('wp', array(__CLASS__, 'display_construction_page'), 0, 1);

            // show under construction notice on login form
            add_filter('login_message', array(__CLASS__, 'login_message'));

            // disable feeds, RPC and REST API
            add_action('do_feed_rdf', array(__CLASS__, 'disable_feed'), 0, 1);
            add_action('do_feed_rss', array(__CLASS__, 'disable_feed'), 0, 1);
            add_action('do_feed_rss2', array(__CLASS__, 'disable_feed'), 0, 1);
            add_action('do_feed_atom', array(__CLASS__, 'disable_feed'), 0, 1);
            add_filter('xmlrpc_enabled', array(__CLASS__, 'disable_rpc'), 0, 1);
            add_filter('rest_authentication_errors', array(__CLASS__, 'disable_rest_api'), 1, 1);
            add_action('robots_txt', array(__CLASS__, 'robots_txt'), 0, 2);
        } // if not admin


        // add reference to custom DB tables
        self::register_custom_tables();

        // admin bar notice for frontend & backend
        add_action('wp_before_admin_bar_render', array(__CLASS__, 'admin_bar'));
        add_action('wp_head', array(__CLASS__, 'admin_bar_style'));
        add_action('admin_head', array(__CLASS__, 'admin_bar_style'));
    } // init

    // check if user has the minimal WP version required by UCP
    static function check_wp_version($min_version)
    {
        if (!version_compare(get_bloginfo('version'), $min_version,  '>=')) {
            add_action('admin_notices', array(__CLASS__, 'notice_min_wp_version'));
            return false;
        } else {
            return true;
        }
    } // check_wp_version


    // display error message if WP version is too low
    static function notice_min_wp_version()
    {
        echo '<div class="error"><p>' . sprintf(__('UnderConstruction plugin <b>requires WordPress version 4.6</b> or higher to function properly. You are using WordPress version %s. Please <a href="%s">update it</a>.', 'under-construction-page'), get_bloginfo('version'), admin_url('update-core.php')) . '</p></div>';
    } // notice_min_wp_version_error

    static function whitelabel_filter()
    {
        $options = self::get_options();

        if (!self::is_activated('agency')) {
            return true;
        }

        if ($options['whitelabel'] != 1) {
            return true;
        }

        return false;
    }

    // some things have to be loaded earlier
    static function plugins_loaded()
    {
        self::get_plugin_version();

        load_plugin_textdomain('under-construction-page');
    } // plugins_loaded


    // activate doesn't get fired on upgrades so we have to compensate
    public static function maybe_upgrade()
    {
        $meta = self::get_meta();
        if (empty($meta['database_ver'])) {
            self::create_custom_tables();
        }
    } // maybe_upgrade


    static function register_custom_tables()
    {
        global $wpdb;

        $wpdb->ucp_stats = $wpdb->prefix . 'ucp_stats';
        $wpdb->ucp_leads = $wpdb->prefix . 'ucp_leads';
        $wpdb->ucp_links = $wpdb->prefix . 'ucp_links';
        $wpdb->ucp_templates = $wpdb->prefix . 'ucp_templates';
    } // register_custom_tables


    // get plugin's options
    static function get_options()
    {
        $options = get_option(UCP_OPTIONS_KEY, array());

        if (!is_array($options)) {
            $options = array();
        }
        $options = array_merge(self::default_options(), $options);

        return $options;
    } // get_options



    // get plugin's meta data
    static function get_meta()
    {
        $meta = get_option(UCP_META_KEY, array());

        if (!is_array($meta) || empty($meta)) {
            $meta['first_version'] = self::get_plugin_version();
            $meta['first_install'] = current_time('timestamp');
            update_option(UCP_META_KEY, $meta);
        }

        return $meta;
    } // get_meta

    static function get_licence_type()
    {
        $options = self::get_options();

        if (stripos($options['license_type'], 'agency') !== false) {
            return 3;
        }
        if (stripos($options['license_type'], 'pro') !== false) {
            return 2;
        }
        if (stripos($options['license_type'], 'basic') !== false) {
            return 1;
        }

        return 0;
    } // get_license_type


    static function get_licence_name()
    {
        $options = self::get_options();

        if (stripos($options['license_type'], 'agency') !== false) {
            return 'AGENCY';
        }
        if (stripos($options['license_type'], 'pro') !== false) {
            return 'PRO';
        }
        if (stripos($options['license_type'], 'basic') !== false) {
            return 'BASIC';
        }

        return 'pro';
    } // get_license_name


    // check if license key is valid and not expired
    // todo
    static function is_activated($license_type = false)
    {
        $options = self::get_options();

        if (
            !empty($options['license_active']) && $options['license_active'] === true &&
            !empty($options['license_expires']) && $options['license_expires'] >= date('Y-m-d')
        ) {


            if (mt_rand(0, 100) > 98 && is_admin()) {
                $tmp = self::validate_license_key($options['license_key']);
                if ($tmp['success']) {
                    $update['license_type'] = $tmp['license_type'];
                    $update['license_expires'] = $tmp['license_expires'];
                    $update['license_active'] = $tmp['license_active'];
                    update_option(UCP_OPTIONS_KEY, array_merge($options, $update));
                }
            } // random license revalidation

            // check for specific license type?
            if (!empty($license_type)) {
                if (strtolower(trim($license_type)) == strtolower(self::get_licence_name())) {
                    return true;
                } else {
                    return false;
                }
            } // check specific license type

            return true;
        } else {
            return false;
        }
    } // is_activated


    // check if activation code is valid
    static function validate_license_key($code)
    {
        $out = array('success' => false, 'license_active' => false, 'license_key' => $code, 'error' => '', 'license_type' => '', 'license_expires' => '1900-01-01');
        $result = self::query_licensing_server('validate_license', array('license_key' => $code));

        if (false === $result) {
            $out['error'] = 'Unable to contact licensing server. Please try again in a few moments.';
        } elseif (!is_array($result['data']) || sizeof($result['data']) != 4) {
            $out['error'] = 'Invalid response from licensing server. Please try again later.';
        } else {
            $out['success'] = true;
            $out = array_merge($out, $result['data']);
        }

        return $out;
    } // validate_license_key

    static function validate_license_ajax()
    {
        $old_options = self::get_options();

        if (empty($_GET['license_key'])) {
            $new_options['license_type'] = '';
            $new_options['license_expires'] = '';
            $new_options['license_active'] = false;
            $new_options['license_key'] = '';
            self::add_settings_error(__('License key saved.', 'under-construction-page'), 'updated');
            update_option(UCP_OPTIONS_KEY, array_merge($old_options, $new_options));
            wp_send_json_success();
        } else {
            $tmp = self::validate_license_key($_GET['license_key']);
            $new_options['license_key'] = $options['license_key'];
            if ($tmp['success']) {
                $new_options['license_type'] = $tmp['license_type'];
                $new_options['license_expires'] = $tmp['license_expires'];
                $new_options['license_active'] = $tmp['license_active'];
                if ($tmp['license_active']) {
                    $new_options['license_key'] = $_GET['license_key'];
                    self::add_settings_error(__('License key saved and activated!', 'under-construction-page'), 'updated');
                } else {
                    self::add_settings_error('License not active. ' . $tmp['error'], 'error');
                }
                update_option(UCP_OPTIONS_KEY, array_merge($old_options, $new_options));
                wp_send_json_success();
            } else {
                wp_send_json_error();
            }
        }
    }

    static function ucp_save_license_ajax()
    {
        check_ajax_referer('ucp_save_lc');
        $old_options = self::get_options();

        if ($_POST['license_active'] == 'true') {
            $new_options['license_key'] = $_POST['license_key'];
            $new_options['license_type'] = $_POST['license_type'];
            $new_options['license_expires'] = $_POST['license_expires'];
            $new_options['license_active'] = true;
            self::add_settings_error(__('License key saved and activated!', 'under-construction-page'), 'updated');
            update_option(UCP_OPTIONS_KEY, array_merge($old_options, $new_options));
            wp_send_json_success();
        } else {
            $new_options['license_type'] = '';
            $new_options['license_expires'] = '';
            $new_options['license_active'] = false;
            $new_options['license_key'] = '';
            update_option(UCP_OPTIONS_KEY, array_merge($old_options, $new_options));
            wp_send_json_success();
        }
    }

    // run any query on licensing server
    static function query_licensing_server($action, $data = array(), $method = 'GET')
    {
        $options = self::get_options();
        $request_params = array('sslverify' => false, 'timeout' => 25, 'redirection' => 2);
        $default_data = array(
            'license_key' => $options['license_key'],
            'code_base' => 'pro',
            '_rand' => rand(1000, 9999),
            'version' => self::$version,
            'site' => get_home_url()
        );

        $request_data = array_merge($default_data, $data, array('action' => $action));

        $url = add_query_arg($request_data, self::$licensing_servers[0]);
        $response = wp_remote_get(esc_url_raw($url), $request_params);

        if (is_wp_error($response) || !($body = wp_remote_retrieve_body($response)) || !($result = @json_decode($body, true))) {
            $url = add_query_arg($request_data, self::$licensing_servers[0]);
            $response = wp_remote_get(esc_url_raw($url), $request_params);
            $body = wp_remote_retrieve_body($response);
            $result = @json_decode($body, true);
        }

        $result['success'] = true;

        if (!is_array($result) || !isset($result['success'])) {
            return false;
        } else {
            return $result;
        }
    } // query_licensing_server

    /**
     * Helper function for adding plugins to featured list
     *
     * @return array
     */
    static function featured_plugins_tab($args)
    {
        add_filter('plugins_api_result', array(__CLASS__, 'plugins_api_result'), 10, 3);
        return $args;
    } // featured_plugins_tab


    /**
     * Add plugins to featured plugins list
     *
     * @return object
     */
    static function plugins_api_result($res, $action, $args)
    {
        remove_filter('plugins_api_result', array(__CLASS__,'plugins_api_result'), 10, 3);

        
        $res = self::add_plugin_featured('eps-301-redirects', $res);
        $res = self::add_plugin_featured('wp-force-ssl', $res);
        
        return $res;
    } // plugins_api_result


    /**
     * Add single plugin to featured list
     *
     * @return object
     */
    static function add_plugin_featured($plugin_slug, $res)
    {
        // check if plugin is already on the list
        if (!empty($res->plugins) && is_array($res->plugins)) {
            foreach ($res->plugins as $plugin) {
                if (is_object($plugin) && !empty($plugin->slug) && $plugin->slug == $plugin_slug) {
                    return $res;
                }
            } // foreach
        }

        $plugin_info = get_transient('wf-plugin-info-' . $plugin_slug);

        if (!$plugin_info) {
            $plugin_info = plugins_api('plugin_information', array(
                'slug' => $plugin_slug,
                'is_ssl' => is_ssl(),
                'fields' => array(
                    'banners' => true,
                    'reviews' => true,
                    'downloaded' => true,
                    'active_installs' => true,
                    'icons' => true,
                    'short_description' => true,
                ),
            ));
            if (!is_wp_error($plugin_info)) {
                set_transient('wf-plugin-info-' . $plugin_slug, $plugin_info, DAY_IN_SECONDS * 7);
            }
        }

        if (!empty($res->plugins) && is_array($res->plugins) && $plugin_info && is_object($plugin_info)) {
            array_unshift($res->plugins, $plugin_info);
        }

        return $res;
    } // add_plugin_featured


    static function clean_access_link_from_url()
    {
        // missing or bad access key in URL - don't do anyting
        if (empty($_GET['ucp-access']) || strlen(trim($_GET['ucp-access'])) != 8) {
            return;
        }

        $secret_key = trim(strtolower($_GET['ucp-access']));

        // construct new link to redirect to
        global $wpdb;
        $url_params = $_GET;

        unset($url_params['ucp-access']);
        $path = strtolower(@parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $clean_url = untrailingslashit(get_bloginfo('url')) . trailingslashit($path) . http_build_query($url_params);

        $link = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->ucp_links . ' WHERE secret_key = %s LIMIT 1', $secret_key));

        // unknown access key - do nothing
        if (empty($link)) {
            return;
        }

        // date based key - expired - do nothing
        if ($link->expire_type == 'date' && $link->expire_value < current_time('mysql')) {
            unset($_SESSION['ucp_access_link']);
            return;
        }

        // session based key - expired - do nothing
        if ($link->expire_type == 'sessions' && $link->expire_value <= $link->sessions) {
            unset($_SESSION['ucp_access_link']);
            return;
        }

        // IP based key - expired - do nothing
        $ips = unserialize($link->ips);
        if (!is_array($ips)) {
            $ips = array();
            $ips_cnt = 0;
        } else {
            $ips_cnt = sizeof($ips);
        }

        // IP based key - expired and not on the list - do nothing
        if ($link->expire_type == 'ip' && $link->expire_value <= $ips_cnt && !isset($ips[UCP_utility::getUserIP()])) {
            unset($_SESSION['ucp_access_link']);
            return;
        }

        // we have a good link
        // if new link - increment count
        // save and redirect
        if (empty($_SESSION['ucp_access_link']) || $_SESSION['ucp_access_link']['id'] != $link->id) {
            $ips[UCP_utility::getUserIP()] = true;
            $wpdb->update($wpdb->ucp_links, array('sessions' => (int) $link->sessions + 1, 'ips' => serialize($ips)), array('id' => $link->id), array('%d', '%s'), array('%d'));
        }
        $_SESSION['ucp_access_link'] = array('id' => $link->id, 'secret_key' => $secret_key, 'expire_value' => $link->expire_value, 'expire_type' => $link->expire_type, 'ips' => $ips);

        wp_safe_redirect(site_url('/'));
        die;
    } // clean_access_link_from_url


    static function session_start()
    {
        if (!session_id()) {
            @session_start();
        }

        $time = time();
        $timeout_duration = 2 * HOUR_IN_SECONDS;

        if (isset($_SESSION['ucp_last_activity']) && ($time - $_SESSION['ucp_last_activity']) > $timeout_duration) {
            @session_unset();
            @session_destroy();
            @session_start();
        }

        $_SESSION['ucp_last_activity'] = $time;
    } // session_start


    // detect, check and redirect if direct access key is used
    static function check_direct_access_link()
    {
        global $wpdb;

        self::session_start();

        self::clean_access_link_from_url();

        if (empty($_SESSION['ucp_access_link'])) {
            $link = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->ucp_links . ' WHERE expire_type = %s AND ips LIKE %s ORDER BY id DESC LIMIT 1', 'ip', '%' . UCP_utility::getUserIP() . '%'));

            if ($link) {
                $wpdb->update($wpdb->ucp_links, array('sessions' => (int) $link->sessions + 1), array('id' => $link->id), array('%d'), array('%d'));
                $_SESSION['ucp_access_link'] = array('id' => $link->id, 'secret_key' => $link->secret_key, 'expire_value' => $link->expire_value, 'expire_type' => $link->expire_type, 'ips' => unserialize($link->ips));
            } else {
                // no active session, no whitelisted IP == no access
                return false;
            }
        }

        $link = $_SESSION['ucp_access_link'];

        // we already let the person in, so we must let him do AJAX too, regardless of time/sessions passed in the mean time
        // DOING_AJAX is already checked so this won't be run if it's an AJAX call

        // date based - expired - not whitelisted
        if ($link['expire_type'] == 'date' && $link['expire_value'] < current_time('mysql')) {
            return false;
        }

        // session are checked and incremented when set
        // no need to check again - they are valid till timeout

        return true;
    } // validate_direct_access_link



    static function check_password_unlock_ajax()
    {
        $options = self::get_options();
        $password = $_POST['pass'];

        if ($password == $options['direct_access_password']) {
            $_SESSION['ucp_direct_access_password'] = true;
            self::clear_3rdparty_cache();
            wp_send_json_success();
        } else if ($password == $options['site_password']) {
            $_SESSION['ucp_access_password'] = true;
            self::clear_3rdparty_cache();
            wp_send_json_success();
        } else {
            wp_send_json_error('Invalid password!');
        }
    }

    static function check_direct_access_password_unlock()
    {
        if (isset($_SESSION['ucp_direct_access_password']) && $_SESSION['ucp_direct_access_password'] == true) {
            return true;
        } else {
            return false;
        }
    }

    static function check_ucp_access_password_unlock()
    {
        if (isset($_SESSION['ucp_access_password']) && $_SESSION['ucp_access_password'] === true) {
            return true;
        } else {
            return false;
        }
    }

    static function check_ucp_password_locked()
    {
        $options = self::get_options();
        if (isset($options['site_password']) && strlen($options['site_password']) > 0 && !self::check_ucp_access_password_unlock() && !(current_user_can('manage_options') && isset($_GET['ucp_editing']))) {
            return true;
        }

        return false;
    }

    static function check_direct_access_locked()
    {
        $options = self::get_options();

        if (isset($options['direct_access_password']) && strlen($options['direct_access_password']) > 0 && !self::check_direct_access_password_unlock() && !(current_user_can('manage_options') && isset($_GET['ucp_editing']))) {
            return true;
        }

        return false;
    }

    // send support message
    static function submit_support_message_ajax()
    {
        check_ajax_referer('ucp_submit_support_message');

        $options = self::get_options();

        $email = sanitize_text_field($_POST['support_email']);
        if (!is_email($email)) {
            wp_send_json_error('Please double-check your email address.');
        }

        $message = stripslashes(sanitize_text_field($_POST['support_message']));
        $subject = 'UCP Support';
        $body = $message;
        $theme = wp_get_theme();
        $body .= "\r\n\r\nSite details:\r\n";
        $body .= '  WordPress version: ' . get_bloginfo('version') . "\r\n";
        $body .= '  UCP version: ' . self::$version . "\r\n";
        $body .= '  Site URL: ' . get_bloginfo('url') . "\r\n";
        $body .= '  WordPress URL: ' . get_bloginfo('wpurl') . "\r\n";
        $body .= '  Theme: ' . $theme->get('Name') . ' v' . $theme->get('Version') . "\r\n";
        if (self::is_activated()) {
            $body .= '  License key: ' . $options['license_key'] . "\r\n";;
            $body .= '  License details: ' . $options['license_type'] . ', expires on ' . $options['license_expires'] . "\r\n";;
        } else {
            $body .= '  License key: ' . (empty($options['license_key']) ? 'n/a' : $options['license_key']) . "\r\n";
        }
        $headers = 'From: ' . $email . "\r\n" . 'Reply-To: ' . $email;

        if (true === wp_mail('ucp@webfactoryltd.com', $subject, $body, $headers)) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Something is not right with your wp_mail() function. Please email as directly at ucp@webfactoryltd.com.');
        }
    } // submit_support_message


    // fetch and display the construction page if it's enabled or preview requested
    static function display_construction_page()
    {
        $options = self::get_options();
        $request_uri = UCP_utility::slashit(strtolower(@parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));

        // never run UCP when these constants are set
        if (defined('DOING_CRON') && DOING_CRON) {
            return false;
        }
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return false;
        }
        if (defined('WP_CLI') && WP_CLI) {
            return false;
        }

        // some URLs have to be accessible at all times
        if (
            $request_uri == '/wp-admin/' ||
            $request_uri == '/feed/' ||
            $request_uri == '/feed/rss/' ||
            $request_uri == '/feed/rss2/' ||
            $request_uri == '/feed/rdf/' ||
            $request_uri == '/feed/atom/' ||
            $request_uri == '/admin/' ||
            $request_uri == '/robots.txt' ||
            $request_uri == '/wp-login.php'
        ) {
            return false;
        }

        if (
            true == self::is_construction_mode_enabled(false)
            || (is_user_logged_in() && isset($_GET['ucp_preview']))
        ) {

            UCP_affiliate::check_affiliate_link();
            if ($options['redirect']) {
                wp_redirect($options['redirect'], 302);
            } else {
                header(UCP_utility::wp_get_server_protocol() . ' ' . $options['http_response_code']);
                if (substr($options['http_response_code'], 0, 3) == '503') {
                    if ($options['end_date']) {
                        header('Retry-After: ' . date('D, d M Y H:i:s T', strtotime($options['end_date'])));
                    } else {
                        header('Retry-After: ' . DAY_IN_SECONDS / 2);
                    }
                }
                if ($options['no_index']) {
                    header('X-Robots-Tag: noindex');
                }
                if ($options['send_nocache_headers']) {
                    header('Expires: Tue, 03 Jul 2001 06:00:00 GMT');
                    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
                    header('Pragma: no-cache');
                }
                UCP_stats::add_visit();
                UCP_templates::display_template($options['theme']);
            }
            exit;
        } else if (isset($_GET['ucp_template_preview'])) {
            UCP_templates::display_template();
            exit;
        } // UCP is enabled
    } // display_construction_page


    // discurage search engines
    static function robots_txt($output, $public)
    {
        $options = self::get_options();

        if ($public && $options['no_index'] && true == self::is_construction_mode_enabled(false)) {
            $output .= 'Disallow: /';
        }

        return $output;
    } // robots_txt


    // disables feed if necessary
    static function disable_feed()
    {
        $options = self::get_options();

        if ($options['disable_feeds'] && true == self::is_construction_mode_enabled(false)) {
            echo '<?xml version="1.0" encoding="UTF-8" ?><status>Service unavailable.</status>';
            exit;
        }
    } // disable_feed


    // disables RPC if necessary
    static function disable_rpc()
    {
        $options = self::get_options();

        if ($options['disable_rpc'] && true == self::is_construction_mode_enabled(false)) {
            return false;
        } else {
            return true;
        }
    } // disable_rpc


    // disables REST if necessary
    static function disable_rest_api($access)
    {
        $options = self::get_options();

        if (
            $options['disable_rest_api'] &&
            !is_user_logged_in() &&
            true == self::is_construction_mode_enabled(false)
        ) {
            return new WP_Error('rest_cannot_access', __('Only authenticated users can access the REST API.', 'under-construction-page'), array('status' => rest_authorization_required_code()));
        }

        return $access;
    } // disable_rest_api

    static function admin_dequeue_scripts($hook)
    {
        if ('settings_page_ucp' == $hook || 'posts_page_ucp_editor' == $hook) {
            wp_dequeue_style('widgetopts-admin-styles');
            wp_enqueue_style('widgetopts-jquery-ui');
            wp_deregister_style('widgetopts-admin-styles');
            wp_deregister_style('widgetopts-jquery-ui');
            wp_deregister_script('admin_uncode_js');
            wp_dequeue_script('admin_uncode_js');
        }
    }
    // enqueue CSS and JS scripts in admin
    static function admin_enqueue_scripts($hook)
    {
        $meta = self::get_meta();

        $js_localize = array(
            'undocumented_error' => __('An undocumented error has occured. Please refresh the page and try again.', 'under-construction-page'),
            'plugin_name' => __('UnderConstructionPage', 'under-construction-page'),
            'plugin_url' => UCP_PLUGIN_URL,
            'settings_url' => admin_url('options-general.php?page=ucp'),
            'whitelisted_users_placeholder' => __('Select whitelisted user(s)', 'under-construction-page'),
            'nonce_submit_support_message' => wp_create_nonce('ucp_submit_support_message'),
            'nonce_affiliate_links' => wp_create_nonce('ucp_affiliate_links'),
            'nonce_access_links' => wp_create_nonce('ucp_access_links'),
            'code_base' => 'pro',
            'version' => self::$version,
            'site' => get_home_url(),
            'nonce_lc_check' => wp_create_nonce('ucp_save_lc'),
            'nonce_ucp_refresh_templates' => wp_create_nonce('ucp_refresh_templates'),
            'lc_ep' => self::$licensing_servers[0],
            'weglot_dialog_upsell_title' => '<img alt="' . __('Weglot', 'under-construction-page') . '" title="' . __('Weglot', 'under-construction-page') . '" src="' . UCP_PLUGIN_URL . 'images/weglot-logo-white.png' . '">',
            'weglot_install_url' => add_query_arg(array('action' => 'install_weglot'), admin_url('admin.php')),
            'deactivate_confirmation' => __('Are you sure you want to deactivate UnderConstruction plugin?' . "\n" . 'If you are removing it because of a problem please contact our support. They will be more than happy to help.', 'under-construction-page')
        );


        if ('settings_page_ucp' == $hook) {
            $templates = get_option(UCP_TEMPLATES_KEY);
            if (is_array($templates) && $templates['updated'] < (time() - 84000)) {
                $js_localize['refresh_templates'] = true;
            }

            wp_enqueue_style('wp-jquery-ui-dialog');
            wp_enqueue_style('ucp-select2', UCP_PLUGIN_URL . 'css/select2.min.css', array(), self::$version);
            wp_enqueue_style('ucp-admin', UCP_PLUGIN_URL . 'css/ucp-admin.css', array(), self::$version);
            wp_enqueue_style('ucp-dataTables', UCP_PLUGIN_URL . 'css/jquery.dataTables.min.css', array(), self::$version);
            wp_enqueue_style('ucp-font-awesome', UCP_PLUGIN_URL . 'css/font-awesome/font-awesome.min.css', array(), UCP::$version);

            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-dialog');
            wp_enqueue_script('ucp-jquery-plugins', UCP_PLUGIN_URL . 'js/ucp-jquery-plugins.js', array('jquery'), self::$version, true);
            wp_enqueue_script('ucp-select2', UCP_PLUGIN_URL . 'js/select2.min.js', array(), self::$version, true);
            wp_enqueue_script('ucp-admin', UCP_PLUGIN_URL . 'js/ucp-admin.js', array('jquery'), self::$version, true);
            wp_enqueue_script('ucp-clipboard', UCP_PLUGIN_URL . 'js/clipboard.min.js', array(), self::$version, true);
            wp_enqueue_script('ucp-dataTables', UCP_PLUGIN_URL . 'js/jquery.dataTables.min.js', array(), self::$version, true);

            wp_enqueue_script('ucp-dataTables-buttons', UCP_PLUGIN_URL . 'js/dataTables.buttons.min.js', array(), self::$version, true);
            wp_enqueue_script('ucp-dataTables-jszip', UCP_PLUGIN_URL . 'js/jszip.min.js', array(), self::$version, true);
            wp_enqueue_script('ucp-dataTables-buttons-flash', UCP_PLUGIN_URL . 'js/buttons.flash.min.js', array(), self::$version, true);
            wp_enqueue_script('ucp-dataTables-pdfmake', UCP_PLUGIN_URL . 'js/pdfmake.min.js', array(), self::$version, true);
            wp_enqueue_script('ucp-dataTables-vfs_fonts', UCP_PLUGIN_URL . 'js/vfs_fonts.js', array(), self::$version, true);
            wp_enqueue_script('ucp-dataTables-buttons-html5', UCP_PLUGIN_URL . 'js/buttons.html5.min.js', array(), self::$version, true);
            wp_enqueue_script('ucp-dataTables-print', UCP_PLUGIN_URL . 'js/buttons.print.min.js', array(), self::$version, true);

            $tmp_stats = UCP_stats::get_data('chart');

            if ($tmp_stats['totals']['days'] >= 2 && self::is_activated()) {
                $js_localize['stats'] = $tmp_stats;
                wp_enqueue_script('ucp-moment', UCP_PLUGIN_URL . 'js/moment.min.js', array(), self::$version, true);
                wp_enqueue_script('ucp-chart', UCP_PLUGIN_URL . 'js/chart.min.js', array(), self::$version, true);
            }

            wp_localize_script('ucp-admin', 'ucp_vars', $js_localize);

            // fix for aggressive plugins
            wp_dequeue_style('uiStyleSheet');
            wp_dequeue_style('wpcufpnAdmin');
            wp_dequeue_style('unifStyleSheet');
            wp_dequeue_style('wpcufpn_codemirror');
            wp_dequeue_style('wpcufpn_codemirrorTheme');
            wp_dequeue_style('collapse-admin-css');
            wp_dequeue_style('jquery-ui-css');
            wp_dequeue_style('tribe-common-admin');
            wp_dequeue_style('file-manager__jquery-ui-css');
            wp_dequeue_style('file-manager__jquery-ui-css-theme');
            wp_dequeue_style('wpmegmaps-jqueryui');
            wp_dequeue_style('wp-botwatch-css');
            wp_dequeue_style('jquery-ui-css');
        }

        if ('plugins.php' == $hook) {
            wp_enqueue_script('ucp-admin-plugins', UCP_PLUGIN_URL . 'js/ucp-admin-plugins.js', array('jquery'), self::$version, true);
            wp_localize_script('ucp-admin-plugins', 'ucp_vars', $js_localize);
        }

        $pointers = get_option(UCP_POINTERS_KEY);
        if ($pointers && 'settings_page_ucp' != $hook) {
            $pointers['_nonce_dismiss_pointer'] = wp_create_nonce('ucp_dismiss_pointer');
            wp_enqueue_script('wp-pointer');
            wp_enqueue_script('ucp-pointers', plugins_url('js/ucp-admin-pointers.js', __FILE__), array('jquery'), self::$version, true);
            wp_enqueue_style('wp-pointer');
            wp_localize_script('wp-pointer', 'ucp_pointers', $pointers);
            wp_localize_script('jquery', 'ucp', $js_localize);
        }
    } // admin_enqueue_scripts


    // permanently dismiss a pointer
    static function dismiss_pointer_ajax()
    {
        check_ajax_referer('ucp_dismiss_pointer');

        $pointers = get_option(UCP_POINTERS_KEY);
        $pointer = trim($_POST['pointer']);

        if (empty($pointers) || empty($pointers[$pointer])) {
            wp_send_json_error();
        }

        unset($pointers[$pointer]);
        update_option(UCP_POINTERS_KEY, $pointers);

        wp_send_json_success();
    } // dismiss_pointer_ajax


    // all AJAX actions for access links
    // add, edit, delete, delete all
    static function access_links_ajax()
    {
        check_ajax_referer('ucp_access_links');

        global $wpdb;
        $action = trim($_POST['sub_action']);

        switch ($action) {
            case 'add':
                do {
                    $key = substr(md5(time() . rand()), 0, 8);
                    $test = $wpdb->get_var($wpdb->prepare('SELECT id FROM ' . $wpdb->ucp_links . ' WHERE secret_key = %s', $key));
                } while ($test);

                $name = trim(stripslashes($_POST['link_name']));
                $expire_type = trim($_POST['link_expire_type']);
                if (empty($expire_type)) {
                    $expire_value = '';
                } elseif ($expire_type == 'date') {
                    $expire_value = substr($_POST['link_expire_value2'], 0, 16);
                } else {
                    $expire_value = (int) $_POST['link_expire_value'];
                }

                $data = array('name' => $name, 'type' => 'access', 'created' => current_time('mysql'), 'secret_key' => $key, 'expire_type' => $expire_type, 'expire_value' => $expire_value, 'sessions' => 0);
                $new_link = $wpdb->insert($wpdb->ucp_links, $data, array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d'));

                if ($new_link) {
                    wp_send_json_success($new_link);
                } else {
                    wp_send_json_error('Unable to add new link. Please reload the page and try again.');
                }
                break;
            case 'edit':
                $link_id = (int) $_POST['link_id'];
                $name = trim(stripslashes($_POST['link_name']));
                $expire_type = trim($_POST['link_expire_type']);
                if (empty($expire_type)) {
                    $expire_value = '';
                } elseif ($expire_type == 'date') {
                    $expire_value = substr($_POST['link_expire_value2'], 0, 16);
                } else {
                    $expire_value = (int) $_POST['link_expire_value'];
                }

                $data = array('name' => $name, 'modified' => current_time('mysql'), 'expire_type' => $expire_type, 'expire_value' => $expire_value);
                $edit_link = $wpdb->update($wpdb->ucp_links, $data, array('id' => $link_id), array('%s', '%s', '%s', '%s'), array('%d'));

                if ($edit_link) {
                    wp_send_json_success($edit_link);
                } else {
                    wp_send_json_error('Unable to edit access link. Please reload the page and try again.');
                }
                break;
            case 'delete':
                $link_id = (int) $_POST['link_id'];
                $affected = $wpdb->delete($wpdb->ucp_links, array('id' => $link_id), array('%d'));
                if ($affected == 1) {
                    wp_send_json_success();
                } else {
                    wp_send_json_error('Unable to delete access link. Please reload the page and try again.');
                }
                break;
            case 'delete_all':
                $wpdb->query('DELETE FROM ' . $wpdb->ucp_links . ' WHERE type = "access"');
                wp_send_json_success();
                break;
            default:
                wp_send_json_error('Unknown action');
        } // switch

        wp_send_json_error('Unknown error.');
    } // access_links_ajax


    // all AJAX actions for affiliate links
    // add, edit, delete, delete all
    static function affiliate_links_ajax()
    {
        check_ajax_referer('ucp_affiliate_links');

        global $wpdb;
        $action = trim($_POST['sub_action']);

        switch ($action) {
            case 'add':
                do {
                    $key = substr(md5(time() . rand()), 0, 8);
                    $test = $wpdb->get_var($wpdb->prepare('SELECT id FROM ' . $wpdb->ucp_links . ' WHERE secret_key = %s', $key));
                } while ($test);

                $name = trim(stripslashes($_POST['link_name']));

                $data = array('name' => $name, 'type' => 'affiliate', 'created' => current_time('mysql'), 'secret_key' => $key);
                $new_link = $wpdb->insert($wpdb->ucp_links, $data, array('%s', '%s', '%s', '%s'));

                if ($new_link) {
                    wp_send_json_success($new_link);
                } else {
                    wp_send_json_error('Unable to add new link. Please reload the page and try again.');
                }
                break;
            case 'edit':
                $link_id = (int) $_POST['link_id'];
                $name = trim(stripslashes($_POST['link_name']));

                $data = array('name' => $name, 'modified' => current_time('mysql'));
                $edit_link = $wpdb->update($wpdb->ucp_links, $data, array('id' => $link_id), array('%s', '%s'), array('%d'));

                if ($edit_link) {
                    wp_send_json_success($edit_link);
                } else {
                    wp_send_json_error('Unable to edit affiliate link. Please reload the page and try again.');
                }
                break;
            case 'delete':
                $link_id = (int) $_POST['link_id'];
                $affected = $wpdb->delete($wpdb->ucp_links, array('id' => $link_id), array('%d'));
                if ($affected == 1) {
                    wp_send_json_success();
                } else {
                    wp_send_json_error('Unable to delete affiliate link. Please reload the page and try again.');
                }
                break;
            case 'delete_all':
                $wpdb->query('DELETE FROM ' . $wpdb->ucp_links . ' WHERE type = "affiliate"');
                wp_send_json_success();
                break;
            default:
                wp_send_json_error('Unknown action');
        } // switch

        wp_send_json_error('Unknown error.');
    } // affiliate_links_ajax

    //delete lead
    static function delete_lead()
    {
        global $wpdb;

        $wpdb->query('DELETE FROM ' . $wpdb->ucp_leads . ' WHERE id="' . $_GET['leadid'] . '"');

        self::add_settings_error('Lead has been deleted.', 'notice-info');

        if (!empty($_GET['redirect'])) {
            if (strpos($_GET['redirect'], 'settings-updated=true') == false) {
                $_GET['redirect'] .= '&settings-updated=true';
            }
            wp_safe_redirect($_GET['redirect']);
        } else {
            wp_safe_redirect(admin_url());
        }

        exit;
    } // delete_lead

    //delete all leads
    static function delete_all_leads()
    {
        global $wpdb;

        $wpdb->query('DELETE FROM ' . $wpdb->ucp_leads);

        self::add_settings_error('All leads have been deleted.', 'notice-info');

        if (!empty($_GET['redirect'])) {
            if (strpos($_GET['redirect'], 'settings-updated=true') == false) {
                $_GET['redirect'] .= '&settings-updated=true';
            }
            wp_safe_redirect($_GET['redirect']);
        } else {
            wp_safe_redirect(admin_url());
        }

        exit;
    } // delete_all_leads

    // change status via admin bar
    static function change_status()
    {
        if (false === current_user_can('administrator') || empty($_GET['new_status']) || false === check_admin_referer('ucp_change_status')) {
            wp_safe_redirect(admin_url());
            exit;
        }

        $options = self::get_options();

        if ($_GET['new_status'] == 'enabled') {
            $options['status'] = '1';
        } else {
            $options['status'] = '0';
        }

        update_option(UCP_OPTIONS_KEY, $options);

        if (!empty($_GET['redirect'])) {
            wp_safe_redirect($_GET['redirect']);
        } else {
            wp_safe_redirect(admin_url());
        }

        exit;
    } // change_status


    static function add_settings_error($message, $type = 'error', $code = 'ucp')
    {
        global $wp_settings_errors;

        $wp_settings_errors[] = array(
            'setting' => UCP_OPTIONS_KEY,
            'code'    => $code,
            'message' => $message,
            'type'    => $type
        );
        set_transient('settings_errors', $wp_settings_errors);
    } // add_settings_error


    // todo
    static function reset_settings($redirect = true)
    {
        global $wpdb;

        $options_default = self::default_options();
        $options = self::get_options();

        $options_default['status'] = $options['status'];
        $options_default['license_key'] = $options['license_key'];
        $options_default['license_active'] = $options['license_active'];
        $options_default['license_expires'] = $options['license_expires'];
        $options_default['license_type'] = $options['license_type'];

        update_option(UCP_OPTIONS_KEY, $options_default);

        $wpdb->query('TRUNCATE TABLE ' . $wpdb->ucp_links);

        if (false === $redirect) {
            return true;
        }

        self::add_settings_error('All settings have been reset.', 'notice-info');

        if (!empty($_GET['redirect'])) {
            if (strpos($_GET['redirect'], 'settings-updated=true') == false) {
                $_GET['redirect'] .= '&settings-updated=true';
            }
            wp_safe_redirect($_GET['redirect']);
        } else {
            wp_safe_redirect(admin_url());
        }

        exit;
    } // reset_setings


    // parse shortcode alike variables
    static function parse_vars($string)
    {
        $org_string = $string;

        $vars = array(
            'site-title' => get_bloginfo('name'),
            'site-tagline' => get_bloginfo('description'),
            'site-description' => get_bloginfo('description'),
            'site-url' => trailingslashit(get_home_url()),
            'wp-url' => trailingslashit(get_site_url()),
            'site-login-url' => get_site_url() . '/wp-login.php'
        );

        foreach ($vars as $var_name => $var_value) {
            $var_name = '[' . $var_name . ']';
            $string = str_ireplace($var_name, $var_value, $string);
        }

        $string = apply_filters('ucp_parse_vars', $string, $org_string, $vars);

        return $string;
    } // parse_vars


    // shortcode for inserting things in header
    static function generate_head($options, $template_id)
    {
        $out = '';

        $out .= '<link rel="stylesheet" href="' . trailingslashit(UCP_PLUGIN_URL . 'themes') . 'css/bootstrap.min.css?v=' . self::$version . '" type="text/css">' . "\n";
        $out .= '<link rel="stylesheet" href="' . trailingslashit(UCP_PLUGIN_URL . 'themes') . 'css/common.css?v=' . self::$version . '" type="text/css">' . "\n";
        $out .= '<link rel="stylesheet" href="' . trailingslashit(UCP_PLUGIN_URL . 'themes/' . $template_id) . 'style.css?v=' . self::$version . '" type="text/css">' . "\n";

        $out .= '<link rel="stylesheet" href="https://opensource.keycdn.com/fontawesome/4.7.0/font-awesome.min.css" integrity="sha384-dNpIIXE8U05kAbPhy3G1cz+yZmTzA6CY8Vg/u2L9xRnHjJiAK76m2BIEaSEV+/aU" crossorigin="anonymous">';

        if (!empty($options['ga_tracking_id'])) {
            $out .= "
      <script type=\"text/javascript\">
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
        ga('create', '{$options['ga_tracking_id']}', 'auto');
        ga('send', 'pageview');
      </script>";
        }

        if (!empty($options['custom_css'])) {
            $out .= "\n" . '<style type="text/css">' . $options['custom_css'] . '</style>';
        }

        if ($options['no_index']) {
            $out .= "\n" . '<meta name="robots" content="noindex,follow" />';
        }

        $out = apply_filters('ucp_head', $out, $options, $template_id);

        return trim($out);
    } // generate_head


    // shortcode for inserting things in footer
    static function generate_footer($options, $template_id)
    {
        $out = '';

        if ($options['login_button'] == '1') {
            if (is_user_logged_in()) {
                $out .= '<div id="login-button" class="loggedin">';
                $out .= '<a title="' . __('Open WordPress admin', 'under-construction-page') . '" href="' . get_site_url() . '/wp-admin/"><i class="fa fa-wordpress fa-2x" aria-hidden="true"></i></a>';
            } else {
                $out .= '<div id="login-button" class="loggedout">';
                $out .= '<a title="' . __('Log in to WordPress admin', 'under-construction-page') . '" href="' . get_site_url() . '/wp-login.php"><i class="fa fa-wordpress fa-2x" aria-hidden="true"></i></a>';
            }
            $out .= '</div>';
        }

        // todo handle properly
        if (!empty($options['ga_tracking_id']) && $options['ga_track_events']) {
            $out .= "\n" . '<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>';

            $out .= "
      <script type=\"text/javascript\">
      jQuery(function(\$){
  \$('a:not([href*=\"' + document.domain + '\"])').click(function(event){
if (typeof ga == 'undefined') {
      return;
    }
url = \$(this).attr('href');
text = \$(this).text() || url;
ga('send', 'event', 'outbound-link', url, url, \$(this).text());
  });  });
      </script>";
        }

        $out = apply_filters('ucp_footer', $out, $options, $template_id);

        return $out;
    } // generate_footer


    // returnes parsed template
    static function get_template($template_id)
    {
        $vars = array();
        $options = self::get_options();

        $vars['version'] = self::$version;
        $vars['site-url'] = trailingslashit(get_home_url());
        $vars['wp-url'] = trailingslashit(get_site_url());
        $vars['theme-url'] = trailingslashit(UCP_PLUGIN_URL . 'themes/' . $template_id);
        $vars['theme-url-common'] = trailingslashit(UCP_PLUGIN_URL . 'themes');
        $vars['title'] = self::parse_vars($options['title']);
        $vars['generator'] = __('Free UnderConstructionPage plugin for WordPress', 'under-construction-page');
        $vars['heading1'] = self::parse_vars($options['heading1']);
        $vars['content'] = nl2br(self::parse_vars($options['content']));
        $vars['description'] = self::parse_vars($options['description']);
        $vars['head'] = self::generate_head($options, $template_id);
        $vars['footer'] = self::generate_footer($options, $template_id);

        $vars = apply_filters('ucp_get_template_vars', $vars, $template_id, $options);

        ob_start();
        require UCP_PLUGIN_DIR . 'themes/' . $template_id . '/index.php';
        $template = ob_get_clean();

        foreach ($vars as $var_name => $var_value) {
            $var_name = '[' . $var_name . ']';
            $template = str_ireplace($var_name, $var_value, $template);
        }

        $template = apply_filters('ucp_get_template', $template, $vars, $options);

        return $template;
    } // get_template


    // checks if construction mode is enabled for the current visitor
    static function is_construction_mode_enabled($settings_only = false)
    {
        $options = self::get_options();
        $current_user = wp_get_current_user();
        $request_uri = UCP_utility::slashit(strtolower(@parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));

        // just check if it's generally enabled
        if ($settings_only) {
            if ($options['status']) {
                return true;
            } else {
                return false;
            }
        }

        $override_status = apply_filters('ucp_is_construction_mode_enabled', null, $options);
        if (is_bool($override_status)) {
            return $override_status;
        }

        // generally enabled
        if (!$options['status']) {
            return false;
        }

        // whitelisted / blacklisted URLs
        if ($options['url_rules_type'] == 'whitelist' && !empty($options['url_rules'])) {
            if (is_array($options['url_rules']) && in_array($request_uri, $options['url_rules'])) {
                return false;
            }
        } elseif ($options['url_rules_type'] == 'blacklist' && !empty($options['url_rules'])) {
            if (!in_array($request_uri, $options['url_rules'])) {
                return false;
            }
        }

        // whitelisted by direct access link
        if (self::check_direct_access_link()) {
            return false;
        }

        // direct acess password entered
        if (self::check_direct_access_password_unlock()) {
            return false;
        }

        // whitelisted IP
        if (is_array($options['whitelisted_ips']) && in_array(UCP_utility::getUserIP(), $options['whitelisted_ips'])) {
            return false;
        }

        // user logged in and role whitelisted
        if (self::user_has_role($options['whitelisted_roles'])) {
            return false;
        }

        // user logged in and whitelisted
        if (in_array($current_user->ID, $options['whitelisted_users'])) {
            return false;
        }

        // date - auto start / end
        if (strlen($options['end_date']) === 16 && $options['end_date'] < current_time('Y-m-d H:i')) {
            return false;
        }
        if (strlen($options['start_date']) === 16 && $options['start_date'] > current_time('Y-m-d H:i')) {
            return false;
        }

        return true;
    } // is_construction_mode_enabled


    // check if user has the specified role
    static function user_has_role($roles)
    {
        $current_user = wp_get_current_user();

        if ($current_user->roles) {
            $user_role = $current_user->roles[0];
        } else {
            $user_role = 'guest';
        }

        return in_array($user_role, $roles);
    } // user_has_role


    // displays various notices in admin header
    static function admin_notices()
    {
        global $wpdb;

        $options = self::get_options();
        $notices = get_option(UCP_NOTICES_KEY);
        $meta = self::get_meta();



        if (self::is_construction_mode_enabled(true)) {
            $themes = $wpdb->get_results('SELECT * FROM ' . $wpdb->ucp_templates . ' WHERE type = "user" AND slug="' . $options['theme'] . '"');

            if ($themes == false) {
                echo '<div id="ucp_end_date_notice" class="notice-error notice"><p>Under construction mode is enabled but you have not marked any template as Active! Open <a href="' . admin_url('options-general.php?page=ucp') . '" class="change_tab" data-tab="design" data-tab2="pages">Settings -> Appearance -> Your Pages</a> and click on the Activate button on the template you wish to use.</p></div>';
            }
        }
        if (self::is_plugin_page() && self::is_construction_mode_enabled(true) && !empty($options['end_date']) && $options['end_date'] != '0000-00-00 00:00' && $options['end_date'] < current_time('mysql')) {
            echo '<div id="ucp_end_date_notice" class="notice-error notice"><p>Under construction mode is enabled but the <a href="#end_date" class="change_tab" data-tab="main">end date</a> is set to a past date so the <b>under construction page will not be shown</b>. Either move the <a href="#end_date" class="change_tab" data-tab="0">end date</a> to a future date or disable it.</p></div>';
        }

        $_GET['settings-updated'] = true;
    } // notices


    // handle dismiss button for notices
    static function dismiss_notice()
    {
        if (empty($_GET['notice'])) {
            wp_safe_redirect(admin_url());
            exit;
        }

        $notices = get_option(UCP_NOTICES_KEY, array());

        if ($_GET['notice'] == 'rate') {
            $notices['dismiss_rate'] = true;
        }

        update_option(UCP_NOTICES_KEY, $notices);

        if (!empty($_GET['redirect'])) {
            wp_safe_redirect($_GET['redirect']);
        } else {
            wp_safe_redirect(admin_url());
        }

        exit;
    } // dismiss_notice


    static function admin_bar_style()
    {
        // admin bar has to be anabled, user an admin and custom filter true
        if (false === is_admin_bar_showing() || false === current_user_can('administrator') || false === apply_filters('ucp_show_admin_bar', true)) {
            return;
        }

        // no sense in loading a new CSS file for 2 lines of CSS
        $custom_css = '<style type="text/css">#wpadminbar ul li#wp-admin-bar-ucp-info { padding: 5px 0; } #wpadminbar ul li#wp-admin-bar-ucp-settings, #wpadminbar ul li#wp-admin-bar-ucp-status { } #wpadminbar i.ucp-status-dot { font-size: 17px; margin-top: -7px; color: #02ca02; height: 17px; display: inline-block; } #wpadminbar i.ucp-status-dot-enabled { color: #87c826; } #wpadminbar i.ucp-status-dot-disabled { color: #ea1919; } #wpadminbar #ucp-status-wrapper { display: inline; border: 1px solid rgba(240,245,250,.7); padding: 0; margin: 0 0 0 5px; background: rgb(35, 40, 45); } #wpadminbar .ucp-status-btn { padding: 0 7px; color: #fff; } #wpadminbar #ucp-status-wrapper.off #ucp-status-off { background: #ea1919;} #wpadminbar #ucp-status-wrapper.on #ucp-status-on { background: #66b317; }#wp-admin-bar-under-construction-page img.logo { height: 17px; margin-bottom: -3px; padding-right: 3px; } body.wp-admin #wp-admin-bar-under-construction-page img.logo { height: 17px; margin-bottom: -4px; padding-right: 3px; }</style>';

        echo $custom_css;
    } // admin_bar_style


    // add admin bar menu and status
    static function admin_bar()
    {
        global $wp_admin_bar;

        // only show to admins
        if (false === current_user_can('administrator') || false === apply_filters('ucp_show_admin_bar', true)) {
            return;
        }

        if (self::is_construction_mode_enabled(true)) {
            $main_label = '<img class="logo" src="' . UCP_PLUGIN_URL . '/images/ucp_icon.png" alt="' . __('Under construction mode is enabled', 'under-construction-page') . '" title="' . __('Under construction mode is enabled', 'under-construction-page') . '"> <span class="ab-label">' . __('UnderConstruction', 'under-construction-page') . ' <i class="ucp-status-dot ucp-status-dot-enabled">&#9679;</i></span>';
            $class = 'ucp-enabled';
            $status = 'Under construction mode is <b style="font-weight: bold;">enabled</b>';
            $action_url = add_query_arg(array('action' => 'ucp_change_status', 'new_status' => 'disabled', 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php'));
            $action_url = wp_nonce_url($action_url, 'ucp_change_status');
            $action = __('Under Construction Mode', 'under-construction-page');
            $action .= '<a href="' . $action_url . '" id="ucp-status-wrapper" class="on"><span id="ucp-status-off" class="ucp-status-btn">OFF</span><span id="ucp-status-on" class="ucp-status-btn">ON</span></a>';
        } else {
            $main_label = '<img class="logo" src="' . UCP_PLUGIN_URL . '/images/ucp_icon.png" alt="' . __('Under construction mode is disabled', 'under-construction-page') . '" title="' . __('Under construction mode is disabled', 'under-construction-page') . '"> <span class="ab-label">' . __('UnderConstruction', 'under-construction-page') . ' <i class="ucp-status-dot ucp-status-dot-disabled">&#9679;</i></span>';
            $class = 'ucp-disabled';
            $status = 'Under construction mode is <b style="font-weight: bold;">disabled</b>';
            $action_url = add_query_arg(array('action' => 'ucp_change_status', 'new_status' => 'enabled', 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php'));
            $action_url = wp_nonce_url($action_url, 'ucp_change_status');
            $action = __('Under Construction Mode', 'under-construction-page');
            $action .= '<a href="' . $action_url . '" id="ucp-status-wrapper" class="off"><span id="ucp-status-off" class="ucp-status-btn">OFF</span><span id="ucp-status-on" class="ucp-status-btn">ON</span></a>';
        }

        $wp_admin_bar->add_menu(array(
            'parent' => '',
            'id'     => 'under-construction-page',
            'title'  => $main_label,
            'href'   => admin_url('options-general.php?page=ucp'),
            'meta'   => array('class' => $class)
        ));
        $wp_admin_bar->add_node(array(
            'id'    => 'ucp-status',
            'title' => $action,
            'href'  => false,
            'parent' => 'under-construction-page'
        ));
        $wp_admin_bar->add_node(array(
            'id'     => 'ucp-settings',
            'title'  => __('Settings', 'under-construction-page'),
            'href'   => admin_url('options-general.php?page=ucp'),
            'parent' => 'under-construction-page'
        ));
        $wp_admin_bar->add_node(array(
            'id'     => 'ucp-preview',
            'title'  => __('Preview', 'under-construction-page'),
            'href'   => get_home_url() . '?ucp_preview',
            'parent' => 'under-construction-page'
        ));
    } // admin_bar


    // show under construction notice on WP login form
    static function login_message($message)
    {
        if (self::is_construction_mode_enabled(true)) {
            $message .= '<div class="message">' . __('Under construction mode is <b>enabled</b>.', 'under-construction-page') . '</div>';
        }

        return $message;
    } // login_notice


    // add settings link to plugins page
    static function plugin_action_links($links)
    {
        $settings_link = '<a href="' . admin_url('options-general.php?page=ucp') . '" title="' . __('UnderConstruction Settings', 'under-construction-page') . '">' . __('Settings', 'under-construction-page') . '</a>';
        array_unshift($links, $settings_link);

        return $links;
    } // plugin_action_links


    // add links to plugin's description in plugins table
    static function plugin_meta_links($links, $file)
    {
        $support_link = '<a href="mailto:ucp@webfactoryltd.com" title="' . __('Get help', 'under-construction-page') . '">' . __('Support', 'under-construction-page') . '</a>';
        if (!self::whitelabel_filter() && $file == plugin_basename(__FILE__)) {
            unset($links[1]);
            unset($links[2]);
        } else if ($file == plugin_basename(__FILE__)) {
            $links[] = $support_link;
        }

        return $links;
    } // plugin_meta_links


    static function admin_footer_text($text)
    {
        if (!self::is_plugin_page()) {
            return $text;
        }

        $text = '<i><a href="https://underconstructionpage.com" target="_blank">UnderConstructionPage</a> <span class="superscript">' . self::get_licence_name() . '</span> v' . self::$version . ' by <a href="https://www.webfactoryltd.com/" target="_blank">WebFactory Ltd</a>. </i>' . $text;

        return $text;
    } // admin_footer_text


    // test if we're on plugin's page
    static function is_plugin_page()
    {
        $current_screen = get_current_screen();

        if ($current_screen->id == 'settings_page_ucp') {
            return true;
        } else {
            return false;
        }
    } // is_plugin_page


    // create the admin menu item
    static function admin_menu()
    {
        $title = '<span style="font-size: 11px;">UnderConstruction <span style="color: #FF7900; vertical-align: super; font-size: 9px;">' . strtoupper(self::get_licence_name()) . '</span></span>';
        add_options_page('UnderConstructionPage', $title, 'manage_options', 'ucp', array(__CLASS__, 'main_page'));
    } // admin_menu


    // all settings are saved in one option
    static function register_settings()
    {
        register_setting(UCP_OPTIONS_KEY, UCP_OPTIONS_KEY, array(__CLASS__, 'sanitize_settings'));
    } // register_settings


    // set default settings
    // todo
    static function default_options()
    {
        $defaults = array(
            'status' => '0',
            'license_key' => '',
            'license_active' => false,
            'license_expires' => '',
            'license_type' => '',
            'start_date' => '',
            'end_date' => '',
            'ga_tracking_id' => '',
            'theme' => 'ucp-template-mad-designer-default',
            'custom_css' => '',
            'title' => '[site-title] is under construction',
            'description' => '[site-tagline]',
            'heading1' => __('Sorry, we\'re doing some work on the site', 'under-construction-page'),
            'content' => __('Thank you for being patient. We are doing some work on the site and will be back shortly.', 'under-construction-page'),
            'login_button' => '1',
            'password_button' => '0',
            'whitelisted_ips' => array(),
            'whitelisted_roles' => array('administrator'),
            'whitelisted_users' => array(),
            'url_rules_type' => '',
            'url_rules' => array(),
            'site_password' => '',
            'direct_access_password' => '',
            'http_response_code' => '200 OK',
            'no_index' => '0',
            'ga_track_events' => '1',
            'disable_feeds' => '0',
            'disable_rpc' => '0',
            'disable_rest_api' => '0',
            'send_nocache_headers' => '1',
            'redirect' => '',
            'shortcodes' => '1',
            'affiliate_cookie_lifetime' => '60',
            'mc_list' => '',
            'mc_lists' => array(),
            'mc_double_optin' => '1',
            'mc_api_key' => '',
            'zapier_webhook_url' => '',
            'autoresponder_action_url' => '',
            'autoresponder_email_field' => '',
            'autoresponder_name_field' => '',
            'autoresponder_extra_data' => '',
            'autoresponder_method' => 'get',
            'captcha_question' => 'Are you human? Please solve:',
            'whitelabel' => 0,
        );

        return $defaults;
    } // default_options


    // sanitize settings on save
    static function sanitize_settings($options)
    {
        $old_options = self::get_options();

        if (isset($_POST['submit'])) {
            foreach ($options as $key => $value) {
                switch ($key) {
                    case 'title':
                    case 'description':
                    case 'heading1':
                    case 'content':
                    case 'custom_css':
                    case 'redirect':
                    case 'zapier_webhook_url':
                    case 'mc_api_key':
                    case 'site_password':
                    case 'password_button':
                    case 'direct_access_password':
                        $options[$key] = trim($value);
                        break;
                    case 'ga_tracking_id':
                        $options[$key] = substr(strtoupper(trim($value)), 0, 15);
                        break;
                    case 'start_date':
                    case 'end_date':
                        $options[$key] = substr(trim($value), 0, 16);
                        if ($options[$key] == '0000-00-00 00:00') {
                            $options[$key] = '';
                        }
                        break;
                    case 'affiliate_cookie_lifetime':
                        $options[$key] = (int) $value;
                        $options[$key] = max(1, $options[$key]);
                        $options[$key] = min(700, $options[$key]);
                        break;
                } // switch
            } // foreach

            if (empty($options['start_date_toggle'])) {
                $options['start_date'] = '';
            }
            unset($options['start_date_toggle']);
            if (empty($options['end_date_toggle'])) {
                $options['end_date'] = '';
            }
            unset($options['end_date_toggle']);

            if (!empty($options['start_date']) && !empty($options['end_date']) && $options['start_date'] > $options['end_date']) {
                add_settings_error('ucp', 'start_date', __('Automatic start date is set after the automatic end date. Please switch the dates or correct them.', 'under-construction-page'));
            }

            $options['whitelisted_roles'] = empty($options['whitelisted_roles']) ? array() : $options['whitelisted_roles'];
            $options['whitelisted_users'] = empty($options['whitelisted_users']) ? array() : $options['whitelisted_users'];
            $options = UCP_utility::check_var_isset($options, array('status' => '0', 'login_button' => '0', 'password_button' => '0', 'disable_feeds' => '0', 'disable_rpc' => '0', 'disable_rest_api' => '0', 'send_nocache_headers' => '0', 'shortcodes' => '0', 'no_index' => '0', 'ga_track_events' => '0', 'mc_double_optin' => '0'));

            $ips_cleaned = array();
            $ips = explode("\n", $options['whitelisted_ips']);
            foreach ($ips as $ip) {
                $ip = trim($ip);
                $ips_cleaned[] = $ip;
            }
            $options['whitelisted_ips'] = $ips_cleaned;

            $urls_cleaned = array();
            $urls = explode("\n", $options['url_rules']);
            foreach ($urls as $url) {
                $url = trim($url);
                if (!empty($url)) {
                    $urls_cleaned[] = UCP_utility::slashit($url);
                }
            }
            $options['url_rules'] = $urls_cleaned;

            if (!isset($options['ga_tracking_toggle'])) {
                $options['ga_tracking_id'] = '';
            }
            if (!empty($options['ga_tracking_id']) && preg_match('/^UA-\d{3,}-\d{1,3}$/', $options['ga_tracking_id']) === 0) {
                add_settings_error('ucp', 'ga_tracking_id', __('Please enter a valid Google Analytics Tracking ID, or leave empty to disable tracking.', 'under-construction-page'));
            }
            unset($options['ga_tracking_toggle']);

            if (!isset($options['site_password_toggle'])) {
                $options['site_password'] = '';
            }
            if (!empty($options['site_password']) && strlen($options['site_password']) < 4) {
                add_settings_error('ucp', 'site_password', __('Please enter a site password that\'s at least 4 characters long.', 'under-construction-page'));
            }
            unset($options['site_password_toggle']);

            if (!empty($options['direct_access_password']) && strlen($options['direct_access_password']) < 4) {
                add_settings_error('ucp', 'direct_access_password', __('Please enter a direct access password that\'s at least 4 characters long.', 'under-construction-page'));
            }

            if (!isset($options['redirect_toggle'])) {
                $options['redirect'] = '';
            }
            unset($options['redirect_toggle']);

            if (!empty($options['mc_api_key']) && $options['mc_api_key'] != $old_options['mc_api_key']) {
                $mc = new UCP_MailChimp($options['mc_api_key']);
                $test = $mc->get('');

                if (false == $mc->success()) {
                    add_settings_error('ucp', 'mc_api_key', __('Please enter a valid MailChimp API key, or leave empty to disable MailChimp integration.', 'under-construction-page'));
                    $options['mc_lists'] = array();
                } else {
                    $options['mc_lists'] = self::get_mc_lists($options['mc_api_key']);
                }
            } elseif (empty($options['mc_api_key'])) {
                $options['mc_lists'] = array();
            }
        } elseif (isset($_POST['submit-import'])) { // import settings
            unset($_POST['submit-import']);

            $import_data = UCP_ei::options_validate_import_file();
            if (is_wp_error($import_data)) {
                add_settings_error(UCP_OPTIONS_KEY, 'import_settings', $import_data->get_error_message(), 'error');
            } else {
                $options = $import_data['data'];
                add_settings_error(UCP_OPTIONS_KEY, 'import_settings', __('Settings have been imported.', 'under-contruction-page'), 'updated');
            }
        } elseif (isset($_POST['submit-license'])) { // handle license
            if (empty($options['license_key'])) {
                $new_options['license_type'] = '';
                $new_options['license_expires'] = '';
                $new_options['license_active'] = false;
                $new_options['license_key'] = '';
                add_settings_error(UCP_OPTIONS_KEY, 'license_key', __('License key saved.', 'under-construction-page'), 'updated');
            } else {
                $tmp = self::validate_license_key($options['license_key']);
                $new_options['license_key'] = $options['license_key'];
                if ($tmp['success']) {
                    $new_options['license_type'] = $tmp['license_type'];
                    $new_options['license_expires'] = $tmp['license_expires'];
                    $new_options['license_active'] = $tmp['license_active'];
                    if ($tmp['license_active']) {
                        add_settings_error(UCP_OPTIONS_KEY, 'license_key', __('License key saved and activated!', 'under-construction-page'), 'updated');
                    } else {
                        add_settings_error(UCP_OPTIONS_KEY, 'license_key', 'License not active. ' . $tmp['error'], 'error');
                    }
                } else {
                    add_settings_error(UCP_OPTIONS_KEY, 'license_key', 'Unable to contact licensing server. Please try again in a few moments.', 'error');
                }
            }
            $options = $new_options;
        } elseif (isset($_FILES['template-zip']) && ($_FILES['template-zip']['type'] == 'application/x-zip-compressed' || $_FILES['template-zip']['type'] == 'application/zip')) {

            $uploads = wp_upload_dir();
            $templates_import_path = $uploads['basedir'] . '/ucp-templates/';
            if (!file_exists($templates_import_path)) {
                mkdir($templates_import_path, 0777);
            }
            $template_zip = $templates_import_path . 'tmp_' . time() . '_' . basename($_FILES['template-zip']['name']);
            if (move_uploaded_file($_FILES['template-zip']['tmp_name'], $template_zip)) {
                $zip = new ZipArchive;


                if ($zip->open($template_zip) === TRUE) {
                    $template_name = 'tmp_' . time() . '_' . str_replace('.zip', '', basename($_FILES['template-zip']['name']));
                    $template_folder = $templates_import_path . $template_name;
                    $template_files = array();
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $template_files[] = $zip->getNameIndex($i);
                    }
                    $zip->extractTo($template_folder);
                    $zip->close();
                    $template_data_file = file_get_contents($template_folder . '/template.txt');
                    $template_data = json_decode($template_data_file);

                    $images = array();
                    preg_match_all('/#!ucpimage#(.*?)#ucpimage!#/', $template_data->html, $images);

                    if (count($images[1]) > 0) {
                        foreach ($images[1] as $image_id) {
                            $image_file = '';
                            foreach ($template_files as $template_file) {
                                if (substr($template_file, 0, strlen($image_id)) . '.' == $image_id . '.') {
                                    $image_file = $template_file;
                                }
                            }

                            if ($image_file != '') {
                                $image_src = media_sideload_image($uploads['baseurl'] . '/ucp-templates/' . $template_name . '/' . $image_file, 0, '', 'src');
                                if (!is_wp_error($image_src)) {
                                    $template_data->html = str_replace('#!ucpimage#' . $image_id . '#ucpimage!#', $image_src, $template_data->html);
                                }
                            }
                        }
                    }

                    $template['template_name'] = $template_data->name;
                    $template['template_html'] = $template_data->html;
                    $template['template_type'] = 'user';
                    $template['template_version'] = '1.0';
                    $template['template_import'] = true;

                    $template_slug = UCP_templates::ucp_editor_save_template($template);


                    if (strlen($template_slug) > 0) {
                        if (!file_exists($uploads['basedir'] . '/ucp/')) {
                            mkdir($uploads['basedir'] . '/ucp/', 0777);
                        }
                        copy($template_folder . '/screenshot.png', $uploads['basedir'] . '/ucp/template-' . $template_slug . '.png');
                        add_settings_error(UCP_OPTIONS_KEY, 'template_import', 'The template ' . $template_data->name . ' has been imported.', 'updated');
                    }
                } else {
                    add_settings_error(UCP_OPTIONS_KEY, 'template_import', 'The template zip file could not be extracted.', 'error');
                }
            } else {
                add_settings_error(UCP_OPTIONS_KEY, 'template_import', 'Sorry, there was an error uploading the template zip.', 'error');
            }
        }

        // empty cache in 3rd party plugins
        if (isset($options['status']) && ($options['status'] != $old_options['status'] || $options['status'] == '1')) {
            self::clear_3rdparty_cache();
        }

        return array_merge($old_options, $options);
    } // sanitize_settings


    // todo
    static function get_mc_lists($api_key = false)
    {
        if (false === $api_key) {
            $options = self::get_options();
            $api_key = $options['mc_api_key'];
        }

        $lists = array();
        $mc = new UCP_MailChimp($api_key);

        $raw_lists = $mc->get('lists');
        if ($mc->success()) {
            foreach ($raw_lists['lists'] as $list) {
                $lists[] = array('val' => $list['id'], 'label' => $list['name']);
                $raw_segments = $mc->get('lists/' . $list['id'] . '/segments');
                if ($mc->success()) {
                    foreach ($raw_segments['segments'] as $segment) {
                        $lists[] = array('val' => $list['id'] . '|' . $segment['id'], 'label' => $list['name'] . ' - ' . $segment['name']);
                    }
                }
            } // foreach list
            usort($lists, array('UCP_utility', 'sort_select_options'));
        } else {
            $lists = false;
        } // if success

        return $lists;
    } // get_mc_lists

    static function autoresponder_send($fields)
    {
        $options = self::get_options();

        $query_data = array();
        if (isset($options['autoresponder_email_field'])) {
            $query_data[$options['autoresponder_email_field']] = $fields['email'];
        }

        if (isset($options['autoresponder_name_field'])) {
            $query_data[$options['autoresponder_name_field']] = $fields['name'];
        }

        if (isset($options['autoresponder_extra_data'])) {
            $extra_fields = explode('&', $options['autoresponder_extra_data']);
            foreach ($extra_fields as $extra_field) {
                $extra_field_pair = explode('=', $extra_field, 2);
                $query_data[$extra_field_pair[0]] = $extra_field_pair[1];
            }
        }

        if ($options['autoresponder_method'] == 'get') {
            $res = wp_remote_get(esc_url_raw($options['autoresponder_action_url']), $query_data);
        } else {
            $res = wp_remote_post($options['autoresponder_action_url'], array('sslverify' => false, 'body' => $query_data));
        }

        if (!is_wp_error($res)) {
            return true;
        } else {
            return new WP_Error('external_api', 'Unable to send autoresponder. ' . $res->get_error_message());
        }
    }

    static function mc_add_subscriber($fields)
    {
        $options = self::get_options();
        $api_key = $options['mc_api_key'];

        $mc = new UCP_MailChimp($api_key);
        if (empty($options['mc_list'])) {
            return false;
        }

        $tmp = explode('|', $options['mc_list']);
        $list = $tmp[0];
        $segment = @$tmp[1];

        $member_info = $mc->get('search-members', array('list_id' => $list, 'query' => $fields['email']));

        if (isset($member_info['exact_matches']) && $member_info['exact_matches']['total_items'] == 0) {
            $email = $fields['email'];
            $name = explode(' ', $fields['name'], 2);
            $fields['FNAME'] = $name[0];
            $fields['LNAME'] = $name[1];

            if (strlen($fields['FNAME']) < 2) {
                $fields['FNAME'] = '';
            }

            if (strlen($fields['LNAME']) < 2) {
                $fields['LNAME'] = '';
            }

            unset($fields['name']);
            unset($fields['email']);

            $new = array();
            foreach ($fields as $key => $val) {
                $new[strtoupper($key)] = $val;
            }



            if (isset($options['mc_double_optin']) && $options['mc_double_optin'] != '1') {
                $status = 'subscribed';
            } else {
                $status = 'pending';
            }

            $mc->post('lists/' . $list . '/members', array('email_address' => $email, 'status' => $status, 'merge_fields' => $new));

            if ($status == 'subscribed' && !empty($segment)) {
                $mc->post('lists/' . $list . '/segments' . $segment . '/members', array('email_address' => $email, 'status' => $status));
            }

            if ($mc->success()) {
                return true;
            } else {
                //echo $mc->getLastError();
                return false;
            }
        } else {
            return false;
        }
    } // get_mc_lists

    // todo
    static function get_mc_lists_ajax()
    {
        // todo
        // check_ajax_referer('ucp_dismiss_pointer');

        $api_key = trim(@$_GET['mc_api_key']);
        if (empty($api_key) || strlen($api_key) < 30) {
            wp_send_json_error('Invalid MailChimp API key.');
        }

        $options = self::get_options();

        $lists = self::get_mc_lists($api_key);
        if ($lists === false) {
            wp_send_json_error('Invalid MailChimp API key.');
        } else {
            $options['mc_api_key'] = $api_key;
            $options['mc_lists'] = $lists;
            update_option(UCP_OPTIONS_KEY, $options);
            wp_send_json_success($lists);
        }
    } // get_mc_lists

    static function zapier_send($fields = array())
    {
        $options = self::get_options();

        if (empty($options['zapier_webhook_url'])) {
            return false;
        }

        $res = wp_remote_post($options['zapier_webhook_url'], array('sslverify' => false, 'body' => $fields));

        if (!is_wp_error($res)) {
            $res = wp_remote_retrieve_body($res);
            $res = json_decode($res);

            if (!empty($res->status) && $res->status == 'success') {
                return true;
            } else {
                return new WP_Error('external_api', 'Zap returned the following request status: ' . $res->status);
            }
        } else {
            return new WP_Error('external_api', 'Unable to send Zap. ' . $res->get_error_message());
        }
    }

    // when status changes empty cache in various 3rd party plugins
    static function clear_3rdparty_cache()
    {
        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
        }

        if (function_exists('w3tc_flush_all')) {
            w3tc_flush_all();
        }
        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }
        if (method_exists('LiteSpeed_Cache_API', 'purge_all')) {
            LiteSpeed_Cache_API::purge_all();
        }
        if (class_exists('Endurance_Page_Cache')) {
            $epc = new Endurance_Page_Cache;
            $epc->purge_all();
        }
        if (class_exists('SG_CachePress_Supercacher') && method_exists('SG_CachePress_Supercacher', 'purge_cache')) {
            SG_CachePress_Supercacher::purge_cache(true);
        }
        if (class_exists('SiteGround_Optimizer\Supercacher\Supercacher')) {
            SiteGround_Optimizer\Supercacher\Supercacher::purge_cache();
        }
        if (isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')) {
            $GLOBALS['wp_fastest_cache']->deleteCache(true);
        }
        if (is_callable(array('Swift_Performance_Cache', 'clear_all_cache'))) {
            Swift_Performance_Cache::clear_all_cache();
        }
        if (is_callable(array('Hummingbird\WP_Hummingbird', 'flush_cache'))) {
            Hummingbird\WP_Hummingbird::flush_cache(true, false);
        }
    } // empty_3rdparty_cache


    static function get_themes()
    {
        $themes = array(
            'mad_designer' => __('Mad Designer', 'under-construction-page'),
            'plain_text' => __('Plain Text', 'under-construction-page'),
            'under_construction' => __('Under Construction', 'under-construction-page'),
            'dark' => __('Things Went Dark', 'under-construction-page'),
            'forklift' => __('Forklift at Work', 'under-construction-page'),
            'under_construction_text' => __('Under Construction Text', 'under-construction-page'),
            'cyber_chick' => __('Cyber Chick', 'under-construction-page'),
            'rocket' => __('Rocket Launch', 'under-construction-page'),
            'loader' => __('Loader at Work', 'under-construction-page'),
            'cyber_chick_dark' => __('Cyber Chick Dark', 'under-construction-page'),
            'safe' => __('Safe', 'under-construction-page'),
            'people' => __('People at Work', 'under-construction-page'),
            'windmill' => __('Windmill', 'under-construction-page'),
            'sad_site' => __('Sad Site', 'under-construction-page'),
            'lighthouse' => __('Lighthouse', 'under-construction-page'),
            'hot_air_baloon' => __('Hot Air Baloon', 'under-construction-page'),
            'people_2' => __('People at Work #2', 'under-construction-page'),
            'rocket_2' => __('Rocket Launch #2', 'under-construction-page'),
            'light_bulb' => __('Light Bulb', 'under-construction-page'),
            'ambulance' => __('Ambulance', 'under-construction-page'),
            'laptop' => __('Laptop', 'under-construction-page'),
            'puzzles' => __('Puzzles', 'under-construction-page'),
            'iot' => __('Internet of Things', 'under-construction-page'),
            'setup' => __('Setup', 'under-construction-page')
        );
        $themes = apply_filters('ucp_themes', $themes);

        return $themes;
    } // get_themes


    // output the whole options page
    static function main_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        UCP_templates::ucp_refresh_templates();

        $options = self::get_options();
        $default_options = self::default_options();
        $pointers = get_option(UCP_POINTERS_KEY);

        // auto remove welcome pointer when options are opened
        if (isset($pointers['welcome'])) {
            unset($pointers['welcome']);
            update_option(UCP_POINTERS_KEY, $pointers);
        }

        echo '<div class="wrap">
          <h1 class="ucp-logo">
            <img src="' . UCP_PLUGIN_URL . '/images/ucp_logo.png" alt="UnderConstructionPage ' . self::get_licence_name() . '" title="UnderConstructionPage ' . self::get_licence_name() . '">
            <img src="' . UCP_PLUGIN_URL . '/images/ucp-logo-' . strtolower(self::get_licence_name()) . '.png" alt="UnderConstructionPage ' . self::get_licence_name() . '" title="UnderConstructionPage ' . self::get_licence_name() . '">
          </h1>';

        echo '<form method="post" action="options.php" enctype="multipart/form-data" id="ucp_form">';
        settings_fields(UCP_OPTIONS_KEY);

        $tabs = array();
        if (self::is_activated()) {
            $tabs[] = array('id' => 'ucp_main', 'icon' => 'dashicons-admin-settings', 'class' => '', 'label' => __('Main', 'under-construction-page'), 'callback' => array('UCP_tab_main', 'display'));
            $tabs[] = array('id' => 'ucp_design', 'icon' => 'dashicons-admin-appearance', 'class' => '', 'label' => __('Appearance', 'under-construction-page'), 'callback' => array('UCP_tab_design', 'display'));
            $tabs[] = array('id' => 'ucp_access', 'icon' => 'dashicons-shield', 'class' => '', 'label' => __('Access', 'under-construction-page'), 'callback' => array('UCP_tab_access', 'display'));
            $tabs[] = array('id' => 'ucp_advanced', 'icon' => 'dashicons-admin-tools', 'class' => '', 'label' => __('Settings', 'under-construction-page'), 'callback' => array('UCP_tab_advanced', 'display'));
            $tabs[] = array('id' => 'ucp_leads', 'icon' => 'dashicons-groups', 'class' => '', 'label' => __('Leads', 'under-construction-page'), 'callback' => array('UCP_tab_leads', 'display'));
            $tabs[] = array('id' => 'ucp_support', 'icon' => 'dashicons-sos', 'class' => '', 'label' => __('Support', 'under-construction-page'), 'callback' => array('UCP_tab_support', 'display'));
            if (self::whitelabel_filter()) {
                $tabs[] = array('id' => 'ucp_license', 'icon' => 'dashicons-id', 'class' => '', 'label' => __('License', 'under-construction-page'), 'callback' => array('UCP_tab_license', 'display'));
            }
        } else {
            $tabs[] = array('id' => 'ucp_license', 'icon' => 'dashicons-id', 'class' => '', 'label' => __('License', 'under-construction-page'), 'callback' => array('UCP_tab_license', 'display'));
            $tabs[] = array('id' => 'ucp_support', 'icon' => 'dashicons-sos', 'class' => '', 'label' => __('Support', 'under-construction-page'), 'callback' => array('UCP_tab_support', 'display'));
        }

        $tabs = apply_filters('ucp_tabs', $tabs);

        echo '<div id="ucp_tabs" class="ui-tabs" style="display: none;">';
        echo '<ul class="ucp-main-tab">';
        foreach ($tabs as $tab) {
            echo '<li><a href="#' . $tab['id'] . '" class="' . $tab['class'] . '"><span class="icon"><span class="dashicons ' . $tab['icon'] . '"></span></span><span class="label">' . $tab['label'] . '</span></a></li>';
        }
        echo '</ul>';

        foreach ($tabs as $tab) {
            if (is_callable($tab['callback'])) {
                echo '<div style="display: none;" id="' . $tab['id'] . '">';
                call_user_func($tab['callback']);
                echo '</div>';
            }
        } // foreach

        echo '</div>'; // ucp_tabs
        echo '</form>';

        // weglot install dialog
        echo '<div id="weglot-upsell-dialog" style="display: none;" title="Weglot"><span class="ui-helper-hidden-accessible"><input type="text"/></span>';
        echo '<div style="padding: 20px; font-size: 15px;">';
        echo '<ul class="ucp-list">';
        echo '<li>Best-rated WordPress multilingual plugin</li>';
        echo '<li>Simple 5-minute set-up. No coding required</li>';
        echo '<li>Accelerated translation management: Machine & human translations with access to professional translators</li>';
        echo '<li>Compatible with any WordPress theme or plugin</li>';
        echo '<li>Optimized for multilingual SEO</li>';
        echo '<li>10-day Free trial and free plan available</li>';
        echo '</ul>';
        echo '<p class="upsell-footer"><a class="button button-primary" id="install-weglot">Install &amp; activate Weglot to make your website multilingual</a></p>';
        echo '</div>';
        echo '</div>';
        // weglot install dialog

        echo '</div>'; // wrap
    } // options_page


    static function footer_save_button()
    {
        echo '<p class="submit">';
        echo get_submit_button(__('Save Changes', 'under-construction-page'), 'primary large', 'submit', false);
        echo '</p>';
    } // footer_save_button


    // reset all pointers to default state - visible
    static function reset_pointers()
    {
        $pointers = array();
        $pointers['welcome'] = array('target' => '#menu-settings', 'edge' => 'left', 'align' => 'right', 'content' => 'Thank you for installing the <b style="font-weight: 800; font-variant: small-caps;">UnderConstructionPage</b> plugin! Please open <a href="' . admin_url('options-general.php?page=ucp') . '">Settings - UnderConstruction</a> to create a beautiful under construction page.');

        update_option(UCP_POINTERS_KEY, $pointers);
    } // reset_pointers


    // todo
    static function create_custom_tables()
    {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        global $wpdb;
        $meta = self::get_meta();

        self::register_custom_tables();

        $stats = "CREATE TABLE IF NOT EXISTS `" . $wpdb->ucp_stats . "` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `date` date NOT NULL,
              `views` int(10) unsigned NOT NULL DEFAULT '0',
              `sessions` int(10) unsigned NOT NULL DEFAULT '0',
              `conversions` int(10) unsigned NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`),
              UNIQUE KEY `date` (`date`)
              ) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";

        $leads = "CREATE TABLE IF NOT EXISTS `" . $wpdb->ucp_leads . "` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `type` varchar(16) DEFAULT NULL,
              `email` varchar(128) NOT NULL,
              `name` varchar(128) DEFAULT NULL,
              `custom` text,
              `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `ip` varchar(39) NOT NULL,
              `location` varchar(200) NOT NULL,
              `user_agent` text,
              PRIMARY KEY (`id`)
              ) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";

        $links = "CREATE TABLE IF NOT EXISTS `" . $wpdb->ucp_links . "` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `type` varchar(10) DEFAULT NULL,
              `name` varchar(256) NOT NULL,
              `secret_key` varchar(8) DEFAULT NULL,
              `expire_type` varchar(16) DEFAULT NULL,
              `expire_value` varchar(32) DEFAULT NULL,
              `sessions` int(10) unsigned NOT NULL DEFAULT '0',
              `views` int(10) unsigned NOT NULL DEFAULT '0',
              `conversions` int(10) unsigned NOT NULL DEFAULT '0',
              `ips` text NOT NULL,
              `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `modified` timestamp NULL DEFAULT NULL,
               PRIMARY KEY (`id`),
               UNIQUE KEY `key` (`secret_key`),
               KEY `type` (`type`)
               ) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8"; // todo - new fields

        $templates = "CREATE TABLE IF NOT EXISTS `" . $wpdb->ucp_templates . "` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `slug` varchar(256) NOT NULL,
              `name` varchar(256) NOT NULL,
              `thumb` varchar(256) DEFAULT NULL,
              `type` varchar(20) DEFAULT NULL,
              `version` varchar(20) DEFAULT NULL,
              `tags` TEXT,
              `desc` TEXT,
              `page_title` TEXT,
              `page_desc` TEXT,
              `html` TEXT,
              `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `modified` timestamp NULL DEFAULT NULL,
              PRIMARY KEY (`id`)
               ) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8"; // todo - new fields
        dbDelta($stats);
        dbDelta($leads);
        dbDelta($links);
        dbDelta($templates);


        //Install default template
        $check_if_template_exists = $wpdb->get_row('SELECT * FROM ' . $wpdb->ucp_templates . ' WHERE slug=\'ucp-template-mad-designer\' LIMIT 1');
        if (!$check_if_template_exists) {

            $default_template_html = ' <div class="container"> <div class="ucp-row row ui-sortable ui-droppable" style="cursor: auto;"><div class="ucp-module col-12 col-md-12 col-sm-12 ui-sortable-handle" data-module-type="image" data-module-id="m96924" id="ucp-m96924" style="cursor: auto;"><div class="ucp-element" data-element-type="image" data-css-attr="border" data-attr="src" data-element-id="e49991" style="cursor: auto;"><img class="image" src="' . UCP_PLUGIN_URL . 'images/original/mad-designer.png" alt="Mad Designer" title="Mad Designer" style="cursor: move;"></div></div><div class="ucp-module col-12 col-md-12 col-sm-12 ui-sortable-handle" data-module-type="heading_l" data-module-id="m14779" id="ucp-m14779" style="cursor: auto;"><div class="ucp-element" data-element-type="text" data-css-attr="color,font-size" data-attr="html" data-element-id="e76053" style="cursor: auto;"><h1 class="heading1">Sorry, we&#39;re doing some work on the site</h1></div></div><div class="ucp-module col-12 col-md-12 col-sm-12 ui-sortable-handle" data-module-type="heading_l" data-module-id="m11483" id="ucp-m11483" style="cursor: auto;"><div class="ucp-element" data-element-type="text" data-css-attr="color,font-size" data-attr="html" data-element-id="e80194" style="cursor: auto;"><p class="heading1">Thank you for being patient. We are doing some work on the site and will be back shortly.</p></div></div><div class="ucp-module col-12 col-md-12 col-sm-12 ui-sortable-handle" data-module-type="social" data-module-id="m39671" id="ucp-m39671" style="cursor: auto;"><div class="ucp-element" data-element-type="social" data-css-attr="color,font-size" data-attr="src" data-element-id="e96906" style="cursor: auto;"><div class="socialicons"><a class="ucp-social-facebook" title="facebook" href="#" target="_blank"><i class="fa fa-facebook-square fa-3x"></i></a><a class="ucp-social-twitter" title="twitter" href="#" target="_blank"><i class="fa fa-twitter-square fa-3x"></i></a><a class="ucp-social-google" title="google" href="#" target="_blank"><i class="fa fa-google-plus-square fa-3x"></i></a><a class="ucp-social-linkedin" title="linkedin" href="#" target="_blank"><i class="fa fa-linkedin-square fa-3x"></i></a><a class="ucp-social-youtube" title="youtube" href="#" target="_blank"><i class="fa fa-youtube-square fa-3x"></i></a><a class="ucp-social-pinterest" title="pinterest" href="#" target="_blank"><i class="fa fa-pinterest-square fa-3x"></i></a></div></div></div></div></div><style id="ucp_template_style">html body{background: rgba(211,211,211,1);background: -moz-linear-gradient(left, rgba(211,211,211,1) 0%, rgba(250,250,250,1) 100%);background: -webkit-linear-gradient(left, rgba(211,211,211,1) 0%,rgba(250,250,250,1) 100%);background: linear-gradient(to right, rgba(211,211,211,1) 0%,rgba(250,250,250,1) 100%);}#ucp-m96924{padding-left:0px;box-sizing:border-box;padding-right:0px;padding-top:0px;padding-bottom:0px;margin-left:0px;margin-right:0px;margin-top:0px;margin-bottom:0px;border-color:rgb(51, 51, 51);border-width:0px;border-style:none;}#ucp-m14779{padding-left:0px;box-sizing:border-box;padding-right:0px;padding-top:20px;padding-bottom:20px;margin-left:0px;margin-right:0px;margin-top:0px;margin-bottom:0px;border-color:rgb(51, 51, 51);border-width:0px;border-style:none;}#ucp-m14779 .heading1{color:rgb(51, 51, 51);font-size:44px;font-family:\'Roboto\';font-weight:bold;}#ucp-m11483{padding-left:0px;box-sizing:border-box;padding-right:0px;padding-top:20px;padding-bottom:20px;margin-left:0px;margin-right:0px;margin-top:0px;margin-bottom:0px;border-color:rgb(51, 51, 51);border-width:0px;border-style:none;}#ucp-m11483 .heading1{color:rgb(51, 51, 51);font-size:14px;font-family:\'Roboto\';font-weight:300;}#ucp-m39671{padding-left:0px;box-sizing:border-box;padding-right:0px;padding-top:20px;padding-bottom:20px;margin-left:0px;margin-right:0px;margin-top:0px;margin-bottom:0px;border-color:rgb(51, 51, 51);border-width:0px;border-style:none;}#ucp-m39671 .socialicons{color:rgb(51, 51, 51);font-size:16px;}#ucp-m39671 .socialicons a{color:rgb(51, 51, 51);}</style> <link rel="stylesheet" id="ucp-google-fonts-loader" href="https://fonts.googleapis.com/css?family=Roboto:bold,300"><style id="ucp_template_custom_style"></style>';

            $wpdb->insert(
                $wpdb->ucp_templates,
                array(
                    'name' => 'Mad Designer',
                    'slug' => 'ucp-template-mad-designer-default',
                    'html' => $default_template_html,
                    'type' => 'user',
                    'version' => '1.0',
                    'tags' => 'under construction,design,simple',
                    'desc' => 'Mad Designer Under Construction Page Template',
                    'page_title' => '',
                    'page_desc' => ''
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                )
            );
        }

        if (empty($meta['database_ver'])) {
            $meta['database_ver'] = '1.0';
            update_option(UCP_META_KEY, $meta);
        }
    } // create_custom_tables

    // check if Weglot plugin is active and min version installed
    static function is_weglot_active()
    {
        if (!function_exists('is_plugin_active') || !function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if (is_plugin_active('weglot/weglot.php')) {
            $weglot_info = get_plugin_data(ABSPATH . 'wp-content/plugins/weglot/weglot.php');
            if (version_compare($weglot_info['Version'], '2.5', '<')) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    } // is_weglot_active


    // check if Weglot is setup with a working API key
    static function is_weglot_setup()
    {
        if (!self::is_weglot_active()) {
            return false;
        }

        $active_languages = weglot_get_destination_languages();
        if (!empty($active_languages)) {
            return true;
        } else {
            return false;
        }
    } // is_weglot_setup

    // auto download / install / activate Weglot plugin
    static function install_weglot()
    {
        $plugin_slug = 'weglot/weglot.php';
        $plugin_zip = 'https://downloads.wordpress.org/plugin/weglot.latest-stable.zip';

        @include_once ABSPATH . 'wp-admin/includes/plugin.php';
        @include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        @include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        @include_once ABSPATH . 'wp-admin/includes/file.php';
        @include_once ABSPATH . 'wp-admin/includes/misc.php';
        echo '<style>
		body{
			font-family: sans-serif;
			font-size: 14px;
			line-height: 1.5;
			color: #444;
        }
        
        .ucp-button{
            display: inline-block;
            text-decoration: none;
            font-size: 13px;
            line-height: 26px;
            height: 28px;
            margin: 0;
            padding: 0 10px 1px;
            cursor: pointer;
            border-width: 1px;
            border-style: solid;
            -webkit-appearance: none;
            border-radius: 3px;
            white-space: nowrap;
            box-sizing: border-box;
            border-color: #ccc;
            vertical-align: top;
            -webkit-border-radius: 0;
            border-radius: 0;
            border: 1px solid #FF9900;
            background: transparent;
            color: #FF9900;
            text-shadow: none;
            box-shadow: none;
            margin-right: 4px;
            background: #FF9900;
            color: #FFFFFF;
        }

        .ucp-button:hover{
            background: rgba(255, 153, 0, 0.7);
            color: #FFFFFF;
            border: 1px solid #FF9900;
            box-shadow: none;
        }

        .popup-buttons{
            text-align: center;
            position: absolute;
            bottom: 10px;
            width: 500px;
        }

        .weglot_install_progress{
            margin: 20px 0 20px 20px;
            color: #444;
            height: 363px;
            display: block;
            overflow-y: scroll;
        }
		</style>';

        echo '<div class="weglot_install_progress">';
        echo 'If the installation process isn\'t done within a minute, please <a target="_parent" href="' . admin_url('plugin-install.php?s=weglot&tab=search&type=term') . '">install the plugin manually via the Plugins page</a><br><br>';
        echo 'Starting ...<br><br>';

        wp_cache_flush();
        $upgrader = new Plugin_Upgrader();
        echo 'Checking if Weglot is already installed ... <br />';
        if (self::is_plugin_installed($plugin_slug)) {
            echo 'Weglot is already installed! <br /><br />Making sure Weglot is up to date …<br />';
            $upgrader->upgrade($plugin_slug);
            $installed = true;
        } else {
            echo 'Installing Weglot.<br />';
            $installed = $upgrader->install($plugin_zip);
        }
        wp_cache_flush();

        if (!is_wp_error($installed) && $installed) {
            echo 'Activating Weglot.<br />';
            $activate = activate_plugin($plugin_slug);

            if (is_null($activate)) {
                echo 'Weglot is activated!<br />';

                if (isset($_GET['editor'])) {
                    echo '<div class="popup-buttons">';
                    echo '<a class="ucp-button" href="' . admin_url('admin.php?page=weglot-settings') . '" target="_blank">Add your translated language and finish Weglot install</a>';
                    echo '<a href="#" class="ucp-button" onclick="window.parent.closeWeglotInstaller();">Return to editor</a><br><i>Please reload the editor in order for the changes to take effect.</i>';
                    echo '</div>';
                } else {
                    echo '<script>setTimeout(function() { top.location = "options-general.php?page=ucp"; }, 1000);</script>';
                    echo '<br>If you are not redirected in a few seconds - <a href="options-general.php?page=ucp" target="_parent">click here</a>.';
                }
            }
        } else {
            echo 'Could not install Weglot. You\'ll have to <a target="_parent" href="' . admin_url('plugin-install.php?s=weglot&tab=search&type=term') . '">download and install manually</a>.';
            if (isset($_GET['editor'])) {
                echo '<div class="popup-buttons">';
                echo '<a href="#" class="ucp-button" onclick="window.parent.closeWeglotInstaller();">Return to editor</a>';
                echo '</div>';
            }
        }

        echo '</div>';
    } // install_weglot

    static function is_plugin_installed($slug)
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();

        if (!empty($all_plugins[$slug])) {
            return true;
        } else {
            return false;
        }
    } // is_plugin_installed

    // reset pointers on activation
    static function activate()
    {
        self::reset_pointers();
        self::create_custom_tables();
    } // activate


    // clean up on deactivation
    static function deactivate()
    {
        delete_option(UCP_POINTERS_KEY);
        delete_option(UCP_NOTICES_KEY);
    } // deactivate


    // clean up on uninstall
    static function uninstall()
    {
        global $wpdb;

        delete_option(UCP_OPTIONS_KEY);
        delete_option(UCP_META_KEY);
        delete_option(UCP_POINTERS_KEY);
        delete_option(UCP_NOTICES_KEY);

        self::register_custom_tables();
        $wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->ucp_leads);
        $wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->ucp_stats);
        $wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->ucp_links);
    } // uninstall

} // class UCP


// hook everything up
register_activation_hook(__FILE__, array('UCP', 'activate'));
register_deactivation_hook(__FILE__, array('UCP', 'deactivate'));
register_uninstall_hook(__FILE__, array('UCP', 'uninstall'));
add_action('init', array('UCP', 'init'));
add_action('plugins_loaded', array('UCP', 'plugins_loaded'));
