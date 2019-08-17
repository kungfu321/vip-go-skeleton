<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Cyprus_Walker_Nav_Menu extends Walker_Nav_Menu_Edit {

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$item_output = '';

		parent::start_el( $item_output, $item, $depth, $args, $id );

		// NOTE: Check this regex from time to time!
		$output .= preg_replace(
			'/(?=<(fieldset|p)[^>]+class="[^"]*field-move)/',
			$this->get_fields( $item, $depth, $args ),
			$item_output
		);
	}

	/**
	 * Get custom fields
	 *
	 * @access protected
	 *
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   Menu item args.
	 * @param int    $id     Nav menu ID.
	 *
	 * @return string Form fields
	 */
	protected function get_fields( $item, $depth = 0, $args = array(), $id = 0 ) {
		ob_start();

		/**
		 * Get menu item custom fields from plugins/themes
		 */
		do_action( 'cyprus_menu_item_custom_fields', $item->ID, $item, $depth, $args, $id );

		return ob_get_clean();
	}
}
