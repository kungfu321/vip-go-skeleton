<?php
class MTS_Options_editor extends MTS_Options {

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
		$settings = array_merge( isset( $this->field['args'] ) ? $this->field['args'] : array(), array(
			'textarea_name' => $this->args['opt_name'] . '[' . $this->field['id'] . ']',
			'editor_class' => $class,
			'textarea_rows' => 6,
		) );
		wp_editor( $this->value, $this->field['id'], $settings );

		$this->print_description();
	}
}
