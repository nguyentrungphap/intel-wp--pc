<?php
/**
 * Authors List setup main class.
 *
 * @package WPKube
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Authors_List setup class.
 */
class Authors_List {

	/**
	 * Authors_List version.
	 *
	 * @var string
	 */
	public $version = '2.0.3';

	/**
	 * The single instance of the class.
	 *
	 * @var Authors_List
	 */
	protected static $instance = null;

	/**
	 * Main Authors_List Instance.
	 *
	 * Ensures only one instance of Authors_List is loaded or can be loaded.
	 *
	 * @return Authors_List - Main instance.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();

			/**
			 * Action hook authors_list_loaded.
			 *
			 * Hooks after all instance of Authors_List is loaded.
			 */
			do_action( 'authors_list_loaded' );
		}

		return self::$instance;

	}

	/**
	 * Authors_List constructor.
	 */
	public function __construct() {

		// Define constants.
		$this->define_constants();

		// Hook into actions and filters.
		$this->init_hooks();

		// Include the required plugin files.
		$this->includes();

	}

	/**
	 * Define constants.
	 */
	public function define_constants() {

		$this->define( 'AUTHORS_LIST_VERSION', $this->version );
		$this->define( 'AUTHORS_LIST_URL', plugin_dir_url( AUTHORS_LIST_PLUGIN_FILE ) );
		$this->define( 'AUTHORS_LIST_BASENAME', plugin_basename( AUTHORS_LIST_PLUGIN_FILE ) );
		$this->define( 'AUTHORS_LIST_DIR_NAME', dirname( plugin_basename( AUTHORS_LIST_PLUGIN_FILE ) ) );
		$this->define( 'AUTHORS_LIST_ABS', dirname( AUTHORS_LIST_PLUGIN_FILE ) );

	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	public function define( $name, $value ) {

		if ( ! defined( $name ) ) {
			define( $name, $value ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound
		}

	}

	/**
	 * Hook into actions and filters.
	 */
	public function init_hooks() {

		// Load text domain.
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

	}

	/**
	 * Load text domain.
	 */
	public function load_textdomain() {

		load_plugin_textdomain(
			'authors-list',
			false,
			AUTHORS_LIST_DIR_NAME . '/languages'
		);

	}

	/**
	 * Include the required plugin files.
	 */
	public function includes() {

		// WP hooks class.
		include AUTHORS_LIST_ABS . '/includes/class-authors-list-wp-hooks.php';

		// Shortcode class.
		include AUTHORS_LIST_ABS . '/includes/class-authors-list-shortcode.php';

		// Backend class
		include AUTHORS_LIST_ABS . '/backend/authors-list-backend.php';

	}

}

// Instantiate the class.
Authors_List::instance();
