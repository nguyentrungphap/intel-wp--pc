<?php
/**
 * The Template for displaying the profile about tab content.
 *
 * This template can be overridden by copying it to yourtheme/wpum/profiles/about.php
 *
 * HOWEVER, on occasion WPUM will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @version 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="profile-content-box">

		<h3> <?php echo esc_html( $data->user->display_name ); ?></h3> 
	
		<div class="author-title-container"> 	<?php echo esc_html( $data->user->author_custom ); ?> </div>
		<div class="author-social-profile"> 	
		<?php 
		
		$linkedin =  esc_html( $data->user->author_linkedin );
		$facebook =  esc_html( $data->user->author_facebook );
		$twitter =  esc_html( $data->user->author_twitter );
		$medium =  esc_html( $data->user->author_dribble );

		?>	

		<?php  if( $linkedin ) : ?>
			<a class="linkedin"  href="<?php echo $linkedin ;?>" target="_blank"    title="LinkedIn" data-trigger="click"><span class="screen-reader-text">LinkedIn</span></a>
        <?php  endif; ?>

		<?php  if( $twitter ) : ?>
			<a class="twitter"  href="<?php echo $twitter ;?>" target="_blank"   title="Twitter" data-trigger="click"><span class="screen-reader-text">Twitter</span></a>
		<?php  endif; ?>
		
		<?php  if( $facebook ) : ?>
			<a class="facebook"  href="<?php echo $facebook ;?>" target="_blank"   title="Facebook" data-trigger="click"><span class="screen-reader-text">Facebook</span></a>
		<?php  endif; ?>
		
		<?php  if( $medium ) : ?>
			<a class="medium"  href="<?php echo $medium ;?>" target="_blank"    title="medium" data-trigger="click"><span class="screen-reader-text">Medium</span></a>
        <?php  endif; ?>

		</div>
		
			



			
			<?php // if( $data->current_user_id === $data->user->ID ) : ?>
				<!-- <a href="<?php echo esc_url( get_permalink( wpum_get_core_page_id( 'account' ) ) ); ?>"><small><?php esc_html_e( '( Edit account )', 'wp-user-manager' ); ?></small></a> -->
			<?php // endif; ?>
		
		

</div>
