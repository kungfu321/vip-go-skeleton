<?php
class MTS_Options_heading extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = $field;
		$this->value = $value;
	}

	public function render() {

		if ( isset( $this->field['title'] ) && ! empty( $this->field['title'] ) ) {
			printf( '<h3>%s</h3>', $this->field['title'] );
		}

		if ( isset( $this->field['desc'] ) && ! empty( $this->field['desc'] ) ) {
			printf( '<div class="mts-opts-section-desc">%s</div>', $this->field['desc'] );
		}
	}
}
