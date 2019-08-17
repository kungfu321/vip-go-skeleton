<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Social Profile Icons
	Description: Show social profile icons in sidebar or footer.
	Version: 2.0

-----------------------------------------------------------------------------------*/

//Widget Registration.

function mts_load_widget() {

	register_widget( 'Social_Profile_Icons_Widget' );

}
if( ! class_exists( 'Social_Profile_Icons_Widget' ) ){
	class Social_Profile_Icons_Widget extends WP_Widget {

		protected $defaults;
		protected $sizes;
		protected $profiles;

		function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_footer-widgets.php', array( $this, 'print_scripts' ), 9999 );
			add_action( 'admin_footer-widgets.php', array( $this, 'cyprus_social_custom_stylings' ), 9999 );

			$this->defaults = array(
				'title'             => '',
				'social_font_color' => '#ffffff',
				'social_bg_color'   => '#333333',
				'font_size'         => 16,
				'br_radius'         => 3,
				'pd_top'            => 0,
				'pd_right'          => 0,
				'pd_bottom'         => 0,
				'pd_left'           => 0,
				'new_window'        => 0,
				'size'              => 32,
				'facebook'          => '',
				'behance'           => '',
				'flickr'            => '',
				'gplus'             => '',
				'pinterest'         => '',
				'instagram'         => '',
				'dribbble'          => '',
				'linkedin'          => '',
				'skype'             => '',
				'soundcloud'        => '',
				'email'             => '',
				'rss'               => '',
				'stumbleupon'       => '',
				'twitter'           => '',
				'youtube'           => '',
				'vimeo'             => '',
				'foursquare'        => '',
				'reddit'            => '',
				'github'            => '',
				'dropbox'           => '',
				'tumblr'            => '',
			);

			$this->sizes = array( '32' );

			$this->profiles = array(
				'facebook'    => array(
					'label'              => __( 'Facebook URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-facebook"><a title="Facebook" href="%s" %s><i class="fa fa-facebook"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#5d82d1',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'behance'     => array(
					'label'              => __( 'Behance URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-behance"><a title="Behance" href="%s" %s><i class="fa fa-behance"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#1879fd',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'flickr' => array(
					'label'	             => __( 'Flickr URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-flickr"><a title="Flickr" href="%s" %s><i class="fa fa-flickr"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#ff48a3',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'gplus' => array(
					'label'	             => __( 'Google+ URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-gplus"><a title="Google+" href="%s" %s><i class="fa fa-google-plus"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#eb5e4c',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'pinterest' => array(
					'label'	             => __( 'Pinterest URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-pinterest"><a title="Pinterest" href="%s" %s><i class="fa fa-pinterest"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#e13138',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'instagram' => array(
					'label'	             => __( 'Instagram URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-instagram"><a title="Instagram" href="%s" %s><i class="fa fa-instagram"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#91653f',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'dribbble' => array(
					'label'	             => __( 'Dribbble URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-dribbble"><a title="Dribbble" href="%s" %s><i class="fa fa-dribbble"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#f7659c',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'linkedin' => array(
					'label'	             => __( 'Linkedin URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-linkedin"><a title="LinkedIn" href="%s" %s><i class="fa fa-linkedin"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#238cc8',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'soundcloud' => array(
					'label'	             => __( 'Soundcloud URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-soundcloud"><a title="LinkedIn" href="%s" %s><i class="fa fa-soundcloud"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#ff7e30',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'twitter' => array(
					'label'	             => __( 'Twitter URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-twitter"><a title="Twitter" href="%s" %s><i class="fa fa-twitter"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#40bff5',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'vimeo' => array(
					'label'	             => __( 'Vimeo URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-vimeo"><a title="Vimeo" href="%s" %s><i class="fa fa-vimeo-square"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#35c6ea',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'stumbleupon' => array(
					'label'	             => __( 'StumbleUpon URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-stumbleupon"><a title="StumbleUpon" href="%s" %s><i class="fa fa-stumbleupon"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#ff5c30',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'tumblr' => array(
					'label'	             => __( 'Tumblr URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-tumblr"><a title="Tumblr" href="%s" %s><i class="fa fa-tumblr"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#426d9b',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'github' => array(
					'label'	             => __( 'GitHub URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-github"><a title="GitHub" href="%s" %s><i class="fa fa-github-alt"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#b5a470',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'youtube' => array(
					'label'	             => __( 'YouTube URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-youtube"><a title="YouTube" href="%s" %s><i class="fa fa-youtube"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#c9322b',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'foursquare' => array(
					'label'	             => __( 'FourSquare URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-foursquare"><a title="FourSquare" href="%s" %s><i class="fa fa-foursquare"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#0bbadf',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'reddit' => array(
					'label'	             => __( 'Reddit URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-reddit"><a title="Reddit" href="%s" %s><i class="fa fa-reddit"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#ff4400',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'dropbox' => array(
					'label'	             => __( 'Dropbox URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-dropbox"><a title="GitHub" href="%s" %s><i class="fa fa-dropbox"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#3476e4',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'skype' => array(
					'label'	             => __( 'Skype URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-skype"><a title="LinkedIn" href="%s" %s><i class="fa fa-skype"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#13c1f3',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'email' => array(
					'label'	             => __( 'Email URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-email"><a title="Email" href="%s" %s><i class="fa fa-envelope-o"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#1d90dd',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
				'rss' => array(
					'label'	             => __( 'RSS URI', 'cyprus' ),
					'text'               => __( 'Styling Options', 'cyprus' ),
					'pattern'            => '<li class="social-rss"><a title="RSS" href="%s" %s><i class="fa fa-rss"></i></a></li>',
					'social_color'       => __( 'Color', 'cyprus' ),
					'social_color_value' => '#ffffff',
					'social_bg'          => __( 'Background', 'cyprus' ),
					'social_bg_value'    => '#ef922f',
					'hover_color'        => __( 'Hover Color', 'cyprus' ),
					'hover_color_value'  => '#ffffff',
					'hover_bg'           => __( 'Hover Background', 'cyprus' ),
					'hover_bg_value'     => '#666666',
				),
			);

			$widget_ops = array(
				'classname'	 => 'social-profile-icons',
				'description' => __( 'Show profile icons.', 'cyprus' ),
			);

			$control_ops = array(
				'id_base' => 'social-profile-icons',
				#'width'   => 505,
				#'height'  => 350,
			);

			parent::__construct( 'social-profile-icons', sprintf( __( '%sSocial Profile Icons', 'cyprus' ), MTS_THEME_WHITE_LABEL ? '' : 'MTS ' ), $widget_ops, $control_ops );

		}

		/**
		 * Enqueue scripts.
		 *
		 * @since 1.0
		 *
		 * @param string $hook_suffix
		 */
		public function enqueue_scripts( $hook_suffix ) {
			if ( 'widgets.php' !== $hook_suffix ) {
				return;
			}

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
		}

		/**
		 * Print scripts.
		 *
		 * @since 1.0
		 */
		public function print_scripts() {
			?>
			<script>
				( function( $ ){
					function initColorPicker( widget ) {
						widget.find( '.color-picker' ).wpColorPicker( {
							change: _.throttle( function() { // For Customizer
								$(this).trigger( 'change' );
							}, 3000 )
						});
					}

					function onFormUpdate( event, widget ) {
						initColorPicker( widget );
					}

					$( document ).on( 'widget-added widget-updated', onFormUpdate );

					$( document ).ready( function() {
						$( '#widgets-right .widget:has(.color-picker)' ).each( function () {
							initColorPicker( $( this ) );
						} );
					} );
				}( jQuery ) );
			</script>
			<?php
		}

		public function cyprus_social_custom_stylings() {
			?>
			<style>
				.widget .wp-picker-active {
					position: relative;
					z-index: 2;
				}
				.wp-picker-container .wp-color-result.button { margin-right: 0 }
				.styling-options {
					clear: both;
					position: relative;
				}
				.widget .wp-picker-container { right: auto }
				.wp-picker-holder { z-index: 0 }
				.styling-option {
					position: absolute;
					right: 0;
					top: -58px;
					cursor: pointer;
				}
				.styling-options p {
					display: none;
					transition: linear 0.25s ease;
					margin-top: 0;
				}
			</style>

			<script>
				(function ($) {

					function onSocialUpdate() {
						var $options = $('.styling-option');
						$options.on('click', function(e) {
							e.preventDefault();
				      $(this).parent().children('p').slideToggle('fast');
						});
					}

					$( document ).on( 'widget-added widget-updated', onSocialUpdate );

					$( document ).ready( function() {
						onSocialUpdate();
					} );

				}( jQuery ) );
			</script>

			<?php
		}

		/**
		 * Widget Form.
		 *
		 * Outputs the widget form that allows users to control the output of the widget.
		 *
		 */
		function form( $instance ) {

			/** Merge with defaults */
			$instance = wp_parse_args( (array) $instance, $this->defaults ); ?>

			<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'cyprus' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" /></p>

			<h3><?php _e( 'Normal State', 'cyprus' ); ?></h3>
			<hr style="background: #ccc; border: 0; height: 1px; margin: 20px 0 0;" />
			<!-- Font Color -->
			<p style="float: left; width: 48%; margin-bottom: 30px;">
				<label for="<?php echo $this->get_field_id( 'social_font_color' ); ?>"><?php _e( 'Font Color', 'cyprus' ); ?></label><br>
				<input type="text" name="<?php echo $this->get_field_name( 'social_font_color' ); ?>" class="color-picker" id="<?php echo $this->get_field_id( 'social_font_color' ); ?>" value="<?php echo $instance['social_font_color']; ?>" data-default-color="#fff" />
			</p>

			<!-- Background Color -->
			<p style="float: right; width: 48%; margin-bottom: 30px;">
				<label for="<?php echo $this->get_field_id( 'social_bg_color' ); ?>"><?php _e( 'Background Color', 'cyprus' ); ?></label><br>
				<input type="text" name="<?php echo $this->get_field_name( 'social_bg_color' ); ?>" class="color-picker" id="<?php echo $this->get_field_id( 'social_bg_color' ); ?>" value="<?php echo $instance['social_bg_color']; ?>" data-default-color="#333333" />
			</p>

			<!-- Font-size -->
			<p style="float: left; width: 48%;">
			  <label for="<?php echo $this->get_field_id( 'font_size' ); ?>"><?php _e( 'Font Size', 'cyprus' ); ?></label>
			  <input id="<?php echo $this->get_field_id( 'font_size' ); ?>" name="<?php echo $this->get_field_name( 'font_size' ); ?>" type="number" min="1" step="1" value="<?php echo esc_attr( $instance['font_size'] ); ?>" />
			</p>

			<!-- Border Radius -->
			<p style="float: right; width: 48%;">
			   <label for="<?php echo $this->get_field_id( 'br_radius' ); ?>"><?php _e( 'Border Radius', 'cyprus' ); ?></label>
			   <input id="<?php echo $this->get_field_id( 'br_radius' ); ?>" name="<?php echo $this->get_field_name( 'br_radius' ); ?>" type="number" min="1" step="1" value="<?php echo esc_attr( $instance['br_radius'] ); ?>" />
			</p>

			<!-- Padding-->
			<p>
				<p style="font-size: 14px; margin-bottom: 0;"><strong><?php _e( 'Padding', 'cyprus' ); ?></strong></p>
				<p style="float: left; width: 22%; margin-right: 4%;">
				   <label for="<?php echo $this->get_field_id( 'pd_top' ); ?>"><?php _e( 'Top', 'cyprus' ); ?></label>
				   <input id="<?php echo $this->get_field_id( 'pd_top' ); ?>" name="<?php echo $this->get_field_name( 'pd_top' ); ?>" type="number" value="<?php echo esc_attr( $instance['pd_top'] ); ?>" style="width: 100%" />
				</p>

				<!-- Padding Right-->
				<p style="float: left; width: 22%; margin-right: 4%;">
				   <label for="<?php echo $this->get_field_id( 'pd_right' ); ?>"><?php _e( 'Right', 'cyprus' ); ?></label>
				   <input id="<?php echo $this->get_field_id( 'pd_right' ); ?>" name="<?php echo $this->get_field_name( 'pd_right' ); ?>" type="number" value="<?php echo esc_attr( $instance['pd_right'] ); ?>" style="width: 100%" />
				</p>

				<!-- Padding Bottom-->
				<p style="float: left; width: 22%; margin-right: 4%;">
				   <label for="<?php echo $this->get_field_id( 'pd_bottom' ); ?>"><?php _e( 'Bottom', 'cyprus' ); ?></label>
				   <input id="<?php echo $this->get_field_id( 'pd_bottom' ); ?>" name="<?php echo $this->get_field_name( 'pd_bottom' ); ?>" type="number" value="<?php echo esc_attr( $instance['pd_bottom'] ); ?>" style="width: 100%" />
				</p>

				<!-- Padding Left-->
				<p style="float: left; width: 22%;">
				   <label for="<?php echo $this->get_field_id( 'pd_left' ); ?>"><?php _e( 'Left', 'cyprus' ); ?></label>
				   <input id="<?php echo $this->get_field_id( 'pd_left' ); ?>" name="<?php echo $this->get_field_name( 'pd_left' ); ?>" type="number" value="<?php echo esc_attr( $instance['pd_left'] ); ?>" style="width: 100%" />
				</p>
			</p>

			<p><label><input id="<?php echo $this->get_field_id( 'new_window' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'new_window' ); ?>" value="1" <?php checked( 1, $instance['new_window'] ); ?>/> <?php esc_html_e( 'Open links in new window?', 'cyprus' ); ?></label></p>

			<hr style="background: #ccc; border: 0; height: 1px; margin: 20px 0;" />

			<?php
			foreach ( (array) $this->profiles as $profile => $data ) {

				$social_color_value = isset( $instance[ $profile . 'social_color_value' ] ) ? esc_attr( $instance[ $profile . 'social_color_value' ] ) : '';
				$social_bg_value    = isset( $instance[ $profile . 'social_bg_value' ] ) ? esc_attr( $instance[ $profile . 'social_bg_value' ] ) : '';
				$hover_color_value  = isset( $instance[ $profile . 'hover_color_value' ] ) ? esc_attr( $instance[ $profile . 'hover_color_value' ] ) : '';
				$hover_bg_value     = isset( $instance[ $profile . 'hover_bg_value' ] ) ? esc_attr( $instance[ $profile . 'hover_bg_value' ] ) : '';

				printf( '<p><label for="%s">%s</label>', esc_attr( $this->get_field_id( $profile ) ), esc_attr( $data['label'] ) );
				printf( '<input type="text" id="%s" class="widefat" name="%s" value="%s" /></p>', esc_attr( $this->get_field_id( $profile ) ), esc_attr( $this->get_field_name( $profile ) ), esc_url( $instance[ $profile ] ) );

				printf( '<div class="styling-options"><div class="styling-option">%s</div>', esc_attr( $data['text'] ) );
				/*social color field*/
				printf( '<p style="float: left; width: 48%%; margin-bottom: 40px;"><label for="%s">%s</label>', esc_attr( $this->get_field_id( $profile . 'social_color_value' ) ), esc_attr( $data['social_color'] ) );
				printf( '<input type="text" id="%s" class="color-picker" name="%s" value="%s" data-default-color="%s" /></p>', esc_attr( $this->get_field_id( $profile . 'social_color_value' ) ), esc_attr( $this->get_field_name( $profile . 'social_color_value' ) ), $social_color_value, $data['social_color_value'] );
				/*social background field*/
				printf( '<p style="float: right; width: 48%%; margin-bottom: 40px;"><label for="%s">%s</label>', esc_attr( $this->get_field_id( $profile . 'social_bg_value' ) ), esc_attr( $data['social_bg'] ) );
				printf( '<input type="text" id="%s" class="color-picker" name="%s" value="%s" data-default-color="%s" /></p>', esc_attr( $this->get_field_id( $profile . 'social_bg_value' ) ), esc_attr( $this->get_field_name( $profile . 'social_bg_value' ) ), $social_bg_value, $data['social_bg_value'] );
				/*social hover color*/
				printf( '<p style="float: left; width: 48%%; margin-bottom: 40px;"><label for="%s">%s</label>', esc_attr( $this->get_field_id( $profile . 'hover_color_value' ) ), esc_attr( $data['hover_color'] ) );
				printf( '<input type="text" id="%s" class="color-picker" name="%s" value="%s" data-default-color="%s" /></p>', esc_attr( $this->get_field_id( $profile . 'hover_color_value' ) ), esc_attr( $this->get_field_name( $profile . 'hover_color_value' ) ), $hover_color_value, $data['hover_color_value'] );
				/*social hover background color*/
				printf( '<p style="float: right; width: 48%%; margin-bottom: 40px;"><label for="%s">%s</label>', esc_attr( $this->get_field_id( $profile . 'hover_bg_value' ) ), esc_attr( $data['hover_bg'] ) );
				printf( '<input type="text" id="%s" class="color-picker" name="%s" value="%s" data-default-color="%s" /></p></div>', esc_attr( $this->get_field_id( $profile . 'hover_bg_value' ) ), esc_attr( $this->get_field_name( $profile . 'hover_bg_value' ) ), $hover_bg_value, $data['hover_bg_value'] );

			}

		}

		/**
		 * Form validation and sanitization.
		 *
		 * Runs when you save the widget form. Allows you to validate or sanitize widget options before they are saved.
		 *
		 */
		function update( $newinstance, $oldinstance ) {

			foreach ( (array) $this->profiles as $profile => $data ) {
				$newinstance[ $profile . 'social_color_value' ] = sanitize_hex_color( $newinstance[ $profile . 'social_color_value' ] );
				$newinstance[ $profile . 'social_bg_value' ]    = sanitize_hex_color( $newinstance[ $profile . 'social_bg_value' ] );
				$newinstance[ $profile . 'hover_color_value' ]  = sanitize_hex_color( $newinstance[ $profile . 'hover_color_value' ] );
				$newinstance[ $profile . 'hover_bg_value' ]     = sanitize_hex_color( $newinstance[ $profile . 'hover_bg_value' ] );
			}

			$newinstance['social_bg_color']    = sanitize_hex_color( $newinstance['social_bg_color'] );
			$newinstance['social_font_color']  = sanitize_hex_color( $newinstance['social_font_color'] );
			$newinstance['font_size']   = intval( $newinstance['font_size'] );
			$newinstance['br_radius']   = intval( $newinstance['br_radius'] );
			$newinstance['pd_top']      = intval( $newinstance['pd_top'] );
			$newinstance['pd_right']    = intval( $newinstance['pd_right'] );
			$newinstance['pd_bottom']   = intval( $newinstance['pd_bottom'] );
			$newinstance['pd_left']     = intval( $newinstance['pd_left'] );

			foreach ( $newinstance as $key => $value ) {
				if ( array_key_exists( $key, (array) $this->profiles  ) ) {
					$newinstance[$key] = esc_url( $newinstance[$key] );
				}
			}

			return $newinstance;
		}

		/**
		 * Widget Output.
		 *
		 * Outputs the actual widget on the front-end based on the widget options the user selected.
		 *
		 */
		function widget( $args, $instance ) {

			extract( $args );

			/** Merge with defaults */
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			echo $before_widget;

				if ( ! empty( $instance['title'] ) )
					echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;

				$output = '';

				$social_font_color = sanitize_hex_color( $instance['social_font_color'] );
				$social_bg_color   = sanitize_hex_color( $instance['social_bg_color'] );
				$font_size         = esc_attr( $instance['font_size'] ) . 'px';
				$br_radius         = esc_attr( $instance['br_radius'] ) . 'px';
				$pd_top            = esc_attr( $instance['pd_top'] ) . 'px';
				$pd_right          = esc_attr( $instance['pd_right'] ) . 'px';
				$pd_bottom         = esc_attr( $instance['pd_bottom'] ) . 'px';
				$pd_left           = esc_attr( $instance['pd_left'] ) . 'px';
				$new_window        = $instance['new_window'] ? 'target="_blank"' : '';

				foreach ( (array) $this->profiles as $profile => $data ) {
					if ( ! empty( $instance[ $profile ] ) )
						$output .= sprintf( $data['pattern'], esc_url( $instance[ $profile ] ), $new_window );
				}

				if ( $output )
					printf( '<div class="social-profile-icons"><ul class="%s">%s</ul></div>', '', $output ); ?>

				<style>

				</style>
			<?php echo $after_widget; ?>

			<style>
				.social-profile-icons ul li i { font-size: <?php echo $font_size; ?> }
				.social-profile-icons ul li a {
					color: <?php echo $social_font_color; ?>!important;
					background: <?php echo $social_bg_color; ?>;
					border-radius: <?php echo $br_radius; ?>;
					padding-top: <?php echo $pd_top; ?>;
					padding-right: <?php echo $pd_right; ?>;
					padding-bottom: <?php echo $pd_bottom; ?>;
					padding-left: <?php echo $pd_left; ?>;
				}
				<?php
				foreach ( (array) $this->profiles as $profile => $data ) {
					echo '.social-profile-icons .social-'.$profile.' a { color: '.$instance[ $profile . 'social_color_value' ].'!important; background: '.$instance[ $profile . 'social_bg_value' ].' } .social-profile-icons .social-'.$profile.' a:hover { color: '.$instance[ $profile . 'hover_color_value' ].'; background: '.$instance[ $profile . 'hover_bg_value' ].' }';
				}
				?>
			</style>

		<?php }

	}
}
add_action( 'widgets_init', 'mts_load_widget' );
