<?php
/**
 * Tweaks for the footer of the document.
 *
 * @package Cyprus
 */

defined( 'WPINC' ) || exit;

/**
 * Tweaks for the <head> of the document.
 */
class Cyprus_Footer extends Cyprus_Base {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->add_action( 'wp_footer', 'tracking_field', 999 );
	}

	/**
	 * Add tracking field code
	 */
	public function tracking_field() {
		echo cyprus_get_settings( 'mts_analytics_code' );
	}
}

/**
 * Init
 */
new Cyprus_Footer;
