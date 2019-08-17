<?php
/**
 * Field: Switch
 */

if ( ! class_exists( 'MTS_Options_button_set' ) ) {
	require_once( $this->dir . 'fields/button_set/field_button_set.php' );
}

class MTS_Options_switch extends MTS_Options_button_set {

	public function __construct( $field = array(), $value = '', $parent ) {

		$field['class'] = isset( $field['class'] ) ? 'button-switch ' . $field['class'] : 'button-switch';

		if ( ! isset( $field['options'] ) || empty( $field['options'] ) ) {
			$field['options'] = array(
				'0' => __( 'Off', 'cyprus' ),
				'1' => __( 'On', 'cyprus' ),
			);
		}

		parent::__construct( $field, $value, $parent );
	}
}
