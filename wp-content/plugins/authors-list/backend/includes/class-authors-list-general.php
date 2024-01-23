<?php
/**
 * General plugin class.
 *
 * @package WPKube
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Authors_List_General' ) ) {

	/**
	 * General plugin class.
	 */
	class Authors_List_General {

		/**
		 * Current item id.
		 *
		 * @var int
		 */
		public $current_item_id = 0;

		/**
		 * The single instance of the class.
		 *
		 * @var Authors_List_General
		 */
		protected static $instance = null;

		/**
		 * Main Authors_List_General Instance.
		 *
		 * Ensures only one instance of Authors_List_General is loaded or can be loaded.
		 *
		 * @return Authors_List_General - Main instance.
		 */
		public static function instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();

				/**
				 * Action hook authors_list_general_loaded.
				 *
				 * Hooks after all instance of Authors_List_General is loaded.
				 */
				do_action( 'authors_list_general_loaded' );
			}

			return self::$instance;

		}

		/**
		 * Authors_List_General constructor.
		 */
		public function __construct() {

			// Item actions.
			add_action( 'init', array( $this, 'trash_item' ) );
			add_action( 'init', array( $this, 'delete_item' ) );
			add_action( 'init', array( $this, 'restore_item' ) );
			add_action( 'init', array( $this, 'duplicate_item' ) );

		}

		/**
		 * Get items.
		 */
		public function get_items() {
			return get_option( 'authors_list_items', array() );
		}

		/**
		 * Get data.
		 */
		public function get_data() {
			return get_option( 'authors_list_data', array() );
		}

		/**
		 * Get new ID.
		 *
		 * @param bool $update The update data.
		 *
		 * @return int Updated post id.
		 */
		public function get_new_item_id( $update = true ) {

			// Get data.
			$data = $this->get_data();

			// Increment ID value.
			if ( isset( $data['id'] ) ) {
				$data['id']++;

			// If no value exists, start from 1.
			} else {
				$data['id'] = 1;
			}

			// Update data.
			if ( $update ) {
				update_option( 'authors_list_data', $data );
			}

			// Pass back the ID.
			return $data['id'];

		}

		/**
		 * Create new item.
		 */
		public function add_item() {

			// Do not proceed if user not admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Get a new ID.
			$item_id = $this->get_new_item_id();

			// Get current items.
			$items = $this->get_items();

			// Add new item.
			$items[ 'authors-list-' . $item_id ] = array(
				'id'         => $item_id,
				'name'       => esc_html__( 'Untitled authors list', 'authors-list' ) . ' ' . $item_id,
				'date'       => current_time( 'timestamp' ), // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
				'date_mysql' => current_time( 'mysql' ),
				'author'     => get_current_user_id(),
				'status'     => 'active',
				'settings'   => array(),
			);

			// Update DB.
			update_option( 'authors_list_items', $items );

			// Pass back the new item.
			return $items[ 'authors-list-' . $item_id ];

		}

		/**
		 * Set current item ID.
		 *
		 * @param int $id item id.
		 */
		public function set_current_item_id( $id ) {
			$this->current_item_id = $id;
		}

		/**
		 * Get current item ID.
		 */
		public function get_current_item_id() {
			return $this->current_item_id;
		}

		/**
		 * Trash item.
		 */
		public function trash_item() {

			// Do not proceed if not doing trash action.
			if ( ! isset( $_GET['al_do'] ) || 'trash' !== $_GET['al_do'] ) {
				return;
			}

			// Do not proceed if user not admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Do not proceed if the nonce is not valid.
			$request_nonce = isset( $_REQUEST['trash_nonce'] ) ? wp_unslash( $_REQUEST['trash_nonce'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ! wp_verify_nonce( $request_nonce, 'authors-list-trash-item' ) ) {
				return;
			}

			// Get item data.
			$item_id = isset( $_GET['al_id'] ) ? (int) $_GET['al_id'] : 0;
			$items   = $this->get_items();

			// Switch status to trash.
			$items[ 'authors-list-' . $item_id ]['status'] = 'trash';

			// Update item data.
			update_option( 'authors_list_items', $items );

		}

		/**
		 * Delete item.
		 */
		public function delete_item() {

			// Do not proceed if not doing delete action.
			if ( ! isset( $_GET['al_do'] ) || 'delete' !== $_GET['al_do'] ) {
				return;
			}

			// Do not proceed if user not admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Do not proceed if the nonce is not valid.
			$request_nonce = isset( $_REQUEST['delete_nonce'] ) ? wp_unslash( $_REQUEST['delete_nonce'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ! wp_verify_nonce( $request_nonce, 'authors-list-delete-item' ) ) {
				return;
			}

			// Get item data.
			$item_id = isset( $_GET['al_id'] ) ? (int) $_GET['al_id'] : 0;
			$items   = $this->get_items();

			// Switch to trash.
			unset( $items[ 'authors-list-' . $item_id ] );

			// Update item data.
			update_option( 'authors_list_items', $items );

			// Delete item settings.
			delete_option( 'authors_list_item_settings_' . $item_id );

		}

		/**
		 * Restore item.
		 */
		public function restore_item() {

			// Do not proceed if not doing restore action.
			if ( ! isset( $_GET['al_do'] ) || 'restore' !== $_GET['al_do'] ) {
				return;
			}

			// Do not proceed if user not admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Do not proceed if the nonce is not valid.
			$request_nonce = isset( $_REQUEST['restore_nonce'] ) ? wp_unslash( $_REQUEST['restore_nonce'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ! wp_verify_nonce( $request_nonce, 'authors-list-restore-item' ) ) {
				return;
			}

			// Get item data.
			$item_id = isset( $_GET['al_id'] ) ? (int) $_GET['al_id'] : 0;
			$items   = $this->get_items();

			// Switch to active.
			$items[ 'authors-list-' . $item_id ]['status'] = 'active';

			// Update item data.
			update_option( 'authors_list_items', $items );

		}

		/**
		 * Duplicate item.
		 */
		public function duplicate_item() {

			// Do not proceed if not doing duplicate action.
			if ( ! isset( $_GET['al_do'] ) || 'duplicate' !== $_GET['al_do'] ) {
				return;
			}

			// Do not proceed if user not admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Do not proceed if the nonce is not valid.
			$request_nonce = isset( $_REQUEST['duplicate_nonce'] ) ? wp_unslash( $_REQUEST['duplicate_nonce'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ! wp_verify_nonce( $request_nonce, 'authors-list-duplicate-item' ) ) {
				return;
			}

			// Get item data.
			$item_id = isset( $_GET['al_id'] ) ? (int) $_GET['al_id'] : 0;
			$items   = $this->get_items();

			// Get a new ID.
			$item_new_id = $this->get_new_item_id();

			// Add new item.
			$items[ 'authors-list-' . $item_new_id ] = array(
				'id'         => $item_new_id,
				'name'       => esc_html__( 'Untitled authors list', 'authors-list' ) . ' ' . $item_new_id,
				'date'       => current_time( 'timestamp' ), // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
				'date_mysql' => current_time( 'mysql' ),
				'author'     => get_current_user_id(),
				'status'     => 'active',
				'settings'   => array(),
			);

			// Update DB.
			update_option( 'authors_list_items', $items );

			// Update the settings.
			$old_item_settings = $this->get_item_settings( $item_id );
			update_option( 'authors_list_item_settings_' . $item_new_id, $old_item_settings );

		}

		/**
		 * Get amount of items.
		 */
		public function item_count() {

			$items  = $this->get_items();
			$counts = array();

			$counts['active'] = 0;
			$counts['trash']  = 0;

			foreach ( $items as $item ) {
				if ( 'active' === $item['status'] ) {
					$counts['active']++;
				} elseif ( 'trash' === $item['status'] ) {
					$counts['trash']++;
				}
			}

			return $counts;

		}

		/**
		 * Get settings of a specific item.
		 *
		 * @param int $item_id item id.
		 *
		 * @return mixed item settings.
		 */
		public function get_item_settings( $item_id ) {
			return get_option( 'authors_list_item_settings_' . $item_id, array() );
		}

	}

}

// Instantiate the class.
Authors_List_General::instance();
