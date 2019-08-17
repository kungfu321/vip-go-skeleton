<?php
/**
 * Calculate image sizes
 *
 * @package Cyprus
 */

defined( 'WPINC' ) || exit;

/**
 * Class images.
 */
class Cyprus_Images extends Cyprus_Base {

	/**
	 * The Constructor
	 */
	public function __construct() {

		// Retina Images.
		if ( cyprus_get_settings( 'retina_images' ) ) {
			$this->add_filter( 'wp_generate_attachment_metadata', 'generate_attachment_metadata', 10, 2 );
			$this->add_filter( 'delete_attachment', 'delete_attachment' );
		}

		if ( ! cyprus_get_settings( 'images_responsive' ) ) {

			// Override the calculated image sizes.
			add_filter( 'wp_calculate_image_sizes', '__return_false', 9999 );

			// Override the calculated image sources.
			add_filter( 'wp_calculate_image_srcset', '__return_false', 9999 );

			// Remove the reponsive stuff from the content.
			remove_filter( 'the_content', 'wp_make_content_images_responsive' );
		}
	}

	/**
	 * Generate retina images whenever a new image is uploaded
	 *
	 * @param  array $meta          An array of attachment meta data.
	 * @param  int   $attachment_id Current attachment ID.
	 * @return array
	 */
	public function generate_attachment_metadata( $meta, $attachment_id ) {

		if ( $this->is_image_meta( $meta ) ) {
			$this->generate_images( $meta, $attachment_id );
		}

		return $meta;
	}

	/**
	 * Generate images
	 *
	 * @param  array $meta An array of attachment meta data.
	 * @return array
	 */
	private function generate_images( $meta ) {

		if ( ! isset( $meta['file'] ) ) {
			return;
		}

		global $_wp_additional_image_sizes;
		$sizes  = $this->get_image_sizes();
		$ignore = array();

		// File info.
		$original_file     = $meta['file'];
		$pathinfo          = pathinfo( $original_file );
		$original_basename = $pathinfo['basename'];
		$uploads           = wp_upload_dir();
		$basepath          = trailingslashit( $uploads['basedir'] ) . $pathinfo['dirname'];

		foreach ( $meta['sizes'] as $name => $attr ) {

			if ( in_array( $name, $ignore, true ) ) {
				continue;
			}

			// Is the file related to this size there?
			$normal_file = '';
			$pathinfo    = null;
			$retina_file = null;

			// Generate retina file name.
			if ( isset( $meta['sizes'][ $name ] ) && isset( $meta['sizes'][ $name ]['file'] ) ) {
				$normal_file = trailingslashit( $basepath ) . $meta['sizes'][ $name ]['file'];
				$pathinfo    = pathinfo( $normal_file );
				$retina_file = trailingslashit( $pathinfo['dirname'] ) . $pathinfo['filename'] . '@2x.' . $pathinfo['extension'];
			}

			// Bail, if retina file already exists.
			if ( $retina_file && file_exists( $retina_file ) ) {
				continue;
			}

			if ( $retina_file ) {
				$original_file = trailingslashit( $pathinfo['dirname'] ) . $original_basename;

				if ( ! file_exists( $original_file ) ) {
					return $meta;
				}

				// @codingStandardsIgnoreStart
				// Maybe that new image is exactly the size of the original image.
				// In that case, let's make a copy of it.
				if ( $meta['sizes'][ $name ]['width'] * 2 == $meta['width'] && $meta['sizes'][ $name ]['height'] * 2 == $meta['height'] ) {
					copy( $original_file, $retina_file );
				}
				// Otherwise let's resize (if the original size is big enough).
				else if ( $this->are_dimensions_ok( $meta['width'], $meta['height'], $meta['sizes'][ $name ]['width'] * 2, $meta['sizes'][ $name ]['height'] * 2 ) ) {

					$crop = isset( $_wp_additional_image_sizes[ $name ] ) ? $_wp_additional_image_sizes[ $name ]['crop'] : true;

					$image = $this->resize(
						$original_file,
						$meta['sizes'][ $name ],
						$crop
					);
				}
				// @codingStandardsIgnoreEnd
			}
		}

		return $meta;
	}

	/**
	 * Resize the image
	 *
	 * @param  string  $file_path File path.
	 * @param  array   $meta      An array of attachment meta data.
	 * @param  boolean $crop      Do crop the image or not.
	 * @return array
	 */
	private function resize( $file_path, $meta, $crop ) {

		$image  = wp_get_image_editor( $file_path );
		$width  = $meta['width'] * 2;
		$height = $meta['height'] * 2;

		if ( is_wp_error( $image ) ) {
			error_log( 'Resize failure: ' . $image->get_error_message() );
			return false;
		}

		// Resize or use Custom Crop.
		$image->resize( $width, $height, $crop );

		// Quality.
		$image->set_quality();

		// Save.
		$cropped_img_path = $image->generate_filename( $meta['width'] . 'x' . $meta['height'] . '@2x' );
		$saved            = $image->save( $cropped_img_path );

		if ( is_wp_error( $saved ) ) {
			$error = $saved->get_error_message();
			error_log( 'Retina: Could not create/resize image ' . $file_path . ' to ' . $newfile . ':' . $error );
			return false;
		}

		return true;
	}

	/**
	 * Check if attachment metadata contains all the needed information
	 *
	 * @param  array $meta An array of attachment meta data.
	 * @return boolean
	 */
	private function is_image_meta( $meta ) {

		if ( ! isset( $meta ) ) {
			return false;
		}

		if ( ! isset( $meta['sizes'] ) ) {
			return false;
		}

		if ( ! isset( $meta['width'], $meta['height'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get registered image sizes
	 *
	 * @return array
	 */
	private function get_image_sizes() {
		$sizes = array();
		global $_wp_additional_image_sizes;

		foreach ( get_intermediate_image_sizes() as $s ) {

			$crop = false;

			if ( isset( $_wp_additional_image_sizes[ $s ] ) ) {

				$width  = intval( $_wp_additional_image_sizes[ $s ]['width'] );
				$height = intval( $_wp_additional_image_sizes[ $s ]['height'] );
				$crop   = $_wp_additional_image_sizes[ $s ]['crop'];
			} else {

				$width  = get_option( $s . '_size_w' );
				$height = get_option( $s . '_size_h' );
				$crop   = get_option( $s . '_crop' );
			}

			$sizes[ $s ] = array(
				'width'  => $width,
				'height' => $height,
				'crop'   => $crop,
			);
		}

		return $sizes;
	}

	/**
	 * Compares two images dimensions (resolutions) against each while accepting an margin error.
	 *
	 * @param  int $width         Width.
	 * @param  int $height        Height.
	 * @param  int $retina_width  Retina idth.
	 * @param  int $retina_height Retina height.
	 * @return int
	 */
	private function are_dimensions_ok( $width, $height, $retina_width, $retina_height ) {
		$w_margin = $width - $retina_width;
		$h_margin = $height - $retina_height;

		return ( $w_margin >= -2 && $h_margin >= -2 );
	}

	/**
	 * Remove Retina image when deleting media.
	 *
	 * @param int $attachment_id Current attachment ID.
	 */
	public function delete_attachment( $attachment_id ) {

		$meta       = wp_get_attachment_metadata( $attachment_id );
		$upload_dir = wp_upload_dir();

		if ( ! $this->is_image_meta( $meta ) ) {
			return false;
		}

		$sizes = $meta['sizes'];
		if ( ! $sizes || ! is_array( $sizes ) ) {
			return false;
		}

		$originalfile = $meta['file'];
		$pathinfo     = pathinfo( $originalfile );
		$uploads      = wp_upload_dir();
		$basepath     = trailingslashit( $uploads['basedir'] ) . $pathinfo['dirname'];

		foreach ( $sizes as $name => $attr ) {
			$pathinfo    = pathinfo( $attr['file'] );
			$retina_file = $pathinfo['filename'] . '@2x.' . $pathinfo['extension'];

			if ( file_exists( trailingslashit( $basepath ) . $retina_file ) ) {
				$fullpath = trailingslashit( $basepath ) . $retina_file;
				unlink( $fullpath );
			}
		}

		return true;
	}
}

/**
 * Init
 */
new Cyprus_Images;
