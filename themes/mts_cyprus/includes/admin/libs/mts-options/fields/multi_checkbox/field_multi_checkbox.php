<?php
class MTS_Options_multi_checkbox extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = $field;
		$this->value = (array) $value;
	}

	public function render() {

		$class = isset( $this->field['class'] ) ? $this->field['class'] : 'regular-text';
		if ( '' === $this->value && isset( $this->field['std'] ) ) {
			$this->value = $this->field['std'];
		}
		echo '<fieldset>';
		foreach ( $this->field['options'] as $k => $v ) {

			echo '<label for="' . $this->field['id'] . '_' . $k . '">';
			echo '<input type="checkbox" id="' . $this->field['id'] . '_' . $k . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][' . $k . ']" ' . $class . ' value="' . $k . '" ' . checked( in_array( $k, $this->value ), true, false ) . '/>';
			echo ' ' . $v . '</label><br/>';

		}

		$this->print_description();

		echo '</fieldset>';
	}
}
