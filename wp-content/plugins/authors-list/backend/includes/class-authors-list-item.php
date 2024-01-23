<?php
/**
 * Item display class.
 *
 * @package WPKube
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Authors_List_Item' ) ) {

	/**
	 * Item display class.
	 */
	class Authors_List_Item {

		/**
		 * The single instance of the class.
		 *
		 * @var Authors_List_Item
		 */
		protected static $instance = null;

		/**
		 * Main Authors_List_Item Instance.
		 *
		 * Ensures only one instance of Authors_List_Item is loaded or can be loaded.
		 *
		 * @return Authors_List_Item - Main instance.
		 */
		public static function instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();

				/**
				 * Action hook authors_list_item_loaded.
				 *
				 * Hooks after all instance of Authors_List_Item is loaded.
				 */
				do_action( 'authors_list_item_loaded' );
			}

			return self::$instance;

		}

		/**
		 * Authors_List_Item constructor.
		 */
		public function __construct() {

			// Filter shortcode atts for authors_list shortcode output.
			add_filter( 'authors_list_shortcode_atts', array( $this, 'shortcode_atts' ) );

			// Add the styler data.
			add_filter( 'authors_list_before_loop', array( $this, 'styler_data' ) );

			// Add the filter button in the front end.
			add_action( 'authors_list_before_loop', array( $this, 'ajax_filter_buttons_open' ) );

			// Add the filter button in the front end.
			add_action( 'authors_list_before_loop', array( $this, 'ajax_filter_buttons' ) );

			// Add the filter button in the front end.
			add_action( 'authors_list_after_loop', array( $this, 'ajax_filter_buttons_close' ) );

			// Add the fontawesome spinning icon in the front end.
			add_action( 'authors_list_after_main_loop_end', array( $this, 'authors_list_after_main_loop_start' ) );

			// Ajax event to update the authors list display in the front end.
			add_action( 'wp_ajax_update_authors_list_ajax', array( $this, 'update_authors_list_ajax' ) );
			add_action( 'wp_ajax_nopriv_update_authors_list_ajax', array( $this, 'update_authors_list_ajax' ) );

		}

		/**
		 * Get data for a specific item.
		 *
		 * @param int  $item_id Item id.
		 * @param bool $preview If the item is being previewed or not.
		 */
		public function get_item_data( $item_id, $preview = false ) {

			// Get all items.
			$items = get_option( 'authors_list_items', array() );

			// Check if needed item exists and is active.
			if ( ! empty( $items[ 'authors-list-' . $item_id ] ) && 'active' === $items[ 'authors-list-' . $item_id ]['status'] ) {

				// Get item settings.
				$item_settings = $this->get_item_settings( $item_id );

				// Add settings to the data we return.
				$items[ 'authors-list-' . $item_id ]['settings'] = $item_settings;

				if ( $preview && get_option( 'authors_list_item_draft_' . $item_id ) ) {
					$items[ 'authors-list-' . $item_id ]['settings'] = get_option( 'authors_list_item_draft_' . $item_id );
				}

				// Strip slashes.
				foreach ( $items[ 'authors-list-' . $item_id ]['settings'] as $setting_id => $setting_value ) {
					$items[ 'authors-list-' . $item_id ]['settings'][ $setting_id ] = is_array( $setting_value ) ? wp_unslash( $setting_value ) : stripslashes( $setting_value );
				}

				// Return the data.
				return $items[ 'authors-list-' . $item_id ];

			}

			// Nothing so far, return false.
			return false;

		}

		/**
		 * Get item settings.
		 *
		 * @param int $item_id item id.
		 */
		public function get_item_settings( $item_id ) {

			$item_settings = get_option( 'authors_list_item_settings_' . $item_id );

			if ( ! $item_settings ) {
				$item_settings = array();
			}

			$fields = Authors_List_Settings::get_fields(); // phpcs:ignore Squiz.Classes.SelfMemberReference.NotUsed
			foreach ( $fields as $section ) {
				foreach ( $section as $field ) {
					if ( ! isset( $item_settings[ $field['id'] ] ) ) {
						$default_value = '';
						if ( ! empty( $field['default'] ) ) {
							$default_value = $field['default'];
						}
						$item_settings[ $field['id'] ] = $default_value;
					}
				}
			}

			return $item_settings;

		}

		/**
		 * Filter the shortcode atts for displaying the authors list.
		 *
		 * @param array $atts Array attributes value of shortcodes.
		 *
		 * @return array The filtered value of the shortcode attributes.
		 */
		public function shortcode_atts( $atts ) {

			// No ID, return the default attributes.
			if ( empty( $atts['id'] ) ) {
				return $atts;
			}

			// Get the item id from shortcode attributes.
			$item_id = $atts['id'];

			$preview = false;
			if ( ! empty( $atts['preview'] ) && true == $atts['preview'] ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				$preview = true;
			}

			$load_preview = false;
			if ( ! empty( $atts['load_preview'] ) && true == $atts['load_preview'] ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				$load_preview = true;
			}

			// Get item data.
			$item = $this->get_item_data( $item_id, $preview, $load_preview );

			// item not found, return.
			if ( ! $item ) {
				return;
			}

			// Item settings.
			$settings = $item['settings'];

			return self::shortcode_atts_data( $settings, $item_id, $preview, $load_preview );

		}

		/**
		 * Return the filtered shortcode attributes.
		 *
		 * @param string[] $settings     Shortcode attributes data.
		 * @param int      $item_id      Shortcode/Item id.
		 * @param bool     $preview      If the item is being previewed or not.
		 * @param bool     $load_preview If the item is being previewed or not on the options page load.
		 */
		public static function shortcode_atts_data( $settings, $item_id, $preview = false, $load_preview = false ) {

			// For id.
			$atts['id'] = $item_id;

			// For search.
			$atts['search_enable'] = isset( $settings['search_enable'] ) ? $settings['search_enable'] : 'no';

			// For search_cols.
			$atts['search_cols'] = isset( $settings['search_cols'] ) ? $settings['search_cols'] : array();

			// For filters.
			$atts['filters_enable'] = isset( $settings['filters_enable'] ) ? $settings['filters_enable'] : 'no';

			// For style.
			$atts['style'] = isset( $settings['style'] ) ? $settings['style'] : '1';

			// For columns.
			$atts['columns'] = isset( $settings['columns'] ) ? $settings['columns'] : '4';

			// For columns_direction.
			$atts['columns_direction'] = isset( $settings['columns_direction'] ) ? $settings['columns_direction'] : 'horizontal';

			// For avatar_size.
			$atts['avatar_size'] = isset( $settings['avatar_size'] ) ? $settings['avatar_size'] : '500';

			// For avatar_meta_key.
			$atts['avatar_meta_key'] = isset( $settings['avatar_meta_key'] ) ? $settings['avatar_meta_key'] : '';

			// For bio_word_trim.
			$atts['bio_word_trim'] = isset( $settings['bio_word_trim'] ) ? $settings['bio_word_trim'] : false;

			// For show_title.
			$atts['show_title'] = isset( $settings['show_title'] ) ? $settings['show_title'] : 'yes';

			// For show_count.
			$atts['show_count'] = isset( $settings['show_count'] ) ? $settings['show_count'] : 'yes';

			// For show_bio.
			$atts['show_bio'] = isset( $settings['show_bio'] ) ? $settings['show_bio'] : 'yes';

			// For show_link.
			$atts['show_link'] = isset( $settings['show_link'] ) ? $settings['show_link'] : 'yes';

			// For count_text.
			$atts['count_text'] = isset( $settings['count_text'] ) ? $settings['count_text'] : 'posts';

			// For before_avatar.
			$atts['before_avatar'] = isset( $settings['before_avatar'] ) ? $settings['before_avatar'] : '';

			// For before_title.
			$atts['before_title'] = isset( $settings['before_title'] ) ? $settings['before_title'] : '';

			// For before_count.
			$atts['before_count'] = isset( $settings['before_count'] ) ? $settings['before_count'] : '';

			// For before_bio.
			$atts['before_bio'] = isset( $settings['before_bio'] ) ? $settings['before_bio'] : '';

			// For before_link.
			$atts['before_link'] = isset( $settings['before_link'] ) ? $settings['before_link'] : '';

			// For after_avatar.
			$atts['after_avatar'] = isset( $settings['after_avatar'] ) ? $settings['after_avatar'] : '';

			// For after_title.
			$atts['after_title'] = isset( $settings['after_title'] ) ? $settings['after_title'] : '';

			// For after_count.
			$atts['after_count'] = isset( $settings['after_count'] ) ? $settings['after_count'] : '';

			// For after_bio.
			$atts['after_bio'] = isset( $settings['after_bio'] ) ? $settings['after_bio'] : '';

			// For after_link.
			$atts['after_link'] = isset( $settings['after_link'] ) ? $settings['after_link'] : '';

			// For styler enable.
			$atts['styler_enable'] = isset( $settings['styler_enable'] ) ? $settings['styler_enable'] : 'no';

			// For styler.
			$atts['styler'] = isset( $settings['styler'] ) ? $settings['styler'] : '';

			// For styler_js_data.
			$atts['styler_js_data'] = isset( $settings['styler_js_data'] ) ? $settings['styler_js_data'] : '';

			// For custom_css.
			$atts['custom_css'] = isset( $settings['custom_css'] ) ? $settings['custom_css'] : '';

			// For amount.
			$atts['amount'] = isset( $settings['amount'] ) ? $settings['amount'] : false;

			// pagination
			$atts['pagination'] = isset( $settings['pagination'] ) ? $settings['pagination'] : 'no';

			// For minimum_posts_count.
			$atts['minimum_posts_count'] = isset( $settings['minimum_posts_count'] ) ? $settings['minimum_posts_count'] : 0;

			// For only_authors.
			$atts['only_authors'] = isset( $settings['only_authors'] ) ? $settings['only_authors'] : 'yes';

			// For skip_empty.
			$atts['skip_empty'] = isset( $settings['skip_empty'] ) ? $settings['skip_empty'] : 'yes';

			// For orderby.
			$atts['orderby'] = isset( $settings['orderby'] ) ? $settings['orderby'] : 'post_count';

			// For order.
			$atts['order'] = isset( $settings['order'] ) ? $settings['order'] : 'DESC';

			// For exclude.
			$atts['exclude'] = isset( $settings['exclude'] ) ? $settings['exclude'] : '';

			// For include.
			$atts['include'] = isset( $settings['include'] ) ? $settings['include'] : '';

			// For roles.
			$atts['roles'] = isset( $settings['roles'] ) ? $settings['roles'] : '';

			// For post_types.
			$atts['post_types'] = isset( $settings['post_types'] ) ? $settings['post_types'] : '';

			// For latest_post_after.
			$atts['latest_post_after'] = isset( $settings['latest_post_after'] ) ? $settings['latest_post_after'] : '';

			// For name_starts_with.
			$atts['name_starts_with'] = isset( $settings['name_starts_with'] ) ? $settings['name_starts_with'] : '';

			// For first_name_starts_with.
			$atts['first_name_starts_with'] = isset( $settings['first_name_starts_with'] ) ? $settings['first_name_starts_with'] : '';

			// For last_name_starts_with.
			$atts['last_name_starts_with'] = isset( $settings['last_name_starts_with'] ) ? $settings['last_name_starts_with'] : '';

			// For categories.
			$atts['categories'] = isset( $settings['categories'] ) ? $settings['categories'] : '';

			// For terms.
			$atts['terms'] = isset( $settings['terms'] ) ? $settings['terms'] : '';

			// For taxonomy.
			$atts['taxonomy'] = isset( $settings['taxonomy'] ) ? $settings['taxonomy'] : '';

			// For title_element.
			$atts['title_element'] = isset( $settings['title_element'] ) ? $settings['title_element'] : 'div';

			// For link_to.
			$atts['link_to'] = isset( $settings['link_to'] ) ? $settings['link_to'] : 'archive';

			// For link_to_meta_key.
			$atts['link_to_meta_key'] = isset( $settings['link_to_meta_key'] ) ? $settings['link_to_meta_key'] : '';

			// For bp_member_types.
			$atts['bp_member_types'] = isset( $settings['bp_member_types'] ) ? $settings['bp_member_types'] : '';

			// Modify the shortcode atts for the preview only.
			if ( $preview || $load_preview ) {
				// For show_title.
				$atts['show_title'] = isset( $settings['show_title'] ) ? $settings['show_title'] : 'no';

				// For show_count.
				$atts['show_count'] = isset( $settings['show_count'] ) ? $settings['show_count'] : 'no';

				// For show_bio.
				$atts['show_bio'] = isset( $settings['show_bio'] ) ? $settings['show_bio'] : 'no';

				// For show_link.
				$atts['show_link'] = isset( $settings['show_link'] ) ? $settings['show_link'] : 'no';

				// For amount.
				$atts['amount'] = 1;

				// For columns.
				$atts['columns'] = 1;

				// For search.
				$atts['search_enable'] = 'no';

				// For filters.
				$atts['filters_enable'] = 'no';
			}

			return $atts;

		}

		/**
		 * Add the styler data.
		 *
		 * @param array $atts The shortcode attributes.
		 */
		public function styler_data( $atts ) {

			// No ID, return the default attributes.
			if ( empty( $atts['id'] ) ) {
				return $atts;
			}

			// Get the item id from shortcode attributes.
			$item_id = $atts['id'];

			$preview = false;
			if ( ! empty( $atts['preview'] ) && true == $atts['preview'] ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				$preview = true;
			}

			// Get item data.
			$item = $this->get_item_data( $item_id, $preview );

			// item not found, return.
			if ( ! $item ) {
				return;
			}

			// Item settings.
			$settings = $item['settings'];

			// Styler data style tag.
			$ajax_request = isset( $atts['ajax_request'] ) ? $atts['ajax_request'] : 'no';

			// Display only for non ajax request.
			if ( 'no' === $ajax_request ) {
				if ( 'yes' === $atts['styler_enable'] ) {
					?>
					<style>
						<?php echo ( ( ! empty( $settings['styler'] ) ) ? esc_attr( $settings['styler'] ) : '' ); ?>
					</style>

					<?php if ( ! empty( $settings['custom_css'] ) ) { ?>
						<style>
							<?php echo esc_attr( $settings['custom_css'] ); ?>
						</style>
					<?php } ?>
					<?php
				}
			}

		}

		/**
		 * Display the Ajax filter buttons.
		 *
		 * @param array $atts The shortcode attributes.
		 */
		public function ajax_filter_buttons_open( $atts ) {

			// Return if search as well as filter functionality is disabled.
			$search_enable  = isset( $atts['search_enable'] ) ? $atts['search_enable'] : false;
			$filters_enable = isset( $atts['filters_enable'] ) ? $atts['filters_enable'] : false;
			if ( ( ! $search_enable || 'no' === $search_enable ) && ( ! $filters_enable || 'no' === $filters_enable ) ) {
				return;
			}

			$preview = false;
			if ( ! empty( $atts['preview'] ) && true == $atts['preview'] ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				$preview = true;
			}

			// Get item data.
			$item = $this->get_item_data( $atts['id'], $preview );

			// item not found, return.
			if ( ! $item ) {
				return;
			}

			// item settings.
			$settings = $item['settings'];

			// Search options.
			$ajax_request = isset( $atts['ajax_request'] ) ? $atts['ajax_request'] : 'no';

			// Display the required divs.
			if ( 'no' === $ajax_request ) {
				// Display the opening div.
				if ( 'left' === $settings['filters_position'] || 'right' === $settings['filters_position'] ) {
					echo '<div class="authors-list-filters-button-align-' . esc_attr( $settings['filters_position'] ) . '">';
				}
			}

		}

		/**
		 * Display the Ajax filter buttons.
		 *
		 * @param array $atts The shortcode attributes.
		 */
		public function ajax_filter_buttons( $atts ) {

			// Return if search as well as filter functionality is disabled.
			$search_enable  = isset( $atts['search_enable'] ) ? $atts['search_enable'] : false;
			$filters_enable = isset( $atts['filters_enable'] ) ? $atts['filters_enable'] : false;
			if ( ( ! $search_enable || 'no' === $search_enable ) && ( ! $filters_enable || 'no' === $filters_enable ) ) {
				return;
			}

			$preview = false;
			if ( ! empty( $atts['preview'] ) && true == $atts['preview'] ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				$preview = true;
			}

			// Get item data.
			$item = $this->get_item_data( $atts['id'], $preview );

			// item not found, return.
			if ( ! $item ) {
				return;
			}

			// item settings.
			$settings = $item['settings'];

			// Search columns.
			$shortcode_id  = isset( $atts['id'] ) ? $atts['id'] : '';
			$ajax_request  = isset( $atts['ajax_request'] ) ? $atts['ajax_request'] : 'no';
			$search_cols   = isset( $atts['search_cols'] ) ? $atts['search_cols'] : array();
			$button_text   = ( isset( $settings['button_text'] ) && ! empty( $settings['button_text'] ) ) ? $settings['button_text'] : esc_html__( 'Search', 'authors-list' );
			$author_lists  = isset( $settings['filters'] ) ? $settings['filters'] : array();
			$user_meta_key = array();
			if ( $author_lists ) {
				$search_filters_array = explode( '|', $author_lists );
				foreach ( $search_filters_array as $search_filter ) {
					$single_search_filter_array = explode( ',', $search_filter );
					$user_meta_key[]            = $single_search_filter_array[0];
				}
			}
			?>

			<?php if ( 'no' === $ajax_request ) { ?>
				<div class="authors-list-ajax-filter">
					<form action="" type="post">
						<?php if ( $shortcode_id ) { ?>
							<input type="hidden" name="authors_list_search_shortcode" class="authors-list-search-shortcode" value="<?php echo esc_attr( "[authors_list id={$shortcode_id}]" ); ?>" />
						<?php } ?>

						<?php if ( 'yes' === $search_enable ) { ?>
							<input type="search" name="authors_list_search" class="authors-list-search-input" placeholder="<?php esc_attr_e( 'Search term', 'authors-list' ); ?>" />

							<?php if ( ! empty( $search_cols ) ) { ?>
								<input type="hidden" name="authors_list_search_author_columns[]" class="authors-list-search-column" value="<?php echo esc_attr( implode( ',', $search_cols ) ); ?>" />
							<?php } ?>
						<?php } ?>

						<?php
						if ( 'yes' === $filters_enable ) {
							$all_users_metadatas = self::get_users_meta_keys( $settings );

							if ( $author_lists ) {
								$search_filters_array = explode( '|', $author_lists );
								foreach ( $search_filters_array as $search_filter ) {
									$single_search_filter_array = explode( ',', $search_filter );
									$temp_value                 = array();

									foreach ( array_unique( $user_meta_key ) as $chosen_author_meta ) {

										// For select option.
										if ( ( 'select' === $single_search_filter_array[2] ) && ( $chosen_author_meta === $single_search_filter_array[0] ) ) {
											?>
											<div class="authors-list-select">
												<div class="authors-list-label"><?php echo esc_html( $single_search_filter_array[1] ); ?></div>

												<select name="authors_list_search_<?php echo esc_attr( $chosen_author_meta ); ?>_metadata" class="authors-list-search-<?php echo esc_attr( $chosen_author_meta ); ?>-metadata">
													<option value="All"><?php esc_html_e( 'All', 'authors-list' ); ?></option>

													<?php
													foreach ( $all_users_metadatas as $all_users_metadata ) {
														foreach ( $all_users_metadata as $user_metadata ) {
															if ( ( $chosen_author_meta === $user_metadata['meta_key'] ) && ( ! in_array( $user_metadata['meta_value'], $temp_value, true ) ) && ! empty( $user_metadata['meta_value'] ) ) {
																?>

																<option value="<?php echo esc_attr( $user_metadata['meta_value'] ); ?>"><?php echo esc_html( $user_metadata['meta_value'] ); ?></option>

																<?php $temp_value[] = $user_metadata['meta_value']; ?>

																<?php
															}
														}
													}
													?>

												</select>
											</div>
											<?php
										}

										// For radio option.
										if ( ( 'radio' === $single_search_filter_array[2] ) && ( $chosen_author_meta === $single_search_filter_array[0] ) ) {
											?>
											<div class="authors-list-radio">
												<div class="authors-list-label"><?php echo esc_html( $single_search_filter_array[1] ); ?></div>

												<div class="authors-list-radio-selects">
													<?php
													foreach ( $all_users_metadatas as $all_users_metadata ) {
														foreach ( $all_users_metadata as $user_metadata ) {
															if ( ( $chosen_author_meta === $user_metadata['meta_key'] ) && ( ! in_array( $user_metadata['meta_value'], $temp_value, true ) ) && ! empty( $user_metadata['meta_value'] ) ) {
																?>

																<div class="authors-list-single-radio">
																	<label>
																		<input type="radio" name="authors_list_search_<?php echo esc_attr( $chosen_author_meta ); ?>_metadata" value="<?php echo esc_attr( $user_metadata['meta_value'] ); ?>" id="<?php echo esc_attr( $user_metadata['meta_value'] ); ?>"  class="authors-list-search-<?php echo esc_attr( $chosen_author_meta ); ?>-metadata" />

																		<?php echo esc_html( $user_metadata['meta_value'] ); ?>
																	</label>
																</div>

																<?php $temp_value[] = $user_metadata['meta_value']; ?>

																<?php
															}
														}
													}
													?>
												</div>
											</div>
											<?php
										}

										// For checkboxes option.
										if ( ( 'checkboxes' === $single_search_filter_array[2] ) && ( $chosen_author_meta === $single_search_filter_array[0] ) ) {
											?>
											<div class="authors-list-checkboxes">
												<div class="authors-list-label"><?php echo esc_html( $single_search_filter_array[1] ); ?></div>

												<ul class="authors-list-multiselect">

													<?php
													foreach ( $all_users_metadatas as $all_users_metadata ) {
														foreach ( $all_users_metadata as $user_metadata ) {
															if ( ( $chosen_author_meta === $user_metadata['meta_key'] ) && ( ! in_array( $user_metadata['meta_value'], $temp_value, true ) )  && ! empty( $user_metadata['meta_value'] ) ) {

																?>

																<li>
																	<label>
																		<input type="checkbox" name="authors_list_search_<?php echo esc_attr( $chosen_author_meta ); ?>_metadata[]" value="<?php echo esc_attr( $user_metadata['meta_value'] ); ?>" id="<?php echo esc_attr( $user_metadata['meta_value'] ); ?>"  class="authors-list-search-<?php echo esc_attr( $chosen_author_meta ); ?>-metadata" />

																		<?php echo esc_html( $user_metadata['meta_value'] ); ?>
																	</label>
																</li>

																<?php $temp_value[] = $user_metadata['meta_value']; ?>

																<?php
															}
														}
													}
													?>

												</ul>
											</div>
											<?php
										}

										// For number range.
										if ( ( 'number_range' === $single_search_filter_array[2] ) && ( $chosen_author_meta === $single_search_filter_array[0] ) ) {
											?>
											<div class="authors-list-number-range">
												<div class="authors-list-label"><?php echo esc_html( $single_search_filter_array[1] ); ?></div>

												<?php
												$name          = '';
												$class         = '';
												$numbers       = array();
												$min_max_value = array();
												foreach ( $all_users_metadatas as $all_users_metadata ) {
													foreach ( $all_users_metadata as $user_metadata ) {
														if ( ( $chosen_author_meta === $user_metadata['meta_key'] ) && ( ! in_array( $user_metadata['meta_value'], $temp_value, true ) ) && ! empty( $user_metadata['meta_value'] ) ) {
															$name      = 'authors_list_search_' . esc_attr( $chosen_author_meta ) . '_metadata[]';
															$class     = 'authors-list-search-' . esc_attr( $chosen_author_meta ) . '-metadata';
															$numbers[] = absint( $user_metadata['meta_value'] );
															?>

															<?php $temp_value[] = $user_metadata['meta_value']; ?>

															<?php
														}
													}
												}
												$min_max_value['min'] = min( $numbers );
												$min_max_value['max'] = max( $numbers );
												?>
												<div class="authors-list-ranges">
													<?php esc_html_e( 'Range:', 'authors-list' ); ?>

													<span class="authors-list-min-range">
														<?php echo esc_attr( $min_max_value['min'] ); ?>
													</span> - <span class="authors-list-max-range">
														<?php echo esc_attr( $min_max_value['max'] ); ?>
													</span>
												</div>

												<input type="hidden" name="<?php echo esc_attr( $name ); ?>" class="<?php echo esc_attr( $class ); ?>" value="<?php echo esc_attr( $min_max_value['min'] ); ?>,<?php echo esc_attr( $min_max_value['max'] ); ?>">

												<div class="authors-list-number-range-holder" data-min="<?php echo esc_attr( $min_max_value['min'] ); ?>" data-max="<?php echo esc_attr( $min_max_value['max'] ); ?>"></div>
											</div>
											<?php
										}

									}
								}
							}
						}
						?>

						<button type="submit" class="authors-list-search-filter-submit">
							<?php echo esc_html( $button_text ); ?>
						</button>
					</form>
				</div>
			<?php } ?>

			<?php

		}

		/**
		 * Display the Ajax filter buttons.
		 *
		 * @param array $atts The shortcode attributes.
		 */
		public function ajax_filter_buttons_close( $atts ) {

			// Return if search as well as filter functionality is disabled.
			$search_enable  = isset( $atts['search_enable'] ) ? $atts['search_enable'] : false;
			$filters_enable = isset( $atts['filters_enable'] ) ? $atts['filters_enable'] : false;
			if ( ( ! $search_enable || 'no' === $search_enable ) && ( ! $filters_enable || 'no' === $filters_enable ) ) {
				return;
			}

			$preview = false;
			if ( ! empty( $atts['preview'] ) && true == $atts['preview'] ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				$preview = true;
			}

			// Get item data.
			$item = $this->get_item_data( $atts['id'], $preview );

			// item not found, return.
			if ( ! $item ) {
				return;
			}

			// item settings.
			$settings = $item['settings'];

			// Search options.
			$ajax_request = isset( $atts['ajax_request'] ) ? $atts['ajax_request'] : 'no';

			// Display the required divs.
			if ( 'no' === $ajax_request ) {
				// Display the closing div.
				if ( 'left' === $settings['filters_position'] || 'right' === $settings['filters_position'] ) {
					echo '</div>';
				}
			}

		}

		/**
		 * Display the Ajax spinning icon.
		 *
		 * @param array $atts The shortcode attributes.
		 */
		public function authors_list_after_main_loop_start( $atts ) {
			?>

			<span class="spinner">
				<span class="spin">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
						<path d="M304 48C304 74.51 282.5 96 256 96C229.5 96 208 74.51 208 48C208 21.49 229.5 0 256 0C282.5 0 304 21.49 304 48zM304 464C304 490.5 282.5 512 256 512C229.5 512 208 490.5 208 464C208 437.5 229.5 416 256 416C282.5 416 304 437.5 304 464zM0 256C0 229.5 21.49 208 48 208C74.51 208 96 229.5 96 256C96 282.5 74.51 304 48 304C21.49 304 0 282.5 0 256zM512 256C512 282.5 490.5 304 464 304C437.5 304 416 282.5 416 256C416 229.5 437.5 208 464 208C490.5 208 512 229.5 512 256zM74.98 437C56.23 418.3 56.23 387.9 74.98 369.1C93.73 350.4 124.1 350.4 142.9 369.1C161.6 387.9 161.6 418.3 142.9 437C124.1 455.8 93.73 455.8 74.98 437V437zM142.9 142.9C124.1 161.6 93.73 161.6 74.98 142.9C56.24 124.1 56.24 93.73 74.98 74.98C93.73 56.23 124.1 56.23 142.9 74.98C161.6 93.73 161.6 124.1 142.9 142.9zM369.1 369.1C387.9 350.4 418.3 350.4 437 369.1C455.8 387.9 455.8 418.3 437 437C418.3 455.8 387.9 455.8 369.1 437C350.4 418.3 350.4 387.9 369.1 369.1V369.1z"/>
					</svg>
				</span>
			</span>

			<?php
		}

		/**
		 * Ajax handler to update the authors list from the Ajax filter button.
		 */
		public function update_authors_list_ajax() {

			// Return if the nonce is not verified.
			$request_nonce = isset( $_REQUEST['authorsListNonce'] ) ? wp_unslash( $_REQUEST['authorsListNonce'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ! wp_verify_nonce( $request_nonce, 'authors-list-search' ) ) {
				return;
			}

			// Remove the filter of authors_list_shortcode_atts for Ajax event.
			remove_filter( 'authors_list_shortcode_atts', array( $this, 'shortcode_atts' ) );

			// Get all of the available $_POST datas.
			$shortcode     = isset( $_POST['shortcode'] ) ? wp_unslash( $_POST['shortcode'] ) : '';
			$shortcode_id  = str_replace( array( '[authors_list id=', ']' ), '', $shortcode );
			$search_param  = isset( $_POST['searchParam'] ) ? wp_unslash( $_POST['searchParam'] ) : '';
			$search_column = isset( $_POST['searchColumn'] ) ? wp_unslash( $_POST['searchColumn'] ) : '';

			// Add the filter of authors_list_shortcode_atts for Ajax event after getting new required parameters.
			add_filter( 'authors_list_shortcode_atts', array( $this, 'shortcode_atts_ajax' ) );

			// Add the filter of authors_list_custom_args for custom arguments in get_users query.
			add_filter( 'authors_list_custom_args', array( $this, 'custom_args' ), 10, 2 );

			$preview = false;
			if ( ! empty( $atts['preview'] ) && true == $atts['preview'] ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				$preview = true;
			}

			// Get item data.
			$item = $this->get_item_data( $shortcode_id, $preview );

			// item not found, return.
			if ( ! $item ) {
				return;
			}

			// item settings.
			$settings = $item['settings'];

			// For search option.
			$search_atts = '';
			if ( $search_param ) {
				$search_atts = "search='{$search_param}'";
			}

			// For search column option.
			$search_column_atts = '';
			if ( $search_column ) {
				$search_column_atts = "search_columns='{$search_column}'";
			}

			// For filter options.
			$filter_atts = '';
			if ( $settings['filters'] ) {
				$filter_atts = "filters='{$settings['filters']}'";
			}

			$output = do_shortcode( "[authors_list id={$shortcode_id} $search_atts $search_column_atts $filter_atts ajax_request='yes']" );

			wp_send_json_success( $output );

		}

		/**
		 * Filter the shortcode atts for displaying the authors list via Ajax event.
		 *
		 * @param array $atts Array attributes value of shortcodes.
		 *
		 * @return array The filtered value of the shortcode attributes.
		 */
		public function shortcode_atts_ajax( $atts ) {

			$preview = false;
			if ( ! empty( $atts['preview'] ) && true == $atts['preview'] ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				$preview = true;
			}

			// Get item data.
			$item = $this->get_item_data( $atts['id'], $preview );

			// Item not found, return.
			if ( ! $item ) {
				return;
			}

			// Item settings.
			$settings = $item['settings'];

			$item_settings_atts = self::shortcode_atts_data( $settings, $atts['id'] );

			// For search term.
			$atts['search'] = isset( $atts['search'] ) ? $atts['search'] : '';

			// For search columns.
			$atts['search_columns'] = isset( $atts['search_columns'] ) ? $atts['search_columns'] : '';

			// For filters.
			$atts['filters'] = isset( $atts['filters'] ) ? $atts['filters'] : '';

			// For ajax request.
			$atts['ajax_request'] = isset( $atts['ajax_request'] ) ? $atts['ajax_request'] : 'no';

			return array_merge( $item_settings_atts, $atts );

		}

		/**
		 * Filter the arguments of get_users query for displaying the authors list via Ajax event.
		 *
		 * @param string[] $args Arguments of get_users.
		 * @param string[] $atts Shortcode attributes.
		 *
		 * @return array Filtered arguments of get_users.
		 */
		public function custom_args( $args, $atts ) {

			// Return if the nonce is not verified.
			$request_nonce = isset( $_REQUEST['authorsListNonce'] ) ? wp_unslash( $_REQUEST['authorsListNonce'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ! wp_verify_nonce( $request_nonce, 'authors-list-search' ) ) {
				return;
			}

			// Get all of the available $_POST datas.
			$search_param      = isset( $_POST['searchParam'] ) ? wp_unslash( $_POST['searchParam'] ) : '';
			$search_column     = isset( $_POST['searchColumn'] ) ? wp_unslash( $_POST['searchColumn'] ) : '';
			$ajax_filters_data = isset( $_POST['ajaxFiltersData'] ) ? wp_unslash( $_POST['ajaxFiltersData'] ) : array();

			// For search.
			if ( ( isset( $atts['search'] ) && $atts['search'] ) && $search_param ) {
				$args['search'] = '*' . $search_param . '*';
			}

			// For search column.
			if ( ( isset( $atts['search_columns'] ) && $atts['search_columns'] ) && $search_column ) {
				$args['search_columns'] = explode( ',', $search_column );
			}

			// Set metaquery args.
			if ( isset( $atts['filters'] ) && $atts['filters'] ) {

				$args['meta_query'] = array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				if ( count( $ajax_filters_data ) > 1 ) {
					$args['meta_query']['relation'] = 'AND';
				}

				foreach ( $ajax_filters_data as $meta_key => $meta_value ) {

					// No need to proceed if the meta value from Ajax request is set to `All`.
					if ( ! is_array( $meta_value ) && 'All' === $meta_value ) {
						continue;
					}

					$compare = 'IN';
					// Check for the number ranges.
					if ( strpos( $meta_value, ',' ) ) {
						$number_ranges = explode( ',', $meta_value );
						$meta_value    = array(
							$number_ranges[0],
							$number_ranges[1],
						);
						$compare       = 'BETWEEN';
					}

					$args['meta_query'][] = array(
						'key'     => $meta_key,
						'value'   => $meta_value,
						'compare' => $compare,
					);

				}

			}

			return $args;

		}

		/**
		 * Get users meta keys.
		 *
		 * @param string[] $settings The item settings.
		 */
		public static function get_users_meta_keys( $settings ) {

			$author_lists  = isset( $settings['filters'] ) ? $settings['filters'] : array();
			$user_meta_key = array();
			if ( $author_lists ) {
				$search_filters_array = explode( '|', $author_lists );
				foreach ( $search_filters_array as $search_filter ) {
					$single_search_filter_array = explode( ',', $search_filter );
					$user_meta_key[]            = $single_search_filter_array[0];
				}
			}
			$author_metadata = array();
			$all_users       = get_users();

			// Return the authors meta_key and meta_values.
			foreach ( $all_users as $user ) {

				$user_id        = $user->ID;
				$all_user_metas = get_user_meta( $user_id );

				foreach ( $all_user_metas as $meta_key => $meta_value ) {

					foreach ( $user_meta_key as $chosen_author_meta ) {
						if ( $meta_key === $chosen_author_meta ) {
							$author_metadata[ $user_id ][] = array(
								'meta_key'   => $chosen_author_meta,
								'meta_value' => get_user_meta( $user_id, $chosen_author_meta, true ),
							);
						}
					}

				}

			}

			return $author_metadata;

		}

	}

}

// Instantiate the class.
Authors_List_Item::instance();
