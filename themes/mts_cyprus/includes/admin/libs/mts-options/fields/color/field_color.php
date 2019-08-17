<?php
class MTS_Options_color extends MTS_Options {

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

		echo '<div id="colorpicker-' . $this->field['id'] . '">';
		echo '<input type="text" id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" value="' . $this->value . '" class="' . $class . ' popup-colorpicker" ' . ( isset( $this->field['args']['opacity'] ) ? 'data-alpha="true"' : '' ) . ' />';
		echo '</div>';
	}

	public function enqueue() {

		wp_enqueue_style( 'wp-color-picker' );

		if ( ! wp_script_is( 'wp-color-picker-alpha-js' ) ) {
			wp_enqueue_script(
				'wp-color-picker-alpha-js',
				$this->url . 'fields/color/wp-color-picker-alpha.min.js',
				array( 'wp-color-picker' ),
				cyprus()->get_version(),
				true
			);
		}
	}
}
