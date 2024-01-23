<?php
/**
 * Authors List Shortcode class.
 *
 * @package WPKube
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Authors_List_Shortcode class.
 */
class Authors_List_Shortcode {

	/**
	 * The single instance of the class.
	 *
	 * @var Authors_List_Shortcode
	 */
	protected static $instance = null;

	/**
	 * Main Authors_List_Shortcode Instance.
	 *
	 * Ensures only one instance of Authors_List_Shortcode is loaded or can be loaded.
	 *
	 * @return Authors_List_Shortcode - Main instance.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();

			/**
			 * Action hook authors_list_shortcode_loaded.
			 *
			 * Hooks after all instance of Authors_List_Shortcode is loaded.
			 */
			do_action( 'authors_list_shortcode_loaded' );
		}

		return self::$instance;

	}

	/**
	 * Authors_List_Shortcode constructor.
	 */
	public function __construct() {

		// Register shortcode.
		add_shortcode( 'authors_list', array( $this, 'shortcode' ) );

	}

	/**
	 * Shortcode to display the authors list.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Shortcode content.
	 */
	public function shortcode( $atts = false, $content = false ) {

		// if specific list ID supplied
		$get_item_data = false;
		if ( isset( $atts['id'] ) ) {

			// get list by ID
			$authors_list_item = Authors_List_Item::instance();
			$item_id           = isset( $atts['id'] ) ? $atts['id'] : '';
			$get_item_data     = $authors_list_item->get_item_data( $item_id );

			// if list found, apply filter (to set shortcode atts based on the user defined settings)
			if ( $get_item_data ) {
				$atts = apply_filters( 'authors_list_shortcode_atts', $atts );
			}

		}

		// Get the shortcode settings datas.
		$settings = $this->settings( $atts, $content );

		// Get the authors ids.
		$args = $this->args( $settings, $atts );

		// Filter shortcode attributes
		if ( $get_item_data ) {
			$args = apply_filters( 'authors_list_custom_args', $args, $atts );
		}

		// Get authors order by post count.
		$authors_ids = get_users( $args );

		// Start output buffer.
		ob_start();

		$this->shortcode_content( $authors_ids, $settings, $atts );

		// Get the output buffer content.
		$output = ob_get_contents();

		// Clean the output buffer.
		ob_end_clean();

		return $output;

	}

	/**
	 * Set the shortcode attribute values.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Shortcode content.
	 *
	 * @return array Shortcode attribute settings.
	 */
	public function settings( $atts, $content ) {

		// If no atts supplied make it an empty array.
		if ( ! $atts ) {
			$atts = array();
		}

		// Default values.
		$defaults = array(
			'style'                  => '1',
			'columns'                => '4',
			'columns_direction'      => 'horizontal',
			'avatar_size'            => 500,
			'avatar_meta_key'        => '',
			'amount'                 => false,
			'show_avatar'            => 'yes',
			'show_title'             => 'yes',
			'show_count'             => 'yes',
			'show_bio'               => 'yes',
			'show_link'              => 'yes',
			'orderby'                => 'post_count',
			'order'                  => 'DESC',
			'skip_empty'             => 'yes',
			'minimum_posts_count'    => 0,
			'bio_word_trim'          => false,
			'only_authors'           => 'yes',
			'exclude'                => '',
			'include'                => '',
			'roles'                  => '',
			'latest_post_after'      => '',
			'post_types'             => '',
			'name_starts_with'       => '',
			'first_name_starts_with' => '',
			'last_name_starts_with'  => '',
			'link_to'                => 'archive',
			'link_to_meta_key'       => '',
			'pagination'             => 'no',
			'count_text'             => 'posts',

			'categories'             => '',
			'taxonomy'               => '',
			'terms'                  => '',

			'before_avatar'          => '',
			'before_title'           => '',
			'before_count'           => '',
			'before_bio'             => '',
			'before_link'            => '',

			'after_avatar'           => '',
			'after_title'            => '',
			'after_count'            => '',
			'after_bio'              => '',
			'after_link'             => '',

			'title_element'          => 'div',

			'bp_member_types'        => '',
		);

		// Merge settings/shortcode attributes.
		return array_merge( $defaults, $atts );

	}

	/**
	 * Generate the arguments/query for the authors lists display.
	 *
	 * @param array $settings Shortcode attributes.
	 * @param array $atts     Shortcode attributes.
	 *
	 * @return array|mixed Query for the authors list display.
	 */
	public function args( $settings, $atts ) {

		// Args.
		$args = array(
			'fields'  => 'ID',
			'orderby' => $settings['orderby'],
			'order'   => $settings['order'],
		);

		// Pagination.
		if ( 'yes' === $settings['pagination'] && $settings['amount'] ) {
			$paged          = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			$args['number'] = $settings['amount'];
			$args['offset'] = $paged ? ( $paged - 1 ) * $settings['amount'] : 0;
		}

		// Order by last name.
		if ( 'last_name' === $settings['orderby'] ) {
			$args['meta_key'] = 'last_name'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['orderby']  = 'meta_value';
		}

		// Order by first name.
		if ( 'first_name' === $settings['orderby'] ) {
			$args['meta_key'] = 'first_name'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['orderby']  = 'meta_value';
		}

		// Only authors.
		if ( 'yes' === $settings['only_authors'] ) {
			if ( version_compare( $GLOBALS['wp_version'], '5.9-alpha', '<' ) ) {
				$args['who'] = 'authors';
			} else {
				$args['capability'] = array( 'edit_posts' );
			}
		}

		// Exclude.
		if ( ! empty( $settings['exclude'] ) ) {
			$args['exclude'] = explode( ',', $settings['exclude'] );
		}

		// Include.
		if ( ! empty( $settings['include'] ) ) {
			$args['include'] = explode( ',', $settings['include'] );
		}

		// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
		// Switch "categories" to "taxonomy" and "terms".
		if ( ! empty( $settings['categories'] ) ) {
			$settings['taxonomy'] = 'category';
			$settings['terms']    = $settings['categories'];
		}

		// Default taxonomy.
		if ( empty( $settings['taxonomy'] ) ) {
			$settings['taxonomy'] = 'category';
		}

		// Include based on taxonomy/term.
		if ( ! empty( $settings['terms'] ) ) {

			// Array of supplied categories.
			$terms = explode( ',', $settings['terms'] );

			// Query arguments.
			$args = array(
				'posts_per_page'         => -1,
				'orderby'                => 'author',
				'order'                  => 'ASC',
				'cache_results'          => false,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'tax_query'              => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy'         => $settings['taxonomy'],
						'terms'            => $terms,
						'include_children' => true,
					),
				),
			);

			// Get posts.
			$posts_array = get_posts( $args );

			// Get the author IDs.
			$post_author_ids = false;
			if ( $posts_array ) {
				$post_author_ids = wp_list_pluck( $posts_array, 'post_author' );
				$post_author_ids = array_unique( $post_author_ids );
			}

			if ( is_array( $post_author_ids ) ) {
				if ( empty( $args['include'] ) ) {
					$args['include'] = $post_author_ids;
				} else {
					$args['include'] = array_merge( $args['include'], $post_author_ids );
				}
			}

		}

		// Roles.
		if ( ! empty( $settings['roles'] ) ) {
			$args['role__in'] = explode( ',', $settings['roles'] );
			unset( $args['who'] );
			unset( $args['capability'] );
		}

		// Post types.
		if ( ! empty( $settings['post_types'] ) ) {
			$args['has_published_posts'] = explode( ',', $settings['post_types'] );
			unset( $args['who'] );
			unset( $args['capability'] );
		}

		// Limit by name starts with.
		if ( ! empty( $settings['name_starts_with'] ) ) {
			$args['search']         = sanitize_text_field( $settings['name_starts_with'] ) . '*';
			$args['search_columns'] = array(
				'display_name',
			);
		}

		// Limit by first name starts with.
		if ( ! empty( $settings['first_name_starts_with'] ) ) {
			$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => 'first_name',
					'value'   => '^' . sanitize_text_field( $settings['first_name_starts_with'] ),
					'compare' => 'REGEXP',
				),
			);
		}

		// Limit by last name starts with.
		if ( ! empty( $settings['last_name_starts_with'] ) ) {
			$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => 'last_name',
					'value'   => '^' . sanitize_text_field( $settings['last_name_starts_with'] ),
					'compare' => 'REGEXP',
				),
			);
		}

		return $args;

	}

	/**
	 * Display the shortcode content.
	 *
	 * @param array $authors_ids Author ids.
	 * @param array $settings    Shortcode attributes.
	 * @param array $atts        Shortcode attributes.
	 */
	public function shortcode_content( $authors_ids, $settings, $atts ) {

		$output_items = array();
		$count        = 0;
		$real_count   = 0;
		$ajax_request = isset( $atts['ajax_request'] ) ? $atts['ajax_request'] : 'no';

		// Get the override values.
		// Pagination.
		if ( 'yes' === $settings['pagination'] && $settings['amount'] ) {
			$total_users = count_users();
			$total_users = $total_users['avail_roles']['author'];
			$total_pages = ceil( $total_users / $settings['amount'] );
		}

		// Post types.
		if ( ! empty( $settings['post_types'] ) ) {
			$settings['skip_empty'] = 'no';
		}

		// Get last post date.
		$get_last_post_date = false;
		if ( 'post_date' === $settings['orderby'] || ! empty( $settings['latest_post_after'] ) ) {
			$get_last_post_date = true;
		}

		if ( 'no' === $ajax_request ) {
			?>
			<style>.authors-list-cols-dir-horizontal .authors-list-col{display:block;float:left;margin-right:3.42%}.authors-list-cols-dir-horizontal .authors-list-col-1{width:5.198%}.authors-list-cols-dir-horizontal .authors-list-col-2{width:13.81%}.authors-list-cols-dir-horizontal .authors-list-col-3{width:22.43%}.authors-list-cols-dir-horizontal .authors-list-col-4{width:31.05%}.authors-list-cols-dir-horizontal .authors-list-col-5{width:39.67%}.authors-list-cols-dir-horizontal .authors-list-col-6{width:48.29%}.authors-list-cols-dir-horizontal .authors-list-col-7{width:56.9%}.authors-list-cols-dir-horizontal .authors-list-col-8{width:65.52%}.authors-list-cols-dir-horizontal .authors-list-col-9{width:74.14%}.authors-list-cols-dir-horizontal .authors-list-col-10{width:82.76%}.authors-list-cols-dir-horizontal .authors-list-col-11{width:91.38%}.authors-list-cols-dir-horizontal .authors-list-col-12{width:100%}.authors-list-cols-dir-horizontal .authors-list-col-last{margin-right:0}.authors-list-cols-dir-horizontal .authors-list-col-first{clear:both}.authors-list-cols-dir-horizontal.authors-list-cols-2 .authors-list-col:nth-child(2n){margin-right:0}.authors-list-cols-dir-horizontal.authors-list-cols-2 .authors-list-col:nth-child(2n+1){clear:both}.authors-list-cols-dir-horizontal.authors-list-cols-3 .authors-list-col:nth-child(3n){margin-right:0}.authors-list-cols-dir-horizontal.authors-list-cols-3 .authors-list-col:nth-child(3n+1){clear:both}.authors-list-cols-dir-horizontal.authors-list-cols-4 .authors-list-col:nth-child(4n){margin-right:0}.authors-list-cols-dir-horizontal.authors-list-cols-4 .authors-list-col:nth-child(4n+1){clear:both}.authors-list-cols-dir-vertical{column-gap:3.42%}.authors-list-cols-dir-vertical.authors-list-cols-2{column-count:2}.authors-list-cols-dir-vertical.authors-list-cols-3{column-count:3}.authors-list-cols-dir-vertical.authors-list-cols-3{column-count:3}.authors-list-cols-dir-vertical.authors-list-cols-4{column-count:4}.authors-list-clearfix:after,.authors-list-clearfix:before{content:" ";display:table}.authors-list-clearfix:after{clear:both}.authors-list-item{margin-bottom:30px;position:relative}.authors-list-cols-dir-vertical .authors-list-item{break-inside:avoid-column;page-break-inside:avoid}.authors-list-item-thumbnail{margin-bottom:20px;position:relative}.authors-list-item-thumbnail a,.authors-list-item-thumbnail img{display:block;position:relative;border:0}.authors-list-item-thumbnail img{max-width:100%;height:auto;border-radius:inherit;}.authors-list-item-title{font-size:22px;font-weight:700;margin-bottom:5px}.authors-list-item-title a{color:inherit}.authors-list-item-subtitle{margin-bottom:5px;font-size:80%}.authors-list-item-social{margin-bottom:10px}.authors-list-item-social a{font-size:15px;margin-right:5px;text-decoration:none}.authors-list-item-social svg{width:15px}.authors-list-item-social-facebook{fill:#3b5998}.authors-list-item-social-instagram{fill:#405de6}.authors-list-item-social-linkedin{fill:#0077b5}.authors-list-item-social-pinterest{fill:#bd081c}.authors-list-item-social-tumblr{fill:#35465c}.authors-list-item-social-twitter{fill:#1da1f2}.authors-list-item-social-youtube{fill:red}.authors-list-item-social-tiktok{fill:#1e3050}.authors-list-item-excerpt{margin-bottom:10px}.authors-list-items-s2 .authors-list-item-main{position:absolute;bottom:0;left:0;right:0;padding:30px;color:#fff;background:rgba(0,0,0,.3)}.authors-list-items-s2 .authors-list-item-thumbnail{margin-bottom:0}.authors-list-items-s2 .authors-list-item-title{color:inherit}.authors-list-items-s2 .authors-list-item-link{color:inherit}.authors-list-items-s3 .authors-list-item-thumbnail{margin-bottom:0}.authors-list-items-s3 .authors-list-item-main{position:absolute;bottom:0;left:0;right:0;top:0;padding:30px;opacity:0;transform:scale(0);transition:all .3s;background:#fff;border:2px solid #eee}.authors-list-items-s3 .authors-list-item:hover .authors-list-item-main{opacity:1;transform:scale(1)}.authors-list-items-s4 .authors-list-item-thumbnail{float:left;margin-right:20px;width:25%}.authors-list-item-s4 .authors-list-item-main{overflow:hidden}.author-list-item-after-avatar,.author-list-item-after-bio,.author-list-item-after-count,.author-list-item-after-link,.author-list-item-after-title,.author-list-item-before-avatar,.author-list-item-before-bio,.author-list-item-before-count,.author-list-item-before-link,.author-list-item-before-title{margin-bottom:5px}@media only screen and (max-width:767px){.authors-list-cols-dir-horizontal .authors-list-col{width:100%;margin-right:0!important}.authors-list-cols-dir-vertical{column-count:1!important}}.authors-list-pagination{text-align:center}.authors-list-pagination li{display:inline-block;margin:0 2px}.authors-list-pagination li a,.authors-list-pagination li>span{display:inline-block;border:1px solid rgba(0,0,0,.2);padding:10px;line-height:1}</style>
			<?php
		}

		// Action hook `authors_list_before_loop`.
		// Hooks before the author lists loop.
		do_action( 'authors_list_before_loop', $atts );

		// Opening of the main authors list div.
		if ( 'no' === $ajax_request ) {
			?>
			<div class="authors-list-items authors-list-items-s<?php echo esc_attr( $settings['style'] ); ?> authors-list-clearfix authors-list-cols-<?php echo esc_attr( $settings['columns'] ); ?> authors-list-cols-dir-<?php echo esc_attr( $settings['columns_direction'] ); ?> <?php echo isset( $atts['id'] ) ? 'authors-list-item-' . esc_attr( $atts['id'] ) : ''; ?>">
			<?php
		}

		// Action hook `authors_list_after_main_loop_start`.
		// Hooks after the main loop div starts.
		do_action( 'authors_list_after_main_loop_start', $atts );

		// Loop through each author.
		foreach ( $authors_ids as $author_id ) :
			$count++;

			// Get the post count.
			$post_types = 'post';
			if ( ! empty( $settings['post_types'] ) ) {
				$post_types = explode( ',', $settings['post_types'] );
			}

			// Get the user post count.
			if ( is_object( $author_id ) ) {
				$author_id  = (int) $author_id->ID;
				$post_count = count_user_posts( $author_id, $post_types, true );
			} else {
				$post_count = count_user_posts( $author_id, $post_types, true );
			}

			// No posts, end.
			if ( ! $post_count && 'yes' === $settings['skip_empty'] ) {
				continue;
			}

			// Less than minimum posts, end.
			if ( $post_count < $settings['minimum_posts_count'] ) {
				continue;
			}

			// Buddypress member type.
			if ( ! empty( $settings['bp_member_types'] ) && function_exists( 'bp_get_member_type' ) ) {
				$bp_member_types = explode( ',', $settings['bp_member_types'] );
				if ( ! in_array( bp_get_member_type( $author_id ), $bp_member_types ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
					continue;
				}
			}

			// Get the last post date if needed.
			if ( $get_last_post_date ) {
				// Skip if no posts.
				if ( ! $post_count ) {
					$latest_post_date_unix = 1;
				}

				// Get latest post.
				$latest_post = get_posts(
					array(
						'author'      => $author_id,
						'orderby'     => 'date',
						'order'       => 'desc',
						'numberposts' => 1,
						'post_type'   => $post_types,
					)
				);

				if ( empty( $latest_post ) ) {
					$latest_post_date_unix = 1;
					$latest_post_date_ymd  = 1;
				} else {
					$latest_post_date_unix = strtotime( $latest_post[0]->post_date );
					$latest_post_date_ymd  = date( 'yyyymmdd', strtotime( $latest_post[0]->post_date ) ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
				}

			} else {
				$latest_post_date_unix = 1;
				$latest_post_date_ymd  = 1;
			}

			// Skip if latest post older than date limit.
			if ( ! empty( $settings['latest_post_after'] ) ) {
				// Skip if no posts.
				if ( ! $post_count ) {
					continue;
				}

				if ( 'daily' === $settings['latest_post_after'] ) {
					$latest_post_date = $latest_post_date_ymd;
					$limit_post_date  = current_time( 'yyyymmdd' );
				} else {
					$latest_post_date = $latest_post_date_unix;
					$limit_post_date  = strtotime( $settings['latest_post_after'] . ' days ago' );
				}

				if ( $latest_post_date < $limit_post_date ) {
					continue;
				}
			}

			// For ordering by comment count.
			$comment_count = 0;
			if ( 'comment_count' === $settings['orderby'] ) {
				$comment_count = get_comments(
					array(
						'post_author'               => $author_id,
						'fields'                    => 'ids',
						'count'                     => true,
						'update_comment_meta_cache' => false,
						'update_comment_post_cache' => false,
					)
				);
			}

			// Variables.
			$name          = get_the_author_meta( 'display_name', $author_id );
			$bio           = get_the_author_meta( 'description', $author_id );
			$custom_avatar = $this->get_meta( $author_id, $settings['avatar_meta_key'] );
			$posts_url     = get_author_posts_url( $author_id );

			// Posts URL for bbPress profile.
			if ( 'bbpress_profile' === $settings['link_to'] && function_exists( 'bbp_get_user_profile_url' ) ) {
				$posts_url = bbp_get_user_profile_url( $author_id );
			}

			// Posts URL for BuddyPress profile.
			if ( 'buddypress_profile' === $settings['link_to'] && function_exists( 'bp_core_get_user_domain' ) ) {
				$posts_url = bp_core_get_user_domain( $author_id );
			}

			// Posts URL for custom user meta.
			if ( 'meta' === $settings['link_to'] && ! empty( $settings['link_to_meta_key'] ) ) {
				$posts_url = $this->get_meta( $author_id, sanitize_text_field( $settings['link_to_meta_key'] ) );
			}

			// Start output buffer.
			ob_start();

			$this->authors_list_loop(
				$settings,
				array(
					'author_id'            => $author_id,
					'author_name'          => $name,
					'author_bio'           => $bio,
					'author_custom_avatar' => $custom_avatar,
					'author_posts_url'     => $posts_url,
					'author_post_count'    => $post_count,
				)
			);

			// Get the output buffer content.
			$output_item = ob_get_contents();

			// Clean the output buffer.
			ob_end_clean();

			$output_items[] = array(
				'date_unix'     => $latest_post_date_unix,
				'comment_count' => $comment_count,
				'post_count'    => $post_count,
				'output'        => $output_item,
			);

			$real_count++;

			// Limit reached, end.
			if ( $settings['amount'] && $real_count >= $settings['amount'] ) {
				break;
			}

		endforeach;

		// Order by latest post date.
		if ( 'post_date' === $settings['orderby'] ) {
			$array_column = array_column( $output_items, 'date_unix' );
			if ( 'DESC' === $settings['order'] ) {
				array_multisort( $array_column, SORT_DESC, SORT_NUMERIC, $output_items );
			} else {
				array_multisort( $array_column, SORT_ASC, SORT_NUMERIC, $output_items );
			}
		}

		// Order by comment count.
		if ( 'comment_count' === $settings['orderby'] ) {
			$array_column = array_column( $output_items, 'comment_count' );
			if ( 'DESC' === $settings['order'] ) {
				array_multisort( $array_column, SORT_DESC, SORT_NUMERIC, $output_items );
			} else {
				array_multisort( $array_column, SORT_ASC, SORT_NUMERIC, $output_items );
			}
		}

		// Order by all post (CPT) count.
		if ( 'all_post_count' === $settings['orderby'] ) {
			$array_column = array_column( $output_items, 'post_count' );
			if ( 'DESC' === $settings['order'] ) {
				array_multisort( $array_column, SORT_DESC, SORT_NUMERIC, $output_items );
			} else {
				array_multisort( $array_column, SORT_ASC, SORT_NUMERIC, $output_items );
			}
		}

		// Display the output.
		foreach ( $output_items as $output_item ) {
			echo $output_item['output']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Action hook `authors_list_after_main_loop_end`.
		// Hooks after the main loop div ends.
		do_action( 'authors_list_after_main_loop_end', $atts );

		// Closing of the main authors list div.
		if ( 'no' === $ajax_request ) {
			?>
			</div><!-- authors-list-items -->
			<?php
		}

		// Action hook `authors_list_after_loop`.
		// Hooks after the author lists loop.
		do_action( 'authors_list_after_loop', $atts );

		if ( 'yes' === $settings['pagination'] && $settings['amount'] ) :
			?>

			<div class="authors-list-pagination">
				<?php
				$current_page = max( 1, get_query_var( 'paged' ) );
				echo paginate_links( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'base'      => get_pagenum_link( 1 ) . '%_%',
						'current'   => $current_page,
						'total'     => $total_pages,
						'prev_next' => false,
						'type'      => 'list',
					)
				);
				?>
			</div><!-- .authors-list-pagination -->
			<?php

		endif;

	}

	/**
	 * Display the authors lists main contents.
	 *
	 * @param array $settings Shortcode attributes.
	 * @param array $vars     Author details.
	 */
	public function authors_list_loop( $settings, $vars ) {

		$item_class = '';
		switch ( $settings['columns'] ) {
			case '4':
				$item_class .= 'authors-list-col-3';
				break;
			case '3':
				$item_class .= 'authors-list-col-4';
				break;
			case '2':
				$item_class .= 'authors-list-col-6';
				break;
		}
		?>

		<div class="authors-list-item authors-list-item-clearfix authors-list-col <?php echo esc_attr( $item_class ); ?>">

			<?php if ( 'yes' === $settings['show_avatar'] ) : ?>
				<div class="authors-list-item-thumbnail">
					<?php if ( $settings['before_avatar'] ) : ?>
						<div class="author-list-item-before-avatar">
							<?php echo $this->parse_vars( $vars['author_id'], $settings['before_avatar'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>

					<a href="<?php echo esc_url( $vars['author_posts_url'] ); ?>" class="author-list-item-avatar">
						<?php if ( ! empty( $vars['author_custom_avatar'] ) ) : ?>
							<img src="<?php echo esc_url( $vars['author_custom_avatar'] ); ?>" alt="<?php echo esc_attr( $vars['author_name'] ); ?>">
						<?php else : ?>
							<?php echo get_avatar( $vars['author_id'], $settings['avatar_size'], '', esc_attr( $vars['author_name'] ) ); ?>
						<?php endif; ?>
					</a>

					<?php if ( $settings['after_avatar'] ) : ?>
						<div class="author-list-item-after-avatar">
							<?php echo $this->parse_vars( $vars['author_id'], $settings['after_avatar'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>
				</div><!-- .team-item-thumbnail -->
			<?php endif; ?>

			<div class="authors-list-item-main">

				<?php if ( $settings['before_title'] ) : ?>
					<div class="author-list-item-before-title">
						<?php echo $this->parse_vars( $vars['author_id'], $settings['before_title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_title'] ) : ?>
					<<?php echo esc_attr( $settings['title_element'] ); ?> class="authors-list-item-title">
						<a href="<?php echo esc_url( $vars['author_posts_url'] ); ?>"><?php echo esc_html( $vars['author_name'] ); ?></a>
					</<?php echo esc_attr( $settings['title_element'] ); ?>>
				<?php endif; ?>

				<?php if ( $settings['after_title'] ) : ?>
					<div class="author-list-item-after-title">
						<?php echo $this->parse_vars( $vars['author_id'], $settings['after_title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>

				<?php if ( $settings['before_count'] ) : ?>
					<div class="author-list-item-before-count">
						<?php echo $this->parse_vars( $vars['author_id'], $settings['before_count'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_count'] ) : ?>
					<?php
					$count_text = $settings['count_text'];
					if ( strpos( $count_text, ',' ) !== false ) {
						$count_text = explode( ',', $count_text );
					}

					if ( is_array( $count_text ) && 3 === count( $count_text ) ) {
						if ( 0 === $vars['author_post_count'] ) {
							$count_text = $count_text[0];
						} elseif ( 1 === $vars['author_post_count'] ) {
							$count_text = $count_text[1];
						} else {
							$count_text = $count_text[2];
						}
						$count_text = str_replace( '%', $vars['author_post_count'], $count_text );
					} else {
						$count_text = $vars['author_post_count'] . ' ' . $count_text;
					}
					?>
					<div class="authors-list-item-subtitle"><?php echo esc_html( $count_text ); ?></div>
				<?php endif; ?>

				<?php if ( $settings['after_count'] ) : ?>
					<div class="author-list-item-after-count">
						<?php echo $this->parse_vars( $vars['author_id'], $settings['after_count'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>

				<?php if ( $settings['before_bio'] ) : ?>
					<div class="author-list-item-before-bio">
						<?php echo $this->parse_vars( $vars['author_id'], $settings['before_bio'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_bio'] ) : ?>
					<div class="authors-list-item-excerpt">
						<?php
						if ( $settings['bio_word_trim'] ) {
							echo wp_trim_words( $vars['author_bio'], intval( $settings['bio_word_trim'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						} else {
							echo $vars['author_bio']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</div>
				<?php endif; ?>

				<?php if ( $settings['after_bio'] ) : ?>
					<div class="author-list-item-after-bio">
						<?php echo $this->parse_vars( $vars['author_id'], $settings['after_bio'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>

				<?php if ( $settings['before_link'] ) : ?>
					<div class="author-list-item-before-link">
						<?php echo $this->parse_vars( $vars['author_id'], $settings['before_link'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_link'] ) : ?>
					<a href="<?php echo esc_url( $vars['author_posts_url'] ); ?>" class="authors-list-item-link" aria-label="<?php esc_attr( $vars['author_name'] ); ?> - <?php esc_attr_e( 'View Posts &rarr;', 'authors-list' ); ?>"><?php esc_html_e( 'View Posts &rarr;', 'authors-list' ); ?></a>
				<?php endif; ?>

				<?php if ( $settings['after_link'] ) : ?>
					<div class="author-list-item-after-link">
						<?php echo $this->parse_vars( $vars['author_id'], $settings['after_link'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>

			</div><!-- .team-item-main -->

		</div><!-- .authors-list-item -->

		<?php

	}

	/**
	 * Return the parsed value of the author's details.
	 *
	 * @param int    $user_id User ID.
	 * @param string $text    Shortcode attribute content.
	 */
	public function parse_vars( $user_id, $text ) {

		$text = preg_replace_callback(
			'/{al:([^\s]+)}/i',
			function( $matches ) use ( $user_id ) {
				return $this->get_meta( $user_id, $matches[1] );
			},
			$text
		);

		$text = preg_replace_callback(
			'/\{alf:([^}]+)\}/i',
			function( $matches ) use ( $user_id ) {

				// No match.
				if ( empty( $matches[1] ) ) {
					return;
				}

				// Get all data in an array.
				$data = explode( ' ', $matches[1] );

				// No match for func name.
				if ( empty( $data[0] ) ) {
					return;
				}

				// Get method name.
				$method_name = $data[0];

				// No method by that name, return.
				if ( ! method_exists( $this, $method_name ) ) {
					return;
				}

				// Any args?
				$function_args = array(
					'user_id' => $user_id,
				);
				unset( $data[0] );
				if ( count( $data ) > 0 ) {
					foreach ( $data as $data_item ) {
						$data_item_args = explode( '=', $data_item );
						if ( ! empty( $data_item_args[0] ) && ! empty( $data_item_args[1] ) ) {
							$function_args[ $data_item_args[0] ] = trim( $data_item_args[1], "'" );
						}

					}
				}

				return call_user_func( array( $this, $method_name ), $function_args );

			},
			$text
		);

		return $text;

	}

	/**
	 * Get user meta field value.
	 *
	 * @param bool $user_id User ID.
	 * @param bool $name    The meta key to retrieve.
	 */
	public function get_meta( $user_id = false, $name = false ) {

		// No user ID and meta field supplied, return.
		if ( ! $user_id || ! $name ) {
			return;
		}

		// Get the user meta.
		$value = get_user_meta( $user_id, $name, true );

		// No user meta available, try userdata instead.
		if ( ! $value ) {
			$user_data = get_userdata( $user_id );

			if ( ! empty( $user_data->data->$name ) ) {
				$value = $user_data->data->$name;
			}
		}

		// Return the value.
		return $value;

	}

	/**
	 * Display author posts.
	 *
	 * @param array $args Arguments.
	 */
	public function posts( $args = array() ) {

		if ( empty( $args['amount'] ) ) {
			$args['amount'] = 1;
		}

		if ( empty( $args['type'] ) ) {
			$args['type'] = 'list';
		}

		if ( empty( $args['show_date'] ) ) {
			$args['show_date'] = 'no';
		}

		if ( empty( $args['date_format'] ) ) {
			$args['date_format'] = get_option( 'date_format' );
		}

		if ( empty( $args['categories'] ) ) {
			$args['categories'] = '';
		} else {
			$args['categories'] = explode( ',', $args['categories'] );
		}

		// Get posts.
		$query_args = array(
			'author'      => $args['user_id'],
			'numberposts' => $args['amount'],
		);

		if ( ! empty( $args['categories'] ) ) {
			$query_args['category__in'] = $args['categories'];
		}

		$posts = get_posts( $query_args );

		// No posts found, return.
		if ( ! $posts ) {
			return;
		}

		$el_wrap = 'ul';
		$el_item = 'li';

		if ( 'plain' === $args['type'] ) {
			$el_wrap = 'div';
			$el_item = 'div';
		}

		// Output buffer.
		ob_start();
		?>
			<<?php echo esc_attr( $el_wrap ); ?> class="authors-list-posts">
				<?php foreach ( $posts as $post ) : ?>
					<?php $post_date = '<span>' . get_the_time( $args['date_format'], $post ) . '</span><br>'; ?>

					<<?php echo esc_attr( $el_item ); ?> class="authors-list-posts-item">
						<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
							<?php
							if ( 'yes' === $args['show_date'] ) {
								echo wp_kses(
									$post_date,
									array(
										'span' => array(),
										'br'   => array(),
									)
								);
							}
							echo esc_html( get_the_title( $post->ID ) );
							?>
						</a>
					</<?php echo esc_attr( $el_item ); ?>>
				<?php endforeach; ?>
			</<?php echo esc_attr( $el_wrap ); ?>>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		// Return output.
		return $output;

	}

	/**
	 * Display author social.
	 *
	 * @param array $args Arguments.
	 */
	public function social( $args = array() ) {

		$user_id = $args['user_id'];

		if ( empty( $args['type'] ) ) {
			$args['type'] = 'svg';
		}

		$urls = array();

		$urls['facebook']  = get_user_meta( $user_id, 'facebook', true );
		$urls['instagram'] = get_user_meta( $user_id, 'instagram', true );
		$urls['linkedin']  = get_user_meta( $user_id, 'linkedin', true );
		$urls['pinterest'] = get_user_meta( $user_id, 'pinterest', true );
		$urls['tumblr']    = get_user_meta( $user_id, 'tumblr', true );
		$urls['twitter']   = get_user_meta( $user_id, 'twitter', true );
		$urls['youtube']   = get_user_meta( $user_id, 'youtube', true );
		$urls['tiktok']   = get_user_meta( $user_id, 'tiktok', true );


		if ( ! empty( $urls['twitter'] ) ) {
			$urls['twitter'] = 'https://twitter.com/' . $urls['twitter'];
		}

		$user_data = get_userdata( $user_id );
		if ( ! empty( $user_data->user_url ) ) {
			$urls['website'] = $user_data->user_url;
		}

		$icons = array();

		if ( 'svg' === $args['type'] ) {

			$icons['facebook'] = '<path d="M23.9981 11.9991C23.9981 5.37216 18.626 0 11.9991 0C5.37216 0 0 5.37216 0 11.9991C0 17.9882 4.38789 22.9522 10.1242 23.8524V15.4676H7.07758V11.9991H10.1242V9.35553C10.1242 6.34826 11.9156 4.68714 14.6564 4.68714C15.9692 4.68714 17.3424 4.92149 17.3424 4.92149V7.87439H15.8294C14.3388 7.87439 13.8739 8.79933 13.8739 9.74824V11.9991H17.2018L16.6698 15.4676H13.8739V23.8524C19.6103 22.9522 23.9981 17.9882 23.9981 11.9991Z"/>';

			$icons['instagram'] = '<path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>';

			$icons['linkedin'] = '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>';

			$icons['pinterest'] = '<path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z"/>';

			$icons['tumblr'] = '<path d="M14.563 24c-5.093 0-7.031-3.756-7.031-6.411V9.747H5.116V6.648c3.63-1.313 4.512-4.596 4.71-6.469C9.84.051 9.941 0 9.999 0h3.517v6.114h4.801v3.633h-4.82v7.47c.016 1.001.375 2.371 2.207 2.371h.09c.631-.02 1.486-.205 1.936-.419l1.156 3.425c-.436.636-2.4 1.374-4.156 1.404h-.178l.011.002z"/>';

			$icons['twitter'] = '<path d="M23.954 4.569c-.885.389-1.83.654-2.825.775 1.014-.611 1.794-1.574 2.163-2.723-.951.555-2.005.959-3.127 1.184-.896-.959-2.173-1.559-3.591-1.559-2.717 0-4.92 2.203-4.92 4.917 0 .39.045.765.127 1.124C7.691 8.094 4.066 6.13 1.64 3.161c-.427.722-.666 1.561-.666 2.475 0 1.71.87 3.213 2.188 4.096-.807-.026-1.566-.248-2.228-.616v.061c0 2.385 1.693 4.374 3.946 4.827-.413.111-.849.171-1.296.171-.314 0-.615-.03-.916-.086.631 1.953 2.445 3.377 4.604 3.417-1.68 1.319-3.809 2.105-6.102 2.105-.39 0-.779-.023-1.17-.067 2.189 1.394 4.768 2.209 7.557 2.209 9.054 0 13.999-7.496 13.999-13.986 0-.209 0-.42-.015-.63.961-.689 1.8-1.56 2.46-2.548l-.047-.02z"/>';

			$icons['youtube'] = '<path d="M23.495 6.205a3.007 3.007 0 0 0-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 0 0 .527 6.205a31.247 31.247 0 0 0-.522 5.805 31.247 31.247 0 0 0 .522 5.783 3.007 3.007 0 0 0 2.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 0 0 2.088-2.088 31.247 31.247 0 0 0 .5-5.783 31.247 31.247 0 0 0-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/>';

			$icons['website'] = '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="external-link-alt" class="svg-inline--fa fa-external-link-alt fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M432,320H400a16,16,0,0,0-16,16V448H64V128H208a16,16,0,0,0,16-16V80a16,16,0,0,0-16-16H48A48,48,0,0,0,0,112V464a48,48,0,0,0,48,48H400a48,48,0,0,0,48-48V336A16,16,0,0,0,432,320ZM488,0h-128c-21.37,0-32.05,25.91-17,41l35.73,35.73L135,320.37a24,24,0,0,0,0,34L157.67,377a24,24,0,0,0,34,0L435.28,133.32,471,169c15,15,41,4.5,41-17V24A24,24,0,0,0,488,0Z"></path></svg>';

			$icons['tiktok'] = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M448,209.91a210.06,210.06,0,0,1-122.77-39.25V349.38A162.55,162.55,0,1,1,185,188.31V278.2a74.62,74.62,0,1,0,52.23,71.18V0l88,0a121.18,121.18,0,0,0,1.86,22.17h0A122.18,122.18,0,0,0,381,102.39a121.43,121.43,0,0,0,67,20.14Z"/></svg>';

		} elseif ( 'fontawesome-v4' === $args['type'] ) {

			$icons['facebook']  = '<i class="fa fa-facebook"></i>';
			$icons['instagram'] = '<i class="fa fa-instagram"></i>';
			$icons['linkedin']  = '<i class="fa fa-linkedin"></i>';
			$icons['pinterest'] = '<i class="fa fa-pinterest"></i>';
			$icons['tumblr']    = '<i class="fa fa-tumblr"></i>';
			$icons['twitter']   = '<i class="fa fa-twitter"></i>';
			$icons['youtube']   = '<i class="fa fa-youtube"></i>';
			$icons['website']   = '<i class="fa fa-link"></i>';
			$icons['tiktok']   = '<i class="fa fa-tiktok"></i>';

		} elseif ( 'fontawesome-v5' === $args['type'] ) {

			$icons['facebook']  = '<i class="fab fa-facebook"></i>';
			$icons['instagram'] = '<i class="fab fa-instagram"></i>';
			$icons['linkedin']  = '<i class="fab fa-linkedin"></i>';
			$icons['pinterest'] = '<i class="fab fa-pinterest"></i>';
			$icons['tumblr']    = '<i class="fab fa-tumblr"></i>';
			$icons['twitter']   = '<i class="fab fa-twitter"></i>';
			$icons['youtube']   = '<i class="fab fa-youtube"></i>';
			$icons['website']   = '<i class="fa fa-link"></i>';
			$icons['tiktok']   = '<i class="fab fa-tiktok"></i>';

		}

		// Output buffer.
		ob_start();
		?>

		<div class="authors-list-item-social">
			<?php foreach ( $urls as $site => $url ) : ?>
				<?php if ( ! empty( $url ) ) : ?>
					<a target="_blank" rel="nofollow external noopener noreferrer" href="<?php echo esc_url( $url ); ?>" class="authors-list-item-social-<?php echo esc_attr( $site ); ?>">
						<?php if ( 'svg' === $args['type'] ) : ?>
							<svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
						<?php endif; ?>

							<?php echo $icons[ $site ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

						<?php if ( 'svg' === $args['type'] ) : ?>
							</svg>
						<?php endif; ?>
					</a>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

		<?php
		$output = ob_get_contents();
		ob_end_clean();

		// Return output.
		return $output;

	}

	/**
	 * Display follow link buddypress.
	 *
	 * @param array $args Arguments.
	 */
	public function buddypress_follow_link( $args = array() ) {

		$user_id = $args['user_id'];

		if ( function_exists( 'bp_follow_add_follow_button' ) && bp_loggedin_user_id() && bp_loggedin_user_id() != $user_id ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			bp_follow_add_follow_button(
				array(
					'leader_id'   => $user_id,
					'follower_id' => bp_loggedin_user_id(),
				)
			);
		}

	}

	/**
	 * Display user role.
	 *
	 * @param array $args Arguments.
	 */
	public function role( $args = array() ) {

		$user_id = $args['user_id'];

		$roles = get_userdata( $user_id );
		$roles = $roles->roles;

		// Output buffer.
		ob_start();
		?>

		<ul class="authors-list-item-roles">
			<?php foreach ( $roles as $role ) : ?>
				<li><?php echo esc_html( $role ); ?></li>
			<?php endforeach; ?>
		</ul>

		<?php
		$output = ob_get_contents();
		ob_end_clean();

		// Return output.
		return $output;

	}

	/**
	 * Display link to.
	 *
	 * @param array $args Arguments.
	 */
	public function link( $args = array() ) {

		$user_id = $args['user_id'];

		if ( empty( $args['url'] ) ) {
			$args['url'] = 'archive';
		}

		if ( 'archive' === $args['url'] ) {
			$url  = get_author_posts_url( $user_id );
			$text = esc_html__( 'View Posts', 'authors-list' );
		} elseif ( 'bbpress_profile' === $args['url'] && function_exists( 'bbp_get_user_profile_url' ) ) {
			$url  = bbp_get_user_profile_url( $user_id );
			$text = esc_html__( 'View Profile', 'authors-list' );
		} elseif ( 'buddypress_profile' === $args['url'] && function_exists( 'bp_core_get_user_domain' ) ) {
			$url  = bp_core_get_user_domain( $user_id );
			$text = esc_html__( 'View Profile', 'authors-list' );
		} else {
			$url  = esc_url( $args['url'] );
			$text = $url;
		}

		// Output buffer.
		ob_start();
		?>
			<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $text ); ?></a>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		// Return output.
		return $output;

	}

	/**
	 * Display latest post date.
	 *
	 * @param array $args Arguments.
	 */
	public function latest_post_date( $args = array() ) {

		if ( empty( $args['format'] ) ) {
			$args['format'] = get_option( 'date_format' );
		}

		if ( empty( $args['link'] ) ) {
			$args['link'] = 'yes';
		}

		// Get posts.
		$posts = get_posts(
			array(
				'author'      => $args['user_id'],
				'numberposts' => 1,
			)
		);

		// No posts found, return.
		if ( ! $posts ) {
			return;
		}

		// Output buffer.
		ob_start();
		?>

			<div class="authors-list-latest-post-date">
				<?php foreach ( $posts as $post ) : ?>
					<?php if ( 'yes' === $args['link'] ) : ?>
						<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
					<?php endif; ?>

						<?php echo esc_html( get_the_time( $args['format'], $post ) ); ?>

					<?php if ( 'yes' === $args['link'] ) : ?>
						</a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>

		<?php
		$output = ob_get_contents();
		ob_end_clean();

		// Return output.
		return $output;

	}

}

// Instantiate the class.
Authors_List_Shortcode::instance();
