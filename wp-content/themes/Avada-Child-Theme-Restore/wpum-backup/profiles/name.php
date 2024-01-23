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

<div class="profile-content-name">

		<h3> 
			<?php echo esc_html( $data->user->display_name ); ?>
			<?php echo esc_html( $data->user->post_date ); ?>

			<?php // if( $data->current_user_id === $data->user->ID ) : ?>
				<!-- <a href="<?php echo esc_url( get_permalink( wpum_get_core_page_id( 'account' ) ) ); ?>"><small><?php esc_html_e( '( Edit account )', 'wp-user-manager' ); ?></small></a> -->
			<?php // endif; ?>
		
		</h3> 

</div>
