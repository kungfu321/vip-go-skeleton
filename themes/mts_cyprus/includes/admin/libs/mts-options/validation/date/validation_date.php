<?php
class NHP_Validation_date extends MTS_Options {

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since MTS_Options 1.0
	*/
	function __construct( $field, $value, $current ) {

		parent::__construct();
		$this->field = $field;
		$this->field['msg'] = isset( $this->field['msg'] ) ? $this->field['msg'] : __( 'This field must be a valid date.', 'cyprus' );
		$this->value = $value;
		$this->current = $current;
		$this->validate();
	}

	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since MTS_Options 1.0
	*/
	function validate() {

		$string = str_replace( '/', '', $this->value );

		if ( ! is_numeric( $string ) ) {
			$this->value = isset( $this->current ) ? $this->current : '';
			$this->error = $this->field;
			return;
		}

		if ( '/' != $this->value[2] ) {
			$this->value = isset( $this->current ) ? $this->current : '';
			$this->error = $this->field;
			return;
		}

		if ( '/' != $this->value[5] ) {
			$this->value = isset( $this->current ) ? $this->current : '';
			$this->error = $this->field;
		}
	}
}
