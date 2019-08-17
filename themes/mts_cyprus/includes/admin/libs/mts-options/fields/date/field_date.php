<?php
class MTS_Options_date extends MTS_Options {

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
		echo '<input type="text" id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" value="' . $this->value . '" class="' . $class . ' mts-opts-datepicker" />';

		$this->print_description();
	}

	public function enqueue() {

		wp_enqueue_style( 'mts-opts-jquery-ui-css' );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
	}
}
