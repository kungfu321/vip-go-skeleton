<?php
class MTS_Options_multi_select extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = $field;
		$this->value = $value;
	}

	public function render() {

		$class = isset( $this->field['class'] ) ? 'class="' . $this->field['class'] . '" ' : '';
		if ( '' === $this->value && isset( $this->field['std'] ) ) {
			$this->value = $this->field['std'];
		}
		echo '<select id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][]" ' . $class . 'multiple="multiple" rows="6" >';

		foreach ( $this->field['options'] as $k => $v ) {

			$selected = ( is_array( $this->value ) && in_array( $k, $this->value ) ) ? ' selected="selected"' : '';

			echo '<option value="' . $k . '"' . $selected . '>' . $v . '</option>';
		}

		echo '</select>';

		$this->print_description( '<br />' );
	}
}
