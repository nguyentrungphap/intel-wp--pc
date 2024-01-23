<?php
/**
 * Dashboard admin page.
 *
 * @package WPKube
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Authors_List_Dashboard' ) ) {

	/**
	 * Dashboard admin page.
	 *
	 * @class Authors_List_Dashboard
	 */
	class Authors_List_Dashboard {

		/**
		 * Suffix for assets.
		 *
		 * @var string
		 */
		public $suffix = '';

		/**
		 * The single instance of the class.
		 *
		 * @var Authors_List_Dashboard
		 */
		protected static $instance = null;

		/**
		 * Main Authors_List_Dashboard Instance.
		 *
		 * Ensures only one instance of Authors_List_Dashboard is loaded or can be loaded.
		 *
		 * @return Authors_List_Dashboard - Main instance.
		 */
		public static function instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();

				/**
				 * Action hook authors_list_dashboard_loaded.
				 *
				 * Hooks after all instance of Authors_List_Dashboard is loaded.
				 */
				do_action( 'authors_list_dashboard_loaded' );
			}

			return self::$instance;

		}

		/**
		 * Authors_List_Dashboard constructor.
		 */
		public function __construct() {

			$this->suffix = '';

			// Scripts and styles.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Add the admin page.
			add_action( 'admin_menu', array( $this, 'add_admin_page' ) );

			// Actions.
			add_action( 'admin_init', array( $this, 'actions' ), 5 );

			// Ajax item preview.
			add_action( 'wp_ajax_authors_list_display_edit_item_preview_ajax', array( $this, 'display_edit_item_preview_ajax' ) );

		}

		/**
		 * Enqueue scripts and styles.
		 *
		 * @param string $hook Hook suffix for the current admin page.
		 */
		public function enqueue_scripts( $hook ) {

			// If not on post add/edit page do not proced.
			if ( 'toplevel_page_authors_list_dashboard' !== $hook ) {
				return;
			}

			// CSS.
			wp_enqueue_style(
				'authors-list-dashboard-css',
				AUTHORS_LIST_BACKEND_URL . 'assets/css/dashboard' . $this->suffix . '.css',
				array(),
				AUTHORS_LIST_BACKEND_VERSION
			);

			// JS.
			wp_enqueue_script(
				'authors-list-dashboard-js',
				AUTHORS_LIST_BACKEND_URL . 'assets/js/dashboard' . $this->suffix . '.js',
				array( 'jquery' ),
				AUTHORS_LIST_BACKEND_VERSION,
				true
			);

			// Localize the script for dashboard item actions.
			wp_localize_script(
				'authors-list-dashboard-js',
				'authorsListDashboardActions',
				array(
					'trash' => esc_html__( 'Are you sure you want to delete this item?', 'authors-list' ),
				)
			);

			// Localize the script.
			wp_localize_script(
				'authors-list-dashboard-js',
				'authorsListSearchFilters',
				array(
					'select' => array(
						'select'       => esc_html__( 'Select', 'authors-list' ),
						'radio'        => esc_html__( 'Radio', 'authors-list' ),
						'checkboxes'   => esc_html__( 'Checkboxes', 'authors-list' ),
						'number_range' => esc_html__( 'Number Range', 'authors-list' ),
					),
					'remove' => esc_html__( 'Remove', 'authors-list' ),
				)
			);

			$authors_list_settings = Authors_List_Settings::instance(); // phpcs:ignore Squiz.Classes.SelfMemberReference.NotUsed

			// For looping through each of the items.
			$settings    = array();
			$item_fields = $authors_list_settings->get_fields();

			// Dependency for the item options.
			foreach ( $item_fields as $item_field ) {
				foreach ( $item_field as $field ) {
					if ( isset( $field['dependency'] ) ) {
						$settings[ $field['id'] ] = $field['dependency'];
					}
				}
			}

			// Localize the script for the dependency set.
			wp_localize_script(
				'authors-list-dashboard-js',
				'authorsListOptionsDependency',
				$settings
			);

		}

		/**
		 * Actions.
		 */
		public function actions() {

			// Do not proceed if user not admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Do not proceed if not making an action.
			if ( ! isset( $_GET['al_action'] ) ) {
				return;
			}

			// Which action are we doing?
			$action = wp_unslash( $_GET['al_action'] );

			// Get the main general class.
			$authors_list_general = Authors_List_General::instance();

			// When "add new" button clicked, create a new item and redirect to the item edit page.
			if ( 'add' === $action ) {

				// Add new item.
				$item = $authors_list_general->add_item();

				// Redirect to edit item page.
				$edit_url = admin_url( 'admin.php?page=authors_list_dashboard&al_action=edit&al_id=' . $item['id'] );
				wp_safe_redirect( $edit_url );

			}

			// When on the edit page, set the current item ID.
			if ( 'edit' === $action ) {

				if ( isset( $_GET['al_id'] ) ) {
					$item_id = (int) $_GET['al_id'];

					if ( $item_id > 0 ) {
						$authors_list_general->set_current_item_id( $item_id );
					}
				}

			}

		}

		/**
		 * Add the admin page.
		 */
		public function add_admin_page() {

			$page_title = esc_html__( 'Dashboard', 'authors-list' );
			$menu_title = esc_html__( 'Authors List', 'authors-list' );
			$capability = 'manage_options';
			$slug       = 'authors_list_dashboard';
			$function   = array( $this, 'display_admin_page' );

			// Parent menu.
			add_menu_page(
				$page_title,
				$menu_title,
				$capability,
				$slug,
				$function,
				'dashicons-admin-users'
			);

			// Main sub-menu.
			add_submenu_page(
				$slug,
				$page_title,
				esc_html__( 'Dashboard', 'authors-list' ),
				$capability,
				$slug,
				$function
			);

		}

		/**
		 * Display the admin page.
		 */
		public function display_admin_page() {

			// Do not proceed if user not admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$action = '';

			if ( isset( $_GET['al_action'] ) ) {
				$action = wp_unslash( $_GET['al_action'] );
			}
			?>
			<div class="wrap">

				<h1><?php esc_html_e( 'Authors List', 'authors-list' ); ?></h1>

				<?php if ( defined( 'AUTHORS_LIST_FREE' ) && defined( 'AUTHORS_LIST_PRO' ) ) : ?>
					<div class="authors-list-free-notice">
						You have both FREE and PRO versions of Authors List plugin enabled.
						<br><a href="<?php echo admin_url( 'plugins.php?plugin_status=active' ); ?>">Please disable the FREE version</a>.
					</div>
				<?php endif; ?>

				<div class="authors-list-dashboard-wrap">

					<?php
					switch ( $action ) {
						case 'edit':
							$this->display_edit();
							$this->display_edit_item_preview();
							break;

						default:
							$this->display_list();
							break;
					}
					?>

				</div><!-- .authors-list-dashboard-wrap -->

			</div>
			<?php

		}

		/**
		 * Display list of items.
		 */
		public function display_list() {

			// Do not proceed if user not admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$authors_list_general = Authors_List_General::instance();
			$authors_list_item    = Authors_List_Item::instance();

			// Item counts ( active/trash ).
			$item_count = $authors_list_general->item_count();

			// Reverse order ( latest to oldest ).
			$items = array_reverse( $authors_list_general->get_items() );

			// Current filter ( active/trash ).
			$filter = 'active';
			if ( isset( $_GET['al_filter'] ) ) {
				$filter = wp_unslash( $_GET['al_filter'] );
			}
			?>

			<div class="authors-list-dashboard-primary">
				<div class="authors-list-dashboard-filter">
					<a href="admin.php?page=authors_list_dashboard" class="<?php echo ( 'active' === $filter ) ? 'authors-list-dashboard-active' : ''; ?>"><?php esc_html_e( 'Active', 'authors-list' ); ?> (<?php echo esc_html( $item_count['active'] ); ?>)</a>
					|
					<a href="admin.php?page=authors_list_dashboard&al_filter=trash" class="<?php echo ( 'trash' === $filter ) ? 'authors-list-dashboard-active' : ''; ?>"><?php esc_html_e( 'Trash', 'authors-list' ); ?> (<?php echo esc_html( $item_count['trash'] ); ?>)</a>
				</div>

				<table>
					<thead>
						<tr>
							<th><?php esc_html_e( 'ID', 'authors-list' ); ?></th>
							<th><?php esc_html_e( 'Name', 'authors-list' ); ?></th>
							<th><?php esc_html_e( 'Shortcode', 'authors-list' ); ?></th>
							<th>&nbsp;</th>
						</tr>
					</thead>

					<tbody>
						<?php if ( ! empty( $items ) ) : ?>
							<?php foreach ( $items as $item ) : ?>
								<?php
								if ( 'active' === $filter && 'active' !== $item['status'] ) {
									continue;
								}
								if ( 'trash' === $filter && 'trash' !== $item['status'] ) {
									continue;
								}

								$item_data = $authors_list_item->get_item_data( $item['id'] );
								if ( empty( $item_data['settings']['name'] ) ) {
									$item_data['settings']['name'] = esc_html__( 'Untitled item #', 'authors-list' ) . $item['id'];
								}
								?>

								<tr>
									<td><?php echo esc_html( $item['id'] ); ?></td>
									<td><?php echo esc_html( $item_data['settings']['name'] ); ?></td>
									<td><input type="text" value="[authors_list id=<?php echo esc_attr( $item['id'] ); ?>]" disabled ></td>
									<td class="authors-list-dashboard-table-actions">
									<?php if ( 'active' === $filter ) : ?>
										<a href="?page=authors_list_dashboard&al_action=edit&al_id=<?php echo esc_attr( $item['id'] ); ?>" class="authors-list-settings-link"><?php esc_html_e( 'Settings', 'authors-list' ); ?></a>
										&nbsp;-&nbsp;
										<a href="?page=authors_list_dashboard&al_do=trash&al_id=<?php echo esc_attr( $item['id'] ); ?>&trash_nonce=<?php echo wp_create_nonce( 'authors-list-trash-item' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" class="authors-list-trash-link"><?php esc_html_e( 'Trash', 'authors-list' ); ?></a>
										&nbsp;-&nbsp;
										<a href="?page=authors_list_dashboard&al_do=duplicate&al_id=<?php echo esc_attr( $item['id'] ); ?>&duplicate_nonce=<?php echo wp_create_nonce( 'authors-list-duplicate-item' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" class="authors-list-duplicate-link"><?php esc_html_e( 'Duplicate', 'authors-list' ); ?></a>
									<?php elseif ( 'trash' === $filter ) : ?>
										<a href="?page=authors_list_dashboard&al_do=restore&al_filter=trash&al_id=<?php echo esc_attr( $item['id'] ); ?>&restore_nonce=<?php echo wp_create_nonce( 'authors-list-restore-item' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" class="authors-list-restore-link"><?php esc_html_e( 'Restore', 'authors-list' ); ?></a>
										&nbsp;-&nbsp;
										<a href="?page=authors_list_dashboard&al_do=delete&al_filter=trash&al_id=<?php echo esc_attr( $item['id'] ); ?>&delete_nonce=<?php echo wp_create_nonce( 'authors-list-delete-item' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" class="authors-list-delete-link"><?php esc_html_e( 'Delete', 'authors-list' ); ?></a>
									<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>

				<?php if ( 'trash' === $filter && 0 === $item_count['trash'] ) : ?>
					<p><?php esc_html_e( 'You do not have any authors list in the trash at the moment.', 'authors-list' ); ?></p>
				<?php endif; ?>

				<?php if ( 'active' === $filter && 0 === $item_count['active'] ) : ?>
					<p><?php esc_html_e( 'You do not have any active authors list at the moment. Add one using the button below.', 'authors-list' ); ?></p>
				<?php endif; ?>

				<?php if ( 'trash' !== $filter ) : ?>
					<?php $next_item_id = (int) $authors_list_general->get_new_item_id( false ); ?>

					<div class="authors-list-dashboard-actions">
						<a href="?page=authors_list_dashboard&al_action=add&al_id=<?php echo esc_attr( $next_item_id ); ?>" class="button button-primary"><?php esc_html_e( 'Add New', 'authors-list' ); ?></a>
					</div>
				<?php endif; ?>
			</div>

			<?php

		}

		/**
		 * Display edit screen.
		 */
		public function display_edit() {

			// Do not proceed if user not admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			?>
			<div class="authors-list-dashboard-primary">
				<?php do_action( 'authors_list_settings_item' ); ?>
			</div>
			<?php

		}

		/**
		 * Display preview.
		 */
		public function display_edit_item_preview() {

			// Do not proceed if user not admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$authors_list_general = Authors_List_General::instance();
			$item_id              = $authors_list_general->get_current_item_id();
			?>
			<div class="authors-list-dashboard-preview" data-item-id="<?php echo esc_attr( $item_id ); ?>">
				<div class="authors-list-dashboard-preview-wrap">
					<span>&darr; live preview of an author as it would look in the list &darr;</span>
					<div class="authors-list-dashboard-preview-inner">
						<div class="authors-list-dashboard-preview-styler-disabled"></div>
						<?php echo do_shortcode( '[authors_list load_preview="true" id=' . $item_id . ']' ); ?>
					</div>
					<div class="authors-list-dashboard-preview-loader"><?php esc_html_e( 'refreshing...', 'authors-list' ); ?></div>
				</div>
			</div>
			<?php

		}

		/**
		 * Display preview - AJAX.
		 */
		public function display_edit_item_preview_ajax() {

			// Do not proceed if user not admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$item_id  = isset( $_POST['item_id'] ) ? (int) $_POST['item_id'] : 0;
			$settings = isset( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : '';

			parse_str( $settings, $settings_array );

			update_option( 'authors_list_item_draft_' . $item_id, $settings_array[ 'authors_list_item_settings_' . $item_id ] );

			$data            = array();
			$data['disable'] = '<div class="authors-list-dashboard-preview-styler-disabled"></div>';
			$data['output']  = do_shortcode( '[authors_list preview="true" id=' . $item_id . ']' );

			wp_send_json_success( $data );

		}

	}

}

// Instantiate the class.
Authors_List_Dashboard::instance();
