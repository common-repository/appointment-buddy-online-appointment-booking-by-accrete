<?php

/**
 * Adds Foo_Widget widget.
 */
 
class Appointment_buddy_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {

		parent::__construct(
				'Appointment_buddy_Widget', // Base ID
				esc_html__( 'Appointment Buddy', 'appointment_buddy_Widget' ), // Name
				array( 'description' => esc_html__( 'A simple plugin for Appointments Booking', 'appointment_buddy_Widget' ), ) // Args
			);
		if ( is_active_widget( false, false, $this->id_base ) ) {
					add_action( 'wp_enqueue_scripts', array( $this, 'apbud_plugin_general_scriptsNstyles' ));
		}
			
	}
	
	public function apbud_plugin_general_scriptsNstyles()
	{
		//JS
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'ab-validate-js',apbud_JS . '/jquery.validate.min.js' );
		wp_enqueue_script( 'ab-datepicker-js',apbud_JS . '/datetimepicker.full.min.js' );
		wp_enqueue_script( 'ab-script-js',apbud_JS . '/ab-script.js' );
		//CSS
		wp_enqueue_style( 'ab-datepicker-min',apbud_CSS . '/datetimepicker.min.css' );
		wp_enqueue_style( 'ab-style',apbud_CSS . '/ab-style.css' );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['appointment_title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['appointment_title'] ) . $args['after_title'];
		}
		
		include(apbud_INC.'/form.php');
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$appointment_title = ! empty( $instance['appointment_title'] ) ? $instance['appointment_title'] : esc_html__( 'New title', 'appointment_buddy_Widget' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'appointment_title' ) ); ?>"><?php esc_attr_e( 'Title:', 'appointment_buddy_Widget' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'appointment_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'appointment_title' ) ); ?>" type="text" value="<?php echo esc_attr( $appointment_title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['appointment_title'] = ( ! empty( $new_instance['appointment_title'] ) ) ? strip_tags( $new_instance['appointment_title'] ) : '';
		
		return $instance;
	}

} // class Foo_Widget

?>