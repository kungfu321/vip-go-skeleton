<?php
/*-----------------------------------------------------------------------------------

	Plugin Name: MyThemeShop Contact Info
	Version: 2.0.1
	
-----------------------------------------------------------------------------------*/

class mts_contact_info_widget extends WP_Widget {
	
	function __construct() {

		$widget_ops = array (
			'classname' => 'mts_contact_info',
			'description' => __('A widget for contact info', 'cyprus' )
		);

		$control_ops = array (
			'id_base' => 'mts_contact_info'
		);
		
		parent::__construct( 'mts_contact_info', sprintf( __('%sContact Info', 'cyprus' ), MTS_THEME_WHITE_LABEL ? '' : 'MTS ' ), $widget_ops, $control_ops );
	
	}
	
	function form( $instance ) {

		$defaults = array(
			'title'    => 'Contact Info',
			'address'  => '',
			'phone'    => '',
			'mobile'   => '',
			'email'    => '',
			'emailtxt' => '',
			'web'      => '',
			'webtxt'   => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'cyprus' ); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'address' ); ?>"><?php _e( 'Address:', 'cyprus' ); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'address' ); ?>" name="<?php echo $this->get_field_name( 'address' ); ?>" value="<?php echo $instance['address']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'phone' ); ?>"><?php _e( 'Phone:', 'cyprus' ); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'phone' ); ?>" name="<?php echo $this->get_field_name( 'phone' ); ?>" value="<?php echo $instance['phone']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('mobile'); ?>"><?php _e( 'Mobile:', 'cyprus' ); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'mobile' ); ?>" name="<?php echo $this->get_field_name( 'mobile' ); ?>" value="<?php echo $instance['mobile']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'email' ); ?>"><?php _e( 'Email:', 'cyprus' ); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'email' ); ?>" name="<?php echo $this->get_field_name( 'email' ); ?>" value="<?php echo $instance['email']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'emailtxt' ); ?>"><?php _e( 'Email Link Text:', 'cyprus' ); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'emailtxt' ); ?>" name="<?php echo $this->get_field_name( 'emailtxt' ); ?>" value="<?php echo $instance['emailtxt']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'web' ); ?>"><?php _e( 'Website URL (with HTTP):', 'cyprus' ); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'web' ); ?>" name="<?php echo $this->get_field_name( 'web' ); ?>" value="<?php echo $instance['web']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'webtxt' ); ?>"><?php _e( 'Website URL Text:', 'cyprus' ); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'webtxt' ); ?>" name="<?php echo $this->get_field_name( 'webtxt' ); ?>" value="<?php echo $instance['webtxt']; ?>" />
		</p>
		<?php

	}

	
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']    = $new_instance['title'];
		$instance['address']  = $new_instance['address'];
		$instance['phone']    = $new_instance['phone'];
		$instance['mobile']   = $new_instance['mobile'];
		$instance['email']    = $new_instance['email'];
		$instance['emailtxt'] = $new_instance['emailtxt'];
		$instance['web']      = $new_instance['web'];
		$instance['webtxt']   = $new_instance['webtxt'];

		return $instance;

	}
	
	function widget( $args, $instance ) {

		extract( $args );

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '' );

		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		?>
			<div class="contact-info-container">
			<?php if ( isset( $instance['address'] ) && !empty( $instance['address'] ) ) : ?>
				<p><?php echo $instance['address']; ?></p>
			<?php endif; ?>

			<?php if ( isset( $instance['phone'] ) && !empty( $instance['phone'] ) ) : ?>
				<p><?php _e( 'Phone:', 'cyprus' ); ?> <?php echo $instance['phone']; ?></p>
			<?php endif; ?>

			<?php if ( isset( $instance['mobile'] ) && !empty( $instance['mobile'] ) ) : ?>
				<p><?php _e( 'Mobile:', 'cyprus' ); ?> <?php echo $instance['mobile']; ?></p>
			<?php endif; ?>

			<?php if ( isset( $instance['email'] ) && !empty( $instance['email'] ) ) : ?>
				<p><a href="mailto:<?php echo $instance['email']; ?>"><?php if ( $instance['emailtxt'] ) { echo $instance['emailtxt']; } else { echo $instance['email']; } ?></a></p>
			<?php endif; ?>

			<?php if ( isset( $instance['web'] ) && !empty( $instance['web'] ) ) : ?>
				<p><a href="<?php echo $instance['web']; ?>"><?php if ( isset( $instance['webtxt'] ) && $instance['webtxt'] ) { echo $instance['webtxt']; } else { echo $instance['web']; } ?></a></p>
			<?php endif; ?>
		</div>
		<?php

		echo $after_widget;
		
	}
	
}


// Register widget
add_action( 'widgets_init', 'register_mts_contact_info_widget' );
function register_mts_contact_info_widget() {
	register_widget( 'mts_contact_info_widget' );
}