<?php
class NHP_Validation_email extends MTS_Options {

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
		$this->field['msg'] = isset( $this->field['msg'] ) ? $this->field['msg'] : __( 'You must provide a valid email for this option.', 'cyprus' );
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

		if ( ! is_email( $this->value )  ) {
			$this->value = isset( $this->current ) ? $this->current : '';
			$this->error = $this->field;
		}
	}
}
