<?php
class NHP_Validation_html extends MTS_Options {

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
		$this->value = $value;
		$this->current = $current;
		$this->validate();
	}

	/**
	 * Field Render Function.
	 *
	 * Takes the vars and validates them
	 *
	 * @since MTS_Options 1.0
	*/
	function validate() {
		$this->value = wp_kses_post( $this->value );
	}
}
