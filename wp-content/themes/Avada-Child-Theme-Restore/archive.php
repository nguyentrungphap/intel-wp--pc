<?php
/**
 * Archives template.
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
<section id="content" <?php Avada()->layout->add_class( 'content_class' ); ?> <?php Avada()->layout->add_style( 'content_style' ); ?>>
 <!--h1> <?php single_cat_title() ?> </h1--> 
 
<?php if ( category_description() ) : ?>
		<!--div id="post-<?php the_ID(); ?>" <?php post_class( 'fusion-archive-description' ); ?>>
			<div class="post-content">
				<?php echo category_description(); ?>
			</div>
		</div-->
	<?php endif; ?>

	<?php get_template_part( 'templates/blog', 'layout' ); ?>
</section>

<?php if ( !have_posts() && is_category() ) : ?>
    <div class="post-content category-no-results">

    	<?php Avada()->template->title_template( esc_html__( 'Couldn\'t find what you\'re looking for!', 'Avada' ) ); ?>
	</div>
    <div class="error-page">
				<div class="fusion-columns fusion-columns-3">
					<div class="fusion-column col-lg-4 col-md-4 col-sm-4 fusion-error-page-oops">
						<h1 class="oops"><?php esc_html_e( 'Oops!', 'Avada' ); ?></h1>
					</div>
                 </div>
	</div>
    
<?php endif; ?>

<?php do_action( 'avada_after_content' ); ?>
<?php
get_footer();

/* Omit closing PHP tag to avoid "Headers already sent" issues. */