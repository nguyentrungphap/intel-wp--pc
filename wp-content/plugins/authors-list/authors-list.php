<?php
/**
 * Plugin Name:       Authors List
 * Description:       Display a list of post authors.
 * Version:           2.0.3
 * Author:            WPKube
 * Author URI:        http://wpkube.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       authors-list
 * Domain Path:       /languages
 *
 * @package WPKube
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define AUTHORS_LIST_PLUGIN_FILE constant.
if ( ! defined( 'AUTHORS_LIST_PLUGIN_FILE' ) ) {
	define( 'AUTHORS_LIST_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'AUTHORS_LIST_FREE' ) ) {
	define( 'AUTHORS_LIST_FREE', true );
}

// Include the required main plugin file.
include_once dirname( AUTHORS_LIST_PLUGIN_FILE ) . '/includes/class-authors-list.php';