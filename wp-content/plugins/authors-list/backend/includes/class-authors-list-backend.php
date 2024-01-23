<?php
/**
 * Authors_List_Backend setup class.
 *
 * @package WPKube
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Authors_List_Backend' ) ) {

	/**
	 * Main Authors_List_Backend class.
	 *
	 * @class Authors_List_Backend
	 */
	class Authors_List_Backend {

		/**
		 * Authors_List_Backend version.
		 *
		 * @var string
		 */
		public $version = '2.0.3';

		/**
		 * Suffix for assets.
		 *
		 * @var string
		 */
		public $suffix = '';

		/**
		 * The single instance of the class.
		 *
		 * @var Authors_List_Backend
		 */
		protected static $instance = null;

		/**
		 * Main Authors_List_Backend Instance.
		 *
		 * Ensures only one instance of Authors_List_Backend is loaded or can be loaded.
		 *
		 * @return Authors_List_Backend - Main instance.
		 */
		public static function instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();

				/**
				 * Action hook authors_list_backend_loaded.
				 *
				 * Hooks after all instance of Authors_List_Backend is loaded.
				 */
				do_action( 'authors_list_backend_loaded' );
			}

			return self::$instance;

		}

		/**
		 * Authors_List_Backend constructor.
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
		 * Set the suffix value according to `SCRIPT_DEBUG`, WordPress constant.
		 */
		public function set_suffix() {
			$this->suffix = '';
		}

		/**
		 * Define constants.
		 */
		public function define_constants() {

			$this->define( 'AUTHORS_LIST_BACKEND_VERSION', $this->version );
			$this->define( 'AUTHORS_LIST_BACKEND_BASENAME', plugin_basename( AUTHORS_LIST_BACKEND_PLUGIN_FILE ) );
			$this->define( 'AUTHORS_LIST_BACKEND_ABSPATH', trailingslashit( dirname( AUTHORS_LIST_BACKEND_PLUGIN_FILE ) ) );
			$this->define( 'AUTHORS_LIST_BACKEND_INCLUDES', trailingslashit( AUTHORS_LIST_BACKEND_ABSPATH . 'includes' ) );
			$this->define( 'AUTHORS_LIST_BACKEND_URL', plugin_dir_url( AUTHORS_LIST_BACKEND_PLUGIN_FILE ) );

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

			// Load scripts and styles in the front end.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		}

		/**
		 * Load scripts and styles in the front end.
		 */
		public function enqueue_scripts() {

			// Google fonts url.
			$google_fonts_url = Authors_List_Helper::google_fonts_url();

			// CSS.
			wp_enqueue_style(
				'authors-list-css',
				AUTHORS_LIST_BACKEND_URL . 'assets/css/front' . $this->suffix . '.css',
				array(),
				AUTHORS_LIST_BACKEND_VERSION
			);

			wp_enqueue_style(
				'jquery-ui-css',
				AUTHORS_LIST_BACKEND_URL . 'assets/css/jquery-ui' . $this->suffix . '.css',
				array(),
				AUTHORS_LIST_BACKEND_VERSION
			);

			wp_enqueue_style(
				'authors-list-google-fonts',
				$google_fonts_url,
				array(),
				AUTHORS_LIST_BACKEND_VERSION
			);

			// JS.
			wp_enqueue_script(
				'authors-list-js',
				AUTHORS_LIST_BACKEND_URL . 'assets/js/front' . $this->suffix . '.js',
				array(
					'jquery',
					'jquery-ui-slider',
				),
				AUTHORS_LIST_BACKEND_VERSION,
				true
			);

			// Localize the script for Ajax Events.
			wp_localize_script(
				'authors-list-js',
				'authorsListAjaxSearch',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'authors-list-search' ),
				)
			);

		}

		/**
		 * Include the required plugin files.
		 */
		public function includes() {

			// Helper class.
			include_once AUTHORS_LIST_BACKEND_INCLUDES . 'class-authors-list-helper.php';

			// Functionality.
			include_once AUTHORS_LIST_BACKEND_INCLUDES . 'class-authors-list-general.php';
			include_once AUTHORS_LIST_BACKEND_INCLUDES . 'class-authors-list-item.php';
			include_once AUTHORS_LIST_BACKEND_INCLUDES . 'class-authors-list-dashboard.php';
			include_once AUTHORS_LIST_BACKEND_INCLUDES . 'class-authors-list-settings.php';
			include_once AUTHORS_LIST_BACKEND_INCLUDES . 'class-authors-list-styler.php';

		}

	}

}

// Instantiate the class.
Authors_List_Backend::instance();
