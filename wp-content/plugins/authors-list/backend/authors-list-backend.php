<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin file constant.
if ( ! defined( 'AUTHORS_LIST_BACKEND_PLUGIN_FILE' ) ) {
	define( 'AUTHORS_LIST_BACKEND_PLUGIN_FILE', __FILE__ );
}

// Include the required plugin file.
include_once dirname( AUTHORS_LIST_BACKEND_PLUGIN_FILE ) . '/includes/class-authors-list-backend.php';
