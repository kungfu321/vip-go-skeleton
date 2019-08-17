<?php

if ( ! class_exists( 'MTS_Options_GoogleFonts' ) ) :

	class MTS_Options_GoogleFonts {

		private static $instance = null;

		public $fonts = array();

		public static function get_instance() {

			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		private function __construct() {
			$this->fonts = $this->get_fonts_array();
		}

		public function get_fonts_array() {

			if ( $transient = get_transient( 'cyprus_options_googlefonts' ) ) {
				return $transient;
			}

			$fonts = $this->get_from_api();
			set_transient( 'cyprus_options_googlefonts', $fonts, 7 * 24 * HOUR_IN_SECONDS );

			return $fonts;
		}

		private function get_from_api() {

			// Get the contents of the file
			$path = wp_normalize_path( dirname( __FILE__ ) . '/googlefonts-array.php' );
			$fonts_array = include_once $path;

			$final_fonts = array();
			$dir_path = get_template_directory() . '/includes/admin/libs/mts-options/img/fonts/';
	        $dir_url = get_template_directory_uri() . '/includes/admin/libs/mts-options/img/fonts/';

			if ( isset( $fonts_array['items'] ) ) {
				$all_variants = array();

				foreach ( $fonts_array['items'] as $font ) {
					// If font-family is not set then skip this item.
					if ( ! isset( $font['family'] ) ) {
						continue;
					}

					$font_slug = str_replace( ' ', '', strtolower( $font['family'] ) );
			        $exists = file_exists( $dir_path . $font_slug . '.png' );

					$final_fonts[ $font['family'] ] = array(
						'text' => $font['family'],
						'variants' => array(),
						'has-preview' => $exists ? $font_slug : false,
					);
					if ( isset( $font['variants'] ) && is_array( $font['variants'] ) ) {
						foreach ( $font['variants'] as $variant ) {
							$final_fonts[ $font['family'] ]['variants'][] = $this->convert_font_variants( $variant );
						}
					}
				}
			}
			return $final_fonts;
		}

		private function convert_font_variants( $variant ) {

			$variants = array(
				'regular'   => array( 'id' => '400',       'name' => esc_attr__( 'Normal 400', 'cyprus' ) ),
				'italic'    => array( 'id' => '400italic', 'name' => esc_attr__( 'Normal 400 Italic', 'cyprus' ) ),
				'100'       => array( 'id' => '100',       'name' => esc_attr__( 'Ultra-Light 100', 'cyprus' ) ),
				'200'       => array( 'id' => '200',       'name' => esc_attr__( 'Light 200', 'cyprus' ) ),
				'300'       => array( 'id' => '300',       'name' => esc_attr__( 'Book 300', 'cyprus' ) ),
				'500'       => array( 'id' => '500',       'name' => esc_attr__( 'Medium 500', 'cyprus' ) ),
				'600'       => array( 'id' => '600',       'name' => esc_attr__( 'Semi-Bold 600', 'cyprus' ) ),
				'700'       => array( 'id' => '700',       'name' => esc_attr__( 'Bold 700', 'cyprus' ) ),
				'700italic' => array( 'id' => '700italic', 'name' => esc_attr__( 'Bold 700 Italic', 'cyprus' ) ),
				'900'       => array( 'id' => '900',       'name' => esc_attr__( 'Ultra-Bold 900', 'cyprus' ) ),
				'900italic' => array( 'id' => '900italic', 'name' => esc_attr__( 'Ultra-Bold 900 Italic', 'cyprus' ) ),
				'100italic' => array( 'id' => '100italic', 'name' => esc_attr__( 'Ultra-Light 100 Italic', 'cyprus' ) ),
				'300italic' => array( 'id' => '300italic', 'name' => esc_attr__( 'Book 300 Italic', 'cyprus' ) ),
				'500italic' => array( 'id' => '500italic', 'name' => esc_attr__( 'Medium 500 Italic', 'cyprus' ) ),
				'800'       => array( 'id' => '800',       'name' => esc_attr__( 'Extra-Bold 800', 'cyprus' ) ),
				'800italic' => array( 'id' => '800italic', 'name' => esc_attr__( 'Extra-Bold 800 Italic', 'cyprus' ) ),
				'600italic' => array( 'id' => '600italic', 'name' => esc_attr__( 'Semi-Bold 600 Italic', 'cyprus' ) ),
				'200italic' => array( 'id' => '200italic', 'name' => esc_attr__( 'Light 200 Italic', 'cyprus' ) ),
			);
			if ( array_key_exists( $variant, $variants ) ) {
				return $variants[ $variant ];
			}
			return array(
				'id'   => $variant,
				'name' => $variant,
			);
		}
	}

	$googlefonts = MTS_Options_GoogleFonts::get_instance();
	return $googlefonts->fonts;

endif;
