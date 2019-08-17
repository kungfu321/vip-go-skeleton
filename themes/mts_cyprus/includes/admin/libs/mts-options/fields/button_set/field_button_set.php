<?php
/**
 * Field: Button Set
 */
class MTS_Options_button_set extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = $field;
		$this->value = $value;
	}

	public function render() {

		$class = isset( $this->field['class'] ) ? ' ' . $this->field['class'] : '';

		printf( '<div class="toggle-buttons%s">', $class );

		if ( '' === $this->value && isset( $this->field['std'] ) ) {
			$this->value = $this->field['std'];
		}
		foreach ( $this->field['options'] as $k => $v ) {

			printf( '<input type="radio" %s %s>',
				$this->html_attributes(
					array(
						'id' => $this->field['id'] . "_$k",
						'name' => $this->args['opt_name'] . '[' . $this->field['id'] . ']',
						'value' => $k,
					)
				),
				checked( $this->value, $k, false )
			);

			printf( '<label for="%1$s_%2$s">%3$s</label>', $this->field['id'], $k, $v );
		}

		$this->print_description();

		echo '</div>';
	}
}
