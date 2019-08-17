<?php
class MTS_Options_text extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = $field;
		$this->value = $value;
	}

	public function render() {

		$class = isset( $this->field['class'] ) ? $this->field['class'] : 'regular-text';
		$type  = isset( $this->field['args']['type'] ) ? $this->field['args']['type'] : 'text';

		if ( 'hidden' === $type ) {
			$this->value = empty( $this->value ) ? $this->field['std'] : $this->value;
			echo '<input type="' . $type . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" value="' . esc_attr( $this->value ) . '">';
		} else {
			echo '<input type="' . $type . '" id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" value="' . esc_attr( $this->value ) . '" class="' . $class . '" data-std="' . ( isset( $this->field['std'] ) ? $this->field['std'] : '' ) . '" />';
		}

		$this->print_description();
	}
}
