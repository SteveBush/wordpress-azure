<?php
/**
 * @package BushChang_Membership
 */
/*
Plugin Name: BushChang Membership
Plugin URI: https://wordpress.org/plugins/bushchang-membership
Description: BushChang Membership is a simple plugin for displaying either a social login prompt or membership profile.
Version: 1.0.0
Author: Steve Bush
Author URI: https://profiles.wordpress.org/stevebush
License: GPLv2 or later
Text Domain: bushchang-membership
Domain Path: /languages
*/

/*
Copyright © 2013-2017 Steve Bush

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 2 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program; if not, write to the Free Software Foundation, Inc., 51 Franklin
Street, Fifth Floor, Boston, MA 02110-1301, USA.
*/

define( 'BUSHCHANG_MEMBERSHIP_VERSION', '1.0.0' );
define( 'BUSHCHANG_MEMBERSHIP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BUSHCHANG_MEMBERSHIP_DEFAULT_SIZE', 0 );

/**
 * Sets up plugin defaults and makes BushChang Membership available for translation.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses plugin_basename() For retrieving the basename of the plugin.
 *
 * @since BushChang Membership 1.0.0
 */
function bushchang_membership_init() {
	// Makes BushChang Membership available for translation.
	load_plugin_textdomain( 'bushchang-membership', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'init', 'bushchang_membership_init' );

/**
 * Registers sanitization callback and plugin setting fields.
 *
 * @uses register_setting() For registering a setting and its sanitization
 * callback.
 * @uses add_settings_field() For registering a settings field to a settings
 * page and section.
 * @uses __() For retrieving the translated string from the translate().
 *
 * @since BushChang Membership 1.0.0
 */
function bushchang_membership_admin_init() {
	// Registers plugin setting and its sanitization callback.
	register_setting( 'discussion', 'bushchang_membership', 'bushchang_membership_sanitize_options' );

	// Registers Default Size settings field under the Settings Discussion
	// Screen.
	add_settings_field( 'bushchang-membership-default-size', __( 'Default Size', 'bushchang-membership' ), 'bushchang_membership_default_size_settings_field', 'discussion', 'avatars' );
}

add_action( 'admin_init', 'bushchang_membership_admin_init' );

/**
 * Returns plugin default options.
 *
 * @since BushChang Membership 1.0.0
 *
 * @return array Plugin default options.
 */
function bushchang_membership_get_default_options() {
	$options = array(
		'default_size'   => BUSHCHANG_MEMBERSHIP_DEFAULT_SIZE
	);

	return $options;
}

/**
 * Returns plugin options.
 *
 * @uses get_option() For getting values for a named option.
 * @uses bushchang_membership_get_default_options() For retrieving plugin default
 * options.
 *
 * @since BushChang Membership 1.0.0
 *
 * @return array Plugin options.
 */
function bushchang_membership_get_options() {
	return get_option( 'bushchang_membership', bushchang_membership_get_default_options() );
}

/**
 * Sanitizes and validates plugin options.
 *
 * @uses bushchang_membership_get_default_options() For retrieving plugin default
 * options.
 * @uses absint() For converting a value to a non-negative integer.
 *
 * @since BushChang Membership 1.0.0
 *
 * @param array $input An associative array with user input.
 * @return array Sanitized plugin options.
 */
function bushchang_membership_sanitize_options( $input ) {
	$options = bushchang_membership_get_default_options();

	if ( isset( $input['default_size'] ) && is_numeric( trim( $input['default_size'] ) ) ) {
		$options['default_size'] = absint( trim( $input['default_size'] ) );

		if ( $options['default_size'] < 1 )
			$options['default_size'] = 1;
		elseif ( $options['default_size'] > 512 )
			$options['default_size'] = 512;
	}

	return $options;
}

/**
 * Prints Avatar Uploads settings field.
 *
 * @uses bushchang_membership_get_options() For retrieving plugin options.
 * @uses _e() For displaying the translated string from the translate().
 * @uses checked() For comparing two given values.
 *
 * @since BushChang Membership 1.0.0
 */
function bushchang_membership_avatar_uploads_settings_field() {
	// Retrieves plugin options.
	$options = bushchang_membership_get_options();
?>
	<fieldset>
		<legend class="screen-reader-text">
			<span>
				<?php _e( 'Avatar Uploads', 'bushchang-membership' ); ?>
			</span>
		</legend><!-- .screen-reader-text -->
		<label>
			<input <?php checked( $options['avatar_uploads'], 1, true ); ?> name="bushchang_membership[avatar_uploads]" type="checkbox" value="1">
			<?php _e( 'Anyone can upload', 'bushchang-membership' ); ?>
		</label>
	</fieldset>
	<?php
}

/**
 * Prints Default Size settings field.
 *
 * @uses bushchang_membership_get_options() For retrieving plugin options.
 * @uses _e() For displaying the translated string from the translate().
 *
 * @since BushChang Membership 1.0.0
 */
function bushchang_membership_default_size_settings_field() {
	// Retrieves plugin options.
	$options = bushchang_membership_get_options();
	?>
	<fieldset>
		<legend class="screen-reader-text">
			<span>
				<?php _e( 'Default Size', 'bushchang-membership' ); ?>
			</span>
		</legend><!-- .screen-reader-text -->
		<label>
			<?php _e( 'Default size of the avatar image', 'bushchang-membership' ); ?>
			<input class="small-text" min="1" name="bushchang_membership[default_size]" step="1" type="number" value="<?php echo $options['default_size']; ?>">
		</label>
	</fieldset>
	<?php
}

/**
 * Prints Avatar section.
 *
 * @uses bushchang_membership_get_options() For retrieving plugin options.
 * @uses is_multisite() For determining whether Multisite support is enabled.
 * @uses switch_to_blog() For switching the current blog to a different blog.
 * @uses get_post_meta() For retrieving attachment meta fields.
 * @uses restore_current_blog() For restoring the current blog.
 * @uses remove_filter() For removing a function attached to a specified action
 * hook.
 * @uses _e() For displaying the translated string from the translate().
 * @uses checked() For comparing two given values.
 * @uses get_avatar() For retrieving the avatar for a user.
 * @uses bushchang_membership_get_custom_avatar() For retrieving user custom avatar
 * based on user ID.
 * @uses current_user_can() For checking whether the current user has a certain
 * capability.
 * @uses add_query_arg() For retrieving a modified URL (with) query string.
 * @uses self_admin_url() For retrieving an admin url link with optional path
 * appended.
 * @uses wp_nonce_url() For retrieving URL with nonce added to URL query.
 * @uses esc_attr_e() For displaying translated text that has been escaped for
 * safe use in an attribute.
 * @uses did_action() For retrieving the number of times an action is fired.
 * @uses __() For retrieving the translated string from the translate().
 * @uses esc_attr() For escaping HTML attributes.
 *
 * @since BushChang Membership 1.0.0
 *
 * @param array $profileuser User to edit.
 */
function bushchang_membership_edit_user_profile( $profileuser ) {
	// Retrieves plugin options.
	$options = bushchang_membership_get_options();

	$avatar_type = isset( $profileuser->bushchang_membership_avatar_type ) ? $profileuser->bushchang_membership_avatar_type : 'gravatar';

	if ( isset( $profileuser->bushchang_membership_custom_avatar ) ) {
		// Determines whether Multisite support is enabled.
		if ( is_multisite() ) {
			// Switches the current blog to a different blog.
			switch_to_blog( $profileuser->bushchang_membership_blog_id );
		}

		// Retrieves attachment meta fields based on attachment ID.
		$custom_avatar_rating   = get_post_meta( $profileuser->bushchang_membership_custom_avatar, '_bushchang_membership_custom_avatar_rating', true );
		$user_has_custom_avatar = get_post_meta( $profileuser->bushchang_membership_custom_avatar, '_bushchang_membership_is_custom_avatar', true );

		// Determines whether Multisite support is enabled.
		if ( is_multisite() ) {
			// Restores the current blog.
			restore_current_blog();
		}
	}

	if ( ! isset( $custom_avatar_rating ) || empty( $custom_avatar_rating ) )
		$custom_avatar_rating = 'G';

	if ( ! isset( $user_has_custom_avatar ) || empty( $user_has_custom_avatar ) )
		$user_has_custom_avatar = false;

	if ( $user_has_custom_avatar ) {
		// Removes the function attached to the specified action hook.
		remove_filter( 'get_avatar', 'bushchang_membership_get_avatar' );
	}
	?>
	<h3>
		<?php _e( 'Avatar', 'bushchang-membership' ); ?>
	</h3>
	<table class="form-table" id="bushchang-membership">
		<tr>
			<th>
				<?php _e( 'Display this avatar', 'bushchang-membership' ); ?>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text">
						<span>
							<?php _e( 'Display this avatar', 'bushchang-membership' ); ?>
						</span><!-- .screen-reader-text -->
					</legend>
					<label>
						<input <?php checked( $avatar_type, 'gravatar', true ); ?> name="bushchang_membership_avatar_type" type="radio" value="gravatar">
						<?php echo get_avatar( $profileuser->ID, 32, '', false ); ?>
						<?php _e( 'Gravatar', 'bushchang-membership' ); ?>
					</label>
					<?php _e( '<a href="http://codex.wordpress.org/How_to_Use_Gravatars_in_WordPress" target="_blank">More information</a>', 'bushchang-membership' ); ?>
					<?php if ( $user_has_custom_avatar ) : ?>
						<br>
						<label>
							<input <?php checked( $avatar_type, 'custom', true ); ?> name="bushchang_membership_avatar_type" type="radio" value="custom">
							<?php echo bushchang_membership_get_custom_avatar( $profileuser->ID, 32, '', false ); ?>
							<?php _e( 'Custom', 'bushchang-membership' ); ?>
						</label>
						<?php
						if ( current_user_can( 'upload_files' ) || $options['avatar_uploads'] ) {
							$href = add_query_arg( array(
								'action'                => 'update',
								'bushchang_membership_action' => 'remove-avatar',
								'user_id'               => $profileuser->ID
							),
							self_admin_url( IS_PROFILE_PAGE ? 'profile.php' : 'user-edit.php' ) );
							?>
							<a class="delete" href="<?php echo wp_nonce_url( $href, 'update-user_' . $profileuser->ID ); ?>" onclick="return showNotice.warn();">
								<?php _e( 'Delete', 'bushchang-membership' ); ?>
							</a><!-- .delete -->
							<?php
						}
						?>
					<?php endif; ?>
				</fieldset>
			</td><!-- .bushchang-membership -->
		</tr>
		<?php if ( current_user_can( 'upload_files' ) || $options['avatar_uploads'] ) : ?>
			<tr>
				<th>
					<?php _e( 'Select Image', 'bushchang-membership' ); ?>
				</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span>
								<?php _e( 'Select Image', 'bushchang-membership' ); ?>
							</span>
						</legend><!-- .screen-reader-text -->
						<p>
							<label class="description" for="bushchang-membership-upload">
								<?php _e( 'Choose an image from your computer:', 'bushchang-membership' ); ?>
							</label><!-- .description -->
							<br>
							<input id="bushchang-membership-upload" name="bushchang_membership_import" type="file">
							<input class="button" name="bushchang_membership_submit" type="submit" value="<?php esc_attr_e( 'Upload', 'bushchang-membership' ); ?>">
						</p>
						<?php if ( current_user_can( 'upload_files' ) && did_action( 'wp_enqueue_media' ) ) : ?>
							<p>
								<label class="description" for="bushchang-membership-choose-from-library-link">
									<?php _e( 'Or choose an image from your media library:', 'bushchang-membership' ); ?>
								</label><!-- .description -->
								<br>
								<?php
								$modal_update_href = add_query_arg( array(
									'action'                => 'update',
									'bushchang_membership_action' => 'set-avatar',
									'user_id'               => $profileuser->ID
								),
								self_admin_url( IS_PROFILE_PAGE ? 'profile.php' : 'user-edit.php' ) );
								?>
								<a class="button" data-choose="<?php esc_attr_e( 'Choose a Custom Avatar', 'bushchang-membership' ); ?>" data-update="<?php esc_attr_e( 'Set as avatar', 'bushchang-membership' ); ?>" data-update-link="<?php echo wp_nonce_url( $modal_update_href, 'update-user_' . $profileuser->ID ); ?>" id="bushchang-membership-choose-from-library-link">
									<?php _e( 'Choose Image', 'bushchang-membership' ); ?>
								</a><!-- #bushchang-membership-choose-from-library-link -->
							</p>
						<?php endif; ?>
					</fieldset>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ( $user_has_custom_avatar ) : ?>
			<tr>
				<th>
					<?php _e( 'Avatar Rating', 'bushchang-membership' ); ?>
				</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span>
								<?php _e( 'Avatar Rating', 'bushchang-membership' ); ?>
							</span>
						</legend><!-- .screen-reader-text -->
						<?php
						$ratings = array(
							// Translators: Content suitability rating:
							// http://bit.ly/89QxZA
							'G'  => __( 'G &#8212; Suitable for all audiences', 'bushchang-membership' ),
							// Translators: Content suitability rating:
							// http://bit.ly/89QxZA
							'PG' => __( 'PG &#8212; Possibly offensive, usually for audiences 13 and above', 'bushchang-membership' ),
							// Translators: Content suitability rating:
							// http://bit.ly/89QxZA
							'R'  => __( 'R &#8212; Intended for adult audiences above 17', 'bushchang-membership' ),
							// Translators: Content suitability rating:
							// http://bit.ly/89QxZA
							'X'  => __( 'X &#8212; Even more mature than above', 'bushchang-membership' )
						);

						foreach ( $ratings as $key => $rating ) {
							?>
							<label>
								<input <?php checked( $custom_avatar_rating, $key, true ); ?> name="bushchang_membership_custom_avatar_rating" type="radio" value="<?php echo esc_attr( $key ); ?>">
								<?php echo $rating; ?>
							</label>
							<br>
							<?php
						}
						?>
						<span class="description">
							<?php _e( 'Choose a rating for your custom avatar.', 'bushchang-membership' ); ?>
						</span><!-- .description -->
					</fieldset>
				</td>
			</tr>
		<?php endif; ?>
	</table><!-- .form-table #bushchang-membership -->
	<?php
}

add_action( 'edit_user_profile', 'bushchang_membership_edit_user_profile' );
add_action( 'show_user_profile', 'bushchang_membership_edit_user_profile' );

/**
 * Enqueues plugin scripts and styles for Users Your Profile Screen.
 *
 * @uses is_admin() For checking if the Dashboard or the administration panel is
 * attempting to be displayed.
 * @uses current_user_can() For checking whether the current user has a certain
 * capability.
 * @uses wp_enqueue_media() For enqueuing all scripts, styles, settings, and
 * templates necessary to use all media JavaScript APIs.
 * @uses wp_register_style() For registering a CSS style file.
 * @uses wp_enqueue_style() For enqueuing a CSS style file.
 * @uses wp_register_script() For registering a JS script file.
 * @uses wp_enqueue_script() For enqueuing a JS script file.
 *
 * @since BushChang Membership 1.0.0
 */
function bushchang_membership_admin_enqueue_scripts() {
	if ( is_admin() && ! defined( 'IS_PROFILE_PAGE' ) )
		return;

	if ( current_user_can( 'upload_files' ) ) {
		// Enqueues all scripts, styles, settings, and templates necessary to
		// use all media JavaScript APIs.
		wp_enqueue_media();
	}

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	// Registers plugin CSS style file.
	wp_register_style( 'bushchang-membership', BUSHCHANG_MEMBERSHIP_PLUGIN_URL . 'assets/css/bushchang-membership' . $suffix . '.css', array(), '1.2.1' );

	// Enqueues plugin CSS style file.
	wp_enqueue_style( 'bushchang-membership' );

	// Registers plugin JS script file.
	wp_register_script( 'bushchang-membership', BUSHCHANG_MEMBERSHIP_PLUGIN_URL . 'assets/js/bushchang-membership' . $suffix . '.js', array( 'jquery' ), '1.2.1' );

	// Enqueues plugin JS script file.
	wp_enqueue_script( 'bushchang-membership' );
}

add_action( 'admin_enqueue_scripts', 'bushchang_membership_admin_enqueue_scripts' );
add_action( 'wp_enqueue_scripts', 'bushchang_membership_admin_enqueue_scripts' );


?>
