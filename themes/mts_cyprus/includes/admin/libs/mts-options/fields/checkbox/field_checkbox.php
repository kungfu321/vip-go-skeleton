<?php
/**
 * Field: Checkbox
 */
class MTS_Options_checkbox extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = $field;
		$this->value = $value;
	}

	public function render() {

		$class = isset( $this->field['class'] ) ? $this->field['class'] : '';
		if ( '' === $this->value && isset( $this->field['std'] ) ) {
			$this->value = $this->field['std'];
		}
		if ( isset( $field['desc'] ) ) {
			echo '' !== $this->field['desc'] ? ' <label>' : '';
		}

		echo '<input type="checkbox" id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" value="1" class="' . $class . '" ' . checked( $this->value, '1', false ) . '/>';

		echo isset( $this->field['desc'] ) && ! empty( $this->field['desc'] ) ? ' ' . $this->field['desc'] . '</label>' : '';
	}
}
