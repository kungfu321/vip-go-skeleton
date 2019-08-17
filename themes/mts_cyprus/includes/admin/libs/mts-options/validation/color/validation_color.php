<?php
class NHP_Validation_color extends MTS_Options {

	/**
	 * Field Constructor.
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since MTS_Options 1.0
	 */
	function __construct( $field, $value, $current ) {

		parent::__construct();
		$this->field = $field;
		$this->field['msg'] = isset( $this->field['msg'] ) ? $this->field['msg'] : esc_html__( 'This field must be a valid color value.', 'cyprus' );
		$this->value = $value;
		$this->current = $current;
		$this->validate();
	}

	/**
	 * Field Render Function.
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since MTS_Options 1.0
	 */
	function validate() {

		if ( 'transparent' === $this->value ) {
			return;
		}

		if ( '#' === $this->value[0] ) {

			$this->value = str_replace( '#', '', $this->value );
			if ( 3 === strlen( $this->value ) ) {
				$this->value = $this->value . $this->value;
			}

			if ( preg_match( '/^[a-f0-9]{6}$/i', $this->value ) ) {
				$this->value = '#' . $this->value;
				return;
			}

			$this->value = isset( $this->current ) ? $this->current : '';
			$this->error = $this->field;

			return;
		}
	}
}
