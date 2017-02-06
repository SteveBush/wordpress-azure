<?php
/**
 * @package BushChang-Membership
 */

/**
 * class short summary.
 *
 * class description.
 *
 * @version 1.0
 * @author stevebu
 */
class BushChang_Membership_Widget extends WP_Widget {

	function __construct() {

		load_plugin_textdomain( 'bushchang-membership' );

		parent::__construct(
			'bushchang_membership_widget',
			__( 'BushChang Membership Widget' , 'bushchang-membership'),
			array( 'description' => __( 'Manages BushChang Membership' , 'bushchang-membership') )
		);
	}

	function form( $instance ) {
		if ( $instance ) {
			$title = $instance['title'];
		}
		else {
			$title = __( 'BushChang Membership' , 'bushchang-membership' );
		}
?>

<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>">
		<?php esc_html_e( 'BushChang Membership:' , 'bushchang-membership'); ?>
	</label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>

<?php
	}


	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	function widget( $args, $instance ) {
		if ( ! isset( $instance['title'] ) ) {
			$instance['title'] = __( 'BushChang Membership' , 'bushchang-membership' );
		}

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'];
			echo esc_html( $instance['title'] );
			echo $args['after_title'];
		}

?>
		<div class="widefat">
			Here is my Widget
		</div>

<?php
		echo $args['after_widget'];
	}

}

function bushchang_membership_register_widgets() {
	register_widget( 'BushChang_Membership_Widget' );
}

add_action( 'widgets_init', 'bushchang_membership_register_widgets' );