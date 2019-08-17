<?php
class MTS_Options_textarea extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = $field;
		$this->value = $value;

		// Default
		$this->field['rows'] = isset( $this->field['rows'] ) ? $this->field['rows'] : '6';
	}

	public function render() {

		$class = isset( $this->field['class'] ) ? $this->field['class'] : 'large-text';
		if ( '' === $this->value && isset( $this->field['std'] ) ) {
			$this->value = $this->field['std'];
		}
		echo '<textarea id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" class="' . $class . '" rows="' . $this->field['rows'] . '">' . esc_textarea( $this->value ) . '</textarea>';

		$this->print_description( '<br />' );
	}
}
