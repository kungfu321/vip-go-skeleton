<?php
class MTS_Options_color_gradient extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = $field;
		$this->value = $value;
	}

	public function render() {

		$class = isset( $this->field['class'] ) ? $this->field['class'] : '';

		echo '<div class="farb-popup-wrapper" id="' . $this->field['id'] . '">';

		echo __( 'From:', 'cyprus' ) . ' <input type="text" id="' . $this->field['id'] . '-from" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][from]" value="' . $this->value['from'] . '" class="' . $class . ' popup-colorpicker" style="width:70px;"/>';
		echo '<div class="farb-popup"><div class="farb-popup-inside"><div id="' . $this->field['id'] . '-frompicker" class="color-picker"></div></div></div>';

		echo __( ' To:', 'cyprus' ) . ' <input type="text" id="' . $this->field['id'] . '-to" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][to]" value="' . $this->value['to'] . '" class="' . $class . ' popup-colorpicker" style="width:70px;"/>';
		echo '<div class="farb-popup"><div class="farb-popup-inside"><div id="' . $this->field['id'] . '-topicker" class="color-picker"></div></div></div>';

		$this->print_description();

		echo '</div>';
	}
}
