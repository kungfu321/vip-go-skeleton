<?php
class NHP_Validation_str_replace extends MTS_Options {

	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since MTS_Options 1.0.1
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
	 * @since MTS_Options 1.0.1
	*/
	function validate() {
		$this->value = str_replace( $this->field['str']['search'], $this->field['str']['replacement'], $this->value );
	}
}
