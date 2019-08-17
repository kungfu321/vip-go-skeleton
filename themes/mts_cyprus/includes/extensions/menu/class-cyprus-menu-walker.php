<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Cyprus_Menu_Walker extends Walker_Nav_Menu {

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$item_html       = '';
		$old_link_before = '';

		if ( $item->icon ) {
			$old_link_before   = $args->link_before;
			$args->link_before = '<i class="fa fa-' . $item->icon . '"></i> ' . $args->link_before;
		}

		parent::start_el( $item_html, $item, $depth, $args, $id );

		if ( $item->icon ) {
			$args->link_before = $old_link_before;
		}

		$output .= $item_html;
	}
}
