<?php
/**
 * Authors List WP Hooks class.
 *
 * @package WPKube
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Authors_List_WP_Hooks class.
 */
class Authors_List_WP_Hooks {

	/**
	 * The single instance of the class.
	 *
	 * @var Authors_List_WP_Hooks
	 */
	protected static $instance = null;

	/**
	 * Main Authors_List_WP_Hooks Instance.
	 *
	 * Ensures only one instance of Authors_List_WP_Hooks is loaded or can be loaded.
	 *
	 * @return Authors_List_WP_Hooks - Main instance.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();

			/**
			 * Action hook authors_list_wp_hooks_loaded.
			 *
			 * Hooks after all instance of Authors_List_WP_Hooks is loaded.
			 */
			do_action( 'authors_list_wp_hooks_loaded' );
		}

		return self::$instance;

	}

	/**
	 * Authors_List_WP_Hooks constructor.
	 */
	public function __construct() {

		// Hook to `pre_user_query` action hook.
		add_action( 'pre_user_query', array( $this, 'query_modification' ) );

	}

	/**
	 * Hook to `pre_user_query` action hook.
	 *
	 * @param object $query WP_User_Query object.
	 */
	public function query_modification( $query ) {

		if ( 'rand' === $query->query_vars['orderby'] ) {
			$query->query_orderby = str_replace( 'user_login', 'RAND()', $query->query_orderby );
		}

		return $query;

	}

}

// Instantiate the class.
Authors_List_WP_Hooks::instance();
