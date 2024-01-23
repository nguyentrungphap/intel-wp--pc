<?php
/**
 * The template used for 404 pages.
 *
 * @package Avada
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php get_header(); ?>
<section id="content" class="full-width">
	<div id="post-404page">
		<div class="post-content">
			<?php
			// Render the page titles.
			$subtitle = esc_html__( 'Oops, This Page Could Not Be Found!', 'Avada' );
			Avada()->template->title_template( $subtitle );
			?>
			<div class="fusion-clearfix"></div>
			<div class="error-page">
				<div class="fusion-columns fusion-columns-2">
					<div class="fusion-column col-lg-8 col-md-8 col-sm-12 fusion-error-page-404">
						<div class="error-message">404</div>
					</div>
					<div class="fusion-column col-lg-4 col-md-4 col-sm-4 fusion-error-page-search">
						<h3><?php esc_html_e( 'Search Our Website', 'Avada' ); ?></h3>
						<p><?php esc_html_e( 'Can\'t find what you need? Take a moment and do a search below!', 'Avada' ); ?></p>
						<div class="search-page-search-form">
							<?php echo get_search_form( false ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php
get_footer();

/* Omit closing PHP tag to avoid "Headers already sent" issues. */