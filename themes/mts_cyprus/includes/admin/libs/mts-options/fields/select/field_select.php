<?php
class MTS_Options_select extends MTS_Options {

	public function __construct( $field = array(), $value = '', $parent ) {

		parent::__construct( $parent->sections, $parent->args, $parent->extra_tabs );
		$this->field = $field;
		$this->value = $value;
	}

	public function render() {

		$attr = array(
			'id'               => $this->field['id'],
			'name'             => $this->args['opt_name'] . '[' . $this->field['id'] . ']',
			'data-std'         => isset( $this->field['std'] ) ? $this->field['std'] : '',
			'style'            => 'width: 100%; max-width: 240px;',
			'data-placeholder' => __( 'Select', 'cyprus' ),
		);

		$attr['class'] = 'search_select ';

		if ( isset( $this->field['multiple'] ) && $this->field['multiple'] ) {
			$attr['multiple'] = 'multiple';
			$attr['name']     = $attr['name'] . '[]';
			$attr['class']    = 'search_select ';
		}

		if ( isset( $this->field['class'] ) ) {
			$attr['class'] .= $this->field['class'];
		}

		if ( '' === $this->value && isset( $this->field['std'] ) ) {
			$this->value = $this->field['std'];
		}
		echo '<select' . cyprus_generate_attributes( $attr ) . '>';

		// latest field for category dropdown.
		if ( isset( $this->field['args']['include_latest'] ) && $this->field['args']['include_latest'] ) {
			$this->field['options'] = array( 'latest' => esc_html__( 'Latest Posts', 'cyprus' ) ) + $this->field['options'];
		}

		foreach ( $this->field['options'] as $k => $v ) {
			if ( is_array( $this->value ) ) {
				$selected = in_array( $k, $this->value ) ? ' selected="selected"' : '';
			} else {
				$selected = $k == $this->value ? ' selected="selected"' : '';
			}
			echo '<option value="' . $k . '"' . $selected . '>' . $v . '</option>';
		}

		echo '</select>';

		if ( isset( $this->field['multiple'] ) && $this->field['multiple'] ) {
			echo '<input type="button" id="' . $this->field['id'] . '-selectall" value="' . __( 'Select All', 'cyprus' ) . '" class="button button-secondary select_all">';
		}

		$this->print_description();
	}

	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 */
	public function enqueue() {
		wp_enqueue_script(
			'select2',
			$this->url . 'js/select2.min.js',
			array( 'jquery' ),
			cyprus()->get_version(),
			true
		);

		wp_enqueue_style(
			'select2',
			$this->url . 'css/select2.css',
			array(),
			cyprus()->get_version(),
			'all'
		);
	}
}
