<?php
/*-----------------------------------------------------------------------------------

Plugin Name: MyThemeShop Instagram Feeds
Version: 1.0

-----------------------------------------------------------------------------------*/
if ( ! class_exists( 'mts_instagram_widget' ) ) {
	class mts_instagram_widget extends WP_Widget {

		public function __construct() {
			parent::__construct(
				'mts_instagram_widget',
				sprintf( __('%sInstagram Feeds', 'cyprus' ), MTS_THEME_WHITE_LABEL ? '' : 'MTS ' ),
				array( 'description' => __( 'Show Instagram Feeds with image and URL.', 'cyprus' ) )
			);
		}

		public function form( $instance ) {
			$defaults              = array(
				'instagram_username'    => '',
				'instagram_num_post'    => '6',
				'instagram_grid'        => 'two-grid',
				'instagram_follow_text' => 'Follow me on Instagram',
				'instagram_follow_uri'  => '#',
			);
			$instance              = wp_parse_args( (array) $instance, $defaults );
			$title                 = isset( $instance['title'] ) ? $instance['title'] : '';
			$instagram_username    = isset( $instance['instagram_username'] ) ? $instance['instagram_username'] : '';
			$instagram_num_post    = isset( $instance['instagram_num_post'] ) ? $instance['instagram_num_post'] : 6;
			$instagram_grid        = isset( $instance['instagram_grid'] ) ? $instance['instagram_grid'] : __( 'two-grid', 'cyprus' );
			$instagram_follow_text = isset( $instance['instagram_follow_text'] ) ? $instance['instagram_follow_text'] : __( 'Follow me on Instagram', 'cyprus' );
			$instagram_follow_uri  = isset( $instance['instagram_follow_uri'] ) ? $instance['instagram_follow_uri'] : '#';
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'cyprus' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'instagram_username' ); ?>"><?php _e( 'Instagram User Name:', 'cyprus' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'instagram_username' ); ?>" name="<?php echo $this->get_field_name( 'instagram_username' ); ?>" type="text" value="<?php echo esc_attr( $instagram_username ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'instagram_num_post' ); ?>"><?php _e( 'Number of Posts to show', 'cyprus' ); ?></label>
				<input id="<?php echo $this->get_field_id( 'instagram_num_post' ); ?>" name="<?php echo $this->get_field_name( 'instagram_num_post' ); ?>" type="number" min="1" step="1" value="<?php echo esc_attr( $instagram_num_post ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'instagram_grid' ); ?>"><?php _e( 'Posts layout:', 'cyprus' ); ?></label>
				<select id="<?php echo $this->get_field_id( 'instagram_grid' ); ?>" name="<?php echo $this->get_field_name( 'instagram_grid' ); ?>">
					<option value="one-grid" <?php selected( $instagram_grid, 'one-grid', true ); ?>><?php _e( 'One Column', 'cyprus' ); ?></option>
					<option value="two-grid" <?php selected( $instagram_grid, 'two-grid', true ); ?>><?php _e( 'Two Column', 'cyprus' ); ?></option>
					<option value="three-grid" <?php selected( $instagram_grid, 'three-grid', true ); ?>><?php _e( 'Three Column', 'cyprus' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'instagram_follow_text' ); ?>"><?php _e( 'Follow Text:', 'cyprus' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'instagram_follow_text' ); ?>" name="<?php echo $this->get_field_name( 'instagram_follow_text' ); ?>" type="text" value="<?php echo esc_attr( $instagram_follow_text ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'instagram_follow_uri' ); ?>"><?php _e( 'Follow URL:', 'cyprus' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'instagram_follow_uri' ); ?>" name="<?php echo $this->get_field_name( 'instagram_follow_uri' ); ?>" type="text" value="<?php echo esc_attr( $instagram_follow_uri ); ?>" />
			</p>

			<?php
		}

		public function update( $new_instance, $old_instance ) {
			$instance                          = array();
			$instance['title']                 = strip_tags( $new_instance['title'] );
			$instance['instagram_username']    = $new_instance['instagram_username'];
			$instance['instagram_num_post']    = $new_instance['instagram_num_post'];
			$instance['instagram_grid']        = $new_instance['instagram_grid'];
			$instance['instagram_follow_text'] = $new_instance['instagram_follow_text'];
			$instance['instagram_follow_uri']  = $new_instance['instagram_follow_uri'];
			return $instance;
		}

		public function widget( $args, $instance ) {
			extract( $args );
			$title                 = apply_filters( 'widget_title', $instance['title'] );
			$instagram_username    = $instance['instagram_username'];
			$instagram_num_post    = $instance['instagram_num_post'];
			$instagram_grid        = $instance['instagram_grid'];
			$instagram_follow_text = $instance['instagram_follow_text'];
			$instagram_follow_uri  = $instance['instagram_follow_uri'];

			$before_widget = preg_replace( '/class="([^"]+)"/i', 'class="$1 ' . ( isset( $instance['box_layout'] ) ? $instance['box_layout'] : 'two-grid' ) . '"', $before_widget ); // Add horizontal/vertical class to widget
			echo $before_widget;
			if ( ! empty( $title ) ) {
				echo $before_title . $title . $after_title;
			}
			echo self::instrafram_data( $instagram_username, $instagram_num_post, $instagram_grid, $instagram_follow_text, $instagram_follow_uri );
			echo $after_widget;
		}

		public function instrafram_data( $instagram_username, $instagram_num_post, $instagram_grid, $instagram_follow_text, $instagram_follow_uri ) {

			echo '<div class="instagram-posts ' . $instagram_grid . '">';

				cyprus_instagram( $instagram_username, $instagram_num_post );

			echo '</div>';

			if ( ! empty( $instagram_follow_text ) && ! empty( $instagram_follow_uri ) ) {
				echo '<div class="instagram-button">';
				echo '<a href="' . $instagram_follow_uri . '" target="_blank">' . $instagram_follow_text . '</a>';
				echo '</div>';
			}
		}
	}
}
// Register widget
add_action( 'widgets_init', 'register_mts_instagram_widget' );
function register_mts_instagram_widget() {
	register_widget( 'mts_instagram_widget' );
}
