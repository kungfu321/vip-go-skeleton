<?php
class MTS_Options_radio_img extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent = '' ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = $field;
		$this->value = $value;
	}

	public function render() {

		if ( '' === $this->value && isset( $this->field['std'] ) ) {
			$this->value = $this->field['std'];
		}
		echo '<fieldset class="mts-radio-img">';

		$counter = 0;
		foreach ( $this->field['options'] as $k => $v ) {

			$selected = '' != checked( $this->value, $k, false ) ? 'mts-radio-img-selected' : '';

			echo '<label for="' . $this->field['id'] . '_' . $counter . '" class="' . $selected . ' mts-radio-img-' . $this->field['id'] . '">';

			echo '<input type="radio" id="' . $this->field['id'] . '_' . $counter . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" value="' . $k . '" ' . checked( $this->value, $k, false ) . '/>';
			echo '<img src="' . $v['img'] . '" />';

			if ( isset( $v['title'] ) ) {
				printf( '<span>%s</span>', $v['title'] );
			}

			echo '</label>';

			$counter++;
		}

		$this->print_description( '<br />' );

		echo '</fieldset>';
	}
}
