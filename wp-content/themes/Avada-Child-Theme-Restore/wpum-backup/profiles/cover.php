<?php
/**
 * The Template for displaying the profile cover.
 *
 * This template can be overridden by copying it to yourtheme/wpum/profiles/cover.php
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

$cover_image = get_user_meta( $data->user->ID, 'user_cover', true );

?>
<div class="profile-header-top"> 
	<div class="profile-header-wrapper">
	<div id="header-cover" class="header-cover">
		<div id="header-avatar" class="header-avatar-container">
			<a href="<?php echo esc_url( wpum_get_profile_url( $data->user ) ); ?>">
				<?php echo get_avatar( $data->user->ID, 128 ); ?>
			</a>
		</div>
		<div class="header-profile-content"> 

		<?php
				WPUM()->templates
					->set_template_data( [
						'user'            => $data->user,
						'current_user_id' => $data->current_user_id,
						'tabs'            => wpum_get_registered_profile_tabs()
					] )
					->get_template_part( 'profiles/name' );
			?>
		</div>
	</div>

	<div class="discription-container" >
	<?php
				WPUM()->templates
					->set_template_data( [
						'user'            => $data->user,
						'current_user_id' => $data->current_user_id,
						'tabs'            => wpum_get_registered_profile_tabs()
					] )
					->get_template_part( 'profiles/description' );
			?>
	</div>
					</div>
</div>
