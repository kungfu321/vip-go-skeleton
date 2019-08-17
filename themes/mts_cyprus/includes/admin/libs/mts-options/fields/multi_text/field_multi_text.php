<?php
class MTS_Options_multi_text extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = $field;
		$this->value = $value;
	}

	public function render() {

		$class = isset( $this->field['class'] ) ? $this->field['class'] : 'regular-text';

		echo '<ul id="' . $this->field['id'] . '-ul">';

		if ( isset( $this->value ) && is_array( $this->value ) ) {
			foreach ( $this->value as $k => $value ) {
				if ( '' !== $value ) {
					echo '<li><input type="text" id="' . $this->field['id'] . '-' . $k . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][]" value="' . esc_attr( $value ) . '" class="' . $class . '" /> <a href="javascript:void(0);" class="mts-opts-multi-text-remove">' . __( 'Remove', 'cyprus' ) . '</a></li>';
				}
			}
		} else {
			echo '<li><input type="text" id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][]" value="" class="' . $class . '" /> <a href="javascript:void(0);" class="mts-opts-multi-text-remove">' . __( 'Remove', 'cyprus' ) . '</a></li>';
		}

		echo '<li style="display:none;"><input type="text" id="' . $this->field['id'] . '" name="" value="" class="' . $class . '" /> <a href="javascript:void(0);" class="mts-opts-multi-text-remove">' . __( 'Remove', 'cyprus' ) . '</a></li>';

		echo '</ul>';

		echo '<a href="javascript:void(0);" class="mts-opts-multi-text-add" rel-id="' . $this->field['id'] . '-ul" rel-name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][]">' . __( 'Add More', 'cyprus' ) . '</a><br/>';

		$this->print_description();
	}
}
