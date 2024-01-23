<?php
/**
 * Settings page.
 *
 * @package WPKube
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Authors_List_Settings' ) ) {

	/**
	 * Settings page.
	 *
	 * @class Authors_List_Settings
	 */
	class Authors_List_Settings {

		/**
		 * The single instance of the class.
		 *
		 * @var Authors_List_Settings
		 */
		protected static $instance = null;

		/**
		 * Main Authors_List_Settings Instance.
		 *
		 * Ensures only one instance of Authors_List_Settings is loaded or can be loaded.
		 *
		 * @return Authors_List_Settings - Main instance.
		 */
		public static function instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();

				/**
				 * Action hook authors_list_settings_loaded.
				 *
				 * Hooks after all instance of Authors_List_Settings is loaded.
				 */
				do_action( 'authors_list_settings_loaded' );
			}

			return self::$instance;

		}

		/**
		 * Authors_List_Settings constructor.
		 */
		public function __construct() {
			add_action( 'authors_list_settings_item', array( $this, 'display_item' ) );
		}

		/**
		 * Define sections.
		 */
		public static function get_sections() {

			// Array of sections.
			$sections = array();

			$sections[] = array(
				'title' => esc_html__( 'General', 'authors-list' ),
				'id'    => 'general',
			);

			$sections[] = array(
				'title' => esc_html__( 'Query', 'authors-list' ),
				'id'    => 'query',
			);

			$sections[] = array(
				'title' => esc_html__( 'Layout', 'authors-list' ),
				'id'    => 'design',
			);

			$sections[] = array(
				'title' => esc_html__( 'Content', 'authors-list' ),
				'id'    => 'content',
			);

			$sections[] = array(
				'title' => esc_html__( 'Search & Filters', 'authors-list' ),
				'id'    => 'search_filters',
				'pro' => 'full',
			);

			$sections[] = array(
				'title' => esc_html__( 'Styling', 'authors-list' ),
				'id'    => 'styling',
				'pro' => 'full',
			);

			// Pass it back.
			return apply_filters( 'authors_list_item_settings_sections', $sections );

		}

		/**
		 * Define fields.
		 */
		public static function get_fields() {

			// Start with an empty array which we populate below.
			$fields = array();

			$fields['general'] = array(
				array(
					'title' => esc_html__( 'Name', 'authors-list' ),
					'descr' => esc_html__( 'Internal name for this authors list. For your own reference.', 'authors-list' ),
					'id'    => 'name',
					'type'  => 'text',
				),
				array(
					'title' => esc_html__( 'Shortcode', 'authors-list' ),
					'descr' => esc_html__( 'Add this shortcode in the place where you want to show the authors list.', 'authors-list' ),
					'id'    => 'shortcode',
					'type'  => 'shortcode',
				),
			);

			$fields['search_filters'] = array(
				array(
					'title'      => esc_html__( 'Position', 'authors-list' ),
					'id'         => 'filters_position',
					'type'       => 'select',
					'choices'    => array(
						'left'  => esc_html__( 'Left', 'authors-list' ),
						'above' => esc_html__( 'Above', 'authors-list' ),
						'right' => esc_html__( 'Right', 'authors-list' ),
					),
					'default'    => 'left',
				),
				array(
					'title'   => esc_html__( 'Search - Enable', 'authors-list' ),
					'id'      => 'search_enable',
					'type'    => 'checkbox',
					'default' => 'no',
				),
				array(
					'title'      => esc_html__( 'Search - Columns', 'authors-list' ),
					'descr'      => esc_html__( 'Which columns should be searched? If none selected, all will be searched.', 'authors-list' ),
					'id'         => 'search_cols',
					'type'       => 'multicheck',
					'choices'    => array(
						'ID'            => esc_html__( 'ID', 'authors-list' ),
						'user_login'    => esc_html__( 'Login', 'authors-list' ),
						'user_email'    => esc_html__( 'Email', 'authors-list' ),
						'user_url'      => esc_html__( 'URL', 'authors-list' ),
						'user_nicename' => esc_html__( 'Nicename', 'authors-list' ),
						'display_name'  => esc_html__( 'Display Name', 'authors-list' ),
					),
					'dependency' => array(
						'search_enable' => true,
					),
				),
				array(
					'title'   => esc_html__( 'Filters - Enable', 'authors-list' ),
					'id'      => 'filters_enable',
					'type'    => 'checkbox',
					'default' => 'no',
				),
				array(
					'title'      => esc_html__( 'Filters', 'authors-list' ),
					'id'         => 'filters',
					'type'       => 'search_filters',
					'descr'      => esc_html__( 'Add the required filter types of author. It takes the values of the author meta_key, label and display types respectively.', 'authors-list' ),
					'dependency' => array(
						'filters_enable' => true,
					),
				),
				array(
					'title' => esc_html__( 'Button Text', 'authors-list' ),
					'id'    => 'button_text',
					'type'  => 'text',
				),
			);

			$fields['design'] = array(
				array(
					'title'            => esc_html__( 'Style', 'authors-list' ),
					'id'               => 'style',
					'type'             => 'select',
					'choices'          => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
					),
					'default'          => '1',
					'reload_on_change' => true,
				),
				array(
					'title'   => esc_html__( 'Columns per Row', 'authors-list' ),
					'id'      => 'columns',
					'type'    => 'select',
					'choices' => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
					),
					'default' => '4',
				),
				array(
					'title'   => esc_html__( 'Columns Direction', 'authors-list' ),
					'id'      => 'columns_direction',
					'type'    => 'select',
					'choices' => array(
						'horizontal' => esc_html__( 'Horizontal', 'authors-list' ),
						'vertical'   => esc_html__( 'Vertical', 'authors-list' ),
					),
					'default' => 'horizontal',
				),
			);

			$fields['content'] = array(
				array(
					'title'            => esc_html__( 'Show Title', 'authors-list' ),
					'id'               => 'show_title',
					'type'             => 'checkbox',
					'default'          => 'yes',
					'reload_on_change' => true,
				),
				array(
					'title'            => esc_html__( 'Show Count', 'authors-list' ),
					'id'               => 'show_count',
					'type'             => 'checkbox',
					'default'          => 'yes',
					'reload_on_change' => true,
				),
				array(
					'title'            => esc_html__( 'Show Bio', 'authors-list' ),
					'id'               => 'show_bio',
					'type'             => 'checkbox',
					'default'          => 'yes',
					'reload_on_change' => true,
				),
				array(
					'title'            => esc_html__( 'Show Link', 'authors-list' ),
					'id'               => 'show_link',
					'type'             => 'checkbox',
					'default'          => 'yes',
					'reload_on_change' => true,
				),
				array(
					'title'            => esc_html__( 'Count Text', 'authors-list' ),
					'id'               => 'count_text',
					'type'             => 'text',
					'default'          => 'posts',
					'reload_on_change' => true,
				),
				array(
					'title'            => esc_html__( 'Before Avatar', 'authors-list' ),
					'id'               => 'before_avatar',
					'type'             => 'text',
					'default'          => '',
					'reload_on_change' => true,
				),
				array(
					'title'            => esc_html__( 'Before Title', 'authors-list' ),
					'id'               => 'before_title',
					'type'             => 'text',
					'default'          => '',
					'reload_on_change' => true,
				),
				array(
					'title'            => esc_html__( 'Before Count', 'authors-list' ),
					'id'               => 'before_count',
					'type'             => 'text',
					'default'          => '',
					'reload_on_change' => true,
				),
				array(
					'title'            => esc_html__( 'Before Bio', 'authors-list' ),
					'id'               => 'before_bio',
					'type'             => 'text',
					'default'          => '',
					'reload_on_change' => true,
				),
				array(
					'title'            => esc_html__( 'Before Link', 'authors-list' ),
					'id'               => 'before_link',
					'type'             => 'text',
					'default'          => '',
					'reload_on_change' => true,
				),
				array(
					'title'            => esc_html__( 'After Avatar', 'authors-list' ),
					'id'               => 'after_avatar',
					'type'             => 'text',
					'default'          => '',
					'reload_on_change' => true,
				),
				array(
					'title'            => esc_html__( 'After Title', 'authors-list' ),
					'id'               => 'after_title',
					'type'             => 'text',
					'default'          => '',
					'reload_on_change' => true,
				),
				array(
					'title'            => esc_html__( 'After Count', 'authors-list' ),
					'id'               => 'after_count',
					'type'             => 'text',
					'default'          => '',
					'reload_on_change' => true,
				),
				array(
					'title'            => esc_html__( 'After Bio', 'authors-list' ),
					'id'               => 'after_bio',
					'type'             => 'text',
					'default'          => '',
					'reload_on_change' => true,
				),
				array(
					'title'            => esc_html__( 'After Link', 'authors-list' ),
					'id'               => 'after_link',
					'type'             => 'text',
					'default'          => '',
					'reload_on_change' => true,
				),
				array(
					'title'            => esc_html__( 'Title Element', 'authors-list' ),
					'id'               => 'title_element',
					'type'             => 'select',
					'choices'          => array(
						'div'  => esc_html__( '<div>', 'authors-list' ),
						'h2' => esc_html__( '<h2>', 'authors-list' ),
						'h3' => esc_html__( '<h3>', 'authors-list' ),
						'h4' => esc_html__( '<h4>', 'authors-list' ),
						'h5' => esc_html__( '<h5>', 'authors-list' ),
						'h6' => esc_html__( '<h6>', 'authors-list' ),
					),
					'default'          => 'div',
					'descr'            => esc_html__( 'The element that wraps the name of the author. Defaults to div, can be any element, for example h2', 'authors-list' ),
				),
				array(
					'title'            => esc_html__( 'Link To', 'authors-list' ),
					'id'               => 'link_to',
					'type'             => 'select',
					'choices'          => array(
						'archive'  => esc_html__( 'Archive', 'authors-list' ),
						'bbpress_profile' => esc_html__( 'bbPress Profile', 'authors-list' ),
						'buddypress_profile' => esc_html__( 'BuddyPress Profile', 'authors-list' ),
						'meta' => esc_html__( 'User Meta Value', 'authors-list' ),
					),
					'default'          => 'archive',
					'descr'            => esc_html__( 'Where should the links for the authors go to?', 'authors-list' ),
				),
				array(
					'title'            => esc_html__( 'Link To Meta Key', 'authors-list' ),
					'id'               => 'link_to_meta_key',
					'type'             => 'text',
					'default'          => '',
					'descr'            => esc_html__( 'Author meta key when the Link To option is set as "User Meta Value".', 'authors-list' ),
				),
				array(
					'title'            => esc_html__( 'Avatar Size', 'authors-list' ),
					'id'               => 'avatar_size',
					'descr'            => esc_html__( 'Size for the avatar image (used when requesting the image from Gravatar)', 'authors-list' ),
					'type'             => 'text',
					'default'          => '500',
					'reload_on_change' => true,
				),
				array(
					'title'            => esc_html__( 'Avatar Meta Key', 'authors-list' ),
					'descr'            => esc_html__( 'If you wish the avatar URL to be based on "user meta" value instead of default WP avatar, enter the "user meta" key here.', 'authors-list' ),
					'id'               => 'avatar_meta_key',
					'type'             => 'text',
					'default'          => '',
					'reload_on_change' => true,
				),
				array(
					'title'            => esc_html__( 'Bio Word Trim', 'authors-list' ),
					'descr'            => esc_html__( 'Trim the "biography" (description) of the author to a specific number of words.', 'authors-list' ),
					'id'               => 'bio_word_trim',
					'type'             => 'text',
					'reload_on_change' => true,
				),
			);

			$fields['styling'] = array(
				array(
					'id'               => 'styler_enable',
					'title'            => esc_html__( 'Enable Styler', 'authors-list' ),
					'type'             => 'checkbox',
					'default'          => 'no',
					'reload_on_change' => true,
				),
				array(
					'id'         => 'styler',
					'title'      => esc_html__( 'Styler', 'authors-list' ),
					'type'       => 'styler',
					'default'    => '',
					'dependency' => array(
						'styler_enable' => true,
					),
				),
				array(
					'id'         => 'styler_js_data',
					'title'      => esc_html__( 'Styler JS data', 'authors-list' ),
					'type'       => 'textarea',
					'default'    => '',
					'field_id'   => 'authors-list-styler-panel-data',
					'dependency' => array(
						'styler_enable' => true,
					),
				),
				array(
					'id'         => 'custom_css',
					'title'      => esc_html__( 'Custom CSS', 'authors-list' ),
					'type'       => 'textarea',
					'default'    => '',
					'dependency' => array(
						'styler_enable' => true,
					),
				),
			);

			$fields['query'] = array(
				array(
					'title'            => esc_html__( 'Amount', 'authors-list' ),
					'descr'            => esc_html__( 'How many authors to show?', 'authors-list' ),
					'id'               => 'amount',
					'type'             => 'text',
				),
				array(
					'title'            => esc_html__( 'Pagination', 'authors-list' ),
					'descr'            => esc_html__( 'If enabled, the list will be paginated, with the amount of items per page taken from the "Amount" option above.', 'authors-list' ),
					'id'               => 'pagination',
					'type'             => 'checkbox',
					'default'          => 'no',
				),
				array(
					'title'            => esc_html__( 'Minimum Posts', 'authors-list' ),
					'descr'            => esc_html__( 'Minimum amount of posts an authors needs to have to be shown in the list.', 'authors-list' ),
					'id'               => 'minimum_posts_count',
					'type'             => 'text',
					'default'          => 0,
				),
				array(
					'title'            => esc_html__( 'Skip Empty', 'authors-list' ),
					'descr'            => esc_html__( 'Skip authors that do not have any posts published.', 'authors-list' ),
					'id'               => 'skip_empty',
					'type'             => 'checkbox',
					'default'          => 'yes',
				),
				array(
					'title'            => esc_html__( 'Only Authors', 'authors-list' ),
					'descr'            => esc_html__( 'Display only authors. Disable this to include all users.', 'authors-list' ),
					'id'               => 'only_authors',
					'type'             => 'checkbox',
					'default'          => 'yes',
				),
				array(
					'title'            => esc_html__( 'Orderby', 'authors-list' ),
					'id'               => 'orderby',
					'type'             => 'select',
					'choices'          => array(
						'post_count'   => esc_html__( 'Total Posts Count', 'authors-list' ),
						'post_date'    => esc_html__( 'Latest Post Date', 'authors-list' ),
						'ID'           => esc_html__( 'User ID', 'authors-list' ),
						'login'        => esc_html__( 'Username', 'authors-list' ),
						'nicename'     => esc_html__( 'Nicename', 'authors-list' ),
						'email'        => esc_html__( 'Email', 'authors-list' ),
						'url'          => esc_html__( 'URL', 'authors-list' ),
						'registered'   => esc_html__( 'Registration Date', 'authors-list' ),
						'display_name' => esc_html__( 'Display Name', 'authors-list' ),
						'first_name'   => esc_html__( 'First Name', 'authors-list' ),
						'last_name'    => esc_html__( 'Last Name', 'authors-list' ),
					),
					'default'          => 'post_count',
				),
				array(
					'title'            => esc_html__( 'Order', 'authors-list' ),
					'id'               => 'order',
					'type'             => 'select',
					'choices'          => array(
						'ASC'  => esc_html__( 'Ascending', 'authors-list' ),
						'DESC' => esc_html__( 'Descending', 'authors-list' ),
					),
					'default'          => 'DESC',
				),
				array(
					'title'            => esc_html__( 'Exclude Authors', 'authors-list' ),
					'id'               => 'exclude',
					'type'             => 'text',
					'descr'            => esc_html__( 'User IDs separated by comma, example 1,3,4', 'authors-list' ),
				),
				array(
					'title'            => esc_html__( 'Include Authors', 'authors-list' ),
					'id'               => 'include',
					'type'             => 'text',
					'descr'            => esc_html__( 'User IDs separated by comma, example 1,3,4', 'authors-list' ),
				),
				array(
					'title'            => esc_html__( 'Roles', 'authors-list' ),
					'descr'            => __( 'Roles separated by comma, example administrator,editor. More info on roles in <a href="https://wordpress.org/support/article/roles-and-capabilities/#summary-of-roles" target="_blank">WordPress Docs</a>', 'authors-list' ),
					'id'               => 'roles',
					'type'             => 'text',
				),
				array(
					'title'            => esc_html__( 'Post Types', 'authors-list' ),
					'id'               => 'post_types',
					'type'             => 'text',
					'descr'            => __( 'Post types separated by comma, example post,page,portfolio. . More info on roles in <a href="https://wordpress.org/support/article/post-types/" target="_blank">WordPress Docs</a>', 'authors-list' ),
				),
				array(
					'title'            => esc_html__( 'Published in Past X Days', 'authors-list' ),
					'id'               => 'latest_post_after',
					'type'             => 'text',
					'descr'            => esc_html__( 'For example if set to 7 it will only show authors that have posts published in the past 7 days', 'authors-list' ),
				),
				array(
					'title'            => esc_html__( 'Display Name Starts With', 'authors-list' ),
					'id'               => 'name_starts_with',
					'type'             => 'text',
					'descr'            => esc_html__( 'Limit to authors whose display name starts with specific characters', 'authors-list' ),
				),
				array(
					'title'            => esc_html__( 'First Name Starts With', 'authors-list' ),
					'id'               => 'first_name_starts_with',
					'type'             => 'text',
					'descr'            => esc_html__( 'Limit to authors whose first name starts with specific characters', 'authors-list' ),
				),
				array(
					'title'            => esc_html__( 'Last Name Starts With', 'authors-list' ),
					'id'               => 'last_name_starts_with',
					'type'             => 'text',
					'descr'            => esc_html__( 'Limit to authors whose last name starts with specific characters', 'authors-list' ),
				),
				array(
					'title'            => esc_html__( 'Categories', 'authors-list' ),
					'id'               => 'categories',
					'type'             => 'text',
					'descr'            => esc_html__( 'Category IDs separated by comma, example 1,3,4', 'authors-list' ),
				),
				array(
					'title'            => esc_html__( 'Taxonomy', 'authors-list' ),
					'id'               => 'taxonomy',
					'type'             => 'text',
					'descr'            => esc_html__( 'Name of a taxonomy, for example post_tag', 'authors-list' ),
				),
				array(
					'title'            => esc_html__( 'Terms', 'authors-list' ),
					'id'               => 'terms',
					'type'             => 'text',
					'descr'            => esc_html__( 'Term IDs separated by comma, example 1,3,4', 'authors-list' ),
				),
				array(
					'title'            => esc_html__( 'Buddypress member type', 'authors-list' ),
					'id'               => 'bp_member_types',
					'type'             => 'text',
					'descr'            => esc_html__( 'Buddypress member type separated by comma, example student,teacher', 'authors-list' ),
				),
			);

			// Pass it back.
			return apply_filters( 'authors_list_item_settings_fields', $fields );

		}

		/**
		 * Display settings item.
		 */
		public function display_item() {

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// The ID in which the item data is stored.
			$authors_list_general = Authors_List_General::instance();
			$option_id            = 'authors_list_item_settings_' . $authors_list_general->get_current_item_id();

			// Get sections and fields.
			$sections = $this->get_sections();
			$fields   = $this->get_fields();

			// Save settings.
			$settings_saved = false;
			if ( isset( $_POST['al_option_name'] ) && isset( $_POST['authors_list_save_nonce'] ) ) {

				// Do not proceed if the save is not valid.
				$save_nonce = isset( $_POST['authors_list_save_nonce'] ) ? wp_unslash( $_POST['authors_list_save_nonce'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				if ( ! wp_verify_nonce( $save_nonce, 'authors-list-save-settings' ) ) {
					return;
				}

				$option_name     = wp_unslash( $_POST['al_option_name'] );
				$checkbox_values = array();
				foreach ( $sections as $section ) {
					foreach ( $fields[ $section['id'] ] as $field ) {
						if ( 'checkbox' === $field['type'] ) {
							if ( isset( $_POST[ $option_name ][ $field['id'] ] ) ) {
								$checkbox_values[ $field['id'] ] = 'yes';
							} else {
								$checkbox_values[ $field['id'] ] = 'no';
							}
						}
					}
				}
				$all_options = array_merge( wp_unslash( $_POST[ $option_name ] ), $checkbox_values ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated;
				update_option( $option_name, $all_options );
				$settings_saved = true;
			}

			// Get current section details for active class.
			$active_section = isset( $_POST['al_get_section'] ) ? sanitize_text_field( wp_unslash( $_POST['al_get_section'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			?>

			<div class="authors-list-above-item-settings">
				<p><a href="?page=authors_list_dashboard"><?php esc_html_e( '&larr; Back to overview', 'authors-list' ); ?></a></p>

				<?php if ( $settings_saved ) : ?>
					<div class="authors-list-settings-notice-success"><?php esc_html_e( 'Settings Saved', 'authors-list' ); ?></div>
				<?php endif; ?>
			</div>

			<div class="authors-list-settings">

				<h2 class="nav-tab-wrapper authors-list-settings-nav">
					<?php foreach ( $sections as $section ) : ?>
						<?php
						// Data - id.
						$data = 'data-of-id="' . esc_attr( $section['id'] ) . '" ';
						?>

						<a href="#" class="nav-tab <?php echo ( $section['id'] === $active_section ) ? 'nav-tab-active' : ''; ?>" <?php echo $data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php 
							echo esc_html( $section['title'] ); 
							if ( ! empty( $section['pro'] ) && $section['pro'] == 'full' ) {
								if ( ! defined( 'AUTHORS_LIST_PRO' ) ) {
									echo '<span class="authors-list-settings-nav-pro-indicator">PRO</span>';
								}
							}
						?></a>
					<?php endforeach; ?>
				</h2>

				<div class="authors-list-settings-main">
					<form action="" method="post">
						
						<input type="hidden" name="al_option_name" value="<?php echo esc_attr( $option_id ); ?>">
						<input type="hidden" name="al_get_section" class="authors-list-selected-section" value="<?php echo esc_attr( $active_section ); ?>">
						<?php wp_nonce_field( 'authors-list-save-settings', 'authors_list_save_nonce' ); ?>

						<?php foreach ( $sections as $section ) : ?>

							<div class="authors-list-settings-section <?php echo ( $section['id'] === $active_section ) ? 'authors-list-active' : ''; ?>" data-of-id="<?php echo esc_attr( $section['id'] ); ?>">

								<div class="authors-list-settings-section-heading">
									<h2><?php echo esc_html( $section['title'] ); ?></h2>
									<?php if ( ! empty( $section['descr'] ) ) : ?>
										<div class="authors-list-settings-section-description"><?php echo wp_kses_post( $section['descr'] ); ?></div>
									<?php endif; ?>
								</div><!-- .authors-list-settings-section-heading -->

								<?php
								foreach ( $fields[ $section['id'] ] as $field ) :
									$this->display_item_field( $field, $option_id );
								endforeach;
								?>

								<?php if ( ! empty( $section['pro'] ) ) : ?>
									<?php if ( ! defined( 'AUTHORS_LIST_PRO' ) ) : ?>
										<div class="authors-list-settings-section-pro-cover">
											<div class="authors-list-settings-section-pro-cover-text">
												<?php if ( $section['pro'] == 'full' ) : ?>
													<p>These settings are available in the PRO version of the plugin.</p>
												<?php elseif ( $section['pro'] == 'half' ) : ?>
													<p>You can manually set these settings with shortcode parameters. <a href="#">More info here.</a></p>
													<p>These settings are available in the PRO version of the plugin.</p>
												<?php endif; ?>
												<p><a href="https://www.authorslist.com/" target="_blank" class="button button-secondary">Get Authors List PRO</a></p>
											</div>
										</div>
									<?php endif; ?>
								<?php endif; ?>

							</div>

						<?php endforeach; ?>

						<?php submit_button(); ?>

						<?php if ( $settings_saved ) : ?>
							<div class="authors-list-settings-notice-success"><?php esc_html_e( 'Settings Saved', 'authors-list' ); ?></div>
						<?php endif; ?>
					</form>
				</div>

			</div>
			<?php

		}

		/**
		 * Display singular item field.
		 *
		 * @param string[] $field     The feild name.
		 * @param string   $option_id The option id.
		 */
		public function display_item_field( $field, $option_id ) {

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$al_id = '';
			if ( ! empty( $_GET['al_id'] ) ) {
				$al_id = (int) $_GET['al_id'];
			}

			$class = 'authors-list-settings-field-main';

			if ( ! empty( $field['reload_on_change'] ) ) {
				$class .= ' authors-list-settings-field-reload-on-change';
			}
			?>

			<div class="authors-list-settings-field authors-list-settings-field-<?php echo esc_attr( $field['id'] ); ?>">
				<label for=""><?php echo esc_html( $field['title'] ); ?></label>

				<div class="<?php echo esc_attr( $class ); ?>">
					<?php
					$value = $this->get_item_field_value( $field, $option_id );
					switch ( $field['type'] ) {

						case 'shortcode':
							?>
							<input type="text" name="" value="[authors_list id=<?php echo $al_id;?>]" disabled>

							<?php
							if ( isset( $field['descr'] ) && $field['descr'] ) {
								?>
								<div class="authors-list-field-note"><?php echo wp_kses_post( $field['descr'] ); ?></div>
								<?php
							}
							break;

						case 'text':
							?>
							<input type="text" name="<?php echo esc_attr( $option_id ); ?>[<?php echo esc_attr( $field['id'] ); ?>]" value="<?php echo esc_html( htmlspecialchars( $value ) ); ?>" data-of-id="<?php echo esc_attr( $field['id'] ); ?>">

							<?php
							if ( isset( $field['descr'] ) && $field['descr'] ) {
								?>
								<div class="authors-list-field-note"><?php echo wp_kses_post( $field['descr'] ); ?></div>
								<?php
							}
							break;

						case 'textarea':
							$id = '';
							if ( ! empty( $field['field_id'] ) ) {
								$id = 'id="' . $field['field_id'] . '"';
							}
							?>
							<textarea <?php echo $id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> cols="40" rows="5" name="<?php echo esc_attr( $option_id ); ?>[<?php echo esc_attr( $field['id'] ); ?>]" data-of-id="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_attr( $value ); ?></textarea>
							<?php
							if ( isset( $field['descr'] ) && $field['descr'] ) {
								?>
								<div class="authors-list-field-note"><?php echo wp_kses_post( $field['descr'] ); ?></div>
								<?php
							}
							break;

						case 'select':
							?>
							<select name="<?php echo esc_attr( $option_id ); ?>[<?php echo esc_attr( $field['id'] ); ?>]" data-of-id="<?php echo esc_attr( $field['id'] ); ?>">
								<?php foreach ( (array) $field['choices'] as $c_value => $c_label ) : ?>
									<option value="<?php echo esc_attr( $c_value ); ?>" <?php selected( $c_value, $value ); ?>><?php echo esc_html( $c_label ); ?></option>
								<?php endforeach; ?>
							</select>
							<?php
							if ( isset( $field['descr'] ) && $field['descr'] ) {
								?>
								<div class="authors-list-field-note"><?php echo wp_kses_post( $field['descr'] ); ?></div>
								<?php
							}
							break;

						case 'styler':
							$authors_list_styler = Authors_List_Styler::instance();
							$authors_list_styler->display();
							?>
							<textarea id="authors-list-styler-panel-code" cols="40" rows="5" name="<?php echo esc_attr( $option_id ); ?>[<?php echo esc_attr( $field['id'] ); ?>]"><?php echo esc_textarea( $value ); ?></textarea>
							<?php
							break;

						case 'radio_image':
							?>
							<div class="authors-list-radio-image">
								<?php foreach ( (array) $field['choices'] as $key => $image ) : ?>
									<div class="authors-list-radio-image-item<?php echo ( ( $key === (int) $value ) ? ' authors-list-radio-image-selected' : '' ); ?>" data-of-id="<?php echo esc_attr( $field['id'] ); ?>">
										<label>
											<input type="radio" name="<?php echo esc_attr( $option_id ); ?>[<?php echo esc_attr( $field['id'] ); ?>]" value="<?php echo esc_attr( $key ); ?>" <?php checked( $key, $value ); ?> data-of-id="<?php echo esc_attr( $field['id'] ); ?>" />

											<img src="<?php echo esc_url( $image ); ?>" />
										</label>
									</div>
								<?php endforeach; ?>
							</div>
							<?php
							if ( isset( $field['descr'] ) && $field['descr'] ) {
								?>
								<div class="authors-list-field-note"><?php echo wp_kses_post( $field['descr'] ); ?></div>
								<?php
							}
							break;

						case 'checkbox':
							?>
							<input type="checkbox" name="<?php echo esc_attr( $option_id ); ?>[<?php echo esc_attr( $field['id'] ); ?>]" <?php checked( 'yes', $value ); ?> value='yes' data-of-id="<?php echo esc_attr( $field['id'] ); ?>">
							<?php
							if ( isset( $field['descr'] ) && $field['descr'] ) {
								?>
								<div class="authors-list-field-note"><?php echo wp_kses_post( $field['descr'] ); ?></div>
								<?php
							}
							break;

						case 'multicheck':
							?>
							<ul class="authors-list-multicheck">
								<?php foreach ( (array) $field['choices'] as $c_value => $c_label ) : ?>
									<?php
									$checked = '';
									foreach ( (array) $value as $search_column ) :
										if ( $c_value === $search_column ) {
											$checked = checked( $search_column, $c_value, false );
										}
									endforeach;
									?>

									<li>
										<input type="checkbox" name="<?php echo esc_attr( $option_id ); ?>[<?php echo esc_attr( $field['id'] ); ?>][]" id="authors-list-multicheck-<?php echo esc_attr( $c_value ); ?>" <?php echo $checked; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> value="<?php echo esc_attr( $c_value ); ?>">

										<label for="authors-list-multicheck-<?php echo esc_attr( $c_value ); ?>">
											<?php echo esc_html( $c_label ); ?>
										</label>
									</li>
								<?php endforeach; ?>
							</ul>
							<?php
							if ( isset( $field['descr'] ) && $field['descr'] ) {
								?>
								<div class="authors-list-field-note"><?php echo wp_kses_post( $field['descr'] ); ?></div>
								<?php
							}
							break;

						case 'search_filters':
							if ( isset( $field['descr'] ) && $field['descr'] ) {
								?>
								<div class="authors-list-field-note"><?php echo wp_kses_post( $field['descr'] ); ?></div>
								<?php
							}
							?>

							<?php if ( $value ) { ?>
								<div class="authors-list-search-filter-saved-options">
									<?php
									$search_filters_array = explode( '|', $value );
									foreach ( $search_filters_array as $search_filter ) {
										$single_search_filter_array = explode( ',', $search_filter );
										if ( empty( $single_search_filter_array[2] ) ) {
											$single_search_filter_array[2] = 'select';
										}
										?>
										<div class="authors-list-search-filters">
											<input type="text" class="authors-list-meta-key" placeholder="Meta Key" value="<?php echo esc_attr( $single_search_filter_array[0] ); ?>" />

											<input type="text" class="authors-list-label" placeholder="Label" value="<?php echo esc_attr( $single_search_filter_array[1] ); ?>" />

											<select class="authors-list-type">
												<option value="select" <?php selected( 'select', $single_search_filter_array[2] ); ?>><?php esc_html_e( 'Select', 'authors-list' ); ?></option>
												<option value="radio" <?php selected( 'radio', $single_search_filter_array[2] ); ?>><?php esc_html_e( 'Radio', 'authors-list' ); ?></option>
												<option value="checkboxes" <?php selected( 'checkboxes', $single_search_filter_array[2] ); ?>><?php esc_html_e( 'Checkboxes', 'authors-list' ); ?></option>
												<option value="number_range" <?php selected( 'number_range', $single_search_filter_array[2] ); ?>><?php esc_html_e( 'Number Range', 'authors-list' ); ?></option>
											</select>

											<input type="button" class="authors-list-filters-remove button button-secondary" value="<?php esc_attr_e( 'Remove', 'authors-list' ); ?>" />
										</div>
									<?php } ?>
								</div>
							<?php } ?>

							<div class="authors-list-search-filter-options"></div>

							<input type="hidden" class="authors-list-search-filters-values" name="<?php echo esc_attr( $option_id ); ?>[<?php echo esc_attr( $field['id'] ); ?>]" value="<?php echo esc_attr( $value ); ?>" />

							<input type="button" class="button button-primary authors-list-filters-add" value="<?php esc_html_e( 'Add New', 'authors-list' ); ?>" />
							<?php
							break;

						default:
							break;

					}
					?>
				</div>
			</div>

			<?php
		}

		/**
		 * Get item field value.
		 *
		 * @param string[] $field     The feild name.
		 * @param string   $option_id The option id.
		 */
		public function get_item_field_value( $field, $option_id ) {

			$current_values = get_option( $option_id, array() );
			$value          = '';

			if ( ! empty( $current_values[ $field['id'] ] ) ) {
				$value = $current_values[ $field['id'] ];
			} elseif ( ! empty( $field['default'] ) ) {
				$value = $field['default'];
			}

			return is_array( $value ) ? wp_unslash( $value ) : stripslashes( $value );

		}

	}

}

// Instantiate the class.
Authors_List_Settings::instance();
