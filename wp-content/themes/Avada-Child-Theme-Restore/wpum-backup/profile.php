<?php
/**
 * The Template for displaying the profile page.
 *
 * This template can be overridden by copying it to yourtheme/wpum/profile.php
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

<div class="wpum-template wpum-profile-page profile-container" id="wpum-profile">

	<?php do_action( 'wpum_before_profile' ); ?>

	<div id="profile-header-container" class="profile-header">
		<?php
			WPUM()->templates
				->set_template_data( [
					'user'            => $data->user,
					'current_user_id' => $data->current_user_id
				] )
				->get_template_part( 'profiles/cover' );

	

			

				

		?>
	</div>
<?php


WPUM()->templates
				->set_template_data( [
					'user'            => $data->user,
					'current_user_id' => $data->current_user_id
				] )
				->get_template_part( 'profiles/posts' );	
?>
	

	<div class="wpum_clearfix"></div>

	<?php do_action( 'wpum_after_profile' ); ?>

</div>
