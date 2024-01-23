<?php
/**
 * Styler class.
 *
 * @package WPKube
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Authors_List_Styler' ) ) {

	/**
	 * Styler class.
	 */
	class Authors_List_Styler {

		/**
		 * Suffix for assets.
		 *
		 * @var string
		 */
		public $suffix = '';

		/**
		 * The single instance of the class.
		 *
		 * @var Authors_List_Styler
		 */
		protected static $instance = null;

		/**
		 * Main Authors_List_Styler Instance.
		 *
		 * Ensures only one instance of Authors_List_Styler is loaded or can be loaded.
		 *
		 * @return Authors_List_Styler - Main instance.
		 */
		public static function instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();

				/**
				 * Action hook authors_list_styler_loaded.
				 *
				 * Hooks after all instance of Authors_List_Styler is loaded.
				 */
				do_action( 'authors_list_styler_loaded' );
			}

			return self::$instance;

		}

		/**
		 * Authors_List_Styler constructor.
		 */
		public function __construct() {

			$this->suffix = '';

			// Enqueue scripts and styles.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		}

		/**
		 * Enqueue scripts and styles.
		 *
		 * @param string $hook Hook suffix for the current admin page.
		 */
		public function enqueue_scripts( $hook ) {

			// User doesn't have access here.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// If not on settings page do not proceed.
			if ( 'toplevel_page_authors_list_dashboard' !== $hook ) {
				return;
			}

			// Full array of regular and google fonts.
			$fonts = Authors_List_Helper::fonts();

			// Google fonts url.
			$google_fonts_url = Authors_List_Helper::google_fonts_url();

			// CSS.
			wp_enqueue_style( 'jquery-ui-slider' );
			wp_enqueue_style(
				'authors-list-styler-plugins-css',
				AUTHORS_LIST_BACKEND_URL . 'assets/css/styler.plugins' . $this->suffix . '.css',
				array(),
				AUTHORS_LIST_BACKEND_VERSION
			);
			wp_enqueue_style(
				'authors-list-styler-css',
				AUTHORS_LIST_BACKEND_URL . 'assets/css/styler' . $this->suffix . '.css',
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
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script( 'jquery-ui-droppable' );
			wp_enqueue_script( 'jquery-effects-core' );
			wp_enqueue_script( 'jquery-ui-slider' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script(
				'authors-list-styler-google-webfont',
				'//ajax.googleapis.com/ajax/libs/webfont/1/webfont.js',
				array(),
				AUTHORS_LIST_BACKEND_VERSION,
				false
			);
			wp_enqueue_script(
				'authors-list-styler-plugins-js',
				AUTHORS_LIST_BACKEND_URL . 'assets/js/styler.plugins' . $this->suffix . '.js',
				array(),
				AUTHORS_LIST_BACKEND_VERSION,
				false
			);
			wp_enqueue_script(
				'authors-list-styler-js',
				AUTHORS_LIST_BACKEND_URL . 'assets/js/styler' . $this->suffix . '.js',
				array(),
				AUTHORS_LIST_BACKEND_VERSION,
				false
			);

			// Localize.
			$styler_data = array(
				array(
					'element' => '.author-list-item-before-avatar',
				),
				array(
					'element'   => '.author-list-item-avatar',
					'nosupport' => 'typography',
				),
				array(
					'element' => '.author-list-item-after-avatar',
				),
				array(
					'element' => '.author-list-item-before-title',
				),
				array(
					'element' => '.authors-list-item-title',
				),
				array(
					'element' => '.author-list-item-after-title',
				),
				array(
					'element' => '.author-list-item-before-count',
				),
				array(
					'element' => '.authors-list-item-subtitle',
				),
				array(
					'element' => '.author-list-item-after-count',
				),
				array(
					'element' => '.author-list-item-before-bio',
				),
				array(
					'element' => '.authors-list-item-excerpt',
				),
				array(
					'element' => '.author-list-item-after-bio',
				),
				array(
					'element' => '.author-list-item-before-link',
				),
				array(
					'element' => '.authors-list-item-link',
				),
				array(
					'element' => '.author-list-item-after-link',
				),
			);
			$styler_data = apply_filters( 'authors_list_styler_data', $styler_data );
			wp_localize_script( 'authors-list-styler-js', 'authorsListStylerData', $styler_data );
			wp_localize_script( 'authors-list-styler-js', 'authorsListFonts', $fonts );

			// Enqueue the WP Media scripts.
			wp_enqueue_media();

		}

		/**
		 * Display.
		 */
		public function display() {

			// User doesn't have access here.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			?>
			<div id="authors-list-styler-panel">

				<div id="authors-list-styler-panel-main-wrap">

					<div id="authors-list-styler-panel-main">

					<!-- Select -->

					<div id="authors-list-styler-panel-main-select-note" class="authors-list-styler-panel-section authors-list-styler-active" data-authors-list-styler-id="select">

						<?php esc_html_e( 'To change styling for an element, click the element in the live preview on the right.', 'authors-list' ); ?>

					</div><!-- #authors-list-styler-panel-main-select-note -->

					<!-- Options -->

					<div id="authors-list-styler-panel-options" class="authors-list-styler-panel-section" data-authors-list-styler-id="options">

						<div id="authors-list-styler-panel-header">
							<span id="authors-list-styler-panel-header-primary"><strong><?php esc_html_e( 'Current element:', 'authors-list' ); ?></strong></span>
							<span id="authors-list-styler-panel-header-secondary"><?php esc_html_e( 'none selected', 'authors-list' ); ?></span>
						</div><!-- #authors-list-styler-panel-header -->

						<div class="nav-tab-wrapper authors-list-styler-nav">
							<a href="#" class="nav-tab nav-tab-active" data-authors-list-styler-id="typography"><?php esc_html_e( 'Typography', 'authors-list' ); ?></a>
							<a href="#" class="nav-tab" data-authors-list-styler-id="spacing"><?php esc_html_e( 'Spacing', 'authors-list' ); ?></a>
							<a href="#" class="nav-tab" data-authors-list-styler-id="background"><?php esc_html_e( 'Background', 'authors-list' ); ?></a>
							<a href="#" class="nav-tab" data-authors-list-styler-id="border"><?php esc_html_e( 'Border', 'authors-list' ); ?></a>
						</div>

						<!-- Typography -->

						<div class="authors-list-styler-panel-options-group" data-authors-list-styler-id="typography">

							<div class="authors-list-styler-panel-options-group-main">
								<div class="authors-list-styler-panel-options-group-main-inner">
									<?php $this->display_typography_options(); ?>
								</div><!-- .authors-list-styler-panel-options-group-main-inner -->
							</div><!-- .authors-list-styler-panel-options-group-main -->

						</div><!-- .authors-list-styler-panel-options-group -->

						<!-- Spacing -->

						<div class="authors-list-styler-panel-options-group" data-authors-list-styler-id="spacing">

							<div class="authors-list-styler-panel-options-group-main">
								<div class="authors-list-styler-panel-options-group-main-inner">
									<?php $this->display_spacing_options(); ?>
								</div><!-- .authors-list-styler-panel-options-group-main-inner -->
							</div><!-- .authors-list-styler-panel-options-group-main -->

						</div><!-- .authors-list-styler-panel-options-group -->

						<!-- Background -->

						<div class="authors-list-styler-panel-options-group" data-authors-list-styler-id="background">

							<div class="authors-list-styler-panel-options-group-main">
								<div class="authors-list-styler-panel-options-group-main-inner">
									<?php $this->display_background_options(); ?>
								</div><!-- .authors-list-styler-panel-options-group-main-inner -->
							</div><!-- .authors-list-styler-panel-options-group-main -->

						</div><!-- .authors-list-styler-panel-options-group -->

						<!-- Border -->

						<div class="authors-list-styler-panel-options-group" data-authors-list-styler-id="border">

							<div class="authors-list-styler-panel-options-group-main">
								<div class="authors-list-styler-panel-options-group-main-inner">
									<?php $this->display_border_options(); ?>
								</div><!-- .authors-list-styler-panel-options-group-main-inner -->
							</div><!-- .authors-list-styler-panel-options-group-main -->

						</div><!-- .authors-list-styler-panel-options-group -->

					</div><!-- #authors-list-styler-panel-option -->

					</div><!-- #authors-list-styler-panel-main -->

				</div><!-- #authors-list-styler-panel-main-wrap -->

			</div><!-- .authors-list-styler-panel -->
			<?php

		}

		/**
		 * Display Option.
		 *
		 * @param array $atts HTML attributes.
		 */
		public function display_option( $atts = array() ) {

			// ( all ) type.
			$type = 'text';
			if ( isset( $atts['type'] ) ) {
				$type = $atts['type'];
			}

			// ( all ) affect CSS rule.
			$affect = '';
			if ( isset( $atts['affect'] ) ) {
				$affect = $atts['affect'];
			}

			// ( all ) label.
			$label = '';
			if ( isset( $atts['label'] ) ) {
				$label = $atts['label'];
			}

			// ( slider ) extension.
			$ext = '';
			if ( isset( $atts['ext'] ) ) {
				$ext = $atts['ext'];
			}

			// ( slider ) minimum.
			$min = '';
			if ( isset( $atts['min'] ) ) {
				$min = $atts['min'];
			}

			// ( slider ) maximum.
			$max = '';
			if ( isset( $atts['max'] ) ) {
				$max = $atts['max'];
			}

			// ( slider ) increment.
			$inc = '';
			if ( isset( $atts['inc'] ) ) {
				$inc = $atts['inc'];
			}

			// ( select ) choices.
			$choices = array();
			if ( isset( $atts['choices'] ) ) {
				$choices = $atts['choices'];
			}
			?>

			<div class="authors-list-styler-panel-option authors-list-styler-panel-option-type-<?php echo esc_attr( $type ); ?>" data-authors-list-styler-affect="<?php echo esc_attr( $affect ); ?>" data-authors-list-styler-ext="<?php echo esc_attr( $ext ); ?>" data-authors-list-styler-min="<?php echo esc_attr( $min ); ?>" data-authors-list-styler-max="<?php echo esc_attr( $max ); ?>" data-authors-list-styler-inc="<?php echo esc_attr( $inc ); ?>">

				<div class="authors-list-styler-panel-option-header">
					<span class="authors-list-styler-panel-option-label"><?php echo esc_html( $label ); ?></span>
					<span class="authors-list-styler-panel-option-extra"></span>
				</div><!-- .authors-list-styler-panel-option-header -->

				<div class="authors-list-styler-panel-option-main">

					<?php if ( 'select' === $type ) : ?>
						<select class="authors-list-styler-panel-option-value">
							<?php foreach ( $choices as $choice_val => $choice_lab ) : ?>
								<option value="<?php echo esc_attr( $choice_val ); ?>"><?php echo esc_html( $choice_lab ); ?></option>
							<?php endforeach; ?>
						</select>
					<?php elseif ( 'image' === $type ) : ?>
						<input type="hidden" class="authors-list-styler-panel-option-value" />
						<input type="button" class="authors-list-styler-panel-image-upload button button-secondary" value="<?php esc_html_e( 'Select Image', 'authors-list' ); ?>" />
						<div class="authors-list-styler-panel-image">
							<img src="" class="authors-list-styler-panel-image-url" />
							<span class="authors-list-styler-panel-image-delete">X</span>
						</div>
					<?php else : ?>
						<input type="text" class="authors-list-styler-panel-option-value" />
					<?php endif; ?>

					<?php if ( 'font-family' === $type ) : ?>
						<div class="authors-list-styler-panel-option-type-font-family-suggest"></div>
					<?php elseif ( 'slider' === $type ) : ?>
						<div class="authors-list-styler-panel-option-slider"></div>
					<?php endif; ?>

				</div><!-- .authors-list-styler-panel-option-main -->

			</div><!-- .authors-list-styler-panel-option -->

			<?php

		}

		/**
		 * Display typography options.
		 */
		public function display_typography_options() {

			// Color.
			$this->display_option(
				array(
					'type'   => 'colorpicker',
					'affect' => 'color',
					'label'  => esc_html__( 'Color', 'authors-list' ),
				)
			);

			// Font family.
			$this->display_option(
				array(
					'type'   => 'font-family',
					'affect' => 'font-family',
					'label'  => esc_html__( 'Font Family', 'authors-list' ),
				)
			);

			// Font size.
			$this->display_option(
				array(
					'type'   => 'slider',
					'affect' => 'font-size',
					'ext'    => 'px',
					'label'  => esc_html__( 'Font Size', 'authors-list' ),
				)
			);

			// Font weight.
			$this->display_option(
				array(
					'type'   => 'slider',
					'affect' => 'font-weight',
					'ext'    => '',
					'min'    => '100',
					'max'    => '900',
					'inc'    => '100',
					'label'  => esc_html__( 'Font Weight', 'authors-list' ),
				)
			);

			// Line height.
			$this->display_option(
				array(
					'type'   => 'slider',
					'affect' => 'line-height',
					'ext'    => 'px',
					'label'  => esc_html__( 'Line Height', 'authors-list' ),
				)
			);

			// Letter Spacing.
			$this->display_option(
				array(
					'type'   => 'slider',
					'affect' => 'letter-spacing',
					'ext'    => 'px',
					'max'    => '40',
					'label'  => esc_html__( 'Letter Spacing', 'authors-list' ),
				)
			);

			// Text Align.
			$this->display_option(
				array(
					'type'    => 'select',
					'choices' => array(
						'left'   => esc_html__( 'Left', 'authors-list' ),
						'center' => esc_html__( 'Center', 'authors-list' ),
						'right'  => esc_html__( 'Right', 'authors-list' ),
					),
					'affect'  => 'text-align',
					'label'   => esc_html__( 'Text Align', 'authors-list' ),
				)
			);

		}

		/**
		 * Display spacing options.
		 */
		public function display_spacing_options() {

			// Padding left.
			$this->display_option(
				array(
					'type'   => 'slider',
					'affect' => 'padding-left',
					'ext'    => 'px',
					'label'  => esc_html__( 'Padding Left', 'authors-list' ),
				)
			);

			// Padding right.
			$this->display_option(
				array(
					'type'   => 'slider',
					'affect' => 'padding-right',
					'ext'    => 'px',
					'label'  => esc_html__( 'Padding Right', 'authors-list' ),
				)
			);

			// Padding top.
			$this->display_option(
				array(
					'type'   => 'slider',
					'affect' => 'padding-top',
					'ext'    => 'px',
					'label'  => esc_html__( 'Padding Top', 'authors-list' ),
				)
			);

			// Padding bottom.
			$this->display_option(
				array(
					'type'   => 'slider',
					'affect' => 'padding-bottom',
					'ext'    => 'px',
					'label'  => esc_html__( 'Padding Bottom', 'authors-list' ),
				)
			);

			// Margin left.
			$this->display_option(
				array(
					'type'   => 'slider',
					'affect' => 'margin-left',
					'ext'    => 'px',
					'label'  => esc_html__( 'Margin Left', 'authors-list' ),
				)
			);

			// Margin right.
			$this->display_option(
				array(
					'type'   => 'slider',
					'affect' => 'margin-right',
					'ext'    => 'px',
					'label'  => esc_html__( 'Margin Right', 'authors-list' ),
				)
			);

			// Margin top.
			$this->display_option(
				array(
					'type'   => 'slider',
					'affect' => 'margin-top',
					'ext'    => 'px',
					'label'  => esc_html__( 'Margin Top', 'authors-list' ),
				)
			);

			// Margin bottom.
			$this->display_option(
				array(
					'type'   => 'slider',
					'affect' => 'margin-bottom',
					'ext'    => 'px',
					'label'  => esc_html__( 'Margin Bottom', 'authors-list' ),
				)
			);

		}

		/**
		 * Display background options.
		 */
		public function display_background_options() {

			// BG Color.
			$this->display_option(
				array(
					'type'   => 'colorpicker',
					'affect' => 'background-color',
					'label'  => esc_html__( 'Color', 'authors-list' ),
				)
			);

			// BG Image.
			$this->display_option(
				array(
					'type'   => 'image',
					'affect' => 'background-image',
					'label'  => esc_html__( 'Image', 'authors-list' ),
				)
			);

			// BG Size.
			$this->display_option(
				array(
					'type'   => 'text',
					'affect' => 'background-size',
					'label'  => esc_html__( 'Size', 'authors-list' ),
				)
			);

			// BG Repeat.
			$this->display_option(
				array(
					'type'    => 'select',
					'choices' => array(
						'repeat'    => esc_html__( 'Repeat', 'authors-list' ),
						'repeat-x'  => esc_html__( 'Repeat Horizontal', 'authors-list' ),
						'repeat-y'  => esc_html__( 'Repeat Vertical', 'authors-list' ),
						'no-repeat' => esc_html__( 'No Repeat', 'authors-list' ),
					),
					'affect'  => 'background-repeat',
					'label'   => esc_html__( 'Image Repeat', 'authors-list' ),
				)
			);

			// BG attachment.
			$this->display_option(
				array(
					'type'    => 'select',
					'choices' => array(
						'fixed'  => 'Fixed',
						'scroll' => 'Scroll',
					),
					'affect'  => 'background-attachment',
					'label'   => esc_html__( 'Image Attachment', 'authors-list' ),
				)
			);

			// BG position.
			$this->display_option(
				array(
					'type'    => 'select',
					'choices' => array(
						'left top'      => esc_html__( 'Top Left', 'authors-list' ),
						'right top'     => esc_html__( 'Top Right', 'authors-list' ),
						'center top'    => esc_html__( 'Top Center', 'authors-list' ),
						'left center'   => esc_html__( 'Center Left', 'authors-list' ),
						'right center'  => esc_html__( 'Center Right', 'authors-list' ),
						'center center' => esc_html__( 'Center', 'authors-list' ),
						'left bottom'   => esc_html__( 'Bottom Left', 'authors-list' ),
						'right bottom'  => esc_html__( 'Bottom Right', 'authors-list' ),
						'center bottom' => esc_html__( 'Bottom Center', 'authors-list' ),
					),
					'affect'  => 'background-position',
					'label'   => esc_html__( 'Image Position', 'authors-list' ),
				)
			);

		}

		/**
		 * Display border options.
		 */
		public function display_border_options() {

			// Top Left Radius.
			$this->display_option(
				array(
					'type'   => 'slider',
					'affect' => 'border-top-left-radius',
					'ext'    => 'px',
					'label'  => esc_html__( 'Radius - Top Left', 'authors-list' ),
				)
			);

			// Top Right Radius.
			$this->display_option(
				array(
					'type'   => 'slider',
					'affect' => 'border-top-right-radius',
					'ext'    => 'px',
					'label'  => esc_html__( 'Radius - Top Right', 'authors-list' ),
				)
			);

			// Bottom Right Radius.
			$this->display_option(
				array(
					'type'   => 'slider',
					'affect' => 'border-bottom-right-radius',
					'ext'    => 'px',
					'label'  => esc_html__( 'Radius - Bottom Right', 'authors-list' ),
				)
			);

			// Bottom Left Radius.
			$this->display_option(
				array(
					'type'   => 'slider',
					'affect' => 'border-bottom-left-radius',
					'ext'    => 'px',
					'label'  => esc_html__( 'Radius - Bottom Left', 'authors-list' ),
				)
			);

			// Border Width.
			$this->display_option(
				array(
					'type'   => 'slider',
					'affect' => 'border-width',
					'ext'    => 'px',
					'label'  => esc_html__( 'Border Width', 'authors-list' ),
				)
			);

			// Border Style.
			$this->display_option(
				array(
					'type'    => 'select',
					'choices' => array(
						'none'   => esc_html__( 'None', 'authors-list' ),
						'dotted' => esc_html__( 'Dotted', 'authors-list' ),
						'dashed' => esc_html__( 'Dashed', 'authors-list' ),
						'solid'  => esc_html__( 'Solid', 'authors-list' ),
						'double' => esc_html__( 'Double', 'authors-list' ),
						'groove' => esc_html__( 'Groove', 'authors-list' ),
						'ridge'  => esc_html__( 'Ridge', 'authors-list' ),
						'inset'  => esc_html__( 'Inset', 'authors-list' ),
						'outset' => esc_html__( 'Outset', 'authors-list' ),
					),
					'affect'  => 'border-style',
					'label'   => esc_html__( 'Border Style', 'authors-list' ),
				)
			);

			// Color.
			$this->display_option(
				array(
					'type'   => 'colorpicker',
					'affect' => 'border-color',
					'label'  => esc_html__( 'Color', 'authors-list' ),
				)
			);

		}

	}

}

// Instantiate the class.
Authors_List_Styler::instance();
