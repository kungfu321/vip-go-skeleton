<?php
/*-----------------------------------------------------------------------------------

	Plugin Name: MyThemeShop About Me
	Version: 1.0

-----------------------------------------------------------------------------------*/

class mts_aboutme_widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'mts_aboutme_widget',
			sprintf( __('%sAbout Me', 'cyprus' ), MTS_THEME_WHITE_LABEL ? '' : 'MTS ' ),
			array( 'description' => __( 'Show Author image, description with Social icons.', 'cyprus' ) )
		);
	}

 	public function form( $instance ) {
		$defaults = array(
			'aboutme_image_url'   => '',
			'aboutme_description' => '',
			'author_name'         => '',
			'facebook'            => '',
			'twitter'             => '',
			'instagram'           => '',
			'pinterest'           => '',
			'gplus'               => '',
			'linkedin'            => '',
			'dribbble'            => '',
		);
		$instance = wp_parse_args((array) $instance, $defaults);
		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'About Me', 'cyprus' );
		$aboutme_image_url = isset( $instance[ 'aboutme_image_url' ] ) ? $instance[ 'aboutme_image_url' ] : '';
		$aboutme_description = isset( $instance[ 'aboutme_description' ] ) ? $instance[ 'aboutme_description' ] : '';
		$author_name = isset( $instance[ 'author_name' ] ) ? $instance[ 'author_name' ] : '';
		$facebook = isset( $instance[ 'facebook' ] ) ? $instance[ 'facebook' ] : '';
		$twitter = isset( $instance[ 'twitter' ] ) ? $instance[ 'twitter' ] : '';
		$instagram = isset( $instance[ 'instagram' ] ) ? $instance[ 'instagram' ] : '';
		$pinterest = isset( $instance[ 'pinterest' ] ) ? $instance[ 'pinterest' ] : '';
		$gplus = isset( $instance[ 'gplus' ] ) ? $instance[ 'gplus' ] : '';
		$linkedin = isset( $instance[ 'linkedin' ] ) ? $instance[ 'linkedin' ] : '';
		$dribbble = isset( $instance[ 'dribbble' ] ) ? $instance[ 'dribbble' ] : '';
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'cyprus' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'author_name' ); ?>"><?php _e( 'Author Name', 'cyprus' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'author_name' ); ?>" name="<?php echo $this->get_field_name( 'author_name' ); ?>" type="text" value="<?php echo esc_attr( $author_name ); ?>" />
		</p>

		<p>
		   <label for="<?php echo $this->get_field_id( 'aboutme_image_url' ); ?>"><?php _e( 'Image URL', 'cyprus' ); ?></label>
		   <input class="widefat" id="<?php echo $this->get_field_id( 'aboutme_image_url' ); ?>" name="<?php echo $this->get_field_name( 'aboutme_image_url' ); ?>" type="text" value="<?php echo esc_attr( $aboutme_image_url ); ?>" />
			<small><?php _e( 'Recommended size: 150x150px', 'cyprus' ); ?></small>
		</p>

		<p>
	    <label for="<?php echo $this->get_field_id('aboutme_description'); ?>"><?php _e( 'Description', 'cyprus' ); ?></label>
	    <textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id('aboutme_description'); ?>" name="<?php echo $this->get_field_name('aboutme_description'); ?>"><?php echo $aboutme_description; ?></textarea>
    </p>

    <p>
			<label for="<?php echo $this->get_field_id( 'facebook' ); ?>"><?php _e( 'Facebook URL', 'cyprus' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'facebook' ); ?>" name="<?php echo $this->get_field_name( 'facebook' ); ?>" type="text" value="<?php echo esc_attr( $facebook ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter' ); ?>"><?php _e( 'Twitter URL', 'cyprus' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'twitter' ); ?>" name="<?php echo $this->get_field_name( 'twitter' ); ?>" type="text" value="<?php echo esc_attr( $twitter ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'instagram' ); ?>"><?php _e( 'Instagram URL', 'cyprus' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'instagram' ); ?>" name="<?php echo $this->get_field_name( 'instagram' ); ?>" type="text" value="<?php echo esc_attr( $instagram ); ?>" />
		</p>

		<p>
		 <label for="<?php echo $this->get_field_id( 'pinterest' ); ?>"><?php _e( 'Pinterest URL', 'cyprus' ); ?></label>
		 <input class="widefat" id="<?php echo $this->get_field_id( 'pinterest' ); ?>" name="<?php echo $this->get_field_name( 'pinterest' ); ?>" type="text" value="<?php echo esc_attr( $pinterest ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'gplus' ); ?>"><?php _e( 'G+ URL', 'cyprus' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'gplus' ); ?>" name="<?php echo $this->get_field_name( 'gplus' ); ?>" type="text" value="<?php echo esc_attr( $gplus ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'linkedin' ); ?>"><?php _e( 'Linkedin URL', 'cyprus' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'linkedin' ); ?>" name="<?php echo $this->get_field_name( 'linkedin' ); ?>" type="text" value="<?php echo esc_attr( $linkedin ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'dribbble' ); ?>"><?php _e( 'Dribbble URL', 'cyprus' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'dribbble' ); ?>" name="<?php echo $this->get_field_name( 'dribbble' ); ?>" type="text" value="<?php echo esc_attr( $dribbble ); ?>" />
		</p>

		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['aboutme_image_url'] = $new_instance['aboutme_image_url'];
		$instance['aboutme_description'] = $new_instance['aboutme_description'];
		$instance['author_name'] = $new_instance['author_name'];
		$instance['facebook'] = $new_instance['facebook'];
		$instance['twitter'] = $new_instance['twitter'];
		$instance['instagram'] = $new_instance['instagram'];
		$instance['pinterest'] = $new_instance['pinterest'];
		$instance['gplus'] = $new_instance['gplus'];
		$instance['linkedin'] = $new_instance['linkedin'];
		$instance['dribbble'] = $new_instance['dribbble'];
		return $instance;
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$aboutme_image_url = $instance['aboutme_image_url'];
		$aboutme_description = $instance['aboutme_description'];
		$author_name = $instance['author_name'];
		$facebook = $instance['facebook'];
		$twitter = $instance['twitter'];
		$instagram = $instance['instagram'];
		$pinterest = $instance['pinterest'];
		$gplus = $instance['gplus'];
		$linkedin = $instance['linkedin'];
		$dribbble = $instance['dribbble'];

		$before_widget = preg_replace('/class="([^"]+)"/i', 'class="$1 '.(isset($instance['box_layout']) ? $instance['box_layout'] : 'two-grid').'"', $before_widget); // Add horizontal/vertical class to widget
		echo $before_widget;
		if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
		echo self::aboutme_data( $aboutme_image_url, $aboutme_description, $author_name, $facebook, $twitter, $instagram, $pinterest, $gplus, $linkedin, $dribbble );
		echo $after_widget;
	}

	public function aboutme_data( $aboutme_image_url, $aboutme_description, $author_name, $facebook, $twitter, $instagram, $pinterest, $gplus, $linkedin, $dribbble ) {

		echo '<div class="aboutme-widget clearfix">';

			if ( ! empty( $aboutme_image_url ) ) {
				echo '<div class="aboutme-image">';
					echo '<img src="' . $aboutme_image_url . '" />';
				echo '</div>';
			}

			echo '<div class="left clearfix">';

				if ( ! empty( $author_name ) ) {
					echo '<div class="author-name">';
						echo '<h4>' . $author_name . '</h4>';
					echo '</div>';
				}

				if ( ! empty( $facebook ) || ! empty( $twitter ) || ! empty( $instagram ) || ! empty( $pinterest ) || ! empty( $gplus ) || ! empty( $linkedin ) || ! empty( $dribbble ) ) {
					echo '<div class="aboutme-social">';
						if( ! empty( $facebook ) ) {
							echo '<a href="'.$facebook.'" target="_blank"><i class="fa fa-facebook"></i></a>';
						}
						if( ! empty( $twitter ) ) {
							echo '<a href="'.$twitter.'" target="_blank"><i class="fa fa-twitter"></i></a>';
						}
						if( ! empty( $instagram ) ) {
							echo '<a href="'.$instagram.'" target="_blank"><i class="fa fa-instagram"></i></a>';
						}
						if( ! empty( $pinterest ) ) {
							echo '<a href="'.$pinterest.'" target="_blank"><i class="fa fa-pinterest-p"></i></a>';
						}
						if( ! empty( $gplus ) ) {
							echo '<a href="'.$gplus.'" target="_blank"><i class="fa fa-google-plus"></i></a>';
						}
						if( ! empty( $linkedin ) ) {
							echo '<a href="'.$linkedin.'" target="_blank"><i class="fa fa-linkedin"></i></a>';
						}
						if( ! empty( $dribbble ) ) {
							echo '<a href="'.$dribbble.'" target="_blank"><i class="fa fa-dribbble"></i></a>';
						}
					echo '</div>';
				}

			echo '</div>';

			if( !empty($aboutme_description) ) {
				echo '<div class="aboutme-description">';
					echo '<p>' . $aboutme_description . '</p>';
				echo '</div>';
			}

		echo '</div>';

	}
}
// Register widget
add_action( 'widgets_init', 'register_mts_aboutme_widget' );
function register_mts_aboutme_widget() {
	register_widget( 'mts_aboutme_widget' );
}
