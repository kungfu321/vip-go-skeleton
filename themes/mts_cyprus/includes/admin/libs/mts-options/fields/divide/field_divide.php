<?php
class MTS_Options_divide extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = $field;
		$this->value = $value;
	}

	public function render() {

		$class = isset( $this->field['class'] ) ? ' ' . $this->field['class'] . '' : '';

		echo '</td></tr></table><div class="hr' . $class . '"/></div><table class="form-table no-border"><tbody><tr><th></th><td>';
	}
}
