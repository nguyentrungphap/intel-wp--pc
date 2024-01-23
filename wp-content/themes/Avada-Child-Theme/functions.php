<?php




function mytheme_enqueue_style() {
    // wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/telematics_style.css', array( 'avada-stylesheet' ) );
    //wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/css/style-file49.css', array( 'avada-stylesheet' ) );
    
}
add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_style' );





function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

function customJSFooter(){
?>
<!-- <script>
if(document.querySelector('.single-post')){
	document.querySelector('.title-heading-left').innerText = "You may also like ...";
}
</script> -->
<style>
.mega-contact-us-CTA a.mega-menu-link {
    color: #fff !important;
    font-family: ralewaysemi !important;
}
</style>
<?php
}

add_action( 'wp_footer', 'customJSFooter' );
add_image_size('yarpp-thumbnail', '400', '400', true );
add_action( 'widgets_init', 'main_menu_widgets_init' );

function main_menu_widgets_init() {
    register_sidebar( array(
        'name' => __( 'Mobile CTA', 'Avada' ),
        'id' => 'mobile-cta',
        'description' => __( 'Widgets in this area will be shown on all posts and pages.', 'Avada' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s top_cta_mobile">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
    ) );

}

/* addign new widget area below the single page content area*/
if ( function_exists('register_sidebar') )
  register_sidebar(array(
    'name' => 'single-page-bottom',
    'before_widget' => '<div class = "widgetizedArea">',
    'after_widget' => '</div>',
    'before_title' => '<h3>',
    'after_title' => '</h3>',
  )
);


/* addign new widget area below the single page content area*/
if ( function_exists('register_sidebar') )
  register_sidebar(array(
    'name' => 'blog-listing-top',
    'before_widget' => '<div class = "widgetizedArea">',
    'after_widget' => '</div>',
    'before_title' => '<h3>',
    'after_title' => '</h3>',
  )
);


/* addign new widget area on the right side of the news landing page*/
if ( function_exists('register_sidebar') )
  register_sidebar(array(
    'name' => 'Resources Featured Right',
    'before_widget' => '<div class = "widgetizedArea">',
    'after_widget' => '</div>',
    'before_title' => '<h3>',
    'after_title' => '</h3>',
  )
);

if ( ! function_exists( 'fusion_render_post_metadata' ) ) {
	/**
	 * Render the full meta data for blog archive and single layouts.
	 *
	 * @param string $layout    The blog layout (either single, standard, alternate or grid_timeline).
	 * @param string $settings HTML markup to display the date and post format box.
	 * @return  string
	 */
	function fusion_render_post_metadata( $layout, $settings = array() ) {

		$html     = '';
		$author   = '';
		$date     = '';
		$metadata = '';

		$settings = ( is_array( $settings ) ) ? $settings : array();

		if ( is_search() ) {
			$search_meta = array_flip( fusion_library()->get_option( 'search_meta' ) );

			$default_settings = array(
				'post_meta'          => empty( $search_meta ) ? false : true,
				'post_meta_author'   => isset( $search_meta['author'] ),
				'post_meta_date'     => isset( $search_meta['date'] ),
				'post_meta_cats'     => isset( $search_meta['categories'] ),
				'post_meta_tags'     => isset( $search_meta['tags'] ),
				'post_meta_comments' => isset( $search_meta['comments'] ),
				'post_meta_type'     => isset( $search_meta['post_type'] ),
			);
		} else {
			$default_settings = array(
				'post_meta'          => fusion_library()->get_option( 'post_meta' ),
				'post_meta_author'   => fusion_library()->get_option( 'post_meta_author' ),
				'post_meta_date'     => fusion_library()->get_option( 'post_meta_date' ),
				'post_meta_cats'     => fusion_library()->get_option( 'post_meta_cats' ),
				'post_meta_tags'     => fusion_library()->get_option( 'post_meta_tags' ),
				'post_meta_comments' => fusion_library()->get_option( 'post_meta_comments' ),
				'post_meta_type'     => false,
			);
		}

		$settings = wp_parse_args( $settings, $default_settings );
		$post_meta = get_post_meta( get_queried_object_id(), 'pyre_post_meta', true );

		// Check if meta data is enabled.
		if ( ( $settings['post_meta'] && 'no' !== $post_meta ) || ( ! $settings['post_meta'] && 'yes' === $post_meta ) ) {

			// For alternate, grid and timeline layouts return empty single-line-meta if all meta data for that position is disabled.
			if ( in_array( $layout, array( 'alternate', 'grid_timeline' ), true ) && ! $settings['post_meta_author'] && ! $settings['post_meta_date'] && ! $settings['post_meta_cats'] && ! $settings['post_meta_tags'] && ! $settings['post_meta_comments'] && ! $settings['post_meta_type'] ) {
				return '';
			}

			// Render post type meta data.
			if ( $settings['post_meta_type'] ) {
				$metadata .= '<span class="fusion-meta-post-type">' . esc_html( ucwords( get_post_type() ) ) . '</span>';
				$metadata .= '<span class="fusion-inline-sep">|</span>';
			}

			// Render author meta data.
			if ( $settings['post_meta_author'] ) {
				ob_start();
				the_author_posts_link();
				$author_post_link = ob_get_clean();

				// Check if rich snippets are enabled.
				if ( fusion_library()->get_option( 'disable_date_rich_snippet_pages' ) && fusion_library()->get_option( 'disable_rich_snippet_author' ) ) {
					/* translators: The author. */
					$metadata .= sprintf( esc_attr__( '', 'fusion-builder' ), '<span class="vcard"><span class="fn">' . $author_post_link . '</span></span>' );
				} else {
					/* translators: The author. */
					$metadata .= sprintf( esc_attr__( '', 'fusion-builder' ), '<span>' . $author_post_link . '</span>' );
				}
				// $metadata .= '<span class="fusion-inline-sep222222">|</span>';
			} else { // If author meta data won't be visible, render just the invisible author rich snippet.
				$author .= fusion_render_rich_snippets_for_pages( false, true, false );
			}

			// Render the updated meta data or at least the rich snippet if enabled.
			if ( $settings['post_meta_date'] ) {
				$metadata .= fusion_render_rich_snippets_for_pages( false, false, true );

				$formatted_date = get_the_time( fusion_library()->get_option( 'date_format' ) );
				$date_markup = '<span class="date">' . $formatted_date . '</span><span class="category-container">';
				$metadata .= apply_filters( 'fusion_post_metadata_date', $date_markup, $formatted_date );
			} else {
				$date .= fusion_render_rich_snippets_for_pages( false, false, true );
			}

			// Render rest of meta data.
			// Render categories.
			if ( $settings['post_meta_cats'] ) {
				$post_type  = get_post_type();

				$taxonomies = array(
					'avada_portfolio' => 'portfolio_category',
					'avada_faq'       => 'faq_category',
					'product'         => 'product_cat',
					'tribe_events'    => 'tribe_events_cat',
				);
				ob_start();
				if ( 'post' === $post_type ) {
					the_category( ', ' );
				} elseif ( 'page' !== $post_type && isset( $taxonomies[ $post_type ] ) ) {
					the_terms( get_the_ID(), $taxonomies[ $post_type ], '', ', ' );
				}
				$categories = ob_get_clean();

				if ( $categories ) {
					/* translators: The categories list. */
					$metadata .= ( $settings['post_meta_tags'] ) ? sprintf( esc_html__( 'Categories: %s', 'fusion-builder' ), $categories ) : $categories;
					$metadata .= '</span>';
				}
			}

			// Render tags.
			if ( $settings['post_meta_tags'] ) {
				ob_start();
				the_tags( '' );
				$tags = ob_get_clean();

				if ( $tags ) {
					/* translators: The tags list. */
					$metadata .= '<span class="meta-tags">' . sprintf( esc_html__( 'Tags: %s', 'fusion-builder' ), $tags ) . '</span><span class="fusion-inline-sep">|</span>';
				}
			}

			// Render comments.
			if ( $settings['post_meta_comments'] && 'grid_timeline' !== $layout ) {
				ob_start();
				comments_popup_link( esc_html__( '0 Comments', 'fusion-builder' ), esc_html__( '1 Comment', 'fusion-builder' ), esc_html__( '% Comments', 'fusion-builder' ) );
				$comments = ob_get_clean();
				$metadata .= '<span class="fusion-comments">' . $comments . '</span>';
			}

			// Render the HTML wrappers for the different layouts.
			if ( $metadata ) {
				$metadata = $author . $date . $metadata;

				if ( 'single' === $layout ) {
					$html .= '<div class="fusion-meta-info"><div class="fusion-meta-info-wrapper">' . $metadata . '</div></div>';
				} elseif ( in_array( $layout, array( 'alternate', 'grid_timeline' ), true ) ) {
					$html .= '<p class="fusion-single-line-meta">' . $metadata . '</p>';
				} else {
					$html .= '<div class="fusion-alignleft">' . $metadata . '</div>';
				}
			} else {
				$html .= $author . $date;
			}
		} else {
			// Render author and updated rich snippets for grid and timeline layouts.
			if ( fusion_library()->get_option( 'disable_date_rich_snippet_pages' ) ) {
				$html .= fusion_render_rich_snippets_for_pages( false );
			}
		}// End if().

		return apply_filters( 'fusion_post_metadata_markup', $html );
	}
}// End if().